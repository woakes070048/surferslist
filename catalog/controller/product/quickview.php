<?php
class ControllerProductQuickview extends Controller {
	public function index() {
		$this->data = $this->load->language('product/product');

		$product_id = !empty($this->request->get['listing_id']) ? (int)$this->request->get['listing_id'] : 0;

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

		$url = $this->getQueryParams();

        if (($product_info && $product_info['status'] == '1') || ($product_info && $preview_listing && $product_info['date_expiration'] >= date('Y-m-d H:i:s', time()))) {
			$this->load->model('tool/image');

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

			$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

			$result_product_data = $this->cache->get('product_' . (int)$product_id . '.min.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id);

			if ($result_product_data) {
				$this->document->addLink($result_product_data['href'], 'canonical');

				$this->data['product_href'] = $result_product_data['href']; //  . $url
				$this->data['manufacturer'] = $result_product_data['manufacturer'];
				$this->data['thumb'] = $result_product_data['image'];
			} else {
				$this->document->addLink($this->url->link('product/product', 'product_id=' . $product_id), 'canonical');

				$this->data['product_href'] = $this->url->link('product/product', 'product_id=' . $this->request->get['listing_id'] . $url, 'SSL');
				$this->data['manufacturer'] = $product_info['manufacturer'];

				if ($product_info['image']) {
					$this->data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'fw');
				} else {
					$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'fw');
				}
			}

			if ($preview_listing) {
				$url_add = $url ? '&' : '?';
				$this->data['product_href'] .= $url_add . 'preview_listing=' . $this->request->get['preview_listing'];
			}

			$this->data['heading_title'] = $product_info['name'];
			$this->data['product_id'] = $product_id;
			$this->data['model'] = $product_info['model'];
			$this->data['size'] = $product_info['size'];
			$this->data['year'] = $product_info['year'] != '0000' ? $product_info['year'] : ''; // $this->language->get('text_unknown')
			$this->data['manufacturer_image'] = !empty($product_info['manufacturer_image']) && $product_info['manufacturer_id'] != 1 ? $this->model_tool_image->resize($product_info['manufacturer_image'], 100, 40, "fh") : false;
			$this->data['manufacturer_href'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id'], 'SSL');
			$this->data['location_href'] = $this->url->link('product/search', 'country=' . $product_info['country_id'] . '&state=' . $product_info['zone_id'], 'SSL');
			$this->data['featured'] = $product_info['featured'];
			$this->data['condition'] = $product_info['condition'];
			$this->data['location'] = $product_info['location'];
			$this->data['location_zone'] = $product_info['zone'];
			$this->data['location_country'] = $product_info['country']; // e.g. USA
			$this->data['quantity'] = $product_info['quantity'];
			// $this->data['minimum'] = $product_info['minimum'] ? $product_info['minimum'] : 1;
			$this->data['type_id'] = $product_info['type_id'];
			$this->data['type'] = $product_info['type_id'] == 1 ? $this->language->get('text_buy_now') : ($product_info['type_id'] == 0 ? $this->language->get('text_classified') : $this->language->get('text_shared'));
			$this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$this->data['tab_question'] = sprintf($this->language->get('tab_question'), $product_info['questions']);
			$this->data['points'] = $product_info['points'];
			$this->data['reward'] = $product_info['reward'];

			// Description and Learn More link
			$learn_more = '';
			$description = nl2br(strip_tags_decode($product_info['description']), false);

			if (strpos($description, '<br>') !== false) {
				$explode_description = explode('<br>', $description, 2);

				if (is_url($explode_description[0])) {
					$learn_more = $explode_description[0];
					$description = $explode_description[1];
				}
			} else if (is_url($description)) {
				$learn_more = $description;
				$description = '';
			}

			$description = convert_links(strip_tags_decode($description));

			$this->data['description'] = $description;
			$this->data['button_view'] = ($preview_listing ? $this->language->get('button_preview') : $this->language->get('button_view')) . ' ' . $this->language->get('text_product');
			$this->data['learn_more'] = $learn_more;

			// Price/Special
			$price = false;
			$special = false;
			$salebadges = false;
			$savebadges = false;
			$tax = false;

			if (!$this->config->get('config_customer_price') || $this->customer->isLogged()) {
				$price = $product_info['price'] != 0
					? $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
					: $this->language->get('text_free');
			}

			if ((float)$product_info['special']) {
				$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				$salebadges = round((($product_info['price'] - $product_info['special']) / $product_info['price']) * 100, 0);
				$savebadges = $this->currency->format(($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))) - ($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'))));
			}

			if ($this->config->get('config_tax')) {
				$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price']);
			}

			$this->data['price'] = $price;
			$this->data['special'] = $special;
			$this->data['salebadges'] = $salebadges;
			$this->data['savebadges'] = $savebadges;
			$this->data['tax'] = $tax;

			// Categories
			$this->data['categories'] = array();

			foreach ($product_info['categories'] as $product_category) {
				$this->data['categories'][] = array(
					'category_id' => $product_category['category_id'],
					'name'        => $product_category['name'],
					'href'        => $this->url->link('product/category', 'path=' . $product_category['path'], 'SSL')
				);
			}

			// Member
			$this->data['member'] = array();
			$this->data['member_customer_id'] = isset($product_info['customer_id']) ? (int)$product_info['customer_id'] : 0;

			$member_info = $product_info['member_info'];

			if ($this->config->get('member_status') && $member_info) {
				$this->data['profile_name'] = $member_info['member_account_name'];

				$this->data['member'] = array(
					'member_id'   => $member_info['customer_id'],
					'name'        => $member_info['member_account_name'],
					'image'       => !empty($member_info['member_account_image']) ? $this->model_tool_image->resize($member_info['member_account_image'], 40, 40, 'autocrop') : false,
					'href'        => $member_info['member_account_id'] ? $this->url->link('product/member/info', 'member_id=' . $member_info['member_account_id'], 'SSL') : '',
					'group_id'    => $member_info['member_group_id'],
					'group'       => $member_info['member_group'],
					'rating'      => (int)$member_info['rating'],
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$member_info['reviews'])
				);
			} else {
				$this->data['profile_name'] = $this->config->get('config_name');
			}

			// Shipping
			if ($product_info['shipping'] && $this->config->get('product_shipping_status')) {
				$this->data['shipping'] = $this->language->get('text_yes');
				$this->data['shipping_display'] = true;
			} else {
				$this->data['shipping'] = $this->language->get('text_no');
				$this->data['shipping_display'] = false;
			}

			// Discounts
			$this->data['discounts'] = array();

			foreach ($product_info['discounts'] as $discount) {
				$this->data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
				);
			}

			if (!$preview_listing) {
				$this->model_catalog_product->updateViewed($this->request->get['listing_id']);
			}

			$this->template = '/template/product/quickview.tpl';

			$this->response->setOutput($this->render());
		} else {
			$this->addBreadcrumb($this->language->get('text_error'), $this->url->link('product/product', $url . '&product_id=' . $product_id));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

			$this->document->setTitle($this->language->get('text_error'));

			$this->data['heading_title'] = $this->language->get('text_error');

			$this->data['search'] = $this->url->link('product/search', '', 'SSL');
			$this->data['continue'] = $this->url->link('common/home');

			$this->template = '/template/error/not_found.tpl';

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
