<?php
class ControllerErrorProductRetired extends Controller {
	public function index() {
		if (!isset($this->request->get['route']) || empty($this->request->get['product_id'])) {
			$this->redirect($this->url->link('common/home'));
		}

		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('error/product_retired')
		);

		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

		$this->load->model('catalog/product');
		$this->load->model('catalog/member');
		$this->load->model('tool/image');

		$config_product_count = false; // $this->config->get('config_product_count')

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('meta_description'));
		$this->document->setKeywords($this->language->get('meta_keyword'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));

		if ($this->config->get('apac_status') && $this->config->get('apac_categories_status') && $this->config->get('apac_categories_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_categories'), $this->url->link('product/allcategories'));
		}

		if ($this->config->get('apac_status') && $this->config->get('apac_products_status') && $this->config->get('apac_products_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_products'), $this->url->link('product/allproducts'));
		}

		// product retired info
		$product_info = $this->model_catalog_product->getProductRetired($product_id);

		// get related member
		$product_member = array();

		if (!empty($product_info['member_info'])) {
			$member_info = $product_info['member_info'];
		} else if (isset($this->request->get['member_id'])) {
			$this->load->model('catalog/member');
			$member_info = $this->model_catalog_member->getMember($this->request->get['member_id']);
		}

		if (!empty($member_info)) {
			// $this->addBreadcrumb($member_info['member_account_name'], $this->url->link('product/member/info', 'member_id=' . $member_info['member_account_id']));

			$product_member = array(
				'name'        => $member_info['member_account_name'] . ($config_product_count ? sprintf($this->language->get('text_product_count'), $member_info['product_count']) : ''),
				'thumb'       => $this->model_tool_image->resize($member_info['member_account_image'], $this->config->get('config_image_product_width'), 120),
				'href'        => $this->url->link('product/member/info', 'member_id=' . $member_info['member_account_id'])
			);
		}

		// get related categories
		$category_id = 0; // required for search link
		$product_categories = array();

		if (!empty($product_info['categories'])) {
			$categories = $product_info['categories'];
		} else if (isset($this->request->get['path']) && !is_array($this->request->get['path'])) {
			$this->load->model('catalog/category');

			$categories = array();
			$path = '';
			$parts = explode('_', (string)$this->request->get['path']);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$categories[] = $this->model_catalog_category->getCategory($path_id);

				$category_id = $path_id;  // required for search link
			}
		}

		if (!empty($categories)) {
			foreach ($categories as $category) {
				$this->addBreadcrumb($category['name'], $this->url->link('product/category', 'path=' . $category['path']));

				if (utf8_strpos($category['name'], $this->language->get('heading_more')) !== false) {
					$category_name = $this->language->get('heading_more');
				} else if (utf8_strpos($category['name'], $this->language->get('heading_other')) !== false) {
					$category_name = $this->language->get('heading_other');
				} else {
					$category_name = $category['name'];
				}

				$product_categories[] = array(
					'name'  	=> $category_name . ($config_product_count ? sprintf($this->language->get('text_product_count'), $category['product_count']) : ''),
					'thumb'		=> $this->model_tool_image->resize($category['image'], $this->config->get('config_image_product_width'), 120),
					'href'		=> $this->url->link('product/category', 'path=' . $category['path'])
				);
			}
		}

		// get related brand
		$product_manufacturer = array();

		if (!empty($product_info['manufacturer_info'])) {
			$manufacturer_info = $product_info['manufacturer_info'];
		} else if (isset($this->request->get['manufacturer_id'])) {
			$this->load->model('catalog/manufacturer');
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);
		}

		if (!empty($manufacturer_info)) {
			$this->addBreadcrumb($manufacturer_info['name'], $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_info['manufacturer_id']));

			$product_manufacturer = array(
				'name'		  => $manufacturer_info['name'] . ($config_product_count ? sprintf($this->language->get('text_product_count'), $manufacturer_info['product_count']) : ''),
				'thumb'       => $this->model_tool_image->resize($manufacturer_info['image'], $this->config->get('config_image_product_width'), 120),
				'href'        => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer_info['manufacturer_id'])
			);
		}

		// final breadcrumb
		$this->addBreadcrumb($this->language->get('heading_title'), null);

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['member'] = $product_member;
		$this->data['categories'] = $product_categories;
		$this->data['manufacturer'] = $product_manufacturer;

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_error'] = $this->language->get('text_error');
		$this->data['text_search'] = $this->language->get('text_search');

		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['continue'] = $this->url->link('common/home');

		$this->data['button_search'] = $this->language->get('button_search');
		$this->data['search'] = $category_id ? $this->url->link('product/search', 'category=' . $category_id) : $this->url->link('product/search');

		$this->session->data['notification'] = $this->language->get('text_error');

		$this->document->addScript('catalog/view/root/wookmark/wookmark.min.js');

		$this->template = '/template/error/product_retired.tpl';

		$this->children = array(
			'common/notification',
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 410 Gone');

		$this->response->setOutput($this->render());
	}
}
?>
