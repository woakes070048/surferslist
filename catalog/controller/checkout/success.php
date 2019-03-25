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

		if (!$this->request->checkReferer($this->config->get('config_url')) && !$this->request->checkReferer($this->config->get('config_ssl'))) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->data = $this->load->language('checkout/success');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_basket'), $this->url->link('checkout/cart'));
		$this->addBreadcrumb($this->language->get('text_checkout'), $this->url->link('checkout/checkout'));
		$this->addBreadcrumb($this->language->get('text_success'), $this->url->link('checkout/success'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->session->data['success'] = $this->language->get('text_success_order');

		$message = $this->language->get('text_message_success');

		$customer_id = $this->config->get('config_customer_id');
		$member_id = $this->config->get('config_member_id');
		$profile_name = $this->config->get('config_owner');
		// $contact_email = $this->config->get('config_email');

		if (isset($this->request->get['order_no'])) {
			$this->load->model('checkout/order');

			$order_id = $this->model_checkout_order->getOrderIdByOrderNo($this->request->get['order_no']);

			if ($order_id) {
				$this->load->model('account/order');

				$order_info = $this->model_account_order->getOrder($this->request->get['order_no']);
				$order_member = $this->model_checkout_order->getOrderMember($order_id);

				if ($order_info && ($this->customer->isLogged() || $this->config->get('config_guest_checkout'))) {
					$message .= sprintf($this->language->get('text_message_order_no'), $order_info['order_no']);
				}

				if ($order_member) {
					$customer_id = $order_member['customer_id'];
					$member_id = $order_member['member_id'];
					$profile_name = $order_member['member_name'];
					// $contact_email = $order_member['email'];
				}

				$contact_href = $member_id ? $this->url->link('product/member/info', 'member_id=' . $member_id, 'SSL') : $this->url->link('information/contact', 'contact_id=' . $customer_id, 'SSL');
			}
		}

		$message .= $this->language->get('text_message_details');

		if ($this->customer->isLogged()) {
			$message_breadcrumb = sprintf($this->language->get('text_message_breadcrumb'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'));

			if (!empty($order_info)) {
				$message_breadcrumb .= sprintf($this->language->get('text_message_breadcrumb_last'), $this->url->link('account/order/info', 'order_no=' . $order_info['order_no'], 'SSL'), $order_info['order_no']);
			}

			$message .= sprintf($this->language->get('text_message_order_info'), $message_breadcrumb);
		}

		if (isset($contact_href)) {
			$message .= sprintf($this->language->get('text_message_contact'), $contact_href, $profile_name);
		}

		$message .= sprintf($this->language->get('text_message_thanks'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));

		$this->data['text_message'] = $message;

		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['continue'] = $this->url->link('common/home', '', 'SSL');

		$this->template = 'template/common/success.tpl';

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
}
?>
