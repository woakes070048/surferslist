<?php
class ControllerAccountNewsletter extends Controller {
	public function index() {
		// disable
		$this->redirect($this->url->link('error/not_found'));

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/newsletter', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->data = $this->load->language('account/newsletter');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/newsletter', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			$this->load->model('account/customer');

			$this->model_account_customer->editNewsletter($this->request->post['newsletter']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_newsletter'), $this->url->link('account/newsletter'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['action'] = $this->url->link('account/newsletter', 'customer_token=' . $this->session->data['customer_token'], 'SSL');

		$this->data['newsletter'] = $this->customer->getNewsletter();

		$this->data['back'] = $this->url->link('account/account', '', 'SSL');

		$this->template = 'template/account/newsletter.tpl';

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
