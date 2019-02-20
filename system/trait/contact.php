<?php
trait Contact {
	use ValidateField;

	protected $mail_enabled;

	protected function getContactMailStatus() {
		return $this->mail_enabled;
	}

	protected function setContactMailStatus($status) {
		$this->mail_enabled = $status;
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

		if ($this->config->get('config_admin_mail') && isset($data['admin']) && $data['admin'] === true) {
			if (!isset($data['bcc'])) {
				$data['bcc'] = array();
			}

			$data['bcc'][] = $this->config->get('config_email');

			if ($this->config->get('config_alert_emails')) {
				$alert_emails = explode(',', $this->config->get('config_alert_emails'));

				foreach ($alert_emails as $email) {
					if ($this->validateEmail($email)) {
						$data['bcc'][] = $email;
					}
				}
			}
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

		$this->debugLog($mail);

		if ($this->mail_enabled) {
			$mail_sent = $mail->send();
		}

		return $mail_sent;
	}

	protected function debugLog($mail) {
		$email_log = new Log($this->config->get('config_mail_log'));

		$ip = isset($this->request->server['REMOTE_ADDR']) ? $this->request->server['REMOTE_ADDR'] : '';

		$email_log->write($mail->toString() . ' | ' . $this->customer->getId() . ' | ' . $ip);
	}
}
?>
