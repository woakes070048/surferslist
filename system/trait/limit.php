<?php
trait Limit {
	protected $limits;

	protected function getLimits($route = '', $url = '') {
		if ($route) {
			$limit = $this->config->get('config_catalog_limit');

			$limits = array_unique(array($limit / 2, $limit, $limit * 2, $limit * 4));

			sort($limits);

			foreach ($limits as $value) {
				$this->addLimit($value, $value, $this->url->link($route, $url . '&limit=' . $value));
			}
		}

		if (!isset($this->limits)) {
			return array();
		}

		return $this->limits;
	}

	protected function addLimit($text, $value, $href) {
		if (!$text || !$value || (!$href && !is_null($href))) {
			return false;
		}

		$this->limits[] = array(
			'text'  => $text,
			'value' => $value,
			'href'  => $href
		);
	}
}
?>
