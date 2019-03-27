<?php
class ControllerProductAllCategories extends Controller {
	public function index() {
		if (!$this->config->get('apac_status') || !$this->config->get('apac_categories_status')) {
	  		$this->redirect($this->url->link('common/home', ''));
		}

		$this->data = array_merge($this->load->language('product/common'), $this->load->language('product/category'));

		$this->load->model('catalog/category');
		$this->load->model('catalog/product');

		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : $this->config->get('apac_categories_sort_default');
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';

		// get All Categories info from APAC Module config settings
		$language_id = (int)$this->config->get('config_language_id');
		$all_categories_description = $this->config->get('apac_categories_description');

		$all_categories_info = array(
			'name'					=> $all_categories_description[$language_id]['name'],
			'description'			=> $all_categories_description[$language_id]['description'],
			'meta_description'		=> $all_categories_description[$language_id]['meta_description'],
			'meta_keyword'			=> $all_categories_description[$language_id]['meta_keyword'],
			'keyword'				=> $this->config->get('apac_categories_keyword')
		);

		$text_all_categories = (!empty($all_categories_info['name']) ? $all_categories_info['name'] : $this->language->get('text_all_categories'));

		$this->document->setTitle($text_all_categories);

		if (!empty($all_categories_info['meta_description'])) {
			$this->document->setDescription($all_categories_info['meta_description']);
		} else {
			$this->document->setDescription($this->language->get('text_all_categories_meta_description'));
		}

		if (!empty($all_categories_info['meta_keyword'])) {
			$this->document->setKeywords($all_categories_info['meta_keyword']);
		} else {
			$this->document->setKeywords($this->language->get('text_all_categories_meta_keyword'));
		}

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_all_categories'), $this->url->link('product/allcategories'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['heading_title'] = $text_all_categories;
		$this->data['text_all_categories'] = $text_all_categories;

		$this->data['button_continue'] = $this->language->get('button_back');

		$this->data['description'] = !empty($all_categories_info['description']) ? html_entity_decode($all_categories_info['description'], ENT_QUOTES, 'UTF-8') : false;

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$this->data['indexes'] = array();

		$data = array(
			'filter_status'		 => 1,
			'sort'               => $sort,
			'order'              => $order,
			'start'              => 0,
			'limit'              => 999
		);

		$categories = $this->model_catalog_category->getAllCategories($data);

		foreach ($categories as $category) {
			$path_name = $category['path_name'];

			$data = array(
				'filter_category_id' => $category['category_id'],
				'sort'               => $sort,
				'order'              => $order,
				'start'              => 0,
				'limit'              => 999
			);

			if ($this->config->get('config_product_count')) {
				$product_total = $this->model_catalog_product->getTotalProducts($data);
			}

			if (!$this->config->get('apac_categories_index')) {
				$this->data['categories'][] = array(
					'name'  => $path_name . ($this->config->get('config_product_count') ? ' <u>' . $product_total . '</u>' : ''),
					'href'  => $this->url->link('product/category', 'path=' . $category['path'] . $url),
					'icon'	=> friendly_url($category['name'])
				);
			} else {
				$key = utf8_strpos($path_name, $this->language->get('text_separator'))
					? utf8_substr(utf8_strtoupper($path_name), 0, utf8_strpos($path_name, $this->language->get('text_separator')))
					: utf8_strtoupper($path_name);

				if (!isset($this->data['indexes'][$key])) {
					$this->data['indexes'][$key]['name'] = $key;
					$this->data['indexes'][$key]['href'] = $this->url->link('product/allcategories', $url);
				}

				$this->data['indexes'][$key]['category'][] = array(
					'name'  => $category['name'] . ($this->config->get('config_product_count') ? ' (' . $product_total . ')' : ''),
					'href'  => $this->url->link('product/category', 'path=' . $category['path'] . $url),
					'icon'	=> friendly_url($key)
				);
			}
		}

		$this->addSort($this->language->get('text_default'), 'sort_order_path-ASC', $this->url->link('product/allcategories','&sort=sort_order_path&order=ASC'));

		if ($this->config->get('apac_categories_sort_name')) {
			$this->addSort($this->language->get('text_name_asc'), 'name-ASC', $this->url->link('product/allcategories','&sort=name&order=ASC'));
			$this->addSort($this->language->get('text_name_desc'), 'name-DESC', $this->url->link('product/allcategories','&sort=name&order=DESC'));
		}

		if ($this->config->get('apac_categories_sort_path_name')) {
			$this->addSort($this->language->get('text_path_name_asc'), 'path_name-ASC', $this->url->link('product/allcategories','&sort=path_name&order=ASC'));
			$this->addSort($this->language->get('text_path_name_desc'), 'path_name-DESC', $this->url->link('product/allcategories','&sort=path_name&order=DESC'));
		}

		if ($this->config->get('apac_categories_sort_sort_order')) {
			$this->addSort($this->language->get('text_sort_order_asc'), 'sort_order-ASC', $this->url->link('product/allcategories','&sort=sort_order&order=ASC'));
			$this->addSort($this->language->get('text_sort_order_desc'), 'sort_order-DESC', $this->url->link('product/allcategories','&sort=sort_order&order=DESC'));
		}

		if ($this->config->get('apac_categories_sort_path_sort_order')) {
			$this->addSort($this->language->get('text_sort_order_path_asc'), 'sort_order_path-ASC', $this->url->link('product/allcategories','&sort=sort_order_path&order=ASC'));
			$this->addSort($this->language->get('text_sort_order_path_desc'), 'sort_order_path-DESC', $this->url->link('product/allcategories','&sort=sort_order_path&order=DESC'));
		}

		$this->data['sorts'] = $this->getSorts();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->data['compare'] = $this->url->link('product/compare', '');
		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('product/allproducts', '');
		$this->data['search'] = $this->url->link('product/search', '');
		$this->data['reset'] = $this->url->link('product/allcategories', '');
		$this->data['continue'] = $this->url->link('common/home', '');

		$this->document->addScript('catalog/view/root/wookmark/wookmark.min.js');

		$this->template = '/template/product/allcategories.tpl';

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
