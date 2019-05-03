<?php
class ControllerProductData extends Controller {
	private $cache_expires = 60 * 60 * 24; // 1 day

	protected function index() {
        return false;
    }

    protected function info($data) {
		$this->getData($data, true);

		$this->setOutput($data);
	}

	protected function complete($data) {
		$this->getData($data, false);

		$this->setOutput($data);
	}

	protected function list($data) {
        if (empty($data)) {
            return array();
        }

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$referer = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : false;

		$this->load->language('product/common');

		$this->data['heading_params'] = $this->language->get('heading_param_search');

		$this->data['text_loading'] = $this->language->get('text_loading');
		$this->data['text_save'] = $this->language->get('text_save');
		$this->data['text_tax'] = $this->language->get('text_tax');
		$this->data['text_more'] = $this->language->get('text_more');
		$this->data['text_empty'] = $this->language->get('text_empty');

		$this->data['button_quickview'] = $this->language->get('button_quickview');
		$this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['button_learn_more'] = $this->language->get('button_learn_more');
		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['button_contact'] = $this->language->get('button_contact');
		$this->data['button_compare'] = $this->language->get('button_compare');
		$this->data['button_back'] = $this->language->get('button_back');
		$this->data['button_reset'] = $this->language->get('button_reset');
		$this->data['button_search'] = $this->language->get('button_search');
		$this->data['button_home'] = $this->language->get('button_continue');

		$product_data = array();

		foreach ($data['products'] as $product) {
			$this->getMinData($product, $customer_group_id);
			$this->getNonCachedData($product);
			$product_data[$product['product_id']] = $product;
		}

		$this->data['products'] = $product_data;

		$this->data['more'] = isset($data['more']) ? $data['more'] : false;
		$this->data['pagination'] = isset($data['pagination']) ? $data['pagination'] : false;
		$this->data['reset'] = isset($data['reset']) ? $data['reset'] : false;
		$this->data['params'] = isset($data['params']) ? $data['params'] : array();
		$this->data['back'] = $referer && $referer != $this->data['reset'] ? $referer : false;
		$this->data['search'] = $this->url->link('product/search');
		$this->data['home'] = $this->url->link('common/home');

		// Remove Location
		if (!$product_data && (isset($this->session->data['shipping_country_id']) || isset($this->session->data['shipping_zone_id']) || isset($this->session->data['shipping_location']))) {
			if (isset($data['query_params'])) {
				$this->setQueryParams($data['query_params']);
			}

			$url = $this->getQueryString(array('filter_location', 'filter_country_id', 'filter_zone_id', 'location', 'country', 'state'));
			$request_path = isset($this->request->server['REQUEST_URI']) ? parse_url(strtolower(urldecode($this->request->server['REQUEST_URI'])), PHP_URL_PATH) : '';
			$location_remove_url = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode(ltrim($request_path . '?' . ltrim($url, "&"), "/")));

			$this->data['text_empty'] .= '&nbsp; &nbsp;' . sprintf($this->language->get('text_location_remove_url'), $location_remove_url);
		}

		if (!isset($data['pagination']) && !isset($data['reset'])) {
			$this->data['text_empty'] = '';
		}

		$this->template = 'template/product/products.tpl';

		$this->render();
	}

	protected function list_module($data) {
        if (empty($data) || empty($data['products']) || empty($data['position'])) {
            return array();
        }

		$customer_group_id = $this->customer->isLogged()
			? $this->customer->getCustomerGroupId()
			: $this->config->get('config_customer_group_id');

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
			$this->getMinData($product, $customer_group_id);
			$this->getNonCachedData($product);

			if (!isset($product['thumb_alt'])) {
				$product['thumb_alt'] = $product['thumb'];
			}

			$product_data[$product['product_id']] = $product;
		}

		$this->data['products'] = $product_data;

		$this->data['position'] = $data['position'];

		$this->template = 'template/module/products.tpl';

		$this->render();
	}

	protected function embed($data) {
        if (empty($data)) {
            return array();
        }

		$customer_group_id = $this->customer->isLogged()
			? $this->customer->getCustomerGroupId()
			: $this->config->get('config_customer_group_id');

		$this->load->language('product/common');

		// $this->data['text_loading'] = $this->language->get('text_loading');
		$this->data['text_save'] = $this->language->get('text_save');
		$this->data['text_tax'] = $this->language->get('text_tax');

		$this->data['button_quickview'] = $this->language->get('button_quickview');

		$product_data = array();

		foreach ($data['products'] as $product) {
			$this->getMinData($product, $customer_group_id);
			$this->getNonCachedData($product);
			$product['quickview'] = $this->url->link('embed/quickview', 'listing_id=' . $product['product_id'], 'SSL');
			$product_data[$product['product_id']] = $product;
		}

		$this->data['products'] = $product_data;

		$this->template = 'template/embed/products.tpl';

		$this->render();
	}

    protected function getData(&$data, $min = true) {
		if (!$data) {
			return array();
		}

		$this->load->language('product/common');

		$this->load->model('tool/image');

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		if ($min) {
			$this->getMinData($data, $customer_group_id);
		} else {
			$this->getMoreData($data, $customer_group_id);
		}

		$this->getNonCachedData($data);
	}

    protected function getMinData(&$data, $customer_group_id, $cache = true) {
        $product_data = !$cache ? false : $this->cache->get('product_' . (int)$data['product_id'] . '.data.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id);

        if ($product_data === false) {
			$short_description_max_length = 80;
            $short_description = remove_links(preg_replace('/\s+/', ' ', strip_tags_decode($data['description_short'])));

            if (!$short_description) {
                if ($data['year'] != '0000') {
                    $short_description .= $data['year'] . ' ';
                }

                if ($data['manufacturer_id'] > 1) {
                    $short_description .= $data['manufacturer'] . ' ';
                }

                $short_description .= $data['model'];

                if (utf8_strpos(trim($data['size']), ' ') === false && utf8_strlen($data['size']) < 10) {
                    $short_description .= ' ' . $data['size'];
                }
            } else if (utf8_strlen($short_description) > $short_description_max_length) {
                $short_description = utf8_substr($short_description, 0, $short_description_max_length) . $this->language->get('text_ellipses');
            }

            $product_data = array(
				'product_id'		=> $data['product_id'],
    			'manufacturer_id'	=> $data['manufacturer_id'],
    			'member_id'         => $data['member_id'],
    			'customer_id'       => $data['customer_id'],
    			'location'          => $data['location'],
    			'zone_id'           => $data['zone_id'],
    			'country_id'        => $data['country_id'],
                'path'		        => $data['path'],
				'href'              => $this->url->link('product/product', 'product_id=' . $data['product_id']),
            	'type'              => $data['type_id'] == 1 ? $this->language->get('text_buy_now') : ($data['type_id'] == 0 ? $this->language->get('text_classified') : $this->language->get('text_shared')),
				'year' 				=> $data['year'] != '0000' ? $data['year'] : '', // $this->language->get('text_unknown')
                'thumb'             => $this->model_tool_image->resize($data['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'), 'fw'),
                'description_short' => $short_description,
                'quickview'         => $this->url->link('product/quickview', 'listing_id=' . $data['product_id'])
            );

			if ($cache) {
				$this->cache->set('product_' . (int)$data['product_id'] . '.data.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id, $product_data, $this->cache_expires);
			}
        }

		$data = array_merge($data, $product_data);

		return $product_data;
    }

	protected function getMoreData(&$data, $customer_group_id, $cache = true) {
		$product_data = !$cache ? false : $this->cache->get('product_' . (int)$data['product_id'] . '.data.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id);

		if ($product_data === false) {
			$product_data = $this->getMinData($data, $customer_group_id, false);
		}

		if (!isset($product_data['page_title'])) {
			// heading/title
			$page_title_keywords = array_map('trim', explode(' ', $data['name']));
			$page_title_parts = array('year', 'manufacturer', 'model', 'size');
			$page_title_exclude = array('Other', '0000');
			$page_title_category = !empty($category['name']) ? $category['name'] : '';

			foreach ($page_title_parts as $value) {
				if ($data[$value] && utf8_strlen($data[$value]) <= 20) {
					if ($value == 'manufacturer' && $data['manufacturer_id'] == 1) {
						continue;
					}

					// skip if any partial keyword already exists
					foreach ($page_title_keywords as $page_title_keyword) {
						if ($page_title_keyword && strpos($data[$value], $page_title_keyword) !== false) {
							continue 2;
						}
					}

					$product_keywords = explode(' ', $data[$value]);

					foreach ($product_keywords as $product_keyword) {
						if (strpos($page_title_category, $product_keyword) === false
							&& !in_array($product_keyword, $page_title_keywords)
							&& !in_array($product_keyword, $page_title_exclude)) {
							$page_title_keywords[] = $product_keyword;
						}
					}
				}
			}

			$page_title = implode(' ', $page_title_keywords);

			if ($page_title_category) {
				$page_title .= ' | ' . $page_title_category;
			}

			$meta_keyword = $data['meta_keyword']
				? implode(', ', array_unique(array_merge(array_map('trim', explode(',', $data['meta_keyword'])), $page_title_keywords)))
				: implode(', ', $page_title_keywords);

			$meta_description = sprintf($this->language->get('meta_description_prefix_anon'), $data['meta_description']);

			// description and learn more link
			$learn_more = '';
			$description = nl2br(strip_tags_decode($data['description']), false);

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

			// categories
			$categories = array();

			foreach ($data['categories'] as $category) {
				$categories[] = array(
					'category_id' => $category['category_id'],
					'name'        => $category['name'],
					'path'		  => $category['path'],
					'href'        => $this->url->link('product/category', 'path=' . $category['path'])
				);
			}

			// member
			$member = array();

			if ($this->config->get('member_status') && $data['member_info']) {
				$member = array(
					// 'member_customer_id' => $data['member_info']['customer_id'],
					'member_id'	  => $data['member_info']['member_account_id'],
					'name'        => $data['member_info']['member_account_name'],
					'image'       => $data['member_info']['member_account_image'] ? $this->model_tool_image->resize($data['member_info']['member_account_image'], 40, 40, 'autocrop') : false,
					'href'        => $this->url->link('product/member/info', 'member_id=' . $data['member_info']['member_account_id']),
					'contact'	  => $this->url->link('information/contact', 'contact_id=' . $data['member_info']['customer_id']),
					'group_id'    => $data['member_info']['member_group_id'],
					'group'       => $data['member_info']['member_group'],
					'rating'      => (int)$data['member_info']['rating'],
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$data['member_info']['reviews'])
				);

				$meta_description = sprintf($this->language->get('meta_description_prefix_member'), $data['meta_description'], $data['member_info']['member_account_name']);  // . $product_data['date_added']
			}

			// keyword tags
			$tags = array();

			if ($data['tag']) {
				$product_tags = explode(',', $data['tag']);

				foreach ($product_tags as $tag) {
					$tags[] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			// append more data
			$product_data = array_merge($product_data, array(
				'page_title'		=> $page_title,
				'image'				=>  $this->model_tool_image->resize($data['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'fw'),
				'manufacturer_image' => $this->model_tool_image->resize($data['manufacturer_image'], 100, 40, 'fh'),
				'manufacturer_href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $data['manufacturer_id']),
				'member'            => $member,
				'member_customer_id' => isset($data['customer_id']) ? (int)$data['customer_id'] : 0,
				'categories'		=> $categories,
				'description'		=> convert_links(strip_tags_decode($description)),
				'meta_keyword'		=> $meta_keyword,
				'meta_description'	=> $meta_description,
				'tags'				=> $tags,
				'learn_more'		=> $learn_more,
            	'location_zone'     => $data['zone'],
            	'location_country'  => $data['country'],
				'location_href'     => $this->url->link('product/search', 'country=' . $data['country_id'] . '&state=' . $data['zone_id']),
				'weight'            => $this->weight->format($data['weight'], $data['weight_class_id']),
				'length'            => $this->length->format($data['length'], $data['length_class_id']),
				'width'             => $this->length->format($data['width'], $data['length_class_id']),
				'height'            => $this->length->format($data['height'], $data['length_class_id']),
				'date_added'        => date($this->language->get('date_format_short'), strtotime($data['date_added'])) . ' ' . sprintf($this->language->get('text_days_ago'), days_since_date($data['date_added'])),
				'date_modified'     => date($this->language->get('date_format_short'), strtotime($data['date_modified'])) . ' ' . sprintf($this->language->get('text_days_ago'), days_since_date($data['date_modified']))
			));

			// update cache with appended data
			if ($cache) {
				$this->cache->set('product_' . (int)$data['product_id'] . '.data.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id, $product_data, $this->cache_expires);
			}
		}

		$data = array_merge($data, $product_data);

		return $product_data;
	}

	protected function getNonCachedData(&$data) {
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

		$discounts = array();

		if (isset($data['discounts'])) {
			foreach ($data['discounts'] as $discount) {
				$discounts[] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $data['tax_class_id'], $this->config->get('config_tax')))
				);
			}
		}

		$product_data = array(
			'price'             => $price,
			'price_value'       => $data['price'],
			'special'           => $special,
			'special_value'     => $data['special'],
			'salebadges'        => $salebadges,
			'savebadges'        => $savebadges,
			'tax'               => $tax,
			'discounts'			=> $discounts,
			'compare'           => isset($this->session->data['compare']) && in_array($data['product_id'], $this->session->data['compare']) ? true : false
		);

		$data = array_merge($data, $product_data);

		return $product_data;
	}

}
