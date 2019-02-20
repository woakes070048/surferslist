<?php
class ControllerAccountProductDownload extends Controller {
	use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/product_download', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if (!$this->customer->validateMembership() || !$this->config->get('member_tab_download')) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}

	public function init() {
        $this->data = $this->load->language('account/product_download');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('account/product_download');

		$this->setQueryParams(array('sort', 'order', 'page'));
	}

	public function index() {
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		$this->init();
		$this->getList();
	}

  	public function insert() {
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		$this->init();

		if (!$this->validateNew()) {
			$this->getList();
		}

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/member', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateForm()) {
				$data = $this->request->post;
				// $data['filename'] = $this->customer->getMemberDownloadsDirectory() . '/' . $data['filename'];
				$data['customer_id'] = $this->customer->getId();
				$this->model_account_product_download->addDownload($data);

				$this->session->data['success'] = $this->language->get('text_success');

				$this->redirect($this->url->link('account/product_download', $this->getQueryParams(), 'SSL'));
			}
		}

		$this->getForm();
 	}

  	public function update() {
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		$this->init();

    	if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/member', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateForm()) {
				$data = $this->request->post;
				$data['customer_id'] = $this->customer->getId();

				$this->model_account_product_download->editDownload($this->request->get['download_id'], $data);

				$this->session->data['success'] = $this->language->get('text_success');

				$this->redirect($this->url->link('account/product_download', $this->getQueryParams(), 'SSL'));
			}
		}

    	$this->getForm();
  	}

  	public function delete() {
		// disable
		$this->redirect($this->url->link('error/not_found', '', 'SSL'));

		$this->init();

    	if (isset($this->request->post['selected'])) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/member', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			if ($this->validateDelete()) {
				foreach ($this->request->post['selected'] as $download_id) {
					$this->model_account_product_download->deleteDownload($download_id, $this->customer->getId());
				}

				$this->session->data['success'] = $this->language->get('text_success');

				$this->redirect($this->url->link('account/product_download', $this->getQueryParams(), 'SSL'));
			}
    	}

    	$this->getList();
  	}

  	private function getList() {
		$sort = isset($this->request->get['sort']) ? (string)$this->request->get['sort'] : 'dd.name';
		$order = isset($this->request->get['order']) ? (string)$this->request->get['order'] : 'ASC';
		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		$url = $this->getQueryParams();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/product_download'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['insert'] = $this->url->link('account/product_download/insert', $url, 'SSL');
		$this->data['delete'] = $this->url->link('account/product_download/delete', $url, 'SSL');

		$this->data['downloads'] = array();

		$data = array(
			'customer_id' => $this->customer->getId(),
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$download_total = $this->model_account_product_download->getTotalDownloads($this->customer->getId());

		$results = $this->model_account_product_download->getDownloads($data);

    	foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('account/product_download/update', '' . '&download_id=' . $result['download_id'] . $url, 'SSL')
			);

			$this->data['downloads'][] = array(
				'download_id' => $result['download_id'],
				'name'        => $result['name'],
				'remaining'   => $result['remaining'],
				'selected'    => isset($this->request->post['selected']) && in_array($result['download_id'], $this->request->post['selected']),
				'action'      => $action
			);
		}

		$this->data['button_insert'] = $this->language->get('button_new');
 		$this->data['button_continue'] = $this->language->get('button_back');

 		$this->data['error_warning'] = $this->getError('warning');

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$url = $this->getQueryParams(array('sort'));

		$this->data['sort_name'] = $this->url->link('account/product_download', '' . '&sort=dd.name' . $url, 'SSL');
		$this->data['sort_remaining'] = $this->url->link('account/product_download', '' . '&sort=d.remaining' . $url, 'SSL');

		$url = $this->getQueryParams(array('page'));

		$this->data['pagination'] = $this->getPagination($download_total, $page, 10, 'account/product_download', '', $url);

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->document->addScript('catalog/view/root/javascript/account.js');

		$this->template = '/template/account/product_download_list.tpl';

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

  	private function getForm() {
		$data_field_errors = array(
			'warning'		=>	'error_warning',
			'name'			=>	'error_name',
			'filename'		=>	'error_filename',
			'mask'			=>	'error_mask'
		);

		foreach ($data_field_errors as $data_field => $error_name) {
			$this->data[$error_name] = $this->getError($data_field);
		}

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/product_download'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		if (!isset($this->request->get['download_id'])) {
			$this->data['action'] = $this->url->link('account/product_download/insert', '', 'SSL');
		} else {
			$this->data['action'] = $this->url->link('account/product_download/update', '' . '&download_id=' . $this->request->get['download_id'], 'SSL');
		}

		$this->data['cancel'] = $this->url->link('account/product_download', $this->getQueryParams(), 'SSL');

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

    	if (isset($this->request->get['download_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$download_info = $this->model_account_product_download->getDownload($this->request->get['download_id'], $this->customer->getId());
    	}

		$this->data['download_id'] = isset($this->request->get['download_id']) ? (int)$this->request->get['download_id'] : 0;

		if (isset($this->request->post['download_description'])) {
			$this->data['download_description'] = $this->request->post['download_description'];
		} elseif (isset($this->request->get['download_id'])) {
			$this->data['download_description'] = $this->model_account_product_download->getDownloadDescriptions($this->request->get['download_id']);
		} else {
			$this->data['download_description'] = array();
		}

    	if (isset($this->request->post['filename'])) {
    		$this->data['filename'] = $this->request->post['filename'];
    	} elseif (!empty($download_info)) {
      		$this->data['filename'] = $download_info['filename'];
		} else {
			$this->data['filename'] = '';
		}

    	if (isset($this->request->post['mask'])) {
    		$this->data['mask'] = $this->request->post['mask'];
    	} elseif (!empty($download_info)) {
      		$this->data['mask'] = $download_info['mask'];
		} else {
			$this->data['mask'] = '';
		}

		if (isset($this->request->post['remaining'])) {
      		$this->data['remaining'] = $this->request->post['remaining'];
    	} elseif (!empty($download_info)) {
      		$this->data['remaining'] = $download_info['remaining'];
    	} else {
      		$this->data['remaining'] = 1;
    	}

    	if (isset($this->request->post['update'])) {
      		$this->data['update'] = $this->request->post['update'];
    	} else {
      		$this->data['update'] = false;
    	}

		$this->document->addScript('catalog/view/root/javascript/ajaxupload.js');
		$this->document->addScript('catalog/view/root/javascript/account.js');

		$this->template = '/template/account/product_download_form.tpl';

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

  	private function validateForm() {
    	foreach ($this->request->post['download_description'] as $language_id => $value) {
      		if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 64)) {
        		$this->appendError('name', sprintf($this->language->get('error_name'), 3, 64), $language_id);
      		}
    	}

		if ((utf8_strlen($this->request->post['filename']) < 5) || (utf8_strlen($this->request->post['filename']) > 128)) {
			$this->setError('filename', sprintf($this->language->get('error_filename'), 5, 128));
		}

		if (!file_exists(DIR_DOWNLOAD . $this->request->post['filename']) && !is_file(DIR_DOWNLOAD . $this->request->post['filename'])) {
			$this->setError('filename', $this->language->get('error_exists'));
		}

		if ((utf8_strlen($this->request->post['mask']) < 3) || (utf8_strlen($this->request->post['mask']) > 128)) {
			$this->setError('mask', $this->language->get('error_mask'));
		}

		return !$this->hasError();
  	}

  	private function validateDelete() {
		$this->load->model('catalog/product');

		foreach ($this->request->post['selected'] as $download_id) {
  			$product_total = $this->model_catalog_product->getTotalProductsByDownloadId($download_id);

			if ($product_total) {
	  			$this->setError('warning', sprintf($this->language->get('error_product'), $product_total));
			}
		}

		return !$this->hasError();
  	}

  	private function validateNew() {
    	if (!$this->validateCustomer()) {
      		$this->setError('warning', $this->language->get('error_permission'));
    	}

		/* Placeholder for potential futuer feature, additional field Member Max Downloads allowed per Customer account
		$this->customer->getMemberMaxDownloads();
		if($customer_max_downloads  == '-1') return true;

		$download_total = $this->model_account_product_download->getTotalDownloads($this->customer->getId());
		if ($download_total >= $customer_max_downloads) {
			$this->setError('max_downloads', $this->language->get('error_max_downloads'));
		}
		*/

		return !$this->hasError();
  	}

	private function validateAction() {
    	if (!$this->customer->getMemberPermission('download_enabled')) {
      		$this->setError('warning', $this->language->get('error_permission'));
    	}

		return !$this->hasError();
  	}
}
?>
