<?php
class ControllerModuleFilter extends Controller {
	protected function index($setting) {
		if (isset($this->request->get['path']) && !is_array($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
		} else {
			$parts = array();
		}

		$category_id = end($parts);

		$this->load->model('catalog/category');

		$category_info = $this->model_catalog_category->getCategory($category_id);

		if (isset($this->request->get['manufacturer_id'])) {
			$manufacturer_id = (int)$this->request->get['manufacturer_id'];
		} else {
			$manufacturer_id = '';
		}

		if (isset($this->request->get['member_id'])) {
			$member_id = (int)$this->request->get['member_id'];
		} else {
			$member_id = '';
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = '';
		}

		if (isset($this->request->get['filter_manufacturer_id'])) {
			$filter_manufacturer_id = $this->request->get['filter_manufacturer_id'];
		} else {
			$filter_manufacturer_id = '';
		}

		if ($category_info || $manufacturer_id || $member_id || (isset($this->request->get['route']) && $this->request->get['route'] == 'product/allproducts')) {
			$this->load->language('module/filter');

			$this->load->model('catalog/filter');

			if ($this->config->get('config_product_count')) {
				$this->load->model('catalog/product');
			}

			$this->data['heading_title'] = $this->language->get('heading_title');

			$this->data['button_filter'] = $this->language->get('button_filter');

			$url = '';

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}

			if (isset($this->request->get['filter_manufacturer_id'])) {
				$url .= '&filter_manufacturer_id=' . $this->request->get['filter_manufacturer_id'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			if ($category_info) {
				$this->data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url));
			} else if ($manufacturer_id) {
				$this->data['action'] = str_replace('&amp;', '&', $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_id . $url));
			} else if ($member_id) {
				$this->data['action'] = str_replace('&amp;', '&', $this->url->link('product/member/info', 'member_id=' . $member_id . $url));
			} else {
				$this->data['action'] = str_replace('&amp;', '&', $this->url->link('product/allproducts', $url));
			}

			if (isset($this->request->get['filter'])) {
				$this->data['filter_category'] = explode(',', $this->request->get['filter']);
			} else {
				$this->data['filter_category'] = array();
			}

			$this->data['filter_groups'] = array();

			/* if ($category_info) {
				$filter_groups = $this->model_catalog_filter->getCategoryFilters($category_id);
			} else
			if (!$this->config->get('apac_products_filters_all')) {
				$data = array(
					'filter_category_filter' => 1
				);
				$filter_groups = $this->model_catalog_filter->getCategoryFiltersAll($data);
			} else {*/
				$filter_groups = $this->model_catalog_filter->getCategoryFiltersAll(); /* display all filters all the time */
			/* } */

			if ($filter_groups) {
				foreach ($filter_groups as $filter_group) {
					$filter_data = array();

					if ($manufacturer_id) {
						foreach ($filter_group['filter'] as $filter) {
							$data = array(
								'filter_manufacturer_id' => $manufacturer_id,
								'filter_filter'      => $filter['filter_id']
							);

							$filter_data[] = array(
								'filter_id' => $filter['filter_id'],
								'name'      => $filter['name'] . ($this->config->get('config_product_count') ? ' <u>' . $this->model_catalog_product->getTotalProducts($data) . '</u>' : '')
							);
						}
					} else if ($member_id) {
						foreach ($filter_group['filter'] as $filter) {
							$data = array(
								'filter_member_account_id'	=> $member_id,
								'filter_filter'      		=> $filter['filter_id']
							);

							$filter_data[] = array(
								'filter_id' => $filter['filter_id'],
								'name'      => $filter['name'] . ($this->config->get('config_product_count') ? ' <u>' . $this->model_catalog_product->getTotalProducts($data) . '</u>' : '')
							);
						}
					} else {
						foreach ($filter_group['filter'] as $filter) {
							$data = array(
								'filter_category_id' => $category_id,
								'filter_sub_category' => true,
								'filter_filter'      => $filter['filter_id']
							);

							$filter_data[] = array(
								'filter_id' => $filter['filter_id'],
								'name'      => $filter['name'] . ($this->config->get('config_product_count') ? ' <u>' . $this->model_catalog_product->getTotalProducts($data) . '</u>' : '')
							);
						}
					}

					$this->data['filter_groups'][] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $filter_data
					);
				}

				$this->template = '/template/module/filter.tpl';

				$this->render();
			}
		}
	}
}
?>
