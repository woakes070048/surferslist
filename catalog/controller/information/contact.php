<?php
class ControllerInformationContact extends Controller {
	use Captcha, CSRFToken, ValidateField, ValidateTime, Admin, Contact;

	private $member = array(); // stores getMember() results and called in validateForm() function

	public function __construct($registry) {
		parent::__construct($registry);

		$this->setAdmin($registry);

		$this->setCaptchaStatus($this->config->get('config_captcha_contact') && !$this->customer->isLogged() && !$this->isAdmin());
	}

	public function index() {
		$this->data = $this->load->language('information/contact');

		$this->load->model('catalog/member');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('meta_description'));
		$this->document->setKeywords($this->language->get('meta_keyword'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			if (!empty($this->member)) {
				$contact_data['email'] = $this->member['email'];
				$contact_data['recipient'] = $this->member['member_account_name'];
				$contact_data['recipient_url'] = $this->url->link('product/member/info', 'member_id=' . $this->member['member_account_id']);
			} else {
				$contact_data['email'] = $this->config->get('config_email');
				$contact_data['recipient'] = $this->config->get('config_name');
				$contact_data['recipient_url'] = $this->url->link('common/home');
			}

			$mail_sent = $this->sendContact($contact_data);

			if ($mail_sent) {
			 	$success = sprintf($this->language->get('text_message_recipient'), $contact_data['recipient_url'], strip_tags(html_entity_decode($contact_data['recipient'], ENT_QUOTES, 'UTF-8')));
			} else if (!$this->getContactMailStatus()) {
				$this->setError('warning', $this->language->get('error_disabled_mail'));
			} else {
				$this->setError('warning', $this->language->get('error_send_mail'));
			}

			if (!empty($this->request->get['popup'])) {
				$json = array(
					'status'   => 1,
					'message'  => $this->getError('warning') ?: $success,
					'captcha_widget_id' => isset($this->request->post['captcha_widget_id']) ? $this->request->post['captcha_widget_id'] : ''
				);

				$this->response->setOutput(json_encode($json));
				return;
			} else {
				if (!$this->hasError()) {
					$this->session->data['success'] = isset($success) ? $success : '';
					$this->redirect($this->url->link('information/contact'));
				}
			}
    	}

		$this->setCSRFToken();
		$this->setPostTime();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('information/contact'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

    	$this->data['text_intro'] = sprintf($this->language->get('text_intro'), $this->url->link('product/member'));
    	$this->data['text_footer'] = sprintf($this->language->get('text_footer'), $this->url->link('information/information', 'information_id=15'), $this->language->get('text_listing_questions'));  // 15 => Member Questions and Answers

		$this->session->data['warning'] = $this->getError('warning');

		$data_field_errors = array(
			'warning'		=>	'warning',
			'member'		=>	'error_member',
			'name'			=>  'error_name',
			'email'			=>	'error_email',
			'message'		=>	'error_message',
			'captcha'       =>  'error_captcha'
		);

        foreach ($data_field_errors as $data_field => $error_name) {
            $this->data[$error_name] = $this->getError($data_field);
        }

		$this->data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';

    	if (isset($this->request->get['listing_id'])) {
			$this->load->model('catalog/product');

			$product_data = $this->model_catalog_product->getProduct($this->request->get['listing_id']);

			if ($product_data && $product_data['status'] == '1') {
				$message = sprintf($this->language->get('text_message_listing'), html_entity_decode($product_data['name'], ENT_QUOTES, 'UTF-8'), $this->url->link('product/product', 'product_id=' . $this->request->get['listing_id']));
				$this->model_catalog_product->updateViewed($this->request->get['listing_id']);
			}
		}

    	if (isset($this->request->get['profile_id'])) {
			$profile_data = $this->model_catalog_member->getMember($this->request->get['profile_id']);

			if ($profile_data && !$profile_data['customer_id']) {
				$message = sprintf($this->language->get('text_message_profile'), html_entity_decode($profile_data['member_account_name'], ENT_QUOTES, 'UTF-8'), $this->url->link('product/member/info', 'member_id=' . $this->request->get['profile_id']));
				$this->model_catalog_member->updateViewed($this->request->get['profile_id']);
			}
		}

		$this->data['member'] = array();

		if (isset($this->request->post['member'])) {
			$this->data['member'] = $this->request->post['member'];
		} else if (isset($this->request->get['contact_id'])) {
			$member_info = $this->model_catalog_member->getMemberByCustomerId($this->request->get['contact_id']);

			if ($member_info) {
				$this->data['member'] = array(
					'member_name' => strip_tags(html_entity_decode($member_info['member_account_name'], ENT_QUOTES, 'UTF-8')),
					'member_id' => $member_info['member_account_id']
				);
			} else {
				$this->data['member'] = array(
					'member_name' => strip_tags(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')),
					'member_id' => 0
				);
			}
		}

		if (isset($this->request->post['name'])) {
			$this->data['name'] = $this->request->post['name'];
		} else if ($this->customer->isLogged()) {
			$this->data['name'] = $this->customer->getFirstName();
		} else if ($this->isAdmin()) {
			$this->data['name'] = $this->config->get('config_name');
		} else {
			$this->data['name'] = '';
		}

		if (isset($this->request->post['email'])) {
			$this->data['email'] = $this->request->post['email'];
		} else if ($this->customer->isLogged()) {
			$this->data['email'] = $this->customer->getEmail();
		} else if ($this->isAdmin()) {
			$this->data['email'] = $this->config->get('config_email');
		} else {
			$this->data['email'] = '';
		}

		if (isset($this->request->post['message'])) {
			$this->data['message'] = $this->request->post['message'];
		} else if (!empty($message)) {
			$this->data['message'] = $message;
		} else {
			$this->data['message'] = '';
		}

		$captcha_data_fields = array('captcha', 'captcha_widget_id', 'g-recaptcha-response');

		foreach ($captcha_data_fields as $data_field) {
			$this->data[$data_field] = isset($this->request->post[$data_field]) ? $this->request->post[$data_field] : '';
		}

		if (!empty($this->request->get['popup'])) {
			$json = array(
				'status'      => 0,
				'member_name' => isset($this->data['member']['member_name']) ? $this->data['member']['member_name'] : $this->config->get('config_name'),
				'member_id'   => isset($this->data['member']['member_id']) ? $this->data['member']['member_id'] : '',
				'name'        => $this->data['name'],
				'email'       => $this->data['email'],
				'message'     => $this->data['message'],
				'captcha_widget_id' => $this->data['captcha_widget_id'],
				'captcha'     => $this->data['g-recaptcha-response'],
				'csrf_token'  => $this->getCSRFToken()
			);

			if ($this->hasError()) {
				$json['error'] = $this->getErrors();
			}

			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->data['captcha_enabled'] = $this->getCaptchaStatus();
		$this->data['csrf_token'] = $this->getCSRFToken();

		$this->data['members'] = $this->url->link('product/member');
		$this->data['page'] = $this->url->link('information/contact');
		$this->data['action'] = $this->url->link('information/contact');

		$this->data['contact_deails'] = $this->config->get('config_contact_details');
		$this->data['contact_store'] = $this->config->get('config_name');
    	$this->data['contact_address'] = nl2br($this->config->get('config_address'));
    	$this->data['contact_email'] = sprintf($this->language->get('email_contact'), $this->config->get('config_email'));
    	$this->data['contact_telephone'] = $this->config->get('config_telephone');
    	$this->data['contact_fax'] = $this->config->get('config_fax');

		$this->data['help_unauthorized'] = !$this->isAdmin() && !$this->customer->isLogged() ? sprintf($this->language->get('help_unauthorized'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL')) : '';

		$this->document->addStyle('catalog/view/root/ui/jquery-ui.min.css');
		$this->document->addScript('catalog/view/root/ui/jquery-ui.min.js');
		$this->document->addScript('catalog/view/root/javascript/contact.js');

		$this->template = 'template/information/contact.tpl';

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

	protected function sendContact($data) {
		$to = '';
		$bcc = array();

		// profile email is bcc'd for privacy
		$bcc[] = $data['email'];

		// send to the email submitted via form, if submitter is not logged in
		if (!$this->customer->isLogged()) {
			$to = $this->request->post['email'];
		}

		// send to config email, if user has activated a profile and disabled email for sending private messaging or if member emails turned off globablly
		if (!$to && (($this->customer->hasProfile() && !$this->customer->getEmailNotifySetting('email_contact')) || !$this->config->get('member_email_customers'))) {
			$to = $this->config->get('config_email');
		}

		// send to the email provided in the form and bcc the account
		if (!$to && $this->customer->getEmail() != $this->request->post['email']) {
			$to = $this->request->post['email'];
			$bcc[] = $this->customer->getEmail();
		}

		// send to the profile's account email
		if (!$to) {
			$to = $this->customer->getEmail();
		}

		if ($this->customer->isLogged() && $this->customer->hasProfile() && $this->customer->isProfileEnabled()) {
			$poster_name = $this->customer->getProfileName();
			$poster_email = $this->customer->getProfileUrl();
		} else {
			$poster_name = $this->request->post['name'];
			$poster_email = $this->request->post['email'];
		}

		return $this->sendEmail(array(
			'to' 		=> $to,
			'from' 		=> $this->config->get('config_email_noreply'),
			'sender' 	=> $this->config->get('config_name'),
			'subject' 	=> sprintf($this->language->get('email_subject'), $this->config->get('config_name'), $poster_name),
			'message' 	=> sprintf($this->language->get('email_message'), $data['recipient'], $data['recipient_url'], $poster_name, $poster_email, $this->request->post['message']),
			'bcc' 		=> $bcc,
			'reply' 	=> $this->request->post['email']
		));
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

    	// if (empty($this->request->post['member']) || empty($this->request->post['member']['member_id'])) {
      	// 	$this->setError('member', $this->language->get('error_member'));
    	// } else
		if (!empty($this->request->post['member']['member_id'])) {
			$member_info = $this->model_catalog_member->getMember($this->request->post['member']['member_id']);

			if (empty($member_info['email'])) {
				$this->setError('member', $this->language->get('error_member_email'));
			} else {
				$this->member = $member_info;
			}
		}

    	if (!isset($this->request->post['name']) || !$this->validateStringLength($this->request->post['name'], 3, 32)) {
      		$this->setError('name', sprintf($this->language->get('error_name'), 3, 32));
    	}

    	if (!isset($this->request->post['email']) || !$this->validateEmail($this->request->post['email'])) {
      		$this->setError('email', $this->language->get('error_email'));
    	}

		if (!isset($this->request->post['message']) || !$this->validateStringLength($this->request->post['message'], 10, 1000)) {
      		$this->setError('message', sprintf($this->language->get('error_message'), 10, 1000));
    	}

		if (!$this->hasError()) {
			if ($this->customer->isLogged() || $this->isAdmin()) {
				$min_time = 5;
				$max_time = 600;
			} else {
				$min_time = 15;
				$max_time = 300;
			}

			if (!$this->validatePostTimeMin($min_time)) {
				$this->setError('warning', sprintf($this->language->get('error_too_fast'), $min_time, $this->url->link('account/login', '', 'SSL')));
			}

			if (!$this->validatePostTimeMax($max_time)) {
				$this->setError('warning', sprintf($this->language->get('error_timeout'), $min_time, $this->url->link('account/login', '', 'SSL')));
			}
		}

		if ($this->hasError() && !$this->getError('warning')) {
			$this->setError('warning', $this->language->get('error_warning'));
		}

		return !$this->hasError();
  	}

}

