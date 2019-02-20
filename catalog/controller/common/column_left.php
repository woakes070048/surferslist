<?php
class ControllerCommonColumnLeft extends Controller {
	protected function index() {
		$this->load->model('design/layout');
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('catalog/information');

		$route = isset($this->request->get['route']) ? (string)$this->request->get['route'] : 'common/home';

		$sidebar_exists = false;

		if (!isset($this->session->data['warning'])
			&& ($route == 'product/member'
			|| ($route == 'product/member/info' && isset($this->request->get['member_id']))
			|| $route == 'product/manufacturer'
			|| ($route == 'product/manufacturer/info' && isset($this->request->get['manufacturer_id']))
			|| $route == 'product/allproducts'
			|| $route == 'product/allcategories'
			|| $route == 'product/featured'
			|| $route == 'product/special'
			|| $route == 'product/category')) {

			$sidebar_exists = true;
		}

		$layout_id = 0;

		// Disabled Custom Layouts
		// if ($route == 'product/category' && isset($this->request->get['path']) && !is_array($this->request->get['path'])) {
		// 	$path = explode('_', (string)$this->request->get['path']);
        //
		// 	$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		// }
        //
		// if ($route == 'product/product' && isset($this->request->get['product_id'])) {
		// 	$layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
		// }
        //
		// if ($route == 'information/information' && isset($this->request->get['information_id'])) {
		// 	$layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
		// }

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}

		$module_data = array();

		$this->load->model('setting/extension');

		$extensions = $this->model_setting_extension->getExtensions('module');

		foreach ($extensions as $extension) {
			$modules = $this->config->get($extension['code'] . '_module');

			if ($modules) {
				foreach ($modules as $module) {
					if ($module['layout_id'] == $layout_id && $module['position'] == 'column_left' && $module['status']) {
						$module_data[] = array(
							'code'       => $extension['code'],
							'setting'    => $module,
							'sort_order' => $module['sort_order']
						);
					}
				}
			}
		}

		$sort_order = array();

		foreach ($module_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $module_data);

		$this->data['modules'] = array();

		foreach ($module_data as $module) {
			$module = $this->getChild('module/' . $module['code'], $module['setting']);

			if ($module) {
				$this->data['modules'][] = $module;
			}
		}

		$this->data['sidebar_exists'] = $sidebar_exists;

		$this->template = '/template/common/column_left.tpl';

		$this->render();
	}
}
?>
