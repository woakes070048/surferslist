<?php
class ControllerModuleCategory extends Controller {
	protected function index($setting) {
		$this->load->language('module/category');

		$config_product_count = false; // $this->config->get('config_product_count');

		$this->data['heading_title'] = $this->language->get('heading_title');

		if (isset($this->request->get['path']) && !is_array($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
			$cache = md5((string)$this->request->get['path']);
		} else {
			$parts = array();
			$cache = 'base';
		}

		$this->data['category_id'] = isset($parts[0]) ? $parts[0] : 0;
		$this->data['child_id'] = isset($parts[1]) ? $parts[1] : 0;

		$this->data['categories'] = $this->cache->get('category.module.category.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($this->data['categories'] === false) {
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');

			$this->data['categories'] = array();

			$categories = $this->model_catalog_category->getCategories(0);

			foreach ($categories as $category_1) {
				if ($category_1['top']) {
					if (strpos($category_1['name'], $this->language->get('heading_more')) !== false) {
						$category_1_name = ucwords($this->language->get('heading_more'));
					} else if (strpos($category_1['name'], $this->language->get('heading_other')) !== false) {
						$category_1_name = ucwords($this->language->get('heading_other'));
					} else {
						$category_1_name = ucwords($category_1['name']);
					}

					$level_2_data = array();
					$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);
					foreach ($categories_2 as $category_2) {
						$category_2_name = ucwords(str_replace($category_1['name'], '', $category_2['name']));

						$level_3_data = array();

						$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);

						foreach ($categories_3 as $category_3) {
							if ($config_product_count) {
								$data_category_3 = array(
									'filter_category_id'  => $category_3['category_id'],
									'filter_sub_category' => true
								);

								$product_total_category_3 = $this->model_catalog_product->getTotalProducts($data_category_3);
							}

							$category_3_name = ucwords(str_replace(array($category_1['name'], $category_2_name), '', $category_3['name']));

							$level_3_data[] = array(
								'name' 			=> $category_3_name . ($config_product_count ? ' <u>' . $product_total_category_3 . '</u>' : ''),
								'alt' 			=> friendly_url($category_3['name']),
								'href' 			=> $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id']),
								'image' 		=> false,
								'id' 			=> $category_3['category_id'],
								'parent_id' 	=> $category_3['parent_id']
							);
						}

						if ($config_product_count) {
							$data_category_2 = array(
								'filter_category_id'  => $category_2['category_id'],
								'filter_sub_category' => true
							);

							$product_total_category_2 = $this->model_catalog_product->getTotalProducts($data_category_2);
						}

						$level_2_data[] = array(
							'name' 			=> $category_2_name . ($config_product_count ? ' <u>' . $product_total_category_2 . '</u>' : ''),
							'alt' 			=> friendly_url($category_2['name']),
							'children' 		=> $level_3_data,
							'href' 			=> $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id']),
							'image' 		=> false,
							'id' 			=> $category_2['category_id'],
							'parent_id' 	=> $category_2['parent_id']
						);
					}

					if ($config_product_count) {
						$data_category_1 = array(
							'filter_category_id'  => $category_1['category_id'],
							'filter_sub_category' => true
						);

						$product_total_category_1 = $this->model_catalog_product->getTotalProducts($data_category_1);
					}

					$this->data['categories'][] = array(
						'name' 			=> $category_1_name . ($config_product_count ? ' <u>' . $product_total_category_1 . '</u>' : ''),
						'alt' 			=> friendly_url($category_1['name']),
						'children' 		=> $level_2_data,
						'href' 			=> $this->url->link('product/category', 'path=' . $category_1['category_id']),
						'image' 		=> true,
						'id' 			=> $category_1['category_id']
					);
				}
			}

			$this->cache->set('category.module.category.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $this->data['categories'], 60 * 60 * 24 * 30); // 1 month cache expiration
		}

		$this->template = '/template/module/category.tpl';

		$this->render();
	}
}
?>
