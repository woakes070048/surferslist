<?php
class ControllerAjaxProduct extends Controller {

    public function add_savelist() {
		if (!isset($this->request->post['product_id'])) {
            return false;
		}

        $json = array();

		if (!isset($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}

        $product_id = (int)$this->request->post['product_id'];

		$this->load->language('account/wishlist');
		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if (!$product_info) {
            return false;
        }

		if (!in_array($this->request->post['product_id'], $this->session->data['wishlist'])) {
			$this->session->data['wishlist'][] = $this->request->post['product_id'];
		}

		if ($this->customer->isLogged()) {
			$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist', '', 'SSL'));
		} else {
			$json['success'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('account/wishlist', '', 'SSL'));
		}

		$json['total'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));

		$this->response->setOutput(json_encode($json));
	}

    public function add_compare() {
		if (!isset($this->request->post['product_id'])) {
            return false;
		}

        $json = array();

        $product_id = (int)$this->request->post['product_id'];

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if (!$product_info) {
            return false;
        }

        $this->data = array_merge(
            $this->load->language('product/common'),
            $this->load->language('product/compare')
        );

        $compare_max = 4;

        $json = array(
            'error'		=> false,
            'success'	=> false,
            'total'		=> false,
            'redirect'	=> false,
            'compare'	=> $this->language->get('button_compare')
        );

        if (!isset($this->session->data['compare'])) {
            $this->session->data['compare'] = array();
        }

        $compare_total = count($this->session->data['compare']);

        if ($compare_total >= $compare_max) {
            $json['error'] = sprintf($this->language->get('text_full'), $this->url->link('product/compare'), $compare_total);
            $json['redirect'] = $this->url->link('product/compare');
        }

        if (!$json['error']) {
            if (in_array($product_id, $this->session->data['compare'])) {
                $json['error'] = sprintf($this->language->get('text_exists'), $this->url->link('product/compare'));
            } else {
                $this->session->data['compare'][] = $product_id;

                $json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $product_id), $product_info['name'], $this->url->link('product/compare'));
                $json['total'] = sprintf($this->language->get('text_compare'), count($this->session->data['compare']));
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function remove_compare() {
		if (!isset($this->request->post['product_id'])) {
            return false;
		}

        $json = array();

        $product_id = (int)$this->request->post['product_id'];

        $this->data = array_merge(
            $this->load->language('product/common'),
            $this->load->language('product/compare')
        );

        $json = array(
            'error'		=> false,
            'success'	=> false,
            'total'		=> false,
            'redirect'	=> false,
            'compare'	=> $this->language->get('button_compare')
        );

        if (empty($this->session->data['compare'])) {
            $json['error'] = sprintf($this->language->get('text_empty'), $this->url->link('product/compare'), $compare_total);
        }

        if (!$json['error']) {
            if (!in_array($product_id, $this->session->data['compare'])) {
                $json['error'] = sprintf($this->language->get('text_does_not_exist'), $this->url->link('product/compare'));
            } else {
                $this->session->data['compare'] = array_diff($this->session->data['compare'], array($product_id));

                $json['success'] = sprintf($this->language->get('text_remove'), $this->url->link('product/compare'));
                $json['total'] = sprintf($this->language->get('text_compare'), count($this->session->data['compare']));
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function shipping_rates() {
		if (empty($this->request->get['shipping_rate_id'])) {
			return false;
		}

        $output = '';

        $shipping_rate_id = (int)$this->request->get['shipping_rate_id'];

		$this->load->language('product/product');
		$this->load->model('catalog/product');
		$this->load->model('localisation/zone');

		$geo_zones = $this->model_localisation_zone->getGeoZones();

		$geo_zones_info = array();
		$geo_zone_shipping_rates = array();

		if ($geo_zones) {
			$shipping_rates = $this->model_catalog_product->getProductShipping($shipping_rate_id);

			foreach ($geo_zones as $geo_zone) {
				if (isset($shipping_rates[$geo_zone['geo_zone_id']])) {
					$geo_zone_shipping_rates = $shipping_rates[$geo_zone['geo_zone_id']];
				} else {
					continue;
				}

				$geo_zone_rate = current($geo_zone_shipping_rates); // show first rate only
				$geo_zone_rate = $geo_zone_rate == 0 ? $this->language->get('text_shipping_free') : $this->currency->format($geo_zone_rate);

				if ($geo_zone_rate) {
					$geo_zones_info[] = array (
						'geo_zone_id'           => $geo_zone['geo_zone_id'],
						'geo_zone_name'         => $geo_zone['name'],
						'geo_zone_description'  => $geo_zone['description'],
						'geo_zone_rate'         => $geo_zone_rate,
						'geo_zone_zones'        => $this->model_localisation_zone->getZonesByGeoZoneId($geo_zone['geo_zone_id'])
					);
				}
			}
		}

		$output = '<div class="bgWhite" style="max-width:760px;margin:0 auto;">' . "\n";
		$output .= '<h1>' . $this->language->get('heading_shipping_rates') . '</h1>' . "\n";

		if ($geo_zones_info) {
			/*
			$sort_order = array();

			foreach ($zones as $key => $value) {
				$sort_order[$key] = $value['zone'];
			}

			array_multisort($sort_order, SORT_ASC, $zones);
			* */

				$output .= '  <table class="list bordered">' . "\n";
				$output .= '   <thead>' . "\n";
				$output .= '      <tr>' . "\n";
				$output .= '        <th class="left" width="80">' . $this->language->get('column_fee') . '</td>' . "\n";
				$output .= '        <th class="left" width="120">' . $this->language->get('column_geo_zone') . '</td>' . "\n";
				$output .= '        <th class="left">' . $this->language->get('column_zones') . '</td>' . "\n";
				$output .= '      <tr>' . "\n";
				$output .= '   </tbody>' . "\n";
				$output .= '   <tbody>' . "\n";

				foreach ($geo_zones_info as $geo_zone_info) {
					$output .= '      <tr>' . "\n";
					$output .= '        <td class="left">' . $geo_zone_info['geo_zone_rate'] . '</td>' . "\n";
					$output .= '        <td class="left">' . $geo_zone_info['geo_zone_name'] . '</td>' . "\n";
					$output .= '        <td class="left">';

					foreach ($geo_zone_info['geo_zone_zones'] as $geo_zone_zone_info) {
						$output .= strip_tags(html_entity_decode($geo_zone_zone_info['name'], ENT_QUOTES, 'UTF-8')) . ' (' . $geo_zone_zone_info['code'] . '), ';
					}

					$output .= '        </td>' . "\n";
					$output .= '      <tr>' . "\n";
				}

				$output .= '   </tbody>' . "\n";
				$output .= ' </table>' . "\n";
		} else {
			// check if all other zones rate exists
			if (!$geo_zones_info && !empty($shipping_rates['0'])) {
				$shipping_rate = current($shipping_rates['0']); // show first rate only
				$shipping_rate = $shipping_rate == 0 ? $this->language->get('text_shipping_free') : $this->currency->format($shipping_rate);

				$output  .= '<div class="information"><p>' . sprintf($this->language->get('text_shipping_rate'), $shipping_rate) . '</p><span class="icon"><i class="fa fa-info-circle"></i></span></div>';
			} else {
				$output  .= '<div class="warning"><p>' . $this->language->get('text_shipping_none') . '</p><span class="icon"><i class="fa fa-warning"></i></span></div>';
			}
		}

		$output .= '</div>' . "\n";

		$this->response->setOutput($output);
	}

	public function flag_listing() {
        if (!isset($this->request->post['product_id'])) {
            return false;
        }

        $json = array();

        $product_id = (int)$this->request->post['product_id'];

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if (!$product_info) {
            return false;
        }

		$this->load->language('product/product');

        if (!$this->customer->validateLogin()) {
            $json['error'] = $this->language->get('error_flag_logged');
            unset($this->session->data['warning']);
        } /* else if (!$this->customer->validateProfile()) {
            $json['error'] = $this->language->get('error_flag_membership');
            unset($this->session->data['warning']);
        } */ // allow non-activated members to flag listings

		if (!isset($json['error']) && $this->request->server['REQUEST_METHOD'] == 'POST') {
            $member_info = $this->model_catalog_product->getMemberByProductId($product_id);

            if (!empty($member_info['member_id']) && empty($member_info['customer_id'])) {
                $json['error'] = $this->language->get('error_flag_invalid');
                unset($this->session->data['warning']);
            } else {
                $this->model_catalog_product->flagProduct($product_id);
                $json['success'] = $this->language->get('text_flag_success');
            }
		}

		$this->response->setOutput(json_encode($json));
	}

    public function more() {
		if (!$this->config->get('apac_status')
			|| !$this->config->get('apac_products_status')
			|| (!$this->request->checkReferer($this->config->get('config_url')) && !$this->request->checkReferer($this->config->get('config_ssl')))) {
	  		$this->redirect($this->url->link('product/allproducts'));
		}

		$this->load->language('product/common');

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else if (isset($this->request->get['s'])) {
			$search = $this->request->get['s'];
		} else {
			$search = '';
		}

		if ($search && isset($this->request->get['expand_search'])) {
			$expand_search = ($this->request->get['expand_search'] === 'true');
		} else {
			$expand_search = false;
		}

		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} else {
			$tag = '';
		}

		if (isset($this->request->get['description'])) {
			$description = $this->request->get['description'];
		} else {
			$description = '';
		}

		if (isset($this->request->get['type']) && !is_array($this->request->get['type'])) {
			$filter_listing_type = explode(',', $this->request->get['type']);
		} else if (isset($this->request->get['forsale']) && $this->request->get['forsale']) {
			$filter_listing_type = array('0', '1'); // classified and buy-now
		} else {
			$filter_listing_type = array(); // array('-1', '0', '1');
		}

		if (isset($this->request->get['member']) && !is_array($this->request->get['member'])) {
			$filter_member_group = explode(',', $this->request->get['member']);
		} else {
			$filter_member_group = array(); // array('1', '2', '3');
		}

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['filter_listings'])) {
			$filter_listings = $this->request->get['filter_listings'];
		} else {
			$filter_listings = '';
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else if (isset($this->request->get['category_id'])) {
			$filter_category_id = $this->request->get['category_id'];
		} else if (isset($this->request->get['category'])) {
			$filter_category_id = $this->request->get['category'];
		} else if (isset($this->request->get['path']) && !is_array($this->request->get['path'])) {
			$parts = explode('_', (string)$this->request->get['path']);
			$filter_category_id = (int)array_pop($parts);
		} else {
			$filter_category_id = '';
		}

		if (isset($this->request->get['filter_manufacturer_id'])) {
			$filter_manufacturer_id = $this->request->get['filter_manufacturer_id'];
		} else if (isset($this->request->get['manufacturer_id'])) {
			$filter_manufacturer_id = $this->request->get['manufacturer_id'];
		} else if (isset($this->request->get['brand'])) {
			$filter_manufacturer_id = $this->request->get['brand'];
		} else {
			$filter_manufacturer_id = '';
		}

		if (isset($this->request->get['member_id'])) {
			$filter_member_id = (int)$this->request->get['member_id'];
		} else {
			$filter_member_id = null;
		}

		if (isset($this->request->get['filter_location'])) {
			$filter_location = $this->request->get['filter_location'];
		} else if (isset($this->request->get['location'])) {
			$filter_location = $this->request->get['location'];
		} else if (isset($this->session->data['shipping_location'])) {
			$filter_location = $this->session->data['shipping_location'];
		} else {
			$filter_location = '';
		}

		if (isset($this->request->get['filter_country_id'])) {
			$filter_country_id = $this->request->get['filter_country_id'];
		} else if (isset($this->request->get['country'])) {
			$filter_country_id = $this->request->get['country'];
		} else if (isset($this->session->data['shipping_country_id'])) {
			$filter_country_id = $this->session->data['shipping_country_id'];
		} else {
			$filter_country_id = ''; // $this->config->get('config_country_id');
		}

		if (isset($this->request->get['filter_zone_id'])) {
			$filter_zone_id = $this->request->get['filter_zone_id'];
		} else if (isset($this->request->get['state'])) {
			$filter_zone_id = $this->request->get['state'];
		} else if (isset($this->session->data['shipping_zone_id'])) {
			$filter_zone_id = $this->session->data['shipping_zone_id'];
		} else {
			$filter_zone_id = '';
		}

		$module = isset($this->request->get['module']) && $this->request->get['module'] == 'true' ? true : false;
		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : ($module ? 'random' : $this->config->get('apac_products_sort_default')); // 'p.date_added'
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : (($sort == 'p.date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = (isset($this->request->get['limit']) && $this->request->get['limit'] <= $this->config->get('config_catalog_limit') * 4) ? (int)$this->request->get['limit'] : $this->config->get('config_catalog_limit');

		$this->data['products'] = array();

		$filter_data = array(
			'filter_name'         		=> $search,
			'filter_tag'          		=> $tag,
			'filter_description'  		=> $description,
			'filter_filter'      		=> $filter,
			'filter_listings'			=> $filter_listings,
			'filter_category_id'    	=> $filter_category_id,
			'filter_manufacturer_id'	=> $filter_manufacturer_id,
			'filter_country_id'    	    => $filter_country_id,
			'filter_zone_id'      		=> $filter_zone_id,
			'filter_location'      		=> $filter_location,
			'filter_listing_type' 		=> $filter_listing_type,
			'filter_member_group' 		=> $filter_member_group,
			'sort'                		=> $sort,
			'order'               		=> $order,
			'start'               		=> ($page - 1) * $limit,
			'limit'               		=> $limit,
			'expand_search'   			=> $expand_search
		);

		// do not set Profile filter if member_id is not set, so that anon listings are not included
		if (!is_null($filter_member_id)) {
			$filter_data['filter_member_account_id'] = $filter_member_id;

			// remove location filters to ensure entire quiver is included on Profile pages
			unset($filter_data['filter_country_id']);
			unset($filter_data['filter_zone_id']);
			unset($filter_data['filter_location']);
		}

		if (isset($this->request->get['special']) && $this->request->get['special'] == 'true') {
			$product_total = $this->model_catalog_product->getTotalProductSpecials($filter_data);
			$results = $product_total ? $this->model_catalog_product->getProductSpecials($filter_data) : array();
		} else if (isset($this->request->get['featured']) && $this->request->get['featured'] == 'true') {
			$product_total = $this->model_catalog_product->getTotalProductFeatured($filter_data);
			$results = $product_total ? $this->model_catalog_product->getProductFeatured($filter_data) : array();
		} else {
			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);
			$results = $product_total ? $this->model_catalog_product->getProducts($filter_data) : array();
		}

		$max_pages = $limit > 0 ? ceil($product_total / $limit) : 1;

		foreach ($results as $result) {
            $this->data['products'][] = $this->getChild('product/data/info', $result);

			// add to filter_ids
			if ($module && $filter_listings) {
				$filter_listings .= ',' . $result['product_id'];
                $this->setQueryParam('filter_listings', $filter_listings);
			}
		}

        $this->setQueryParams(array(
			's',
			'search',
			'tag',
			'filter_location',
			'location',
			'description',
			'filter',
			'filter_listings',
			'filter_category_id',
			'category_id',
			'category',
			'path',
			'filter_manufacturer_id',
			'manufacturer_id',
			'brand',
			'member_id',
			'filter_country_id',
			'country',
			'filter_zone_id',
			'state',
			'forsale',
			'type',
			'member',
			'special',
			'featured',
			'module',
			'sort',
			'order',
			'limit',
			'expand_search'
		));

		$more_href = $page < $max_pages
			? $this->url->link('ajax/product/more', $this->getQueryString() . '&page=' . ($page + 1))
			: $this->url->link('product/allproducts');

		$json = array(
			'listings' 		=> $this->data['products'],
			'text_more'		=> $page < $max_pages ? $this->language->get('text_more') : $this->language->get('text_view_all'),
			'text_none'		=> $this->language->get('text_no_more'),
			'more_href'		=> isset($more_href) ? urlencode(html_entity_decode($more_href, ENT_QUOTES, 'UTF-8')) : '',
			'module'		=> $module
		);

		// $this->log->write(json_encode($json));

		$this->response->setOutput(json_encode($json));
	}

}
