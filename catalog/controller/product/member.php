<?php
class ControllerProductMember extends Controller {
	use Admin;

	public function index() {
		if (!$this->config->get('member_status') || !$this->config->get('member_member_pages')) {
			$this->session->data['warning'] = $this->language->get('error_membership');
			$this->redirect($this->url->link('common/home'));
		}

		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/member')
		);

		$this->load->model('catalog/member');
		$this->load->model('catalog/product');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$this->load->model('account/customer_group');
		$this->load->model('tool/image');

		$config_product_count = false; // $this->config->get('config_product_count');

		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} else {
			$tag = '';
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
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

		if (isset($this->request->get['filter_member']) && !is_array($this->request->get['filter_member'])) {
			$filter_member_group = explode(',', $this->request->get['filter_member']);
		} else {
			$filter_member_group = array(); // array('1', '2', '3');
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} elseif ($this->config->get('member_members_list_sort')) {
			$sort = 'product_count';
		} else {
			$sort = 'name';
		}

		$order = isset($this->request->get['order']) ? $this->request->get['order'] : (($sort == 'date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = (isset($this->request->get['limit']) && $this->request->get['limit'] <= $this->config->get('config_catalog_limit') * 4) ? (int)$this->request->get['limit'] : $this->config->get('config_catalog_limit');

		$this->setQueryParams(array(
			'tag',
			'filter_name',
			'filter_location',
			'filter_country_id',
			'filter_zone_id',
			'filter_member',
			'sort',
			'order',
			'limit'
		));

		// get Members List info from Member Module config settings
		$language_id = (int)$this->config->get('config_language_id');
		$members_list_description = $this->config->get('member_members_list_description');

		$members_list_info = array(
			'name'					=> $members_list_description[$language_id]['name'],
			'description'			=> $members_list_description[$language_id]['description'],
			'meta_description'		=> $members_list_description[$language_id]['meta_description'],
			'meta_keyword'			=> $members_list_description[$language_id]['meta_keyword'],
			'image'					=> $this->config->get('member_members_list_image'),
			'keyword'				=> $this->config->get('member_members_list_keyword')
		);

		$heading_title = !empty($members_list_info['name']) ? $members_list_info['name'] : $this->language->get('heading_title');
		$meta_description = !empty($members_list_info['meta_description']) ? $members_list_info['meta_description'] : $this->language->get('meta_keyword');
		$meta_keyword= !empty($members_list_info['meta_keyword']) ? $members_list_info['meta_keyword'] : $this->language->get('meta_keyword');

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_member'), $this->url->link('product/member'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['thumb'] = !empty($members_list_info['image']) ? $this->model_tool_image->resize($members_list_info['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height')) : false;
		$this->data['description'] = !empty($members_list_info['description']) ? html_entity_decode($members_list_info['description'], ENT_QUOTES, 'UTF-8') : false;

		$display_custom_fields = false; // $this->config->get('member_display_custom_fields')
		$this->data['custom_fields'] = $display_custom_fields;

		$this->data['entry_member_custom_field_01'] = $this->config->get('member_custom_field_01');
		$this->data['entry_member_custom_field_02'] = $this->config->get('member_custom_field_02');
		$this->data['entry_member_custom_field_03'] = $this->config->get('member_custom_field_03');
		$this->data['entry_member_custom_field_04'] = $this->config->get('member_custom_field_04');
		$this->data['entry_member_custom_field_05'] = $this->config->get('member_custom_field_05');
		$this->data['entry_member_custom_field_06'] = $this->config->get('member_custom_field_06');

		$this->data['members'] = array();

		$url = $this->getQueryString();

		$alphabet = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

		foreach ($alphabet as $letter) {
			$key = utf8_strtoupper($letter);

			$this->data['keys'][] = array(
				'name' => $key,
				'href' => $this->url->link('product/member',
				'filter_member_account_name=' . $letter . $url)
			);
		}

		// do not include filter_location; country and state/region filters only on profile page
		$data = array(
			'filter_member_account_name' => $filter_name,
			'filter_member_group' => $filter_member_group,
			'filter_tag'         => $tag,
			// 'filter_location'    => $filter_location,
			'filter_country_id'  => $filter_country_id,
			'filter_zone_id'     => $filter_zone_id,
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit
		);

		$members_total = $this->model_catalog_member->getTotalMembers($data);

		$max_pages = $limit > 0 ? ceil($members_total / $limit) : 1;

		if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
			$this->redirect($this->url->link('error/not_found'));
		}

		$results = $this->model_catalog_member->getMembers($data);

		foreach ($results as $result) {
			// include all members for now
			// if ($config_product_count && $result['product_count'] >= 0) {
				$first_char = utf8_substr($result['member_account_name'], 0, 1);

				$image = $this->model_tool_image->resize($result['member_account_image'], 306, 306, 'autocrop');

				$rating = $this->config->get('config_review_status') ? round($result['rating']) : false;

				$this->data['members'][] = array(
					'member_account_id'			=> $result['member_account_id'],
					'customer_id'  				=> $result['customer_id'],
					'key'                		=> is_numeric($first_char) ? '0 - 9' : utf8_strtoupper($first_char),
					'name'               		=> $result['member_account_name'],
					'image'              		=> $image,
					'date_added'         		=> date($this->language->get('date_format_long'), strtotime($result['date_added'])),
					'sort_order'         		=> $result['sort_order'],
					'text_products' 			=> false, // $config_product_count ? sprintf($this->language->get('text_products'), (int)$result['product_count']) : false,
					'member_custom_field_01' 	=> $display_custom_fields && isset($result['member_custom_field_01']) ? $result['member_custom_field_01'] : '',
					'member_custom_field_02' 	=> $display_custom_fields && isset($result['member_custom_field_02']) ? $result['member_custom_field_02'] : '',
					'member_custom_field_03' 	=> $display_custom_fields && isset($result['member_custom_field_03']) ? $result['member_custom_field_03'] : '',
					'member_custom_field_04' 	=> $display_custom_fields && isset($result['member_custom_field_04']) ? $result['member_custom_field_04'] : '',
					'member_custom_field_05' 	=> $display_custom_fields && isset($result['member_custom_field_05']) ? $result['member_custom_field_05'] : '',
					'member_custom_field_06' 	=> $display_custom_fields && !empty($result['member_custom_field_06']) ? $this->url->link('embed/profile', 'profile_id=' . $result['customer_id']) : '',
					'reviews'            		=> $result['reviews'] ? (int)$result['reviews'] : 0,
					'rating'             		=> $rating,
					'help_member_rating' 		=> sprintf($this->language->get('help_member_rating'), number_format($result['rating'], 2), (int)$result['reviews']),
					'href'               		=> $this->url->link('product/member/info', 'member_id=' . $result['member_account_id'] . $url)
				);
			// }
		}

		// ksort($this->data['members']);

		/*
		$this->data['locations'] = array();

		$url = $this->getQueryString(array('filter_location'));

		$this->data['locations'][] = array(
			'text'  => $this->language->get('text_default'),
			'value' => 'All Locations - ASC',
			'href'  => $this->url->link('product/member', $url)
		);

		$locations = $this->model_catalog_member->getMemberLocations();

		sort($locations);

		foreach($locations as $value){
			$this->data['locations'][] = array(
				'text'  => $value['location'],
				'value' => $value['location'],
				'href'  => $this->url->link('product/member', 'filter_location=' . $value['location'] . $url)
			);
		}
		* */

		// Country
		$url = $this->getQueryString(array('filter_country_id'));

		$this->data['countries'][] = array(
			'id'	=> 0,
			'name'	=> $this->language->get('text_country_all'),
			'href'	=> $this->url->link('product/member', 'filter_country_id=0' . $url)
		);

		$countries = $this->model_localisation_country->getCountries();

		foreach ($countries as $country) {
			if ($config_product_count) {
				$data = array(
					'filter_country_id'  => $country['country_id']
				);

				$member_total = $this->model_catalog_member->getTotalMembers($data);
			} // disable member count

			$this->data['countries'][] = array(
				'id'	=> $country['country_id'],
				'name'  => $country['name'] . ($config_product_count ? ' (' . $member_total . ')' : ''),
				'href'  => $this->url->link('product/member', 'filter_country_id=' . $country['country_id'] . $url)
			);
		}

		// Zone
		$this->data['zones'] = array();

		$url = $this->getQueryString(array('filter_zone_id'));

		$this->data['zones'][] = array(
			'id'	=> 0,
			'name'	=> $this->language->get('text_zone_all'),
			'href'	=> $this->url->link('product/member', 'filter_zone_id=0' . $url)
		);

		if ($filter_country_id) {
			$zones = $this->model_localisation_zone->getZonesByCountryId($filter_country_id);

			foreach ($zones as $zone) {
				if ($config_product_count) {
					$data = array(
						'filter_country_id'  => $filter_country_id,
						'filter_zone_id'     => $zone['zone_id']
					);

					$member_total = $this->model_catalog_member->getTotalMembers($data);
				} // disable member count

				$this->data['zones'][] = array(
					'id'	=> $zone['zone_id'],
					'name'  => $zone['name'] . ($config_product_count ? ' (' . $member_total . ')' : ''),
					'href'  => $this->url->link('product/member', 'filter_zone_id=' . $zone['zone_id'] . $url)
				);
			}
		}

		// Member Groups
		$this->data['member_groups'] = array();

		$customer_groups = $this->model_account_customer_group->getCustomerGroups();

		foreach ($customer_groups as $customer_group) {
			$this->data['member_groups'][] = array(
				'group_id'	=> $customer_group['customer_group_id'],
				'name'		=> $customer_group['name']
			);
		}

		// Sort, Limit
		$url = $this->getQueryString(array('sort', 'order'));

		if ($this->config->get('member_members_list_sort')) {
			$this->addSort($this->language->get('text_default'), 'default-' . $order, $this->url->link('product/member', '&sort=default' . $url));
		}

		$this->addSort($this->language->get('text_name_asc'), 'name-ASC', $this->url->link('product/member', '&sort=name&order=ASC' . $url));
		$this->addSort($this->language->get('text_name_desc'), 'name-DESC', $this->url->link('product/member', '&sort=name&order=DESC' . $url));
		$this->addSort($this->language->get('text_rating'), 'rating', $this->url->link('product/member', '&sort=rating' . $url));
		$this->addSort($this->language->get('text_random'), 'random-' . $order, $this->url->link('product/member', '&sort=random' . $url));

		$this->data['sorts'] = $this->getSorts();
		$this->data['limits'] = $this->getLimits('product/member', $this->getQueryString(array('limit')));

		$url = $this->getQueryString(array('page'));

		$this->data['pagination'] = $this->getPagination($members_total, $page, $limit, 'product/member', '', $url);

		if ($page > 1) {
			$heading_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
			$meta_description = strip_tags_decode(substr($this->data['pagination'], strpos($this->data['pagination'], '<div class="results'))) . ' - ' . $meta_description;
			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
		}

		$this->document->setTitle($heading_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);

		$this->data['filter_name'] = $filter_name;
		$this->data['filter_location'] = $filter_location;
		$this->data['filter_country_id'] = $filter_country_id;
		$this->data['filter_zone_id'] = $filter_zone_id;
		$this->data['filter_member'] = $filter_member_group;
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
		$this->data['url'] = $url;

		$request_path = isset($this->request->server['REQUEST_URI']) ? parse_url(strtolower(urldecode($this->request->server['REQUEST_URI'])), PHP_URL_PATH) : '';

		$this->data['location_page'] = $this->url->link('information/location', 'redirect_path=' . urlencode(ltrim($request_path, "/")));
		$this->data['random'] = $this->url->link('product/member', '&sort=random' . $url);
		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('product/allproducts');
		$this->data['search'] = $this->url->link('product/search');
		$this->data['reset'] = $this->url->link('product/member');
		$this->data['continue'] = $this->url->link('common/home');

		if (!$this->data['members'] && (isset($this->session->data['shipping_country_id']) || isset($this->session->data['shipping_zone_id']) || isset($this->session->data['shipping_location']))) {
			// Remove Location
			$url = $this->getQueryString(array('filter_location', 'filter_country_id', 'filter_zone_id'));
			$location_remove_url = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode(ltrim($request_path . '?' . ltrim($url, "&"), "/")));
			$this->data['text_empty_members'] .= '&emsp;' . sprintf($this->language->get('text_location_remove_url'), $location_remove_url);
		}

		$this->template = 'template/product/member_list.tpl';

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
		if (!$this->config->get('member_status') || !$this->config->get('member_member_pages')) {
			$this->session->data['warning'] = $this->language->get('error_membership');
			$this->redirect($this->url->link('common/home'));
		}

		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/member')
		);

		$this->load->model('catalog/member');
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('account/customer_group');
		$this->load->model('localisation/zone');
		$this->load->model('localisation/country');
		$this->load->model('tool/image');

		$display_more_options = false;

		$member_account_id = !empty($this->request->get['member_id']) ? (int)$this->request->get['member_id'] : 0;

		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} else {
			$tag = '';
		}

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
			$filter_country_id = '';  // $this->config->get('config_country_id');
		}

		if (isset($this->request->get['filter_zone_id'])) {
			$filter_zone_id = $this->request->get['filter_zone_id'];
		} elseif (isset($this->session->data['shipping_zone_id'])) {
			$filter_zone_id = $this->session->data['shipping_zone_id'];
		} else {
			$filter_zone_id = '';
		}

		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'p.date_added'; // 'random'
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : (($sort == 'p.date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
		$limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : (int)$this->config->get('config_catalog_limit');

		$query_params = array(
			'tag',
			'filter_location',
			'filter',
			'filter_manufacturer_id',
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
		$this->addBreadcrumb($this->language->get('text_member'), $this->url->link('product/member'));

		$member_info = $member_account_id ? $this->model_catalog_member->getMember($member_account_id) : array();

		if (!$member_info) {
			$this->session->data['warning'] = $this->language->get('text_error');
			$this->redirect($this->url->link('error/not_found'));
		}

		$heading_title = $member_info['member_account_name'];
		$meta_keyword = !empty($member_info['meta_keyword']) ? $member_info['meta_keyword'] : sprintf($this->language->get('meta_keyword_profile'), $member_info['member_account_name']);

		if (!empty($member_info['meta_description'])) {
			$meta_description = $member_info['meta_description'];
		} else {
			$member_zone = $this->model_localisation_zone->getZone($member_info['member_zone_id']);
			$member_country = $this->model_localisation_country->getCountry($member_info['member_country_id']);

			$meta_description = sprintf($this->language->get('meta_description_profile'), $member_info['member_account_name'], $member_info['member_city'] . ', ' . $member_zone['name'] . ', ' . $member_country['name']);
			// $meta_description .= '; ' . $this->language->get('text_member_type') . ' ' . $member_info['member_group'];
			// $meta_description .= '; ' . $this->language->get('text_member_description') . ' ' . utf8_substr(trim(strip_tags(html_entity_decode($member_info['member_account_description'], ENT_QUOTES, 'UTF-8'))), 0, 120) . '...;';
		}

		if (!empty($member_info['member_tag'])) {
			$this->document->setKeywords($member_info['member_tag']);
		}

		$url = $this->getQueryString();

		$this->addBreadcrumb($member_info['member_account_name'], $this->url->link('product/member/info', 'member_id=' . $this->request->get['member_id']));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['heading_title'] = $member_info['member_account_name'];
		$this->data['member_name'] = $member_info['member_account_name'];
		$this->data['member_type'] = $member_info['account_group'];
		$this->data['member_date_added'] = date($this->language->get('date_format_long'), strtotime($member_info['date_added']));
		$this->data['member_page'] = $this->url->link('product/member/info', 'member_id=' . $this->request->get['member_id']);
		$this->data['member_url_alias'] = substr($this->data['member_page'], strlen($this->url->link('common/home')));

		// social links
		for ($i = 1; $i <= 6; $i++) {
			$this->data["text_member_custom_field_0{$i}"] = $this->config->get("member_custom_field_0{$i}");
			$this->data["member_custom_field_0{$i}"] = (isset($member_info["member_custom_field_0{$i}"]) ? $member_info["member_custom_field_0{$i}"] : '');
		}

		$this->data['member_custom_field_06'] = !empty($member_info['member_custom_field_06']) && !empty($member_info['customer_id']) ? $this->url->link('embed/profile', 'profile_id=' . $member_info['customer_id']) : '';

		// hashtag/keywords
		$this->data['member_tags'] = array();

		if ($member_info['member_tag']) {
			$tags = explode(',', $member_info['member_tag']);

			foreach ($tags as $tag) {
				$this->data['member_tags'][] = array(
					'tag'  => trim($tag),
					'href' => $this->url->link('product/member', 'tag=' . trim($tag))
				);
			}
		}

		if ($this->config->get('member_display_custom_fields') && ($member_info['member_custom_field_01'] || $member_info['member_custom_field_02'] || $member_info['member_custom_field_03'] || $member_info['member_custom_field_04'] || $member_info['member_custom_field_05'] || $member_info['member_custom_field_06'])) {
			$this->data['member_socials'] = true;
		} else {
			$this->data['member_socials'] = false;
		}

		if(!empty($member_info['member_account_description'])){
			$this->data['member_description'] = nl2br(convert_links(strip_tags_decode($member_info['member_account_description'])));
		} else {
			$this->data['member_description'] = '';
		}

		if(!empty($member_info['member_account_image'])) {
			$this->data['member_thumb'] = $this->model_tool_image->resize($member_info['member_account_image'], 306, 306, 'autocrop');
			$this->data['member_image'] = $this->model_tool_image->resize($member_info['member_account_image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'), 'fw');
		} else {
			$this->data['member_thumb'] = $this->model_tool_image->resize('no_image.jpg', 306, 306, 'autocrop');
			$this->data['member_image'] = '';
		}

		if(!empty($member_info['member_account_banner'])) {
			$this->data['member_banner'] = $this->model_tool_image->resize($member_info['member_account_banner'], 1200, 360, 'autocrop');
		} else {
			$this->data['member_banner'] = '';
		}

		$this->data['member_location'] = $member_info['member_city'];
		$this->data['member_location_zone'] = $member_zone['name'];
		$this->data['member_location_country'] = $member_country['iso_code_3'];
		$this->data['member_location_href'] = $this->url->link('product/search', 'country=' . $member_info['member_country_id'] . '&state=' . $member_info['member_zone_id']);

		// Listings
		$this->data['text_products'] = $member_info['product_count'] ? sprintf($this->language->get('text_products'), $member_info['product_count']) : '';

		// Discussion
		$this->data['discussion_status'] = $this->config->get('config_review_status');
		$this->data['tab_discussion'] = $this->language->get('tab_discussion'); // sprintf($this->language->get('tab_discussion'), (int)$member_info['questions']);

		// if (!$this->customer->validateLogin()) {
		// 	$this->data['discussion_unauthorized'] = $this->language->get('error_discussion_logged');
		// 	unset($this->session->data['warning']);
		// } else if (!$this->customer->validateProfile()) {
		// 	$this->data['discussion_unauthorized'] = $this->language->get('error_discussion_membership');
		// 	unset($this->session->data['warning']);
		// } else {
			// allow anyone to discuss
			$this->data['discussion_unauthorized'] = !$this->customer->isLogged() && !$this->isAdmin() ? true : false;
		// }

		// Catpcha
		$catpcha_fields = array('captcha', 'captcha_widget_id', 'g-recaptcha-response');

		foreach ($catpcha_fields as $captcha_field) {
			$this->data[$captcha_field] = isset($this->request->post[$captcha_field]) ? $this->request->post[$captcha_field] : '';
		}

		$this->data['help_discussion'] = !$this->customer->isLogged() && !$this->isAdmin() ? sprintf($this->language->get('help_unauthorized'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL')) : ''; // $this->language->get('help_discussion');

		// Reviews
		$this->data['tab_review'] = $this->language->get('tab_review'); // sprintf($this->language->get('tab_review'), (int)$member_info['reviews']);

		$this->data['help_member_rating'] = sprintf($this->language->get('help_member_rating'), number_format($member_info['rating'], 2), (int)$member_info['reviews']);

		if (!$this->customer->validateLogin()) {
			$this->data['review_unauthorized'] = $this->language->get('error_review_logged');
			unset($this->session->data['warning']);
		} else if (!$this->customer->validateProfile()) {
			$this->data['review_unauthorized'] = $this->language->get('error_review_membership');
			unset($this->session->data['warning']);
		} else if ($this->customer->getId() == $member_info['customer_id']) {
			$this->data['review_unauthorized'] = $this->language->get('error_review_self');
		/* } else if (!$this->customer->getTotalOrdersWithMember($member_info['customer_id'])) {
			$this->data['review_unauthorized'] = $this->language->get('error_review_order'); */
		} else {
			$this->data['review_unauthorized'] = '';
		}

		$this->data['review_status'] = $this->config->get('config_review_status');
		$this->data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$member_info['reviews']);
		$this->data['rating'] = (int)round($member_info['rating']);
		$this->data['profile_name'] = $member_info['member_account_name'];

		$this->data['text_empty'] = $this->language->get('text_empty_products');

		$this->data['contact_member'] = $member_info['customer_id']
			? $this->url->link('information/contact', 'contact_id=' . $member_info['customer_id'])
			: $this->url->link('information/contact', 'contact_id=0&profile_id=' . $this->request->get['member_id']) ;

		$jumpTo = '#member-listings';

		// Listings
		$this->data['products'] = array();

		// location filter disabled for profile listings
		$data = array(
			'filter_member_account_id' 	=> $member_account_id,
			'filter_name'         		=> $search,
			'filter_filter'          	=> $filter,
			'filter_category_id'     	=> $filter_category_id,
			'filter_manufacturer_id' 	=> $filter_manufacturer_id,
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

		$max_pages = $limit > 0 && $product_total ? ceil($product_total / $limit) : 1;

		if ($page <= 0 || $limit <= 0 || ($max_pages > 0 && $page > $max_pages)) {
			$this->redirect($this->url->link('error/not_found'));
		}

		$url = $this->getQueryString(array('page'));

		$pagination = $this->getPagination($product_total, $page, $limit, 'product/member/info', 'member_id=' . $this->request->get['member_id'], $url, $jumpTo);

		$this->data['products'] = $this->getChild('product/data/list', array(
			'products' => $this->model_catalog_product->getProducts($data),
			'more' => $page < $max_pages ? $this->url->link('ajax/product/more', 'member_id=' . $this->request->get['member_id'] . $url . '&page=' . ($page + 1)) : '',
			'pagination' => $pagination,
			'reset' => !empty($url) ? $this->url->link('product/member/info', 'member_id=' . $this->request->get['member_id']) . $jumpTo : false,
			'query_params' => $query_params
		));

		$this->data['refine'] = $this->getChild('module/refine', array(
			'query_params' => $query_params,
			'route' => 'product/member/info',
			'path' => 'member_id=' . $member_account_id,
			'filter' => $data,
			'products' => $products,
			'product_categories' => $member_info['product_categories'],
			'product_manufacturers' => $member_info['product_manufacturers'],
			'display_more_options' => $display_more_options,
			'forsale' => $forsale
		));

		if ($page > 1) {
			$heading_title .= ' - ' . sprintf($this->language->get('text_page_of'), $page, $max_pages);
			$meta_description = strip_tags_decode(substr($pagination, strpos($pagination, '<div class="results'))) . ' - ' . $meta_description;
			$meta_keyword .= ', ' . strtolower($this->language->get('text_page')) . ' ' . $page;
		}

		// meta
		$image_info = $this->model_tool_image->getFileInfo($this->data['member_image']);

		$this->document->setTitle($heading_title);
		$this->document->setDescription($meta_description);
		$this->document->setKeywords($meta_keyword);
		$this->document->setUrl($this->url->link('product/member/info','member_id=' . $this->request->get['member_id']));

		if ($image_info) {
			$this->document->setImage($this->data['member_image'], $image_info['mime'], $image_info[0], $image_info[1]);
		}

		$this->data['customer_id'] = $member_info['customer_id'];
		$this->data['profile_id'] = $this->request->get['member_id'];
		$this->data['action'] = str_replace('&amp;', '&', $this->url->link('product/member/info', 'member_id=' . $this->request->get['member_id'] . $url));

		$this->model_catalog_member->updateViewed($this->request->get['member_id']);

		$this->template = 'template/product/member_info.tpl';

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
