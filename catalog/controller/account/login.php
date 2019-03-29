<?php
class ControllerAccountLogin extends Controller {
	use Captcha, ValidateField, CSRFToken, Admin;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->setAdmin($registry);

		$this->setCaptchaStatus($this->config->get('config_captcha_login') && !$this->isAdmin());
	}

	public function index() {
		if ($this->config->get('config_secure') && !$this->request->isSecure()) {
			$this->redirect($this->url->link('account/login', '', 'SSL'), 301);
		}

		$this->load->model('account/customer');
		$this->data = $this->load->language('account/login');

		// Login override for password reset and admin users
		if (!empty($this->request->get['token'])) {
			$this->customer->logout();
			$this->cart->clear();
			$this->clearSessionState();

			if ($this->checkLoginAttempts()) {
				$this->setError('warning', $this->language->get('error_attempts'));
			} else {
				$customer_info = $this->model_account_customer->getCustomerByToken($this->request->get['token']);

				if (!$customer_info) {
					$this->model_account_customer->addLoginAttempt();
					$this->setError('warning', $this->language->get('error_login_token'));
					sleep(5);
				} else {
					// clear account verification code if password reset requested for enabled but non-verified account
					if (!empty($this->request->get['reset_password']) && $customer_info['status'] && !$customer_info['approved']) {
						$this->model_account_customer->deleteVerificationCode($customer_info['customer_id']);
					}

					if ($this->customer->login($customer_info['email'], '', true)) {
						$this->completeLogin();

						if (!empty($this->request->get['reset_password'])) {
							$this->redirect($this->url->link('account/password', '', 'SSL'));
						} else {
							$this->redirect($this->url->link('account/account', '', 'SSL'));
						}
					} else {
						$this->setError('warning', sprintf($this->language->get('error_login_fail'), $this->url->link('information/contact')));
					}
				}
			}
		}

		if ($this->customer->isLogged()) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$referer = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : '';

		$redirect_to = (isset($this->request->post['redirect'])
			&& (strpos($this->request->post['redirect'], $this->config->get('config_url')) === 0
			|| strpos($this->request->post['redirect'], $this->config->get('config_ssl')) === 0))
				? str_replace('&amp;', '&', $this->request->post['redirect'])
				: false;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateLogin()) {
			unset($this->session->data['guest']);

			$this->completeLogin();

			if (!empty($this->request->get['popup'])) {
				$json = array(
					'status'   => 1,
					'message'  => $this->language->get('success_login'),
					'captcha_widget_id' => isset($this->request->post['captcha_widget_id']) ? $this->request->post['captcha_widget_id'] : '',
					'redirect' => $redirect_to
				);

				$this->response->setOutput(json_encode($json));
				return;
			} else if ($redirect_to) {
				$this->redirect($redirect_to);
			} else  {
				$this->redirect($this->url->link('account/account', '', 'SSL'));
			}
		}

		$this->setCSRFToken();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_login'), $this->url->link('account/login'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		if (isset($this->session->data['warning'])) {
			$this->data['error_warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} else {
			$this->data['error_warning'] = $this->getError('warning');
		}

		$this->data['error_captcha'] = $this->getError('captcha');

		if ($redirect_to) {
			$this->data['redirect'] = $redirect_to;
		} else if (isset($this->session->data['redirect'])) {
			$this->data['redirect'] = $this->session->data['redirect'];
			unset($this->session->data['redirect']);
		} else if ($referer) {
			if ($referer == $this->config->get('config_url') || $referer == $this->config->get('config_ssl')) {
				$this->data['redirect'] = $this->url->link('account/account', '', 'SSL');
			} else if (strpos($referer, $this->url->link('account/anonpost', '', 'SSL')) === 0) {
				$this->data['redirect'] = $this->url->link('account/product/insert', '', 'SSL');
			} else {
				$this->data['redirect'] = $referer;
			}
		} else {
			$this->data['redirect'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$data_fields = array('email', 'password', 'captcha', 'captcha_widget_id', 'g-recaptcha-response');

		foreach ($data_fields as $data_field) {
			$this->data[$data_field] = isset($this->request->post[$data_field]) ? $this->request->post[$data_field] : '';
		}

		if (!empty($this->request->get['popup'])) {
			$json = array(
				'status'   			=> 0,
				'message'  			=> $this->data['error_warning'],
				'captcha_enabled' 	=> $this->getCaptchaStatus(),
				'captcha_widget_id' => $this->data['captcha_widget_id'],
				'captcha'     		=> $this->data['g-recaptcha-response'],
				'redirect' 			=> $this->data['redirect'],
				'csrf_token'  		=> $this->getCSRFToken()
			);

			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->data['captcha_enabled'] = $this->getCaptchaStatus();
		$this->data['csrf_token'] = $this->getCSRFToken();

		$this->data['page'] = $this->url->link('account/login', '', 'SSL');
		$this->data['action'] = $this->url->link('account/login', '', 'SSL');
		$this->data['register'] = $this->url->link('account/register', '', 'SSL');
		$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');

		$this->document->addScript('catalog/view/root/javascript/login.js');

		$this->template = 'template/account/login.tpl';

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

	public function login_social() {
		$this->load->language('account/login');

		if ($this->request->server['REQUEST_METHOD'] != 'POST' || (!$this->request->checkReferer($this->config->get('config_url')) && !$this->request->checkReferer($this->config->get('config_ssl')))) {
			$this->response->setOutput(json_encode(array('error' => $this->language->get('error_social_request_invalid'))));
			return;
		}

		if ($this->customer->isLogged()) {
			$this->response->setOutput(json_encode(array(
				'success'	=> true,
				'message'	=> $this->language->get('success_login'),
				'redirect'	=> $this->url->link('account/account', '', 'SSL')
			)));
			return;
		}

		$json = array();

		$this->load->model('account/customer');

		$data = array(
			'email'			=> !empty($this->request->post['email']) ? $this->request->post['email'] : '',
			'firstname'		=> !empty($this->request->post['first_name']) ? $this->request->post['first_name'] : '',
			'lastname'		=> !empty($this->request->post['last_name']) ? $this->request->post['last_name'] : '',
			'user_id'		=> !empty($this->request->post['userID']) ? $this->request->post['userID'] : '',
			'token'			=> !empty($this->request->post['accessToken']) ? $this->request->post['accessToken'] : '',
			'expires'		=> !empty($this->request->post['expiresIn']) ? $this->request->post['expiresIn'] : '',
			'provider'		=> !empty($this->request->post['social']) ? $this->request->post['social'] : '',
			'signature'		=> !empty($this->request->post['signedRequest']) ? $this->request->post['signedRequest'] : ''
		);

		if ($this->validateSocialLogin($data)) {
			// check if this email already registered
			$customer_info = $this->model_account_customer->getCustomerSocialByEmail($data['email'], $data['provider']);

			if (!empty($customer_info)) {
				// account with email exists

				// validate account is approved and not disabled
				if (!$customer_info['approved']) {
					$json['error'] = $this->language->get('error_approved');
				}

				if (!$customer_info['account_status']) {
					$json['error'] = sprintf($this->language->get('error_status'), $this->url->link('information/contact'));
				}

				if (!$json) {
					// use token to check if a social account is linked
					if (empty($customer_info['user_id'])) {
						// no social link yet, so create it
						$this->model_account_customer->addSocialLogin($customer_info['customer_id'], $data);
					} else if (!$customer_info['social_status']) {
						$json['error'] = sprintf($this->language->get('error_social_status'), ucwords($data['provider']), $this->url->link('information/contact', '', 'SSL'));
					} else if ($customer_info['user_id'] != $data['user_id']) {
						$this->model_account_customer->addLoginAttempt($data['email']);
						$json['error'] = sprintf($this->language->get('error_social_login_mismatch'), $this->url->link('information/contact'));
					} else {
						// valid account, update social token
						$this->model_account_customer->updateSocialLoginToken($customer_info['customer_id'], $data);
					}

					if (!$json) {
						if ($this->customer->login($customer_info['email'], '', true)) {
							unset($this->session->data['guest']);

							$this->model_account_customer->deleteLoginAttempts($data['email']);
							$this->model_account_customer->updateSocialLoginCount($customer_info['customer_id'], $data);

							$this->completeLogin();

							$json = array(
								'success'   => true,
								'message'  => $this->language->get('success_login'),
								'redirect' => (isset($data['redirect']) && (strpos($data['redirect'], $this->config->get('config_url')) === 0 || strpos($data['redirect'], $this->config->get('config_ssl')) === 0)) ? str_replace('&amp;', '&', $data['redirect']) : false
							);
						} else {
							$json['error'] = sprintf($this->language->get('error_login_fail'), $this->url->link('information/contact', '', 'SSL'));
						}
					}
				}
			} else {
				$data = array(
					'email' 			=> $data['email'],
					'firstname' 		=> $data['firstname'],
					'lastname' 			=> $data['lastname'],
					'password' 			=> generate_password(20),
					'approval_required' => 0,
					'customer_group_id' => $this->config->get('social_login_customer_group_id') ?: $this->config->get('config_customer_group_id'),
					'user_id' 			=> $data['user_id'],
					'token' 			=> $data['token'],
					'token_expires' 	=> $data['expires'],
					'provider' 			=> $data['provider']
				);

				$customer_id = $this->model_account_customer->addCustomer($data);

				if ($customer_id && $this->customer->login($data['email'], '', true)) {
					unset($this->session->data['guest']);

					$this->model_account_customer->updateSocialLoginCount($customer_id, $data);

					$this->completeLogin();

					$json = array(
						'success'  => true,
						'message'  => sprintf($this->language->get('success_register'), $this->url->link('account/member', '', 'SSL'), $this->url->link('account/product', '', 'SSL')),
						'redirect' => (isset($data['redirect']) && (strpos($data['redirect'], $this->config->get('config_url')) === 0 || strpos($data['redirect'], $this->config->get('config_ssl')) === 0)) ? str_replace('&amp;', '&', $data['redirect']) : $this->url->link('account/account', '', 'SSL')
					);
				} else {
					$json['error'] = sprintf($this->language->get('error_login_fail'), $this->url->link('information/contact'));
				}
			}
		} else {
			$json['error'] = $this->language->get('error_social_login_invalid') . $this->getError('warning');
		}

		$this->response->setOutput(json_encode($json));
	}

	protected function completeLogin() {
		if (!$this->customer->isLogged()) {
			return;
		}

		$this->load->model('account/address');

		$address_info = $this->model_account_address->getAddress($this->customer->getAddressId());

		if ($address_info) {
			$this->session->data['shipping_country_id'] = $address_info['country_id'];
			$this->session->data['shipping_country_iso_code_3'] = $this->getCountryCodeById($address_info['country_id']);
			// $this->session->data['shipping_zone_id'] = $address_info['zone_id'];
			// $this->session->data['shipping_location'] = $address_info['city'];
			$this->session->data['shipping_postcode'] = $address_info['postcode'];
			$this->session->data['payment_country_id'] = $address_info['country_id'];
			$this->session->data['payment_zone_id'] = $address_info['zone_id'];
			$this->session->data['payment_location'] = $address_info['city'];
		} else if ($this->customer->hasProfile()) {
			$this->session->data['shipping_country_id'] = $this->customer->getMemberCountryId();
			$this->session->data['shipping_country_iso_code_3'] = $this->getCountryCodeById($this->customer->getMemberCountryId());
			// $this->session->data['shipping_zone_id'] = $this->customer->getMemberZoneId();
			// $this->session->data['shipping_location'] = $this->customer->getMemberCity();
			$this->session->data['payment_country_id'] = $this->customer->getMemberCountryId();
			$this->session->data['payment_zone_id'] = $this->customer->getMemberZoneId();
			$this->session->data['payment_location'] = $this->customer->getMemberCity();
		} else {
			unset($this->session->data['shipping_country_id']);
			unset($this->session->data['shipping_country_iso_code_3']);
			unset($this->session->data['shipping_zone_id']);
			// unset($this->session->data['shipping_location']);
			unset($this->session->data['shipping_postcode']);
			unset($this->session->data['payment_country_id']);
			unset($this->session->data['payment_zone_id']);
			unset($this->session->data['payment_location']);
		}

		unset($this->session->data['shipping_location']);

		// if ($this->config->get('config_tax_customer') == 'payment') {
		// 	$this->session->data['payment_address'] = $address_info;
		// }
		//
		// if ($this->config->get('config_tax_customer') == 'shipping') {
		// 	$this->session->data['shipping_address'] = $address_info;
		// }
	}

	protected function checkLoginAttempts($email = "") {
		$login_info = $this->model_account_customer->getLoginAttempts($email);

		return ($login_info && ($login_info['total'] >= 6) && strtotime('-1 hour') < strtotime($login_info['date_modified']));
	}

	protected function validateSocialLogin($data) {
		if (!$this->validateCSRFToken()) {
			$this->setError('warning', $this->language->get('error_invalid_token'));
			return false;
		};

		$social_providers = array('facebook');

		if (!isset($data['firstname']) || !$this->validateStringLength($data['firstname'], 1, 32) || !preg_match('/^[a-zA-Z- ]*$/', $data['firstname'])) {
			$this->setError('warning', $this->language->get('error_firstname'));
		}

		if (!isset($data['lastname']) || !$this->validateStringLength($data['lastname'], 3, 32) || !preg_match('/^[a-zA-Z- ]*$/', $data['lastname'])) {
			$this->setError('warning', $this->language->get('error_lastname'));
		}

		if (!isset($data['email']) || !$this->validateEmail($data['email'])) {
			$this->setError('warning', $this->language->get('error_email'));
		}

		if (empty($data['user_id']) || empty($data['token']) || empty($data['expires']) || empty($data['provider']) || !in_array($data['provider'], $social_providers)) {
			$this->setError('warning', $this->language->get('error_social_login_incomplete'));
		}

		if (!$this->hasError()) {
			if ($this->checkLoginAttempts($data['email'])) {
				$this->setError('warning', $this->language->get('error_attempts'));
			}
		}

		return !$this->hasError();
	}

	protected function getCountryCodeById($shipping_country_id) {
		$this->load->model('localisation/country');

		$country = $this->model_localisation_country->getCountry($shipping_country_id);

		return $country ? strtoupper($country['iso_code_3']) : false;
	}

	protected function validateLogin() {
		if (!$this->validateCaptcha()) {
			$this->setError('captcha', $this->getCaptchaError());
			$this->setError('warning', $this->getCaptchaError());
			return false;
		}

		if (!$this->validateCSRFToken()) {
			$this->setError('warning', $this->language->get('error_invalid_token'));
			return false;
		};

		// check both credentials are supplied and email is of valid form
		if (empty($this->request->post['email']) || empty($this->request->post['password'])) {
			$this->setError('warning', $this->language->get('error_login_empty'));
		} else if (!$this->validateEmail($this->request->post['email'])) {
			$this->setError('warning', $this->language->get('error_email'));
		}

		// check how many login attempts have been made using this email address
		if (!$this->hasError()) {
			if ($this->checkLoginAttempts($this->request->post['email'])) {
				$this->setError('warning', $this->language->get('error_attempts'));
			}
		}

		// check if account exists but has not yet been approved (validated) or is disabled
		if (!$this->hasError()) {
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

			if ($customer_info && !$customer_info['approved']) {
				$this->setError('warning', $this->language->get('error_approved'));
			}

			if ($customer_info && !$customer_info['status']) {
				$this->setError('warning', sprintf($this->language->get('error_status'), $this->url->link('information/contact', '', 'SSL')));
			}
		}

		// finally, try to log in with credentials provided
		// login attempts are tracked for valid emails not linked to any account, as well, to thwart email phishing attempts
		if (!$this->hasError()) {
			if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
				$this->setError('warning', $this->language->get('error_login'));

				$this->model_account_customer->addLoginAttempt($this->request->post['email']);
			} else {
				$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
			}
		}

		return !$this->hasError();
	}

	protected function clearSessionState() {
		$session_fields = array(
			'wishlist',
			'shipping_address_id',
			'shipping_country_id',
			'shipping_country_iso_code_3',
			'shipping_zone_id',
			'shipping_location',
			'shipping_postcode',
			'shipping_method',
			'shipping_methods',
			'payment_address_id',
			'payment_country_id',
			'payment_zone_id',
			'payment_location',
			'payment_method',
			'payment_methods',
			'comment',
			'order_id',
			'coupon',
			'reward',
			'voucher',
			'vouchers'
		);

		foreach ($session_fields as $session_field) {
			unset($this->session->data[$session_field]);
		}
	}

}
