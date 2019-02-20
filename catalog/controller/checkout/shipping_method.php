<?php
class ControllerCheckoutShippingMethod extends Controller {
	public function index() {
		$this->load->language('checkout/checkout');
		$this->load->language('total/insurance');

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$total_data = array();
			$total = 0;
			$taxes = $this->cart->getTaxes();

			$sort_order = array();

			$this->load->model('setting/extension');
			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				if (!isset($value['key']) && isset($value['code'])) {
					$value['key'] = $value['code'];
				}

				$sort_order[$key] = $this->config->get($value['key'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			$this->session->data['insurance'] = true;

			foreach ($results as $result) {
				if (!isset($result['key']) && isset($result['code'])) {
					$result['key'] = $result['code'];
				}

				if ($result['key'] == 'insurance' || $result['key'] == 'sub_total') {
					$this->load->model('total/' . $result['key']);
					$this->{'model_total_' . $result['key']}->getTotal($total_data, $total, $taxes);
				}
			}

			unset($this->session->data['insurance']);

			foreach ($total_data as $key => $value) {
				if ($value['code'] == 'insurance') {
					$this->data['insurance_fee'] = $value['text'];
					$this->data['insurance_value'] = $value['value'];
				}
			}
		}

		$this->load->model('account/address');

		if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
			$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
		} elseif (isset($this->session->data['guest'])) {
			$shipping_address = $this->session->data['guest']['shipping'];
		}

		if (!empty($shipping_address)) {
			// Shipping Methods
			$quote_data = array();

			$this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('shipping');

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('shipping/' . $result['code']);

					$quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address);

					if ($quote) {
						$quote_data[$result['code']] = array(
							'title'      => $quote['title'],
							'quote'      => $quote['quote'],
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
					}
				}
			}

			$sort_order = array();

			foreach ($quote_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $quote_data);

			$this->session->data['shipping_methods'] = $quote_data;
		}

		$this->data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');
		$this->data['entry_insurance'] = $this->language->get('entry_insurance');
		$this->data['text_insurance'] = $this->language->get('text_insurance');
		$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$this->data['text_comments'] = $this->language->get('text_comments');

		$this->data['button_next'] = $this->language->get('button_next');

		if (empty($this->session->data['shipping_methods'])) {
			$this->data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['shipping_methods'])) {
			$this->data['shipping_methods'] = $this->session->data['shipping_methods'];
		} else {
			$this->data['shipping_methods'] = array();
		}

		if (isset($this->session->data['shipping_method']['code'])) {
			$this->data['code'] = $this->session->data['shipping_method']['code'];
		} else {
			$this->data['code'] = '';
		}

		if (isset($this->session->data['comment'])) {
			$this->data['comment'] = $this->session->data['comment'];
		} else {
			$this->data['comment'] = '';
		}

		$this->data['insurance'] = !empty($this->session->data['insurance']) ? $this->session->data['insurance'] : '';

		$this->template = '/template/checkout/shipping_method.tpl';

		$this->response->setOutput($this->render());
	}

	public function validate() {
		$this->load->language('checkout/checkout');

		$json = array();

		if (isset($this->request->post['insurance'])) {
			$this->session->data['insurance'] = true;
			$this->session->data['shipping_insurance'] = $this->request->post['shipping_insurance'];
		} else {
			unset($this->session->data['insurance']);
			$this->session->data['shipping_insurance'] = 0;
		}

		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}

		// Validate if shipping address has been set.
		$this->load->model('account/address');

		if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
			$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
		} elseif (isset($this->session->data['guest'])) {
			$shipping_address = $this->session->data['guest']['shipping'];
		}

		if (empty($shipping_address)) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}

		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
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
				$json['redirect'] = $this->url->link('checkout/cart');

				break;
			}
		}

		if (!$json) {
			if (!isset($this->request->post['shipping_method'])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			} else {
				$shipping = explode('.', $this->request->post['shipping_method']);

				if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
					$json['error']['warning'] = $this->language->get('error_shipping');
				}
			}

			if (!$json) {
				$shipping = explode('.', $this->request->post['shipping_method']);

				$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

				$this->session->data['comment'] = strip_tags_decode($this->request->post['comment']);
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
?>
