<?php
class ControllerCommonContentTop extends Controller {
	protected function index() {
		$this->data['modules'] = $this->getChild('common/module/getModules', array('content_top'));

		$this->template = 'template/common/content_top.tpl';

		$this->render();
	}
}
?>
