<?php
class ControllerAccountAccount extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	public function index() {
        $this->data = $this->load->language('account/account');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/account');
		$this->load->model('account/customer');
		$this->load->model('account/member');
		$this->load->model('account/address');
		$this->load->model('catalog/information');

		$this->data['text_not_enabled'] = $this->language->get('text_not_enabled') . ' ' . sprintf($this->language->get('text_not_enabled_more'), $this->url->link('information/contact', 'contact_id=0', 'SSL'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['text_member_custom_field_01'] = $this->config->get('member_custom_field_01');
		$this->data['text_member_custom_field_02'] = $this->config->get('member_custom_field_02');
		$this->data['text_member_custom_field_03'] = $this->config->get('member_custom_field_03');
		$this->data['text_member_custom_field_04'] = $this->config->get('member_custom_field_04');
		$this->data['text_member_custom_field_05'] = $this->config->get('member_custom_field_05');
		$this->data['text_member_custom_field_06'] = $this->config->get('member_custom_field_06');

		$this->data['edit'] = $this->url->link('account/edit', '', 'SSL');
		$this->data['member'] = $this->url->link('account/member', '', 'SSL');
		$this->data['password'] = $this->url->link('account/password', '', 'SSL');
		$this->data['addresses'] = $this->url->link('account/address', '', 'SSL');
		$this->data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$this->data['order'] = $this->url->link('account/order', '', 'SSL');
		$this->data['product'] = $this->url->link('account/product', '', 'SSL');
		$this->data['sales'] = $this->url->link('account/sales', '', 'SSL');
		$this->data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$this->data['reviews'] = $this->url->link('account/review', '', 'SSL');
		$this->data['questions'] = $this->url->link('account/question', '', 'SSL');
		$this->data['notify'] = $this->url->link('account/notify', '', 'SSL');

		// Customer Info
		$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

		if ($customer_info) {
			$this->data['firstname'] = $customer_info['firstname'];
			$this->data['lastname'] = $customer_info['lastname'];
			$this->data['email'] = $customer_info['email'];
			$this->data['telephone'] = $customer_info['telephone'];
			$this->data['fax'] = $customer_info['telephone'];
		}

		$privacy_policy_information_id = 3; // information_id 3 => Privacy Policy
		$privacy_policy_info = $this->model_catalog_information->getInformation($privacy_policy_information_id);

		if ($privacy_policy_info) {
			$this->data['help_personal_info'] .= ' ' . sprintf(
				$this->language->get('help_personal_info_more'),
				$this->url->link('information/information', 'information_id=' . $privacy_policy_information_id, 'SSL'),
				$privacy_policy_info['title']
			);
		}

		// Address Info
		$default_address_id = $this->customer->getAddressId();

		if ($default_address_id) {
			$default_address = $this->model_account_address->getAddress($default_address_id);

			if ($default_address['address_format']) {
				$format = $default_address['address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $default_address['firstname'],
				'lastname'  => $default_address['lastname'],
				'company'   => $default_address['company'],
				'address_1' => $default_address['address_1'],
				'address_2' => $default_address['address_2'],
				'city'      => $default_address['city'],
				'postcode'  => $default_address['postcode'],
				'zone'      => $default_address['zone'],
				'zone_code' => $default_address['zone_code'],
				'country'   => $default_address['country']
			);

			$this->data['address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
			$this->data['address'] = substr($this->data['address'], strpos($this->data['address'], '<br />') + 6); // remove first line (firstname lastname)
		} else {
			$this->data['address'] = '';
		}

		$this->data['member_total_addresses'] = $this->model_account_address->getTotalAddresses();

		$this->data['address_add'] = $this->url->link('account/address/insert', '', 'SSL');

		// Member Info
		$member_info = $this->model_account_member->getMember();

		if ($this->customer->hasProfile() && $member_info) {
			$this->data['member_page'] = $this->customer->getProfileUrl();
			$this->data['member_url_alias'] = substr($this->data['member_page'], strlen($this->url->link('common/home', '', 'SSL')));
			$this->data['member_name'] = $member_info['member_account_name'];
			$this->data['member_group'] = $member_info['member_group']; // membership group

			if (!empty($member_info['member_paypal_account'])) {
				$this->data['member_paypal_activated'] = true;
				$this->data['member_paypal'] = $member_info['member_paypal_account'];
			} else {
				$this->data['member_paypal_activated'] = false;
				$this->data['member_paypal'] = sprintf($this->language->get('text_no_paypal'), $this->url->link('account/member', 'no_paypal=true', 'SSL') . '#jump_to_paypal');
			}

			// removed member description
			/*
			if(!empty($member_info['member_account_description'])){
				$this->data['member_description'] = utf8_substr(strip_tags(html_entity_decode($member_info['member_account_description'], ENT_QUOTES, 'UTF-8')), 0, 300) . $this->language->get('text_ellipses');
			} else {
				$this->data['member_description'] = '';
			} */

			// location
			$this->load->model('localisation/zone');
			$this->load->model('localisation/country');

			$member_zone = $this->model_localisation_zone->getZone($member_info['member_zone_id']);
			$member_country = $this->model_localisation_country->getCountry($member_info['member_country_id']);

			$this->data['member_location'] = $member_info['member_city'] . ', ' . $member_zone['name'] . ', ' . $member_country['name'];

			// social links
			$this->data['member_custom_field_01'] = $member_info['member_custom_field_01']; // preg_replace('#^https?://(www.)?#', '', $member_info['member_custom_field_01']);
			$this->data['member_custom_field_02'] = $member_info['member_custom_field_02'];
			$this->data['member_custom_field_03'] = $member_info['member_custom_field_03'];
			$this->data['member_custom_field_04'] = $member_info['member_custom_field_04'];
			$this->data['member_custom_field_05'] = $member_info['member_custom_field_05'];
			$this->data['member_custom_field_06'] = !empty($member_info['member_custom_field_06']) ? $this->url->link('embed/profile', 'profile_id=' . $member_info['customer_id'], 'SSL') : '';

			if ($member_info['member_custom_field_01'] || $member_info['member_custom_field_02'] || $member_info['member_custom_field_03'] || $member_info['member_custom_field_04'] || $member_info['member_custom_field_05'] || $member_info['member_custom_field_06']) {
				$this->data['member_socials'] = true;
			} else {
				$this->data['member_socials'] = false;
			}

			$this->data['member_tags'] = array();

			if ($member_info['member_tag']) {
				$tags = explode(',', $member_info['member_tag']);

				foreach ($tags as $tag) {
					$this->data['member_tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/member', 'tag=' . trim($tag), 'SSL')
					);
				}
			}

			$this->data['member_date_added'] = date($this->language->get('date_format_long'), strtotime($member_info['date_added']));
			$this->data['member_reviews'] = $member_info['reviews'] ? $member_info['reviews'] : 0;
			$this->data['member_rating'] = round($member_info['rating']);
			$this->data['help_member_rating'] = sprintf($this->language->get('help_member_rating'), number_format($member_info['rating'], 2), (int)$member_info['reviews']);

			$this->data['member_total_products'] = $this->model_account_account->getTotalProducts();
			$this->data['member_total_sales'] = $this->model_account_account->getTotalSales();
			$this->data['member_total_reviews'] = $this->model_account_account->getTotalReviews();
			$this->data['member_total_views'] = 0;
		} else {
			$this->data['member_page'] = '';
			$this->data['member_url_alias'] = '';
			$this->data['member_name'] = '';
			$this->data['member_paypal'] = '';
			$this->data['member_location'] = '';
			$this->data['member_description'] = '';
			$this->data['member_custom_field_01'] = '';
			$this->data['member_custom_field_02'] = '';
			$this->data['member_custom_field_03'] = '';
			$this->data['member_custom_field_04'] = '';
			$this->data['member_custom_field_05'] = '';
			$this->data['member_custom_field_06'] = '';
			$this->data['member_tags'] = array();
			$this->data['member_date_added'] = '';
			$this->data['member_reviews'] = '';
			$this->data['member_rating'] = '';
			$this->data['help_member_rating'] = sprintf($this->language->get('help_member_rating'), 0, 0);
			$this->data['member_total_products'] = '';
			$this->data['member_total_sales'] = '';
			$this->data['member_total_reviews'] = '';
			$this->data['member_total_views'] = '';
			$this->data['text_not_activated'] = $this->language->get('text_not_activated') . '  ' . sprintf($this->language->get('text_account_activate'), $this->url->link('account/member', '', 'SSL')) . ' ';

			$member_profile_information_id = 13; // information_id 13 => Member Profile
			$member_profile_info = $this->model_catalog_information->getInformation($member_profile_information_id);

			if ($member_profile_info) {
				$this->data['text_not_activated'] .= ' ' . sprintf($this->language->get('text_not_activated_more'), $this->url->link('information/information/info', 'information_id=' . $member_profile_information_id, 'SSL'), $member_profile_info['title']);
			}
		}

		$this->data['account_total_orders'] = $this->model_account_account->getTotalOrders();
		$this->data['member_total_questions'] = $this->model_account_account->getTotalQuestions();
		$this->data['account_total_wishlist'] = isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0;

		$this->load->model('tool/image');

		$this->data['member_thumb'] = $this->model_tool_image->resize((isset($member_info['member_account_image']) ? $member_info['member_account_image'] : ''), 306, 306, 'autocrop');

		if ($this->config->get('reward_status')) {
			$this->data['reward'] = $this->url->link('account/reward', '', 'SSL');
		} else {
			$this->data['reward'] = '';
		}

		if (!isset($this->session->data['warning'])) {
			if ($this->customer->hasProfile() && !$this->customer->isProfileEnabled()) {
				$this->session->data['warning'] = $this->data['text_not_enabled'];
			} else {
				$this->session->data['warning'] = '';
			}
		}

		$this->data['activated'] = $this->customer->hasProfile();
		$this->data['enabled'] = $this->customer->isProfileEnabled();

		$this->template = '/template/account/account.tpl';

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
