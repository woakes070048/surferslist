<?php
class ControllerProductSpecial extends Controller {
	public function index() {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/special')
		);

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$display_more_options = false;

		if (isset($this->request->get['type']) && !is_array($this->request->get['type'])) {
			$filter_listing_type = explode(',', $this->request->get['type']);
			$display_more_options = true;
		} else if (isset($this->request->get['forsale']) && $this->request->get['forsale']) {
			$filter_listing_type = array('0', '1'); // classified and buy-now
		} else {
			$filter_listing_type = array(); // array('-1', '0', '1');
		}

		if (isset($this->request->get['forsale'])) {
			$forsale = $this->request->get['forsale'];
		} else if ($filter_listing_type == array('0', '1')) {
			$forsale = true;
		} else {
			$forsale = false;
		}

		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else if (isset($this->request->get['s'])) {
			$search = $this->request->get['s'];
		} else {
			$search = '';
		}

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = '';
		}

		if (isset($this->request->get['filter_manufacturer_id'])) {
			$filter_manufacturer_id = $this->request->get['filter_manufacturer_id'];
		} else {
			$filter_manufacturer_id = '';
		}

		if (isset($this->request->get['filter_location'])) {
			$filter_location = $this->request->get['filter_location'];
		// } elseif (isset($this->session->data['shipping_location'])) {
		// 	$filter_location = $this->session->data['shipping_location'];
		} else {
			$filter_location = '';
		}

		if (isset($this->request->get['filter_country_id'])) {
			$filter_country_id = $this->request->get['filter_country_id'];
		// } elseif (isset($this->session->data['shipping_country_id'])) {
		// 	$filter_country_id = $this->session->data['shipping_country_id'];
		} else {
			$filter_country_id = '';
		}

		if (isset($this->request->get['filter_zone_id'])) {
			$filter_zone_id = $this->request->get['filter_zone_id'];
		// } elseif (isset($this->session->data['shipping_zone_id'])) {
		// 	$filter_zone_id = $this->session->data['shipping_zone_id'];
		} else {
			$filter_zone_id = '';
		}

		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : $this->config->get('apac_products_sort_default'); // 'random', 'p.date_added'
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : (($sort == 'p.date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = (isset($this->request->get['limit']) && $this->request->get['limit'] <= $this->config->get('config_catalog_limit') * 4) ? (int)$this->request->get['limit'] : $this->config->get('config_catalog_limit');

		$query_params = array(
			'search',
			'filter_location',
			// 'filter',
			// 'filter_category_id',
			// 'filter_manufacturer_id',
			'filter_country_id',
			'filter_zone_id',
			// 'forsale',
			// 'type',
			'sort',
			'order',
			'limit'
		);

		$this->setQueryParams($query_params);

		$heading_title = $this->language->get('heading_title');
		$meta_description = $this->language->get('meta_description');
		$meta_keyword = $this->language->get('meta_keyword');

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));

		if ($this->config->get('apac_status') && $this->config->get('apac_categories_status') && $this->config->get('apac_categories_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_categories'), $this->url->link('product/allcategories'));
		}

		if ($this->config->get('apac_status') && $this->config->get('apac_products_status') && $this->config->get('apac_products_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_products'), $this->url->link('product/allproducts'));
		}

		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('product/special'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

		// get all special listings
		$url = $this->getQueryString();

		$this->data['products'] = array();

		$data = array(
			'filter_name'        => $search,
			'filter_location'    => $filter_location,
			'filter_country_id'  => $filter_country_id,
			'filter_zone_id'     => $filter_zone_id,
			'sort'  			 => $sort,
			'order' 			 => $order,
			'start' 			 => ($page - 1) * $limit,
			'limit' 			 => $limit
		);

		$product_total = $this->model_catalog_product->getTotalProductSpecials($data);

		$max_pages = $limit > 0 ? ceil($product_total / $limit) : 1;

		if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
			$this->redirect($this->url->link('error/not_found'));
		}

		$this->data['products'] = $this->getChild('product/data/list', $this->model_catalog_product->getProductSpecials($data));

		// Sorts
		$url = $this->getQueryString(array('sort', 'order'));

		$this->addSort($this->language->get('text_default'), 'p.sort_order-ASC', $this->url->link('product/special', 'sort=p.sort_order&order=ASC' . $url));
		$this->addSort($this->language->get('text_name_asc'), 'pd.name-ASC', $this->url->link('product/special', 'sort=pd.name&order=ASC' . $url));
		$this->addSort($this->language->get('text_name_desc'), 'pd.name-DESC', $this->url->link('product/special', 'sort=pd.name&order=DESC' . $url));
		$this->addSort($this->language->get('text_price_asc'), 'ps.price-ASC', $this->url->link('product/special', 'sort=ps.price&order=ASC' . $url));
		$this->addSort($this->language->get('text_price_desc'), 'ps.price-DESC', $this->url->link('product/special', 'sort=ps.price&order=DESC' . $url));

		if ($this->config->get('apac_products_sort_model')) {
			$this->addSort($this->language->get('text_model_asc'), 'p.model-ASC', $this->url->link('product/special', 'sort=p.model&order=ASC' . $url));
			$this->addSort($this->language->get('text_model_desc'), 'p.model-DESC', $this->url->link('product/special', 'sort=p.model&order=DESC' . $url));
		}

		if ($this->config->get('apac_products_sort_date')) {
			$this->addSort($this->language->get('text_date_asc'), 'p.date_added-ASC', $this->url->link('product/special','&sort=p.date_added&order=ASC' . $url));
			$this->addSort($this->language->get('text_date_desc'), 'p.date_added-DESC', $this->url->link('product/special','&sort=p.date_added&order=DESC' . $url));
		}

		$this->addSort($this->language->get('text_random'), 'random-' . $order, $this->url->link('product/special', '&sort=random' . $url));

		$this->data['sorts'] = $this->getSorts();
		$this->data['limits'] = $this->getLimits('product/special', $this->getQueryString(array('limit')));

		$url = $this->getQueryString(array('page'));

		$this->data['pagination'] = $this->getPagination($product_total, $page, $limit, 'product/special', '', $url);

		if ($page > 1) {
			$heading_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
			$meta_description = strip_tags_decode(substr($this->data['pagination'], strpos($this->data['pagination'], '<div class="results'))) . ' - ' . $meta_description;
			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
		}

		$this->document->setTitle($heading_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);

		$this->data['display_more_options'] = $display_more_options;
		$this->data['filter'] = $filter;
		$this->data['filter_search'] = $search;
		$this->data['filter_category_id'] = $filter_category_id;
		$this->data['filter_manufacturer_id'] = $filter_manufacturer_id;
		$this->data['filter_country_id'] = $filter_country_id;
		$this->data['filter_zone_id'] = $filter_zone_id;
		$this->data['filter_location'] = $filter_location;
		$this->data['type_selected'] = $filter_listing_type;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
		$this->data['url'] = $url;

		$this->data['random'] = $this->url->link('product/special', '&sort=random' . $url);
		$this->data['compare'] = $this->url->link('product/compare');
		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('product/allproducts');
		$this->data['search'] = $this->url->link('product/search');
		$this->data['reset'] = $this->url->link('product/special');
		$this->data['continue'] = $this->url->link('common/home');

		// $this->data['more'] = $page < $max_pages ? $this->url->link('product/special', $url . '&page=' . ($page + 1)) : '';
		$this->data['more'] = $page < $max_pages ? $this->url->link('ajax/product/more', $url . '&special=true' . '&page=' . ($page + 1)) : '';

		if (!$this->data['products'] && (isset($this->session->data['shipping_country_id']) || isset($this->session->data['shipping_zone_id']) || isset($this->session->data['shipping_location']))) {
			// Remove Location
			$url = $this->getQueryString(array('filter_location', 'filter_country_id', 'filter_zone_id'));
			$request_path = isset($this->request->server['REQUEST_URI']) ? parse_url(strtolower(urldecode($this->request->server['REQUEST_URI'])), PHP_URL_PATH) : '';
			$location_remove_url = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode(ltrim($request_path . '?' . ltrim($url, "&"), "/")));
			$this->data['text_empty'] .= '&nbsp; &nbsp;' . sprintf($this->language->get('text_location_remove_url'), $location_remove_url);
		}

		$this->template = 'template/product/special.tpl';

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
