<?php
class ControllerCommonColumnLeft extends Controller {
	protected function index() {
		$this->data['modules'] = $this->getChild('common/module/getModules', array('column_left'));

		$this->data['sidebar_exists'] = $this->getChild('common/module/hasSidebar');

		$this->template = '/template/common/column_left.tpl';

		$this->render();
	}
}
?>
