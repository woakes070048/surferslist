<?php
class ControllerEmbedProfile extends Controller {
	public function index() {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/member'),
			$this->load->language('embed/profile')
		);

		if ($this->config->get('member_status') && $this->config->get('member_member_pages') && isset($this->request->get['profile_id']) && (int)$this->request->get['profile_id'] > 0) {
			$this->load->model('catalog/member');
			$member_customer_id = (int)$this->request->get['profile_id'];
			$member_account_id = $this->model_catalog_member->getMemberIdByCustomerId($member_customer_id);
			$member_info = $this->model_catalog_member->getMember($member_account_id);
		}

		$this->data['products'] = array();

		$server = $this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url');

		$this->data['config_url'] = $server;
		$this->data['config_name'] = $this->config->get('config_name');

		if (empty($member_info)) {
			return $this->profileNotFound();
		}

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');

		$config_product_count = true; // $this->config->get('config_product_count');

		$this->data['config_email'] = $this->config->get('config_email');
		$this->data['config_icon'] = $this->config->get('config_icon') ? $server . 'image/' . $this->config->get('config_icon') : '';
		$this->data['text_powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));

		if (CDN_SERVER != $server) {
			$this->data['logo'] = CDN_SERVER . 'image/data/logo/logo-140x60.png';
		} else if (file_exists(DIR_IMAGE . 'data/logo/logo-140x60.png')) {
			$this->data['logo'] = $server . 'image/data/logo/logo-140x60.png';
		} else if ($this->config->get('config_logo')) {
			$this->load->model('tool/image');
			$this->data['logo'] = $this->model_tool_image->resize($this->config->get('config_logo'), 140, 60, "");
		} else {
			$this->data['logo'] = '';
		}

		$display_more_options = false;

		if (isset($this->request->get['type'])) {
			$type_selected = explode(',', $this->request->get['type']);
			$display_more_options = true;
		} else {
			$type_selected = array(); // array('-1', '0', '1');
		}

		if (isset($this->request->get['filter_country_id'])) {
			$filter_country_id = $this->request->get['filter_country_id'];
		} elseif (isset($this->session->data['shipping_country_id'])) {
			$filter_country_id = $this->session->data['shipping_country_id'];
		} else {
			$filter_country_id = $this->config->get('config_country_id');
		}

		if (isset($this->request->get['filter_zone_id'])) {
			$filter_zone_id = $this->request->get['filter_zone_id'];
		} elseif (isset($this->session->data['shipping_zone_id'])) {
			$filter_zone_id = $this->session->data['shipping_zone_id'];
		} else {
			$filter_zone_id = '';
		}

		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'p.date_added';
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : (($sort == 'p.date_added') ? 'DESC' : 'ASC');
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 15; // $this->config->get('config_catalog_limit');

		// config query params (e.g. '&showheader=true&hidefooter=true&nosidebar=true&nobackground=true')
		$embed_settings = array();

		if (!empty($member_info['member_custom_field_06'])) {
			parse_str(html_entity_decode($member_info['member_custom_field_06'], ENT_QUOTES, 'UTF-8'), $embed_settings);
		}

		$query_params = array(
			'tag',
			'search',
			'filter',
			'filter_category_id',
			'filter_manufacturer_id',
			'filter_location',
			'filter_country_id',
			'filter_zone_id',
			'type',
			'sort',
			'order',
			'limit'
		);

		$query_params_bool = array(
			'showheader',
			'hidefooter',
			'nosidebar',
			'nobackground',
			'customcolor'
		);

		$query_params_color = array(
			'color_primary',
			'color_secondary',
			'color_featured',
			'color_special'
		);

		foreach ($query_params_bool as $query_param) {
			if (isset($this->request->get[$query_param])) {
				${$query_param} = $this->request->get[$query_param] == 'true' ? true : false;
			} else if (isset($embed_settings[$query_param])) {
				${$query_param} = $embed_settings[$query_param] == 'true' ? true : false;
			} else {
				${$query_param} = false;
			}
		}

		$query_params_empty = array_merge($query_params, $query_params_color);

		foreach ($query_params_empty as $query_param) {
			if (!isset(${$query_param})) {
				${$query_param} = isset($this->request->get[$query_param]) ? $this->request->get[$query_param] : '';
			}
		}

		$this->setQueryParams(array_merge($query_params, $query_params_bool, $query_params_color));

		$heading_title = $member_info['member_account_name'];
		$page_title = sprintf($this->language->get('heading_title'), $member_info['member_account_name']);
		$meta_description = sprintf($this->language->get('meta_description_profile'), $this->config->get('config_name'), $member_info['member_account_name']);
		$meta_keyword = sprintf($this->language->get('meta_keyword_profile'), $this->config->get('config_name'), $member_info['member_account_name']);
		$meta_keyword .= !empty($member_info['member_tag']) ? ', ' . $member_info['member_tag'] : '';

		// Breadcrumb
		$url = $this->getQueryParams($query_params);

		$this->addBreadcrumb($member_info['member_account_name'] . '\'s ' . $this->language->get('text_all_products'), $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id']));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();
		$this->data['heading_title'] = $heading_title;

		$this->data['profile_name'] = $member_info['member_account_name'];
		$this->data['profile_embed_url'] = $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . $url);
		$this->data['button_quickview'] = $this->language->get('button_quickview');
		$this->data['contact_member'] =  $this->url->link('information/contact', 'contact_id=' . $member_customer_id);

		// Listings
		$data = array(
			'filter_tag'			 	=> $tag,
			'filter_name'			 	=> $search,
			'filter_member_account_id'	=> $member_account_id,
			'filter_filter'          	=> $filter,
			'filter_category_id'     	=> $filter_category_id,
			'filter_manufacturer_id' 	=> $filter_manufacturer_id,
			'filter_listing_type'    	=> $type_selected,
			'sort'                   	=> $sort,
			'order'                  	=> $order,
			'start'                  	=> ($page - 1) * $limit,
			'limit'                  	=> $limit
		);

		$products = $this->model_catalog_product->getProductsIndexes($data);

		$product_total = count($products);

		$max_pages = $limit > 0 && $product_total ? ceil($product_total / $limit) : 1;

		if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
			return $this->profileNotFound();
		}

		$results = $this->model_catalog_product->getProducts($data, false); // don't cache in model

		foreach ($results as $result) {
			// adds to $this->data['products']
			require(DIR_APPLICATION . 'controller/embed/listing_result.inc.php');
		}

		// Category Filters
		$this->data['categories'] = array();

		if ($member_info['product_categories']) {
			$url = $this->getQueryParams(array('filter_category_id'));

			$this->data['categories'][] = array(
				'id'	=> 0,
				'name'	=> $this->language->get('text_category_all'),
				'href'	=> $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&filter_category_id=0' . $url)
			);

			foreach ($member_info['product_categories'] as $category) {
				if (utf8_strpos($category['name'], $this->language->get('heading_more')) !== false) {
					$category_name = $this->language->get('heading_more');
				} else if (utf8_strpos($category['name'], $this->language->get('heading_other')) !== false) {
					$category_name = $this->language->get('heading_other');
				} else {
					$category_name = $category['name'];
				}

				switch (substr_count($category['path'], '_')) {
					case 1:
						$category_name = '&emsp;' . ucwords($category_name);
						break;
					case 2:
						$category_name = '&emsp;' . '&emsp;' . ucwords($category_name);
						break;
					case 0:
					default:
						$category_name = utf8_strtoupper($category_name);
						break;
				}

				if ($category['product_count']) {
					$this->data['categories'][] = array(
						'id'	=> $category['category_id'],
						'name'  => $category_name . ($config_product_count ? sprintf($this->language->get('text_product_count'), $category['product_count']) : ''),
						'product_count' => $config_product_count ? $category['product_count'] : null,
						'href'  => $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&filter_category_id=' . $category['category_id'] . $url)
					);
				}
			}
		}

		// Manufacturer Filters
		$this->data['manufacturers'] = array();

		if ($member_info['product_manufacturers']) {
			$url = $this->getQueryParams(array('filter_manufacturer_id'));

			$this->data['manufacturers'][] = array(
				'id'	=> 0,
				'name'	=> $this->language->get('text_manufacturer_all'),
				'href'	=> $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&filter_manufacturer_id=0' . $url)
			);

			foreach ($member_info['product_manufacturers'] as $manufacturer) {
				if ($this->config->get('config_product_count')) {
					$data = array(
						'filter_member_account_id' 	=> $member_account_id,
						'filter_manufacturer_id'  	=> $manufacturer['manufacturer_id'],
						'filter_filter'           	=> $filter,
						'filter_sub_category'     	=> true,
						'filter_category_id'      	=> $filter_category_id
					);

					$product_total_manufacturer = $this->model_catalog_product->getTotalProducts($data);
				}

				if ($manufacturer['product_count']) {
					$this->data['manufacturers'][] = array(
						'id'	=> $manufacturer['manufacturer_id'],
						'name'  => $manufacturer['name'] . ($config_product_count ? sprintf($this->language->get('text_product_count'), $manufacturer['product_count']) : ''),
						'href'  => $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&filter_manufacturer_id=' . $manufacturer['manufacturer_id'] . $url)
					);
				}
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

		$this->data['type_selected'] = $type_selected;

		// Filter Groups
		$url = $this->getQueryParams(array('filter', 'type'));

		$this->data['heading_filter'] = $this->language->get('heading_filter');

		$this->data['action'] = str_replace('&amp;', '&', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . $url));

		if (isset($this->request->get['filter'])) {
			$this->data['filter_category'] = explode(',', $this->request->get['filter']);
			$display_more_options = true;
		} else {
			$this->data['filter_category'] = array();
		}

		$this->data['display_more_options'] = $display_more_options;

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

		$this->addSort($this->language->get('text_default'), 'p.sort_order-ASC', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=p.sort_order&order=ASC' . $url));
		$this->addSort($this->language->get('text_name_asc'), 'pd.name-ASC', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=pd.name&order=ASC' . $url));
		$this->addSort($this->language->get('text_name_desc'), 'pd.name-DESC', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=pd.name&order=DESC' . $url));
		$this->addSort($this->language->get('text_date_asc'), 'p.date_added-ASC', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=p.date_added&order=ASC' . $url));
		$this->addSort($this->language->get('text_date_desc'), 'p.date_added-DESC', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=p.date_added&order=DESC' . $url));
		$this->addSort($this->language->get('text_price_asc'), 'p.price-ASC', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=p.price&order=ASC' . $url));
		$this->addSort($this->language->get('text_price_desc'), 'p.price-DESC', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=p.price&order=DESC' . $url));
		$this->addSort($this->language->get('text_model_asc'), 'p.model-ASC', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=p.model&order=ASC' . $url));
		$this->addSort($this->language->get('text_model_desc'), 'p.model-DESC', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=p.model&order=DESC' . $url));
		$this->addSort($this->language->get('text_random'), 'random-' . $order, $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=random' . $url));

		$this->data['sorts'] = $this->getSorts();
		$this->data['limits'] = $this->getLimits('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . $this->getQueryParams(array('limit')));
		$this->data['random'] = $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . '&sort=random' . $url);

		$url = $this->getQueryParams(array('page'));

		$this->data['pagination'] = $this->getPagination($product_total, $page, $limit, 'embed/profile', 'profile_id=' . $this->request->get['profile_id'], $url);

		if ($page > 1) {
			$page_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
			$meta_description = strip_tags_decode(substr($this->data['pagination'], strpos($this->data['pagination'], '<div class="results'))) . ' - ' . $meta_description;
			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
		}

		$this->document->setTitle($page_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);

		$this->data['showheader'] = $showheader;
		$this->data['hidefooter'] = $hidefooter;
		$this->data['nosidebar'] = $nosidebar;
		$this->data['nobackground'] = $nobackground;
		$this->data['customcolor'] = $customcolor;

		$this->data['tag'] = $tag;
		$this->data['search_term'] = $search;
		$this->data['filter'] = $filter;
		$this->data['filter_category_id'] = $filter_category_id;
		$this->data['filter_manufacturer_id'] = $filter_manufacturer_id;
		$this->data['filter_location'] = $filter_location;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
		$this->data['profile_id'] = (int)$this->request->get['profile_id'];

		$this->model_catalog_member->updateViewed($member_account_id);

		$this->template = '/template/embed/profile.tpl';

		$this->children = array(
			// 'module/language',
			// 'module/currency',
			// 'module/cart'
			'embed/header',
			'embed/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function profileNotFound() {
		$this->document->setTitle($this->language->get('text_error'));

		$this->addBreadcrumb($this->language->get('text_error'), $this->url->link('embed/not_found'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();
		$this->data['heading_title'] = $this->language->get('text_error');
		$this->data['text_error'] = $this->language->get('text_error');

		$this->template = '/template/embed/not_found.tpl';

		$this->children = array(
			'embed/header',
			'embed/footer'
		);

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		$this->response->setOutput($this->render());
	}

}
?>
