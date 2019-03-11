<?php
class ControllerEmbedQuickview extends Controller {
	public function index() {
		$this->data = $this->load->language('product/product');

		$product_id = isset($this->request->get['listing_id']) ? (int)$this->request->get['listing_id'] : 0;

		$query_params_bool = array(
			'showheader',
			'hidefooter',
			'nosidebar',
			'nobackground',
			'customcolor'
		);

		foreach ($query_params_bool as $query_param) {
			${$query_param} = isset($this->request->get[$query_param]) && $this->request->get[$query_param] == 'true' ? true : false;
		}

		$query_params_empty = array(
			'color_primary',
			'color_secondary',
			'color_featured',
			'color_special'
		);

		foreach ($query_params_empty as $query_param) {
			${$query_param} = isset($this->request->get[$query_param]) ? $this->request->get[$query_param] : '';
		}

		$this->data['config_url'] = $this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url');
		$this->data['config_name'] = $this->config->get('config_name');

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/member');

		$product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info && $product_info['status'] == '1') {
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

			$this->setQueryParams(array(
				'path',
				'search',
				'tag',
				'description',
				'filter',
				'member_id',
				'manufacturer_id',
				'category_id',
				'sort',
				'order',
				'page',
				'limit'
			));

			$url = $this->getQueryString();

			$this->data['manufacturer_href'] = $this->url->link('embed/profile', 'profile_id=' . $product_info['customer_id'] . '&filter_manufacturer_id=' . $product_info['manufacturer_id'] . $url, 'SSL');

			$url = $this->getQueryStringOnlyThese(array_merge($query_params_bool, $query_params_empty));

			$this->data['categories'] = array();

			foreach ($product_info['categories'] as $product_category) {
				$this->data['categories'][] = array(
					'category_id' => $product_category['category_id'],
					'name'        => $product_category['name'],
					'href'        => $this->url->link('embed/profile', 'profile_id=' . $product_info['customer_id'] . '&filter_category_id=' . $product_category['category_id'] . $url, 'SSL')
				);
			}

			$this->data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$this->data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('embed/profile', 'profile_id=' . $product_info['customer_id'] . '&tag=' . trim($tag) . $url)
					);
				}
			}

			// shipping
			if ($product_info['shipping'] && $this->config->get('product_shipping_status')) {
				$this->data['shipping'] = $this->language->get('text_yes');
				$this->data['shipping_display'] = true;
			} else {
				$this->data['shipping'] = $this->language->get('text_no');
				$this->data['shipping_display'] = false;
			}

			$this->model_catalog_product->updateViewed($this->request->get['listing_id']);

			$this->template = '/template/embed/quickview.tpl';

			$this->response->setOutput($this->render());
		} else {
			$this->document->setTitle($this->language->get('text_error'));

			$this->addBreadcrumb($this->language->get('text_error'), $this->url->link('embed/not_found'));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();
			$this->data['heading_title'] = $this->language->get('text_error');
			$this->data['text_error'] = $this->language->get('text_error');

			$this->template = '/template/embed/not_found.tpl';

			$this->children = array(
				'embed/header',
				'embed/footer'
			);

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$this->response->setOutput($this->render());
		}
	}
}
?>
