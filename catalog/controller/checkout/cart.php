<?php
class ControllerCheckoutCart extends Controller {
	use ValidateField;

	private $order_member_id = 0;

	public function index() {
		$this->init();

		if (!isset($this->session->data['vouchers'])) {
			$this->session->data['vouchers'] = array();
		}

		// Update
		if (!empty($this->request->post['quantity'])) {
			foreach ($this->request->post['quantity'] as $key => $value) {
				$this->cart->update($key, $value);
			}

			unset($this->session->data['insurance']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);

			$this->redirect($this->url->link('checkout/cart', '', 'SSL'));
		}

		// Remove
		if (isset($this->request->get['remove'])) {
			$this->cart->remove($this->request->get['remove']);

			unset($this->session->data['vouchers'][$this->request->get['remove']]);

			$this->session->data['success'] = $this->language->get('text_remove');

			unset($this->session->data['insurance']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['reward']);

			$this->redirect($this->url->link('checkout/cart', '', 'SSL'));
		}

		// Coupon
		if (isset($this->request->post['coupon']) && $this->validateCoupon()) {
			$this->session->data['coupon'] = $this->request->post['coupon'];

			$this->session->data['success'] = $this->language->get('text_coupon');

			$this->redirect($this->url->link('checkout/cart', '', 'SSL'));
		}

		// Voucher
		if (isset($this->request->post['voucher']) && $this->validateVoucher()) {
			$this->session->data['voucher'] = $this->request->post['voucher'];

			$this->session->data['success'] = $this->language->get('text_voucher');

			$this->redirect($this->url->link('checkout/cart', '', 'SSL'));
		}

		// Reward
		if (isset($this->request->post['reward']) && $this->validateReward()) {
			$this->session->data['reward'] = abs($this->request->post['reward']);

			$this->session->data['success'] = $this->language->get('text_reward');

			$this->redirect($this->url->link('checkout/cart', '', 'SSL'));
		}

		// Shipping
		if (isset($this->request->post['shipping_method']) && $this->validateShipping()) {
			$shipping = explode('.', $this->request->post['shipping_method']);

			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

			$this->session->data['success'] = $this->language->get('text_shipping');

			$this->redirect($this->url->link('checkout/cart', '', 'SSL'));
		}

		if (!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) {
			//return $this->forward('checkout/cart/empty');
			$this->redirect($this->url->link('error/cart_empty'));
		}

		if ($this->cart->hasProducts()) {
			$this->validateMemberUnique();
		}

		$points = $this->customer->getRewardPoints();

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}

		$this->data['text_use_reward'] = sprintf($this->language->get('text_use_reward'), $points);
		$this->data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);

		if ($this->customer->isLogged() && $this->customer->hasProfile()) {
			$this->load->model('account/member');
			$member_info = $this->model_account_member->getMember();
		}

	    if ($this->getError('warning')) {
			$this->data['error_warning'] = $this->getError('warning');
		} elseif (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
			$this->data['error_warning'] = $this->language->get('error_stock');
		} else {
			$this->data['error_warning'] = '';
		}

		if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
			$this->data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
		} else {
			$this->data['attention'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if ($this->config->get('config_cart_weight')) {
			$this->data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
		} else {
			$this->data['weight'] = '';
		}

		$this->load->model('tool/image');

		$this->data['products'] = array();

		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$this->data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			}

			$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'), 'fw');

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

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
			} else {
				$total = false;
			}

			$this->data['products'][] = array(
				'key'      => $product['key'],
				'thumb'    => $image,
				'name'     => $product['name'],
				'model'    => $product['model'],
				'manufacturer_id'  => $product['manufacturer_id'],
				'manufacturer'     => $product['manufacturer'],
				'manufacturer_href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product['manufacturer_id']),
				'manufacturer_image'        => $product['manufacturer_image'],
				'member' => (!empty($product['member']) ? $product['member'] : '' ),
				'member_href' => (!empty($product['member_id']) ? $this->url->link('product/member/info', 'member_id=' . $product['member_id']) : ''),
				'member_customer_id' => (!empty($product['member_customer_id']) ? $product['member_customer_id'] : '' ),
				'option'   => $option_data,
				'available' => $product['available'],
				'quantity' => $product['quantity'],
				'stock'    => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
				'reward'   => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
				'price'    => $price,
				'total'    => $total,
				'href'     => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				'remove'   => $this->url->link('checkout/cart', 'remove=' . $product['key'])
			);
		}

		// Gift Voucher
		$this->data['vouchers'] = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$this->data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount']),
					'remove'      => $this->url->link('checkout/cart', 'remove=' . $key)
				);
			}
		}

		if (isset($this->request->post['next'])) {
			$this->data['next'] = $this->request->post['next'];
		} else {
			$this->data['next'] = '';
		}

		$this->data['coupon_status'] = $this->config->get('coupon_status');

		if (isset($this->request->post['coupon'])) {
			$this->data['coupon'] = $this->request->post['coupon'];
		} elseif (isset($this->session->data['coupon'])) {
			$this->data['coupon'] = $this->session->data['coupon'];
		} else {
			$this->data['coupon'] = '';
		}

		$this->data['voucher_status'] = $this->config->get('voucher_status');

		if (isset($this->request->post['voucher'])) {
			$this->data['voucher'] = $this->request->post['voucher'];
		} elseif (isset($this->session->data['voucher'])) {
			$this->data['voucher'] = $this->session->data['voucher'];
		} else {
			$this->data['voucher'] = '';
		}

		$this->data['reward_status'] = ($points && $points_total && $this->config->get('reward_status'));

		if (isset($this->request->post['reward'])) {
			$this->data['reward'] = $this->request->post['reward'];
		} elseif (isset($this->session->data['reward'])) {
			$this->data['reward'] = $this->session->data['reward'];
		} else {
			$this->data['reward'] = '';
		}

		$this->data['shipping_status'] = $this->config->get('shipping_status') && $this->config->get('shipping_estimator') && $this->cart->hasShipping();

		if (isset($this->request->post['country_id'])) {
			$this->data['country_id'] = $this->request->post['country_id'];
		} else if (!empty($member_info['member_country_id'])) {
			$this->data['country_id'] = $member_info['member_country_id'];
		} else if (isset($this->session->data['shipping_country_id'])) {
			$this->data['country_id'] = $this->session->data['shipping_country_id'];
		} else {
			$this->data['country_id'] = $this->config->get('config_country_id');
		}

		$this->load->model('localisation/country');

		$this->data['countries'] = $this->model_localisation_country->getCountries();

		if (!empty($this->data['country_id'])) {
			$this->load->model('localisation/zone');
			$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->data['country_id']);
		} else {
			$this->data['zones'] = array();
		}

		if (isset($this->request->post['zone_id'])) {
			$this->data['zone_id'] = (int)$this->request->post['zone_id'];
		} else if (!empty($member_info['member_zone_id'])) {
			$this->data['zone_id'] = $member_info['member_zone_id'];
		} else if (isset($this->session->data['shipping_zone_id'])) {
			$this->data['zone_id'] = $this->session->data['shipping_zone_id'];
		} else {
			$this->data['zone_id'] = '';
		}

		if (isset($this->request->post['postcode'])) {
			$this->data['postcode'] = $this->request->post['postcode'];
		} elseif (isset($this->session->data['shipping_postcode'])) {
			$this->data['postcode'] = $this->session->data['shipping_postcode'];
		} else {
			$this->data['postcode'] = '';
		}

		if (isset($this->request->post['shipping_method'])) {
			$this->data['shipping_method'] = $this->request->post['shipping_method'];
		} elseif (isset($this->session->data['shipping_method'])) {
			$this->data['shipping_method'] = $this->session->data['shipping_method']['code'];
		} else {
			$this->data['shipping_method'] = '';
		}

		// Totals
		$this->load->model('setting/extension');

		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();

		// Display prices
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
			}

			$sort_order = array();

			foreach ($total_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $total_data);
		}

		$this->data['totals'] = $total_data;

		$this->data['continue'] = $this->order_member_id
			? $this->url->link('product/member/info', 'member_id=' . $this->order_member_id)
			: $this->url->link('product/allproducts');

		$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

		if ($this->data['shipping_status']) {
			$this->document->addScript('catalog/view/root/javascript/cart.js');
		}

		$this->template = 'template/checkout/cart.tpl';

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_bottom',
			'common/content_top',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}

	public function empty() {
		$this->init();

		$this->data['text_error'] = $this->language->get('text_empty');

		$this->data['continue'] = $this->url->link('common/home');

		unset($this->session->data['success']);

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

	private function init() {
		$this->data = $this->load->language('checkout/cart');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('checkout/cart', '', 'SSL'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['action'] = $this->url->link('checkout/cart', '', 'SSL');
		$this->data['search'] = $this->url->link('product/search');
	}

	protected function validateCoupon() {
		$this->load->model('checkout/coupon');

		$coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);

		if (!$coupon_info) {
			$this->setError('warning', $this->language->get('error_coupon'));
		}

		return !$this->hasError();
	}

	protected function validateVoucher() {
		$this->load->model('checkout/voucher');

		$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);

		if (!$voucher_info) {
			$this->setError('warning', $this->language->get('error_voucher'));
		}

		return !$this->hasError();
	}

	protected function validateReward() {
		$points = $this->customer->getRewardPoints();

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}

		if (empty($this->request->post['reward'])) {
			$this->setError('warning', $this->language->get('error_reward'));
		}

		if ($this->request->post['reward'] > $points) {
			$this->setError('warning', sprintf($this->language->get('error_points'), $this->request->post['reward']));
		}

		if ($this->request->post['reward'] > $points_total) {
			$this->setError('warning', sprintf($this->language->get('error_maximum'), $points_total));
		}

		return !$this->hasError();
	}

	protected function validateShipping() {
		if (!empty($this->request->post['shipping_method'])) {
			$shipping = explode('.', $this->request->post['shipping_method']);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
				$this->setError('warning', $this->language->get('error_shipping'));
			}
		} else {
			$this->setError('warning', $this->language->get('error_shipping'));
		}

		return !$this->hasError();
	}

	protected function validateMemberUnique() {
		$order_member_id = 0;
		$order_members = array();
		$order_member_names = array();

		foreach ($this->cart->getProducts() as $product) {
			$order_member_id = !empty($product['member_id']) ? $product['member_id'] : 0;

			if ($order_member_id) {
				$order_member_name = $product['member'];
				$order_member_url = $this->url->link('product/member/info', 'member_id=' . $product['member_id']);
			} else {
				$order_member_name = $this->config->get('config_name');
				$order_member_url = $this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url'); // $this->url->link('product/member/info', 'member_id=' . $this->config->get('config_member_id'))
			}

			if (!array_key_exists($order_member_id, $order_members)) {
				$order_members[$order_member_id] = array(
					'name' => $order_member_name,
					'href' => $order_member_url
				);
			}
		}

		if (count($order_members) > 1) {
			foreach ($order_members as $order_member) {
				$order_member_names[] = '<a href="'. $order_member['href'] . '">' . $order_member['name'] . '</a>';
			}

			$ordering_id = $this->config->get('config_ordering_id') ?: $this->config->get('config_account_id');

			$this->setError('warning', sprintf(
				$this->language->get('error_member_unique'),
				implode(', ', $order_member_names),
				$this->url->link('information/information', 'information_id=' . $ordering_id, 'SSL')
			));
		} else {
			reset($order_members);
			$this->order_member_id = key($order_members);
		}

		return !$this->hasError();
	}
}
