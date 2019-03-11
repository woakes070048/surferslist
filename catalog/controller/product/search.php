<?php
class ControllerProductSearch extends Controller {
	public function index() {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/search')
		);

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

		$tag = isset($this->request->get['tag']) ? $this->request->get['tag'] : '';
		$description = isset($this->request->get['description']) ? $this->request->get['description'] : '';
		$brand = isset($this->request->get['brand']) ? $this->request->get['brand'] : '';

		// Category
		if (!empty($this->request->get['category']) && !is_array($this->request->get['category'])) {
			$category_ids = explode('_', (string)$this->request->get['category']);
			$category = (int)array_pop($category_ids);
			$category_ids[] = $category_ids;
			$category_id = isset($category_ids[0]) ? (int)$category_ids[0] : 0;
			$sub_category_id = isset($category_ids[1]) ? (int)$category_ids[1] : 0;
			$third_category_id = isset($category_ids[2]) ? (int)$category_ids[2] : 0;
		} else {
			$category = '';
			$category_id = 0;
			$sub_category_id = 0;
			$third_category_id = 0;
		}

		// filters for price, condition, and age
		if (isset($this->request->get['filter']) && !is_array($this->request->get['filter'])) {
			$this->load->model('catalog/filter');

			$price_filter_ids = $this->model_catalog_filter->getFilterIdsByFilterGroupId($this->config->get('config_filter_group_price_id'));  // array('1', '2', '3', '4', '5');
			$condition_filter_ids = $this->model_catalog_filter->getFilterIdsByFilterGroupId($this->config->get('config_filter_group_condition_id'));  // array('6', '7', '8', '9', '10');
			$age_filter_ids = $this->model_catalog_filter->getFilterIdsByFilterGroupId($this->config->get('config_filter_group_age_id'));  // array('11', '12', '13', '14');

			$filter = $this->request->get['filter'];
			$filters = explode(',', $this->request->get['filter']);
			$price = current(array_intersect($filters, $price_filter_ids));
			$age = current(array_intersect($filters, $age_filter_ids));
			$condition = array_intersect($filters, $condition_filter_ids);
		} else {
			$filter = '';
			$price = '';
			$condition = array();
			$age = '';
		}

		$country = isset($this->request->get['country']) ? $this->request->get['country'] : '';
		$zone = isset($this->request->get['state']) ? $this->request->get['state'] : '';
		$location = isset($this->request->get['location']) ? $this->request->get['location'] : '';

		$type = isset($this->request->get['type']) && !is_array($this->request->get['type'])
			? explode(',', $this->request->get['type'])
			: (isset($this->request->get['forsale']) && $this->request->get['forsale']
				? array('0', '1') // classified and buy-now
				: array()); // array('-1', '0', '1');

		$forsale = isset($this->request->get['forsale'])
			? $this->request->get['forsale']
			: ($type == array('0', '1') ? true : false);

		$member = isset($this->request->get['member']) && !is_array($this->request->get['member']) ? explode(',', $this->request->get['member']) : array(); // array('1', '2', '3');
		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : $this->config->get('apac_products_sort_default'); // 'p.date_added'; // 'random'
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : (($sort == 'p.date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = (isset($this->request->get['limit']) && $this->request->get['limit'] <= $this->config->get('config_catalog_limit') * 4) ? (int)$this->request->get['limit'] : $this->config->get('config_catalog_limit');

		$query_params = array(
			's',
			'search',
			'tag',
			'location',
			'description',
			'category',
			'brand',
			'filter',
			'country',
			'state',
			'type',
			'member',
			'sort',
			'order',
			'limit'
		);

		$this->setQueryParams($query_params);

		$no_search = true;

		$search_params = array(
			'search'			=> $this->language->get('text_param_keyword'),
			'tag'				=> $this->language->get('text_param_tag'),
			'description'		=> $this->language->get('text_param_description'),
			'category'			=> $this->language->get('text_param_category'),
			'brand'				=> $this->language->get('text_param_manufacturer'),
			'price'				=> $this->language->get('text_param_price'),
			'age'				=> $this->language->get('text_param_age'),
			'condition'			=> $this->language->get('text_param_condition'),
			'country'			=> $this->language->get('text_param_country'),
			'zone'				=> $this->language->get('text_param_zone'),
			'location'			=> $this->language->get('text_param_location'),
			'type'				=> $this->language->get('text_param_type'),
			'member'			=> $this->language->get('text_param_member')
		);

		foreach ($search_params as $key => $value) {
			if (!empty(${$key})) {
				$this->data['params'][] = array(
					'name'	=> $value,
					'field' => $key,
					'value'	=> is_array(${$key}) ? implode(',', ${$key}) : ${$key}
				);

				$no_search = false;
			}
		}

		$heading_title = !empty($this->request->get['search']) ? $this->language->get('heading_title') .  ': ' . $this->request->get['search'] : $this->language->get('heading_title');
		$meta_description = $this->language->get('meta_description');
		$meta_keyword = $this->language->get('meta_keyword');

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));

		if ($this->config->get('apac_status') && $this->config->get('apac_categories_status') && $this->config->get('apac_categories_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_categories'), $this->url->link('product/allcategories'));
		}

		if ($this->config->get('apac_status') && $this->config->get('apac_products_status') && $this->config->get('apac_products_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_products'), $this->url->link('product/allproducts'));
		}

		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('product/search'));

		if (isset($this->session->data['shipping_country_id']) && $country == $this->session->data['shipping_country_id']
			&& isset($this->session->data['shipping_zone_id']) && $zone == $this->session->data['shipping_zone_id']
			&& isset($this->session->data['shipping_location']) && $location == $this->session->data['shipping_location']) {

			$location_name = $this->getLocationName('long');

			if ($location_name) {
				$heading_title .= ' - ' . $location_name;

				$this->addBreadcrumb($location_name, $this->url->link('information/location'));
			}
		}

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['heading_title'] = $heading_title;

		$this->data['heading_params'] = $this->language->get('heading_param_search');

		$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

		$this->data['products'] = array();
		$results = array();
		$product_total = 0;
		$max_pages = 0;

		if (!$no_search) {
			$this->load->model('catalog/product');

			$data = array(
				'filter_name'         		=> $search,
				'filter_tag'          		=> $tag,
				'filter_description'  		=> $description,
				'filter_filter'       		=> $filter,
				'filter_category_id'  		=> $category,
				'filter_sub_category' 		=> true,
				'filter_manufacturer_id'	=> $brand,
				'filter_country_id'    		=> $country,
				'filter_zone_id'       		=> $zone,
				'filter_location'      		=> $location,
				'filter_listing_type'  		=> $type,
				'filter_member_group'  		=> $member,
				'sort'                 		=> $sort,
				'order'                		=> $order,
				'start'                		=> ($page - 1) * $limit,
				'limit'                		=> $limit,
				'is_search' 		 		=> true,
				'expand_search' 		 	=> $expand_search
			);

			$product_total = $this->model_catalog_product->getTotalProducts($data);

			if (!$product_total && !$expand_search) {
				$data['expand_search'] = $expand_search = true;
				// try again without checking special keywords (e.g. categories, brands, profiles)
				$product_total = $this->model_catalog_product->getTotalProducts($data);
			}

			$max_pages = $limit > 0 ? ceil($product_total / $limit) : 1;

			if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
				$this->redirect($this->url->link('error/not_found', '', 'SSL'));
			}

			$results = $this->model_catalog_product->getProducts($data);
		}

		if ($results) {
			$this->load->model('tool/image');

			$this->data['products'] = $this->getChild('product/data/list', $results);
		}

		// Sorts
		$url = $this->getQueryString(array('sort', 'order'));

		if ($expand_search) $url .= '&expand_search=' . json_encode($expand_search);

		$this->addSort($this->language->get('text_default'), 'p.sort_order-ASC', $this->url->link('product/search', 'sort=p.sort_order&order=ASC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_name_asc'), 'pd.name-ASC', $this->url->link('product/search', 'sort=pd.name&order=ASC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_name_desc'), 'pd.name-DESC', $this->url->link('product/search', 'sort=pd.name&order=DESC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_date_asc'), 'p.date_added-ASC', $this->url->link('product/search','&sort=p.date_added&order=ASC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_date_desc'), 'p.date_added-DESC', $this->url->link('product/search','&sort=p.date_added&order=DESC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_price_asc'), 'p.price-ASC', $this->url->link('product/search', 'sort=p.price&order=ASC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_price_desc'), 'p.price-DESC', $this->url->link('product/search', 'sort=p.price&order=DESC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_model_asc'), 'p.model-ASC', $this->url->link('product/search', 'sort=p.model&order=ASC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_model_desc'), 'p.model-DESC', $this->url->link('product/search', 'sort=p.model&order=DESC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_random'), 'random-' . $order, $this->url->link('product/search', '&sort=random' . $url, 'SSL'));

		$this->data['sorts'] = $this->getSorts();
		$this->data['random'] = $this->url->link('product/search', '&sort=random' . $url, 'SSL');

		// Limits
		$url = $this->getQueryString(array('limit'));

		if ($expand_search) {
			$url .= '&expand_search=' . json_encode($expand_search);
		}

		$this->data['limits'] = $this->getLimits('product/search', $url);

		// Pagination
		$url = $this->getQueryString(array('page'));

		if ($expand_search) $url .= '&expand_search=' . json_encode($expand_search);

		$this->data['pagination'] = $this->getPagination($product_total, $page, $limit, 'product/search', '', $url);

		if ($page > 1) {
			$heading_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
			$meta_description = strip_tags_decode(substr($this->data['pagination'], strpos($this->data['pagination'], '<div class="results'))) . ' - ' . $meta_description;
			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
		}

		$this->document->setTitle($heading_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);

		$this->data['no_search'] = $no_search;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
		$this->data['url'] = $url;

		$this->data['compare'] = $this->url->link('product/compare', '', 'SSL');
		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('product/allproducts', '', 'SSL');
		$this->data['search'] = $this->url->link('product/search', $url, 'SSL');
		$this->data['reset'] = $this->url->link('product/search', '', 'SSL');
		$this->data['continue'] = $this->url->link('common/home', '', 'SSL');

		$this->data['more'] = $page < $max_pages ? $this->url->link('ajax/product/more', $url . '&page=' . ($page + 1), 'SSL') : '';

		if (!$this->data['products'] && (isset($this->session->data['shipping_country_id']) || isset($this->session->data['shipping_zone_id']) || isset($this->session->data['shipping_location']))) {
			// Remove Location
			$url = $this->getQueryString(array('location', 'country', 'state'));
			$request_path = isset($this->request->server['REQUEST_URI']) ? parse_url(strtolower(urldecode($this->request->server['REQUEST_URI'])), PHP_URL_PATH) : '';
			$location_remove_url = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode(ltrim($request_path . '?' . ltrim($url, "&"), "/")), 'SSL');
			$this->data['text_empty'] .= '&nbsp; &nbsp;' . sprintf($this->language->get('text_location_remove_url'), $location_remove_url);
		}

		$this->template = '/template/product/search.tpl';

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
?>
