<?php
class ControllerAccountRegister extends Controller {
	use Captcha, ValidateField, CSRFToken, Admin;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->setAdmin($registry);

		$this->setCaptchaStatus($this->config->get('config_captcha_register') && !$this->isAdmin());
	}

	public function index() {
		if ($this->customer->isLogged()) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

		if ($this->config->get('config_secure') && !$this->request->isSecure()) {
			$this->redirect($this->url->link('account/register', '', 'SSL'), 301);
		}

		$this->data = $this->load->language('account/register');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription(sprintf($this->language->get('text_meta_description'), $this->config->get('config_name')));
		$this->document->setKeywords($this->language->get('text_meta_keyword'));

		$this->load->model('account/customer');
		$this->load->model('account/customer_group');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$data = $this->request->post;
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');

			if (isset($data['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($data['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$data['customer_group_id'] = $data['customer_group_id'];
			}

			$customer_group = $this->model_account_customer_group->getCustomerGroup($data['customer_group_id']);
			$approval_required = isset($customer_group['approval']) ? $customer_group['approval'] : 1;
			$data['approval_required'] = $approval_required;

			$this->model_account_customer->addCustomer($data);

			$this->customer->login($this->request->post['email'], $this->request->post['password']);

			unset($this->session->data['guest']);

			if (!empty($this->request->get['popup'])) {
				if (!$approval_required) {
					$success_message = sprintf($this->language->get('text_message'), $this->url->link('account/member', '', 'SSL'), $this->url->link('account/product', '', 'SSL'));
				} else {
					$this->load->language('account/success');
					$success_message = sprintf($this->language->get('text_approval'), $this->config->get('config_name'));
				}

				$json = array(
					'status'   => 1,
					'message'  => $success_message,
					'captcha_widget_id' => isset($this->request->post['captcha_widget_id']) ? $this->request->post['captcha_widget_id'] : '',
					'redirect' => (isset($this->request->post['redirect']) &&
						(strpos($this->request->post['redirect'], $this->config->get('config_url')) === 0
						|| strpos($this->request->post['redirect'], $this->config->get('config_ssl')) === 0))
							? str_replace('&amp;', '&', $this->request->post['redirect'])
							: false
				);

				$this->response->setOutput(json_encode($json));
				return;
			}

			if (!$approval_required) {
				$this->session->data['success'] = sprintf($this->language->get('text_message'), $this->url->link('account/member', '', 'SSL'), $this->url->link('account/product', '', 'SSL'));
				$this->redirect($this->url->link('account/account'));
			} else {
				$this->load->language('account/success');
				$this->session->data['success'] = sprintf($this->language->get('text_approval'), $this->config->get('config_name'));
				$this->redirect($this->url->link('account/login'));
			}
		}

		$this->setCSRFToken();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_register'), $this->url->link('account/register'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['text_account_already'] = sprintf($this->language->get('text_account_already'), $this->url->link('account/login', '', 'SSL'));
		$this->data['help_password_requirements'] = sprintf($this->language->get('error_password'), 8);

		$this->data['button_continue'] = $this->language->get('button_register');

		$data_field_errors = array('warning', 'firstname', 'lastname', 'email', 'password', 'captcha');

		foreach ($data_field_errors as $data_field) {
			$this->data['error_' . $data_field] = $this->getError($data_field);
		}

		$data_fields = array('firstname', 'lastname', 'email', 'password', 'captcha', 'customer_group_id', 'captcha_widget_id', 'g-recaptcha-response'); // 'agree'

		foreach ($data_fields as $data_field) {
			$this->data[$data_field] = isset($this->request->post[$data_field]) ? $this->request->post[$data_field] : '';
		}

		$this->data['customer_groups'] = array();

		if (is_array($this->config->get('config_customer_group_display'))) {
			$customer_groups = $this->model_account_customer_group->getCustomerGroups();

			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$this->data['customer_groups'][] = $customer_group;
				}
			}
		}

		if ($this->config->get('config_account_id')) {
			$this->data['text_agree'] = $this->cache->get('information.registeragree.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

			if ($this->data['text_agree'] === false) {
				$this->load->model('catalog/information');
				
				$privacy_policy_id = $this->config->get('config_privacy_policy_id') ?: $this->config->get('config_account_id');
				$terms_of_use_id = $this->config->get('config_terms_of_use_id') ?: $this->config->get('config_account_id');

				$info_terms_of_use = $this->model_catalog_information->getInformation($terms_of_use_id);
				$info_privacy_policy = $this->model_catalog_information->getInformation($privacy_policy_id);

				if ($info_terms_of_use) {
					$this->data['text_agree'] = sprintf($this->language->get('text_agree'),
						$this->url->link('information/information/info', 'information_id=' . $terms_of_use_id, 'SSL'), $info_terms_of_use['title'], $info_terms_of_use['title'],
						$this->url->link('information/information/info', 'information_id=' . $privacy_policy_id, 'SSL'), $info_privacy_policy['title'], $info_privacy_policy['title']);
				} else {
					$this->data['text_agree'] = '';
				}

				$this->cache->set('information.registeragree.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data['text_agree'], 60 * 60 * 24 * 30); // 1 month cache expiration
			}
		} else {
			$this->data['text_agree'] = '';
		}

		if (!empty($this->request->get['popup'])) {
			$json = array(
				'status'      		=> 0,
				'message'     		=> '',
				'captcha_enabled' 	=> $this->getCaptchaStatus(),
				'captcha_widget_id' => $this->data['captcha_widget_id'],
				'captcha'     		=> $this->data['g-recaptcha-response'],
				'redirect'    		=> false,
				'csrf_token'  		=> $this->getCSRFToken()
			);

			if ($this->hasError()) {
				$json['error'] = $this->hasError();
			}

			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->data['captcha_enabled'] = $this->getCaptchaStatus();
		$this->data['csrf_token'] = $this->getCSRFToken();

		$this->data['action'] = $this->url->link('account/register', '', 'SSL');
		$this->data['page'] = $this->url->link('account/register', '', 'SSL');

		$this->document->addScript('catalog/view/root/javascript/login.js');

		$this->template = 'template/account/register.tpl';

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

			if (empty($this->request->get['popup'])) {
				$this->setError('warning', $this->getCaptchaError());
			}

			return false;
		}

		if (!$this->validateCSRFToken()) {
			$this->setError('warning', $this->language->get('error_invalid_token'));
			return false;
		};

		if (!isset($this->request->post['firstname']) || !$this->validateStringLength($this->request->post['firstname'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $this->request->post['firstname'])) {
			$this->setError('firstname', sprintf($this->language->get('error_firstname'), 3, 32));
		}

		if (!isset($this->request->post['lastname']) || !$this->validateStringLength($this->request->post['lastname'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $this->request->post['lastname'])) {
			$this->setError('lastname', sprintf($this->language->get('error_lastname'), 3, 32));
		}

		if (!isset($this->request->post['email']) || !$this->validateEmail($this->request->post['email'])) {
			$this->setError('email', $this->language->get('error_email'));
		}

		if (isset($this->request->post['email']) && $this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$this->setError('warning', $this->language->get('error_exists'));
		}

		if (!isset($this->request->post['password']) || !$this->validatePassword($this->request->post['password'], 8)) {
			$this->setError('password', sprintf($this->language->get('error_password'), 8));
		}

		return !$this->hasError();
	}
}
?>
