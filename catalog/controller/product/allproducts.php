<?php
class ControllerProductAllProducts extends Controller {
	public function index() {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/category')
		);

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
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
		} elseif (isset($this->session->data['shipping_location'])) {
			$filter_location = $this->session->data['shipping_location'];
		} else {
			$filter_location = '';
		}

		if (isset($this->request->get['filter_country_id'])) {
			$filter_country_id = $this->request->get['filter_country_id'];
		} elseif (isset($this->session->data['shipping_country_id'])) {
			$filter_country_id = $this->session->data['shipping_country_id'];
		} else {
			$filter_country_id = ''; // $this->config->get('config_country_id');
		}

		if (isset($this->request->get['filter_zone_id'])) {
			$filter_zone_id = $this->request->get['filter_zone_id'];
		} elseif (isset($this->session->data['shipping_zone_id'])) {
			$filter_zone_id = $this->session->data['shipping_zone_id'];
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
			'filter',
			'filter_category_id',
			'filter_manufacturer_id',
			'filter_country_id',
			'filter_zone_id',
			'forsale',
			'type',
			'sort',
			'order',
			'limit'
		);

		$this->setQueryParams($query_params);

		// get All Products info from APAC Module config settings
		$language_id = (int)$this->config->get('config_language_id');
		$all_products_description = $this->config->get('apac_products_description');

		$all_products_info = array(
			'name'					=> $all_products_description[$language_id]['name'],
			'description'			=> $all_products_description[$language_id]['description'],
			'meta_description'		=> $all_products_description[$language_id]['meta_description'],
			'meta_keyword'			=> $all_products_description[$language_id]['meta_keyword'],
			'image'					=> $this->config->get('apac_products_image'),
			'keyword'				=> $this->config->get('apac_products_keyword')
		);

		$heading_title = (!empty($all_products_info['name']) ? $all_products_info['name'] : $this->language->get('text_all_products'));
		$meta_description = !empty($all_products_info['meta_description']) ? $all_products_info['meta_description'] : sprintf($this->language->get('text_all_products_meta_description'), $this->config->get('config_name'));
		$meta_keyword = !empty($all_products_info['meta_keyword']) ? $all_products_info['meta_keyword'] : $this->language->get('text_all_products_meta_keyword');

		if ($filter_category_id || $filter_manufacturer_id) {
			$heading_title .= ' - ';
		}

		if ($filter_category_id) {
			$category_filter = $this->model_catalog_category->getCategory($filter_category_id);

			if ($category_filter) {
				$heading_title .= $category_filter['name'];
			}
		}

		if ($filter_category_id && $filter_manufacturer_id) {
			$heading_title .= ', ';
		}

		if ($filter_manufacturer_id) {
			$manufacturer_filter = $this->model_catalog_manufacturer->getManufacturer($filter_manufacturer_id);

			if ($manufacturer_filter) {
				$heading_title .= $manufacturer_filter['name'];
			}
		}

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));

		if ($this->config->get('apac_status') && $this->config->get('apac_categories_status') && $this->config->get('apac_categories_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_categories'), $this->url->link('product/allcategories'));
		}

		if ($this->config->get('apac_status') && $this->config->get('apac_products_status') && $this->config->get('apac_products_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_products'), $this->url->link('product/allproducts'));
		}

		$location_name = $this->getLocationName('long');

		if ($location_name) {
			$heading_title .= ' - ' . $location_name;

			$this->addBreadcrumb($location_name, $this->url->link('information/location'));
		}

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['heading_title'] = $heading_title;
		$this->data['text_all_products'] = $heading_title;

		$this->data['thumb'] = (!empty($all_products_info['image']))
			? $this->model_tool_image->resize($all_products_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'))
			: false;

		$this->data['description'] = !empty($all_products_info['description'])
			? html_entity_decode($all_products_info['description'], ENT_QUOTES, 'UTF-8')
			: false;

		// Listings
		$data = array(
			'filter_name'         		=> $search,
			'filter_filter'      		=> $filter,
			'filter_category_id'    	=> $filter_category_id,
			'filter_manufacturer_id'	=> $filter_manufacturer_id,
			'filter_country_id'    	    => $filter_country_id,
			'filter_zone_id'            => $filter_zone_id,
			'filter_location'           => $filter_location,
			'filter_listing_type'       => $filter_listing_type,
			'sort'               		=> $sort,
			'order'              		=> $order,
			'start'              		=> ($page - 1) * $limit,
			'limit'              		=> $limit
		);

		$products = $this->model_catalog_product->getProductsIndexes($data);

		$product_total = count($products);

		$max_pages = $limit > 0 && $product_total ? ceil($product_total / $limit) : 1;

		if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
			$this->redirect($this->url->link('error/not_found'));
		}

		$url = $this->getQueryString(array('page'));

		$this->data['products'] = $this->getChild('product/data/list', array(
			'products' => $this->model_catalog_product->getProducts($data),
			'more' => $page < $max_pages ? $this->url->link('ajax/product/more', $url . '&page=' . ($page + 1)) : ''
		));

		$this->data['refine'] = $this->getChild('module/refine', array(
			'query_params' => $query_params,
			'route' => 'product/allproducts',
			'path' => '',
			'filter' => $data,
			'products' => $products,
			'display_more_options' => $display_more_options,
			'forsale' => $forsale
		));

		$this->data['pagination'] = $this->getPagination($product_total, $page, $limit, 'product/allproducts', '', $url);

		if ($page > 1) {
			$heading_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
			$meta_description = strip_tags_decode(substr($this->data['pagination'], strpos($this->data['pagination'], '<div class="results'))) . ' - ' . $meta_description;
			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
		}

		$this->document->setTitle($heading_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);

		$this->data['action'] = str_replace('&amp;', '&', $this->url->link('product/allproducts', $url));
		$this->data['search'] = $this->url->link('product/search');
		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('common/home');
		$this->data['continue'] = $this->url->link('common/home');
		$this->data['reset'] = $this->url->link('product/allproducts');
		$this->data['url'] = $url;

		if (!$this->data['products'] && (isset($this->session->data['shipping_country_id']) || isset($this->session->data['shipping_zone_id']) || isset($this->session->data['shipping_location']))) {
			// Remove Location
			$url = $this->getQueryString(array('filter_location', 'filter_country_id', 'filter_zone_id'));
			$request_path = isset($this->request->server['REQUEST_URI']) ? parse_url(strtolower(urldecode($this->request->server['REQUEST_URI'])), PHP_URL_PATH) : '';
			$location_remove_url = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode(ltrim($request_path . '?' . ltrim($url, "&"), "/")));
			$this->data['text_empty'] .= '&nbsp; &nbsp;' . sprintf($this->language->get('text_location_remove_url'), $location_remove_url);
		}

		$this->template = 'template/product/allproducts.tpl';

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
