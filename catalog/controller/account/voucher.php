<?php
class ControllerAccountVoucher extends Controller {
	use ValidateField;

	public function index() {
		if ($this->config->get('config_secure') && !$this->request->isSecure()) {
			$this->redirect($this->url->link('account/voucher', '', 'SSL'), 301);
		}

		$this->data = $this->load->language('account/voucher');

		$this->document->setTitle($this->language->get('heading_title'));

		if (!isset($this->session->data['vouchers'])) {
			$this->session->data['vouchers'] = array();
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->session->data['vouchers'][mt_rand()] = array(
				'description'      => sprintf($this->language->get('text_for'), $this->currency->format($this->currency->convert($this->request->post['amount'], $this->currency->getCode(), $this->config->get('config_currency'))), $this->request->post['to_name']),
				'to_name'          => $this->request->post['to_name'],
				'to_email'         => $this->request->post['to_email'],
				'from_name'        => $this->request->post['from_name'],
				'from_email'       => $this->request->post['from_email'],
				'voucher_theme_id' => $this->request->post['voucher_theme_id'],
				'message'          => $this->request->post['message'],
				'amount'           => $this->currency->convert($this->request->post['amount'], $this->currency->getCode(), $this->config->get('config_currency'))
			);

			$this->redirect($this->url->link('account/voucher/success'));
		}

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_voucher'), $this->url->link('account/voucher'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['entry_amount'] = sprintf($this->language->get('entry_amount'), $this->currency->format($this->config->get('config_voucher_min')), $this->currency->format($this->config->get('config_voucher_max')));

		$data_field_errors = array(
			'warning'		=>	'error_warning',
			'to_name'		=>	'error_to_name',
			'to_email'		=>	'error_to_email',
			'from_name'		=>	'error_from_name',
			'from_email'	=>	'error_from_email',
			'theme'			=>	'error_theme',
			'amount'		=>	'error_amount'
		);

		foreach ($data_field_errors as $data_field => $error_name) {
			$this->data[$error_name] = $this->getError($data_field);
		}

		$this->data['action'] = $this->url->link('account/voucher', '', 'SSL');

		$this->data['to_name'] = isset($this->request->post['to_name']) ? $this->request->post['to_name'] : '';
		$this->data['to_email'] = isset($this->request->post['to_email']) ? $this->request->post['to_email'] : '';

		if (isset($this->request->post['from_name'])) {
			$this->data['from_name'] = $this->request->post['from_name'];
		} elseif ($this->customer->isLogged()) {
			$this->data['from_name'] = $this->customer->getFirstName() . ' '  . $this->customer->getLastName();
		} else {
			$this->data['from_name'] = '';
		}

		if (isset($this->request->post['from_email'])) {
			$this->data['from_email'] = $this->request->post['from_email'];
		} elseif ($this->customer->isLogged()) {
			$this->data['from_email'] = $this->customer->getEmail();
		} else {
			$this->data['from_email'] = '';
		}

		$this->load->model('checkout/voucher_theme');

		$this->data['voucher_themes'] = $this->model_checkout_voucher_theme->getVoucherThemes();

		if (isset($this->request->post['voucher_theme_id'])) {
			$this->data['voucher_theme_id'] = $this->request->post['voucher_theme_id'];
		} else {
			$this->data['voucher_theme_id'] = '';
		}

		if (isset($this->request->post['message'])) {
			$this->data['message'] = $this->request->post['message'];
		} else {
			$this->data['message'] = '';
		}

		if (isset($this->request->post['amount'])) {
			$this->data['amount'] = $this->request->post['amount'];
		} else {
			$this->data['amount'] = $this->currency->format($this->config->get('config_voucher_min'), '', '', false);
		}

		if (isset($this->request->post['agree'])) {
			$this->data['agree'] = $this->request->post['agree'];
		} else {
			$this->data['agree'] = false;
		}

		$this->template = '/template/account/voucher.tpl';

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

	public function success() {
		$this->data = $this->load->language('account/voucher');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/voucher'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['continue'] = $this->url->link('checkout/cart');

		$this->template = '/template/common/success.tpl';

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
		if (!isset($this->request->post['to_name']) || !$this->validateStringLength($this->request->post['to_name'], 3, 32)) {
			$this->setError('to_name', $this->language->get('error_to_name'));
		}

		if (!isset($this->request->post['to_email']) || !$this->validateEmail($this->request->post['to_email'])) {
			$this->setError('to_email', $this->language->get('error_email'));
		}

		if (!isset($this->request->post['from_name']) || !$this->validateStringLength($this->request->post['from_name'], 3, 32)) {
			$this->setError('from_name', $this->language->get('error_from_name'));
		}

		if (!isset($this->request->post['from_email']) || !$this->validateEmail($this->request->post['from_email'])) {
			$this->setError('from_email', $this->language->get('error_email'));
		}

		if (!isset($this->request->post['voucher_theme_id'])) {
			$this->setError('theme', $this->language->get('error_theme'));
		}

		if (!isset($this->request->post['amount']) || ($this->currency->convert($this->request->post['amount'], $this->currency->getCode(), $this->config->get('config_currency')) < $this->config->get('config_voucher_min')) || ($this->currency->convert($this->request->post['amount'], $this->currency->getCode(), $this->config->get('config_currency')) > $this->config->get('config_voucher_max'))) {
			$this->setError('amount', sprintf($this->language->get('error_amount'), $this->currency->format($this->config->get('config_voucher_min')), $this->currency->format($this->config->get('config_voucher_max')) . ' ' . $this->currency->getCode()));
		}

		if (!isset($this->request->post['agree'])) {
			$this->setError('warning', $this->language->get('error_agree'));
		}

		return !$this->hasError();
	}
}
?>
