<?php
class ControllerModuleGoogleTalk extends Controller {
	protected function index() {
		$this->load->language('module/google_talk');

		$this->data['heading_title'] = $this->language->get('heading_title');

		if ($this->request->isSecure()) {
			$this->data['code'] = str_replace('http', 'https', html_entity_decode($this->config->get('google_talk_code')));
		} else {
			$this->data['code'] = html_entity_decode($this->config->get('google_talk_code'));
		}

		$this->template = 'template/module/google_talk.tpl';

		$this->render();
	}
}

