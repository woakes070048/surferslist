<?php
class ControllerCommonColumnRight extends Controller {
	protected function index() {
		$this->data['modules'] = $this->getChild('common/module/getModules', array('column_right'));

		$this->data['sidebar_exists'] = $this->getChild('common/module/hideSidebar');

		$this->template = '/template/common/column_right.tpl';

		$this->render();
	}
}
?>
