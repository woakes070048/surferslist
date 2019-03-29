<?php
class ControllerAccountMember extends Controller {
	use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/member', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if (!$this->config->get('member_status')) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}

  	public function index() {
		$this->data = $this->load->language('account/member');

		if ($this->customer->hasProfile() && !$this->customer->isProfileEnabled()) {
			$this->session->data['warning'] = $this->language->get('error_member_disabled');
			$this->session->data['redirect'] = $this->url->link('account/member', '', 'SSL');
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

		$this->load->model('account/member');
	    $this->load->model('account/customer_group');
		$this->load->model('localisation/country');
		$this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));

    	if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/member', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateForm()) {
				$data = $this->preparePostData($this->request->post);

				if (!$this->customer->hasProfile()) {
					if ($this->model_account_member->addMember($data)) {
						$this->session->data['success'] = $this->language->get('text_success_add');
					}
				} else {
					$this->model_account_member->editMember($data);
					$this->session->data['success'] = $this->language->get('text_success_edit');
				}

				$this->redirect($this->url->link('account/account', '', 'SSL'));
			}
		}

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_account_member'), $this->url->link('account/member'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['permissions'] = $this->customer->getMemberPermissions();
		$this->data['activated'] = $this->customer->hasProfile();
		$this->data['enabled'] = $this->customer->isProfileEnabled();

    	$this->data['text_profile_info'] = !$this->customer->hasProfile() ? $this->language->get('text_activate_member') : $this->language->get('text_edit_member');

		$member_custom_fields = false;

		for ($i = 1; $i <= 6; $i++) {
			$this->data["entry_member_custom_field_0{$i}"] = $this->config->get("member_custom_field_0{$i}");

			if ($this->config->get("member_custom_field_0{$i}")) {
				$member_custom_fields = true;
			}
		}

		$this->data['member_custom_fields'] = $member_custom_fields;

		// config membership image settings
        $image_upload_filesize_max = ((int)$this->config->get('member_image_upload_filesize_max') ?: 5120) * 1024; // bytes
		$image_dimensions_min_width = (int)$this->config->get('member_image_dimensions_min_width') ?: 450;
		$image_dimensions_min_height = (int)$this->config->get('member_image_dimensions_min_height') ?: 450;
		$image_dimensions_resize_width = (int)$this->config->get('member_image_dimensions_resize_width') ?: 1200;
        $image_dimensions_resize_height = (int)$this->config->get('member_image_dimensions_resize_height') ?: 1200;
        $image_dimensions_profile_width = (int)$this->config->get('member_image_dimensions_profile_width') ?: 306;
        $image_dimensions_profile_height = (int)$this->config->get('member_image_dimensions_profile_height') ?: 306;
        $image_dimensions_banner_width = (int)$this->config->get('member_image_dimensions_banner_width') ?: 1200;
        $image_dimensions_banner_height = (int)$this->config->get('member_image_dimensions_banner_height') ?: 360;

        $this->data['help_member_account_image'] = sprintf($this->language->get('help_member_account_image'), $image_dimensions_min_width, $image_dimensions_min_height, $image_upload_filesize_max / (1024 * 1024));
		$this->data['help_member_account_banner'] = sprintf($this->language->get('help_member_account_banner'), $image_dimensions_banner_width, $image_upload_filesize_max / (1024 * 1024));
        $this->data['help_member_url_alias'] = sprintf($this->language->get('help_member_url_alias'), $this->config->get('config_url'));

		$data_field_errors = array(
			'member_account_name',
			'member_account_description',
			'customer_group',
			'member_group',
			'member_paypal_account',
			'member_account_image',
			'member_account_banner',
			'embed_settings_bool',
			'embed_settings_hex',
			'member_city',
			'member_zone',
			'member_country',
			'member_custom_fields',
			'member_tag',
			'member_url_alias'
		);

		foreach ($data_field_errors as $data_field) {
			$this->data['error_' . $data_field] = $this->getError($data_field);
		}

		if ($this->customer->hasProfile()) {
			$member_info = $this->model_account_member->getMember();
		}

		$data_fields = array(
			'member_account_name',
			'member_account_description',
			'member_account_image',
			'member_account_banner',
			'member_custom_field_01',
			'member_custom_field_02',
			'member_custom_field_03',
			'member_custom_field_04',
			'member_custom_field_05',
			'member_tag',
			'member_paypal_account',
			'member_url_alias'
		);

		foreach ($data_fields as $data_field) {
			if (isset($this->request->post[$data_field])) {
				$this->data[$data_field] = $this->request->post[$data_field];
			} elseif (isset($member_info)) {
				$this->data[$data_field] = strip_tags_decode($member_info[$data_field]);
			} else {
				$this->data[$data_field] = '';
			}
		}

		// Embed Settings
		// e.g. embed-profile?profile_id=10138&showheader=false&hidefooter=false&nosidebar=false&nobackground=false&customcolor=true&color_primary=66D9EF&color_secondary=A6E22E&color_featured=FD971F&color_special=F92672

		$this->data['embed_settings'] = array();
		$embed_settings = array();

		if (isset($this->request->post['member_custom_field_06'])) {
			$embed_settings_selected = array_merge($this->request->post['embed_settings_bool'], $this->request->post['embed_settings_hex']);

			foreach ($embed_settings_selected as $key => $value) {
				$embed_settings[$key] = $value;
			}
		} elseif (!empty($member_info['member_custom_field_06'])) {
			//$embed_settings = $member_info['member_custom_field_06'] ? json_decode($member_info['member_custom_field_06'], true) : '';
			parse_str($member_info['member_custom_field_06'], $embed_settings);
		} else {
			$embed_settings = array(
				'showheader' => 'false',
				'hidefooter' => 'false',
				'nosidebar' => 'false',
				'nobackground' => 'false',
				'customcolor' => 'true',
				'color_primary' => '0d8f91',
				'color_secondary' => '76cdd8',
				'color_featured' => 'ffcc00',
				'color_special' => 'dc313e'
			);
		}

		$embed_options_bool = array(
			'showheader',
			'hidefooter',
			'nosidebar',
			'nobackground',
			'customcolor'
		);

		foreach ($embed_options_bool as $embed_option) {
			$this->data['embed_settings_bool'][] = array(
				'label' => $this->language->get("entry_embed_settings_{$embed_option}"),
				'key' => $embed_option,
				'value' => isset($embed_settings[$embed_option]) && $embed_settings[$embed_option] == 'true' ? 'true' : 'false'
			);
		}

		$embed_options_hex = array(
			'color_primary',
			'color_secondary',
			'color_featured',
			'color_special'
		);

		foreach ($embed_options_hex as $embed_option) {
			$this->data['embed_settings_hex'][] = array(
				'label' => $this->language->get("entry_embed_settings_{$embed_option}"),
				'key' => $embed_option,
				'value' => isset($embed_settings[$embed_option]) && is_hex_color($embed_settings[$embed_option]) ? utf8_strtolower($embed_settings[$embed_option]) : ''
			);
		}

	 	// $this->customer->getId()
		$embed_url = isset($member_info) ? $this->url->link('embed/profile', 'profile_id=' . $member_info['customer_id'], 'SSL') : '';
		$this->data['embed_url'] = $embed_url;
		$this->data['embed_url_querystring'] = http_build_query($embed_settings);
		$this->data['embed_iframe_code'] = htmlentities('<iframe width="980" height="740" src="' . $embed_url . '" frameborder="0" allowfullscreen></iframe>');
		$this->data['embed_code'] = md5(http_build_query($embed_settings));

		// PayPal Notice
		$this->data['text_no_paypal'] = '';
		$this->data['jump_to_paypal'] = isset($this->request->get['no_paypal']) ? true : false;

		if (empty($member_info['member_paypal_account'])) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation(14); // information_id 14 => Anatomy of a Listing

			if ($information_info) {
				$this->data['text_no_paypal'] = sprintf(
					$this->language->get('text_no_paypal'),
					$this->url->link('information/information/info', 'information_id=14', 'SSL'),
					$information_info['title'],
					$information_info['title']
				);
			}
		}

		$this->data['member_activities'] = is_array($this->config->get('config_category_tags')) ? $this->config->get('config_category_tags') : array();

		if (isset($this->request->post['member_activity'])) {
			$this->data['member_activity'] = $this->request->post['member_activity'];
		} elseif (isset($member_info) && is_array($this->config->get('config_category_tags'))) {
			$this->data['member_activity'] = array_intersect(array_flip($this->config->get('config_category_tags')), array_map('trim', explode(',', $member_info['member_tag'])));
		} else {
			$this->data['member_activity'] = array();
		}

		$no_image = $this->model_tool_image->resize('no_image.jpg', $image_dimensions_profile_width, $image_dimensions_profile_height, 'autocrop');
		$no_banner = $this->model_tool_image->resize('no_image.jpg', $image_dimensions_resize_width, $image_dimensions_banner_height, 'autocrop');

		if (!empty($this->request->post['member_account_image'])) {
			$this->data['member_account_image_thumb'] = $this->model_tool_image->resize($this->request->post['member_account_image'], $image_dimensions_profile_width, $image_dimensions_profile_height, 'autocrop');
		} elseif (isset($member_info) && !empty($member_info['member_account_image'])) {
			$this->data['member_account_image_thumb'] = $this->model_tool_image->resize($member_info['member_account_image'], $image_dimensions_profile_width, $image_dimensions_profile_height, 'autocrop');
		} else {
			$this->data['member_account_image_thumb'] = $no_image;
		}

		if (!empty($this->request->post['member_account_banner'])) {
			$this->data['member_account_banner_thumb'] = $this->model_tool_image->resize($this->request->post['member_account_banner'], $image_dimensions_resize_width, $image_dimensions_banner_height, 'autocrop');
		} elseif (isset($member_info) && !empty($member_info['member_account_banner'])) {
			$this->data['member_account_banner_thumb'] = $this->model_tool_image->resize($member_info['member_account_banner'], $image_dimensions_resize_width, $image_dimensions_banner_height, 'autocrop');
		} else {
			$this->data['member_account_banner_thumb'] = $this->model_tool_image->resize('no_image.jpg', $image_dimensions_resize_width, $image_dimensions_banner_height);
		}

		$this->data['no_image'] = $no_image;
		$this->data['no_banner'] = $no_banner;

		// Customer and Member Groups
		$this->data['customer_groups'] = $this->model_account_customer_group->getCustomerGroups();

		if (isset($member_info)) {
			$this->data['customer_group_id'] = $this->customer->getCustomerGroupId(); // $member_info['customer_group_id'];
		} else if (isset($this->request->post['customer_group_id'])) {
			$this->data['customer_group_id'] = (int)$this->request->post['customer_group_id'];
		} /*elseif ($this->config->get('member_customer_group')) {
			$this->data['customer_group_id'] = $this->config->get('member_customer_group');   // default membership setting value
		} elseif ($this->config->get('config_customer_group_id')) {
			$this->data['customer_group_id'] = $this->config->get('config_customer_group_id'); // default customer group
		} */ else {
			$this->data['customer_group_id'] = 0;
		}

		$data = array('filter_status' => 1); // show only enabled member groups

		$member_groups = $this->model_account_customer_group->getCustomerMemberGroups(0, $data); // args($customer_group_id, $data['filter_status'])

		// member group images
		/*
		foreach ($member_groups as $key => $member_group) {
			if ($member_group['member_group_image']) {
				$member_groups[$key]['thumb'] = $this->model_tool_image->resize($member_group['member_group_image'], 100, 100);
			} else {
				$member_groups[$key]['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
			}
		}
		*/

		$this->data['member_groups'] = array();

		foreach ($member_groups as $member_group) {
			$this->data['member_groups'][] = array(
				'member_group_id' => $member_group['member_group_id'],
				'member_group_name' => $member_group['member_group_name']
			);
		}

		if (isset($this->request->post['member_group_id'])) {
			$this->data['member_group_id'] = (int)$this->request->post['member_group_id'];
		} elseif (isset($member_info)) {
			$this->data['member_group_id'] = $member_info['member_group_id'];
		} else {
			$this->data['member_group_id'] = $this->model_account_customer_group->getDefaultMemberGroupId($this->customer->getCustomerGroupId());
		}

		if ($this->customer->getAddressId()) {
			$default_address_id = $this->customer->getAddressId();
		} else if (isset($this->session->data['shipping_address_id'])) {
			$default_address_id = $this->session->data['shipping_address_id'];
		} else if (isset($this->session->data['payment_address_id'])) {
			$default_address_id = $this->session->data['payment_address_id'];
		} else {
			$default_address_id = '';
		}

		if ($default_address_id) {
			$this->load->model('account/address');
			$default_address = $this->model_account_address->getAddress($default_address_id);
		}

    	if (isset($this->request->post['member_zone_id'])) {
      		$this->data['member_zone_id'] = (int)$this->request->post['member_zone_id'];
		} elseif (isset($member_info)) {
			$this->data['member_zone_id'] = $member_info['member_zone_id'];
		} elseif (!empty($default_address)) {
			$this->data['member_zone_id'] = $default_address['zone_id'];
		} elseif (isset($this->session->data['shipping_zone_id'])) {
			$this->data['member_zone_id'] = $this->session->data['shipping_zone_id'];
		} else {
      		$this->data['member_zone_id'] = '';
    	}

    	if (isset($this->request->post['member_country_id'])) {
      		$this->data['member_country_id'] = (int)$this->request->post['member_country_id'];
		} elseif (isset($member_info)) {
			$this->data['member_country_id'] = $member_info['member_country_id'];
		} elseif (!empty($default_address)) {
			$this->data['member_country_id'] = $default_address['country_id'];
		} elseif (isset($this->session->data['shipping_country_id'])) {
			$this->data['member_country_id'] = $this->session->data['shipping_country_id'];
		} else {
      		$this->data['member_country_id'] = ''; // $this->config->get('config_country_id');
    	}

		if (isset($this->request->post['member_city'])) {
			$this->data['member_city'] = $this->request->post['member_city'];
		} elseif (isset($member_info)) {
			$this->data['member_city'] = strip_tags_decode($member_info['member_city']);
		} elseif (!empty($default_address)) {
			$this->data['member_city'] = $default_address['city'];
		} elseif (isset($this->session->data['shipping_location'])) {
			$this->data['member_city'] = $this->session->data['shipping_location'];
		} else {
			$this->data['member_city'] = '';
		}

    	$this->data['countries'] = $this->model_localisation_country->getCountries();

		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		$this->data['action'] = $this->url->link('account/member', 'customer_token=' . $this->session->data['customer_token'], 'SSL');
		$this->data['help'] = $this->url->link('information/information/info', 'information_id=13', 'SSL'); // information_id=13, Member Profile

		$this->session->data['warning'] = $this->getError('warning');

		$this->document->addStyle('catalog/view/root/ui/jquery-ui.min.css');
		$this->document->addScript('catalog/view/root/ui/jquery-ui.min.js');
		$this->document->addScript('catalog/view/root/javascript/ajaxupload.js');
		$this->document->addScript('catalog/view/root/javascript/account.js');

		if ($this->customer->getMemberPermission('banner_enabled')) {
			$this->document->addStyle('catalog/view/root/colorpicker/spectrum.css');
			$this->document->addScript('catalog/view/root/colorpicker/spectrum.js');
		}

		$this->template = 'template/account/member.tpl';

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

  	private function validateForm() {
		if (!isset($this->request->post['member_account_name']) || !$this->validateStringLength($this->request->post['member_account_name'], 2, 128) || !preg_match('/^[a-zA-Z][a-zA-Z0-9- ]*$/', $this->request->post['member_account_name'])) {
			$this->setError('member_account_name', sprintf($this->language->get('error_member_account_name'), 2, 128));
	    } else {
			$member_id_exists = $this->model_account_member->getMemberIdByName($this->request->post['member_account_name'], $this->request->post['member_zone_id'], $this->request->post['member_country_id']);

			if ((!$this->customer->hasProfile() && $member_id_exists) || ($member_id_exists && $member_id_exists != $this->customer->getProfileId())) {
				$this->setError('member_account_name', $this->language->get('error_exists_name'));
			}

			if (is_array($this->config->get('config_invalid_keywords'))) {
				foreach ($this->config->get('config_invalid_keywords') as $invalid_word) {
					if (stripos(utf8_strtolower($this->request->post['member_account_name']), $invalid_word) !== false) {
						$this->setError('member_account_name', sprintf($this->language->get('error_member_account_name_reserved'), $invalid_word));
					}
				}
			}

			if (is_array($this->config->get('config_invalid_keywords_start'))) {
				foreach ($this->config->get('config_invalid_keywords_start') as $invalid_start) {
					if (substr(utf8_strtolower($this->request->post['member_account_name']), 0, utf8_strlen($invalid_start)) == $invalid_start) {
						$this->setError('member_account_name', sprintf($this->language->get('error_member_account_name_start'), $invalid_start));
					}
				}
			}
		}

		if (!empty($this->request->post['member_account_description']) && !$this->validateStringLength($this->request->post['member_account_description'], 0, 5000)) {
			$this->setError('member_account_description', sprintf($this->language->get('error_member_account_description'), 5000));
	    }

    	if ($this->request->post['customer_group_id'] == '') {
      		$this->setError('customer_group', $this->language->get('error_customer_group'));
    	} else if ($this->customer->hasProfile() && $this->request->post['customer_group_id'] != $this->customer->getCustomerGroupId()) {
      		$this->setError('customer_group', sprintf($this->language->get('error_customer_group_approval'), $this->url->link('information/contact', 'contact_id=0', 'SSL'), $this->config->get('config_name')));
		}

		// if the membership group selected is different from the currently assigned value AND not an element of a default customer/account group, then the change must be made by an admin
		// if ($customer_group_id != $this->customer->getCustomerGroupId() && ($customer_group_id != $this->config->get('member_customer_group') || $customer_group_id != $this->config->get('config_customer_group_id'))) {
		// if the membership group selected is different from the currently assigned value AND from the default, then the change must be made by an admin
		/*
		$customer_group_id = $this->model_account_customer_group->getCustomerGroupIdByMemberGroupId($this->request->post['member_group_id']);
		$member_group_default_id = $this->model_account_customer_group->getDefaultMemberGroupId($customer_group_id);

		if ($this->request->post['member_group_id'] != $this->customer->getMemberGroupId() && $this->request->post['member_group_id'] != $member_group_default_id) {
			$this->setError('member_group', sprintf($this->language->get('error_member_group_approval'), $this->url->link('information/contact', 'contact_id=0', 'SSL'), $this->config->get('config_name')));
		}
		*/

		if (!empty($this->request->post['member_paypal_account'])) {
	      	if (!$this->validateEmail($this->request->post['member_paypal_account'])) {
	      		$this->setError('member_paypal_account', $this->language->get('error_member_paypal_account'));
			}

			if (!$this->config->get('member_member_paypal')) {
	      		$this->setError('member_paypal_account', $this->language->get('error_member_paypal_account_disabled'));
			}
	    } else if ($this->config->get('member_member_paypal_require')) {
			$this->setError('member_paypal_account', $this->language->get('error_member_paypal_account_required'));
		}

		if (!isset($this->request->post['member_city']) || !$this->validateStringLength($this->request->post['member_city'], 2, 128)) {
      		$this->setError('member_city', sprintf($this->language->get('error_city'), 2, 128));
    	}

    	if (!isset($this->request->post['member_zone_id']) || $this->request->post['member_zone_id'] == '') {
      		$this->setError('member_zone', $this->language->get('error_zone'));
    	}

    	if (!isset($this->request->post['member_country_id']) || $this->request->post['member_country_id'] == '') {
      		$this->setError('member_country', $this->language->get('error_country'));
    	}
		/* do not require image
		if (empty($this->request->post['member_account_image'])) {
	      	$this->setError('member_account_image', $this->language->get('error_member_account_image'));
		}
		*/
		/* do not require banner
		if (empty($this->request->post['member_account_banner'])) {
	      	$this->setError('member_account_banner', $this->language->get('error_member_account_banner'));
		}
		*/

		$member_custom_fields = array('member_custom_field_01', 'member_custom_field_02', 'member_custom_field_03', 'member_custom_field_04', 'member_custom_field_05');

		foreach ($member_custom_fields as $member_custom_field) {
			if (!empty($this->request->post[$member_custom_field])) {
				if (!$this->validateStringLength($this->request->post[$member_custom_field], 0, 128)) {
					$this->setError('member_custom_fields', sprintf($this->language->get('error_member_custom_fields_long'), 128));
				}

				if (!$this->validateUrl($this->request->post[$member_custom_field])) {
					if ($this->getError('member_custom_fields')) {
						$this->setError('member_custom_fields',  $this->getError('member_custom_fields') . '<br />' . sprintf($this->language->get('error_url'), $this->config->get($member_custom_field))); // $this->language->get('error_member_custom_fields_url');
					} else {
						$this->setError('member_custom_fields', sprintf($this->language->get('error_url'), $this->config->get($member_custom_field)));
					}
				}
		    }
		}

		// Embed Settings
		if (isset($this->request->post['member_custom_field_06'])) {
			foreach ($this->request->post['embed_settings_bool'] as $embed_settings_bool) {
				if ($embed_settings_bool != 'true' && $embed_settings_bool != 'false') {
					$this->setError('embed_settings_bool', $this->language->get('error_embed_settings_bool'));
				}
			}

			if (isset($this->request->post['embed_settings_bool']['customcolor']) && $this->request->post['embed_settings_bool']['customcolor'] == 'true') {
				foreach ($this->request->post['embed_settings_hex'] as $embed_settings_hex) {
					if (!is_hex_color($embed_settings_hex)) {
						$this->setError('embed_settings_hex', $this->language->get('error_embed_settings_hex'));
					}
				}
			}
		}

		/*
		if (filter_var($this->request->post['member_custom_field_01'], FILTER_VALIDATE_URL) === false) {
      		$this->setError('member_custom_fields', sprintf($this->language->get('error_url'), $this->config->get('member_custom_field_01')));
    	}
    	*/

    	if (isset($this->request->post['member_url_alias'])) {
			if (!$this->validateStringLength($this->request->post['member_url_alias'], 3, 128) || !preg_match('/^[a-zA-Z0-9-]+$/', $this->request->post['member_url_alias'])) {
				$this->setError('member_url_alias', sprintf($this->language->get('error_member_url_alias'), 3, 128));
			} else {
				$url_alias_query = false;

				if (is_array($this->config->get('config_route_keywords')) && in_array(utf8_strtolower($this->request->post['member_url_alias']), $this->config->get('config_route_keywords'), true)) {
					$url_alias_query = array_search(utf8_strtolower($this->request->post['member_url_alias']), $this->config->get('config_route_keywords'));
				}

				if (!$url_alias_query) {
					$this->load->model('tool/seo_url');

					$query = $this->model_tool_seo_url->getQuery($this->request->post['member_url_alias']);

					if ($query) {
						 $url_alias_query = is_array($query) ? $query[0] : $query;  // handle potential duplicates
					}
				}

				if ($url_alias_query) {
					$url_alias_exists_member_id = substr(strstr($url_alias_query, 'member_id='), 10);

					if ($url_alias_exists_member_id != $this->customer->getProfileId()) {
						$this->setError('member_url_alias', sprintf($this->language->get('error_exists_url_alias'), $this->config->get('config_url') . $this->request->post['member_url_alias'], $this->config->get('config_url') . $this->request->post['member_url_alias']));
					}
				}

				if (is_array($this->config->get('config_invalid_keywords'))) {
					foreach ($this->config->get('config_invalid_keywords') as $invalid_word) {
						if (stripos(utf8_strtolower($this->request->post['member_url_alias']), $invalid_word) !== false) {
							$this->setError('member_url_alias', sprintf($this->language->get('error_member_url_alias_reserved'), $invalid_word));
						}
					}
				}

				if (is_array($this->config->get('config_invalid_keywords_start'))) {
					foreach ($this->config->get('config_invalid_keywords_start') as $invalid_start) {
						if (substr(utf8_strtolower($this->request->post['member_url_alias']), 0, utf8_strlen($invalid_start)) == $invalid_start) {
							$this->setError('member_url_alias', sprintf($this->language->get('error_member_url_alias_start'), $invalid_start));
						}
					}
				}
			}
		}

		if ($this->hasError() && !$this->getError('warning')) {
			$this->setError('warning', $this->language->get('error_warning'));
		}

    	return !$this->hasError();
  	}

  	private function preparePostData($data) {
		$data = strip_tags_decode($data);

		$data['member_account_name'] = ucwords(trim($data['member_account_name']));
		$data['member_city'] = ucwords(trim($data['member_city']));

		// meta description prep
		/*
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$member_country = $this->model_localisation_country->getCountry($data['member_country_id']);
		$member_zone = $this->model_localisation_zone->getZone($data['member_zone_id']);

		$meta_description = $this->language->get('text_account_member') . ': ' . ucwords($data['member_account_name']) . '; ';
		$meta_description .= $this->language->get('text_location') . ': ' . $data['member_city'] . ', ' . $member_zone['name'] . ', ' . $member_country['name'] . '; ';
		$meta_description .= $this->language->get('entry_member_account_description') . ': ' . utf8_substr(trim(strip_tags(html_entity_decode($data['member_account_description'], ENT_QUOTES, 'UTF-8'))), 0, 100) . ';';

		$data['meta_description'] = $meta_description;
		*/

		if (!$this->customer->hasProfile()) {
			if ($this->config->get('member_member_alias')) {
				$profile_alias = friendly_url(html_entity_decode($this->request->post['member_account_name'], ENT_QUOTES, 'UTF-8')) . '-' . mt_rand();
			} else {
				$profile_alias = mt_rand();
			}

			$data['keyword'] = 'profile-' . $profile_alias;

			$data['member_group_id'] = $this->model_account_customer_group->getDefaultMemberGroupId($this->request->post['customer_group_id']);

			$data['viewed'] = '0';

			$this->data['member_directory_images'] = $this->config->get('member_image_upload_directory');  // default member setting value
			$this->data['member_directory_downloads'] = $this->config->get('member_download_directory');  // default member setting value
			$this->data['member_max_products'] = $this->config->get('member_products_max');  // default member setting value

			if ($this->config->get('member_member_directories')) {
				$alpha_letter = utf8_substr($profile_alias, 0, 1); // first letter of profile name
				$member_directory_images = clean_path($this->config->get('member_image_upload_directory') . '/' . $alpha_letter . '/' . $profile_alias);
				$directory = DIR_IMAGE . 'data/' . $member_directory_images;  // web-root-home-dir/image/data/member/name-123456

				if (!is_dir($directory)) {
					if (mkdir($directory, 0755, true)) {
						$data['member_directory_images'] = $member_directory_images; // member/name-123456

						// member account image
						if (!empty($this->request->post['member_account_image'])) {
							$ext = substr(strrchr(utf8_strtolower($data['member_account_image']), '.'), 1);  // jpg (file extension)
							$old_filepath = DIR_IMAGE . $data['member_account_image'];  // web-root-home-dir/image/data/member/uploaded-image.jpg
							$new_filename = $data['keyword'] . '-image.' . $ext;  // member-name-image.jpg
							$new_filepath = $directory . '/' . $new_filename;  // web-root-home-dir/image/data/member/a/name-123456/member-name-image.jpg

							if (is_file($old_filepath)) {
								rename($old_filepath, $new_filepath);  // move image file
								$data['member_account_image'] = 'data/' . $member_directory_images . '/' . $new_filename; // data/member/a/member-name-123456/member-name-123456-image.jpg
								// $this->log->write("SUCCESS: image file '" . $old_filepath . "' was moved to file location '" . $new_filepath . "' for new member '" . $profile_alias . "'");
							} else {
								$this->log->write("ERROR: image file '" . $old_filepath . "' could NOT be moved to file location '" . $new_filepath . "' for new member '" . $profile_alias . "'");
							}
						}

						// member account banner
						if (!empty($this->request->post['member_account_banner']) && $this->customer->getMemberPermission('banner_enabled')) {
							$ext = substr(strrchr(utf8_strtolower($data['member_account_banner']), '.'), 1);  // jpg (file extension)
							$old_filepath = DIR_IMAGE . $data['member_account_banner'];  // web-root-home-dir/image/data/member/uploaded-banner.jpg
							$new_filename = $data['keyword'] . '-banner.' . $ext;  // member-name-banner.jpg
							$new_filepath = $directory . '/' . $new_filename;  // web-root-home-dir/image/data/member/a/name-123456/member-name-banner.jpg

							if (is_file($old_filepath)) {
								rename($old_filepath, $new_filepath);  // move banner image file
								$data['member_account_banner'] = 'data/' . $member_directory_images . '/' . $new_filename; // data/member/a/member-name-123456/member-name-123456-banner.jpg
								// $this->log->write("SUCCESS: image banner file '" . $old_filepath . "' was moved to file location '" . $new_filepath . "' for new member '" . $profile_alias . "'");
							} else {
								$this->log->write("ERROR: image banner file '" . $old_filepath . "' could NOT be moved to file location '" . $new_filepath . "' for new member '" . $profile_alias . "'");
							}
						} else {
							$data['member_account_banner'] = '';
						}
					} else {
						$this->log->write("ERROR: directory '" . $directory . "' could NOT be created for new member '" . $profile_alias . "'");
					}
				} else {
					$this->log->write("ERROR: directory '" . $directory . "' already EXISTS. Manually set a private image upload directory for new member '" . $profile_alias . "'");
				}

				$member_directory_downloads = clean_path($this->config->get('member_download_directory') . '/' . $alpha_letter . '/' . $profile_alias); // member/a/lastname-firstname-123456

				$directory = DIR_DOWNLOAD . $member_directory_downloads; // new private downloads dir under /downloads/member/

				if (!is_dir($directory)) {
					if (mkdir($directory, 0755, true)) {
						$data['member_directory_downloads'] = $member_directory_downloads;
					} else {
						$this->log->write("ERROR: directory '" . $member_directory_downloads . "' could NOT be created for new Member '" . $profile_alias . "'");
					}
				} else {
					$this->log->write("ERROR: directory '" . $member_directory_downloads . "' already EXISTS. Manually set a private downloads directory for new member '" . $profile_alias . "'");
				}
			}
		} else {
			if (!$this->customer->getMemberPermission('url_alias_enabled')) {
				// automatically update SEO url alias keyword for existing member profile if and only if profile name has changed and member does not have custom URL permissions
				$profile_data = $this->model_account_member->getMember();

				if (friendly_url($profile_data['member_account_name']) != friendly_url($data['member_account_name'])) {
					$data['member_url_alias'] = 'profile-' . friendly_url($data['member_account_name']) . '-' . mt_rand();
				}

				// $data['member_url_alias'] = ''; // uncomment to enforce NO update
			} else if (isset($data['member_url_alias'])) {
				$data['member_url_alias'] = friendly_url($data['member_url_alias']); // update
			}

			// if (isset($this->request->post['customer_group_id'])) {
			// 	$data['member_group_id'] = $this->model_account_customer_group->getDefaultMemberGroupId($this->request->post['customer_group_id']);
			// }
		}

		if (!$this->customer->getMemberPermission('banner_enabled')) {
			$data['member_account_banner'] = '';
			$data['member_custom_field_06'] = '';
		}

		// Embed Settings
		if ($this->customer->getMemberPermission('banner_enabled') && isset($this->request->post['member_custom_field_06'])) {
			$embed_settings = array();

			foreach ($data['embed_settings_bool'] as $key => $value) {
				$embed_settings[$key] = $value;
			}

			if (isset($data['embed_settings_bool']['customcolor']) && $data['embed_settings_bool']['customcolor'] == 'true') {
				foreach ($data['embed_settings_hex'] as $key => $value) {
					$embed_settings[$key] = $value;
				}
			}

			$embed_settings_stringify = http_build_query($embed_settings);

			$data['member_custom_field_06'] = utf8_strlen($embed_settings_stringify) > 255 ? substr($embed_settings_stringify, 0, 255) : $embed_settings_stringify;
		}

		// htmlspecialchars(json_encode($data['member_custom_field_06']), ENT_COMPAT);

		if (!$this->customer->getMemberPermission('sort_enabled')) {
			$data['sort_order'] = '10';

			if (isset($this->request->post['member_activity']) && is_array($this->config->get('config_category_tags'))) {
				$data['member_tag'] = implode(',', array_intersect(array_flip($this->config->get('config_category_tags')), $data['member_activity']));
			} else {
				$data['member_tag'] = '';
			}
		} else {
			$data['sort_order'] = '1';
			$data['member_tag'] = preg_replace('/[\s,#]+/', ', $1', trim(utf8_strtolower($data['member_tag']), " \t\n\r\0\x0B,#"));
		}

		$data['meta_keyword'] = $data['member_tag'];

		return $data;
	}

}
