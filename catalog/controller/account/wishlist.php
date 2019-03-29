<?php
class ControllerAccountWishList extends Controller {

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/wishlist', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}
	}

	public function index() {
		$this->data = $this->load->language('account/wishlist');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (!isset($this->session->data['wishlist'])) {
			$this->session->data['wishlist'] = array();
		}

		if (isset($this->request->get['remove'])) {
			$key = array_search($this->request->get['remove'], $this->session->data['wishlist']);

			if ($key !== false) {
				unset($this->session->data['wishlist'][$key]);
			}

			$this->session->data['success'] = $this->language->get('text_remove');

			$this->redirect($this->url->link('account/wishlist', '', 'SSL'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/wishlist'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['products'] = array();

		foreach ($this->session->data['wishlist'] as $key => $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				$product_data = $this->getChild('product/data/info', $product_info);

				$product_data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), 'autocrop');
				$product_data['remove'] = $this->url->link('account/wishlist', 'remove=' . $product_data['product_id'], 'SSL');

				$this->data['products'][] = $product_data;
			} else {
				unset($this->session->data['wishlist'][$key]);
			}
		}

		$this->data['button_continue'] = $this->language->get('button_back');

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->template = 'template/account/wishlist.tpl';

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

