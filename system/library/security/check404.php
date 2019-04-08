<?php
/**
 * Created by @exife
 * Website: exife.com
 * Email: support@exife.com
 *
 */

class SecurityCheck404 extends SecurityBase {
    private $ban_list_cache = array();
    private $lock_list_cache = array();

    public function log_event($is_404) {
        $this->language->load('module/security_strings');

        if (empty($this->request->server['REMOTE_ADDR'])) {
            $this->request->server['REMOTE_ADDR'] = '';
        }

        if (empty($this->request->server['HTTP_REFERER'])) {
            $this->request->server['HTTP_REFERER'] = '';
        }

        if (empty($this->request->server['HTTP_HOST'])) {
            $this->request->server['HTTP_HOST'] = '';
        }

        if (empty($this->request->server['REQUEST_URI'])) {
            $this->request->server['REQUEST_URI'] = '';
        }

        if ($this->is_banned()) {
            header('Content-Type: text/plain');
            die($this->language->get('text_security_banned'));
        }

        if ($this->check_lock()) {
            header('Content-Type: text/plain');
            die($this->language->get('text_security_404_lockout'));
        }

        if ($is_404) {
            // don't log requests to certain file types
            if (preg_match('@.*\.(ico|gif|jpg|jpeg|png|js|css)$@', $this->request->server['REQUEST_URI'])) {
                return;
            }

            $this->db->query("
                INSERT INTO " . DB_PREFIX . "security_log
                SET `type` = '2'
                , `host` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
                , `user_id` = '0'
                , `referrer` = '" . $this->db->escape($this->request->server['HTTP_REFERER']) . "'
                , `url` = '" . $this->db->escape(($this->request->isSecure() ? 'https://' : 'http://') . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI']) . "'
            ");

            $period = $this->config->get('security_check_period') * 60;

            $query = $this->db->query("
                SELECT COUNT(*) as `total`
                FROM `" . DB_PREFIX . "security_log`
                WHERE `type` = '2'
                AND `host` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
                AND `timestamp` > FROM_UNIXTIME(" . (int)(time() - $period) . ")
            ");

            $host_count = $query->row['total'];

            if ($host_count >= $this->config->get('security_error_threshold')) {
                $this->lockout();
            }
        }
    }

    protected function lockout() {
        $now = time();
        $expire_time = $now + (60 * $this->config->get('security_lockout_period'));
        $ban = false;

        if ($this->check_list($this->config->get('security_white_list_404')) == false) {
            if (filter_var($this->request->server['REMOTE_ADDR'], FILTER_VALIDATE_IP) && $this->config->get('security_blacklist_repeat_offender')) {
                $lock_limit = $this->config->get('security_blacklist_threshold');

                $lock_count = $this->db->query("
                    SELECT COUNT(*) as `total`
                    FROM `" . DB_PREFIX . "security_lockout`
                    WHERE `type` = '2'
                    AND `host` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
                ")->row['total'] + 1;

            } else {
                $lock_limit = false;
                $lock_count = 0;
            }

            if ($lock_limit !== false && $lock_count >= $lock_limit) {
                $this->db->query("
                    INSERT IGNORE INTO " . DB_PREFIX . "security_ban_list
                    SET `host` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
                ");

                $this->ban_list_cache[$this->request->server['REMOTE_ADDR']] = true;

                $ban = true;
            }

            $this->db->query("
                INSERT INTO " . DB_PREFIX . "security_lockout
                SET `type` = '2'
                , `start_time` = FROM_UNIXTIME(" . (int)$now . ")
                , `expire_time` = FROM_UNIXTIME(" . (int)$expire_time . ")
                , `host` = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'
                , `user_id` = '0'
            ");

            $this->lock_list_cache[$this->request->server['REMOTE_ADDR']] = true;

            if ($ban) {
                $this->cache->delete('security');
            } else {
                $this->cache->delete('security.locklist');
            }

            if ($this->config->get('security_email_404_notifications') && $this->config->get('security_email_404_address') && $this->config->get('config_alert_mail')) {
                $mail = new Mail($this->config->get('config_smtp_api_key'));
                $mail->protocol = $this->config->get('config_mail_protocol');
                $mail->parameter = $this->config->get('config_mail_parameter');
                $mail->hostname = $this->config->get('config_smtp_host');
                $mail->username = $this->config->get('config_smtp_username');
                $mail->password = $this->config->get('config_smtp_password');
                $mail->port = $this->config->get('config_smtp_port');
                $mail->timeout = $this->config->get('config_smtp_timeout');

                $mail->setTo($this->config->get('security_email_login_address'));
                $mail->setFrom($this->config->get('config_email'));

                $mail->setSender($this->config->get('config_name'));
                $mail->setSubject(sprintf($this->language->get('text_security_404_notify_subject'), $this->config->get('config_name')));

                $who = sprintf($this->language->get('text_security_notify_host'), $this->request->server['REMOTE_ADDR'], $this->request->server['REMOTE_ADDR']);

                if ($ban) {
                    $duration = $this->language->get('text_security_notify_ban');
                } else {
                    $duration = sprintf($this->language->get('text_security_notify_until'), date('Y-m-d H:i:s', $expire_time));
                }

                $mail->setText(sprintf($this->language->get('text_security_404_notify_text'), $who, $duration));
                $mail->send();
            }
        }
    }

    protected function check_lock() {
        if (!empty($this->lock_list_cache[$this->request->server['REMOTE_ADDR']])) {
            return true;
        }

        $locklist = $this->cache->get('security.locklist');

        if ($locklist === false) {
            $locklist = array();

            $query = $this->db->query("
                SELECT `host`
                FROM `" . DB_PREFIX . "security_lockout`
                WHERE `expire_time` > NOW()
            ");

            foreach ($query->rows as $result) {
                $locklist[] = $result['host'];
            }

        	$this->cache->set('security.locklist', $locklist, $this->config->get('security_check_period') * 60);
        }

        $request_server_is_locked = !in_array($this->db->escape($this->request->server['REMOTE_ADDR']), $locklist) ? false : true;

        if ($request_server_is_locked) {
            $this->lock_list_cache[$this->request->server['REMOTE_ADDR']] = true;
        }

        return $request_server_is_locked;
    }

    protected function check_list($list) {
        $values = explode("\n", $list);
        $host = ip2long($this->request->server['REMOTE_ADDR']);

        foreach ($values as $item) {
            $parts = explode('.', $item);

            $i = 0;

            $ip_a = '';
            $ip_b = '';

            foreach ($parts as $part) {
                if (strstr($part, '*')) {
                    $ip_a .= '0';
                    $ip_b .= '255';
                } else {
                    $ip_a .= $part;
                    $ip_b .= $part;
                }
                if ($i < 3) {
                    $ip_a .= '.';
                    $ip_b .= '.';
                }
                $i++;
            }

            if (strcmp($ip_a, $ip_b) != 0) {
                if($host >= ip2long(trim($ip_a)) && $host <= ip2long(trim($ip_b))) {
                    return true;
                }
            } else {
                if (trim($this->request->server['REMOTE_ADDR']) == trim($item)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function is_banned() {
        if (!empty($this->ban_list_cache[$this->request->server['REMOTE_ADDR']])) {
            return true;
        }

        $banlist = $this->cache->get('security.banlist');

        if ($banlist === false) {
            $banlist = array();

            $query = $this->db->query("
                SELECT `host`
                FROM " . DB_PREFIX . "security_ban_list
            ");

            foreach ($query->rows as $result) {
                $banlist[] = $result['host'];
            }

        	$this->cache->set('security.banlist', $banlist, $this->config->get('security_check_period') * 60);
        }

        $request_server_is_banned = !in_array($this->db->escape($this->request->server['REMOTE_ADDR']), $banlist) ? false : true;

        if ($request_server_is_banned) {
            $this->ban_list_cache[$this->request->server['REMOTE_ADDR']] = true;
        }

        return $request_server_is_banned;
    }

}
