<?php
class ControllerAccountEdit extends Controller {
	Use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/edit', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	public function index() {
        $this->data = $this->load->language('account/edit');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/customer');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/edit', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validate()) {
				$this->model_account_customer->editCustomer(strip_tags_decode($this->request->post));
				$this->session->data['success'] = $this->language->get('text_success');
				$this->redirect($this->url->link('account/account', '', 'SSL'));
			}
		}

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_edit'), $this->url->link('account/edit'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$data_field_errors = array(
			'firstname',
			'lastname',
			'email',
			'telephone'
		);

		foreach ($data_field_errors as $data_field) {
			$this->data['error_' . $data_field] = $this->getError($data_field);
		}

		if ($this->request->server['REQUEST_METHOD'] != 'POST') {
			$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());
		}

		$data_fields = array_merge($data_field_errors, array('fax'));

		foreach ($data_fields as $data_field) {
			if (isset($this->request->post[$data_field])) {
				$this->data[$data_field] = $this->request->post[$data_field];
			} elseif (isset($customer_info)) {
				$this->data[$data_field] = strip_tags_decode($customer_info[$data_field]);
			} else {
				$this->data[$data_field] = '';
			}
		}

		$this->data['button_continue'] = $this->language->get('button_save');

		$this->data['action'] = $this->url->link('account/edit', 'customer_token=' . $this->session->data['customer_token'], 'SSL');
		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
		$this->data['addresses'] = $this->url->link('account/address', '', 'SSL');

		$this->session->data['warning'] = $this->getError('warning');

		$this->document->addScript('catalog/view/root/javascript/account.js');

		$this->template = '/template/account/edit.tpl';

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

	protected function validate() {
		if (!isset($this->request->post['firstname']) || !$this->validateStringLength($this->request->post['firstname'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $this->request->post['firstname'])) {
			$this->setError('firstname', sprintf($this->language->get('error_firstname'), 3, 32));
		}

		if (!isset($this->request->post['lastname']) || !$this->validateStringLength($this->request->post['firstname'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $this->request->post['lastname'])) {
			$this->setError('lastname', sprintf($this->language->get('error_lastname'), 3, 32));
		}

		if (!$this->validateEmail($this->request->post['email'])) {
			$this->setError('email', $this->language->get('error_email'));
		}

		if (($this->customer->getEmail() != $this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->setError('warning', $this->language->get('error_exists'));
		}

		if ((!empty($this->request->post['telephone']) && !$this->validateStringLength($this->request->post['telephone'], 0, 32)) || (!empty($this->request->post['fax']) && !$this->validateStringLength($this->request->post['fax'], 0, 32))) {
			$this->setError('warning', sprintf($this->language->get('error_length'), 32));
		}

		return !$this->hasError();
	}

}
?>
