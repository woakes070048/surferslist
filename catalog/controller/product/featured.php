<?php
class ControllerProductFeatured extends Controller {
	public function index() {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/featured')
		);

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');

		$config_product_count = false; // $this->config->get('config_product_count');
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
			$filter_country_id = '';  // $this->config->get('config_country_id');
		}

		if (isset($this->request->get['filter_zone_id'])) {
			$filter_zone_id = $this->request->get['filter_zone_id'];
		// } elseif (isset($this->session->data['shipping_zone_id'])) {
		// 	$filter_zone_id = $this->session->data['shipping_zone_id'];
		} else {
			$filter_zone_id = '';
		}

		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'date_added'; // 'random'
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : (($sort == 'date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = (isset($this->request->get['limit']) && $this->request->get['limit'] <= $this->config->get('config_catalog_limit') * 4) ? (int)$this->request->get['limit'] : $this->config->get('config_catalog_limit');

		$this->setQueryParams(array(
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
		));

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

		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('product/featured'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

		// Categories Filter
		$this->data['categories'] = array();

		$url = $this->getQueryParams(array('filter_category_id', 'filter_manufacturer_id'));

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

				$data = array(
					'filter_name'			  => $search,
					'filter_country_id'       => $filter_country_id,
					'filter_zone_id'          => $filter_zone_id,
					'filter_location'         => $filter_location,
					'filter_category_id'      => $category['category_id'],
					'filter_sub_category'     => true,
					'filter_manufacturer_id'  => $filter_manufacturer_id,
					'filter_filter'           => $filter
				);

				if ($config_product_count) {
					$product_total_category = $this->model_catalog_product->getTotalProducts($data);
				} else {
					$product_total_category = '';
				}

				$this->data['categories'][] = array(
					'id'	=> $category['category_id'],
					'name'  => utf8_strtoupper($category_name) . ($config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_category) : ''),
					'href'  => $this->url->link('product/category', 'path=' . $category['path'] . $url, 'SSL')
				);
			}
		}

		// Manufactuer Filter
		$url = $this->getQueryParams(array('filter_manufacturer_id'));

		if ($this->config->get('apac_products_refine_brand')) {
			$this->data['manufacturers'][0] = array(
				'id'	=> 0,
				'name'	=> $this->language->get('text_manufacturer_all'),
				'href'	=> $this->url->link('product/allproducts', $url, 'SSL')
			);

			$data = array(
				'filter_filter'         => $filter,
				'filter_category_id'    => $filter_category_id
			);

			$manufacturers = $this->model_catalog_manufacturer->getManufacturers($data);

			foreach ($manufacturers as $manufacturer) {
				$product_total_manufacturer = $manufacturer['product_count'];

				$this->data['manufacturers'][$manufacturer['manufacturer_id']] = array(
					'id'	=> $manufacturer['manufacturer_id'],
					'name'  => $manufacturer['name'] . ($config_product_count ? sprintf($this->language->get('text_product_count'), $product_total_manufacturer) : ''),
					'count' => !$config_product_count ? false : $manufacturer,
					'href'  => $this->url->link('product/allproducts', 'filter_manufacturer_id=' . $manufacturer['manufacturer_id'] . $url, 'SSL')
				);
			}
		}

		// Products Featured
		$url = $this->getQueryParams();

		$this->data['products'] = array();

		$data = array(
			'filter_name'         		=> $search,
			'filter_filter'      		=> $filter,
			'filter_category_id'    	=> $filter_category_id,
			'filter_manufacturer_id'	=> $filter_manufacturer_id,
			'filter_country_id'    	    => $filter_country_id,
			'filter_zone_id'            => $filter_zone_id,
			'filter_location'           => $filter_location,
			'filter_listing_type'       => $filter_listing_type,
			'filter_featured'			=> true,
			'sort'               		=> $sort,
			'order'              		=> $order,
			'start'              		=> ($page - 1) * $limit,
			'limit'              		=> $limit
		);

		$products_featured = explode(',', $this->config->get('featured_product'));

		// $product_total = $this->model_catalog_product->getTotalProductFeatured($data);
		$products = $this->model_catalog_product->getProductsIndexes($data);

		$products = array_filter($products, function ($item) use ($products_featured) {
			return in_array($item['product_id'], $products_featured);
		});

		$product_total = count($products);

		$max_pages = $limit > 0 && $product_total ? ceil($product_total / $limit) : 1;

		if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
			$this->redirect($this->url->link('error/not_found', '', 'SSL'));
		}

		$results = $this->model_catalog_product->getProductFeatured($data);

		foreach ($results as $result) {
			require(DIR_APPLICATION . 'controller/product/listing_result.inc.php');
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

		$this->data['action'] = str_replace('&amp;', '&', $this->url->link('product/featured', $url, 'SSL'));

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

		// Sorts
		$url = $this->getQueryParams(array('sort', 'order'));

		$this->addSort($this->language->get('text_default'), 'sort_order-ASC', $this->url->link('product/featured', 'sort=sort_order&order=ASC' . $url));
		$this->addSort($this->language->get('text_name_asc'), 'name-ASC', $this->url->link('product/featured', 'sort=name&order=ASC' . $url));
		$this->addSort($this->language->get('text_name_desc'), 'name-DESC', $this->url->link('product/featured', 'sort=name&order=DESC' . $url));
		$this->addSort($this->language->get('text_price_asc'), 'price-ASC', $this->url->link('product/featured', 'sort=price&order=ASC' . $url));
		$this->addSort($this->language->get('text_price_desc'), 'price-DESC', $this->url->link('product/featured', 'sort=price&order=DESC' . $url));

		if ($this->config->get('apac_products_sort_model')) {
			$this->addSort($this->language->get('text_model_asc'), 'model-ASC', $this->url->link('product/featured', 'sort=model&order=ASC' . $url));
			$this->addSort($this->language->get('text_model_desc'), 'model-DESC', $this->url->link('product/featured', 'sort=model&order=DESC' . $url));
		}

		if ($this->config->get('apac_products_sort_date')) {
			$this->addSort($this->language->get('text_date_asc'), 'date_added-ASC', $this->url->link('product/featured','&sort=date_added&order=ASC' . $url, 'SSL'));
			$this->addSort($this->language->get('text_date_desc'), 'date_added-DESC', $this->url->link('product/featured','&sort=date_added&order=DESC' . $url, 'SSL'));
		}

		$this->addSort($this->language->get('text_random'), 'random-' . $order, $this->url->link('product/featured', '&sort=random' . $url, 'SSL'));

		$this->data['sorts'] = $this->getSorts();
		$this->data['limits'] = $this->getLimits('product/featured', $this->getQueryParams(array('limit')));
		$this->data['random'] = $this->url->link('product/featured', '&sort=random' . $url, 'SSL');

		$url = $this->getQueryParams(array('page'));

		$this->data['pagination'] = $this->getPagination($product_total, $page, $limit, 'product/featured', '', $url);

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

		$this->data['compare'] = $this->url->link('product/compare', '', 'SSL');
		$this->data['back'] = (isset($this->request->server['HTTP_REFERER']) && (strpos($this->request->server['HTTP_REFERER'], $this->config->get('config_url')) === 0 || strpos($this->request->server['HTTP_REFERER'], $this->config->get('config_ssl')) === 0)) ? $this->request->server['HTTP_REFERER'] : $this->url->link('product/allproducts', '', 'SSL');
		$this->data['search'] = $this->url->link('product/search', '', 'SSL');
		$this->data['reset'] = $this->url->link('product/featured', '', 'SSL');
		$this->data['continue'] = $this->url->link('common/home', '', 'SSL');

		// $this->data['more'] = $page < $max_pages ? $this->url->link('product/featured', $url . '&page=' . ($page + 1), 'SSL') : '';
		$this->data['more'] = $page < $max_pages ? $this->url->link('product/allproducts/more', $url . '&featured=true' . '&page=' . ($page + 1), 'SSL') : '';

		if (!$this->data['products'] && (isset($this->session->data['shipping_country_id']) || isset($this->session->data['shipping_zone_id']) || isset($this->session->data['shipping_location']))) {
			// Remove Location
			$url = $this->getQueryParams(array('filter_location', 'filter_country_id', 'filter_zone_id'));
			$request_path = isset($this->request->server['REQUEST_URI']) ? parse_url(strtolower(urldecode($this->request->server['REQUEST_URI'])), PHP_URL_PATH) : '';
			$location_remove_url = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode(ltrim($request_path . '?' . ltrim($url, "&"), "/")), 'SSL');
			$this->data['text_empty'] .= '&nbsp; &nbsp;' . sprintf($this->language->get('text_location_remove_url'), $location_remove_url);
		}

		$this->template = '/template/product/featured.tpl';

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
