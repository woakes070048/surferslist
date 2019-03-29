<?php
class ControllerAccountProductViewed extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/product_viewed', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if (!$this->customer->validateMembership() || !$this->config->get('member_report')) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}

	public function index() {
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		$this->data = $this->load->language('account/product_viewed');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		$sort = isset($this->request->get['sort']) ? (string)$this->request->get['sort'] : 'p.viewed';
		$order = isset($this->request->get['order']) ? (string)$this->request->get['order'] : 'DESC';
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		$this->setQueryParams(array(
			'filter_name',
			'filter_model',
			'filter_status',
			'sort',
			'order',
			'limit'
		));

		$url = $this->getQueryString();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/product_viewed'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->load->model('account/product_viewed');

		$data = array(
			'customer_id'	  => $this->customer->getId(),
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * ($this->config->get('member_report_views_limit') ? $this->config->get('member_report_views_limit') : 12),
			'limit'           => ($this->config->get('member_report_views_limit') ? $this->config->get('member_report_views_limit') : 12)
		);

		$this->load->model('tool/image');

		$product_viewed_total = $this->model_account_product_viewed->getTotalProductsViewed($data);
		$product_views_total = $this->model_account_product_viewed->getTotalProductViews($this->customer->getId());
		$member_views_total = $this->model_account_product_viewed->getTotalMemberViews($this->customer->getId());

		$this->data['products'] = array();

		$results = $this->model_account_product_viewed->getProductsViewed($data);

		foreach ($results as $result) {
			if ($result['viewed']) {
				$percent = round($result['viewed'] / $product_views_total * 100, 2);
			} else {
				$percent = 0;
			}

			$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height'));

			$this->data['products'][] = array(
				'image'   => $image,
				'name'    => $result['name'],
				'model'   => $result['model'],
				'href'    => $this->url->link('product/product','&product_id=' . $result['product_id']),
				'status'  => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date'	  => $result['date_added'],
				'viewed'  => $result['viewed'],
				'percent' => $percent . '%'
			);
		}

		$this->data['text_member_views'] = sprintf($this->language->get('text_member_views'), number_format($member_views_total), $this->customer->getProfileUrl());

		$this->data['button_continue'] = $this->language->get('button_back');

 		$this->data['error_warning'] = $this->getError('warning');

		$url = $this->getQueryString(array('sort'));

		$this->data['sort_name'] = $this->url->link('account/product_viewed', '&sort=pd.name' . $url, 'SSL');
		$this->data['sort_model'] = $this->url->link('account/product_viewed', '&sort=p.model' . $url, 'SSL');
		$this->data['sort_date'] = $this->url->link('account/product_viewed', '&sort=p.date_added' . $url, 'SSL');
		$this->data['sort_viewed'] = $this->url->link('account/product_viewed', '&sort=p.viewed' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('account/product_viewed', '&sort=p.status' . $url, 'SSL');
		$this->data['sort_order'] = $this->url->link('account/product_viewed', '&sort=p.sort_order' . $url, 'SSL');

		$url = $this->getQueryString(array('page'));

		$this->data['pagination'] = $this->getPagination($product_viewed_total, $page, 10, 'account/product_viewed', '', $url);

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->data['filter_name'] = $filter_name;
		$this->data['filter_model'] = $filter_model;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->document->addStyle('catalog/view/root/ui/jquery-ui.min.css');
		$this->document->addScript('catalog/view/root/ui/jquery-ui.min.js');
		$this->document->addScript('catalog/view/root/javascript/account.js');

		$this->template = 'template/account/product_viewed.tpl';

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

