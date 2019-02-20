<?php
class ControllerAccountSales extends Controller {
	use ValidateField;

	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/sales', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if (!$this->customer->validateProfile()) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}

	public function index() {
		$this->data = $this->load->language('account/sales');

		$this->load->model('account/sales');
		$this->load->model('catalog/member');

    	$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/sales'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$url = isset($this->request->get['page']) ? '&page=' . (int)$this->request->get['page'] : '';

		if ($this->config->get('member_report_sales_commission') || $this->config->get('member_report_sales_tax') || $this->config->get('member_report_sales_tax_add')) {
			$this->data['text_total_calculation'] = $this->language->get('text_total');

			if ($this->config->get('member_report_sales_commission')) {
				$this->data['text_total_calculation'] .= ' - ' . $this->language->get('text_commission');
			}

			if ($this->config->get('member_report_sales_tax_add') == 1) {
				$this->data['text_total_calculation'] .= ' + ' . $this->language->get('text_tax');
			} elseif ($this->config->get('member_report_sales_tax_add') == 0) {
				$this->data['text_total_calculation'] .= ' - ' . $this->language->get('text_tax');
			}
		} else {
			$this->data['text_total_calculation'] = '';
		}

		$this->data['button_continue'] = $this->language->get('button_back');

		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		$total_products = 0;
		$total_commissions = 0;
		$total_tax = 0;
		$total_grand = 0;
		$currencies = array();

		$this->data['sales'] = array();
		$this->data['totals'] = array();

		$sales_total = $this->model_account_sales->getTotalSales();

		$results = $this->model_account_sales->getSales(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$product_total = $this->model_account_sales->getTotalSalesProductsBySalesId($result['order_id']);
			// $voucher_total = $this->model_account_sales->getTotalSalesVouchersBySalesId($result['sale_id']); // member edit - removed because no specific vouchers configured yet for Member-enabled customer accounts

			if ($this->config->get('member_report_sales_tax_add') == 0) {
				$result['products_tax'] = -abs($result['products_tax']);
			}

			if ($this->config->get('member_report_sales_tax_add') != 2) {
				$total = $result['products_total'] + $result['products_tax'] - $result['products_commission'];
			} else {
				$total = $result['products_total'] - $result['products_commission'];
			}

			$customer_member = $this->model_catalog_member->getMemberByCustomerId($result['customer_id']);

			if ($customer_member) {
				$member_href = $this->url->link('product/member/info', 'member_id=' . $customer_member['member_account_id'], 'SSL');
			} else {
				$member_href = false;
			}

			$order_number = !empty($result['order_no']) ? $result['order_no'] : $result['order_id'];

			$this->data['sales'][] = array(
				'order_id'   => $order_number,
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'member_href' => $member_href,
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_medium'), strtotime($result['date_added'])),
				'products'   => ($product_total), //+ $voucher_total),
				'revenue'	 => $this->currency->format(($result['products_total'] - $result['products_commission']), $result['currency_code'], $result['currency_value']),
				'sales'		 => $this->currency->format($result['products_total'], $result['currency_code'], $result['currency_value']),
				'total'      => $this->currency->format($total, $result['currency_code'], $result['currency_value']),
				'commission' => $this->currency->format($result['products_commission'], $result['currency_code'], $result['currency_value']),
				'tax'		 => $this->currency->format(abs($result['products_tax']), $result['currency_code'], $result['currency_value']),
				'href'       => $this->url->link('account/sales/info', 'sale_id=' . $order_number, 'SSL'),
			);

			// keep track of currencies
			if (!array_key_exists($result['currency_code'], $currencies)) {
				$currencies[$result['currency_code']] = $result['currency_value'];
			}
			// calculate totals only if not more than one currency
			if (count($currencies) <= 1) {
				$total_products += $product_total;
				$total_commissions += $result['products_commission'];
				$total_tax += $result['products_tax'];
				$total_grand += $result['products_total'];
			}
		}

		// totals only relevant if every sale made in the same currency
		if (count($currencies) == 1) {
			foreach ($currencies as $key => $value) {
				$this->data['totals']['products'] = $total_products;
				$this->data['totals']['commissions'] = $this->currency->format($total_commissions, $key, $value);
				$this->data['totals']['tax'] = $this->currency->format(abs($total_tax), $key, $value);
				$this->data['totals']['sales'] = $this->currency->format($total_grand, $key, $value);
				$this->data['totals']['revenue'] = $this->currency->format($total_grand - $total_commissions, $key, $value);

				if ($this->config->get('member_report_sales_tax_add') != 2) {
					$this->data['totals']['grand'] = $this->currency->format($total_grand + $total_tax - $total_commissions, $key, $value);
				} else {
					$this->data['totals']['grand'] = $this->data['totals']['revenue'];
				}
			}
		}

		if (!$this->customer->getMemberPayPal() && !isset($this->session->data['warning'])) {
			$this->session->data['warning'] = sprintf($this->language->get('text_no_paypal'), $this->url->link('account/member', 'no_paypal=true', 'SSL') . '#jump_to_paypal');
		}

		$this->data['pagination'] = $this->getPagination($sales_total, $page, 10, 'account/sales');

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		$this->template = '/template/account/sales_list.tpl';

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

	public function info() {
		$sale_id = isset($this->request->get['sale_id']) ? (int)$this->request->get['sale_id'] : 0;
		$customer_id = $this->customer->getId() ? $this->customer->getId() : 0;

		$this->data = $this->load->language('account/sales');
		$this->load->model('account/sales');

		$sale_info = $this->model_account_sales->getSale($sale_id);

		if ($sale_info) {
			$order_id = (int)$sale_info['order_id'];

			$this->document->setTitle($this->language->get('text_sales'));

			$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
			$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
			$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/sales'));
			$this->addBreadcrumb($this->language->get('text_sales'), $this->url->link('account/sales/info', 'sale_id=' . $this->request->get['sale_id']));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

			$url = isset($this->request->get['page']) ? '&page=' . (int)$this->request->get['page'] : '';

      		$this->data['heading_title'] = $this->language->get('text_sales');

			if ($sale_info['invoice_no']) {
				$this->data['invoice_no'] = $sale_info['invoice_prefix'] . $sale_info['invoice_no'];
			} else {
				$this->data['invoice_no'] = '';
			}

			$this->data['sale_id'] = $sale_id;
			$this->data['date_added'] = date($this->language->get('date_format_long'), strtotime($sale_info['date_added']));

			if ($sale_info['payment_address_format']) {
      			$format = $sale_info['payment_address_format'];
    		} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

    		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
			);

			$replace = array(
	  			'firstname' => $sale_info['payment_firstname'],
	  			'lastname'  => $sale_info['payment_lastname'],
	  			'company'   => $sale_info['payment_company'],
      			'address_1' => $sale_info['payment_address_1'],
      			'address_2' => $sale_info['payment_address_2'],
      			'city'      => $sale_info['payment_city'],
      			'postcode'  => $sale_info['payment_postcode'],
      			'zone'      => $sale_info['payment_zone'],
				'zone_code' => $sale_info['payment_zone_code'],
      			'country'   => $sale_info['payment_country']
			);

			$this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

      		$this->data['payment_method'] = $sale_info['payment_method'];

			if ($sale_info['shipping_address_format']) {
      			$format = $sale_info['shipping_address_format'];
    		} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

    		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
			);

			$replace = array(
	  			'firstname' => $sale_info['shipping_firstname'],
	  			'lastname'  => $sale_info['shipping_lastname'],
	  			'company'   => $sale_info['shipping_company'],
      			'address_1' => $sale_info['shipping_address_1'],
      			'address_2' => $sale_info['shipping_address_2'],
      			'city'      => $sale_info['shipping_city'],
      			'postcode'  => $sale_info['shipping_postcode'],
      			'zone'      => $sale_info['shipping_zone'],
				'zone_code' => $sale_info['shipping_zone_code'],
      			'country'   => $sale_info['shipping_country']
			);

			$this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$this->data['shipping_method'] = $sale_info['shipping_method'];

			$this->data['products'] = array();

			if ($this->config->get('member_report_sales_commission') || $this->config->get('member_report_sales_tax') || $this->config->get('member_report_sales_tax_add')) {
				$text_total_calculation = $this->language->get('text_total');

				if ($this->config->get('member_report_sales_commission')) {
					$text_total_calculation .= ' - ' . $this->language->get('text_commission');
				}

				if ($this->config->get('member_report_sales_tax_add') == 1) {
					$text_total_calculation .= ' + ' . $this->language->get('text_tax');
				} elseif ($this->config->get('member_report_sales_tax_add') == 0) {
					$text_total_calculation .= ' - ' . $this->language->get('text_tax');
				}
			} else {
				$text_total_calculation = '';
			}

			$total_sales = array('title' => $this->language->get('text_total'), 'text' => '', 'value' => 0);
			$total_commissions = array('title' => $this->language->get('text_commission'), 'text' => '', 'value' => 0);
			$total_tax = array('title' => $this->language->get('text_tax'), 'text' => '', 'value' => 0);
			$total_revenue = array('title' => $this->language->get('text_revenue') . ' (' . $this->language->get('text_total') . ' - ' . $this->language->get('text_commission') . ')', 'text' => '', 'value' => 0);
			$total_grand = array('title' => $this->language->get('text_grand_total') . ' (' . $text_total_calculation . ')', 'text' => '', 'value' => 0);

			$products = $this->model_account_sales->getSalesProducts($order_id);

			$this->load->model('tool/image');

      		foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_account_sales->getSalesOptions($order_id, $product['order_product_id']);

        		foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => $option['value'],
							'type'  => $option['type']
						);
					} else {
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.')),
							'type'  => $option['type'],
							'href'  => $this->url->link('account/sales/download', '&order_id=' . $order_id . '&order_option_id=' . $option['order_option_id'], 'SSL')
						);
					}
				}

				$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'), 'fw');

        		$this->data['products'][] = array(
          			'name'		=> $product['name'],
					'image'		=> $image,
          			'model'		=> $product['model'],
 					'member_id' => $product['member_customer_id'],
         			'member'	=> $product['member_account_name'],
					'manufacturer_id'  => $product['manufacturer_id'],
					'manufacturer'     => $product['manufacturer'],
					'manufacturer_href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product['manufacturer_id'], 'SSL'),
					'manufacturer_image'        => $product['manufacturer_image'],
          			'option'	=> $option_data,
          			'quantity'	=> $product['quantity'],
          			'price'		=> $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $sale_info['currency_code'], $sale_info['currency_value']),
					'total'		=> $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $sale_info['currency_code'], $sale_info['currency_value']),
					//'return'   => $this->url->link('account/return/insert', 'sale_id=' . $sale_info['sale_id'] . '&product_id=' . $product['product_id'], 'SSL')
        		);

				if ($this->config->get('member_report_sales_tax_add') == 0) {
					$product['tax'] = -abs($product['tax']);
				}

				$total_sales['value'] += $product['total'];
				$total_commissions['value'] += $product['commission'];
				$total_tax['value'] += $product['tax'] * $product['quantity'];
      		}

      		$total_revenue['value'] += $total_sales['value'] - $total_commissions['value'];

			if ($this->config->get('member_report_sales_tax_add') != 2) {
				$total_grand['value'] += $total_revenue['value'] + $total_tax['value'];
			} else {
				$total_grand['value'] += $total_revenue['value'];
			}

			$total_sales['text'] = $this->currency->format($total_sales['value'], $sale_info['currency_code'], $sale_info['currency_value']);
			$total_commissions['text'] = '- ' . $this->currency->format($total_commissions['value'], $sale_info['currency_code'], $sale_info['currency_value']);
			$total_tax['text'] = $this->currency->format(abs($total_tax['value']), $sale_info['currency_code'], $sale_info['currency_value']);

			if ($this->config->get('member_report_sales_tax_add') == 0) {
				$total_tax['text'] = '- ' . $total_tax['text'];
			} elseif ($this->config->get('member_report_sales_tax_add') == 1) {
				$total_tax['text'] = '+ ' . $total_tax['text'];
			}

			$total_revenue['text'] = $this->currency->format($total_revenue['value'], $sale_info['currency_code'], $sale_info['currency_value']);
			$total_grand['text'] = $this->currency->format($total_grand['value'], $sale_info['currency_code'], $sale_info['currency_value']);

			/* Voucher */
			$this->data['vouchers'] = array();

			$vouchers = $this->model_account_sales->getSalesVouchers($order_id);

			foreach ($vouchers as $voucher) {
				$this->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $sale_info['currency_code'], $sale_info['currency_value'])
				);
			}

			// returns totals only for products for this member, because unique member per order is currently enforced
			// (i.e. forced TRUE until multi-member ordering feature is developed...)
			if (true || !$this->config->get('member_report_sales_unique')) {
				$this->data['totals'] = $this->model_account_sales->getSalesTotals($order_id);
			} else {
				if ($this->config->get('member_report_sales_commission') && $this->config->get('member_report_sales_tax')) {
					$this->data['totals'] = array($total_sales, $total_commissions, $total_tax, $total_grand);
				} elseif ($this->config->get('member_report_sales_commission') && !$this->config->get('member_report_sales_tax')) {
					$this->data['totals'] = array($total_sales, $total_commissions, $total_grand);
				} elseif (!$this->config->get('member_report_sales_commission') && $this->config->get('member_report_sales_tax')) {
					$this->data['totals'] = array($total_sales, $total_tax, $total_grand);
				} else {
					$this->data['totals'] = array($total_sales);
				}
			}

			$this->data['comment'] = nl2br($sale_info['comment']);

			$this->data['sales_statuses'] = $this->model_account_sales->getSalesStatuses();

			$this->data['sales_status_id'] = $sale_info['order_status_id'];

			$this->data['contact'] = $this->url->link('information/contact', 'contact_id=' . $sale_info['customer_id'], 'SSL');

			$this->data['help_comment'] = sprintf($this->language->get('help_comment'), 10, 255);

      		$this->data['continue'] = $this->url->link('account/sales', '', 'SSL');

			$this->document->addScript('catalog/view/root/javascript/account.js');

			$this->template = '/template/account/sales_info.tpl';

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
    	} else {
			$this->document->setTitle($this->language->get('text_sales'));

      		$this->data['heading_title'] = $this->language->get('text_sales');

      		$this->data['text_error'] = $this->language->get('text_error');

			$this->data['button_search'] = $this->language->get('button_search');

			$this->data['search'] = $this->url->link('product/search', '', 'SSL');

      		$this->data['button_continue'] = $this->language->get('button_back');

			$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
			$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
			$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('account/sales'));
			$this->addBreadcrumb($this->language->get('text_sales'), $this->url->link('account/sales/info', 'sale_id=' . $sale_id));

			$this->data['breadcrumbs'] = $this->getBreadcrumbs();

      		$this->data['continue'] = $this->url->link('account/sales', '', 'SSL');

			$this->template = '/template/error/not_found.tpl';

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
  	}

}
?>
