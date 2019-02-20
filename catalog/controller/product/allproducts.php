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

		$config_product_count = true; // $this->config->get('config_product_count');
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

		$this->setQueryParams(array(
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
		));

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

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['heading_title'] = $heading_title;
		$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
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
			$this->redirect($this->url->link('error/not_found', '', 'SSL'));
		}

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$this->data['products'] = array();

		$results = $this->model_catalog_product->getProducts($data);

		foreach ($results as $result) {
			// adds to $this->data['products']
			require(DIR_APPLICATION . 'controller/product/listing_result.inc.php');
		}

		// Categories Filter
		$this->data['categories'] = array();

		$url = $this->getQueryParams(array('filter_category_id'));

		if ($this->config->get('apac_products_refine_category')) {
			$this->data['categories'][] = array(
				'id'	=> 0,
				'name'	=> $this->language->get('text_category_all'),
				'href'	=> $this->url->link('product/allproducts', $url, 'SSL')
			);

			$categories = $this->model_catalog_category->getCategories(0);

			foreach ($categories as $category) {
				if (utf8_strpos($category['name'], $this->language->get('heading_more')) !== false) {
					$category_name = $this->language->get('heading_more');
				} else if (utf8_strpos($category['name'], $this->language->get('heading_other')) !== false) {
					$category_name = $this->language->get('heading_other');
				} else {
					$category_name = $category['name'];
				}

				if (false && $config_product_count) {
					// $product_total_category = $category['product_count'];
					$product_total_category = count(array_filter($products, function ($item) use ($category) {
						return in_array($category['category_id'], $item['category_ids']);
					}));
				}

				$this->data['categories'][] = array(
					'id'	=> $category['category_id'],
					'name'  => utf8_strtoupper($category_name) . (false && $config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_category) : ''),
					'href'  => $this->url->link('product/category', 'path=' . $category['path'] . $url, 'SSL')
				);
			}
		}

		// Manufacturer Filter
		$this->data['manufacturers'] = array();

		$url = $this->getQueryParams(array('filter_manufacturer_id'));

		if ($this->config->get('apac_products_refine_brand')) {
			$this->data['manufacturers'][0] = array(
				'id'	=> 0,
				'name'	=> $this->language->get('text_manufacturer_all'),
				'href'	=> $this->url->link('product/allproducts', $url, 'SSL')
			);

			$manufacturers_data = array(
				'filter_category_id'    => $filter_category_id
			);

			$manufacturers = $this->model_catalog_manufacturer->getManufacturers($manufacturers_data);

			foreach ($manufacturers as $manufacturer) {
				if ($config_product_count) {
					$product_total_manufacturer = count(array_filter($products, function ($item) use ($manufacturer) {
						return $manufacturer['manufacturer_id'] == $item['manufacturer_id'];
					}));
				}

				$this->data['manufacturers'][$manufacturer['manufacturer_id']] = array(
					'id'	=> $manufacturer['manufacturer_id'],
					'name'  => $manufacturer['name'] . ($config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_manufacturer) : ''),
					'product_count' => $config_product_count ? $product_total_manufacturer : null,
					'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id'] . $url, 'SSL')
				);
			}
		}

		// Types
		$this->data['listing_types'] = array();

		$listing_types = array(
			array(
				'id' 	=> '0',
				'name'	=> $this->language->get('text_classified'),
				'sort_order' => '1'
			),
			array(
				'id'	=> '1',
				'name'	=> $this->language->get('text_buy_now'),
				'sort_order' => '2'
			),
			array(
				'id'	=> '-1',
				'name'	=> $this->language->get('text_shared'),
				'sort_order' => '3'
			)
		);

		foreach ($listing_types as $listing_type) {
			if ($config_product_count) {
				$product_total_type = count(array_filter($products, function ($item) use ($listing_type) {
					return $item['type_id'] == $listing_type['id'];
				}));
			}

			$this->data['listing_types'][] = array(
				'type_id' => $listing_type['id'],
				'name'    => $listing_type['name'] . ($config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_type) : '')
			);
		}

		// Filter Groups
		$url = $this->getQueryParams(array('filter', 'type', 'search'));

		$this->data['action'] = str_replace('&amp;', '&', $this->url->link('product/allproducts', $url, 'SSL'));

		if (isset($this->request->get['filter']) && !is_array($this->request->get['filter'])) {
			$this->data['filter_category'] = explode(',', $this->request->get['filter']);
			$display_more_options = true;
		} else {
			$this->data['filter_category'] = array();
		}

		$this->data['filter_groups'] = array();

		$filter_groups = $this->model_catalog_category->getCategoryFiltersAll(); /* display all filters all the time */

		foreach ($filter_groups as $filter_group) {
			$filter_data = array();

			foreach ($filter_group['filter'] as $filter_group_filter) {
				if ($config_product_count) {
					$product_total_filter = count(array_filter($products, function ($item) use ($filter_group_filter) {
						return in_array($filter_group_filter['filter_id'], $item['filter_ids']);
					}));
				}

				$filter_data[] = array(
					'filter_id' => $filter_group_filter['filter_id'],
					'name'      => $filter_group_filter['name'] . ($config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_filter) : '')
				);
			}

			$this->data['filter_groups'][] = array(
				'filter_group_id' => $filter_group['filter_group_id'],
				'name'            => $filter_group['name'],
				'filter'          => $filter_data
			);
		}

		// Sort, Limit
		$url = $this->getQueryParams(array('sort', 'order'));

		if ($this->config->get('apac_products_sort_sort_order')) {
			$this->addSort($this->language->get('text_default'), 'p.sort_order-ASC', $this->url->link('product/allproducts','&sort=p.sort_order&order=ASC' . $url));
		}

		if ($this->config->get('apac_products_sort_name')) {
			$this->addSort($this->language->get('text_name_asc'), 'pd.name-ASC', $this->url->link('product/allproducts','&sort=pd.name&order=ASC' . $url));
			$this->addSort($this->language->get('text_name_desc'), 'pd.name-DESC', $this->url->link('product/allproducts','&sort=pd.name&order=DESC' . $url));
		}

		if ($this->config->get('apac_products_sort_price')) {
			$this->addSort($this->language->get('text_price_asc'), 'p.price-ASC', $this->url->link('product/allproducts','&sort=p.price&order=ASC' . $url));
			$this->addSort($this->language->get('text_price_desc'), 'p.price-DESC', $this->url->link('product/allproducts','&sort=p.price&order=DESC' . $url));
		}

		if ($this->config->get('apac_products_sort_model')) {
			$this->addSort($this->language->get('text_model_asc'), 'p.model-ASC', $this->url->link('product/allproducts','&sort=p.model&order=ASC' . $url));
			$this->addSort($this->language->get('text_model_desc'), 'p.model-DESC', $this->url->link('product/allproducts','&sort=p.model&order=DESC' . $url));
		}

		if ($this->config->get('apac_products_sort_date')) {
			$this->addSort($this->language->get('text_date_asc'), 'p.date_added-ASC', $this->url->link('product/allproducts','&sort=p.date_added&order=ASC' . $url));
			$this->addSort($this->language->get('text_date_desc'), 'p.date_added-DESC', $this->url->link('product/allproducts','&sort=p.date_added&order=DESC' . $url));
		}

		$this->addSort($this->language->get('text_random'), 'random-' . $order,  $this->url->link('product/allproducts', '&sort=random' . $url));

		$this->data['sorts'] = $this->getSorts();
		$this->data['limits'] = $this->getLimits('product/allproducts', $this->getQueryParams(array('limit')));

		// Pagination
		$url = $this->getQueryParams(array('page'));

		$this->data['pagination'] = $this->getPagination($product_total, $page, $limit, 'product/allproducts', '', $url);

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
		$this->data['forsale'] = $forsale;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
		$this->data['url'] = $url;
		// $this->data['all_listings_page'] = true;

		$request_path = isset($this->request->server['REQUEST_URI']) ? parse_url(strtolower(urldecode($this->request->server['REQUEST_URI'])), PHP_URL_PATH) : '';

		$this->data['location_page'] = $this->url->link('information/location', 'redirect_path=' . urlencode(ltrim($request_path, "/")), 'SSL');
		$this->data['random'] = $this->url->link('product/allproducts', '&sort=random' . $url, 'SSL');
		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('common/home', '', 'SSL');
		$this->data['compare'] = $this->url->link('product/compare', '', 'SSL');
		$this->data['search'] = $this->url->link('product/search', '', 'SSL');
		$this->data['reset'] = $this->url->link('product/allproducts', '', 'SSL');
		$this->data['continue'] = $this->url->link('common/home', '', 'SSL');
		$this->data['more'] = $page < $max_pages ? $this->url->link('product/allproducts/more', $url . '&page=' . ($page + 1), 'SSL') : '';

		if (!$this->data['products'] && (isset($this->session->data['shipping_country_id']) || isset($this->session->data['shipping_zone_id']) || isset($this->session->data['shipping_location']))) {
			// Remove Location
			$url = $this->getQueryParams(array('filter_location', 'filter_country_id', 'filter_zone_id'));
			$location_remove_url = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode(ltrim($request_path . '?' . ltrim($url, "&"), "/")), 'SSL');
			$this->data['text_empty'] .= '&nbsp; &nbsp;' . sprintf($this->language->get('text_location_remove_url'), $location_remove_url);
		}

		$this->template = '/template/product/allproducts.tpl';

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
