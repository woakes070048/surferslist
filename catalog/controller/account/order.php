<?php
class ControllerAccountOrder extends Controller {
	use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	public function index() {
		$this->data = $this->load->language('account/order');

		$this->load->model('account/order');
		$this->load->model('catalog/member');

		// disable re-order
		/*
		if (isset($this->request->get['order_no'])) {
			$order_info = $this->model_account_order->getOrder($this->request->get['order_no']);

			if ($order_info) {
				$order_products = $this->model_account_order->getOrderProducts($this->request->get['order_no']);

				foreach ($order_products as $order_product) {
					$option_data = array();

					$order_options = $this->model_account_order->getOrderOptions($this->request->get['order_no'], $order_product['order_product_id']);

					foreach ($order_options as $order_option) {
						if ($order_option['type'] == 'select' || $order_option['type'] == 'radio' || $order_option['type'] == 'image') {
							$option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'checkbox') {
							$option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
							$option_data[$order_option['product_option_id']] = $order_option['value'];
						} elseif ($order_option['type'] == 'file') {
							$option_data[$order_option['product_option_id']] = $this->encryption->encrypt($order_option['value']);
						}
					}

					$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->request->get['order_no']);

					$this->cart->add($order_product['product_id'], $order_product['quantity'], $option_data);
				}

				$this->redirect($this->url->link('checkout/cart'));
			}
		}
		* */

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/order'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['button_continue'] = $this->language->get('button_back');

		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		$this->data['orders'] = array();

		$order_total = $this->model_account_order->getTotalOrders();

		$results = $this->model_account_order->getOrders(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
			$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

			$order_number = !empty($result['order_no']) ? $result['order_no'] : $result['order_id'];

			$this->data['orders'][] = array(
				'order_id'   => $order_number,
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'member'     => $result['member'],
				'member_href' => $result['member_id'] ? $this->url->link('product/member/info', 'member_id=' . $result['member_id'], 'SSL') : '',
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_medium'), strtotime($result['date_added'])),
				'products'   => ($product_total + $voucher_total),
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				// 'reorder'    => $this->url->link('account/order', 'order_no=' . $order_number, 'SSL')
				'href'       => $this->url->link('account/order/info', 'order_no=' . $order_number, 'SSL'),
			);
		}

		$this->data['pagination'] = $this->getPagination($order_total, $page, 10, 'account/order');

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->template = 'template/account/order_list.tpl';

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
		$this->data = $this->load->language('account/order');

		$order_no = isset($this->request->get['order_no']) ? (int)$this->request->get['order_no'] : 0;

		$this->load->model('account/order');
		$this->load->model('catalog/member');

		$order_info = $this->model_account_order->getOrder($order_no);

		if ($order_info) {
			$order_id = (int)$order_info['order_id'];

			$this->document->setTitle($this->language->get('text_order'));

			$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
			$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
			$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/order'));
			$this->addBreadcrumb($this->language->get('text_order'), $this->url->link('account/order/info', 'order_no=' . $order_no));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

			$this->data['heading_title'] = $this->language->get('text_order');
			$this->data['button_continue'] = $this->language->get('button_back');

			if ($order_info['invoice_no']) {
				$this->data['invoice_no'] = $order_info['invoice_prefix'] . $this->request->get['order_no'] . $order_info['invoice_no'];;
			} else {
				$this->data['invoice_no'] = '';
			}

			$this->data['order_no'] = $order_no;
			$this->data['date_added'] = date($this->language->get('date_format_long'), strtotime($order_info['date_added']));

			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
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
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
			);

			$this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$this->data['payment_method'] = $order_info['payment_method'];

			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
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
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']
			);

			$this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$this->data['shipping_method'] = $order_info['shipping_method'];

			$this->data['products'] = array();

			$products = $this->model_account_order->getOrderProducts($order_id);

			$this->load->model('tool/image');

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_account_order->getOrderOptions($order_id, $product['order_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 40 ? utf8_substr($value, 0, 40) . $this->language->get('text_ellipses') : $value)
					);
				}

				$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'), 'fw');

				$this->data['products'][] = array(
					'name'     => $product['name'],
					'model'    => $product['model'],
					'manufacturer_id'  => $product['manufacturer_id'],
					'manufacturer'     => $product['manufacturer'],
					'manufacturer_href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product['manufacturer_id'], 'SSL'),
					'manufacturer_image'        => $product['manufacturer_image'],
					'member_id' => (isset($product['member_customer_id']) ? $product['member_customer_id'] : ''),
					'member' => (isset($product['member_account_name']) ? $product['member_account_name'] : ''),
					'member_href' => $product['member_id'] ? $this->url->link('product/member/info', 'member_id=' . $product['member_id'], 'SSL') : '',
					'image'    => $image,
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'return'   => $this->url->link('account/return/insert', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], 'SSL')
				);
			}

			// Voucher
			$this->data['vouchers'] = array();

			$vouchers = $this->model_account_order->getOrderVouchers($order_id);

			foreach ($vouchers as $voucher) {
				$this->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			$this->data['totals'] = $this->model_account_order->getOrderTotals($order_id);

			$this->data['comment'] = nl2br($order_info['comment']);

			/*
			$this->data['histories'] = array();

			$results = $this->model_account_order->getOrderHistories($order_id);

			foreach ($results as $result) {
				$this->data['histories'][] = array(
					'date_added' => date($this->language->get('date_format_medium'), strtotime($result['date_added'])),
					'status'     => $result['status'],
					'member'     => !empty($result['member']) ? $result['member'] : $this->language->get('text_admin'),
					'notified'   => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
					'comment'    => nl2br($result['comment'])
				);
			}
			* */

			$this->load->model('checkout/order');
			$order_member_info = $this->model_checkout_order->getOrderMember($order_id);

			if (!empty($order_member_info['customer_id'])) {
				$this->data['contact'] = $this->url->link('information/contact', 'contact_id=' . $order_member_info['customer_id'], 'SSL');
			} else {
				$this->data['contact'] = $this->url->link('information/contact', '', 'SSL');
			}

			$this->data['help_comment'] = sprintf($this->language->get('help_comment'), 10, 255);

			$this->data['continue'] = $this->url->link('account/order', '', 'SSL');

			$this->document->addScript('catalog/view/root/javascript/account.js');

			$this->template = 'template/account/order_info.tpl';

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
		} else {
			$this->document->setTitle($this->language->get('text_order'));

			$this->data['heading_title'] = $this->language->get('text_order');
			$this->data['button_continue'] = $this->language->get('button_back');

			$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
			$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
			$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/order'));
			$this->addBreadcrumb($this->language->get('text_order'), $this->url->link('account/order/info', 'order_no=' . $order_no));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

			$this->data['search'] = $this->url->link('product/search', '', 'SSL');
			$this->data['continue'] = $this->url->link('account/order', '', 'SSL');

			$this->template = 'template/error/not_found.tpl';

			$this->children = array(
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

}
?>
