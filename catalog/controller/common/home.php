<?php
class ControllerCommonHome extends Controller {
	public function index() {
        $this->data = array_merge($this->load->language('module/search'), $this->load->language('common/home'));

		$this->document->setTitle($this->config->get('config_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
		$this->document->setUrl($this->url->link('common/home'));

		if (is_file(DIR_IMAGE . $this->config->get('config_banner_image'))) {
			$this->load->model('tool/image');
			$image = $this->model_tool_image->resize($this->config->get('config_banner_image'), 2000, 800);
			$image_info = $this->model_tool_image->getFileInfo($image);

			if ($image_info) {
				$this->document->setImage($image, $image_info['mime'], $image_info[0], $image_info[1]);
			}
		}

		$this->data['heading_title'] = $this->config->get('config_title');

		// Search
		$this->data['heading_search'] = $this->language->get('heading_title');

		$this->load->model('catalog/category');

		$this->data['categories'] = $this->cache->get('category.module.search.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($this->data['categories'] === false) {
			$this->data['categories'] = array();

			$categories_1 = $this->model_catalog_category->getCategories(0);

			foreach ($categories_1 as $category_1) {
				if ($category_1['top']) {
					if (strpos($category_1['name'], $this->language->get('heading_more')) !== false) {
						$category_1_name = ucwords($this->language->get('heading_more'));
					} else if (strpos($category_1['name'], $this->language->get('heading_other')) !== false) {
						$category_1_name = ucwords($this->language->get('heading_other'));
					} else {
						$category_1_name = ucwords($category_1['name']);
					}

					$this->data['categories'][] = array(
						'category_id' => $category_1['category_id'],
						'name'        => strtoupper($category_1_name),
						'url'		  => $this->url->link('product/category', 'path=' . $category_1['category_id'])
					);
				}
			}

			$this->cache->set('category.module.search.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data['categories'], 60 * 60 * 24 * 30); // 1 month cache expiration
		}

		$this->data['country_id'] = isset($this->session->data['shipping_country_id']) ? $this->session->data['shipping_country_id'] : '';
		$this->data['zone_id'] = isset($this->session->data['shipping_zone_id']) ? $this->session->data['shipping_zone_id'] : '';
		$this->data['location'] = isset($this->session->data['shipping_location']) ? $this->session->data['shipping_location'] : '';

		$this->data['search'] = $this->url->link('product/search');
		$this->data['product_random'] = $this->url->link('product/allproducts', 'sort=random');
		$this->data['post'] = $this->url->link('account/anonpost', '', 'SSL');
		$this->data['about'] = $this->url->link('information/information', 'information_id=4'); // About Us

		$this->data['help_about'] = sprintf($this->language->get('help_about'), $this->config->get('config_name')); // About Us

		$this->template = 'template/common/home.tpl';

		$this->children = array(
			'common/notification',
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}
}
?>
