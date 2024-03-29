<?php
trait ValidateField {
	use ValidateError;

	protected function validateEmail($email) {
		if (!$this->validateStringLength($email, 3, 254)) {
			return false;
		}

		// return filter_var($email, FILTER_VALIDATE_EMAIL);
		return preg_match('/^[^\@]+@.*\.[a-z]{2,15}$/i', $email);
	}

	protected function validateUrl($url) {
		$max = min(2000, (int)$this->config->get('member_data_field_description_max'));

		if (!$this->validateStringLength($url, 3, $max)) {
			return false;
		}

		$url = str_replace(' ', '%20', $url);
		
		return preg_match('#((https?)://(\S*?\.\S*?))([\s)\[\]{},;"\':<]|\.\s|$)#i', $url);
	}

	protected function validatePassword($pw, $min = 8, $max = '') {
		if (!$this->validateStringLength($pw, $min, $max)) {
			return false;
		}

		return preg_match('/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{' . preg_quote($min-1, '/') . ',' . preg_quote($max, '/') . '}$/', $pw);
	}

	protected function validateNumeric($number, $positive_only = false, $decimal_allowed = false) {
		if ($positive_only && (int)$number < 0) {
			return false;
		}

		$replace = array(',' => '');

		if ($decimal_allowed) {
			$replace['.'] = '';
		}

		$number = strtr($number, $replace);

		if (!is_numeric($number)) {
			return false;
		}

		return preg_match('/^(?:[0-9]\d*)?$/', $number);
	}

	protected function validateYear($year, $length = 4) {
		if (!$this->validateNumeric($year, true)) {
			return false;
		}

		return preg_match('/^(\d{' . preg_quote($length, '/') . '})?$/', $year);
	}

	protected function validatePrice(&$price) {
		if (!$this->validateNumeric($price, true, true)) {
			return false;
		}

		return preg_match('/^(?:[0-9]\d*)(?:\.\d{1,2})?$/', strtr($price, array(',' => '')));
	}

	protected function validateStringLength($string, $min = 0, $max = 255) {
		if (!is_string($string) || (!$string && $min > 0)) {
			return false;
		}

		$valid = true;

		if ($min && utf8_strlen($string) < $min) {
			$valid = false;
		}

		if ($max && utf8_strlen($string) > $max) {
			$valid = false;
		}

		return $valid;
	}
}
