<?php
class ControllerAccountReturn extends Controller {
	use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/return', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	public function index() {
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		$this->data = $this->load->language('account/return');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/return'));

		$url = isset($this->request->get['page']) ? '&page=' . (int)$this->request->get['page'] : '';

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['button_continue'] = $this->language->get('button_back');

		$this->load->model('account/return');

		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		$this->data['returns'] = array();

		$return_total = $this->model_account_return->getTotalReturns();

		$results = $this->model_account_return->getReturns(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$this->data['returns'][] = array(
				'return_id'  => $result['return_id'],
				'order_id'   => $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'href'       => $this->url->link('account/return/info', 'return_id=' . $result['return_id'] . $url, 'SSL')
			);
		}

		$this->data['pagination'] = $this->getPagination($return_total, $page, 10, 'account/return');

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->document->addScript('catalog/view/root/javascript/account.js');

		$this->template = '/template/account/return_list.tpl';

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

	public function info() {
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		if (empty($this->request->get['return_id'])) {
			return false;
		}

		$return_id = (int)$this->request->get['return_id'];

		$this->data = $this->load->language('account/return');

		$this->load->model('account/return');

		$return_info = $this->model_account_return->getReturn($return_id);

		if ($return_info) {
			$this->document->setTitle($this->language->get('text_return'));

			$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
			$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
			$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/return'));
			$this->addBreadcrumb($this->language->get('text_return'), $this->url->link('account/return/info', 'return_id=' . $this->request->get['return_id'], 'SSL'));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

			$url = isset($this->request->get['page']) ? '&page=' . (int)$this->request->get['page'] : '';

			$this->data['heading_title'] = $this->language->get('text_return');
			$this->data['button_continue'] = $this->language->get('button_back');

			$this->data['return_id'] = $return_info['return_id'];
			$this->data['order_id'] = $return_info['order_id'];
			$this->data['date_ordered'] = date($this->language->get('date_format_short'), strtotime($return_info['date_ordered']));
			$this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($return_info['date_added']));
			$this->data['firstname'] = $return_info['firstname'];
			$this->data['lastname'] = $return_info['lastname'];
			$this->data['email'] = $return_info['email'];
			$this->data['telephone'] = $return_info['telephone'];
			$this->data['product'] = $return_info['product'];
			$this->data['model'] = $return_info['model'];
			$this->data['quantity'] = $return_info['quantity'];
			$this->data['reason'] = $return_info['reason'];
			$this->data['opened'] = $return_info['opened'] ? $this->language->get('text_yes') : $this->language->get('text_no');
			$this->data['comment'] = nl2br($return_info['comment']);
			$this->data['action'] = $return_info['action'];

			$this->data['histories'] = array();

			$results = $this->model_account_return->getReturnHistories($this->request->get['return_id']);

			foreach ($results as $result) {
				$this->data['histories'][] = array(
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'status'     => $result['status'],
					'comment'    => nl2br($result['comment'])
				);
			}

			$this->data['continue'] = $this->url->link('account/return', $url, 'SSL');

			$this->template = '/template/account/return_info.tpl';

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);

			$this->response->setOutput($this->render());
		} else {
			$this->document->setTitle($this->language->get('text_return'));

			$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
			$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
			$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/return'));
			$this->addBreadcrumb($this->language->get('text_return'), $this->url->link('account/return/info', 'return_id=' . $return_id, 'SSL'));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

			$url = isset($this->request->get['page']) ? '&page=' . (int)$this->request->get['page'] : '';

			$this->data['heading_title'] = $this->language->get('text_return');
			$this->data['button_continue'] = $this->language->get('button_back');

			$this->data['search'] = $this->url->link('product/search', '', 'SSL');
			$this->data['continue'] = $this->url->link('account/return', '', 'SSL');

			$this->template = '/template/error/not_found.tpl';

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

	public function insert() {
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		$this->data = $this->load->language('account/return');

		$this->load->model('account/return');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_account_return->addReturn($this->request->post);
			$this->redirect($this->url->link('account/return/success', '', 'SSL'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/return/insert'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['button_continue'] = $this->language->get('button_save');

		$data_field_errors = array(
			'warning',
			'order_id',
			'firstname',
			'lastname',
			'email',
			'telephone',
			'product',
			'model',
			'reason',
			'captcha'
		);

		foreach ($data_field_errors as $data_field) {
			$this->data['error_' . $data_field] = $this->getError($data_field);
		}

		$this->data['action'] = $this->url->link('account/return/insert', '', 'SSL');

		$this->load->model('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_info = $this->model_account_order->getOrder($this->request->get['order_id']);
		}

		$this->load->model('catalog/product');

		if (isset($this->request->get['product_id'])) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}

		if (isset($this->request->post['order_no'])) {
			$this->data['order_id'] = $this->request->post['order_no'];
		} elseif (isset($this->request->post['order_id'])) {
			$this->data['order_id'] = $this->request->post['order_id'];
		} elseif (!empty($order_info)) {
			$this->data['order_id'] = $order_info['order_no'] ?: $order_info['order_id'];
		} else {
			$this->data['order_id'] = '';
		}

		if (isset($this->request->post['date_ordered'])) {
			$this->data['date_ordered'] = $this->request->post['date_ordered'];
		} elseif (!empty($order_info)) {
			$this->data['date_ordered'] = date('Y-m-d', strtotime($order_info['date_added']));
		} else {
			$this->data['date_ordered'] = '';
		}

		if (isset($this->request->post['firstname'])) {
			$this->data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($order_info)) {
			$this->data['firstname'] = $order_info['firstname'];
		} else {
			$this->data['firstname'] = $this->customer->getFirstName();
		}

		if (isset($this->request->post['lastname'])) {
			$this->data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($order_info)) {
			$this->data['lastname'] = $order_info['lastname'];
		} else {
			$this->data['lastname'] = $this->customer->getLastName();
		}

		if (isset($this->request->post['email'])) {
			$this->data['email'] = $this->request->post['email'];
		} elseif (!empty($order_info)) {
			$this->data['email'] = $order_info['email'];
		} else {
			$this->data['email'] = $this->customer->getEmail();
		}

		if (isset($this->request->post['telephone'])) {
			$this->data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($order_info)) {
			$this->data['telephone'] = $order_info['telephone'];
		} else {
			$this->data['telephone'] = $this->customer->getTelephone();
		}

		if (isset($this->request->post['product'])) {
			$this->data['product'] = $this->request->post['product'];
		} elseif (!empty($product_info)) {
			$this->data['product'] = $product_info['name'];
		} else {
			$this->data['product'] = '';
		}

		if (isset($this->request->post['model'])) {
			$this->data['model'] = $this->request->post['model'];
		} elseif (!empty($product_info)) {
			$this->data['model'] = $product_info['model'];
		} else {
			$this->data['model'] = '';
		}

		if (isset($this->request->post['quantity'])) {
			$this->data['quantity'] = $this->request->post['quantity'];
		} else {
			$this->data['quantity'] = 1;
		}

		if (isset($this->request->post['opened'])) {
			$this->data['opened'] = $this->request->post['opened'];
		} else {
			$this->data['opened'] = false;
		}

		if (isset($this->request->post['return_reason_id'])) {
			$this->data['return_reason_id'] = $this->request->post['return_reason_id'];
		} else {
			$this->data['return_reason_id'] = '';
		}

		$this->load->model('localisation/return_reason');

		$this->data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasons();

		if (isset($this->request->post['comment'])) {
			$this->data['comment'] = $this->request->post['comment'];
		} else {
			$this->data['comment'] = '';
		}

		if (isset($this->request->post['captcha'])) {
			$this->data['captcha'] = $this->request->post['captcha'];
		} else {
			$this->data['captcha'] = '';
		}

		if ($this->config->get('config_return_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_return_id'));

			if ($information_info) {
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_return_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}

		if (isset($this->request->post['agree'])) {
			$this->data['agree'] = $this->request->post['agree'];
		} else {
			$this->data['agree'] = false;
		}

		$this->data['back'] = $this->url->link('account/account', '', 'SSL');

		$this->document->addScript('catalog/view/root/ui/jquery-ui-timepicker-addon.js');

		$this->template = '/template/account/return_form.tpl';

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
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		$this->data = $this->load->language('account/return');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/return'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['continue'] = $this->url->link('common/home');

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
		if (!$this->request->post['order_id']) {
			$this->setError('order_id', $this->language->get('error_order_id'));
		}

		if (!isset($this->request->post['firstname']) || !$this->validateStringLength($this->request->post['firstname'], 3, 32)) {
			$this->setError('firstname', $this->language->get('error_firstname'));
		}

		if (!isset($this->request->post['lastname']) || !$this->validateStringLength($this->request->post['lastname'], 3, 32)) {
			$this->setError('lastname', $this->language->get('error_lastname'));
		}

		if (!$this->validateEmail($this->request->post['email'])) {
			$this->setError('email', $this->language->get('error_email'));
		}

		if (!isset($this->request->post['telephone']) || !$this->validateStringLength($this->request->post['telephone'], 3, 32)) {
			$this->setError('telephone', $this->language->get('error_telephone'));
		}

		if (!isset($this->request->post['product']) || !$this->validateStringLength($this->request->post['product'], 1, 255)) {
			$this->setError('product', $this->language->get('error_product'));
		}

		if (!isset($this->request->post['model']) || !$this->validateStringLength($this->request->post['model'], 1, 128)) {
			$this->setError('model', $this->language->get('error_model'));
		}

		if (empty($this->request->post['return_reason_id'])) {
			$this->setError('reason', $this->language->get('error_reason'));
		}

		if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
			$this->setError('captcha', $this->language->get('error_captcha'));
		}

		if ($this->config->get('config_return_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_return_id'));

			if ($information_info && !isset($this->request->post['agree'])) {
				$this->setError('warning', sprintf($this->language->get('error_agree'), $information_info['title']));
			}
		}

		return !$this->hasError();
	}

}
?>
