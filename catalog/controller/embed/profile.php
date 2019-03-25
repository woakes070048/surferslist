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
		$url = $this->getQueryString($query_params);

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

		$this->data['products'] = $this->getChild('product/data/embed', $this->model_catalog_product->getProducts($data, false));

		$this->data['refine'] = $this->getChild('module/refine', array(
			'query_params' => $query_params,
			'route' => 'embed/profile',
			'path' => 'profile_id=' . $this->request->get['profile_id'],
			'filter' => $data,
			'products' => $products,
			'product_categories' => $member_info['product_categories'],
			'product_manufacturers' => $member_info['product_manufacturers'],
			'display_more_options' => $display_more_options,
		));

		$url = $this->getQueryString(array('page'));

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

		$this->data['action'] = str_replace('&amp;', '&', $this->url->link('embed/profile', 'profile_id=' . $this->request->get['profile_id'] . $url));

		$this->data['profile_id'] = (int)$this->request->get['profile_id'];

		$this->model_catalog_member->updateViewed($member_account_id);

		$this->template = 'template/embed/profile.tpl';

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

		$this->template = 'template/embed/not_found.tpl';

		$this->children = array(
			'embed/header',
			'embed/footer'
		);

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		$this->response->setOutput($this->render());
	}

}
?>
