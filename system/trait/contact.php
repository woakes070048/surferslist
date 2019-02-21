<?php
trait Contact {
	protected $mail_enabled;
	protected $admin_mail_enabled;
	protected $mail_log;

	protected function getContactMailStatus() {
		return $this->mail_enabled;
	}

	protected function setContactMailStatus($status) {
		$this->mail_enabled = $status;
	}

	protected function getContactAdminMailStatus() {
		return $this->admin_mail_enabled;
	}

	protected function setContactAdminMailStatus($status) {
		$this->admin_mail_enabled = $status;
	}

	protected function getContactMailLog() {
		return $this->mail_log;
	}

	protected function setContactMailLog($filename) {
		$this->mail_log = $filename;
	}

	protected function sendEmail($data) {
		if (empty($data['to']) || empty($data['from']) || empty($data['sender']) || empty($data['subject']) || (empty($data['message']) && empty($data['html']))) {
			return false;
		}

		if (!isset($this->mail_enabled)) {
			$this->setContactMailStatus($this->config->get('config_alert_mail'));
		}

		$mail_sent = false;

		$mail = new Mail($this->config->get('config_smtp_api_key'));
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');

		if (isset($data['admin']) && $data['admin'] === true) {
			$this->setContactMailAdmin($data);
		}

		$mail->setTo($data['to']);
		$mail->setFrom($data['from']);
		$mail->setSender($data['sender']);
		$mail->setSubject(strip_tags(html_entity_decode($data['subject'], ENT_QUOTES, 'UTF-8')));
		$mail->setText(strip_tags(html_entity_decode($data['message'], ENT_QUOTES, 'UTF-8')));

		if (!empty($data['cc'])) {
			$mail->setCC($data['cc']);
		}

		if (!empty($data['bcc'])) {
			$mail->setBCC($data['bcc']);
		}

		if (!empty($data['reply'])) {
			$mail->setReplyTo($data['reply']);
		}

		if (!empty($data['html'])) {
			$mail->setHtml($data['html']);
		}

		$this->logContactMail($mail);

		if ($this->getContactMailStatus()) {
			$mail_sent = $mail->send();
		}

		return $mail_sent;
	}

	protected function setContactMailAdmin(&$data) {
		if (!isset($this->admin_mail_enabled)) {
			$this->setContactAdminMailStatus($this->config->get('config_admin_mail'));
		}

		if (!$this->getContactAdminMailStatus()) {
			return;
		}

		$admin_emails = array();
		$data_emails = array();

		$data_emails[] = $data['to'];

		if (!isset($data['cc'])) {
			$data['cc'] = array();
		}

		if (!isset($data['bcc'])) {
			$data['bcc'] = array();
		}

		$data_emails = array_merge($data_emails, $data['cc'], $data['bcc']);

		$admin_emails[] = $this->config->get('config_email');

		if ($this->config->get('config_alert_emails')) {
			$admin_emails = array_merge($admin_emails, explode(',', $this->config->get('config_alert_emails')));
		}

		foreach ($admin_emails as $admin_email) {
			if ($admin_email && !in_array($admin_email, $data_emails) && preg_match('/^[^\@]+@.*\.[a-z]{2,15}$/i', $admin_email)) {
				$data['bcc'][] = $admin_email;
			}
		}
	}

	protected function logContactMail($mail) {
		if (!isset($this->mail_log)) {
			$this->setContactMailLog($this->config->get('config_mail_log'));
		}

		$email_log = new Log($this->getContactMailLog());

		$ip = isset($this->request->server['REMOTE_ADDR']) ? $this->request->server['REMOTE_ADDR'] : '';

		$email_log->write($mail->toString() . ' | ' . $this->customer->getId() . ' | ' . $ip);
	}
}
?>
