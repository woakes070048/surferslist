<?php
class ControllerModulePPButton extends Controller {
	public function index() {
		$status = true;

		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) || (!$this->customer->isLogged() && $this->cart->hasDownload())) {
			$status = false;
		}

		if ($status) {
			$this->load->model('payment/pp_express');

			if (preg_match('/Mobile|Android|BlackBerry|iPhone|Windows Phone/', $this->request->server['HTTP_USER_AGENT'])) {
				$this->data['mobile'] = true;
			} else {
				$this->data['mobile'] = false;
			}

			$this->data['payment_url'] = $this->url->link('payment/pp_express/express', '', 'SSL');

			return $this->load->view($this->config->get('config_template') . '/template/module/pp_button.tpl', $data);
		}
	}
}
