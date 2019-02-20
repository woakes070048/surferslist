<?php
trait Sort {
	protected $sorts;

	protected function getSorts() {
		if (!isset($this->sorts)) {
			return array();
		}

		return $this->sorts;
	}

	protected function addSort($text, $value, $href) {
		if (!$text || !$value || (!$href && !is_null($href))) {
			return false;
		}

		$this->sorts[] = array(
			'text'  => $text,
			'value' => $value,
			'href'  => $href
		);
	}
}
?>
