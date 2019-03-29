<?php
trait Captcha {
	protected $captcha_enabled; // bool
	protected $captcha_error; // string
	protected $captcha_secret; // string

	protected function getCaptchaStatus() {
		if (!isset($this->captcha_enabled)) {
			$this->setCaptchaStatus(true);
		}

		return $this->captcha_enabled;
	}

	protected function setCaptchaStatus($bool) {
		$this->captcha_secret = $this->config->get('config_captcha_secret');
		$this->captcha_enabled = $bool !== false && $this->captcha_secret;
	}

	protected function getCaptchaError() {
		if (!isset($this->captcha_enabled)) {
			$this->setCaptchaError('');
		}

		return $this->captcha_error;
	}

	protected function setCaptchaError($msg) {
		$this->captcha_error = $msg;
	}

	protected function validateCaptcha() {
		if (isset($this->captcha_enabled) && !$this->captcha_enabled) {
			return true;
		}

		$is_valid = false;

		if (empty($this->request->post['g-recaptcha-response'])) {
			$error_msg = $this->language->get('error_captcha');
		} else {
			$verify_recaptcha_response = $this->verifyCaptcha($this->request->post['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

			if ($verify_recaptcha_response !== true) {
				$error_msg = $this->language->get('error_captcha');
			} else {
				$is_valid = true;
			}

			if (is_array($verify_recaptcha_response) && !empty($verify_recaptcha_response)) {
				$error_msg .= '<br />' . $this->language->get('error_recaptcha');

				foreach ($verify_recaptcha_response as $recaptcha_error) {
					$error_msg .= ' ' . $recaptcha_error;
				}
			}
		}

		if (isset($error_msg)) {
			$this->setCaptchaError($error_msg);
		}

		return $is_valid;
	}

	protected function verifyCaptcha($response, $remoteip) {
		$this->load->library('nocaptcha');

		$recaptcha = new NoCaptcha($this->captcha_secret);

		$recaptcha->setResponse($response);
		$recaptcha->setRemoteIp($remoteip);

		return $recaptcha->verify();
	}
}

