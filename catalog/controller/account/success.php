<?php
class ControllerAccountSuccess extends Controller {
	public function index() {
		$this->data = $this->load->language('account/success');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_success'), $this->url->link('account/success'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->load->model('account/customer_group');

		$customer_group = $this->model_account_customer_group->getCustomerGroup($this->customer->getCustomerGroupId());

		if ($customer_group && !$customer_group['approval']) {
			$this->data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('account/member', '', 'SSL'));
		} else {
			$this->data['text_message'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'));
		}

		if ($this->cart->hasProducts()) {
			$this->data['continue'] = $this->url->link('checkout/cart');
		} else {
			$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		}

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
