<?php
trait CSRFToken {
	protected $csrf_token;

	protected function getCSRFToken() {
		if (!isset($this->csrf_token)) {
			$this->setCSRFToken();
		}

		return $this->csrf_token;
	}

	protected function setCSRFToken($algorithm = 'md5', $length = 32) {
		$token = hash_rand($algorithm, $length);

		$this->session->data['csrf_token'] = $token;

		$this->csrf_token = $token;
	}

	protected function validateCSRFToken() {
		if (!isset($this->session->data['csrf_token'])) {
			return false;
		}

		$valid = false;

		if (isset($this->request->post['csrf_token'])) {
			if (hash_equals($this->session->data['csrf_token'], $this->request->post['csrf_token'])) {
				$valid = true;
			}
		} else if (isset($this->request->get['csrf_token'])) {
			if (hash_equals($this->session->data['csrf_token'], $this->request->get['csrf_token'])) {
				$valid = true;
			}
		}

		return $valid;
	}
}
