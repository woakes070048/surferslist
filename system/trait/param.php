<?php
trait Param {
	protected $params;

	protected function setQueryParams($params = array()) {
		$this->params = $params;
	}

	protected function getQueryParams($exclude = array()) {
		if (!isset($this->params)) {
			return '';
		}

		return $this->getQueryParamsOnlyThese($this->params, $exclude);
	}

	protected function getQueryParamsOnlyThese($params = array(), $exclude = array()) {
		if (!$params || !is_array($params) || !is_array($exclude)) {
			return '';
		}

		$url = '';

		foreach ($params as $param) {
			if ($exclude && in_array($param, $exclude)) {
				continue;
			}

			if (isset($this->request->get[$param]) && !is_array($this->request->get[$param])) {
				$url .= '&' . $param . '=' . urlencode(html_entity_decode($this->request->get[$param], ENT_QUOTES, 'UTF-8'));
			}
		}

		return $url;
	}
}
?>
