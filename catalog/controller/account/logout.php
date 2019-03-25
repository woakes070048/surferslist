<?php
class ControllerAccountLogout extends Controller {
	public function index() {
		$this->data = $this->load->language('account/logout');

		if ($this->customer->isLogged()) {
			$this->customer->logout();
			$this->cart->clear();

			unset($this->session->data['wishlist']);
			unset($this->session->data['shipping_address_id']);
			unset($this->session->data['shipping_country_id']);
			unset($this->session->data['shipping_country_iso_code_3']);
			unset($this->session->data['shipping_zone_id']);
			unset($this->session->data['shipping_postcode']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_address_id']);
			unset($this->session->data['payment_country_id']);
			unset($this->session->data['payment_zone_id']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);

			if (!empty($this->request->get['popup'])) {
				$json = array(
					'status'   => 1,
					'message'  => $this->language->get('text_success'),
					'redirect' => $this->url->link('common/home', '', 'SSL')
				);

				$this->response->setOutput(json_encode($json));
				return;
			} else {
				$this->session->data['success'] = $this->language->get('text_success');
				$this->redirect($this->url->link('common/home', '', 'SSL'));
			}
		} else {
			if (!empty($this->request->get['popup'])) {
				$json = array(
					'status'   => 0,
					'message'  => $this->language->get('text_warning'),
					'redirect' => $this->url->link('account/login', '', 'SSL')
				);

				$this->response->setOutput(json_encode($json));
				return;
			} else {
				$this->session->data['warning'] = $this->language->get('text_warning');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_logout'), $this->url->link('account/logout'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['continue'] = $this->url->link('common/home');

		$this->template = 'template/common/success.tpl';

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
