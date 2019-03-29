<?php
class Document {
	private $title;
	private $description;
	private $keywords;
	private $open_graph = array('type' => 'website');
	private $links = array();
	private $styles = array();
	private $scripts = array();

	public function setTitle($title) {
		$this->title = $title;
		$this->open_graph['title'] = $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setDescription($description) {
		$this->description = $description;
		$this->open_graph['description'] = $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setKeywords($keywords) {
		$this->keywords = $keywords;
	}

	public function getKeywords() {
		return $this->keywords;
	}

	public function setType($type) {
		$this->open_graph['type'] = $type;
	}

	public function hasUrl() {
		return !empty($this->open_graph['url']);
	}

	public function setUrl($url) {
		$this->open_graph['url'] = $url;
	}

	public function hasImage() {
		return !empty($this->open_graph['image']);
	}

	public function setImage($image, $image_type = '', $image_width = '', $image_height = '') {
		$this->open_graph['image'] = $image;

		if ($image_type) {
			$this->open_graph['image_type'] = $image_type;
		}

		if ($image_width) {
			$this->open_graph['image_width'] = $image_width;
		}

		if ($image_height) {
			$this->open_graph['image_height'] = $image_height;
		}
	}

	public function getOpenGraph() {
		return $this->open_graph;
	}

	public function addLink($href, $rel) {
		$this->links[md5($href)] = array(
			'href' => $href,
			'rel'  => $rel
		);
	}

	public function getLinks() {
		return $this->links;
	}

	public function addStyle($href, $rel = 'stylesheet', $media = 'screen') {
		$this->styles[md5($href)] = array(
			'href'  => $href,
			'rel'   => $rel,
			'media' => $media
		);
	}

	public function getStyles() {
		return $this->styles;
	}

	public function addScript($script) {
		$this->scripts[md5($script)] = $script;
	}

	public function getScripts() {
		return $this->scripts;
	}
}
