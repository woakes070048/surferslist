<?php
class ControllerAccountQuestion extends Controller {
	use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/question', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	private function init() {
		$language = $this->load->language('account/question');
        $this->data = array_merge($this->data, $language);

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/question');
		$this->load->model('catalog/member');

		$this->setQueryParams(array('sort', 'order', 'page'));
	}

	public function index() {
		$this->init();
		$this->getList();
	}

	public function update() {
		$this->init();

		if (!isset($this->request->get['question_id'])) {
			$this->redirect($this->url->link('account/question', '', 'SSL'));
		}

    	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/question', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateForm()) {
				$this->model_account_question->editQuestion($this->request->get['question_id'], strip_tags_decode($this->request->post));

				$this->session->data['success'] = $this->language->get('text_success');

				$this->redirect($this->url->link('account/question', $this->getQueryString(), 'SSL'));
			}
		}

		$this->getForm();
  	}

	public function enable() {
		$this->init();

		if (isset($this->request->post['selected'])) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/question', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			$enabled_count = 0;

			foreach ($this->request->post['selected'] as $question_id) {
				$question_info = $this->model_account_question->getQuestion($question_id);

				if ($question_info && !$question_info['status']) {
					$this->model_account_question->enableQuestion($question_id);
					$enabled_count++;
			  	}
			}

			$this->session->data['success'] = sprintf($this->language->get('text_question_enabled'), $enabled_count);

			$this->redirect($this->url->link('account/question', $this->getQueryString(), 'SSL'));
		} else {
			$this->setError('warning', $this->language->get('error_notchecked'));
		}

		$this->getList();
	}

	public function disable() {
		$this->init();

		if (isset($this->request->post['selected'])) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/question', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			$disabled_count = 0;

		  	foreach ($this->request->post['selected'] as $question_id) {
				$question_info = $this->model_account_question->getQuestion($question_id);

				if ($question_info && $question_info['status']) {
				 	$this->model_account_question->disableQuestion($question_id);
				 	$disabled_count++;
				}
		  	}

			$this->session->data['success'] = sprintf($this->language->get('text_question_disabled'), $disabled_count);

			$this->redirect($this->url->link('account/question', $this->getQueryString(), 'SSL'));
		} else {
			$this->setError('warning', $this->language->get('error_notchecked'));
		}

		$this->getList();
	}

	public function delete() {
		$this->init();

		if (isset($this->request->post['selected'])) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/product', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateDelete()) {
				foreach ($this->request->post['selected'] as $question_id) {
					$this->model_account_question->deleteQuestion($question_id);
				}

				$this->session->data['success'] = $this->language->get('text_success');

				$this->redirect($this->url->link('account/question', $this->getQueryString(), 'SSL'));
			}
		} else {
			$this->setError('warning', $this->language->get('error_notchecked'));
		}

    	$this->getList();
  	}

	private function getList() {
		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'q.date_added';
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC';
		$limit = isset($this->request->get['limit']) && (int)$this->request->get['limit'] > 0 ? (int)$this->request->get['limit'] : 15;  // $this->config->get('config_admin_limit');
		$page = isset($this->request->get['page']) && (int)$this->request->get['page'] > 0 ? (int)$this->request->get['page'] : 1;

		$url = $this->getQueryString();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/question'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['enable'] = $this->url->link('account/question/enable', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
		$this->data['disable'] = $this->url->link('account/question/disable', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');

		$this->data['delete'] = $this->customer->getMemberPermission('inventory_enabled') ? $this->url->link('account/question/delete', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL') : '';

		$this->data['questions'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);

		$question_total = $this->model_account_question->getTotalQuestions();

		$results = $this->model_account_question->getQuestions($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('account/question/update', '&question_id=' . $result['question_id'] . $url, 'SSL')
			);

			$message = strip_tags(html_entity_decode($result['text'], ENT_QUOTES, 'UTF-8'));
			$message_short = utf8_substr($message, 0, 45);
			$message_short .= utf8_strlen($message) > 45 ? $this->language->get('text_ellipses') : '';

			$this->data['questions'][] = array(
				'question_id'  => $result['question_id'],
				'parent_id'    => $result['parent_id'],
				'member'       => $result['member'],
				'member_href'  => $result['member_id'] ? $this->url->link('product/member/info', 'member_id=' . $result['member_id'], 'SSL') : '',
				'product'      => $result['product'],
				'product_href' => $result['product_id'] ? $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url, 'SSL') : '',
				'text'         => $message_short,
				'status'       => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added'   => date($this->language->get('date_format_medium'), strtotime($result['date_added'])),
				'selected'     => isset($this->request->post['selected']) && in_array($result['question_id'], $this->request->post['selected']),
				'action'       => $action
			);
		}

		$url = $this->getQueryString(array('sort'));

		$this->data['sort_location'] = $this->url->link('account/question', '&sort=location' . $url, 'SSL');
		$this->data['sort_member'] = $this->url->link('account/question', '&sort=m1.member_account_name' . $url, 'SSL');
		$this->data['sort_product'] = $this->url->link('account/question', '&sort=pd.name' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('account/question', '&sort=q.status' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('account/question', '&sort=q.date_added' . $url, 'SSL');

		$url = $this->getQueryString(array('page'));

		$this->data['pagination'] = $this->getPagination($question_total, $page, $limit, 'account/question', '', $url);

		$this->data['button_continue'] = $this->language->get('button_back');
		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

 		$this->session->data['warning'] = $this->getError('warning');
		$this->session->data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';

		$this->document->addScript('catalog/view/root/javascript/account.js');

		$this->template = '/template/account/question_list.tpl';

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

	private function getForm() {
		$this->data['error_text'] = $this->getError('text');

		$url = $this->getQueryString();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/question'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['action'] = $this->url->link('account/question/update', 'question_id=' . $this->request->get['question_id'] . '&customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
		$this->data['cancel'] = $this->url->link('account/question', $url, 'SSL');

		if (isset($this->request->get['question_id'])) {
			$question_info = $this->model_account_question->getQuestion($this->request->get['question_id']);

			if (!$question_info) {
				$this->session->data['warning'] = $this->language->get('error_permission');
				$this->redirect($this->url->link('account/question', '', 'SSL'));
			}
		}

		$this->data['member'] = $question_info['member'];
		$this->data['member_href'] = $question_info['member_id'] ? $this->url->link('product/member/info', 'member_id=' . $question_info['member_id'], 'SSL') : '';
		$this->data['product'] = $question_info['product'];
		$this->data['product_id'] = $question_info['product_id'] ? $this->url->link('product/product', 'product_id=' . $question_info['product_id'], 'SSL') : '';

		if (isset($this->request->post['text'])) {
			$this->data['text'] = $this->request->post['text'];
		} elseif (!empty($question_info)) {
			$this->data['text'] = $question_info['text'];
		} else {
			$this->data['text'] = '';
		}

		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($question_info)) {
			$this->data['status'] = $question_info['status'];
		} else {
			$this->data['status'] = '';
		}

 		$this->session->data['warning'] = $this->getError('warning');

		$this->template = '/template/account/question_form.tpl';

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

	private function validateForm() {
		if (empty($this->request->post['text']) || !$this->validateStringLength($this->request->post['text'], 3, 500)) {
			$this->setError('text', sprintf($this->language->get('error_text'), 3, 500));
		}

		return !$this->hasError();
	}

	private function validateDelete() {
    	if (!$this->customer->getMemberPermission('inventory_enabled')) {
      		$this->setError('warning', $this->language->get('error_permission'));
    	}

		return !$this->hasError();
  	}

}
?>
