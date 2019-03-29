<?php
class ControllerCommonNotification extends Controller {
	protected function index() {
		$this->load->language('common/notification');

		$this->data['text_notification'] = $this->language->get('text_notification');

		if (isset($this->session->data['warning'])) {
			$this->data['error_warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->session->data['notification'])) {
			$this->data['notification'] = $this->session->data['notification'];
			unset($this->session->data['notification']);
		} else {
			$this->data['notification'] = '';
		}

		$this->template = 'template/common/notification.tpl';

		$this->render();
	}
}

