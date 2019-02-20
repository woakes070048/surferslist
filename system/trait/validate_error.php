<?php
trait ValidateError {
	private $errors;

	protected function getError($name) {
		return isset($this->errors[$name]) ? $this->errors[$name] : '';
	}

	protected function setError($name, $value) {
		if (!$name) {
			return;
		}

		if (!isset($this->errors)) {
			$this->errors = array();
		}

		$this->errors[$name] = $value;
	}

	protected function appendError($name, $value, $key = null) {
		if (!$name) {
			return;
		}

		if (!isset($this->errors[$name])) {
			$this->setError($name, array());
		}

		if (!is_null($key)) {
			$this->errors[$name][$key] = $value;
		} else {
			$this->errors[$name][] = $value;
		}
	}

	protected function hasError() {
		return !empty($this->errors);
	}

	protected function getErrors() {
		if (!isset($this->errors)) {
			return array();
		}

		return $this->errors;
	}
}
?>
