<?php
class ControllerPaymentCheque extends Controller {
	private $payable = '';
	private $address = '';
	private $memo = '';

	protected function index() {
		$this->load->language('payment/cheque');

		$this->data['text_instruction'] = $this->language->get('text_instruction');
		$this->data['text_payable'] = $this->language->get('text_payable');
		$this->data['text_address'] = $this->language->get('text_address');
		$this->data['text_memo'] = $this->language->get('text_memo');
		$this->data['text_payment'] = $this->language->get('text_payment');

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$order_no = $this->setPaymentInstructions();

		$this->data['payable'] = $this->payable;
		$this->data['address'] = $this->address;
		$this->data['memo'] = $this->memo;

		$this->data['continue'] = $this->url->link('checkout/success', 'order_no=' . $order_no, 'SSL');

		$this->template = 'template/payment/cheque.tpl';

		$this->render();
	}

	public function confirm() {
		$this->load->language('payment/cheque');

		$order_no = $this->setPaymentInstructions();

		$comment  = $this->language->get('text_payable') . "\n";
		$comment .= $this->payable . "\n\n";
		$comment .= $this->language->get('text_address') . "\n";
		$comment .= $this->address . "\n\n";
		$comment .= $this->language->get('text_memo') . "\n";
		$comment .= $this->memo . "\n\n";
		$comment .= $this->language->get('text_payment') . "\n";

		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cheque_order_status_id'), html_entity_decode($comment, ENT_QUOTES, 'UTF-8'), true);
	}

	private function setPaymentInstructions() {
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$order_no = $this->model_checkout_order->getOrderNoByOrderId($this->session->data['order_id']); // $this->session->data['order_no']

		// get member_id (all listings should belong to a single member)
		if ($order_info) {
			foreach ($this->cart->getProducts() as $product) {
				if (isset($product['member_customer_id'])) {
					$member_customer_id = $product['member_customer_id'];
				}

				break;
			}
		}

		if ($order_no) {
			$this->memo = $this->config->get('config_name') . ' ' . sprintf($this->language->get('text_order_no'), $order_no);
			//$total = $this->currency->format($this->currency->convert($order_info['total'], $this->config->get('config_currency'), $order_info['currency_code']), $order_info['currency_code'], 1.0, false);

			if (empty($member_customer_id)) {
				$this->payable = $this->config->get('config_owner');
				$this->address = $this->config->get('config_address');
			} else {
				$this->load->model('catalog/member');
				$this->load->model('account/address');

				$member_info = $this->model_catalog_member->getMemberByCustomerId($member_customer_id);

				if ($member_info['address_id'] > 0) {
					$default_address = $this->model_catalog_member->getMemberAddress($member_customer_id, $member_info['address_id']);

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

					$this->address = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
					// $this->address = substr($address, strpos($address, '<br />') + 6); // remove first line (firstname lastname)
				} else {
					$this->address = sprintf($this->language->get('text_contact_member'), $this->url->link('information/contact', 'contact_id=' . $member_info['customer_id'], 'SSL'), $member_info['member_account_name']);
				}

				$this->payable = $member_info['firstname'] . ' ' . $member_info['lastname'];

				$this->memo .= ' - ' . $member_info['member_account_name'];
			}			
		}

		return $order_no;
	}

}

