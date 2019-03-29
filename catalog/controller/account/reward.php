<?php
class ControllerAccountReward extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/reward', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	public function index() {
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		$this->data = $this->load->language('account/reward');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_reward'), $this->url->link('account/reward'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->load->model('account/reward');

		$this->data['button_continue'] = $this->language->get('button_back');

		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		$this->data['rewards'] = array();

		$data = array(
			'sort'  => 'date_added',
			'order' => 'DESC',
			'start' => ($page - 1) * 10,
			'limit' => 10
		);

		$reward_total = $this->model_account_reward->getTotalRewards($data);

		$results = $this->model_account_reward->getRewards($data);

		foreach ($results as $result) {
			$this->data['rewards'][] = array(
				'order_id'    => $result['order_id'],
				'points'      => $result['points'],
				'description' => $result['description'],
				'date_added'  => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'href'        => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL')
			);
		}

		$this->data['pagination'] = $this->getPagination($reward_total, $page, 10, 'account/reward');

		$this->data['total'] = (int)$this->customer->getRewardPoints();

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->template = 'template/account/reward.tpl';

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

