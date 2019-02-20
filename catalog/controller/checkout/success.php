<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();

			unset($this->session->data['insurance']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['order_no']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
		}

		$this->load->language('checkout/success');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_basket'), $this->url->link('checkout/cart'));
		$this->addBreadcrumb($this->language->get('text_checkout'), $this->url->link('checkout/checkout'));
		$this->addBreadcrumb($this->language->get('text_success'), $this->url->link('checkout/success'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['heading_sub_title'] = $this->language->get('heading_sub_title');

		$this->data['text_my_account'] = $this->language->get('text_my_account');

		$contact_id = 0;
		$listing_owner = $this->config->get('config_store'); // config_owner

		if (isset($this->request->get['order_no'])) {
			$this->load->model('checkout/order');

			$order_id = $this->model_checkout_order->getOrderIdByOrderNo($this->request->get['order_no']);

			if ($order_id) {
				$order_member_info = $this->model_checkout_order->getOrderMember($order_id);

				if (!empty($order_member_info['member_name'])) {
					$contact_id = $order_member_info['customer_id'];
					$listing_owner = $order_member_info['member_name'];
				} else {
					$listing_owner = $this->config->get('config_owner');
				}
			}
		}

		$contact_href = $this->url->link('information/contact', 'contact_id=' . $contact_id, 'SSL');

		if ($this->customer->isLogged()) {
			$this->data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $contact_href, $listing_owner, html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		} else {
			$this->data['text_message'] = sprintf($this->language->get('text_guest'), $contact_href, $listing_owner, html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		}

		$this->data['button_continue'] = $this->language->get('button_continue');

		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['continue'] = $this->url->link('common/home', '', 'SSL');

		$this->template = '/template/common/success.tpl';

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
?>
