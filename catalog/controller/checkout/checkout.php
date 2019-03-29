<?php
class ControllerCheckoutCheckout extends Controller {
	public function index() {
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->redirect($this->url->link('checkout/cart'));
		}

		if ($this->config->get('config_secure') && !$this->request->isSecure()) {
			$this->redirect($this->url->link('checkout/checkout', '', 'SSL'));
		}

		$order_member_ids = array();

		// Validate minimum quantity requirements.
		$products = $this->cart->getProducts();

		foreach ($products as $product) {
			$product_total = 0;
			$order_member_ids[] = (!empty($product['member_customer_id']) ? $product['member_customer_id'] : 0);

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			if ($product['minimum'] > $product_total) {
				$this->redirect($this->url->link('checkout/cart'));
			}
		}

		// Validate Member unique
		if (count(array_unique(array_filter($order_member_ids), SORT_REGULAR)) > 1) {
			$this->redirect($this->url->link('checkout/cart'));
		}

		$this->load->language('checkout/checkout');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_cart'), $this->url->link('checkout/cart'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('checkout/checkout'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_checkout_option'] = $this->language->get('text_checkout_option');
		$this->data['text_checkout_account'] = $this->language->get('text_checkout_account');
		$this->data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
		$this->data['text_checkout_shipping_address'] = $this->language->get('text_checkout_shipping_address');
		$this->data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');
		$this->data['text_checkout_payment_method'] = $this->language->get('text_checkout_payment_method');
		$this->data['text_checkout_confirm'] = $this->language->get('text_checkout_confirm');
		$this->data['text_modify'] = $this->language->get('text_modify');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_select_zone'] = $this->language->get('text_select_zone');
		$this->data['text_select_country'] = $this->language->get('text_select_country');

		$this->data['logged'] = $this->customer->isLogged();
		$this->data['shipping_required'] = $this->cart->hasShipping();

		$this->document->addScript('catalog/view/root/javascript/checkout.js');

		if (!$this->customer->isLogged()) {
			$this->document->addScript('catalog/view/root/javascript/login.js');
		}

		$this->template = 'template/checkout/checkout.tpl';

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

