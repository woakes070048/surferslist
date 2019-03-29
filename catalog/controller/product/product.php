<?php
class ControllerProductProduct extends Controller {
	use Admin;

	public function index() {
		$this->data = $this->load->language('product/product');

		$product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

        if (isset($this->request->get['preview_listing']) && isset($this->session->data['customer_token']) && $this->request->get['preview_listing'] == $this->session->data['customer_token']) {
			$preview_listing = true;
		} else {
			$preview_listing = false;
		}

		$this->load->model('catalog/product');

		$product_info = $product_id ? $this->model_catalog_product->getProduct($product_id, $preview_listing) : array();

		if (!$product_info || ($product_info['status'] == '0' && !$preview_listing)) {
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 307 Temporary Redirect');
			$this->response->redirect($this->url->link('error/product_unavailable', 'listing_id=' . $product_id));
		}

		$this->setQueryParams(array(
			'search',
			'tag',
			'description',
			'path',
			'filter',
			'member_id',
			'manufacturer_id',
			'category_id',
			'sort',
			'order',
			'limit'
		));

		$url = $this->getQueryString();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));

		if ($this->config->get('apac_status') && $this->config->get('apac_categories_status') && $this->config->get('apac_categories_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_categories'), $this->url->link('product/allcategories'));
		}

		if ($this->config->get('apac_status') && $this->config->get('apac_products_status') && $this->config->get('apac_products_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_products'), $this->url->link('product/allproducts'));
		}

		if (!empty($product_info['categories'])) {
			foreach ($product_info['categories'] as $product_category) {
				$this->addBreadcrumb($product_category['name'], $this->url->link('product/category', 'path=' . $product_category['path'] . $this->getQueryStringOnlyThese(array('sort', 'order', 'limit'))));

				// lowest/most-specific category
				$this->request->get['path'] = $product_category['path'];  // useful for featured listing module
			}
		}

		if (!empty($product_info['manufacturer'])) {
			$this->addBreadcrumb($product_info['manufacturer'], $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id'] . $this->getQueryStringOnlyThese(array('sort', 'order', 'limit'))));
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$this->addBreadcrumb($this->language->get('text_search'), $this->url->link('product/search', $url));
		}

        if (($product_info && $product_info['status'] == '1') || ($product_info && $preview_listing && $product_info['date_expiration'] >= date('Y-m-d H:i:s', time()))) {
			$this->load->model('tool/image');

			$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

			$product_data = $this->getChild('product/data/complete', $product_info);

			$this->data = array_merge($this->data, $product_data);

			$this->addBreadcrumb($product_data['name'], $product_data['href']);

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

			// member/profile
			$profile_active_account = false;
			$profile_name = $this->config->get('config_name');
			$profile_link = ''; // $this->url->link('product/member/info', 'member_id=' . $this->config->get('config_member_id'));

			if ($this->config->get('member_status') && $product_data['member']) {
				$profile_active_account = $product_data['member_customer_id'];
				$profile_name = $product_data['member']['name'];
				$profile_link = $product_data['member']['href'];
			}

			$heading_title = html_entity_decode($product_data['name'], ENT_QUOTES, 'UTF-8');

			if ($preview_listing) {
				$heading_title = $this->language->get('button_preview') . ': ' . $heading_title;
			}

			$this->data['heading_title'] = $heading_title;
			$this->data['category_icon'] = !empty($product_data['categories'][0]['name']) ? friendly_url($product_data['categories'][0]['name']) : '';
			$this->data['page_shortlink'] = $product_data['keyword'] ? $this->config->get('config_url') . $product_data['keyword'] : '';
			$this->data['action'] = $product_data['href'];
			$this->data['tab_question'] = $this->language->get('tab_question'); // sprintf($this->language->get('tab_question'), $product_data['questions']),
			$this->data['text_questions'] = sprintf($this->language->get('text_questions'), (int)$product_data['questions']);
			$this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_data['minimum']);
			$this->data['help_discussion'] = !$this->customer->isLogged() && !$this->isAdmin()
				? $this->language->get('help_discussion') . '<br />' . sprintf($this->language->get('help_unauthorized'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'))
				: $this->language->get('help_discussion');

			// discuss
			// if (!$this->customer->validateLogin()) {
			// 	$this->data['question_unauthorized'] = $this->language->get('error_question_logged');
			// 	unset($this->session->data['warning']);
			// } else if (!$this->customer->validateProfile()) {
			// 	$this->data['question_unauthorized'] = $this->language->get('error_question_membership');
			// 	unset($this->session->data['warning']);
			// } else {
				// allow anyone to discuss
				$this->data['question_unauthorized'] = !$this->customer->isLogged() && !$this->isAdmin() ? true : false;
			// }

			// catpcha
			$catpcha_fields = array('captcha', 'captcha_widget_id', 'g-recaptcha-response');

			foreach ($catpcha_fields as $captcha_field) {
	            $this->data[$captcha_field] = isset($this->request->post[$captcha_field]) ? $this->request->post[$captcha_field] : '';
	        }

			// Image(s)
			$this->data['image_small'] = $product_data['image'];
			$this->data['image_large'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'), 'fw');
			$this->data['image_thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), 'autocrop');

			$this->data['images'] = array();

			foreach ($product_info['images'] as $product_image) {
				$this->data['images'][] = array(
					'large' => $this->model_tool_image->resize($product_image['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'), 'fw'),
					'small' => $this->model_tool_image->resize($product_image['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'fw'),
					'thumb' => $this->model_tool_image->resize($product_image['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), 'autocrop')
				);
			}

			// Options
			$this->data['options'] = array();

			foreach ($product_data['options'] as $option) {
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$option_value_data = array();

					foreach ($option['option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_data['tax_class_id'], $this->config->get('config_tax') ? 'P' : false));
							} else {
								$price = false;
							}

							$option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $this->model_tool_image->resize($option_value['image'], 40, 40),
								'price'                   => $price,
								'price_prefix'            => $option_value['price_prefix']
							);
						}
					}

					$this->data['options'][] = array(
						'product_option_id' => $option['product_option_id'],
						'option_id'         => $option['option_id'],
						'name'              => $option['name'],
						'type'              => $option['type'],
						'option_value'      => $option_value_data,
						'required'          => $option['required']
					);
				} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
					$this->data['options'][] = array(
						'product_option_id' => $option['product_option_id'],
						'option_id'         => $option['option_id'],
						'name'              => $option['name'],
						'type'              => $option['type'],
						'option_value'      => $option['option_value'],
						'required'          => $option['required']
					);
				}
			}

			// Questions / Discussion
			$this->data['question_status'] = $this->config->get('config_review_status') && !$preview_listing && ($profile_active_account || !$profile_link);

			// shipping
			if ($this->config->get('member_data_field_shipping') && $this->config->get('product_shipping_status') && $product_data['shipping']) {
				$product_shipping = $this->model_catalog_product->getProductShipping($product_id);
				$this->data['shipping'] = $product_shipping
					? sprintf($this->language->get('text_shipping_yes'), $this->url->link('ajax/product/shipping_rates', 'shipping_rate_id=' . $product_id))
					: $this->language->get('text_yes');
				$this->data['shipping_display'] = true;
				$this->data['shipping_display_dimensions'] = $product_shipping ? false : true;
			} else {
				$this->data['shipping'] = $this->language->get('text_shipping_no');
				$this->data['shipping_display'] = false;
				$this->data['shipping_display_dimensions'] = false;
			}

			// footnote
			$this->data['footnote'] = '';

			if ($product_data['type_id'] == 1 && $product_data['quantity'] > 1) {
				$this->data['footnote'] = $this->config->get('config_stock_display')
					? sprintf($this->language->get('text_instock'), $product_data['quantity'])
					: sprintf($this->language->get('text_instock'), '');
			} else if ($profile_active_account && $profile_link) {
				if ($product_data['type_id'] == 1) {
					$this->data['footnote'] = sprintf($this->language->get('text_action_buy_now'), $profile_link, $profile_name);
				} else if ($product_data['type_id'] == 0) {
					$this->data['footnote'] = $product_data['stock_status_id'] != $this->config->get('config_stock_status_id')
						? $this->language->get('text_stock') . ' ' . $product_data['stock_status']
						: sprintf($this->language->get('text_action_contact'), $profile_link, $profile_name);
				} else {
					$this->data['footnote'] = sprintf($this->language->get('text_action_discuss'), $profile_link, $profile_name);
				}
			} else if ($product_data['learn_more']) {
				$this->data['footnote'] = sprintf($this->language->get('text_action_learn_more'), $product_data['learn_more']);
			}

			// related listings, prev/next and page views
			$product_prev = array();
			$product_next = array();
			$filter_category_id = 0;
			$filter_member_account_id = 0;
			$max_prevnext_length = 35;
			$nav_cols = 1;
			$more_url = '';
			$prev_url = '';
			$prev_title = '';
			$next_url = '';
			$next_title = '';
			$back_url = '';
			$url = '';

			$category_info = end($product_data['categories']);

			if (!$preview_listing && $category_info) {
				$filter_category_id = $category_info['category_id'];
				$url .= '&path=' . $category_info['path'];

				$more_url = $this->url->link('product/category', $url);
				$this->data['more_title'] = utf8_strlen($category_info['name']) > $max_prevnext_length ? utf8_substr($category_info['name'], 0, $max_prevnext_length) . $this->language->get('text_ellipses') : $category_info['name'];
			} else if ($product_data['member']) {
				$filter_member_account_id = $product_data['member']['member_id'];

				// if (!empty($category_info)) {
				// 	$filter_category_id = $category_info['category_id'];
				// 	$url .= '&filter_category_id=' . $category_info['category_id'];
				// }

				$more_url = $this->url->link('product/member/info', 'member_id=' . $product_data['member']['member_id'] . $url) . '#member-listings';
				$this->data['more_title'] = utf8_strlen($product_data['member']['name']) > $max_prevnext_length ? utf8_substr($product_data['member']['name'], 0, $max_prevnext_length) . $this->language->get('text_ellipses') : $product_data['member']['name'];
			} else {
				$more_url = $this->url->link('product/allproducts');
				$this->data['more_title'] = $this->language->get('text_all_products');
			}

			$sort = $this->getQueryParam('sort') ?: $this->config->get('apac_products_sort_default');
			$sort_order = $this->getQueryParam('order') ?: (($sort == 'p.date_added') ? 'DESC' : 'ASC');

			$products = !$preview_listing ? $this->model_catalog_product->getProductsIndexes(array(
				'filter_category_id' => $filter_category_id,
				'filter_sub_category' => false,
				'filter_member_account_id' => $filter_member_account_id,
				'filter_listing_type' => $product_data['quantity'] >= 0 ? array('0', '1') : array('-1'),
				'filter_country_id' => isset($this->session->data['shipping_country_id']) ? $this->session->data['shipping_country_id'] : 0,
				'filter_zone_id' => isset($this->session->data['shipping_zone_id']) ? $this->session->data['shipping_zone_id'] : 0,
				'filter_location' => isset($this->session->data['shipping_location']) ? $this->session->data['shipping_location'] : '',
				'sort' => $sort,
				'order' => $sort_order
			), true) : array();

			$product_related = $this->model_catalog_product->getProductRelatedRandom($products);

			$this->data['products'] = $this->getChild('product/data/list', $product_related);

			// get the index of this listing
			$product_index = key(array_filter($products, function ($item) use ($product_data) {
				return $item['product_id'] === $product_data['product_id'];
			}));

			if ($product_index !== null && array_key_exists($product_index, $products)) {
				reset($products);

				// set array pointer to index position
				while (key($products) !== $product_index) {
					next($products);
				}

				// one step fwd, two back
				if ($sort_order == 'ASC') {
					$product_prev = isset($products[$product_index - 1]) ? $products[$product_index - 1] : false;
					$product_next = isset($products[$product_index + 1]) ? $products[$product_index + 1] : false;
				} else {
					$product_prev = isset($products[$product_index + 1]) ? $products[$product_index + 1] : false;
					$product_next = isset($products[$product_index - 1]) ? $products[$product_index - 1] : false;
				}
			}

			if ($product_prev) {
				$prev_url = $this->url->link('product/product', 'product_id=' .  $product_prev['product_id'] . $url);
				$prev_title = utf8_strlen($product_prev['name']) > $max_prevnext_length ? utf8_substr($product_prev['name'], 0, $max_prevnext_length) . $this->language->get('text_ellipses') : $product_prev['name'];
				$nav_cols++;
			}

			if ($product_next) {
				$next_url = $this->url->link('product/product', 'product_id=' .  $product_next['product_id'] . $url);
				$next_title = utf8_strlen($product_next['name']) > $max_prevnext_length ? utf8_substr($product_next['name'], 0, $max_prevnext_length) . $this->language->get('text_ellipses') : $product_next['name'];
				$nav_cols++;
			}

			// back/previous page
			if (isset($this->request->server['HTTP_REFERER'])
				&& ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl')))
				&& (!$product_prev || strpos($this->request->server['HTTP_REFERER'], $prev_url) === false)
				&& (!$product_next || strpos($this->request->server['HTTP_REFERER'], $next_url) === false)) {
				$this->session->data['back_url'] = $this->request->server['HTTP_REFERER'];
			}

			if (isset($this->session->data['back_url'])) {
				$back_url = $this->session->data['back_url'];
			}

			if ($preview_listing) {
				$url_add = $url ? '&' : '?';
				$prev_url .= $product_prev ? $url_add . 'preview_listing=' . $this->request->get['preview_listing'] : '';
				$next_url .= $product_next ? $url_add . 'preview_listing=' . $this->request->get['preview_listing'] : '';
			}

			$this->data['more_url'] = $more_url;
			$this->data['prev_url'] = $prev_url;
			$this->data['prev_title'] = $prev_title;
			$this->data['next_url'] = $next_url;
			$this->data['next_title'] = $next_title;
			$this->data['back_url'] = $back_url;
			$this->data['nav_cols'] = $nav_cols;

			$this->data['preview_mode'] = $preview_listing;

			// update view count
			if (!$preview_listing) {
				$this->model_catalog_product->updateViewed($this->request->get['product_id']);
			}

			// title and metadata
			$this->document->setTitle($product_data['page_title']);
			$this->document->setDescription($product_data['meta_description']);
			$this->document->setKeywords($product_data['meta_keyword']);
			$this->document->setUrl($product_data['href']);

			$image_info = $this->model_tool_image->getFileInfo($this->data['image_large']);

			if ($image_info) {
				$this->document->setImage($this->data['image_large'], $image_info['mime'], $image_info[0], $image_info[1]);
			}

			$this->document->addLink($product_data['href'], 'canonical');

			// Scripts
			$this->document->addScript('catalog/view/root/zoom/jquery.zoom.min.js');

			$this->data['option_timepicker'] = false;
			$this->data['option_ids_file_type'] = array();

			if ($this->data['options']) {
				foreach ($this->data['options'] as $option_info) {
					if ($option_info['type'] == 'file') {
						$this->data['option_ids_file_type'][] = $option_info['product_option_id'];
					} else if ($option_info['type'] == 'date' || $option_info['type'] == 'datetime' || $option_info['type'] == 'time') {
						$this->data['option_timepicker'] = true;
					}
				}
			}

			if ($this->data['option_ids_file_type']) {
				$this->document->addScript('catalog/view/root/javascript/ajaxupload.js');
			}

			if ($this->data['option_timepicker']) {
				$this->document->addScript('catalog/view/root/ui/jquery-ui-timepicker-addon.js');
			}

			$this->template = 'template/product/product.tpl';

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
}
?>
