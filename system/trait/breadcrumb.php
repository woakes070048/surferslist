<?php
trait Breadcrumb {
	protected $breadcrumbs;

	protected function getBreadcrumbs() {
		if (!isset($this->breadcrumbs)) {
			return array();
		}

		return $this->breadcrumbs;
	}

	protected function addBreadcrumb($text, $href) {
		if (!$text || (!$href && !is_null($href))) {
			return false;
		}

		if (!empty($this->breadcrumbs)) {
			$seperator = $this->language->get('text_separator');
		} else {
			$seperator = false;
		}

		$this->breadcrumbs[] = array(
			'text'      => $text,
			'href'      => $href,
			'separator' => $seperator
		);
	}
}

