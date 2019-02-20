<?php
class ControllerAjaxPopup extends Controller {
	use Captcha, CSRFToken, ValidateTime, Admin;

	public function __construct($registry) {
		parent::__construct($registry);

		$this->setAdmin($registry);
	}

	public function popup_contact() {
        $this->setCaptchaStatus($this->config->get('config_captcha_contact') && !$this->customer->isLogged() && !$this->isAdmin());
		$this->setCSRFToken();
		$this->setPostTime();

		$this->data = $this->load->language('common/popups');

		if (isset($this->request->get['profile_id']) || isset($this->request->get['contact_id'])) {
			$this->load->model('catalog/member');
		}

		$url = '';

		if (isset($this->request->get['listing_id'])) {
			$this->load->model('catalog/product');

			$url .= '&listing_id=' . $this->request->get['listing_id'];

			$product_data = $this->model_catalog_product->getProduct($this->request->get['listing_id']);

			if ($product_data && $product_data['status'] == '1') {
				$message = sprintf($this->language->get('text_message_listing'), html_entity_decode($product_data['name'], ENT_QUOTES, 'UTF-8'), $this->url->link('product/product', 'product_id=' . $this->request->get['listing_id'], 'SSL'));
				$this->model_catalog_product->updateViewed($this->request->get['listing_id']);
			}
		}

    	if (isset($this->request->get['profile_id'])) {
			$url .= '&profile_id=' . $this->request->get['profile_id'];

			$profile_data = $this->model_catalog_member->getMember($this->request->get['profile_id']);

			if ($profile_data && !$profile_data['customer_id']) {
				$message = sprintf($this->language->get('text_message_profile'), html_entity_decode($profile_data['member_account_name'], ENT_QUOTES, 'UTF-8'), $this->url->link('product/member/info', 'member_id=' . $this->request->get['profile_id'], 'SSL'));
				$this->model_catalog_member->updateViewed($this->request->get['profile_id']);
			}
		}

		if (isset($this->request->get['contact_id'])) {
			$url .= '&contact_id=' . $this->request->get['contact_id'];

			$member_info = $this->model_catalog_member->getMemberByCustomerId($this->request->get['contact_id']);

			if ($member_info) {
				$member_data = array(
					'member_name' => strip_tags_decode(html_entity_decode($member_info['member_account_name'], ENT_QUOTES, 'UTF-8')),
					'member_id' => $member_info['member_account_id']
				);
			} else {
				$member_data = array(
					'member_name' => strip_tags_decode(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')),
					'member_id' => 0
				);
			}
		}

		$this->data['captcha_enabled'] = $this->getCaptchaStatus();
		$this->data['csrf_token'] = $this->getCSRFToken();

		$this->data['action'] = $this->url->link('information/contact', $url . '&popup=true', 'SSL');
		$this->data['member'] = !empty($member_data) ? $member_data : array();
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['name'] = $this->customer->isLogged() ? $this->customer->getFirstName() : ($this->isAdmin() ? $this->config->get('config_name') : '');
		$this->data['email'] = $this->customer->isLogged() ? $this->customer->getEmail() : ($this->isAdmin() ? $this->config->get('config_email') : '');
		$this->data['message'] = !empty($message) ? $message : '';
		$this->data['help_unauthorized'] = !$this->isAdmin() && !$this->customer->isLogged() ? sprintf($this->language->get('help_unauthorized'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL')) : '';

		// information_id = 15 => Listing Discussions
		$this->data['text_contact_form_footer'] = sprintf($this->language->get('text_contact_form_footer'), $this->url->link('information/information', 'information_id=15', 'SSL'), $this->language->get('text_contact_listing_questions'));

		$this->template = '/template/common/popup_contact.tpl';
		$this->response->setOutput($this->render());
	}

	public function popup_login() {
		if ($this->customer->isLogged()) {
			$json = array(
				'html' => '',
				'redirect' => $this->url->link('account/account', '', 'SSL')
			);
		} else {
            $this->setCaptchaStatus($this->config->get('config_captcha_login') && !$this->isAdmin());
			$this->setCSRFToken();

			$this->data = $this->load->language('common/popups');

			$referer = isset($this->request->server['HTTP_REFERER'])
				&& (strpos($this->request->server['HTTP_REFERER'], $this->config->get('config_url')) === 0
				|| strpos($this->request->server['HTTP_REFERER'], $this->config->get('config_ssl')) === 0) ? $this->request->server['HTTP_REFERER'] : '';

			$this->data['captcha_enabled'] = $this->getCaptchaStatus();
			$this->data['csrf_token'] = $this->getCSRFToken();

			$this->data['action'] = $this->url->link('account/login', 'popup=true', 'SSL');
			$this->data['register'] = $this->url->link('account/register', '', 'SSL');
			$this->data['account_forgotten_password'] = $this->url->link('account/forgotten', '', 'SSL');

			if ($referer == $this->config->get('config_url') || $referer == $this->config->get('config_ssl')) {
				$this->data['redirect'] = $this->url->link('account/account', '', 'SSL');
			} else if (strpos($referer, $this->url->link('account/anonpost', '', 'SSL')) === 0) {
				$this->data['redirect'] = $this->url->link('account/product/insert', '', 'SSL');
			} else {
				$this->data['redirect'] = '';
			}

			$this->template = '/template/common/popup_login.tpl';

			$json = array(
				'html' => $this->render(),
				'redirect' => false
			);
		}

		$this->response->setOutput(json_encode($json));
	}

	public function popup_register() {
		if ($this->customer->isLogged()) {
			$json = array(
				'html' => '',
				'redirect' => $this->url->link('account/account', '', 'SSL')
			);
		} else {
            $this->setCaptchaStatus($this->config->get('config_captcha_register') && !$this->isAdmin());
			$this->setCSRFToken();

			$this->data = $this->load->language('common/popups');

			$this->data['captcha_enabled'] = $this->getCaptchaStatus();
			$this->data['csrf_token'] = $this->getCSRFToken();

			$this->data['action'] = $this->url->link('account/register', 'popup=true', 'SSL');
			$this->data['text_register_account_already'] = sprintf($this->language->get('text_register_account_already'), $this->url->link('account/login', '', 'SSL'));

			if ($this->config->get('config_account_id')) {
				$this->data['text_register_agree'] = $this->cache->get('information.registeragree.popup.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

				if ($this->data['text_register_agree'] === false) {
					$this->load->model('catalog/information');
					$privacy_policy_id = 3;
					$terms_of_use_id = 5; // $this->config->get('config_account_id')

					$info_terms_of_use = $this->model_catalog_information->getInformation($terms_of_use_id);
					$info_privacy_policy = $this->model_catalog_information->getInformation($privacy_policy_id);

					if ($info_terms_of_use) {
						$this->data['text_register_agree'] = sprintf($this->language->get('text_register_agree'),
							$this->url->link('information/information', 'information_id=' . $terms_of_use_id, 'SSL'), $info_terms_of_use['title'], $info_terms_of_use['title'],
							$this->url->link('information/information', 'information_id=' . $privacy_policy_id, 'SSL'), $info_privacy_policy['title'], $info_privacy_policy['title']);
					} else {
						$this->data['text_register_agree'] = '';
					}

					$this->cache->set('information.registeragree.popup.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data['text_register_agree'], 60 * 60 * 24 * 30); // 1 month cache expiration
				}
			} else {
				$this->data['text_register_agree'] = '';
			}

			$this->data['redirect'] = isset($this->request->server['HTTP_REFERER']) && ($this->request->server['HTTP_REFERER'] == $this->config->get('config_url') || $this->request->server['HTTP_REFERER'] == $this->config->get('config_ssl')) ? $this->url->link('account/account', '', 'SSL') : '';

			$this->template = '/template/common/popup_register.tpl';

			$json = array(
				'html' => $this->render(),
				'redirect' => false
			);
		}

		$this->response->setOutput(json_encode($json));
	}

}
