<?php
class ControllerModuleStore extends Controller {
	protected function index() {
		$status = true;

		if ($this->config->get('store_admin')) {
			$this->load->library('user');

			$this->user = new User($this->registry);

			$status = $this->user->isLogged();
		}

		if ($status) {
			$this->load->language('module/store');

			$this->data['heading_title'] = $this->language->get('heading_title');

			$this->data['text_store'] = $this->language->get('text_store');

			$this->data['store_id'] = $this->config->get('config_store_id');

			$this->data['stores'] = array();

			$this->data['stores'][] = array(
				'store_id' => 0,
				'name'     => $this->language->get('text_default'),
				'url'      => HTTP_SERVER . 'index.php?route=common/home'
			);

			$this->load->model('setting/store');

			$results = $this->model_setting_store->getStores();

			foreach ($results as $result) {
				$this->data['stores'][] = array(
					'store_id' => $result['store_id'],
					'name'     => $result['name'],
					'url'      => $result['url'] . 'index.php?route=common/home'
				);
			}

			$this->template = 'template/module/store.tpl';

			$this->render();
		}
	}
}

