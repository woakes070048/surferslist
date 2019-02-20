<?php
class ControllerAccountPassword extends Controller {
	use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/password', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	public function index() {
		$this->data = $this->load->language('account/password');

		$this->document->setTitle($this->language->get('heading_title'));

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/password', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validate()) {
				$this->load->model('account/customer');
				$this->model_account_customer->editPassword($this->customer->getEmail(), $this->request->post['password']);
				$this->session->data['success'] = $this->language->get('text_success');
				$this->redirect($this->url->link('account/account', '', 'SSL'));
			}
		}

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/password'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['help_password_requirements'] = sprintf($this->language->get('error_password'), 8);

		$this->data['button_continue'] = $this->language->get('button_save');
		$this->data['button_back'] = $this->language->get('button_cancel');

		$this->data['error_password'] = $this->getError('password');
		$this->data['error_confirm'] = $this->getError('confirm');

		$this->data['action'] = $this->url->link('account/password', 'customer_token=' . $this->session->data['customer_token'], 'SSL');

		$this->data['password'] = isset($this->request->post['password']) ? $this->request->post['password'] : '';
		$this->data['confirm'] = isset($this->request->post['confirm']) ? $this->request->post['confirm'] : '';

		$this->data['back'] = $this->url->link('account/account', '', 'SSL');

		$this->template = '/template/account/password.tpl';

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

	protected function validate() {
		if (!isset($this->request->post['password']) || !$this->validatePassword($this->request->post['password'], 8)) {
			$this->setError('password', sprintf($this->language->get('error_password'), 8));
		}

		if ($this->request->post['confirm'] != $this->request->post['password']) {
			$this->setError('confirm', $this->language->get('error_confirm'));
		}

		return !$this->hasError();
	}
}
?>
