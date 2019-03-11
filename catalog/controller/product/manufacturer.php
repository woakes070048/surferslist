<?php
class ControllerProductManufacturer extends Controller {
	public function index() {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/manufacturer')
		);

		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		$config_product_count = false; // $this->config->get('config_product_count');

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = '';
		}

		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'name'; // 'random'
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : (($sort == 'date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = (isset($this->request->get['limit']) && $this->request->get['limit'] <= $this->config->get('config_catalog_limit') * 4) ? (int)$this->request->get['limit'] : $this->config->get('config_catalog_limit');

		$this->setQueryParams(array(
			'filter_name',
			'filter',
			'filter_category_id',
			'sort',
			'order',
			'limit'
		));

		$heading_title = $this->language->get('heading_title');
		$meta_description = $this->language->get('meta_description');
		$meta_keyword = $this->language->get('meta_keyword');

		$this->data['button_continue'] = $this->language->get('button_back');

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_brand'), $this->url->link('product/manufacturer'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		// Categories
		$url = $this->getQueryString(array('filter_category_id'));

		$this->data['category_manufacturers'] = $this->cache->get('category.manufacturers.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($this->data['category_manufacturers'] === false) {
			$this->data['category_manufacturers'] = array();

			$this->data['category_manufacturers'][] = array(
				'id'	=> 0,
				'name'	=> $this->language->get('text_category_all'),
				'href'	=> $this->url->link('product/manufacturer', 'filter_category_id=0' . $url, 'SSL')
			);

			$category_manufacturers = $this->model_catalog_category->getCategories(0);

			foreach ($category_manufacturers as $category_manufacturer) {
				if (utf8_strpos($category_manufacturer['name'], $this->language->get('heading_more')) !== false) {
					$category_name = $this->language->get('heading_more');
				} else if (utf8_strpos($category_manufacturer['name'], $this->language->get('heading_other')) !== false) {
					$category_name = $this->language->get('heading_other');
				} else {
					$category_name = $category_manufacturer['name'];
				}

				$data = array(
					'filter_category_id'  => $category_manufacturer['category_id'],
					'filter_sub_category' => true
				);

				$category_manufacturer_total = $this->model_catalog_manufacturer->getTotalManufacturers($data);

				$this->data['category_manufacturers'][] = array(
					'id'	=> $category_manufacturer['category_id'],
					'name'  => utf8_strtoupper($category_name) . sprintf($this->language->get('text_product_count'), $category_manufacturer_total),
					'href'  => $this->url->link('product/manufacturer', 'filter_category_id=' . $category_manufacturer['category_id'] . $url, 'SSL')
				);
			}

			$this->cache->set('category.manufacturers.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data['category_manufacturers'], 60 * 60 * 24); // 1 day cache expiration
		}

		// Brands
		$this->data['categories'] = array();

		$data = array(
			'filter_filter'      => $filter,
			'filter_name'        => $filter_name,
			'filter_category_id' => $filter_category_id,
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit
		);

		$manufacturer_total = $this->model_catalog_manufacturer->getTotalManufacturers($data);

		$max_pages = $limit > 0 ? ceil($manufacturer_total / $limit) : 1;

		if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
			$this->redirect($this->url->link('error/not_found', '', 'SSL'));
		}

		$results = $this->model_catalog_manufacturer->getManufacturers($data);

		foreach ($results as $result) {
			if (is_numeric(utf8_substr($result['name'], 0, 1))) {
				$key = '0 - 9';
			} else {
				$key = utf8_substr(utf8_strtoupper($result['name']), 0, 1);
			}

			if (!isset($this->data['manufacturers'][$key])) {
				$this->data['categories'][$key]['name'] = $key;
			}

			$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'), 'fw');

			$this->data['categories'][$key]['manufacturer'][] = array(
				'name' => $result['name'],
				'website' => $result['url'],
				'image' => $image,
				'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
			);
		}

		// Sort, Limit
		$url = $this->getQueryString(array('sort', 'order'));

		$this->addSort($this->language->get('text_default'), 'sort_order-ASC', $this->url->link('product/manufacturer','&sort=sort_order&order=ASC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_name_asc'), 'name-ASC', $this->url->link('product/manufacturer','&sort=name&order=ASC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_name_desc'), 'name-DESC', $this->url->link('product/manufacturer','&sort=name&order=DESC' . $url, 'SSL'));
		$this->addSort($this->language->get('text_random'), 'random-' . $order, $this->url->link('product/manufacturer', '&sort=random' . $url, 'SSL'));

		$this->data['sorts'] = $this->getSorts();
		$this->data['limits'] = $this->getLimits('product/manufacturer', $this->getQueryString(array('limit')));

		$url = $this->getQueryString(array('page'));

		$this->data['pagination'] = $this->getPagination($manufacturer_total, $page, $limit, 'product/manufacturer', '', $url);

		if ($page > 1) {
			$heading_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
			$meta_description = strip_tags_decode(substr($this->data['pagination'], strpos($this->data['pagination'], '<div class="results'))) . ' - ' . $meta_description;
			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
		}

		$this->document->setTitle($heading_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);

		$this->data['filter'] = $filter;
		$this->data['filter_name'] = $filter_name;
		$this->data['filter_category_id'] = $filter_category_id;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;

		$this->data['random'] = $this->url->link('product/manufacturer', '&sort=random' . $url, 'SSL');
		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('product/allproducts', '', 'SSL');
		$this->data['search'] = $this->url->link('product/search', '', 'SSL');
		$this->data['reset'] = $this->url->link('product/manufacturer', '', 'SSL');
		$this->data['continue'] = $this->url->link('common/home', '', 'SSL');

		$this->template = '/template/product/manufacturer_list.tpl';

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

	public function info() {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/manufacturer')
		);

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');

		$display_more_options = false;
		$jumpTo = '#brand-listings';

		$manufacturer_id = !empty($this->request->get['manufacturer_id']) ? (int)$this->request->get['manufacturer_id'] : 0;

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
			$filter_country_id = '';  // $this->config->get('config_country_id');
		}

		if (isset($this->request->get['filter_zone_id'])) {
			$filter_zone_id = $this->request->get['filter_zone_id'];
		} elseif (isset($this->session->data['shipping_zone_id'])) {
			$filter_zone_id = $this->session->data['shipping_zone_id'];
		} else {
			$filter_zone_id = '';
		}

		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : $this->config->get('apac_products_sort_default'); // 'p.date_added'; // 'random'
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : (($sort == 'p.date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = (isset($this->request->get['limit']) && $this->request->get['limit'] <= $this->config->get('config_catalog_limit') * 4) ? (int)$this->request->get['limit'] : $this->config->get('config_catalog_limit');

		$query_params = array(
			'search',
			'filter_name',
			'filter_location',
			'filter',
			'filter_category_id',
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
		$this->addBreadcrumb($this->language->get('text_brand'), $this->url->link('product/manufacturer'));

		$this->data['brand'] = array();

		$manufacturer_info = $manufacturer_id ? $this->model_catalog_manufacturer->getManufacturer($manufacturer_id) : array();

		if (!$manufacturer_info) {
			$this->session->data['warning'] = $this->language->get('text_error');
			return $this->forward('error/notfound');
			// $this->redirect($this->url->link('error/not_found', '', 'SSL'));
		}

		$heading_title = $manufacturer_info['name'];
		$meta_description = !empty($manufacturer_info['meta_description']) ? $manufacturer_info['meta_description'] : sprintf($this->language->get('meta_description_manufacturer'), $manufacturer_info['name']);
		$meta_keyword= !empty($manufacturer_info['meta_keyword']) ? $manufacturer_info['meta_keyword'] : sprintf($this->language->get('meta_keyword_manufacturer'), $manufacturer_info['name']);

		$url = $this->getQueryString();

		$this->addBreadcrumb($manufacturer_info['name'], $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id']));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['heading_title'] = $manufacturer_info['name'];

		$image = $this->model_tool_image->resize($manufacturer_info['image'], 188, 188, 'fw');

		$this->data['brand'] = array(
			'name'        => $manufacturer_info['name'],
			'image'       => $image,
			'href'        => $manufacturer_info['url'],
			'description' => html_entity_decode($manufacturer_info['description'], ENT_QUOTES, 'UTF-8')
		);

		// Listings
		$this->data['products'] = array();

		// location filter disabled for brand listings
		$data = array(
			'filter_manufacturer_id' 	=> $manufacturer_id,
			'filter_name'         		=> $search,
			'filter_filter'          	=> $filter,
			'filter_category_id'     	=> $filter_category_id,
			'filter_sub_category'    	=> true,
			// 'filter_location'    	  => $filter_location,
			// 'filter_country_id'  	  => $filter_country_id,
			// 'filter_zone_id'     	  => $filter_zone_id,
			'filter_listing_type'    	=> $filter_listing_type,
			'sort'                   	=> $sort,
			'order'              		=> $order,
			'start'              		=> ($page - 1) * $limit,
			'limit'              		=> $limit
		);

		$products = $this->model_catalog_product->getProductsIndexes($data);

		$product_total = count($products);

		$max_pages = $limit > 0 && $products ? ceil($product_total / $limit) : 1;

		if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
			$this->redirect($this->url->link('error/not_found', '', 'SSL'));
		}

		$this->data['products'] = $this->getChild('product/data/list', $this->model_catalog_product->getProducts($data));

		$this->data['refine'] = $this->getChild('module/refine', array(
			'query_params' => $query_params,
			'route' => 'product/manufacturer/info',
			'path' => 'manufacturer_id=' . $manufacturer_id,
			'filter' => $data,
			'products' => $products,
			'product_categories' => $manufacturer_info['product_categories'],
			'display_more_options' => $display_more_options,
			'forsale' => $forsale
		));

		// All Brands
		$this->data['manufacturers_active'] = $this->cache->get('manufacturer.active.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($this->data['manufacturers_active'] === false) {
			$this->data['manufacturers_active'] = array();

			$this->data['manufacturers_active'][0] = array(
				'id'	=> 0,
				'name'	=> $this->language->get('text_manufacturer_all'),
				'href'	=> $this->url->link('product/manufacturer', '', 'SSL')
			);

			$manufacturers = $this->model_catalog_manufacturer->getManufacturers(array(), false); // do not cache in model

			foreach ($manufacturers as $manufacturer) {
				if ($manufacturer['product_count']) {
					$this->data['manufacturers_active'][$manufacturer['manufacturer_id']] = array(
						'id'	=> $manufacturer['manufacturer_id'],
						'name'  => $manufacturer['name'],
						'href'  => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $manufacturer['manufacturer_id'], 'SSL')
					);
				}
			}

			$this->cache->set('manufacturer.active.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data['manufacturers_active'], 60 * 60 * 24); // 1 day cache expiration
		}

		$url = $this->getQueryString(array('page'));

		$this->data['pagination'] = $this->getPagination($product_total, $page, $limit, 'product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'], $url);

		// meta
		$image_info = $this->model_tool_image->getFileInfo($image);

		if ($page > 1) {
			$heading_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
			$meta_description = strip_tags_decode(substr($this->data['pagination'], strpos($this->data['pagination'], '<div class="results'))) . ' - ' . $meta_description;
			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
		}

		$this->document->setTitle($heading_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);
		$this->document->setUrl($this->url->link('product/manufacturer/info','manufacturer_id=' . $this->request->get['manufacturer_id'], 'SSL'));

		if ($image_info) {
			$this->document->setImage($image, $image_info['mime'], $image_info[0], $image_info[1]);
		}

		$this->data['manufacturer_id'] = $manufacturer_id;
		$this->data['url'] = $url;

		$this->data['action'] = str_replace('&amp;', '&', $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url, 'SSL'));
		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('product/manufacturer');
		$this->data['search'] = $this->url->link('product/search', '', 'SSL');
		$this->data['reset'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'], 'SSL') . $jumpTo;
		$this->data['continue'] = $this->url->link('common/home', '', 'SSL');
		$this->data['more'] = $page < $max_pages ? $this->url->link('ajax/product/more', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url . '&page=' . ($page + 1), 'SSL') : '';

		$this->model_catalog_manufacturer->updateViewed($this->request->get['manufacturer_id']);

		$this->template = '/template/product/manufacturer_info.tpl';

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
