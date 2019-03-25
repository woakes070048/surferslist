<?php
class ControllerAccountAddress extends Controller {
	Use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	public function index() {
		$this->data = $this->load->language('account/address');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/address');

		$this->getList();
	}

	public function insert() {
		$this->data = $this->load->language('account/address');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/address');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateForm()) {
				$this->model_account_address->addAddress(strip_tags_decode($this->request->post));

				$this->session->data['success'] = $this->language->get('text_insert');

				$this->redirect($this->url->link('account/address', '', 'SSL'));
			}
		}

		$this->getForm();
	}

	public function update() {
		$this->data = $this->load->language('account/address');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/address');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateForm()) {
				$this->model_account_address->editAddress($this->request->get['address_id'], strip_tags_decode($this->request->post));

				// Default Shipping Address
				if (isset($this->session->data['shipping_address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address_id'])) {
					$this->session->data['shipping_country_id'] = (int)$this->request->post['country_id'];
					$this->session->data['shipping_zone_id'] = (int)$this->request->post['zone_id'];
					$this->session->data['shipping_postcode'] = $this->request->post['postcode'];

					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
				}

				// Default Payment Address
				if (isset($this->session->data['payment_address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address_id'])) {
					$this->session->data['payment_country_id'] = (int)$this->request->post['country_id'];
					$this->session->data['payment_zone_id'] = (int)$this->request->post['zone_id'];

					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
				}

				$this->session->data['success'] = $this->language->get('text_update');

				$this->redirect($this->url->link('account/address', '', 'SSL'));

			}
		}

		$this->getForm();
	}

	public function delete() {
		$this->data = $this->load->language('account/address');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/address');

		if (isset($this->request->get['address_id'])) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/address', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateDelete()) {
				$this->model_account_address->deleteAddress($this->request->get['address_id']);

				// Default Shipping Address
				if (isset($this->session->data['shipping_address_id']) && ($this->request->get['address_id'] == $this->session->data['shipping_address_id'])) {
					unset($this->session->data['shipping_address_id']);
					unset($this->session->data['shipping_country_id']);
					unset($this->session->data['shipping_zone_id']);
					unset($this->session->data['shipping_postcode']);
					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
				}

				// Default Payment Address
				if (isset($this->session->data['payment_address_id']) && ($this->request->get['address_id'] == $this->session->data['payment_address_id'])) {
					unset($this->session->data['payment_address_id']);
					unset($this->session->data['payment_country_id']);
					unset($this->session->data['payment_zone_id']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
				}

				$this->session->data['success'] = $this->language->get('text_delete');

				$this->redirect($this->url->link('account/address', '', 'SSL'));
			}
		}

		$this->getList();
	}

	protected function getList() {
		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/address'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->session->data['warning'] = $this->getError('warning');

		$this->data['addresses'] = array();

		$results = $this->model_account_address->getAddresses();

		foreach ($results as $result) {
			if ($result['address_format']) {
				$format = $result['address_format'];
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
				'firstname' => $result['firstname'],
				'lastname'  => $result['lastname'],
				'company'   => $result['company'],
				'address_1' => $result['address_1'],
				'address_2' => $result['address_2'],
				'city'      => $result['city'],
				'postcode'  => $result['postcode'],
				'zone'      => $result['zone'],
				'zone_code' => $result['zone_code'],
				'country'   => $result['country']
			);

			$this->data['addresses'][] = array(
				'address_id' => $result['address_id'],
				'address'    => str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))),
				'default'	 => $result['address_id'] == $this->customer->getAddressId() ? true : false,
				'update'     => $this->url->link('account/address/update', 'address_id=' . $result['address_id'], 'SSL'),
				'delete'     => $this->url->link('account/address/delete', 'address_id=' . $result['address_id'] . '&customer_token=' . $this->session->data['customer_token'], 'SSL')
			);
		}

		$this->data['insert'] = $this->url->link('account/address/insert', '', 'SSL');
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');

		$this->template = 'template/account/address_list.tpl';

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

	protected function getForm() {
		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/address'));

		if (!isset($this->request->get['address_id'])) {
			$this->addBreadcrumb($this->language->get('text_new_address'), $this->url->link('account/address/insert'));
		} else {
			$this->addBreadcrumb($this->language->get('text_edit_address'), $this->url->link('account/address/update'), 'address_id=' . $this->request->get['address_id']);
		}

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$data_field_errors = array(
			'firstname',
			'lastname',
			'company_id',
			'tax_id',
			'address_1',
			'city',
			'postcode',
			'country',
			'zone'
		);

		foreach ($data_field_errors as $data_field) {
			$this->data['error_' . $data_field] = $this->getError($data_field);
		}

		if (isset($this->request->get['address_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$address_info = $this->model_account_address->getAddress($this->request->get['address_id']);

			if (!$address_info) {
				$this->session->data['warning'] = $this->language->get('error_permission');
				$this->redirect($this->url->link('account/address', '', 'SSL'));
			}
		} else {
			$this->load->model('account/customer');
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

			if ($this->customer->hasProfile()) {
				$this->load->model('account/member');
				$member_info = $this->model_account_member->getMember();
			}
		}

		if (isset($this->request->post['firstname'])) {
			$this->data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($address_info)) {
			$this->data['firstname'] = $address_info['firstname'];
		} elseif (!empty($customer_info)) {
			$this->data['firstname'] = $customer_info['firstname'];
		} else {
			$this->data['firstname'] = '';
		}

		if (isset($this->request->post['lastname'])) {
			$this->data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($address_info)) {
			$this->data['lastname'] = $address_info['lastname'];
		} elseif (!empty($customer_info)) {
			$this->data['lastname'] = $customer_info['lastname'];
		} else {
			$this->data['lastname'] = '';
		}

		if (isset($this->request->post['company'])) {
			$this->data['company'] = $this->request->post['company'];
		} elseif (!empty($address_info)) {
			$this->data['company'] = $address_info['company'];
		} else {
			$this->data['company'] = '';
		}

		if (isset($this->request->post['company_id'])) {
			$this->data['company_id'] = $this->request->post['company_id'];
		} elseif (!empty($address_info)) {
			$this->data['company_id'] = $address_info['company_id'];
		} else {
			$this->data['company_id'] = '';
		}

		if (isset($this->request->post['tax_id'])) {
			$this->data['tax_id'] = $this->request->post['tax_id'];
		} elseif (!empty($address_info)) {
			$this->data['tax_id'] = $address_info['tax_id'];
		} else {
			$this->data['tax_id'] = '';
		}

		$this->load->model('account/customer_group');

		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($this->customer->getCustomerGroupId());

		if ($customer_group_info) {
			$this->data['company_id_display'] = $customer_group_info['company_id_display'];
		} else {
			$this->data['company_id_display'] = '';
		}

		if ($customer_group_info) {
			$this->data['tax_id_display'] = $customer_group_info['tax_id_display'];
		} else {
			$this->data['tax_id_display'] = '';
		}

		if (isset($this->request->post['address_1'])) {
			$this->data['address_1'] = $this->request->post['address_1'];
		} elseif (!empty($address_info)) {
			$this->data['address_1'] = $address_info['address_1'];
		} else {
			$this->data['address_1'] = '';
		}

		if (isset($this->request->post['address_2'])) {
			$this->data['address_2'] = $this->request->post['address_2'];
		} elseif (!empty($address_info)) {
			$this->data['address_2'] = $address_info['address_2'];
		} else {
			$this->data['address_2'] = '';
		}

		if (isset($this->request->post['city'])) {
			$this->data['city'] = $this->request->post['city'];
		} elseif (!empty($address_info)) {
			$this->data['city'] = $address_info['city'];
		} elseif (!empty($member_info)) {
			$this->data['city'] = $member_info['member_city'];
		} else {
			$this->data['city'] = '';
		}

		if (isset($this->request->post['zone_id'])) {
			$this->data['zone_id'] = (int)$this->request->post['zone_id'];
		} elseif (!empty($address_info)) {
			$this->data['zone_id'] = $address_info['zone_id'];
		} elseif (!empty($member_info)) {
			$this->data['zone_id'] = $member_info['member_zone_id'];
		} else {
			$this->data['zone_id'] = '';
		}

		if (isset($this->request->post['postcode'])) {
			$this->data['postcode'] = $this->request->post['postcode'];
		} elseif (!empty($address_info)) {
			$this->data['postcode'] = $address_info['postcode'];
		} else {
			$this->data['postcode'] = '';
		}

		if (isset($this->request->post['country_id'])) {
			$this->data['country_id'] = $this->request->post['country_id'];
		} elseif (!empty($address_info)) {
			$this->data['country_id'] = $address_info['country_id'];
		} elseif (!empty($member_info)) {
			$this->data['country_id'] = $member_info['member_country_id'];
		} else {
			$this->data['country_id'] = $this->config->get('config_country_id');
		}

		$this->load->model('localisation/country');

		$this->data['countries'] = $this->model_localisation_country->getCountries();

		$account_total_addresses = $this->model_account_address->getTotalAddresses();

		$this->data['account_total_addresses'] = $account_total_addresses;

		if (isset($this->request->post['default'])) {
			$this->data['default'] = $this->request->post['default'];
		} else if (!$account_total_addresses
			|| ($account_total_addresses == 1 && isset($this->request->get['address_id']))
			|| (isset($this->request->get['address_id']) && ($this->request->get['address_id'] == $this->customer->getAddressId()))) {
			$this->data['default'] = true;
		} else {
			$this->data['default'] = false;
		}

		$this->data['hide_default'] = !$account_total_addresses
			|| (isset($this->request->get['address_id']) && ($this->request->get['address_id'] == $this->customer->getAddressId()))
			? true : false;

		if (!isset($this->request->get['address_id'])) {
			$this->data['action'] = $this->url->link('account/address/insert', 'customer_token=' . $this->session->data['customer_token'], 'SSL');
			$this->data['text_address'] = $this->language->get('text_new_address');
		} else {
			$this->data['action'] = $this->url->link('account/address/update', 'address_id=' . $this->request->get['address_id'] . '&customer_token=' . $this->session->data['customer_token'], 'SSL');
			$this->data['text_address'] = $this->language->get('text_edit_address');
		}

		$this->data['back'] = $this->url->link('account/address', '', 'SSL');

		$this->data['button_continue'] = $this->language->get('button_save');

		$this->session->data['warning'] = $this->getError('warning');

		$this->document->addScript('catalog/view/root/javascript/account.js');

		$this->template = 'template/account/address_form.tpl';

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

	protected function validateForm() {
		if (!isset($this->request->post['firstname']) || !$this->validateStringLength($this->request->post['firstname'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $this->request->post['firstname'])) {
			$this->setError('firstname', $this->language->get('error_firstname'));
		}

		if (!isset($this->request->post['lastname']) || !$this->validateStringLength($this->request->post['lastname'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $this->request->post['lastname'])) {
			$this->setError('lastname', $this->language->get('error_lastname'));
		}

		if (!isset($this->request->post['address_1']) || !$this->validateStringLength($this->request->post['address_1'], 3, 128)) {
			$this->setError('address_1', $this->language->get('error_address_1'));
		}

		if (!isset($this->request->post['city']) || !$this->validateStringLength($this->request->post['city'], 2, 128)) {
			$this->setError('city', $this->language->get('error_city'));
		}

		if ($this->model_account_address->getTotalAddresses() == 1 && isset($this->request->get['address_id']) && $this->request->post['default'] == 0) {
			$this->setError('warning', $this->language->get('error_default'));
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		if ($country_info) {
			if ($country_info['postcode_required'] && !$this->validateStringLength($this->request->post['postcode'], 2, 10)) {
				$this->setError('postcode', $this->language->get('error_postcode'));
			}

			$this->load->helper('vat');

			if ($this->config->get('config_vat') && isset($this->request->post['tax_id']) && $this->request->post['tax_id'] != '' && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
				$this->setError('tax_id', $this->language->get('error_vat'));
			}
		}

		if (empty($this->request->post['country_id']) || !is_numeric($this->request->post['country_id'])) {
			$this->setError('country', $this->language->get('error_country'));
		}

		if (empty($this->request->post['zone_id']) || !is_numeric($this->request->post['zone_id'])) {
			$this->setError('zone', $this->language->get('error_zone'));
		}

		return !$this->hasError();
	}

	protected function validateDelete() {
		if (!isset($this->request->get['address_id'])) {
			return false;
		}

		if ($this->customer->getAddressId() == $this->request->get['address_id']) {
			$this->setError('warning', $this->language->get('error_default'));
		} else if ($this->model_account_address->getTotalAddresses() == 1) {
			$this->setError('warning', $this->language->get('error_delete'));
		}

		return !$this->hasError();
	}

}
?>
