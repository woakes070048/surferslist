<?php
class ControllerAccountReview extends Controller {
	use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/review', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if (!$this->customer->validateProfile()) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}

	private function init() {
		$this->data = $this->load->language('account/review');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/review');

		$this->setQueryParams(array(
			'sort',
			'order',
			'page'
		));
	}

	public function index() {
		$this->init();
		$this->getList();
	}

	public function update() {
		$this->init();

		if (!isset($this->request->get['review_id'])) {
			$this->redirect($this->url->link('account/question', '', 'SSL'));
		}

    	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/review', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateForm()) {
				$this->model_account_review->editReview($this->request->get['review_id'], strip_tags_decode($this->request->post));

				$this->session->data['success'] = $this->language->get('text_success');

				$this->redirect($this->url->link('account/review', $this->getQueryParams(), 'SSL'));
			}
		}

		$this->getForm();
  	}

	public function enable() {
		$this->init();

		if (isset($this->request->post['selected'])) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/review', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			$enabled_count = 0;

			foreach ($this->request->post['selected'] as $review_id) {
				$review_info = $this->model_account_review->getReview($review_id);

				if ($review_info && !$review_info['status']) {
					$this->model_account_review->enableReview($review_id);
					$enabled_count++;
				}
			}

			$this->session->data['success'] = sprintf($this->language->get('text_review_enabled'), $enabled_count);

			$this->redirect($this->url->link('account/review', $this->getQueryParams(), 'SSL'));
		} else {
			$this->setError('warning', $this->language->get('error_notchecked'));
		}

		  $this->getList();
	  }

	public function disable() {
		$this->init();

		if (isset($this->request->post['selected'])) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/review', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			$disabled_count = 0;

			foreach ($this->request->post['selected'] as $review_id) {
			  $review_info = $this->model_account_review->getReview($review_id);

			  if ($review_info && $review_info['status']) {
				  $this->model_account_review->disableReview($review_id);
				  $disabled_count++;
			  }
			}

			$this->session->data['success'] = sprintf($this->language->get('text_review_disabled'), $disabled_count);

			$this->redirect($this->url->link('account/review', $this->getQueryParams(), 'SSL'));
		} else {
			$this->setError('warning', $this->language->get('error_notchecked'));
		}

		$this->getList();
	}

	public function delete() {
		$this->init();

		if (isset($this->request->post['selected'])) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/review', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateDelete()) {
				foreach ($this->request->post['selected'] as $review_id) {
					$this->model_account_review->deleteReview($review_id);
				}

				$this->session->data['success'] = $this->language->get('text_success');

				$this->redirect($this->url->link('account/review', $this->getQueryParams(), 'SSL'));
			}
		} else {
			$this->setError('warning', $this->language->get('error_notchecked'));
		}

    	$this->getList();
  	}

	private function getList() {
		$sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'r.date_added';
		$order = isset($this->request->get['order']) ? $this->request->get['order'] : 'DESC';
		$limit = isset($this->request->get['limit']) && (int)$this->request->get['limit'] > 0 ? $this->request->get['limit'] : 15;  // $this->config->get('config_admin_limit');
		$page = isset($this->request->get['page']) && (int)$this->request->get['page'] > 0 ? $this->request->get['page'] : 1;

		$url = $this->getQueryParams();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/review'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['enable'] = $this->url->link('account/review/enable', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
		$this->data['disable'] = $this->url->link('account/review/disable', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');

		$this->data['delete'] = $this->customer->getMemberPermission('inventory_enabled') ? $this->url->link('account/review/delete', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL') : '';

		$this->data['reviews'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		);

		$review_total = $this->model_account_review->getTotalReviews();

		$results = $this->model_account_review->getReviews($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('account/review/update', '&review_id=' . $result['review_id'] . $url, 'SSL')
			);

			$review = strip_tags_decode(html_entity_decode($result['text'], ENT_QUOTES, 'UTF-8'));
			$review_short = utf8_substr($review, 0, 30);
			$review_short .= utf8_strlen($review) > 30 ? $this->language->get('text_ellipses') : '';

			$this->data['reviews'][] = array(
				'review_id'  		=> $result['review_id'],
				'member'       		=> $result['member'],
				'member_href' 		=> $this->url->link('product/member/info', 'member_id=' . $result['member_id'], 'SSL'),
				'order_product'     => $result['order_product'],
				'order_product_href' => $this->url->link('product/product', 'product_id=' . $result['order_product_id'], 'SSL'),
				'rating'     		=> $result['rating'],
				'text'       		=> $review_short,
				'status'     		=> ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added' 		=> date($this->language->get('date_format_medium'), strtotime($result['date_added'])),
				'selected'   		=> isset($this->request->post['selected']) && in_array($result['review_id'], $this->request->post['selected']),
				'action'     		=> $action
			);
		}

		$url = $this->getQueryParams(array('sort'));

		$this->data['sort_member'] = $this->url->link('account/review', '&sort=m1.member_account_name' . $url, 'SSL');
		$this->data['sort_order_product'] = $this->url->link('account/review', '&sort=op.name' . $url, 'SSL');
		$this->data['sort_rating'] = $this->url->link('account/review', '&sort=r.rating' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('account/review', '&sort=r.status' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('account/review', '&sort=r.date_added' . $url, 'SSL');

		$url = $this->getQueryParams(array('page'));

		$this->data['pagination'] = $this->getPagination($review_total, $page, $limit, 'account/review', '', $url);

		$this->data['button_continue'] = $this->language->get('button_back');
		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

 		$this->session->data['warning'] = $this->getError('warning');
		$this->session->data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : '';

		$this->document->addScript('catalog/view/root/javascript/account.js');

		$this->template = '/template/account/review_list.tpl';

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
		$this->data['error_review'] = $this->getError('review');
		$this->data['error_rating'] = $this->getError('rating');

		$url = $this->getQueryParams();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/review'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['action'] = $this->url->link('account/review/update', 'review_id=' . $this->request->get['review_id'] . '&customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
		$this->data['cancel'] = $this->url->link('account/review', $url, 'SSL');

		if (isset($this->request->get['review_id'])) {
			$review_info = $this->model_account_review->getReview($this->request->get['review_id']);

			if (!$review_info) {
				$this->session->data['warning'] = $this->language->get('error_permission');
				$this->redirect($this->url->link('account/review', '', 'SSL'));
			}
		}

		$this->load->model('catalog/member');

		$this->data['member'] = $review_info['member'];
		$this->data['member_href'] = $this->url->link('product/member', 'member_id=' . $review_info['member_id'], 'SSL');
		$this->data['order_product'] = $review_info['order_product'];
		$this->data['order_product_href'] = $this->url->link('product/product', 'product_id=' . $review_info['order_product_id'], 'SSL');

		if (isset($this->request->post['text'])) {
			$this->data['text'] = $this->request->post['text'];
		} elseif (!empty($review_info)) {
			$this->data['text'] = $review_info['text'];
		} else {
			$this->data['text'] = '';
		}

		if (isset($this->request->post['rating'])) {
			$this->data['rating'] = $this->request->post['rating'];
		} elseif (!empty($review_info)) {
			$this->data['rating'] = $review_info['rating'];
		} else {
			$this->data['rating'] = '';
		}

		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($review_info)) {
			$this->data['status'] = $review_info['status'];
		} else {
			$this->data['status'] = '';
		}

 		$this->session->data['warning'] = $this->getError('warning');

		$this->template = '/template/account/review_form.tpl';

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
		if (empty($this->request->post['text']) || !$this->validateStringLength($this->request->post['text'], 25, 1000)) {
			$this->setError('review', sprintf($this->language->get('error_review'), 25, 1000));
		}

		if (empty($this->request->post['rating']) || (int)$this->request->post['rating'] < 1 || (int)$this->request->post['rating'] > 5) {
			$this->setError('rating', sprintf($this->language->get('error_rating'), 1, 5));
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
