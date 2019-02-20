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
		
		$this->data['button_continue'] = $this->language->get('button_back');

		$this->data['products'] = array();

		foreach ($this->session->data['wishlist'] as $key => $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				// $image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_wishlist_width'), $this->config->get('config_image_wishlist_height'));

				$thumb = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), 'autocrop');
				$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'autocrop');

				if ($product_info['quantity'] <= 0) {
					$stock = $product_info['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $product_info['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					$salebadges = round((($product_info['price'] - $product_info['special']) / $product_info['price']) * 100, 0);
					$savebadges = $this->currency->format(($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'))) - ($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'))));
				} else {
					$special = false;
					$salebadges = false;
					$savebadges = false;
				}

				$this->data['products'][] = array(
					'product_id' => $product_info['product_id'],
					'image'      => $image,
					'thumb'      => $thumb,
					'name'       => $product_info['name'],
					'model'      => $product_info['model'],
					'quantity'   => $product_info['quantity'],
					'stock'      => $stock,
					'price'      => $price,
					'special'    => $special,
					'salebadges' => $salebadges,
					'savebadges' => $savebadges,
					'href'       => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'remove'     => $this->url->link('account/wishlist', 'remove=' . $product_info['product_id'], 'SSL')
				);
			} else {
				unset($this->session->data['wishlist'][$key]);
			}
		}

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->template = '/template/account/wishlist.tpl';

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
