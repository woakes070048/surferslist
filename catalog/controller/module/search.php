<?php
class ControllerModuleSearch extends Controller {
	protected function index($setting) {
        $this->data = $this->load->language('module/search');

		$request_path = isset($this->request->server['REQUEST_URI']) ? parse_url(strtolower(urldecode($this->request->server['REQUEST_URI'])), PHP_URL_PATH) : '';
		$this_page = !isset($this->request->get['route']) || $this->request->get['route'] == 'common/home' || ($request_path == '/')	? 'home' : friendly_url($request_path);
		$is_home = $this_page === 'home'	? true : false;

		$no_search_options = !isset($this->request->get['brand'])
			&& !isset($this->request->get['filter'])
			&& !isset($this->request->get['country'])
			&& !isset($this->request->get['state'])
			&& !isset($this->request->get['location'])
			&& !isset($this->request->get['type'])
			&& !isset($this->request->get['member'])
			? true : false;

		$no_search = $no_search_options
			&& !isset($this->request->get['s'])
			&& !isset($this->request->get['search'])
			&& !isset($this->request->get['tag'])
			&& !isset($this->request->get['category'])
			? true : false;

		$category_id = 0;
		$sub_category_id = 0;
		$third_category_id = 0;

		// filter groups
		$filter_group_id_price = 1;
		$filter_group_id_condition = 2;
		$filter_group_id_age = 3;

		$location_code = !empty($this->session->data['shipping_country_iso_code_3']) ? $this->session->data['shipping_country_iso_code_3'] : '';;
		$session_country_id = isset($this->session->data['shipping_country_id']) ? $this->session->data['shipping_country_id'] : '';
		$session_zone_id = isset($this->session->data['shipping_zone_id']) ? $this->session->data['shipping_zone_id'] : '';
		$session_location = isset($this->session->data['shipping_location']) ? $this->session->data['shipping_location'] : '';

		if ($is_home || $no_search) {
			// defaults for home and no search
			$search = '';
			$tag = '';
			$description = '';
			$forsale = false;
			$brand = '';
			$filter = '';
			$price = '';
			$condition = array(); // array('6', '7', '8', '9', '10');
			$age = '';
			$country = $session_country_id;
			$zone = $session_zone_id;
			$location = $session_location;
			$type = array(); //array('-1', '0', '1');
			$member = array(); //array('1', '3');
		} else {
			// $this->load->model('catalog/category');

			if (isset($this->request->get['s'])) {
				$search = $this->request->get['s'];
			} else if (isset($this->request->get['search'])) {
				$search = $this->request->get['search'];
			} else {
				$search = '';
			}

			$tag = isset($this->request->get['tag']) ? $this->request->get['tag'] : '';
			$description = isset($this->request->get['description']) ? $this->request->get['description'] : '';
			$brand = isset($this->request->get['brand']) ? $this->request->get['brand'] : '';

			// Category
			if (!empty($this->request->get['category']) && !is_array($this->request->get['category'])) {
				$category_ids = explode('_', (string)$this->request->get['category']);
				$category_id = isset($category_ids[0]) ? (int)$category_ids[0] : 0;
				$sub_category_id = isset($category_ids[1]) ? (int)$category_ids[1] : 0;
				$third_category_id = isset($category_ids[2]) ? (int)$category_ids[2] : 0;
			}

			// filters for price, condition, and age
			$price_filter_ids = array('1', '2', '3', '4', '5'); // $this->model_catalog_category->getFilterIdsByFilterGroupId($filter_group_id_price);
			$condition_filter_ids = array('6', '7', '8', '9', '10'); // $this->model_catalog_category->getFilterIdsByFilterGroupId($filter_group_id_condition);
			$age_filter_ids = array('11', '12', '13', '14'); // $this->model_catalog_category->getFilterIdsByFilterGroupId($filter_group_id_age);

			if (isset($this->request->get['filter'])) {
				$filter = $this->request->get['filter'];
				$filters = explode(',', $this->request->get['filter']);
				$price = current(array_intersect($filters, $price_filter_ids));
				$age = current(array_intersect($filters, $age_filter_ids));
				$condition = array_intersect($filters, $condition_filter_ids);
			} else {
				$filter = '';
				$price = '';
				$condition = array(); // array('6', '7', '8', '9', '10');
				$age = '';
			}

			$country = isset($this->request->get['country']) ? $this->request->get['country'] : $session_country_id;
			$zone = isset($this->request->get['state']) ? $this->request->get['state'] : $session_zone_id;
			$location = isset($this->request->get['location']) ? $this->request->get['location'] : $session_location;
			$type = isset($this->request->get['type']) && !is_array($this->request->get['type'])
				? explode(',', $this->request->get['type'])
				: (isset($this->request->get['forsale']) && $this->request->get['forsale']
					? array('0', '1') // classified and buy-now
					: array()); // array('-1', '0', '1');
			$forsale = isset($this->request->get['forsale'])
				? $this->request->get['forsale']
				: ($type == array('0', '1') ? true : false);
			$member = isset($this->request->get['member']) && !is_array($this->request->get['member']) ? explode(',', $this->request->get['member']) : array(); // array('1', '2', '3');
		}

		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'p.date_added';
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : (($sort == 'p.date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : $this->config->get('config_catalog_limit');

		// Categories & Manufacturers
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');

		$this->data['categories_complete'] = $this->model_catalog_category->getAllCategoriesComplete();
		$this->data['sub_categories'] = $category_id ? $this->model_catalog_category->getCategories($category_id) : array();
		$this->data['third_categories'] = $sub_category_id ? $this->model_catalog_category->getCategories($sub_category_id) : array();
		$this->data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();

		$this->data['categories'] = $this->cache->get('category.module.search.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($this->data['categories'] === false) {
			$this->data['categories'] = array();

			$categories_1 = $this->model_catalog_category->getCategories(0);

			foreach ($categories_1 as $category_1) {
				if ($category_1['top']) {
					if (strpos($category_1['name'], $this->language->get('heading_more')) !== false) {
						$category_1_name = ucwords($this->language->get('heading_more'));
					} else if (strpos($category_1['name'], $this->language->get('heading_other')) !== false) {
						$category_1_name = ucwords($this->language->get('heading_other'));
					} else {
						$category_1_name = ucwords($category_1['name']);
					}

					$this->data['categories'][] = array(
						'category_id' => $category_1['category_id'],
						'name'        => strtoupper($category_1_name),
						'url'		  => $this->url->link('product/category', 'path=' . $category_1['category_id'], 'SSL')
					);
				}
			}

			$this->cache->set('category.module.search.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data['categories'], 60 * 60 * 24 * 30); // 1 month cache expiration
		}

		// Location
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');

		$this->data['countries'] = $this->model_localisation_country->getCountries();
		$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId($country);
		$this->data['location_geo'] = $this->language->get('heading_geozone');

		// Member Groups
		$this->data['member_groups'] = array();

		$this->load->model('account/customer_group');

		$customer_groups = $this->model_account_customer_group->getCustomerGroups();

		foreach ($customer_groups as $customer_group) {
			$this->data['member_groups'][] = array(
				'group_id'	=> $customer_group['customer_group_id'],
				'name'		=> $customer_group['name']
			);
		}

		// Filters (Condition, Age, Price)
		$this->data['prices'] = $this->model_catalog_category->getFiltersByFilterGroupId($filter_group_id_price);
		$this->data['conditions'] = $this->model_catalog_category->getFiltersByFilterGroupId($filter_group_id_condition);
		$this->data['ages'] = $this->model_catalog_category->getFiltersByFilterGroupId($filter_group_id_age);

		// Types
		$this->data['listing_types'] = array();

		$this->data['listing_types'][] = array(
			'type_id' => '-1',
			'name'    => $this->language->get('text_shared'),
			'sort_order' => '3'
		);

		$this->data['listing_types'][] = array(
			'type_id' => '0',
			'name'    => $this->language->get('text_classified'),
			'sort_order' => '1'
		);

		$this->data['listing_types'][] = array(
			'type_id' => '1',
			'name'    => $this->language->get('text_buy_now'),
			'sort_order' => '2'
		);

		array_multisort(array(3, 1, 2), SORT_ASC, $this->data['listing_types']);

		$this->data['search'] = $search;
		$this->data['tag'] = $tag;
		$this->data['filter'] = $filter;
		$this->data['description'] = $description;
		$this->data['category_id'] = $category_id;
		$this->data['sub_category_id'] = $sub_category_id;
		$this->data['third_category_id'] = $third_category_id;
		$this->data['manufacturer_id'] = $brand;
		$this->data['age_selected'] = $age;
		$this->data['price_selected'] = $price;
		$this->data['country_id'] = $country;
		$this->data['zone_id'] = $zone;
		$this->data['location'] = ucwords($location);
		$this->data['zone_id'] = $zone;
		$this->data['member_selected'] = $member;
		$this->data['type_selected'] = $type;
		$this->data['forsale'] = $forsale;
		$this->data['condition'] = $condition;
		$this->data['is_home'] = $is_home;

		// Sort, Order, Limit
		$url = $this->getQueryParamsOnlyThese(array('sort', 'order', 'limit'));

		$this->data['action'] = $this->url->link('product/search', $url, 'SSL');
		$this->data['search_page'] = $this->url->link('product/search', '', 'SSL');
		$this->data['products_page'] = $this->url->link('product/allproducts', '', 'SSL');
		$this->data['location_page'] = $this->url->link('information/location', 'redirect_path=' . urlencode(ltrim($request_path, "/")), 'SSL');
		$this->data['location_remove'] = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode(ltrim($request_path, "/")), 'SSL');

		$this->data['display_more_options'] = !$no_search_options || !empty($this->request->get['more']) ? true : false;

		$this->data['session_country_id'] = $session_country_id;
		$this->data['session_zoned_id'] = $session_zone_id;
		$this->data['session_location'] = $session_location;

		$this->template = $this->config->get('config_template') . '/template/module/search.tpl';

		$this->render();
	}
}
?>
