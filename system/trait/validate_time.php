<?php
trait ValidateTime {
	protected $post_time;

	protected function getPostTime() {
		if (!isset($this->post_time)) {
			$this->setPostTime();
		}

		return $this->post_time;
	}

	protected function setPostTime() {
		$now = time();

		$this->session->data['post_time'] = $now;

		$this->post_time = $now;
	}

	protected function isPostTimeSet() {
		return isset($this->session->data['post_time']);
	}

	protected function validatePostTimeMin($min_time = 5) {
		if (!$this->isPostTimeSet()) {
			return false;
		}

		if (time() - $this->session->data['post_time'] < $min_time) {
			return false;
		} else {
			return true;
		}
	}

	protected function validatePostTimeMax($max_time = 300) {
		if (!$this->isPostTimeSet()) {
			return false;
		}

		if (time() - $this->session->data['post_time'] >= $max_time) {
			return false;
		} else {
			return true;
		}
	}
}

