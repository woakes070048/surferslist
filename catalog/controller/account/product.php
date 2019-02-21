<?php
class ControllerAccountProduct extends Controller {
	use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/product', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if (!$this->customer->validateProfile()) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}

	private function init($referer = null) {
		$this->data = $this->load->language('account/product');

		if (!is_null($referer)) {
			$this->session->data['redirect'] = $referer;

			if ($referer == $this->url->link('account/product/insert', '', 'SSL')) {
				$this->session->data['warning'] = $this->language->get('error_post_login');
			}
		}

		$this->load->model('account/product');
		$this->load->model('tool/image');

		$this->setQueryParams(array(
			'filter_name',
			'filter_model',
			'filter_quantity',
			'filter_price',
			'filter_type',
			'filter_approved',
			'filter_status',
			'sort',
			'order',
			'limit',
			'page'
		));
	}

	public function index() {
		$this->init($this->url->link('account/product', '', 'SSL'));
		$this->getList();
	}

	public function insert() {
		$this->init($this->url->link('account/product/insert', '', 'SSL'));

		if (!$this->validateNew()) {
			$this->getList();
		} else {
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				if (!$this->customer->validateToken()) {
					$this->session->data['redirect'] = $this->url->link('account/product', '', 'SSL');
					$this->redirect($this->url->link('account/login', '', 'SSL'));
				}

				$this->customer->setToken();

				$data = $this->request->post;

				if ($this->validateForm($data)) {
					$this->prepareData($data);

					if ($this->model_account_product->addProduct($data)) {
						$this->session->data['success'] = $this->language->get('text_success_new');
					}

					$this->redirect($this->url->link('account/product', $this->getQueryParams(), 'SSL'));
				}
			}

			$this->getForm();
		}
  	}

  	public function update() {
		$this->init($this->url->link('account/product/update', '', 'SSL'));

    	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			if (!$this->customer->validateToken()) {
				$this->session->data['redirect'] = $this->url->link('account/product', '', 'SSL');
				$this->redirect($this->url->link('account/login', '', 'SSL'));
			}

			$this->customer->setToken();

			$data = $this->request->post;

			if ($this->validateForm($data)) {
				$product_info = $this->model_account_product->getProduct($this->request->get['listing_id']);

				if ($product_info) {
					$this->prepareData($data);

					if ($this->model_account_product->editProduct($this->request->get['listing_id'], $data)) {
						$this->session->data['success'] = $this->language->get('text_success_edit');
					}
				}

				$this->redirect($this->url->link('account/product', $this->getQueryParams(), 'SSL'));
			}
		}

		$this->getForm();
  	}

  	public function copy() {
		$this->init();

		if (!isset($this->request->post['selected'])) {
			$this->setError('warning', $this->language->get('error_notchecked'));
		} else {
			$this->checkCustomerToken();

			if ($this->validateNew()) {
				$count = $this->processSelected($this->request->post['selected'], 'copyProduct');

				$this->session->data['success'] = $this->language->get('text_success_copy');

				$this->redirect($this->url->link('account/product', $this->getQueryParams(array('filter_quantity', 'filter_price', 'filter_type', 'filter_approved', 'filter_status')), 'SSL'));
			}
		}

    	$this->getList();
  	}

	public function enable() {
		$this->init();

		if (!isset($this->request->post['selected'])) {
			$this->setError('warning', $this->language->get('error_notchecked'));
		} else {
			$this->checkCustomerToken(false);

			$count = $this->processSelected($this->request->post['selected'], 'enableProduct');

			$this->session->data['success'] = sprintf($this->language->get('text_product_enabled'), $count);

			if (isset($this->request->get['response_type']) && $this->request->get['response_type'] == 'json') {
				$json = $count > 0 ? array(
					'status'   => 1,
					'message'  => $this->session->data['success']
				) : array(
					'status'   => 0,
					'message'  => ''
				);

				unset($this->session->data['success']);
				unset($this->session->data['warning']);

				$this->response->setOutput(json_encode($json));
				return;
			} else {
				$this->customer->setToken();
				$this->redirect($this->url->link('account/product', $this->getQueryParams(), 'SSL'));
			}
		}

		$this->getList();
  	}

	public function disable() {
		$this->init();

		if (!isset($this->request->post['selected'])) {
			$this->setError('warning', $this->language->get('error_notchecked'));
		} else {
			$this->checkCustomerToken(false);

			$count = $this->processSelected($this->request->post['selected'], 'disableProduct');

			$this->session->data['success'] = sprintf($this->language->get('text_product_disabled'), $count);

			if (isset($this->request->get['response_type']) && $this->request->get['response_type'] == 'json') {
				$json = $count > 0 ? array(
					'status'   => 1,
					'message'  => $this->session->data['success']
				) : array(
					'status'   => 0,
					'message'  => ''
				);

				unset($this->session->data['success']);
				unset($this->session->data['warning']);

				$this->response->setOutput(json_encode($json));
				return;
			} else {
				$this->customer->setToken();
				$this->redirect($this->url->link('account/product', $this->getQueryParams(), 'SSL'));
			}
		}

		$this->getList();
	}

	public function expire() {
		$this->init();

		if (!isset($this->request->post['selected'])) {
			$this->setError('warning', $this->language->get('error_notchecked'));
		} else {
			$this->checkCustomerToken();

			if ($this->validateExpire()) {
				$count = $this->processSelected($this->request->post['selected'], 'retireProduct');

				$this->session->data['success'] = sprintf($this->language->get('text_product_expired'), $count);

				$this->redirect($this->url->link('account/product', $this->getQueryParams(), 'SSL'));
			}
		}

		$this->getList();
	}

	public function renew() {
		$this->init();

		if (!isset($this->request->post['selected'])) {
			$this->setError('warning', $this->language->get('error_notchecked'));
		} else {
			$this->checkCustomerToken();

			if ($this->validateRenew()) {
				$count = $this->processSelected($this->request->post['selected'], 'renewProduct');

				$this->session->data['success'] = sprintf($this->language->get('text_product_renewed'), $count);

				$this->redirect($this->url->link('account/product', $this->getQueryParams(), 'SSL'));
			}
		}

		$this->getList();
	}

  	public function transfer() {
		$this->init();
		$json = array();
		$json_error = false;
		$json['status'] = 0;

		$to_member_id = !empty($this->request->post['member']['member_id']) ? $this->request->post['member']['member_id'] : 0;

		if (empty($this->request->post['selected'])) {
			$json['message'] = $this->language->get('error_notchecked');
			$json_error = true;
		}

		if (!$to_member_id || $to_member_id == $this->customer->getProfileId()) {
			$json['message'] = $this->language->get('error_member_transfer');
			$json_error = true;
		} else {
			$this->load->model('account/member');
			$to_member_info = $this->model_account_member->getMemberByMemberId($to_member_id);

			if (!$to_member_info) {
				$json['message'] = $this->language->get('error_member_transfer');
				$json_error = true;
			}
		}

		if (!$this->customer->validateToken()) {
			$this->session->data['redirect'] = $this->url->link('account/product', '', 'SSL');
			$json['redirect'] = $this->url->link('account/login', '', 'SSL');
			$json['message'] = $this->language->get('error_invalid_token');
			$json_error = true;
		}

		if (!$this->validateTransfer()) {
			$json['message'] = $this->language->get('error_permission_transfer');
			$json_error = true;
		}

		if (!$json_error) {
			$this->customer->setToken();

			$count = 0;

			foreach ($this->request->post['selected'] as $product_id) {
				$product_info = $this->model_account_product->getProduct($product_id);

				if ($product_info && $product_info['member_approved']) {
					$this->model_account_product->transferProduct($product_info, $to_member_info);
					$count++;
				}
			}

			$json['redirect'] = $this->url->link('account/product', $this->getQueryParams(), 'SSL');
			$json['message'] = sprintf($this->language->get('text_product_transferred'), $count, $to_member_info['member_account_name']);
			$json['status'] = 1;
		}

		$this->response->setOutput(json_encode($json));
		return;
  	}

	private function processSelected($selected, $method) {
		$count = 0;

		foreach ($selected as $product_id) {
			$product_info = $this->model_account_product->getProduct($product_id);

			if ($product_info && $product_info['member_approved']) {
				$count += $this->model_account_product->{$method}($product_id);
			}
		}

		return $count;
	}

	private function checkCustomerToken($reset = true) {
		if (!$this->customer->validateToken()) {
			$this->session->data['redirect'] = $this->url->link('account/product', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if ($reset) {
			$this->customer->setToken();
		}
	}

  	private function getList() {
		$this->document->setTitle($this->language->get('heading_listings'));

		$this->data['heading_title'] = $this->language->get('heading_listings');
		$this->data['heading_sub_title'] = $this->language->get('heading_listings_sub');

		$data_filters = array('filter_name', 'filter_model', 'filter_price', 'filter_type', 'filter_quantity', 'filter_approved', 'filter_status' );

		foreach ($data_filters as $data_filter){
			${$data_filter} = isset($this->request->get[$data_filter]) ? $this->request->get[$data_filter] : null;
		}

		if ($filter_status != null) {
			$filter_approved = $filter_status == '2' ? '0' : '1';
		}

		if (isset($this->request->get['sort'])) {
			$sort = (string)$this->request->get['sort'];
		} else if ($this->customer->getMemberPermission('inventory_enabled')) {
			$sort = 'created';
		} else {
			$sort = 'expires';
		}

		if (isset($this->request->get['order'])) {
			$order = (string)$this->request->get['order'];
		} else if ($this->customer->getMemberPermission('inventory_enabled')) {
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}

		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('member_product_manager_limit') ? (int)$this->config->get('member_product_manager_limit') : 10;
		}

		$url = $this->getQueryParams();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_listings'), $this->url->link('account/product'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();
		$this->data['permissions'] = $this->customer->getMemberPermissions();

		$this->data['action'] = $this->url->link('account/product', '', 'SSL');
		$this->data['enable'] = $this->url->link('account/product/enable', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
		$this->data['disable'] = $this->url->link('account/product/disable', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
		$this->data['insert'] = $this->url->link('account/product/insert', $url, 'SSL');
		$this->data['copy'] = $this->url->link('account/product/copy', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
		$this->data['renew'] = $this->url->link('account/product/renew', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('account/product/expire', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
		$this->data['transfer'] = $this->url->link('account/product/transfer', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
		$this->data['members'] = $this->url->link('product/member', '', 'SSL');

		$this->data['member'] = isset($this->request->post['member']) ? $this->request->post['member'] : array();

		$this->data['products'] = array();

		$data = array(
			'filter_name'     => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_type'	  => $filter_type,
			'filter_quantity' => $filter_quantity,
			'filter_approved' => $filter_approved,
			'filter_status'   => ($filter_status == '2' ? null : $filter_status),
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $limit,
			'limit'           => $limit
		);

		$product_total = $this->model_account_product->getTotalProducts($data);

		$results = $this->model_account_product->getProducts($data);

		if ($results) {
			foreach ($results as $result) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), 'autocrop');

				$special = false;

				$product_specials = $this->model_account_product->getProductSpecials($result['product_id']);

				foreach ($product_specials  as $product_special) {
					if (($product_special['date_start'] == '0000-00-00' || $product_special['date_start'] < date('Y-m-d')) && ($product_special['date_end'] == '0000-00-00' || $product_special['date_end'] > date('Y-m-d'))) {
						// $special = $product_special['price'];
						$special = $this->currency->format($this->tax->calculate($product_special['price'], $result['tax_class_id'], $this->config->get('config_tax')));
						break;
					}
				}

				$featured_products = explode(',', $this->config->get('featured_product'));

				if ($featured_products) {
					$featured = in_array($result['product_id'], $featured_products);
				} else {
					$featured = false;
				}

				if (!$result['member_approved']) {
					$status = $this->language->get('text_approval');
				} else {
					$status = ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'));
				}

				$this->data['products'][] = array(
					'product_id' => $result['product_id'],
					'name'       => $result['name'],
					'model'      => $result['model'],
					// 'href'       => $this->url->link('product/product','product_id=' . $result['product_id'] . '&preview_listing=' . $this->session->data['customer_token'], 'SSL'),
					'quickview'  => $this->url->link('product/quickview','listing_id=' . $result['product_id'] . '&preview_listing=' . $this->session->data['customer_token'], 'SSL'),
					'price'      => $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))),
					'special'    => $special,
					'featured'   => $featured,
					'image'      => $image,
					'quantity'   => ($result['quantity'] > 0 ? $result['quantity'] : 'n/a'),
					'type'       => ($result['quantity'] > 0 ? $this->language->get('text_buy_now') : ($result['quantity'] == 0 ? $this->language->get('text_classified') : $this->language->get('text_shared'))),
					'date_expiration' => date($this->language->get('date_format_medium'), strtotime($result['date_expiration'])),
					'expires_soon' => ((strtotime($result['date_expiration']) - time() <= 604800) ? true : false),
					'approved'   => ($result['member_approved'] ? $this->language->get('text_yes') : $this->language->get('text_no')),
					'status'     => $status,
					'selected'   => isset($this->request->post['selected']) && in_array($result['product_id'], $this->request->post['selected']),
					'edit'       => $this->url->link('account/product/update', '&listing_id=' . $result['product_id'] . $url, 'SSL')
				);
			}
		} else {
			$this->load->model('account/account');
			$this->data['text_no_results'] = $this->model_account_product->getTotalProducts()
				? sprintf($this->language->get('text_no_results'), $this->url->link('account/product', '', 'SSL'))
				: sprintf($this->language->get('text_no_listings'), $this->url->link('account/product/insert', '', 'SSL'));
		}

		// Query Parameters
		$url = $this->getQueryParams(array('sort', 'order', 'limit'));

		$url .= ($order == 'ASC') ? '&order=DESC' : '&order=ASC';

		$this->data['sort_name'] = $this->url->link('account/product', '&sort=name' . $url, 'SSL');
		$this->data['sort_model'] = $this->url->link('account/product', '&sort=model' . $url, 'SSL');
		$this->data['sort_price'] = $this->url->link('account/product', '&sort=price' . $url, 'SSL');
		$this->data['sort_date_expiration'] = $this->url->link('account/product', '&sort=expires' . $url, 'SSL');
		$this->data['sort_type'] = $this->url->link('account/product', '&sort=type' . $url, 'SSL');
		$this->data['sort_quantity'] = $this->url->link('account/product', '&sort=quantity' . $url, 'SSL');
		$this->data['sort_approved'] = $this->url->link('account/product', '&sort=approved' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('account/product', '&sort=status' . $url, 'SSL');
		$this->data['sort_order'] = $this->url->link('account/product', '&sort=order' . $url, 'SSL');

		$url = $this->getQueryParams(array('limit', 'page'));

		$this->data['pagination'] = $this->getPagination($product_total, $page, $limit, 'account/product', '', $url);

		$this->data['button_continue'] = $this->language->get('button_back');
		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->data['filter_name'] = $filter_name;
		$this->data['filter_model'] = $filter_model;
		$this->data['filter_price'] = $filter_price;
		$this->data['filter_type'] = $filter_type;
		$this->data['filter_quantity'] = $filter_quantity;
		$this->data['filter_approved'] = $filter_approved;
		$this->data['filter_status'] = $filter_status;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

 		$this->session->data['warning'] = $this->getError('warning');

		if (isset($this->session->data['success'])) {
			$this->session->data['success'] = $this->session->data['success'];
		} else {
			$this->session->data['success'] = '';
		}

		$this->document->addStyle('catalog/view/root/ui/jquery-ui.min.css');
		$this->document->addScript('catalog/view/root/ui/jquery-ui.min.js');
		$this->document->addScript('catalog/view/root/javascript/account.js');

		if ($this->data['products']) {
			$this->document->addScript('catalog/view/root/javascript/contact.js');
		}

		$this->template = '/template/account/product_list.tpl';

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
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('localisation/language');
		$this->load->model('localisation/currency');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');

		$data_field_errors = array(
			'warning'			=>  'error_warning',
			'name'				=>	'error_name',
			'description'		=>	'error_description',
			'image'				=>	'error_image',
			'images'			=>	'error_images',
			'for_sale'			=>	'error_for_sale',
			'price'				=>	'error_price',
			'value'				=>	'error_value',
			'condition'			=>	'error_condition',
			'size'				=>	'error_size',
			'location'			=>	'error_location',
			'zone'				=>	'error_zone',
			'country'			=>	'error_country',
			'category'			=>	'error_category',
			'category_sub'		=>	'error_category_sub',
			'manufacturer'		=>	'error_manufacturer',
			'model'				=>	'error_model',
			'year'				=>	'error_year',
			'dimensions'		=>	'error_dimensions',
			'weight'			=>	'error_weight',
			'type'				=>	'error_type',
			'part_number'		=>	'error_part_number',
			'quantity'			=>	'error_quantity',
			'minimum'			=>	'error_minimum',
			'shipping'			=>	'error_shipping'
		);

		foreach ($data_field_errors as $data_field => $error_name) {
			$this->data[$error_name] = $this->getError($data_field);
		}

        // Help
        $this->data['help_description'] = sprintf($this->language->get('help_description'), $this->config->get('member_data_field_description_min'), $this->config->get('member_data_field_description_max'));

		if ($this->config->get('member_image_upload_filesize_max')) {
			$image_upload_filesize_max = $this->config->get('member_image_upload_filesize_max') / 1024; // kB to MB
		} else {
			$image_upload_filesize_max = 5; // MB
		}

		if ($this->config->get('member_image_dimensions_min_width')) {
			$image_dimensions_min_width = $this->config->get('member_image_dimensions_min_width');
		} else {
			$image_dimensions_min_width = 245;
		}

		if ($this->config->get('member_image_dimensions_min_height')) {
			$image_dimensions_min_height = $this->config->get('member_image_dimensions_min_height');
		} else {
			$image_dimensions_min_height = 245;
		}

        $this->data['help_image'] = sprintf($this->language->get('help_image'), $image_dimensions_min_width, $image_dimensions_min_height, $image_upload_filesize_max);

		$url = $this->getQueryParams();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_listings'), $this->url->link('account/product'));

		$this->data['permissions'] = $this->customer->getMemberPermissions();

		// Existing Product Info
		if (isset($this->request->get['listing_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_account_product->getProduct($this->request->get['listing_id']);

			// if(!$this->validateProduct($this->request->get['listing_id'])) {
			if (!$product_info) {
				$this->session->data['warning'] = $this->language->get('error_permission');
				$this->redirect($this->url->link('account/product', '', 'SSL'));
			}
    	}

		// New vs. Edit Product
		if (!isset($this->request->get['listing_id'])) {
			$this->document->setTitle($this->language->get('heading_post'));
			$this->data['action'] = $this->url->link('account/product/insert', 'customer_token=' . $this->session->data['customer_token'] . $url, 'SSL');
			$this->data['heading_title'] = $this->language->get('heading_post');
			$this->data['heading_sub_title'] = $this->language->get('heading_post_sub');
			$this->data['text_post_intro'] = $this->language->get('text_post_intro') . sprintf($this->language->get('text_intro_anonpost'), $this->url->link('account/anonpost'));
			$this->data['featured'] = false;
			$this->data['product_id'] = 0; // for digital file downloads
			$this->data['button_save'] = $this->language->get('button_post');

			$this->addBreadcrumb($this->language->get('heading_post'), $this->url->link('account/product/insert', $url));
		} else {
			$this->document->setTitle($this->language->get('heading_edit'));
			$this->data['action'] = $this->url->link('account/product/update', 'customer_token=' . $this->session->data['customer_token'] . '&listing_id=' . $this->request->get['listing_id'] . $url, 'SSL');
			$this->data['heading_title'] = $this->language->get('heading_edit');
			$this->data['heading_sub_title'] = $this->language->get('heading_edit_sub');
			$this->data['text_post_intro'] = $this->language->get('text_edit_intro') . sprintf($this->language->get('text_intro_anonpost'), $this->url->link('account/anonpost'));
			$this->data['featured'] = in_array($this->request->get['listing_id'], explode(',', $this->config->get('featured_product')));
			$this->data['product_id'] = (int)$this->request->get['listing_id']; // for digital file downloads

			$this->addBreadcrumb($this->language->get('heading_edit'), $this->url->link('account/product/update', 'listing_id=' . $this->request->get['listing_id'] . $url));
		}

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['cancel'] = $this->url->link('account/product', $url, 'SSL');

		$product_total = $this->model_account_product->getTotalProducts();

		// Name, Description, Tags
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['product_description'])) {
			$this->data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($this->request->get['listing_id'])) {
			$this->data['product_description'] = strip_tags_decode($this->model_account_product->getProductDescriptions($this->request->get['listing_id']));
		} else {
			$this->data['product_description'] = array();
		}

		// Currency
		$this->data['currency'] = $this->model_localisation_currency->getCurrencyByCode($this->currency->getCode());

		// Primary Image
		if (!empty($this->request->post['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
		} elseif (!empty($product_info) && $product_info['image']) {
			$this->data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
		}

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));

		// Type
		if (isset($this->request->post['type'])) {
			$this->data['type'] = $this->request->post['type'];
		} elseif (!empty($product_info) && $product_info['quantity'] >= 0) {
			$this->data['type'] = ($product_info['quantity'] == 0 ? 0 : 1);
		} elseif (!empty($product_info) && $product_info['quantity'] < 0) {
			$this->data['type'] = 0;
		} elseif ($this->customer->getMemberPayPal()) {
			$this->data['type'] = 1;
		} else {
			$this->data['type'] = 0;
		}

		// For Sale
		if (isset($this->request->post['for_sale'])) {
			$this->data['for_sale'] = $this->request->post['for_sale'];
		} elseif (!empty($product_info)) {
			$this->data['for_sale'] = $product_info['quantity'] >= 0 ? 1 : 0;
		} else {
			$this->data['for_sale'] = 0;
		}

		// Date Available and Date Expiration
		/*
		if (isset($this->request->post['date_available'])) {
       		$this->data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($product_info)) {
			$this->data['date_available'] = date('Y-m-d H:i:s', strtotime($product_info['date_available'])); // "m/d/y g:i A" for mm/dd/yy H:M (AM/PM)
		} else {
			$this->data['date_available'] = date('Y-m-d H:i:s', time()); // date('Y-m-d', time() + (60 * 60 * 24)); // 7200 sec = 1 hr
		}

		if (isset($this->request->post['date_expiration'])) {
       		$this->data['date_expiration'] = $this->request->post['date_expiration'];
		} elseif (!empty($product_info)) {
			$this->data['date_expiration'] = date('Y-m-d H:i:s', strtotime($product_info['date_expiration'])); // "m/d/y g:i A" for mm/dd/yy H:M (AM/PM)
		} else {
			$this->data['date_expiration'] = date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 90)); // (60 * 60 * 24 * 90) sec = 90 days
		}
		* */

		// Initialize Base Data Fields
		$config_data_fields_empty = array('image', 'size', 'points');
		$decimal_data_fields_empty = array('price', 'value');

		// Model
		if ($this->config->get('member_data_field_model')) {
			$config_data_fields_empty[] = 'model';
		}

		// Year
		if (isset($this->request->post['year'])) {
			$this->data['year'] = $this->request->post['year'];
		} elseif (!empty($product_info) && $product_info['year'] != '0000') {
			$this->data['year'] = $product_info['year'];
		} else {
			$this->data['year'] = '';
		}

		// Keyword
		if ($this->config->get('member_data_field_keyword')) {
			$config_data_fields_empty[] = 'keyword';
		}

		// Location
		if ($this->config->get('member_data_field_location')) {
			$this->data['countries'] = $this->model_localisation_country->getCountries();

			if (isset($this->request->post['location'])) {
				$this->data['location'] = $this->request->post['location'];
			} elseif (!empty($product_info['location'])) {
				$this->data['location'] = $product_info['location'];
			} elseif (isset($this->session->data['shipping_location'])) {
				$this->data['location'] = $this->session->data['shipping_location'];
			} else {
				$this->data['location'] = $this->customer->getMemberCity();
			}

			if (isset($this->request->post['zone_id'])) {
				$this->data['zone_id'] = $this->request->post['zone_id'];
			} elseif (!empty($product_info['zone_id'])) {
				$this->data['zone_id'] = $product_info['zone_id'];
			} elseif (isset($this->session->data['shipping_zone_id'])) {
				$this->data['zone_id'] = $this->session->data['shipping_zone_id'];
			} else {
				$this->data['zone_id'] = $this->customer->getMemberZoneId();
			}

			if (isset($this->request->post['country_id'])) {
				$this->data['country_id'] = $this->request->post['country_id'];
			} elseif (!empty($product_info['country_id'])) {
				$this->data['country_id'] = $product_info['country_id'];
			} elseif (isset($this->session->data['shipping_country_id'])) {
				$this->data['country_id'] = $this->session->data['shipping_country_id'];
			} else {
				$this->data['country_id'] = $this->customer->getMemberCountryId(); // $this->config->get('config_country_id');
			}

			if ($this->data['country_id']) {
				$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->data['country_id']);
			} else {
				$this->data['zones'] = array();
			}
		}

		// Part Numbers
		if ($this->config->get('member_data_field_part_numbers')) {
			$config_data_fields_empty[] = 'mpn';
			$config_data_fields_empty[] = 'sku';
			$config_data_fields_empty[] = 'upc';
			$config_data_fields_empty[] = 'ean';
			$config_data_fields_empty[] = 'jan';
			// $config_data_fields_empty[] = 'isbn';
		}

		// Weight
		if ($this->config->get('member_data_field_weight')) {
			$decimal_data_fields_empty[] = 'weight';

			$this->data['weight_classes'] = $this->model_account_product->getWeightClasses();

			if (isset($this->request->post['weight_class_id'])) {
				$this->data['weight_class_id'] = $this->request->post['weight_class_id'];
			} elseif (!empty($product_info['weight_class_id'])) {
				$this->data['weight_class_id'] = $product_info['weight_class_id'];
			} else {
				$this->data['weight_class_id'] = $this->config->get('config_weight_class_id');
			}
		}

		// Length and Dimensions
		if ($this->config->get('member_data_field_dimensions')) {
			$decimal_data_fields_empty[] = 'length';
			$decimal_data_fields_empty[] = 'width';
			$decimal_data_fields_empty[] = 'height';

			$this->data['length_classes'] = $this->model_account_product->getLengthClasses();

			if (isset($this->request->post['length_class_id'])) {
				$this->data['length_class_id'] = $this->request->post['length_class_id'];
			} elseif (!empty($product_info['length_class_id'])) {
				$this->data['length_class_id'] = $product_info['length_class_id'];
			} else {
				$this->data['length_class_id'] = $this->config->get('config_length_class_id');
			}
		}

		// Categories
		if ($this->config->get('member_data_field_category')) {
			$this->data['categories_complete'] = $this->model_catalog_category->getAllCategoriesComplete();
			$this->data['categories'] = $this->model_catalog_category->getCategories(0);

			if (isset($this->request->post['category_id'])) {
				$this->data['category_id'] = $this->request->post['category_id'];
			} elseif (!empty($product_info)) {
				$this->data['category_id'] = $this->model_account_product->getProductCategoryTop($this->request->get['listing_id']);
			} else {
				$this->data['category_id'] = 0;
			}

			if ($this->data['category_id']) {
				$this->data['sub_categories'] = $this->model_catalog_category->getCategories($this->data['category_id']);
			} else {
				$this->data['sub_categories'] = array();
			}

			if (isset($this->request->post['sub_category_id'])) {
				$this->data['sub_category_id'] = $this->request->post['sub_category_id'];
			} elseif (!empty($product_info)) {
				$this->data['sub_category_id'] = $this->model_account_product->getProductCategorySub($this->request->get['listing_id'], $this->data['category_id']);
			} else {
				$this->data['sub_category_id'] = 0;
			}

			if ($this->data['sub_category_id']) {
				$this->data['third_categories'] = $this->model_catalog_category->getCategories($this->data['sub_category_id']);
			} else {
				$this->data['third_categories'] = array();
			}

			if (isset($this->request->post['third_category_id'])) {
				$this->data['third_category_id'] = $this->request->post['third_category_id'];
			} elseif (!empty($product_info)) {
				$this->data['third_category_id'] = $this->model_account_product->getProductCategorySub($this->request->get['listing_id'], $this->data['sub_category_id']);
			} else {
				$this->data['third_category_id'] = 0;
			}
		}

		// Manufacturers (Brands)
		if ($this->config->get('member_data_field_manufacturer')) {
			if (isset($this->request->post['manufacturer_id'])) {
				$this->data['manufacturer_id'] = $this->request->post['manufacturer_id'];
			} elseif (!empty($product_info)) {
				$this->data['manufacturer_id'] = $product_info['manufacturer_id'];
			} else {
				$this->data['manufacturer_id'] = 0;
			}

			if ($this->data['manufacturer_id'] > 1) {
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->data['manufacturer_id']);
				$this->data['manufacturer_thumb'] = !empty($manufacturer_info['image']) ? $this->model_tool_image->resize($manufacturer_info['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'), 'fw') : '';
				$this->data['manufacturer_name'] = !empty($manufacturer_info['name']) ? $manufacturer_info['name'] : '';
			} else {
				$this->data['manufacturer_thumb'] = '';
				$this->data['manufacturer_name'] = '';
			}

			$this->data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
		}

		// Quantity, Minimum, Subtract
		if ($this->customer->getMemberPermission('inventory_enabled') && $this->config->get('member_data_field_quantity')) {
			if (isset($this->request->post['quantity'])) {
				$this->data['quantity'] = $this->request->post['quantity'];
			} elseif (!empty($product_info)) {
				$this->data['quantity'] = $product_info['quantity'];
			} else {
				$this->data['quantity'] = '';
			}

			if (isset($this->request->post['minimum'])) {
				$this->data['minimum'] = $this->request->post['minimum'];
			} elseif (!empty($product_info)) {
				$this->data['minimum'] = $product_info['minimum'];
			} else {
				$this->data['minimum'] = 1;
			}

			if (isset($this->request->post['subtract'])) {
				$this->data['subtract'] = $this->request->post['subtract'];
			} elseif (!empty($product_info)) {
				$this->data['subtract'] = $product_info['subtract'];
			} else {
				$this->data['subtract'] = 1;
			}
		}

		// Product Filters (Condition)
		// if ($this->config->get('member_data_field_filter')) {
			$filter_group_id = 2; // Condition

			$this->data['conditions'] = $this->model_account_product->getFilters($filter_group_id);

			if (isset($this->request->post['condition_id'])) {
				$this->data['condition_id'] = $this->request->post['condition_id'];
			} elseif (isset($this->request->get['listing_id'])) {
				$product_filter_conditions = $this->model_account_product->getProductFilters($this->request->get['listing_id'], $filter_group_id);
				$this->data['condition_id'] = reset($product_filter_conditions);
			} else {
				$this->data['condition_id'] = 0;
			}
		// }

		// Shipping
 		if ($this->config->get('member_data_field_shipping')) {
			if (isset($this->request->post['shipping'])) {
				$this->data['shipping'] = $this->request->post['shipping'];
			} elseif (!empty($product_info)) {
				$this->data['shipping'] = $product_info['shipping'];
			} else {
				$this->data['shipping'] = 0;
			}

			$this->data['product_shipping_rates'] = $this->config->get('product_shipping_status')
				&& $this->customer->getMemberPermission('inventory_enabled')
				//&& $this->customer->getMemberCountryId() == $this->config->get('config_country_id')
				? true
				: false;

			$this->data['geo_zones'] = $this->model_account_product->getGeoZones();

			if (isset($this->request->post['product_shipping'])) {
				$this->data['product_shipping'] = $this->request->post['product_shipping'];
			} elseif (isset($this->request->get['listing_id'])) {
				$this->data['product_shipping'] = $this->model_account_product->getProductShipping($this->request->get['listing_id']);
			} else {
				$this->data['product_shipping'] = array();
			}

			$this->data['help_shipping_list'] = sprintf($this->language->get('help_shipping_list'), $this->url->link('account/product/display_geo_zones', '', 'SSL'), $this->url->link('account/product/display_geo_zones', '', 'SSL'));
		}

		// More Images
		if ($this->config->get('member_tab_image')) {
			if (isset($this->request->post['product_image'])) {
				$product_images = $this->request->post['product_image'];
			} elseif (isset($this->request->get['listing_id'])) {
				$product_images = $this->model_account_product->getProductImages($this->request->get['listing_id']);
			} else {
				$product_images = array();
			}

			$this->data['product_images'] = array();

			foreach ($product_images as $product_image) {
				$image = $product_image['image'] ? $product_image['image'] : '';

				$this->data['product_images'][] = array(
					'image'      => $image,
					'thumb'      => $image ? $this->model_tool_image->resize($image, $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')) : $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')),
					'sort_order' => $product_image['sort_order']
				);
			}
		}

		// Assign String Data Fields
        foreach ($config_data_fields_empty as $config_data_field) {
            if (isset($this->request->post[$config_data_field])) {
                $this->data[$config_data_field] = $this->request->post[$config_data_field];
            } elseif (!empty($product_info)) {
                $this->data[$config_data_field] = $product_info[$config_data_field];
            } else {
				$this->data[$config_data_field] = '';
			}
        }

		if (!empty($product_info)) {
			$price_currency_adjusted = $this->currency->convert($product_info['price'], $this->config->get('config_currency'), $this->currency->getCode());
		}

		// Assign Number Data Fields
        foreach ($decimal_data_fields_empty as $decimal_data_field) {
            if (isset($this->request->post[$decimal_data_field])) {
                $this->data[$decimal_data_field] = $this->request->post[$decimal_data_field];
            } elseif ($decimal_data_field == 'value') {
                $this->data['value'] = !empty($product_info) && $product_info['price'] != '0.00' ? number_format($price_currency_adjusted, 2, '.', '') : '';
            } elseif ($decimal_data_field == 'price') {
                $this->data['price'] = !empty($product_info) && ($product_info['quantity'] >= 0 || $product_info['price'] != '0.00') ? number_format($price_currency_adjusted, 2, '.', '') : '';
            } elseif (!empty($product_info[$decimal_data_field])) {
                $this->data[$decimal_data_field] = number_format($product_info[$decimal_data_field], 2, '.', '');
            } else {
				$this->data[$decimal_data_field] = '';
			}
        }

		// Status
		if (isset($this->request->post['status'])) {
			$this->data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$this->data['status'] = $product_info['status'];
		} else {
			$this->data['status'] = 1;
		}

		// Premium Membership features
		if ($this->customer->getMemberPermissions()) {

			// Related
			if ($this->customer->getMemberPermission('related_enabled') && $this->config->get('member_data_field_related')) {
				if (isset($this->request->post['product_related'])) {
					$products = $this->request->post['product_related'];
				} elseif (isset($this->request->get['listing_id'])) {
					$products = $this->model_account_product->getProductRelated($this->request->get['listing_id']);
				} else {
					$products = $this->model_account_product->getMemberProductRelated(); // $products = array();
				}

				$this->data['product_related'] = array();

				foreach ($products as $product_id) {
					$related_info = $this->model_account_product->getProduct($product_id);

					if ($related_info) {
						$this->data['product_related'][] = array(
							'product_id' => $related_info['product_id'],
							'name'       => $related_info['name']
						);
					}
				}
			}

			// Sort Order
			if ($this->customer->getMemberPermission('sort_enabled') && $this->config->get('member_data_field_sort_order')) {
				if (isset($this->request->post['sort_order'])) {
					$this->data['sort_order'] = $this->request->post['sort_order'];
				} elseif (!empty($product_info)) {
					$this->data['sort_order'] = $product_info['sort_order'];
				} else {
					$this->data['sort_order'] = 1;
				}
			}

			// Taxes
			if ($this->customer->getMemberPermission('tax_enabled') && $this->config->get('member_data_field_tax')) {
				$this->data['tax_classes'] = $this->model_account_product->getTaxClasses();

				if (isset($this->request->post['tax_class_id'])) {
					$this->data['tax_class_id'] = $this->request->post['tax_class_id'];
				} elseif (!empty($product_info)) {
					$this->data['tax_class_id'] = $product_info['tax_class_id'];
				} else {
					$this->data['tax_class_id'] = 0;
				}
			}

			// Stock
			if ($this->customer->getMemberPermission('inventory_enabled') && $this->config->get('member_data_field_stock')) {
				$this->data['stock_statuses'] = $this->model_account_product->getStockStatuses();

				if (isset($this->request->post['stock_status_id'])) {
					$this->data['stock_status_id'] = $this->request->post['stock_status_id'];
				} elseif (!empty($product_info)) {
					$this->data['stock_status_id'] = $product_info['stock_status_id'];
				} else {
					$this->data['stock_status_id'] = $this->config->get('config_stock_status_id');
				}
			}

			// Stores
			if (false && $this->config->get('member_data_field_store')) {
				$this->load->model('setting/store');

				$this->data['stores'] = $this->model_setting_store->getStores();

				if (isset($this->request->post['product_store'])) {
					$this->data['product_store'] = $this->request->post['product_store'];
				} elseif (isset($this->request->get['listing_id'])) {
					$this->data['product_store'] = $this->model_account_product->getProductStores($this->request->get['listing_id']);
				} else {
					$this->data['product_store'] = array(0);
				}
			}

			// Downloads
			if ($this->customer->getMemberPermission('download_enabled') && $this->config->get('member_tab_download')) {
				$this->load->model('account/product_download');

				$this->data['downloads'] = $this->model_account_product_download->getDownloads($data);

				if (isset($this->request->post['product_download'])) {
					$this->data['product_download'] = $this->request->post['product_download'];
				} elseif (isset($this->request->get['listing_id'])) {
					$this->data['product_download'] = $this->model_account_product->getProductDigitalDownloads($this->request->get['listing_id']);
				} else {
					$this->data['product_download'] = array();
				}

				if (isset($this->request->post['digital'])) {
					$this->data['digital'] = $this->request->post['digital'];
				} elseif (!empty($product_info)) {
					$this->data['digital'] = $product_info['digital'];
				} else {
					$this->data['digital'] = 0;
				}
			}

			// Attibutes
			if ($this->customer->getMemberPermission('attribute_enabled') && $this->config->get('member_tab_attribute')) {
				if (isset($this->request->post['product_attribute'])) {
					$this->data['product_attributes'] = $this->request->post['product_attribute'];
				} elseif (isset($this->request->get['listing_id'])) {
					$this->data['product_attributes'] = $this->model_account_product->getProductAttributes($this->request->get['listing_id']);
				} else {
					$this->data['product_attributes'] = array();
				}

				$this->data['help_attribute_list'] = sprintf($this->language->get('help_attribute_list'), $this->url->link('account/product/display_attributes', '&limit=100', 'SSL'), $this->url->link('account/product/display_attributes', '&limit=100', 'SSL'));
			}

			// Options
			if ($this->customer->getMemberPermission('option_enabled') && $this->config->get('member_tab_option')) {
				if (isset($this->request->post['product_option'])) {
					$product_options = $this->request->post['product_option'];
				} elseif (isset($this->request->get['listing_id'])) {
					$product_options = $this->model_account_product->getProductOptions($this->request->get['listing_id']);
				} else {
					$product_options = array();
				}

				$this->data['product_options'] = array();

				foreach ($product_options as $product_option) {
					if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$product_option_value_data[] = array(
								'product_option_value_id' => $product_option_value['product_option_value_id'],
								'option_value_id'         => $product_option_value['option_value_id'],
								'quantity'                => $product_option_value['quantity'],
								'subtract'                => $product_option_value['subtract'],
								'price'                   => $product_option_value['price'],
								'price_prefix'            => $product_option_value['price_prefix'],
								'points'                  => $product_option_value['points'],
								'points_prefix'           => $product_option_value['points_prefix'],
								'weight'                  => $product_option_value['weight'],
								'weight_prefix'           => $product_option_value['weight_prefix']
							);
						}

						$this->data['product_options'][] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $product_option['name'],
							'type'                 => $product_option['type'],
							'required'             => $product_option['required']
						);
					} else {
						$this->data['product_options'][] = array(
							'product_option_id' => $product_option['product_option_id'],
							'option_id'         => $product_option['option_id'],
							'name'              => $product_option['name'],
							'type'              => $product_option['type'],
							'option_value'      => $product_option['option_value'],
							'required'          => $product_option['required']
						);
					}

					$this->data['help_options_list'] = sprintf($this->language->get('help_options_list'), $this->url->link('account/product/display_options', '&limit=100', 'SSL'), $this->url->link('account/product/display_options', '&limit=100', 'SSL'));
				}

				$this->data['option_values'] = array();

				foreach ($product_options as $product_option) {
					if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
						if (!isset($this->data['option_values'][$product_option['option_id']])) {
							$this->data['option_values'][$product_option['option_id']] = $this->model_account_product->getOptionValues($product_option['option_id']);
						}
					}
				}
			}

			// Discount, Specials, Reward Points
			if ($this->config->get('member_tab_discount') || $this->config->get('member_tab_special') || $this->config->get('member_tab_reward_points')) {
				$this->data['customer_groups'] = $this->model_account_product->getCustomerGroups();

				if ($this->customer->getMemberPermission('discount_enabled') && $this->config->get('member_tab_discount')) {
					if (isset($this->request->post['price_discount'])) {
						$this->data['price_discount'] = $this->request->post['price_discount'];
						$this->data['discount_quantity'] = $this->request->post['discount_quantity'];
					} elseif (isset($this->request->get['listing_id'])) {
						$product_discounts = $this->model_account_product->getProductDiscounts($this->request->get['listing_id']);

						if ($product_discounts) {
							foreach ($product_discounts as $product_discount) {
								$this->data['price_discount'] = number_format($this->currency->convert($product_discount['price'], $this->config->get('config_currency'), $this->currency->getCode()), 2, '.', '');
								$this->data['discount_quantity'] = $product_discount['quantity'];
								break;
							}
						} else {
							$this->data['price_discount'] = '';
							$this->data['discount_quantity'] = '';
						}
					} else {
						$this->data['price_discount'] = '';
						$this->data['discount_quantity'] = '';
					}
				}

				if ($this->customer->getMemberPermission('special_enabled') && $this->config->get('member_tab_special')) {
					if (isset($this->request->post['price_special'])) {
						$this->data['price_special'] = $this->request->post['price_special'];
					} elseif (isset($this->request->get['listing_id'])) {
						$product_specials = $this->model_account_product->getProductSpecials($this->request->get['listing_id']);

						if ($product_specials) {
							foreach ($product_specials as $product_special) {
								$this->data['price_special'] = number_format($this->currency->convert($product_special['price'], $this->config->get('config_currency'), $this->currency->getCode()), 2, '.', '');
								break;
							}
						} else {
							$this->data['price_special'] = '';
						}
					} else {
						$this->data['price_special'] = '';
					}
				}

				if ($this->customer->getMemberPermission('reward_enabled') && $this->config->get('member_tab_reward_points')) {
					if (isset($this->request->post['product_reward'])) {
						$this->data['product_reward'] = $this->request->post['product_reward'];
					} elseif (isset($this->request->get['listing_id'])) {
						$this->data['product_reward'] = $this->model_account_product->getProductRewards($this->request->get['listing_id']);
					} else {
						$this->data['product_reward'] = array();
					}
				}
			}

			// Layout (Design)
			if ($this->customer->getMemberPermission('design_enabled') && $this->config->get('member_tab_design')) {
				if (isset($this->request->post['product_layout'])) {
					$this->data['product_layout'] = $this->request->post['product_layout'];
				} elseif (isset($this->request->get['listing_id'])) {
					$this->data['product_layout'] = $this->model_account_product->getProductLayouts($this->request->get['listing_id']);
				} else {
					$this->data['product_layout'] = array();
				}

				$this->load->model('design/layout');

				$this->data['layouts'] = $this->model_design_layout->getLayouts();
			}

		} // end premium membership features

		$this->data['help'] = $this->url->link('information/information/info', 'information_id=14', 'SSL'); // information_id=14, How to Create a New Listing
		$this->data['continue'] = $this->url->link('account/product', '', 'SSL');

 		$this->session->data['warning'] = $this->getError('warning');
		$this->data['error'] = $this->hasError();

		$this->document->addStyle('catalog/view/root/ui/jquery-ui.min.css');
		$this->document->addScript('catalog/view/root/ui/jquery-ui.min.js');
		$this->document->addScript('catalog/view/root/javascript/ajaxupload.js');
		$this->document->addScript('catalog/view/root/javascript/post.js');

		if ($this->customer->getMemberPermission('option_enabled') && $this->config->get('member_tab_option')) {
			$this->document->addScript('catalog/view/root/ui/jquery-ui-timepicker-addon.js');
		}

		$this->template = '/template/account/product_form.tpl';

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

	private function validateFields(&$data) {
		$data_required = array(
			'product_description',
			'size',
			'year',
			'type',
			'for_sale',
			'price',
			'value',
			'condition_id',
			'status'
		);

		$data_optional = array(
			'digital'
		);

		if ($this->config->get('member_data_field_category')) {
			$data_required[] = 'category_id';
			$data_required[] = 'sub_category_id';
			$data_optional[] = 'third_category_id';
    	}

		if ($this->config->get('member_data_field_manufacturer')) {
			$data_required[] = 'manufacturer_id';
    	}

		if ($this->config->get('member_data_field_model')) {
			$data_required[] = 'model';
    	}

		if ($this->config->get('member_data_field_location')) {
			$data_required[] = 'country_id';
			$data_required[] = 'zone_id';
			$data_required[] = 'location';
    	}

		if ($this->config->get('member_data_field_weight') || $this->config->get('member_data_field_shipping')) {
			$data_optional[] = 'weight';
    	}

		if ($this->config->get('member_data_field_dimensions') || $this->config->get('member_data_field_shipping')) {
			$data_optional[] = 'length';
			$data_optional[] = 'width';
			$data_optional[] = 'height';
			$data_optional[] = 'weight_class_id';
			$data_optional[] = 'length_class_id';
		}

		if ($this->config->get('member_data_field_image')) {
			$data_required[] = 'image';
		}

		if ($this->config->get('member_data_field_quantity')) {
			$data_optional[] = 'quantity';
			$data_optional[] = 'subtract';
			$data_optional[] = 'minimum';
			$data_optional[] = 'stock_status_id';
		}

		// if ($this->config->get('member_data_field_filter')) {
		// 	$data_optional[] = 'product_filters';
		// }

		// if ($this->config->get('member_data_field_related')) {
		// 	$data_optional[] = 'product_related';
		// }

		if ($this->config->get('member_data_field_store')) {
			$data_optional[] = 'product_store';
		}

		if ($this->config->get('member_data_field_tax')) {
			$data_optional[] = 'tax_class_id';
		}

		if ($this->config->get('member_data_field_shipping')) {
			$data_optional[] = 'shipping';
			$data_optional[] = 'product_shipping';
		}

		if ($this->config->get('member_data_field_part_numbers')) {
			$data_optional[] = 'mpn';
			$data_optional[] = 'sku';
			$data_optional[] = 'upc';
			$data_optional[] = 'ean';
			$data_optional[] = 'jan';
		}

		if ($this->config->get('member_data_field_date')) {
			$data_optional[] = 'date_available';
    	}

		// if ($this->config->get('member_data_field_tags')) {
		// 	$data_optional[] = 'product_description';
    	// }

		// if ($this->config->get('member_data_field_sort_order')) {
		// 	$data_optional[] = 'sort_order';
    	// }

		if ($this->config->get('member_tab_image')) {
			$data_optional[] = 'product_image';
		}

		if ($this->config->get('member_tab_discount')) {
			$data_optional[] = 'price_discount';
			$data_optional[] = 'discount_quantity';
		}

		if ($this->config->get('member_tab_special')) {
			$data_optional[] = 'price_special';
		}

		// if ($this->config->get('member_tab_attribute')) {
		// 	$data_optional[] = 'product_attributes';
		// }
        //
		// if ($this->config->get('member_tab_option')) {
		// 	$data_optional[] = 'product_option';
		// }
        //
		// if ($this->config->get('member_tab_download')) {
		// 	$data_optional[] = 'product_download';
		// }
        //
		// if ($this->config->get('member_tab_design')) {
		// 	$data_optional[] = 'product_layout';
		// }
        //
		// if ($this->config->get('member_tab_reward_points')) {
		// 	$data_optional[] = 'product_reward';
		// }
        //
		// if ($this->config->get('member_shipping')) {
		// 	$data_required[] = 'product_shipping';
		// }

		foreach ($data as $key => $value) {
			if (!in_array($key, array_merge($data_required, $data_optional), true)) {
				// clearn/remove all extra fields
				unset($data[$key]);
			} else if (in_array($key, $data_required, true) && !isset($data[$key])) {
				// ensure all required data fields are submitted
				$this->appendError('fields', sprintf($this->language->get('error_field_missing'), $key));
			}
		}

		return !$this->getError('fields');
	}

  	private function validateForm(&$data) {
		if (!$this->validateFields($data)) {
			$this->log->write(json_encode($this->getError('fields')));
			$this->setError('warning', implode('<br />', $this->getError('fields')));
			return;
		}

		// uncomment to enforce unique Product Names
		/*
    	if (isset($this->request->get['listing_id'])) {
			$product_description_data = $this->model_account_product->getProductDescriptions($this->request->get['listing_id']);
		} else {
			$product_description_data = array();
		}
		*/

    	foreach ($data['product_description'] as $language_id => $value) {
      		if (!$this->validateStringLength($value['name'], 3, 255) || !preg_match('/^[a-zA-Z0-9-_ \(\)\.\'\"\/\&]*$/', htmlspecialchars_decode($value['name']))) {
        		$this->appendError('name', sprintf($this->language->get('error_name'), 3, 255), $language_id);
      		}

      		/* uncomment to enforce unique Product Names
      		if (((isset($this->request->get['listing_id']) && ($product_description_data[$language_id]['name'] != $value['name'])) || (!isset($this->request->get['listing_id']))) && ($this->model_account_product->getTotalProductsByName($value['name'], $language_id))) {
				$this->appendError('name', $this->language->get('error_name_exists'), $language_id);
      		}
      		*/

      		if (!$this->validateStringLength($value['description'], $this->config->get('member_data_field_description_min'), $this->config->get('member_data_field_description_max'))) {
				$this->appendError('description', sprintf($this->language->get('error_description'), $this->config->get('member_data_field_description_min'), $this->config->get('member_data_field_description_max')), $language_id);
      		}

      		if (!$this->validateStringLength($value['tag'], 0, 255)) {
				$this->appendError('tag', $this->language->get('error_tag'), $language_id);
      		}
    	}

		if ($this->config->get('member_data_field_image') && empty($data['image'])) {
			$this->setError('image', $this->language->get('error_image'));
		}

		if ($this->config->get('member_data_field_image') && !empty($data['product_image']) && count($data['product_image']) > $this->config->get('member_image_max_number')) {
			$this->setError('images', sprintf($this->language->get('error_max_images'), $this->config->get('member_image_max_number')));
		}

		if ($data['for_sale'] == 0) {
	    	if ($data['value'] < 0 || ($data['value'] > 0 && !$this->validatePrice($data['value']))) {
	      		$this->setError('value', $this->language->get('error_value'));
	    	}
		} else {
			if (!$this->validatePrice($data['price'])) {
				$this->setError('price', $this->language->get('error_price'));
			}
		}

    	if ($this->config->get('member_tab_discount') && isset($data['price_discount']) && ($data['price_discount'] !== '')) {
			if ($data['price_discount'] == 0 || !$this->validatePrice($data['price_discount'])) {
				$this->setError('price', $this->language->get('error_discount'));
			} else if ($data['discount_quantity'] < 0 || !$this->validateNumber($data['discount_quantity'])) {
				$this->setError('price', $this->language->get('error_discount_quantity'));
			} else if ($data['discount_quantity'] && !empty($data['price_special'])) {
				$this->setError('price', $this->language->get('error_special_discount'));
			}
		}

    	if ($this->config->get('member_tab_special') && isset($data['price_special']) && ($data['price_special'] !== '')) {
			if ($data['price_special'] == 0 || !$this->validatePrice($data['price_special'])) {
				$this->setError('price', $this->language->get('error_special'));
			}
		}

		if ($data['for_sale'] == 1 && $data['type'] == 1 && !$this->customer->getMemberPayPal()) {
			$this->setError('type', sprintf($this->language->get('error_type'), $this->url->link('account/member', 'no_paypal=true', 'SSL') . '#jump_to_paypal'));
		}

    	if ($this->config->get('member_data_field_date') && empty($data['date_available'])) {
      		$this->setError('date_available', $this->language->get('error_date_available'));
    	}

		if ($this->config->get('member_data_field_category')) {
			if (empty($data['category_id'])) {
				$this->setError('category', $this->language->get('error_category'));
			}

			if (empty($data['sub_category_id'])) {
				// $this->setError('category', $this->language->get('error_category'));
				$this->setError('category_sub', $this->language->get('error_category_sub'));
			}
		}

		if ($this->config->get('member_data_field_manufacturer') && empty($data['manufacturer_id'])) {
			$this->setError('manufacturer', $this->language->get('error_manufacturer'));
		}

		if ($this->config->get('member_data_field_model')) {
			if (empty($data['model']) || !$this->validateStringLength($data['model'], 1, 128)) {
				$this->setError('model', $this->language->get('error_model'));
		    }
		}

		if (empty($data['size']) || !$this->validateStringLength($data['size'], 1, 128)) {
			$this->setError('size', $this->language->get('error_size'));
		}

		if ($this->config->get('member_data_field_part_numbers') && $data['for_sale'] == 1) {
			if (isset($data['mpn']) && isset($data['sku']) && isset($data['upc']) && isset($data['ean']) && isset($data['jan'])) {
				if (utf8_strlen($data['mpn']) > 64 || utf8_strlen($data['sku']) > 64 || utf8_strlen($data['upc']) > 64 || utf8_strlen($data['ean']) > 64 || utf8_strlen($data['jan']) > 64) {
					$this->setError('part_number', $this->language->get('error_part_number'));
				}
			}
		}

    	if ($this->config->get('member_data_field_quantity') && $data['for_sale'] == 1) {
			if (isset($data['quantity'])) {
				if ($data['type'] == 1 && ($data['quantity'] <= 0 || !$this->validateNumber($data['quantity']))) {
					$this->setError('quantity', $this->language->get('error_quantity'));
				// } else if ($data['type'] == 0 && ($data['quantity'] > 1 || !$this->validateNumber($data['quantity']))) {
				// 	$this->setError('quantity', $this->language->get('error_quantity_classified'));
				}
			}

			if (isset($data['minimum'])) {
				if ($data['type'] == 1 && ($data['minimum'] <= 0 || !$this->validateNumber($data['minimum']))) {
					$this->setError('minimum', $this->language->get('error_minimum'));
				}
			}
    	}

    	if ($data['year'] && !$this->validateYear($data['year'])) {
      		$this->setError('year', $this->language->get('error_year'));
    	}

    	if (!$data['country_id']) {
      		$this->setError('country', $this->language->get('country_id'));
    	}

    	if (!$data['condition_id']) {
      		$this->setError('condition', $this->language->get('error_condition'));
    	}

    	if ($data['country_id'] && !$data['zone_id']) {
      		$this->setError('zone', $this->language->get('error_zone'));
    	}

		if ($this->config->get('member_data_field_location') && empty($data['location'])) {
			$this->setError('location', $this->language->get('error_location'));
		}

		if ($this->config->get('member_data_field_weight') && empty($data['digital'])) {
			if (($this->config->get('member_data_field_shipping') && $data['shipping'] && !$data['weight']) || ($data['weight'] && !preg_match('/^(?:[0-9]*)(?:\.\d{1,2})?$/', $data['weight']))) {
				$this->setError('weight', $this->language->get('error_weight'));
			}
		}

		if ($this->config->get('member_data_field_dimensions')) {
			if (($data['length'] && !preg_match('/^(?:[0-9]*)(?:\.\d{1,2})?$/', $data['length'])) || ($data['width'] && !preg_match('/^(?:[0-9]*)(?:\.\d{1,2})?$/', $data['width'])) || ($data['height'] && !preg_match('/^(?:[0-9]*)(?:\.\d{1,2})?$/', $data['height']))) {
				$this->setError('dimensions', $this->language->get('error_dimensions_format'));
			}

			if ($this->config->get('member_data_field_shipping') && $data['shipping']) {  //  && empty($data['digital'] ?
				if ($data['length'] <= 0 || $data['width'] <= 0 || $data['height'] <= 0) {
					$dimensions_absolute = array(
						'length' 	=> abs($data['length']),
						'width' 	=> abs($data['width']),
						'height' 	=> abs($data['height'])
					);
					if (count(array_filter($dimensions_absolute)) < 2) {
						$this->setError('dimensions', $this->language->get('error_dimensions_two'));
					}
				}

				// USPS shipping (must be measured in inches and pounds!)
				if (false && $this->config->get('member_shipping')
					&& !empty($data['product_shipping'])
					&& $data['length_class_id'] == 3
					&& $data['weight_class_id'] == 5) {
					// check that "Shipping (fixed per item)" Option value is set if product is too large for USPS (> 108 inches in length + girth, with padding, or > 70 pounds)
					$max_usps_shipping_dimensions = 108;
					$max_usps_shipping_weight = 70;

					if (($data['product_shipping'][0]['first'] <= 0)
						&& ($data['product_shipping'][1]['first'] <= 0)
						&& ((($data['length'] + (2 * $data['width']) + (2 * $data['height']) + (10 * $this->config->get('member_shipping_padding'))) > $max_usps_shipping_dimensions) || ($data['weight'] > $max_usps_shipping_weight))) {
						$this->session->data['dimensions'] = $this->language->get('error_toolarge_usps');
					} else if ($data['length'] >= 12 || $data['width'] >= 12 || $data['height'] >= 12) {
						if ($data['length'] <= 0 || $data['width'] <= 0 || $data['height'] <= 0) {
							$this->setError('dimensions', $this->language->get('error_dimensions_all'));
						}
					}
				}
			}
			// removed requirement if shipping not selected
			/*else {
				if ($data['length'] <= 0 && $data['width'] <= 0 && $data['height'] <= 0) {
					$this->setError('dimensions', $this->language->get('error_dimensions_one'));
				}
			} */
		}

    	if ($this->config->get('member_data_field_shipping') && $this->config->get('product_shipping_status') && $data['shipping']) {
			$no_rates = true;

			foreach ($data['product_shipping'] as $geo_zone_rates) {
				if ($geo_zone_rates['first'] > 0 || $geo_zone_rates['first'] == '0.00' || $geo_zone_rates['first'] == '0') {
					$no_rates = false;
					break;
				}
			}

			if ($no_rates) {
				$this->setError('shipping', $this->language->get('error_shipping'));
			}
    	}

		// disabled to allow FREE shipping
		/*
		if ((empty($data['product_shipping'][0]['first']) && !empty($data['product_shipping'][1]['first'])) || (!empty($data['product_shipping'][0]['first']) && empty($data['product_shipping'][1]['first']))) {
			$this->setError('shipping', $this->language->get('error_shipping'));
		}
		*/

		// multiple geographical stores
		/*
		if ($this->config->get('member_data_field_store') && empty($data['product_store'])) {
      		$this->setError('product_store', $this->language->get('error_product_store'));
    	}
		*/

		if ($this->hasError() && !$this->getError('warning')) {
			$this->setError('warning', $this->language->get('error_warning'));
		}

    	return !$this->hasError();
  	}

  	private function prepareData(&$data) {
		if (empty($data)) return;

		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');

		$data = strip_tags_decode($data);

		$listing_name_changed = false;
		$listing_images_changed = false;

		$listing_keyword = !empty($data['year']) && $data['year'] != '0000' && strpos($data['product_description'][1]['name'], $data['year']) === false
		  ? friendly_url(clean_path(html_entity_decode($data['product_description'][1]['name'] . '-' . $data['year'], ENT_QUOTES, 'UTF-8')))
		  : friendly_url(clean_path(html_entity_decode($data['product_description'][1]['name'], ENT_QUOTES, 'UTF-8')));

		// remove any empty images
  		if ($this->config->get('member_tab_image') && isset($data['product_image'])) {
  			foreach ($data['product_image'] as $image_key => $product_image) {
  				if (empty($product_image['image']) || $product_image['image'] == 'no_image.jpg') {
  					unset($data['product_image'][$image_key]);
  				}
  			}
  		}

		// check if title and/or images have changed for an existing listing
		if (!empty($this->request->get['listing_id'])) {
			$product_description_data = $this->model_account_product->getProductDescriptions($this->request->get['listing_id']);
			$product_images = $this->model_account_product->getProductImages($this->request->get['listing_id']);

			if ($data['product_description'][1]['name'] != $product_description_data[1]['name']) {
				$listing_name_changed = true;
			}

			if ($this->config->get('member_tab_image') && isset($data['product_image']) && $data['product_image'] != $product_images) {
				$listing_images_changed = true;
			}
		}

		// set SEO url alias keyword if new listing or if title of existing listing has changed
		if (empty($this->request->get['listing_id']) || $listing_name_changed) {
			$data['keyword'] = 'listing-' . $listing_keyword . '-' . mt_rand();
		}

		// move images and update image filepaths if new listing, or if title and/or images of existing listing has changed
		if (empty($this->request->get['listing_id']) || $listing_name_changed || $listing_images_changed) {
			if ($this->customer->getMemberImagesDirectory()) {
				$destination_sub_directory = 'data/' . $this->customer->getMemberImagesDirectory();

				$new_image = $this->model_tool_image->move($data['image'], $destination_sub_directory, $listing_keyword . '-' . mt_rand());

				if ($new_image) {
					$data['image'] = $new_image;
				}

				if ($this->config->get('member_tab_image') && !empty($data['product_image'])) {
					foreach ($data['product_image'] as $key => $product_image) {
						$new_image = $this->model_tool_image->move($product_image['image'], $destination_sub_directory, $listing_keyword . '-' . ($key + 2) . '-' . mt_rand());

						if ($new_image) {
							$data['product_image'][$key]['image'] = $new_image;
						}
					}
				}
			}
		}

		$data['model'] = ucwords($data['model']);
		$data['size'] = ucwords($data['size']);

		// meta description prep
		$product_category_main = $this->model_catalog_category->getCategory($data['category_id']);
		$product_category_sub = $this->model_catalog_category->getCategory($data['sub_category_id']);

		if (!empty($data['third_category_id'])) {
			$product_category_third = $this->model_catalog_category->getCategory($data['third_category_id']);
		}

		$product_manufacturer = $this->model_catalog_manufacturer->getManufacturer($data['manufacturer_id']);
		$product_country = $this->model_localisation_country->getCountry($data['country_id']);
		$product_zone = $this->model_localisation_zone->getZone($data['zone_id']);

		foreach ($data['product_description'] as $language_id => $value) {
			$data['product_description'][$language_id]['name'] = ucwords($value['name']);
			$data['product_description'][$language_id]['description'] = trim($value['description']);
			$data['product_description'][$language_id]['tag'] = preg_replace('/[\s,#]+/', ', $1', trim(strtolower($value['tag']), " \t\n\r\0\x0B,#"));
			$data['product_description'][$language_id]['meta_keyword'] = strtolower($value['tag']);

			$meta_description = $this->language->get('entry_name') . ': ' . ucwords($value['name']) . '; ';
			$meta_description .= $this->language->get('entry_manufacturer') . ': ' . $product_manufacturer['name'] . '; ';
			$meta_description .= $this->language->get('entry_model') . ': ' . $data['model'] . '; ';
			$meta_description .= $this->language->get('entry_year') . ': ' . ($data['year'] ? $data['year'] : $this->language->get('text_unknown')) . '; ';
			$meta_description .= $this->language->get('entry_size') . ': ' . $data['size'] . '; ';
			$meta_description .= $this->language->get('entry_category') . ': ' . $product_category_main['name'] . ', ' . $product_category_sub['name'];
			$meta_description .= !empty($product_category_third['name']) ? ', ' . $product_category_third['name'] . '; ' : ';';
			$meta_description .= $this->language->get('entry_location') . ': ' . $data['location'] . ', ' . $product_zone['name'] . ', ' . $product_country['name'] . '; ';
			// $meta_description .= $this->language->get('entry_description') . ': ' . utf8_substr(trim(strip_tags_decode(html_entity_decode($value['description'], ENT_QUOTES, 'UTF-8'))), 0, 100) . ';';

			$data['product_description'][$language_id]['meta_description'] = $meta_description;
		}

		if ($data['price']) {
			$data['price'] = $this->currency->convert((float)$data['price'], $this->currency->getCode(), $this->config->get('config_currency'));
		}

		if ($data['value']) {
			$data['value'] = $this->currency->convert((float)$data['value'], $this->currency->getCode(), $this->config->get('config_currency'));
		}

		$data['product_category'] = array($data['category_id'], $data['sub_category_id']);

		if (isset($data['third_category_id'])) {
			$data['product_category'][] = $data['third_category_id'];
		}

		$data['product_store'] = array('0');

		$data['date_available'] = date('Y-m-d H:i:s', time() - (60 * 60 * 24)); // now minus 1 day (ensures immediate)
		$data['date_expiration'] = $this->customer->getMemberPermission('auto_renew_enabled')
			? date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 365 * 10))
			: date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 90)); // 10 years : 90 days

		if ($this->customer->getMemberPermission('inventory_enabled')) {
			if (!$this->config->get('member_data_field_quantity') || $data['type'] == 0 || !$this->customer->getMemberPayPal()) {
				$data['quantity'] = '0';
				$data['minimum'] = '1';
				$data['subtract'] = '1';
			}

			if (!$this->config->get('member_data_field_stock')) {
				$data['stock_status_id'] = $this->config->get('config_stock_status_id');
			}

			if (!$this->config->get('member_data_field_part_numbers')) {
				$data['mpn'] = '';
				$data['sku'] = '';
				$data['upc'] = '';
				$data['ean'] = '';
				$data['jan'] = '';
			}

			$data['sort_order'] = '1'; // highest priority (1-10 scale)
		}

		if (!$this->customer->getMemberPermission('inventory_enabled')) {
			$data['mpn'] = '';
			$data['sku'] = '';
			$data['upc'] = '';
			$data['ean'] = '';
			$data['jan'] = '';
			$data['quantity'] = ($data['type'] == 1 && $this->customer->getMemberPayPal()) ? '1' : '0';
			$data['minimum'] = '1';
			$data['subtract'] = '1';
			$data['stock_status_id'] = $this->config->get('config_stock_status_id');
			$data['sort_order'] = '10'; // lowest priority (1-10 scale)
		}

		$data['isbn'] = 'userpost';
		$data['points'] = '0';
		$data['tax_class_id'] = '0'; // None

		// Price / Value
		if (!$data['for_sale']) {
			$price = $data['value'];
			$data['price'] =  round($data['value'], 2);
			$data['quantity'] = -1;
			unset($data['price_special']);
		} else {
			if (!empty($data['price_special'])) {
				$price = $data['price_special'];
				$data['price_special'] =  round($data['price_special'], 2);
			} else {
				$price = $data['price'];
			}

			$data['price'] = round($data['price'], 2);
		}

		$price_rounded = (int)$price;

		$customer_groups = $this->model_account_product->getCustomerGroups();

		// Filters
		$data['product_filters'] = array();

		$data['product_filters'][] = (int)$data['condition_id']; // condition

		if ($data['for_sale'] || $price > 0) {
			if ($price_rounded < 50) {
				$data['product_filters'][] = 1;
			} else if ($price_rounded <= 100) {
				$data['product_filters'][] = 2;
			} else if ($price_rounded <= 500) {
				$data['product_filters'][] = 3;
			} else if ($price_rounded <= 1000) {
				$data['product_filters'][] = 4;
			} else {
				$data['product_filters'][] = 5; // more than $1,000
			}
		}

		if ($data['year']) {
			$listing_age = (int)date('Y') - (int)$data['year']; // this year minus listing year

			if ($listing_age < 2) {
				$data['product_filters'][] = 11;
			} else if ($listing_age <= 5) {
				$data['product_filters'][] = 12;
			} else if ($listing_age <= 10) {
				$data['product_filters'][] = 13;
			} else  {
				$data['product_filters'][] = 14; // greater than 10 years old
			}
		} else {
			$data['product_filters'][] = 15; // unknown
		}

		// Discount
		if (isset($data['price_discount']) && $this->customer->getMemberPermission('discount_enabled') && $this->config->get('member_tab_discount') && $data['price_discount'] !== '') {
			$data['product_discount'] = array();

			foreach ($customer_groups as $customer_group) {
				$data['product_discount'][$customer_group['customer_group_id']]['customer_group_id'] = $customer_group['customer_group_id'];
				$data['product_discount'][$customer_group['customer_group_id']]['quantity'] = $data['discount_quantity'];
				$data['product_discount'][$customer_group['customer_group_id']]['priority'] = 1;
				$data['product_discount'][$customer_group['customer_group_id']]['price'] = $this->currency->convert($data['price_discount'], $this->currency->getCode(), $this->config->get('config_currency'));
				$data['product_discount'][$customer_group['customer_group_id']]['date_start'] = '0000-00-00';
				$data['product_discount'][$customer_group['customer_group_id']]['date_end'] = '0000-00-00';
			}
		}

		// Special
		if (isset($data['price_special']) && $this->customer->getMemberPermission('special_enabled') && $this->config->get('member_tab_special') && $data['price_special'] !== '') {
			$data['product_special'] = array();

			foreach ($customer_groups as $customer_group) {
				$data['product_special'][$customer_group['customer_group_id']]['customer_group_id'] = $customer_group['customer_group_id'];
				$data['product_special'][$customer_group['customer_group_id']]['priority'] = 1;
				$data['product_special'][$customer_group['customer_group_id']]['price'] = $this->currency->convert($data['price_special'], $this->currency->getCode(), $this->config->get('config_currency'));
				$data['product_special'][$customer_group['customer_group_id']]['date_start'] = '0000-00-00';
				$data['product_special'][$customer_group['customer_group_id']]['date_end'] = '0000-00-00';
			}
		}

		// Digital Downloads
		if (!empty($data['digital'])) {
			$data['length'] = 0;
			$data['width'] = 0;
			$data['height'] = 0;
			$data['weight'] = 0;
		}

		// Reward
		$data['product_reward'] = array();

		foreach ($customer_groups as $customer_group) {
			$data['product_reward'][$customer_group['customer_group_id']]['points'] = (int)$data['price'];
		}

		// Related
		/*
		if (isset($data['related']) && $this->customer->getMemberPermission('related_enabled') && $this->config->get('member_tab_related') && $data['related'] !== '') {
			$data['product_related'] = array();
		}
		*/

		// Option
		/*
		if (isset($data['option']) && $this->customer->getMemberPermission('option_enabled') && $this->config->get('member_tab_option') && $data['option'] !== '') {
			$data['product_option'] = array();
		}
		*/

		// Attribute
		/*
		if (isset($data['attribute']) && $this->customer->getMemberPermission('attribute_enabled') && $this->config->get('member_tab_attribute') && $data['attribute'] !== '') {
			$data['product_attributes'] = array();
		}
		*/

		// Layout (Design)
	}

  	private function validateProduct($product_id) {
		$product_info = $this->model_account_product->getProduct($product_id);

    	if (!$product_info) {
      		$this->setError('warning', $this->language->get('error_permission'));
    	}

		return !$this->hasError();
  	}

  	private function validateExpire() {
		// allow for all for now
		return true;

    	// if (!$this->customer->getMemberPermission('inventory_enabled')) {
      	// 	$this->setError('warning', $this->language->get('error_permission_delete'));
    	// }
        //
		// return !$this->hasError();
  	}

  	private function validateRenew() {
    	if ($this->customer->getMemberPermission('auto_renew_enabled')) {
      		$this->setError('warning', $this->language->get('error_permission_renew'));
    	}

		return !$this->hasError();
  	}

  	private function validateTransfer() {
		// allow for all for now
		return true;

		// return $this->customer->getMemberPermission('inventory_enabled') ? true : false;
  	}

  	private function validateNew() {
		// allow nearly unlimited
		$absolute_max = 1000;

		$customer_max_products = $this->customer->getMemberMaxProducts();

		$product_total = $this->model_account_product->getTotalProducts();

		if (($customer_max_products != '-1' || $product_total >= $absolute_max) && ($product_total >= $customer_max_products)) {
			$this->setError('warning', $this->language->get('error_max_products'));
		}

		return !$this->hasError();
  	}

	public function display_attributes() {
		$this->init();

		if (!$this->customer->getMemberPermission('inventory_enabled')) {
			return false;
		}

		$limit = (isset($this->request->get['limit']) ? $this->request->get['limit'] : 100);

		$data = array(
			'start'       => 0,
			'limit'       => $limit
		);

		$results = $this->model_account_product->getAttributes($data);

		if ($results) {
			/*
			$sort_order = array();

			foreach ($results as $key => $value) {
				$sort_order[$key] = $value['name'];
			}

			array_multisort($sort_order, SORT_ASC, $results);
			* */

			$output .= '  <h1>List of Attributes Available</h1>' . "\n";
			$output .= '  <table class="list bordered">' . "\n";
			$output .= '    <thead>' . "\n";
			$output .= '      <tr>' . "\n";
			$output .= '        <td class="left">Name</td>' . "\n";
			$output .= '        <td class="left">Group</td>' . "\n";
			$output .= '      <tr>' . "\n";
			$output .= '   </thead>' . "\n";
			$output .= '   <tbody>' . "\n";
			foreach ($results as $result) {
			$output .= '      <tr>' . "\n";
			$output .= '        <td class="left">' . strip_tags_decode(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')) . '</td>' . "\n";
			$output .= '        <td class="left">' . $result['attribute_group'] . '</td>' . "\n";
			$output .= '      <tr>' . "\n";
			}
			$output .= '   </tbody>' . "\n";
			$output .= ' </table>' . "\n";
		} else {
			$output  = '<div class="bgWhite"><p style="color:red;">Sorry, there are no attributes available.</p></div>';
		}

		$this->response->setOutput($output);
	}

	public function display_options() {
		$this->init();

		if (!$this->customer->getMemberPermission('inventory_enabled')) {
			return false;
		}

		$limit = (isset($this->request->get['limit']) ? $this->request->get['limit'] : 100);

		$data = array(
			'start'       => 0,
			'limit'       => $limit
		);

		$results = $this->model_account_product->getOptions($data);

		if ($results) {
			$sort_order = array();

			foreach ($results as $key => $value) {
				$sort_order[$key] = $value['name'];
			}

			array_multisort($sort_order, SORT_ASC, $results);

			$output .= '  <h1>List of Options Available</h1>' . "\n";
			$output .= '  <table class="list bordered">' . "\n";
			$output .= '    <thead>' . "\n";
			$output .= '      <tr>' . "\n";
			$output .= '        <td class="left">Name</td>' . "\n";
			$output .= '        <td class="left">Type</td>' . "\n";
			$output .= '      <tr>' . "\n";
			$output .= '   </thead>' . "\n";
			$output .= '   <tbody>' . "\n";
			foreach ($results as $result) {
				$type = '';

				if ($result['type'] == 'select' || $result['type'] == 'radio' || $result['type'] == 'checkbox' || $result['type'] == 'image') {
					$type = $this->language->get('text_choose');
				}

				if ($result['type'] == 'text' || $result['type'] == 'textarea') {
					$type = $this->language->get('text_input');
				}

				if ($result['type'] == 'file') {
					$type = $this->language->get('text_file');
				}

				if ($result['type'] == 'date' || $result['type'] == 'datetime' || $result['type'] == 'time') {
					$type = $this->language->get('text_date');
				}

			$output .= '      <tr>' . "\n";
			$output .= '        <td class="left">' . strip_tags_decode(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')) . '</td>' . "\n";
			$output .= '        <td class="left">' . $type . ' (' . $result['type'] . ')</td>' . "\n";
			$output .= '      <tr>' . "\n";
			}
			$output .= '   </tbody>' . "\n";
			$output .= ' </table>' . "\n";
		} else {
			$output  = '<div class="bgWhite"><p style="color:red;">Sorry, there are no options available.</p></div>';
		}

		$this->response->setOutput($output);
	}

	public function display_geo_zones() {
		$this->init();

		if (!$this->customer->getMemberPermission('inventory_enabled')) {
			return false;
		}

		$geo_zones_info = array();
		$geo_zones = $this->model_account_product->getGeoZones();

		if ($geo_zones) {
			foreach ($geo_zones as $geo_zone) {
				$geo_zones_info[] = array (
					'geo_zone_id'           => $geo_zone['geo_zone_id'],
					'geo_zone_name'         => $geo_zone['name'],
					'geo_zone_description'  => $geo_zone['description'],
					'geo_zone_zones'        => $this->model_account_product->getZonesByGeoZoneId($geo_zone['geo_zone_id'])
				);
			}
		}

		$output = '<div class="bgWhite" style="max-width:760px;margin:0 auto;">' . "\n";

		if ($geo_zones_info) {
			/*
			$sort_order = array();

			foreach ($zones as $key => $value) {
				$sort_order[$key] = $value['zone'];
			}

			array_multisort($sort_order, SORT_ASC, $zones);
			* */

			$output .= '<h1>' . $this->language->get('heading_shipping_zones') . '</h1>' . "\n";

			foreach ($geo_zones_info as $geo_zone_info) {
				$output .= '<div class="geo-zone-display">'. "\n";
				$output .= '<h3>' . $geo_zone_info['geo_zone_name'] . ' <span>(' . $geo_zone_info['geo_zone_description'] . ')</span>' . '</h3>'. "\n";
				$output .= '<div class="none">' . "\n";
				$output .= '  <table class="list bordered">' . "\n";
				$output .= '   <tbody>' . "\n";

				foreach ($geo_zone_info['geo_zone_zones'] as $geo_zone_zone_info) {
					$output .= '      <tr>' . "\n";
					$output .= '        <td class="left">' . strip_tags_decode(html_entity_decode($geo_zone_zone_info['name'], ENT_QUOTES, 'UTF-8')) . ' (' . $geo_zone_zone_info['code'] . ')' . '</td>' . "\n";
					$output .= '      <tr>' . "\n";
				}

				$output .= '   </tbody>' . "\n";
				$output .= ' </table>' . "\n";
				$output .= '</div>' . "\n";
				$output .= '</div>' . "\n";
			}
		} else {
			$output  .= '<div class="warning"><p>' . $this->language->get('text_shipping_zone_none') . '</p><span class="icon"><i class="fa fa-warning"></i></span></div>';
		}

		$output .= '</div>' . "\n";

		$this->response->setOutput($output);
	}

}
?>
