<?php
class ControllerModuleWelcome extends Controller {
	protected function index($setting) {
		$this->load->language('module/welcome');

		$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));

		$this->data['message'] = html_entity_decode($setting['description'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

		$this->template = '/template/module/welcome.tpl';

		$this->render();
	}
}
?>
