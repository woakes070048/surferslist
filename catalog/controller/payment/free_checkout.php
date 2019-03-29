<?php
class ControllerPaymentFreeCheckout extends Controller {
	protected function index() {
		$this->load->model('checkout/order');

		$order_no = $this->model_checkout_order->getOrderNoByOrderId($this->session->data['order_id']);

		$this->data['button_confirm'] = $this->language->get('button_confirm');

		$this->data['continue'] = $this->url->link('checkout/success', 'order_no=' . $order_no, 'SSL');

		$this->template = 'template/payment/free_checkout.tpl';

		$this->render();
	}

	public function confirm() {
		$this->load->model('checkout/order');

		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('free_checkout_order_status_id'));
	}
}

