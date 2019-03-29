<?php
class ControllerAccountVerification extends Controller {
	use ValidateField;

	public function index() {
		$this->data = $this->load->language('account/verification');

		$this->load->model('account/customer');

		if (!isset($this->request->get['u']) || !isset($this->request->get['v'])) {
			$this->redirect($this->url->link('error/not_found', '', 'SSL'));
		}

		$customer_email = $this->validateVerification($this->request->get['u'], $this->request->get['v']);

		if ($customer_email) {
			$this->model_account_customer->deleteVerificationCode($this->request->get['u']);

			if ($this->customer->login($customer_email, '', true)) {
				$this->getChild('account/login/completeLogin');

				$this->session->data['success'] = $this->language->get('text_success');

				$this->redirect($this->url->link('account/account', '', 'SSL'));
			}
		}

		if ($this->customer->isLogged()) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_verification'), $this->url->link('account/verification', 'v=' . $this->request->get['v'] . '&u=' . $this->request->get['u']));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['warning'] = $this->getError('warning') ?: $this->language->get('text_account_not_verified');

		$this->template = 'template/account/verification.tpl';

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

	private function validateVerification($customer_id, $verification_code) {
		if (strlen($verification_code) != 32 || intval($customer_id) <= 0) {
			$this->setError('warning', $this->language->get('error_invalid_code'));
		} else {
			$customer_info = $this->model_account_customer->getVerificationCode($customer_id);

			if ($customer_info && !empty($customer_info['verification_code']) && !empty($customer_info['email'])) {
				// Check how many login attempts have been made.
				$login_info = $this->model_account_customer->getLoginAttempts($customer_info['email']);

				if ($login_info && ($login_info['total'] >= 6) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
					$this->setError('warning', $this->language->get('error_attempts'));
				} else if ($customer_info['verification_code'] != $verification_code) {
					$this->setError('warning', $this->language->get('error_invalid_code'));

					$this->model_account_customer->addLoginAttempt($customer_info['email']);
				} else {
					$this->model_account_customer->deleteLoginAttempts($customer_info['email']);
				}
			} else {
				$this->setError('warning', $this->language->get('error_invalid_code'));
			}
		}

		if (!$this->hasError()) {
			return $customer_info['email'];
		} else {
			return false;
		}
	}
}

