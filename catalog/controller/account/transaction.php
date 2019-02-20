<?php
class ControllerAccountTransaction extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/transaction', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	public function index() {
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		$this->data = $this->load->language('account/transaction');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_transaction'), $this->url->link('account/transaction'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->load->model('account/transaction');

		$this->data['column_amount'] = sprintf($this->language->get('column_amount'), $this->config->get('config_currency'));

		$this->data['button_continue'] = $this->language->get('button_back');

		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		$this->data['transactions'] = array();

		$data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

		$transaction_total = $this->model_account_transaction->getTotalTransactions($data);

		$results = $this->model_account_transaction->getTransactions($data);

		foreach ($results as $result) {
			$this->data['transactions'][] = array(
				'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$this->data['pagination'] = $this->getPagination($transaction_total, $page, 10, 'account/transaction');

		$this->data['total'] = $this->currency->format($this->customer->getBalance());

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->template = '/template/account/transaction.tpl';

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

}
?>
