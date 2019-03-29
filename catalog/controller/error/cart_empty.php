<?php
class ControllerErrorCartEmpty extends Controller {
	public function index() {
		$this->data = $this->load->language('checkout/cart');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('checkout/cart'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['text_error'] = $this->language->get('text_empty');

		$this->data['action'] = $this->url->link('checkout/cart', '', 'SSL');
		// $this->data['search'] = $this->url->link('product/search');
		$this->data['continue'] = $this->url->link('common/home');

		$this->template = 'template/error/not_found.tpl';

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/notification',
			'common/footer',
			'common/header'
		);

		unset($this->session->data['success']);

		$this->response->setOutput($this->render());
	}
}

