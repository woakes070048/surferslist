<?php
class ControllerAccountForgotten extends Controller {
	use Captcha, ValidateField, CSRFToken, Admin, Contact;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->setAdmin($registry);

		$this->setCaptchaStatus($this->config->get('config_captcha_forgotten') && !$this->isAdmin());
	}

	public function index() {
		if ($this->customer->isLogged()) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

		if ($this->config->get('config_secure') && !$this->request->isSecure()) {
			$this->redirect($this->url->link('account/forgotten', '', 'SSL'), 301);
		}

		$this->data = $this->load->language('account/forgotten');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->load->language('mail/forgotten');

			$pw_token = hash_rand('md5');

			$this->model_account_customer->editToken($this->request->post['email'], $pw_token);

			// $password = generate_password(12);
			// $this->model_account_customer->editPassword($this->request->post['email'], $password);
			// $this->language->get('text_password') . "\n\n" . $password;

			$mail_sent = $this->sendEmail(array(
				'to' 		=> $this->request->post['email'],
				'from' 		=> $this->config->get('config_email'),
				'sender' 	=> $this->config->get('config_name'),
				'subject' 	=> sprintf($this->language->get('text_subject'), $this->config->get('config_name')),
				'message' 	=> sprintf($this->language->get('text_message'), $this->config->get('config_name'), $this->url->link('account/login', 'token=' . $pw_token . '&reset_password=true', 'SSL')),
				'admin'		=> true
			));

			if ($mail_sent) {
				$this->session->data['success'] = $this->language->get('text_success');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			} else if (!$this->getContactMailStatus()) {
				$this->setError('warning', $this->language->get('error_disabled_mail'));
			} else {
				$this->setError('warning', $this->language->get('error_send_mail'));
			}
		}

		$this->setCSRFToken();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_forgotten'), $this->url->link('account/forgotten'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$data_field_errors = array(
			'name'		    =>	'error_name',
			'email'			=>	'error_email',
			'warning'		=>	'error_warning',
			'captcha'       =>  'error_captcha'
		);

        foreach ($data_field_errors as $data_field => $error_name) {
            $this->data[$error_name] = $this->getError($data_field);
        }

		$data_fields = array('name', 'email', 'captcha', 'captcha_widget_id', 'g-recaptcha-response');

		foreach ($data_fields as $data_field) {
			$this->data[$data_field] = isset($this->request->post[$data_field]) ? $this->request->post[$data_field] : '';
		}

		$this->data['button_continue'] = $this->language->get('button_submit');

		$this->data['name_required'] = $this->config->get('config_reset_password_name_required');

		$this->data['captcha_enabled'] = $this->getCaptchaStatus();
		$this->data['csrf_token'] = $this->getCSRFToken();

		$this->data['action'] = $this->url->link('account/forgotten', '', 'SSL');
		$this->data['cancel'] = $this->url->link('common/home', '', 'SSL');

		$this->template = 'template/account/forgotten.tpl';

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

	protected function validateForm() {
		if (!$this->validateCaptcha()) {
			$this->setError('captcha', $this->getCaptchaError());
			$this->setError('warning', $this->getCaptchaError());
			return false;
		}

		if (!$this->validateCSRFToken()) {
			$this->setError('warning', $this->language->get('error_invalid_token'));
			return false;
		};

		if (!isset($this->request->post['email']) || !$this->validateEmail($this->request->post['email'])) {
			$this->setError('email', $this->language->get('error_email'));
		}

		if ($this->config->get('config_reset_password_name_required')) {
			if (!isset($this->request->post['name']) || !$this->validateStringLength($this->request->post['name'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $this->request->post['name'])) {
				$this->setError('name', sprintf($this->language->get('error_name'), 3, 32));
			}

			$customer_name = $this->request->post['name'];
		} else {
			$customer_name = false;
		}

		if (!$this->hasError() && !$this->model_account_customer->getTotalCustomersByEmailAndName($this->request->post['email'], $customer_name)) {
			$this->setError('warning', $this->language->get('error_reset'));
		}

		return !$this->hasError();
	}

}

