<?php
trait CSRFToken {
	protected $csrf_token;

	protected function getCSRFToken() {
		if (!isset($this->csrf_token)) {
			$this->setCSRFToken();
		}

		return $this->csrf_token;
	}

	protected function setCSRFToken() {
		$token = hash_rand('md5');

		$this->session->data['csrf_token'] = $token;

		$this->csrf_token = $token;
	}

	protected function validateCSRFToken() {
		if (!isset($this->session->data['csrf_token'])) {
			return false;
		}

		$valid = false;

		if (isset($this->request->post['csrf_token']) && $this->request->post['csrf_token'] == $this->session->data['csrf_token']) {
			$valid = true;
		}

		if (isset($this->request->get['csrf_token']) && $this->request->get['csrf_token'] == $this->session->data['csrf_token']) {
			$valid = true;
		}

		return $valid;
	}
}
?>
