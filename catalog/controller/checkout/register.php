<?php
class ControllerCheckoutRegister extends Controller {
	use Captcha;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->setCaptchaStatus($this->config->get('config_captcha_register'));
	}

	public function index() {
		$this->data = $this->load->language('checkout/checkout');

		$this->data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter'), $this->config->get('config_name'));
		$this->data['button_continue'] = $this->language->get('button_register');

		$this->data['customer_groups'] = array();

		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');

			$customer_groups = $this->model_account_customer_group->getCustomerGroups();

			foreach ($customer_groups  as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$this->data['customer_groups'][] = $customer_group;
				}
			}
		}

		$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');

		$this->data['postcode'] = isset($this->session->data['shipping_postcode']) ? $this->session->data['shipping_postcode'] : '';
		$this->data['country_id'] = isset($this->session->data['shipping_country_id']) ? $this->session->data['shipping_country_id'] : ''; // $this->config->get('config_country_id')
		$this->data['zone_id'] = isset($this->session->data['shipping_zone_id']) ? $this->session->data['shipping_zone_id'] : '';

		$this->load->model('localisation/country');

		$this->data['countries'] = $this->model_localisation_country->getCountries();

		if ($this->config->get('config_account_id')) {
			$this->data['text_agree'] = $this->cache->get('information.registeragree.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

			if ($this->data['text_agree'] === false) {
				$this->load->model('catalog/information');
				
				$privacy_policy_id = $this->config->get('config_privacy_policy_id') ?: $this->config->get('config_account_id');
				$terms_of_use_id = $this->config->get('config_terms_of_use_id') ?: $this->config->get('config_account_id');

				$info_terms_of_use = $this->model_catalog_information->getInformation($terms_of_use_id);
				$info_privacy_policy = $this->model_catalog_information->getInformation($privacy_policy_id);

				if ($info_terms_of_use) {
					$this->data['text_agree'] = sprintf($this->language->get('text_agree'),
						$this->url->link('information/information', 'information_id=' . $terms_of_use_id, 'SSL'), $info_terms_of_use['title'], $info_terms_of_use['title'],
						$this->url->link('information/information', 'information_id=' . $privacy_policy_id, 'SSL'), $info_privacy_policy['title'], $info_privacy_policy['title']);
				} else {
					$this->data['text_agree'] = '';
				}

				$this->cache->set('information.registeragree.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data['text_agree'], 60 * 60 * 24 * 30); // 1 month cache expiration
			}
		} else {
			$this->data['text_agree'] = '';
		}

		$this->template = 'template/checkout/register.tpl';

		$this->response->setOutput($this->render());
	}

	public function validate() {
		$this->load->language('checkout/checkout');

		$this->load->model('account/customer');

		$json = array();

		// Validate if customer is already logged out.
		if ($this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
		}

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
				break;
			}
		}

		if (!$json) {
			if (!$this->validateCaptcha()) {
				$json['error']['captcha'] = $this->getCaptchaError();
			}
		}

		if (!$json) {
			if (!isset($this->request->post['firstname']) || !$this->validateStringLength($this->request->post['firstname'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $this->request->post['firstname'])) {
				$json['error']['firstname'] = sprintf($this->language->get('error_firstname'), 3, 32);
			}

			if (!isset($this->request->post['lastname']) || !$this->validateStringLength($this->request->post['lastname'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $this->request->post['lastname'])) {
				$json['error']['lastname'] = sprintf($this->language->get('error_lastname'), 3, 32);
			}

			if (!isset($this->request->post['email']) || !$this->validateEmail($this->request->post['email'])) {
				$json['error']['email'] = $this->language->get('error_email');
			}

			if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				$json['error']['warning'] = $this->language->get('error_exists');
			}

			if (!isset($this->request->post['password']) || !$this->validatePassword($this->request->post['password'], 8)) {
				$json['error']['password'] = sprintf($this->language->get('error_password'), 8);
			}

			/*
			if ($this->request->post['confirm'] != $this->request->post['password']) {
				$json['error']['confirm'] = $this->language->get('error_confirm');
			}
			*/
		}

		if (!$json) {
			$data = $this->request->post;
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');

			if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$data['customer_group_id'] = $this->request->post['customer_group_id'];
			}

			$this->load->model('account/customer_group');
			$customer_group = $this->model_account_customer_group->getCustomerGroup($data['customer_group_id']);
			$approval_required = isset($customer_group['approval']) ? $customer_group['approval'] : 1;
			$data['approval_required'] = $approval_required;

			$this->model_account_customer->addCustomer($data);

			$this->session->data['account'] = 'register';

			if (!$approval_required) {
				$this->customer->login($this->request->post['email'], $this->request->post['password']);
			} else {
				$json['redirect'] = $this->url->link('account/success');
			}

			unset($this->session->data['guest']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
		} else {
			$json['captcha_widget_id'] = isset($this->request->post['captcha_widget_id']) ? $this->request->post['captcha_widget_id'] : '';
		}

		$this->response->setOutput(json_encode($json));
	}

}
