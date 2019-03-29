<?php
class ControllerCheckoutShippingAddress extends Controller {
	public function index() {
		$this->data = $this->load->language('checkout/checkout');

		$this->load->model('account/address');
		$this->load->model('account/customer');
		$this->load->model('localisation/country');

		$address_id = isset($this->session->data['shipping_address_id']) ? $this->session->data['shipping_address_id'] : $this->customer->getAddressId();

		$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

		if ($this->customer->hasProfile()) {
			$this->load->model('account/member');
			$member_info = $this->model_account_member->getMember();
		}

		$this->data['address_id'] = $address_id;
		$this->data['addresses'] = $this->model_account_address->getAddresses();

		if (!empty($member_info)) {
			$this->data['country_id'] = $member_info['member_country_id'];
		} else if (isset($this->session->data['shipping_country_id'])) {
			$this->data['country_id'] = $this->session->data['shipping_country_id'];
		} else {
			$this->data['country_id'] = ''; // $this->config->get('config_country_id');
		}

		if (!empty($member_info)) {
			$this->data['zone_id'] = $member_info['member_zone_id'];
		} else if (isset($this->session->data['shipping_zone_id'])) {
			$this->data['zone_id'] = $this->session->data['shipping_zone_id'];
		} else {
			$this->data['zone_id'] = '';
		}

		// $this->data['postcode'] = isset($this->session->data['shipping_postcode']) ? $this->session->data['shipping_postcode'] : '';

		$this->data['firstname'] = !empty($customer_info) ? $customer_info['firstname'] : '';
		$this->data['lastname'] = !empty($customer_info) ? $customer_info['lastname'] : '';
		$this->data['city'] = !empty($member_info) ? $member_info['member_city'] : '';

		$this->data['countries'] = $this->model_localisation_country->getCountries();

		$this->template = 'template/checkout/shipping_address.tpl';

		$this->response->setOutput($this->render());
	}

	public function validate() {
		$this->load->language('checkout/checkout');

		$json = array();

		// Validate if customer is logged in.
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}

		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
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
			if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'existing') {
				$this->load->model('account/address');

				if (empty($this->request->post['address_id'])) {
					$json['error']['warning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$json['error']['warning'] = $this->language->get('error_address');
				}

				if (!$json) {
					$this->session->data['shipping_address_id'] = $this->request->post['address_id'];

					// Default Shipping Address
					$this->load->model('account/address');

					$address_info = $this->model_account_address->getAddress($this->request->post['address_id']);

					if ($address_info) {
						if (isset($this->session->data['shipping_zone_id']) && $this->session->data['shipping_zone_id'] != $address_info['zone_id']) {
							unset($this->session->data['shipping_method']);
						}

						$this->session->data['shipping_country_id'] = $address_info['country_id'];
						$this->session->data['shipping_zone_id'] = $address_info['zone_id'];
						$this->session->data['shipping_postcode'] = $address_info['postcode'];
					} else {
						unset($this->session->data['shipping_country_id']);
						unset($this->session->data['shipping_zone_id']);
						unset($this->session->data['shipping_postcode']);
						unset($this->session->data['shipping_method']);
					}

					unset($this->session->data['shipping_methods']);
				}
			}

			if ($this->request->post['shipping_address'] == 'new') {
				if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
					$json['error']['firstname'] = $this->language->get('error_firstname');
				}

				if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
					$json['error']['lastname'] = $this->language->get('error_lastname');
				}

				if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
					$json['error']['address_1'] = $this->language->get('error_address_1');
				}

				if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
					$json['error']['city'] = $this->language->get('error_city');
				}

				$this->load->model('localisation/country');

				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

				if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
					$json['error']['postcode'] = $this->language->get('error_postcode');
				}

				if ($this->request->post['country_id'] == '') {
					$json['error']['country'] = $this->language->get('error_country');
				}

				if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
					$json['error']['zone'] = $this->language->get('error_zone');
				}

				if (!$json) {
					if (!isset($this->session->data['shipping_zone_id']) || $this->request->post['zone_id'] != $this->session->data['shipping_zone_id']) {
						unset($this->session->data['shipping_method']);
					}

					// Default Shipping Address
					$this->load->model('account/address');

					$post_data = $this->request->post;

					if (!$this->customer->getAddressId()) {
						$post_data['default'] = 1;
					}

					$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($post_data);
					$this->session->data['shipping_country_id'] = (int)$this->request->post['country_id'];
					$this->session->data['shipping_zone_id'] = (int)$this->request->post['zone_id'];
					$this->session->data['shipping_postcode'] = $this->request->post['postcode'];

					unset($this->session->data['shipping_methods']);
				}
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}

