<?php
class ControllerModuleNote extends Controller {
	protected function index($setting) {
		static $module = 0;

		if (strlen($setting['note_box_heading'][$this->config->get('config_language_id')]) > 0) {
      		$this->data['note_box_heading'] = $setting['note_box_heading'][$this->config->get('config_language_id')];
		} else {
      		$this->data['note_box_heading'] = false;
		}

		if (strlen($setting['note_button'][$this->config->get('config_language_id')]) > 0){
      		$this->data['note_button'] = $setting['note_button'][$this->config->get('config_language_id')];
		} else {
      		$this->data['note_button'] = false;
		}

		if (strlen($setting['note_content'][$this->config->get('config_language_id')]) > 0) {
      		$this->data['note_content'] = html_entity_decode($setting['note_content'][$this->config->get('config_language_id')]);
		} else {
      		$this->data['note_content'] = false;
		}

		$this->data['button_position'] = $setting['button_position'];
		$this->data['note_url'] = $setting['note_url'];
		$this->data['note_image'] = $setting['note_image'];
		$this->data['image_location'] = $setting['image_location']

		$this->template = 'template/module/note.tpl';

		$this->data['module'] = $module++;

		$this->render();
	}
}
?>
