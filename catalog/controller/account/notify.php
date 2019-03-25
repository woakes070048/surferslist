<?php
class ControllerAccountNotify extends Controller {
	use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/notify', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if (!$this->config->get('member_status')) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}

  	public function index() {
		$this->data = $this->load->language('account/notify');

		if ($this->customer->hasProfile() && !$this->customer->isProfileEnabled()) {
			$this->session->data['warning'] = $this->language->get('error_member_disabled');
			$this->session->data['redirect'] = $this->url->link('account/member', '', 'SSL');
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['notifications'] = array();

		foreach ($this->customer->getEmailNotifySettings() as $key => $value) {
			$this->data['notifications'][] = array(
				'label' => $this->language->get("entry_notify_{$key}"),
				'help' => $this->language->get("help_notify_{$key}"),
				'key' => $key,
				'value' => !empty($value) ? 1 : 0
			);
		}

    	if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/notify', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

		    $this->load->model('account/notify');
			$this->model_account_notify->editMemberNotifications($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success_edit');

			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_account_notify'), $this->url->link('account/notify'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['activated'] = $this->customer->hasProfile();
		$this->data['enabled'] = $this->customer->isProfileEnabled();

		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
    	$this->data['action'] = $this->url->link('account/notify', 'customer_token=' . $this->session->data['customer_token'], 'SSL');

		$this->session->data['warning'] = $this->getError('warning');

		$this->document->addScript('catalog/view/root/javascript/account.js');

		$this->template = 'template/account/notify.tpl';

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
}
?>
