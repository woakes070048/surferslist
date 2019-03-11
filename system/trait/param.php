<?php
trait Param {
	protected $params = array();

	protected function setQueryParams($names = array()) {
		foreach ($names as $name) {
			$this->params[$name] = isset($this->request->get[$name]) ? $this->request->get[$name] : '';
		}
	}

	protected function getQueryParam($name) {
		return isset($this->params[$name]) ? $this->params[$name] : '';
	}

	protected function getQueryString($exclude = array()) {
		return $this->getQueryStringOnlyThese(array_keys($this->params), $exclude);
	}

	protected function getQueryStringOnlyThese($names = array(), $exclude = array()) {
		if (!$names || !is_array($names) || !is_array($exclude)) {
			return '';
		}

		$url = '';

		foreach ($names as $name) {
			if (($exclude && in_array($name, $exclude)) || empty($this->params[$name]) ) {
				continue;
			}

			if (!is_array($this->params[$name])) {
				$url .= '&' . $name . '=' . urlencode(html_entity_decode($this->params[$name], ENT_QUOTES, 'UTF-8'));
			} else {
				foreach ($this->params[$name] as $value) {
					$url .= '&' . $name . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
				}
			}
		}

		return $url;
	}
}
?>
