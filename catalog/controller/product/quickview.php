<?php
class ControllerProductQuickview extends Controller {
	public function index() {
		$this->data = $this->load->language('product/product');

		$product_id = isset($this->request->get['listing_id']) ? (int)$this->request->get['listing_id'] : 0;

        if (isset($this->request->get['preview_listing']) && isset($this->session->data['customer_token']) && $this->request->get['preview_listing'] == $this->session->data['customer_token']) {
			$preview_listing = true;
		} else {
			$preview_listing = false;
		}

		$this->load->model('catalog/product');

		$product_info = $product_id ? $this->model_catalog_product->getProduct($product_id, $preview_listing) : array();

		$this->setQueryParams(array(
			'search',
			'tag',
			'description',
			'path',
			'filter',
			'member_id',
			'manufacturer_id',
			'category_id',
			'sub_category',
			'sort',
			'order',
			'limit'
		));

		$url = $this->getQueryString();

        if (($product_info && $product_info['status'] == '1') || ($product_info && $preview_listing && $product_info['date_expiration'] >= date('Y-m-d H:i:s', time()))) {
			$product_data = $this->getChild('product/data/complete', $product_info);

			$this->data = array_merge($this->data, $product_data);

			$this->document->setTitle($product_data['page_title']);
			$this->document->setDescription($product_data['meta_description']);
			$this->document->setKeywords($product_data['meta_keyword']);
			$this->document->addLink($product_data['href'], 'canonical');

			$this->data['product_id'] = $product_id;
			$this->data['heading_title'] = $product_data['name'];
			$this->data['thumb'] = $product_data['image'];
			$this->data['action'] = $product_data['href'];
			$this->data['tab_question'] = $this->language->get('tab_question'); // sprintf($this->language->get('tab_question'), $product_data['questions']),
			$this->data['text_questions'] = sprintf($this->language->get('text_questions'), (int)$product_data['questions']);
			$this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_data['minimum']);

			if ($preview_listing) {
				$this->data['action'] .= ($url ? '&' : '?') . 'preview_listing=' . $this->request->get['preview_listing'];
			}

			// Shipping
			if ($this->config->get('member_data_field_shipping') && $this->config->get('product_shipping_status') && $product_data['shipping']) {
				$this->data['shipping'] = $this->language->get('text_yes');
				$this->data['shipping_display'] = true;
			} else {
				$this->data['shipping'] = $this->language->get('text_no');
				$this->data['shipping_display'] = false;
			}

			if (!$preview_listing) {
				$this->model_catalog_product->updateViewed($this->request->get['listing_id']);
			}

			$this->data['button_view'] = ($preview_listing ? $this->language->get('button_preview') : $this->language->get('button_view')) . ' ' . $this->language->get('text_product');

			$this->template = 'template/product/quickview.tpl';

			$this->response->setOutput($this->render());
		} else {
			$this->addBreadcrumb($this->language->get('text_error'), $this->url->link('product/product', $url . '&product_id=' . $product_id));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

			$this->document->setTitle($this->language->get('text_error'));

			$this->data['heading_title'] = $this->language->get('text_error');

			$this->data['search'] = $this->url->link('product/search');
			$this->data['continue'] = $this->url->link('common/home');

			$this->template = 'template/error/not_found.tpl';

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$this->response->setOutput($this->render());
		}
	}
}
?>
