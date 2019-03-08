<?php
class ControllerProductCategory extends Controller {
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

		if (isset($this->request->get['filter_manufacturer_id'])) {
			$filter_manufacturer_id = $this->request->get['filter_manufacturer_id'];
		} else if (isset($this->request->get['manufacturer_id'])) {
			$filter_manufacturer_id = $this->request->get['manufacturer_id'];
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
			'manufacturer_id',
			'filter_country_id',
			'filter_zone_id',
			'forsale',
			'type',
			'sort',
			'order',
			'limit'
		);

		$this->setQueryParams($query_params);

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));

		if ($this->config->get('apac_status') && $this->config->get('apac_categories_status') && $this->config->get('apac_categories_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_categories'), $this->url->link('product/allcategories'));
		}

		if ($this->config->get('apac_status') && $this->config->get('apac_products_status') && $this->config->get('apac_products_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_products'), $this->url->link('product/allproducts'));
		}

		$category_parents = array();

		$category_parents[] = array(
			'id'      	=> 0,
			'path'      => '',
			'name'      => $this->language->get('text_category_all')
		);

		if (!empty($this->request->get['path']) && !is_array($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$this->addBreadcrumb($category_info['name'], $this->url->link('product/category', 'path=' . $path));

					$category_parents[] = array(
						'id'      	=> (int)$path_id,
						'path'      => $path,
						'name'      => $category_info['name']
					);
				}
			}
		} else {
			$category_id = 0;
		}

		$category_info = $category_id ? $this->model_catalog_category->getCategory($category_id) : array();

		if (!$category_info || ($filter_manufacturer_id && !in_array($filter_manufacturer_id, explode(',', $category_info['manufacturer_ids'])))) {
			$this->session->data['warning'] = $this->language->get('text_error');
			return $this->forward('error/notfound');
			// return $this->redirect($this->url->link('error/not_found', '', 'SSL'));
		}

		// $heading_title = substr_count($category_info['path'], '_') > 0 ? sprintf($this->language->get('text_for_sale'), $category_info['name']) : sprintf($this->language->get('text_for_sale'), $category_info['name'] . ' ' . $this->language->get('text_equipment'));
		$heading_title = sprintf($this->language->get('text_for_sale'), $category_info['name']);
		$meta_description = !empty($category_info['meta_description']) ? $category_info['meta_description'] : sprintf($this->language->get('text_category_meta_description'), $category_info['name'], $this->config->get('config_name'));
		$meta_keyword = !empty($category_info['meta_keyword']) ? $category_info['meta_keyword'] : sprintf($this->language->get('text_category_meta_keyword'), strtolower($category_info['name']));

		// more breadcrumbs
		$this->addBreadcrumb($category_info['name'], $this->url->link('product/category', 'path=' . $category_info['path']));

		if ($filter_manufacturer_id) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($filter_manufacturer_id);
			$this->addBreadcrumb($manufacturer_info['name'], $this->url->link('product/category', 'path=' . $category_info['path'] . '&manufacturer_id=' . $filter_manufacturer_id));
		}

		$location_name = $this->getLocationName('long');

		if ($location_name) {
			$heading_title .= ' - ' . $location_name;

			$this->addBreadcrumb($location_name, $this->url->link('information/location'));
		}

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['heading_title'] = $heading_title;
		$this->data['category_icon'] = strpos($category_info['path_name'], $this->language->get('text_separator')) ? friendly_url(utf8_substr($category_info['path_name'], 0, strpos($category_info['path_name'], $this->language->get('text_separator')))) : friendly_url($category_info['path_name']);
		$this->data['description'] = !empty($category_info['description']) ? html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8') : '<p>' . $meta_description . '</p>';

		// Listings
		$data = array(
			'filter_name'         		=> $search,
			'filter_filter'      		=> $filter,
			'filter_category_id' 		=> $category_id,
			'filter_manufacturer_id' 	=> $filter_manufacturer_id,
			'filter_country_id'  		=> $filter_country_id,
			'filter_zone_id'            => $filter_zone_id,
			'filter_location'           => $filter_location,
			'filter_listing_type' 		=> $filter_listing_type,
			'sort'               		=> $sort,
			'order'              		=> $order,
			'start'              		=> ($page - 1) * $limit,
			'limit'              		=> $limit
		);

		$products = $this->model_catalog_product->getProductsIndexes($data);

		$product_total = count($products);

		$max_pages = $limit > 0 && $product_total ? ceil($product_total / $limit) : 1;

		if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
			$this->redirect($this->url->link('error/not_found', '', 'SSL'));
		}

		$this->data['products'] = $this->getChild('product/product/list', $this->model_catalog_product->getProducts($data));

		$this->data['refine'] = $this->getChild('module/refine', array(
			'query_params' => $query_params,
			'route' => 'product/category',
			'path' => 'path=' . $category_info['path'],
			'filter' => $data,
			'products' => $products,
			'category_parents' => $category_parents,
			'display_more_options' => $display_more_options,
			'forsale' => $forsale
		));

		$url = $this->getQueryParams(array('page'));

		$this->data['pagination'] = $this->getPagination($product_total, $page, $limit, 'product/category', 'path=' . $category_info['path'] . ($filter_manufacturer_id ? '&manufacturer_id=' . $filter_manufacturer_id : ''), $url);

		if ($page > 1) {
			$heading_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
			$meta_description = strip_tags_decode(substr($this->data['pagination'], strpos($this->data['pagination'], '<div class="results'))) . ' - ' . $meta_description;
			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
		}

		$this->document->setTitle($heading_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);

		$this->data['action'] = str_replace('&amp;', '&', $this->url->link('product/category', 'path=' . $category_info['path'] . $url, 'SSL'));
		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('product/allproducts', '', 'SSL');
		$this->data['search'] = $this->url->link('product/search', '', 'SSL');
		$this->data['reset'] = $this->url->link('product/category', 'path=' . $category_info['path'], 'SSL');
		$this->data['continue'] = $this->url->link('common/home', '', 'SSL');
		$this->data['more'] = $page < $max_pages ? $this->url->link('ajax/product/more', 'path=' . $category_info['path'] . $url . '&page=' . ($page + 1), 'SSL') : '';
		$this->data['url'] = $url;

		if (!$this->data['products'] && (isset($this->session->data['shipping_country_id']) || isset($this->session->data['shipping_zone_id']) || isset($this->session->data['shipping_location']))) {
			$request_path = isset($this->request->server['REQUEST_URI']) ? parse_url(strtolower(urldecode($this->request->server['REQUEST_URI'])), PHP_URL_PATH) : '';
			$url = $this->getQueryParams(array('filter_location', 'filter_country_id', 'filter_zone_id'));
			$location_remove_url = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode(ltrim($request_path . '?' . ltrim($url, "&"), "/")), 'SSL');
			$this->data['text_empty'] .= '&emsp;' . sprintf($this->language->get('text_location_remove_url'), $location_remove_url);
		}

		$this->template = '/template/product/category.tpl';

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
