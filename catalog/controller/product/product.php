<?php
class ControllerProductProduct extends Controller {
	use Admin;

	public function index() {
		$this->data = $this->load->language('product/product');

		$this->data['language_id'] = (int)$this->config->get('config_language_id');

		$product_id = !empty($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

        if (isset($this->request->get['preview_listing']) && isset($this->session->data['customer_token']) && $this->request->get['preview_listing'] == $this->session->data['customer_token']) {
			$preview_listing = true;
		} else {
			$preview_listing = false;
		}

		$this->load->model('catalog/product');

		$product_info = $product_id ? $this->model_catalog_product->getProduct($product_id, $preview_listing) : array();

		if (!$product_info || ($product_info['status'] == '0' && !$preview_listing)) {
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 307 Temporary Redirect');
			$this->response->redirect($this->url->link('error/product_unavailable', 'listing_id=' . $product_id, 'SSL'));
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

		$url = $this->getQueryParams();

		// START Breadcrumbs
		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));

		if ($this->config->get('apac_status') && $this->config->get('apac_categories_status') && $this->config->get('apac_categories_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_categories'), $this->url->link('product/allcategories'));
		}

		if ($this->config->get('apac_status') && $this->config->get('apac_products_status') && $this->config->get('apac_products_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_products'), $this->url->link('product/allproducts'));
		}

		if (!empty($product_info['categories'])) {
			foreach ($product_info['categories'] as $product_category) {
				$this->addBreadcrumb($product_category['name'], $this->url->link('product/category', 'path=' . $product_category['path'] . $this->getQueryParamsOnlyThese(array('sort', 'order', 'limit'))));

				// lowest/most-specific category
				$this->request->get['path'] = $product_category['path'];  // useful for featured listing module
			}
		}

		if (!empty($product_info['manufacturer'])) {
			$this->addBreadcrumb($product_info['manufacturer'], $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id'] . $this->getQueryParamsOnlyThese(array('sort', 'order', 'limit'))));
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$this->addBreadcrumb($this->language->get('text_search'), $this->url->link('product/search', $url));
		}
		// END Breadcrumbs

		// START Product Info
        if (($product_info && $product_info['status'] == '1') || ($product_info && $preview_listing && $product_info['date_expiration'] >= date('Y-m-d H:i:s', time()))) {
			$this->load->model('tool/image');

			// final breadcrumb - product link
			$this->addBreadcrumb($product_info['name'], $this->url->link('product/product', 'product_id=' . $product_id . $url));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

			// Heading Title
			$page_title_keywords = array_map('trim', explode(' ', $product_info['name']));
			$page_title_parts = array('year', 'manufacturer', 'model', 'size');
			$page_title_exclude = array('Other', '0000');
			$page_title_category = !empty($product_category['name']) ? $product_category['name'] : '';

			foreach ($page_title_parts as $value) {
				if ($product_info[$value] && utf8_strlen($product_info[$value]) <= 20) {
					if ($value == 'manufacturer' && $product_info['manufacturer_id'] == 1) {
						continue;
					}

					// skip if any partial keyword already exists
					foreach ($page_title_keywords as $page_title_keyword) {
						if ($page_title_keyword && strpos($product_info[$value], $page_title_keyword) !== false) {
							continue 2;
						}
					}

					$product_keywords = explode(' ', $product_info[$value]);

					foreach ($product_keywords as $product_keyword) {
						if (strpos($page_title_category, $product_keyword) === false
							&& !in_array($product_keyword, $page_title_keywords)
							&& !in_array($product_keyword, $page_title_exclude)) {
							$page_title_keywords[] = $product_keyword;
						}
					}
				}
			}

			$heading_title = implode(' ', $page_title_keywords);

			if ($page_title_category) {
				$heading_title .= ' | ' . $page_title_category;
			}

			$product_canonical = $this->url->link('product/product', 'product_id=' . $product_id);
			$meta_description = $product_info['meta_description'];
			$meta_keyword = $product_info['meta_keyword']
				? implode(', ', array_unique(array_merge(array_map('trim', explode(',', $product_info['meta_keyword'])), $page_title_keywords)))
				: implode(', ', $page_title_keywords);

			$this->data['heading_title'] = $preview_listing ? $this->language->get('button_preview') . ': ' . $product_info['name'] : $product_info['name'];
			$this->data['category_icon'] = !empty($product_info['categories'][0]['name']) ? friendly_url($product_info['categories'][0]['name']) : '';
			$this->data['page_canonical'] = $product_canonical;
			$this->data['page_shortlink'] = $product_info['keyword'] ? $this->config->get('config_url') . $product_info['keyword'] : '';

			// Language
			$this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$this->data['help_discussion'] = !$this->customer->isLogged() && !$this->isAdmin()
				? $this->language->get('help_discussion') . '<br />' . sprintf($this->language->get('help_unauthorized'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'))
				: $this->language->get('help_discussion');
			$this->data['tab_question'] = $this->language->get('tab_question'); // sprintf($this->language->get('tab_question'), $product_info['questions']);

			$this->data['product_id'] = $product_id;

			// Featured Listing
			$this->data['featured'] = $product_info['featured'];

			// Compare
			$this->data['compare'] = false; // in_array($product_info['product_id'], $this->session->data['compare']) ? true : false;

			// Quantity, Minimum
			$this->data['quantity'] = $product_info['quantity'];
			$this->data['minimum'] = $product_info['minimum'] ? $product_info['minimum'] : 1;

			// Categories
			$this->data['categories'] = array();

			foreach ($product_info['categories'] as $product_category) {
				$this->data['categories'][] = array(
					'category_id' => $product_category['category_id'],
					'name'        => $product_category['name'],
					'path'		  => $product_category['path'],
					'href'        => $this->url->link('product/category', 'path=' . $product_category['path'], 'SSL')
				);
			}

			// Manufacturer / Brand
			$this->data['manufacturer'] = $product_info['manufacturer'];
			$this->data['manufacturer_image'] = !empty($product_info['manufacturer_image']) && $product_info['manufacturer_id'] != 1 ? $this->model_tool_image->resize($product_info['manufacturer_image'], 100, 40, "fh") : false;
			$this->data['manufacturer_href'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id'], 'SSL');
			$this->data['model'] = $product_info['model'];
			$this->data['size'] = $product_info['size'];
			$this->data['year'] = $product_info['year'] != '0000' ? $product_info['year'] : ''; // $this->language->get('text_unknown')

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
			if ($this->config->get('member_data_field_shipping') && $product_info['shipping'] && $this->config->get('product_shipping_status')) {
				$product_shipping = $this->model_catalog_product->getProductShipping($product_id);
				$this->data['shipping'] = $product_shipping
					? sprintf($this->language->get('text_shipping_yes'), $this->url->link('product/product/shipping_rates', 'shipping_rate_id=' . $product_id, 'SSL'))
					: $this->language->get('text_yes');
				$this->data['shipping_display'] = true;
				$this->data['shipping_display_dimensions'] = $product_shipping ? false : true;
			} else {
				$this->data['shipping'] = $this->language->get('text_shipping_no');
				$this->data['shipping_display'] = false;
				$this->data['shipping_display_dimensions'] = false;
			}

			// Dimensions
			$this->data['weight'] = $this->weight->format($product_info['weight'], $product_info['weight_class_id']);
			$this->data['length'] = $this->length->format($product_info['length'], $product_info['length_class_id']);
			$this->data['width'] = $this->length->format($product_info['width'], $product_info['length_class_id']);
			$this->data['height'] = $this->length->format($product_info['height'], $product_info['length_class_id']);

			// Dates
			$days_passed_added = floor(abs(time() - strtotime($product_info['date_added'])) / (60*60*24));
			$days_passed_modified = floor(abs(time() - strtotime($product_info['date_modified'])) / (60*60*24));
			$this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($product_info['date_added'])) . ' ' . sprintf($this->language->get('text_days_ago'), $days_passed_added);
			$this->data['date_modified'] = date($this->language->get('date_format_short'), strtotime($product_info['date_modified'])) . ' ' . sprintf($this->language->get('text_days_ago'), $days_passed_modified);

			// Question
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

			// Catpcha
			$catpcha_fields = array('captcha', 'captcha_widget_id', 'g-recaptcha-response');

			foreach ($catpcha_fields as $captcha_field) {
	            $this->data[$captcha_field] = isset($this->request->post[$captcha_field]) ? $this->request->post[$captcha_field] : '';
	        }

			// Member
			$this->data['member'] = array();

			$member_customer_id = isset($product_info['customer_id']) ? (int)$product_info['customer_id'] : 0;

			$profile_active_account = $this->config->get('member_status') && $member_customer_id;

			$member_info = $product_info['member_info'];

			if ($this->config->get('member_status') && $member_info) {
				$profile_name = $member_info['member_account_name'];
				$profile_link = $this->url->link('product/member/info', 'member_id=' . $member_info['member_account_id'], 'SSL');

				$this->data['member'] = array(
					'member_customer_id' => $member_info['customer_id'],
					'member_id'	  => $member_info['member_account_id'],
					'name'        => $member_info['member_account_name'],
					'image'       => !empty($member_info['member_account_image']) ? $this->model_tool_image->resize($member_info['member_account_image'], 40, 40, 'autocrop') : false,
					'href'        => $profile_link,
					'group_id'    => $member_info['member_group_id'],
					'group'       => $member_info['member_group'],
					'rating'      => (int)$member_info['rating'],
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$member_info['reviews'])
				);

				$meta_description = sprintf($this->language->get('meta_description_prefix_member'), $meta_description, $member_info['member_account_name']);  // , date($this->language->get('date_format_medium'), strtotime($product_info['date_added']))
			} else {
				$profile_name = $this->config->get('config_name');
				$profile_link = ''; // $this->url->link('product/member/info', 'member_id=3', 'SSL');

				$meta_description = sprintf($this->language->get('meta_description_prefix_anon'), $meta_description);
			}

			$this->data['profile_name'] = $profile_name;
			$this->data['member_customer_id'] = $member_customer_id;

			$this->data['points'] = $product_info['points'];
			$this->data['reward'] = $product_info['reward'];

			if ($product_info['image']) {
				$this->data['image_large'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'), 'fw');
				$this->data['image_small'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'fw');
				$this->data['image_thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), 'autocrop');
			} else {
				$this->data['image_large'] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'), "");
				$this->data['image_small'] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), "");
				$this->data['image_thumb'] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), "");
			}

			// Additional Images
			$this->data['images'] = array();

			foreach ($product_info['images'] as $product_image) {
				$this->data['images'][] = array(
					'large' => $this->model_tool_image->resize($product_image['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'), 'fw'),
					'small' => $this->model_tool_image->resize($product_image['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'fw'),
					'thumb' => $this->model_tool_image->resize($product_image['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), 'autocrop')
				);
			}

			// Price / Value
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				if ($product_info['price'] != 0) {
					$this->data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$this->data['price'] = $this->language->get('text_free');
				}
			} else {
				$this->data['price'] = false;
			}

			// Special
			if ((float)$product_info['special']) {
				$this->data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				$this->data['salebadges'] = round((($product_info['price'] - $product_info['special']) / $product_info['price']) * 100, 0);
				$this->data['savebadges'] = $this->currency->format(($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))) - ($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'))));
			} else {
				$this->data['special'] = false;
				$this->data['salebadges'] = false;
				$this->data['savebadges'] = false;
			}

			// Tax
			if ($this->config->get('config_tax')) {
				$this->data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price']);
			} else {
				$this->data['tax'] = false;
			}

			// Discounts
			$this->data['discounts'] = array();

			foreach ($product_info['discounts'] as $discount) {
				$this->data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
				);
			}

			// Options
			$this->data['options'] = array();

			foreach ($product_info['options'] as $option) {
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$option_value_data = array();

					foreach ($option['option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false));
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

			// Attributes
			$this->data['attribute_groups'] = $product_info['attributes'];

			// Questions / Discussion
			$this->data['question_status'] = $this->config->get('config_review_status') && ($profile_active_account || !$profile_link);
			$this->data['text_questions'] = sprintf($this->language->get('text_questions'), (int)$product_info['questions']);

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
			$this->data['learn_more'] = $learn_more;

			// Footnote
			$this->data['footnote'] = '';

			if ($product_info['type_id'] == 1 && $product_info['quantity'] > 1) {
				$this->data['footnote'] = $this->config->get('config_stock_display')
					? sprintf($this->language->get('text_instock'), $product_info['quantity'])
					: sprintf($this->language->get('text_instock'), '');
			} else if ($profile_active_account && $profile_link) {
				if ($product_info['type_id'] == 1) {
					$this->data['footnote'] = sprintf($this->language->get('text_action_buy_now'), $profile_link, $profile_name);
				} else if ($product_info['type_id'] == 0) {
					$this->data['footnote'] = $product_info['stock_status_id'] != $this->config->get('config_stock_status_id')
						? $this->language->get('text_stock') . ' ' . $product_info['stock_status']
						: sprintf($this->language->get('text_action_contact'), $profile_link, $profile_name);
				} else {
					$this->data['footnote'] = sprintf($this->language->get('text_action_discuss'), $profile_link, $profile_name);
				}
			} else if ($learn_more) {
				$this->data['footnote'] = sprintf($this->language->get('text_action_learn_more'), $learn_more);
			}

			// Related Product Listings
			$this->data['products'] = $this->getChild('product/product/list', $product_info['related']);

			// Keyword Tags
			$this->data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$this->data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			// Prev / Next Listings and Update Views
			$product_prev = array();
			$product_next = array();

			$nav_cols = 1;
			$max_prevnext_length = 35;

			$url = '';
			$filter_category_id = '';
			$filter_member_account_id = '';
			$more_url = '';
			$prev_url = '';
			$prev_title = '';
			$next_url = '';
			$next_title = '';
			$back_url = '';

			$category_info = end($this->data['categories']);

			if (!$preview_listing && $category_info) {
				$filter_category_id = $category_info['category_id'];
				$url .= '&path=' . $category_info['path'];

				$more_url = $this->url->link('product/category', $url, 'SSL');
				$this->data['more_title'] = utf8_strlen($category_info['name']) > $max_prevnext_length ? utf8_substr($category_info['name'], 0, $max_prevnext_length) . $this->language->get('text_ellipses') : $category_info['name'];
			} else if ($member_info) {
				$filter_member_account_id = $member_info['member_account_id'];

				// if (!empty($category_info)) {
				// 	$filter_category_id = $category_info['category_id'];
				// 	$url .= '&filter_category_id=' . $category_info['category_id'];
				// }

				$more_url = $this->url->link('product/member/info', 'member_id=' . $member_info['member_account_id'] . $url, 'SSL') . '#member-listings';
				$this->data['more_title'] = utf8_strlen($member_info['member_account_name']) > $max_prevnext_length ? utf8_substr($member_info['member_account_name'], 0, $max_prevnext_length) . $this->language->get('text_ellipses') : $member_info['member_account_name'];
			} else {
				$more_url = $this->url->link('product/allproducts');
				$this->data['more_title'] = $this->language->get('text_all_products');
			}

			$data = array();

			if ($filter_category_id) {
				$data['filter_category_id'] = $filter_category_id;
				$data['filter_sub_category'] = false;
			} else if ($filter_member_account_id) {
				$data['filter_member_account_id'] = $filter_member_account_id;
			}

			// if (in_array($product_info['type_id'], array(0, 1))) {
			// 	$data['filter_listing_type'] = array('0', '1');  // for-sale
			// }

			$data['sort'] = isset($this->request->get['sort']) ? $this->request->get['sort'] : $this->config->get('apac_products_sort_default');
			$data['order'] = isset($this->request->get['order']) ? $this->request->get['order'] : (($data['sort'] == 'p.date_added') ? 'DESC' : 'ASC');

			$products = $this->model_catalog_product->getProductsIndexes($data);

			$session_data = $this->session->data;

			if (!empty($this->session->data['shipping_country_id'])) {
				$products = array_filter($products, function ($item) use ($session_data) {
					return $item['country_id'] === $session_data['shipping_country_id'];
				});
			}

			if (!empty($this->session->data['shipping_zone_id'])) {
				$products = array_filter($products, function ($item) use ($session_data) {
					return $item['zone_id'] === $session_data['shipping_zone_id'];
				});
			}

			if (!empty($this->session->data['shipping_location'])) {
				$products = array_filter($products, function ($item) use ($session_data) {
					return $item['location'] === $session_data['shipping_location'];
				});
			}

			// get the index of this listing
			$product_index = key(array_filter($products, function ($item) use ($product_info) {
				return $item['product_id'] === $product_info['product_id'];
			}));

			if ($product_index !== null && array_key_exists($product_index, $products)) {
				reset($products);

				// set array pointer to index position
				while (key($products) !== $product_index) {
					next($products);
				}

				// one step fwd, two back
				if ($data['order'] == 'ASC') {
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

			// Back/Previous Page
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

			// Update Views
			if (!$preview_listing) {
				$this->model_catalog_product->updateViewed($this->request->get['product_id']);
			}

			// Metadata
			$image_info = $this->model_tool_image->getFileInfo($this->data['image_large']);

			$this->document->setTitle($heading_title);
			$this->document->setDescription($meta_description);
			$this->document->setKeywords($meta_keyword);
			$this->document->setUrl($product_canonical);

			if ($image_info) {
				$this->document->setImage($this->data['image_large'], $image_info['mime'], $image_info[0], $image_info[1]);
			}

			$this->document->addLink($product_canonical, 'canonical');

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

			$this->template = '/template/product/product.tpl';

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
		// END Product Info
	}

	protected function getProductData($data) {
        if (empty($data)) {
            return array();
        }

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

        $product_data = $this->cache->get('product_' . (int)$data['product_id'] . '.min.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id);

        if ($product_data === false) {
            $thumb = $this->model_tool_image->resize($data['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'), 'fw');
            $image = $this->model_tool_image->resize($data['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'fw');

            $data_description = remove_links(preg_replace('/\s+/', ' ', strip_tags_decode($data['description'])));

            if (!$data_description) {
                if ($data['year'] != '0000') {
                    $data_description .= $data['year'] . ' ';
                }

                if ($data['manufacturer_id'] > 1) {
                    $data_description .= $data['manufacturer'] . ' ';
                }

                $data_description .= $data['model'];

                if (utf8_strpos(trim($data['size']), ' ') === false && utf8_strlen($data['size']) < 10) {
                    $data_description .= ' ' . $data['size'];
                }
            } else if (utf8_strlen($data_description) > 80) {
                $data_description = utf8_substr($data_description, 0, 80) . $this->language->get('text_ellipses');
            }

            $product_data = array(
                'href'              => $this->url->link('product/product', 'product_id=' . $data['product_id'], 'SSL'),
                'product_id'        => $data['product_id'],
                'customer_id'       => $data['customer_id'],
                'member_id'         => $data['member_id'],
                'manufacturer_id'   => $data['manufacturer_id'],
                'manufacturer'      => $data['manufacturer'],
                'path'              => $data['path'],
                'type_id'           => $data['type_id'],
            	'type'              => $data['type_id'] == 1 ? $this->language->get('text_buy_now') : ($data['type_id'] == 0 ? $this->language->get('text_classified') : $this->language->get('text_shared')),
                'name'              => $data['name'],
                'model'             => $data['model'],
                'size'              => $data['size'],
                'year'              => $data['year'],
                'thumb'             => $thumb,
                'image'             => $image,
                'description'       => $data_description,
                'quantity'          => $data['quantity'],
                'quickview'         => $this->url->link('product/quickview', 'listing_id=' . $data['product_id'], 'SSL'),
            	'location'          => isset($data['location']) ? $data['location'] : '',
                'zone_id'           => isset($data['zone_id']) ? $data['zone_id'] : '',
            	'location_zone'     => !empty($location_zone) ? $location_zone['name'] : '',
                'country_id'        => isset($data['country_id']) ? $data['country_id'] : '',
            	'location_country'  => !empty($location_country) ? $location_country['iso_code_3'] : '',
                'featured'          => $data['featured'],
				'sort_order'        => $data['sort_order']
            );
            // 'manufacturer_image' => !empty($data['manufacturer_image']) && $data['manufacturer_id'] != 1 ? $this->model_tool_image->resize($data['manufacturer_image'], 100, 40, "fh") : false,
            // 'manufacturer_href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $data['manufacturer_id'], 'SSL'),
            // 'member'            => isset($data['member']) ? $data['member'] : '',
            // 'member_href'       => $this->url->link('product/member/info', 'member_id=' . $data['member_id'], 'SSL'),
            // 'member_contact'    => $this->url->link('information/contact', 'contact_id=' . $data['customer_id'], 'SSL'),
            // 'date_added'        => $data['date_added'],
            // 'date_modified'     => $data['date_modified'],
            // 'viewed'            => $data['viewed'],
            // 'attribute'         => isset($attribute_data) ? $attribute_data : array(),
            // 'weight'            => isset($data['weight']) && isset($data['weight_class_id']) ? $this->weight->format($data['weight'], $data['weight_class_id']) : '',
            // 'length'            => isset($data['length']) && isset($data['length_class_id']) ? $this->length->format($data['length'], $data['length_class_id']) : '',
            // 'width'             => isset($data['width']) && isset($data['length_class_id']) ? $this->length->format($data['width'], $data['length_class_id']) : '',
            // 'height'            => isset($data['height']) && isset($data['length_class_id']) ? $this->length->format($data['height'], $data['length_class_id']) : '',
            // 'location_href'     => isset($data['country_id']) && isset($data['zone_id']) ? $this->url->link('product/search', 'country=' . $data['country_id'] . '&state=' . $data['zone_id'], 'SSL') : ''

            $this->cache->set('product_' . (int)$data['product_id'] . '.min.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id, $product_data);
        }

        // add-in non-cached listing data
        $price = false;
        $special = false;
        $salebadges = false;
        $savebadges = false;
        $tax = false;

        if (!$this->config->get('config_customer_price') || $this->customer->isLogged()) {
            $price = $data['price'] != 0
                ? $this->currency->format($this->tax->calculate($data['price'], $data['tax_class_id'], $this->config->get('config_tax')))
                : $this->language->get('text_free');
        }

        if ((float)$data['special']) {
            $special = $this->currency->format($this->tax->calculate($data['special'], $data['tax_class_id'], $this->config->get('config_tax')));
            $salebadges = round((($data['price'] - $data['special']) / $data['price']) * 100, 0);
            $savebadges = $this->currency->format(($this->tax->calculate($data['price'], $data['tax_class_id'], $this->config->get('config_tax'))) - ($this->tax->calculate($data['special'], $data['tax_class_id'], $this->config->get('config_tax'))));
        }

        if ($this->config->get('config_tax')) {
            $tax = $this->currency->format((float)$data['special'] ? $data['special'] : $data['price']);
        }

        $product_data = array_merge($product_data, array(
            'price'             => $price,
            'price_value'       => $data['price'],
            'special'           => $special,
            'special_value'     => $data['special'],
            'salebadges'        => $salebadges,
            'savebadges'        => $savebadges,
            'tax'               => $tax,
            'compare'           => isset($this->session->data['compare']) && in_array($data['product_id'], $this->session->data['compare']) ? true : false
        ));

		return $product_data;
    }

	protected function info($data) {
		$this->setOutput($this->getProductData($data));
	}

	protected function list($products) {
        if (empty($products)) {
            return array();
        }

		$this->load->language('product/common');

		$this->data['text_loading'] = $this->language->get('text_loading');
		$this->data['text_save'] = $this->language->get('text_save');
		$this->data['text_tax'] = $this->language->get('text_tax');
		// $this->data['text_more'] = $this->language->get('text_more');
		// $this->data['text_empty'] = $this->language->get('text_empty');

		$this->data['button_quickview'] = $this->language->get('button_quickview');
		$this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['button_learn_more'] = $this->language->get('button_learn_more');
		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['button_contact'] = $this->language->get('button_contact');
		$this->data['button_compare'] = $this->language->get('button_compare');

		$product_data = array();

		foreach ($products as $product) {
			$product_data[$product['product_id']] = $this->getProductData($product);
		}

		$this->data['products'] = $product_data;

		$this->template = '/template/product/products.tpl';

		$this->render();
	}

	protected function list_module($data) {
        if (empty($data) || empty($data['products']) || empty($data['position'])) {
            return array();
        }

		$this->load->language('product/common');

		$this->data['text_save'] = $this->language->get('text_save');
		$this->data['text_tax'] = $this->language->get('text_tax');
		$this->data['text_view'] = $this->language->get('text_view');

		$this->data['button_quickview'] = $this->language->get('button_quickview');
		$this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['button_contact'] = $this->language->get('button_contact');
		$this->data['button_compare'] = $this->language->get('button_compare');

		$product_data = array();

		foreach ($data['products'] as $product) {
			$product_data[$product['product_id']] = $this->getProductData($product);
		}

		$this->data['products'] = $product_data;

		$this->data['position'] = $data['position'];

		$this->template = '/template/module/products.tpl';

		$this->render();
	}

	protected function embed($products) {
        if (empty($products)) {
            return array();
        }

		$this->load->language('product/common');

		// $this->data['text_loading'] = $this->language->get('text_loading');
		$this->data['text_save'] = $this->language->get('text_save');
		$this->data['text_tax'] = $this->language->get('text_tax');

		$this->data['button_quickview'] = $this->language->get('button_quickview');

		$product_data = array();

		foreach ($products as $product) {
			$product_data[$product['product_id']] = $this->getProductData($product);
			$product_data[$product['product_id']]['quickview'] = $this->url->link('embed/quickview', 'listing_id=' . $product['product_id'], 'SSL');
		}

		$this->data['products'] = $product_data;

		$this->template = '/template/embed/products.tpl';

		$this->render();
	}
}
?>
