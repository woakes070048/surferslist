<?php
class ControllerCommonModule extends Controller {
    private $route;

	public function __construct($registry) {
		parent::__construct($registry);

        $this->route = isset($this->request->get['route']) ? (string)$this->request->get['route'] : 'common/home';
	}

    protected function index() {
        return false;
    }

	protected function getModules($data) {
        if (empty($data) || !is_array($data) || !array_intersect(array('column_left', 'column_right', 'content_top', 'content_bottom'), $data)) {
            return array();
        }

		$this->load->model('design/layout');
		$this->load->model('setting/extension');

		$layout_id = 0;
        $position = $data[0];

		// Disabled Custom Layouts
		// if ($this->route == 'product/category' && isset($this->request->get['path']) && !is_array($this->request->get['path'])) {
		// 	$path = explode('_', (string)$this->request->get['path']);
        //
		// 	$layout_id = $this->model_design_layout->getCategoryLayoutId(end($path));
		// }
        //
		// if ($this->route == 'product/product' && isset($this->request->get['product_id'])) {
		// 	$layout_id = $this->model_design_layout->getProductLayoutId($this->request->get['product_id']);
		// }
        //
		// if ($this->route == 'information/information' && isset($this->request->get['information_id'])) {
		// 	$layout_id = $this->model_design_layout->getInformationLayoutId($this->request->get['information_id']);
		// }

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($this->route);
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}

		$module_data = array();

		$extensions = $this->model_setting_extension->getExtensions('module');

		foreach ($extensions as $extension) {
			$modules = $this->config->get($extension['code'] . '_module');

			if ($modules) {
				foreach ($modules as $module) {
					if ($module['layout_id'] == $layout_id && $module['position'] == $position && $module['status']) {
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

		$module_info = array();

		foreach ($module_data as $module) {
			$module = $this->getChild('module/' . $module['code'], $module['setting']);

			if ($module) {
				$module_info[] = $module;
			}
		}

		$this->setOutput($module_info);
	}

    protected function hasSidebar() {
        $sidebar_exists = false;

		if ((strpos($this->route, 'product') === 0
			&& $this->route != 'product/product'
			&& $this->route != 'product/search'
			&& $this->route != 'product/compare')
            || (strpos($this->route, 'blog') === 0)) {

			$sidebar_exists = true;
		}

        $this->setOutput($sidebar_exists);
    }
}

