<?php
class ControllerModuleCart extends Controller {
	public function index() {
		$this->data = $this->load->language('module/cart');

		if (isset($this->request->get['remove'])) {
			$this->cart->remove($this->request->get['remove']);

			unset($this->session->data['vouchers'][$this->request->get['remove']]);
		}

		// Totals
		$this->load->model('setting/extension');

		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();

		// Display prices
		if ((!$this->config->get('config_customer_price') || ($this->config->get('config_customer_price') && $this->customer->isLogged())) && $this->cart->countProducts()) {
			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}
			}

			$sort_order = array();

			foreach ($total_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $total_data);
		}

		$this->data['totals'] = $total_data;

		$this->data['language_id'] = (int)$this->config->get('config_language_id');

		$this->data['text_items'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));

		$this->load->model('tool/image');

		$this->data['products'] = array();

		foreach ($this->cart->getProducts() as $product) {
			$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), 'autocrop');

			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['option_value'];
				} else {
					$filename = $this->encryption->decrypt($option['option_value']);

					$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . $this->language->get('text_ellipses') : $value),
					'type'  => $option['type']
				);
			}

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
			} else {
				$total = false;
			}

			$this->data['products'][] = array(
				'key'      => $product['key'],
				'thumb'    => $image,
				'name'     => $product['name'],
				'model'    => $product['model'],
				'manufacturer' => $product['manufacturer'],
				'option'   => $option_data,
				'available' => $product['available'],
				'quantity' => $product['quantity'],
				'price'    => $price,
				'total'    => $total,
				'href'     => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}

		// Gift Voucher
		$this->data['vouchers'] = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$this->data['vouchers'][] = array(
					'key'         => $key,
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'])
				);
			}
		}

		$this->data['cart_info'] = $this->config->get('config_cart_info');

		$this->data['cart'] = $this->url->link('checkout/cart', '', 'SSL');
		$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

		$this->template = 'template/module/cart.tpl';

		$this->response->setOutput($this->render());
	}
}

