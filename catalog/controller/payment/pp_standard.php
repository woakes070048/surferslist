<?php
class ControllerPaymentPPStandard extends Controller {
	protected function index() {
		$this->load->language('payment/pp_standard');

		$this->data['text_testmode'] = $this->language->get('text_testmode');

		$this->data['help_paypal'] = sprintf($this->language->get('help_paypal'), $this->config->get('config_name'));

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['testmode'] = $this->config->get('pp_standard_test');

		if (!$this->config->get('pp_standard_test')) {
			$this->data['action'] = 'https://www.paypal.com/cgi-bin/webscr';
		} else {
			$this->data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$order_no = $this->model_checkout_order->getOrderNoByOrderId($this->session->data['order_id']); // $this->session->data['order_no']

		if ($order_info) {
			// all listings should belong to a single member
			$this->load->model('catalog/product');
			$this->load->model('catalog/member');

			$member_paypal_email = false;

			foreach ($this->cart->getProducts() as $product) {
				if (isset($product['member_customer_id'])) {
					$member_info = $this->model_catalog_member->getMemberByCustomerId($product['member_customer_id']);

					if (!empty($member_info['member_paypal_account'])) {
						$member_paypal_email = $member_info['member_paypal_account'];
					}
				}

				break;
			}

			if ($member_paypal_email) {
				$this->data['business'] = $member_paypal_email;
			} else {
				$this->data['business'] = $this->config->get('pp_standard_email');
			}

			$this->data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$this->data['products'] = array();

			$subtotal = 0;

			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];
					} else {
						$filename = $this->encryption->decrypt($option['option_value']);

						$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . $this->language->get('text_ellipses') : $value)
					);
				}

				/* placeholder for listing individual shipping costs */
				if (false && $product['shipping']) {
					$this->load->model('shipping/ocapps');
					$this->model_shipping_ocapps->getQuotePerProduct($address); // getQuotePerProduct does not exist yet
					$product_shipping = '';
				} else {
					$product_shipping = '';
				}

				/* placeholder for listing commissions costs */
				if (false && $product['member_commission_rate']) {
					$product_commission = $this->currency->format(($product['price'] / 100) * $product['member_commission_rate'], $order_info['currency_code'], false, false);
				} else {
					$product_commission = '';
				}

				$price = $this->currency->format($product['price'], $order_info['currency_code'], false, false);
				$subtotal += $price * $product['quantity'];

				$this->data['products'][] = array(
					'name'     => $product['name'],
					'model'    => $product['model'],
					'price'    => $price,
					'shipping' => $product_shipping,
					'commission' => $product_commission,
					'quantity' => $product['quantity'],
					'option'   => $option_data,
					'weight'   => $product['weight']
				);
			}

			$this->data['discount_amount_cart'] = 0;

			$total = $this->currency->format($this->currency->convert($order_info['total'], $this->config->get('config_currency'), $order_info['currency_code']) - $subtotal, $order_info['currency_code'], 1.0, false);

			if ($total > 0) {
				$this->data['products'][] = array(
					'name'     => $this->language->get('text_total'),
					'model'    => '',
					'price'    => $total,
					'shipping' => '',
					'commission' => '',
					'quantity' => 1,
					'option'   => array(),
					'weight'   => 0
				);
			} else {
				$this->data['discount_amount_cart'] -= $total;
			}

			$this->data['currency_code'] = $order_info['currency_code'];

			if ($order_info['shipping_country_id']) {
				$this->data['no_shipping'] = 0;
				$this->data['first_name'] = html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8');
				$this->data['last_name'] = html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8');
				$this->data['address1'] = html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8');
				$this->data['address2'] = html_entity_decode($order_info['shipping_address_2'], ENT_QUOTES, 'UTF-8');
				$this->data['city'] = html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8');
				$this->data['state'] = html_entity_decode($order_info['shipping_zone_code'], ENT_QUOTES, 'UTF-8');
				$this->data['zip'] = html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8');
				$this->data['country'] = $order_info['shipping_iso_code_2'];
				$this->data['invoice'] = $order_no . ' - ' . html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8');
			} else {
				$this->data['no_shipping'] = 1; // do not prompt for an address
				$this->data['first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
				$this->data['last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
				$this->data['address1'] = html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8');
				$this->data['address2'] = html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8');
				$this->data['city'] = html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8');
				$this->data['state'] = html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8');
				$this->data['zip'] = html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8');
				$this->data['country'] = $order_info['payment_iso_code_2'];
				$this->data['invoice'] = $order_no . ' - ' . html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8') . ' ' . html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
			}

			$this->data['email'] = $order_info['email'];
			$this->data['lc'] = $this->session->data['language'];

			if ($order_no) {
				$this->data['return'] = $this->url->link('checkout/success', 'order_no=' . $order_no, 'SSL');
			} else {
				$this->data['return'] = $this->url->link('checkout/success', '', 'SSL');
			}

			$this->data['notify_url'] = $this->url->link('payment/pp_standard/callback', '', 'SSL');
			$this->data['cancel_return'] = $this->url->link('checkout/checkout', '', 'SSL');

			if (!$this->config->get('pp_standard_transaction')) {
				$this->data['paymentaction'] = 'authorization';
			} else {
				$this->data['paymentaction'] = 'sale';
			}

			$this->data['custom'] = $order_no;

			$this->template = 'template/payment/pp_standard.tpl';

			$this->render();
		}
	}

	public function callback() {
		if (isset($this->request->post['custom'])) {
			$order_no = $this->request->post['custom'];
		} else {
			$order_no = 0;
		}

		$this->load->model('checkout/order');

		$order_id = (int)$this->model_checkout_order->getOrderIdByOrderNo($order_no); // $this->session->data['order_no']

		$order_info = $this->model_checkout_order->getOrder($order_id);

		if ($order_info) {
			$log = new Log('paypal.log');

			$request = 'cmd=_notify-validate';

			foreach ($this->request->post as $key => $value) {
				$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
			}

			if (!$this->config->get('pp_standard_test')) {
				$curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
			} else {
				$curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
			}

			curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$response = curl_exec($curl);

			if (!$response) {
				$log->write('PP_STANDARD :: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
			}

			if ($this->config->get('pp_standard_debug')) {
				$log->write('PP_STANDARD :: IPN REQUEST: ' . $request);
				$log->write('PP_STANDARD :: IPN RESPONSE: ' . $response);
			}

			if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && isset($this->request->post['payment_status'])) {
				$order_member = $this->model_checkout_order->getOrderMember($order_info['order_id']);
				$send_payment_email = !empty($order_member['member_paypal']) ? $order_member['member_paypal'] : $this->config->get('config_email');

				$order_status_id = $this->config->get('config_order_status_id');

				switch($this->request->post['payment_status']) {
					case 'Canceled_Reversal':
						$order_status_id = $this->config->get('pp_standard_canceled_reversal_status_id');
						break;
					case 'Completed':
						if ((strtolower($this->request->post['receiver_email']) == strtolower($send_payment_email)) && ((float)$this->request->post['mc_gross'] == $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false))) {
							$order_status_id = $this->config->get('pp_standard_completed_status_id'); // "Paid" as of 2017
						} else {
							$log->write('PP_STANDARD :: RECEIVER EMAIL MISMATCH! ' . strtolower($this->request->post['receiver_email']));
						}
						break;
					case 'Denied':
						$order_status_id = $this->config->get('pp_standard_denied_status_id');
						break;
					case 'Expired':
						$order_status_id = $this->config->get('pp_standard_expired_status_id');
						break;
					case 'Failed':
						$order_status_id = $this->config->get('pp_standard_failed_status_id');
						break;
					case 'Pending':
						$order_status_id = $this->config->get('pp_standard_pending_status_id');
						break;
					case 'Processed':
						$order_status_id = $this->config->get('pp_standard_processed_status_id');
						break;
					case 'Refunded':
						$order_status_id = $this->config->get('pp_standard_refunded_status_id');
						break;
					case 'Reversed':
						$order_status_id = $this->config->get('pp_standard_reversed_status_id');
						break;
					case 'Voided':
						$order_status_id = $this->config->get('pp_standard_voided_status_id');
						break;
				}

				if (!$order_info['order_status_id']) {
					$this->model_checkout_order->confirm($order_id, $order_status_id, "Seller's Paypal: " . $send_payment_email, true);
				} else {
					$this->model_checkout_order->update($order_id, $order_status_id);
				}
			} else {
				$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
			}

			curl_close($curl);
		}
	}
}

