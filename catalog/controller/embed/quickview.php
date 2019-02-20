<?php
class ControllerEmbedQuickview extends Controller {
	public function index() {
		$this->data = $this->load->language('product/product');

		$product_id = isset($this->request->get['listing_id']) ? (int)$this->request->get['listing_id'] : 0;

        if (isset($this->request->get['preview_listing']) && isset($this->session->data['customer_token']) && $this->request->get['preview_listing'] == $this->session->data['customer_token']) {
			$preview_listing = true;
		} else {
			$preview_listing = false;
		}

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

		$product_info = $this->model_catalog_product->getProduct($product_id, $preview_listing);

        if (($product_info && $product_info['status'] == '1') || ($product_info && $preview_listing && $product_info['date_expiration'] >= date('Y-m-d H:i:s', time()))) {
			$page_title = $product_info['name'];
			$page_title_parts = array('manufacturer', 'model', 'size', 'year');

			foreach ($page_title_parts as $needle) {
				if ($product_info[$needle] && strpos($product_info['name'], $product_info[$needle]) === false && $product_info[$needle] != 'Other') {
					$page_title .=  ' ' . $product_info[$needle];
				}
			}

			if (!empty($page_title_category)) $page_title .= ' | ' . $page_title_category;

			$this->document->setTitle($page_title);
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);

			$this->data['heading_title'] = $product_info['name'];

			$this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$this->data['tab_question'] = sprintf($this->language->get('tab_question'), $product_info['questions']);

			$this->load->model('tool/image');

			$this->data['product_id'] = $product_id;

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

			$url = $this->getQueryParams();

			$this->data['product_href'] = $this->url->link('product/product', 'product_id=' . $this->request->get['listing_id'] . $url . '&source=embed', 'SSL');

			// Featured Listing
			$this->data['featured'] = $product_info['featured'];

			// Quantity
			$this->data['quantity'] = $product_info['quantity'];

			// Categories
			$url = $this->getQueryParamsOnlyThese(array_merge($query_params_bool, $query_params_empty));

			$this->data['categories'] = array();

			foreach ($product_info['categories'] as $product_category) {
				$this->data['categories'][] = array(
					'category_id' => $product_category['category_id'],
					'name'        => $product_category['name'],
					'href'        => $this->url->link('embed/profile', 'profile_id=' . $product_info['customer_id'] . '&filter_category_id=' . $product_category['category_id'] . $url, 'SSL')
				);
			}

			// Brand
			$this->data['manufacturer_image'] = $this->model_tool_image->resize($product_info['manufacturer_image'], 100, 40, "fh");
			$this->data['manufacturer'] = $product_info['manufacturer'];
			$this->data['manufacturer_href'] = $this->url->link('embed/profile', 'profile_id=' . $product_info['customer_id'] . '&filter_manufacturer_id=' . $product_info['manufacturer_id'] . $url, 'SSL');
			$this->data['model'] = $product_info['model'];
			$this->data['size'] = $product_info['size'];
			$this->data['year'] = ($product_info['year'] != '0000' ? $product_info['year'] : $this->language->get('text_unknown'));

			// Condition
			$this->data['condition'] = $product_info['condition'];

			// Type
			$this->data['type_id'] = $product_info['type_id'];
			$this->data['type'] = $product_info['type_id'] == 1 ? $this->language->get('text_buy_now') : ($product_info['type_id'] == 0 ? $this->language->get('text_classified') : $this->language->get('text_shared'));

			// Location
			$this->data['location'] = $product_info['location'];
			$this->data['location_zone'] = $product_info['zone'];
			$this->data['location_country'] = $product_info['country']; // e.g. USA
			$this->data['location_href'] = $this->url->link('product/search', 'country=' . $product_info['country_id'] . '&state=' . $product_info['zone_id'], 'SSL');

			// Shipping
			if ($product_info['shipping'] && $this->config->get('product_shipping_status')) {
				$this->data['shipping'] = $this->language->get('text_yes');
				$this->data['shipping_display'] = true;
			} else {
				$this->data['shipping'] = $this->language->get('text_no');
				$this->data['shipping_display'] = false;
			}

			// Member
			$this->data['member'] = array();
			$member_info = $product_info['member_info'];

			if ($this->config->get('member_status') && $member_info) {
				$this->data['member_id'] = (int)$member_info['customer_id'];
				$this->data['profile_name'] = $member_info['member_account_name'];

				if (!empty($member_info['member_account_image'])) {
					$member_image = $this->model_tool_image->resize($member_info['member_account_image'], 40, 40, 'autocrop');
				} else {
					$member_image = $this->model_tool_image->resize('no_image.jpg', 40, 40, "");
				}

				$this->data['member'] = array(
					'member_id'   => $member_info['customer_id'],
					'name'        => $member_info['member_account_name'],
					'image'       => $member_image,
					'href'        => $member_info['member_account_id'] ? $this->url->link('product/member/info', 'member_id=' . $member_info['member_account_id'], 'SSL') : '',
					'group_id'    => $member_info['member_group_id'],
					'group'       => $member_info['member_group'],
					'rating'      => (int)$member_info['rating'],
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$member_info['reviews'])
				);
			} else {
				$this->data['member_id'] = 0;
				$this->data['profile_name'] = $this->config->get('config_name');
			}

			$this->data['points'] = $product_info['points'];
			$this->data['reward'] = $product_info['reward'];

			if ($product_info['type_id'] == 1 && $product_info['quantity'] > 1) {
				if ($this->config->get('config_stock_display')) {
					$this->data['stock'] = sprintf($this->language->get('text_instock'), $product_info['quantity']);
				} else {
					$this->data['stock'] = sprintf($this->language->get('text_instock'), '');
				}
			} else if ($product_info['type_id'] == 1) {
				$this->data['stock'] = $this->language->get('text_stock_buy_now');
			} else if ($product_info['type_id'] == 0) {
				if ($product_info['stock_status_id'] != $this->config->get('config_stock_status_id')) {
					$this->data['stock'] = $product_info['stock_status'];
				} else {
					$this->data['stock'] = $this->language->get('text_stock_contact');
				}
			} else {
				$this->data['stock'] = $this->language->get('text_stock_shared');
			}

			if ($product_info['image']) {
				$this->data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'autocrop');
			} else {
				$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'autocrop');
			}

			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				if ($product_info['price'] != 0) {
					$this->data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$this->data['price'] = $this->language->get('text_free');
				}
			} else {
				$this->data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$this->data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				$this->data['salebadges'] = round((($product_info['price'] - $product_info['special']) / $product_info['price']) * 100, 0);
				$this->data['savebadges'] = $this->currency->format(($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))) - ($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'))));
			} else {
				$this->data['special'] = false;
				$this->data['salebadges'] = false;
				$this->data['savebadges'] = false;
			}

			if ($this->config->get('config_tax')) {
				$this->data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price']);
			} else {
				$this->data['tax'] = false;
			}

			$this->data['discounts'] = array();

			foreach ($product_info['discounts'] as $discount) {
				$this->data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
				);
			}

			// Description
			$this->data['description'] = nl2br(convert_links(strip_tags_decode($product_info['description'])));

			// Tags
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

			if (!$preview_listing) {
				$this->model_catalog_product->updateViewed($this->request->get['listing_id']);
			}

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
