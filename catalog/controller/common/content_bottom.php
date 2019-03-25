<?php
class ControllerCommonContentBottom extends Controller {
	protected function index() {
		$this->data['modules'] = $this->getChild('common/module/getModules', array('content_bottom'));

		$this->template = 'template/common/content_bottom.tpl';

		$this->render();
	}
}
?>
