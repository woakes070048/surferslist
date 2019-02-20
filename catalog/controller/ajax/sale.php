<?php
class ControllerAjaxSale extends Controller {

    public function history_order() {
		if (!isset($this->request->get['order_no'])) {
			return false;
		}

		$this->data = $this->load->language('account/order');

		$this->data['error'] = '';
		$this->data['success'] = '';

		$this->load->model('account/order');
		$this->load->model('account/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->config->get('member_report_sales_history') && (!$this->data['error'])) {
			$data = strip_tags_decode($this->request->post);

			if ((utf8_strlen($this->request->post['comment']) < 10) || (utf8_strlen($this->request->post['comment']) > 255)) {
				$order_id = $this->model_account_order->getOrderIdByOrderNo($this->request->get['order_no']);
				$order_member_info = $this->model_account_order->getOrderMember($order_id);

				if (!empty($order_member_info['customer_id'])) {
					$contact_member = $this->url->link('information/contact', 'contact_id=' . $order_member_info['customer_id'], 'SSL');
				} else {
					$contact_member = $this->url->link('information/contact', '', 'SSL');
				}

				$this->data['error'] = sprintf($this->language->get('error_comment'), 10, 255, $contact_member);
			} else {
				$data['order_status_id'] = $this->model_account_order->getOrderStatusIdByOrderNo($this->request->get['order_no']);
				$this->model_account_order->addOrderHistory($this->request->get['order_no'], $data);
				$this->data['success'] = $this->language->get('text_history_success');
			}
		}

		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		$this->data['histories'] = array();

		$results = $this->model_account_order->getOrderHistories($this->request->get['order_no'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			if (!empty($result['member'])) {
				$member = $result['member'];
			} else if (!empty($result['customer_id'])) {
				$customer_info = $this->model_account_customer->getCustomer($result['customer_id']);
				$member = $customer_info ? $customer_info['lastname'] . ', ' . $customer_info['firstname'] : $this->language->get('text_non_member');
			} else {
				$member = $this->language->get('text_admin');
			}

			$this->data['histories'][] = array(
				'date_added' => date($this->language->get('date_format_medium'), strtotime($result['date_added'])),
				'status'     => $result['status'],
				'member'     => $member,
				'emailed'    => $result['emailed'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'comment'    => utf8_substr(nl2br($result['comment']), 0, 25) . $this->language->get('text_ellipses'),
				'comment_full' => nl2br($result['comment'])
			);
		}

		$history_total = $this->model_account_order->getTotalOrderHistories($this->request->get['order_no']);

        $this->data['pagination'] = $this->getPagination($history_total, $page, 12, 'account/order/history', 'order_no=' . $this->request->get['order_no']);

		$this->template = '/template/account/order_history.tpl';

		$this->response->setOutput($this->render());
	}

    public function history_sale() {
		if (!isset($this->request->get['sale_id'])) {
			return false;
		}

		$this->data = $this->load->language('account/sales');

		$this->data['error'] = '';
		$this->data['success'] = '';

		$this->load->model('account/sales');
		$this->load->model('account/customer');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->config->get('member_report_sales_history') && (!$this->data['error'])) {
			if ((utf8_strlen($this->request->post['comment']) < 10) || (utf8_strlen($this->request->post['comment']) > 255)) {
				$sale_info = $this->model_account_sales->getSale($this->request->get['sale_id']);
				$this->data['error'] = sprintf($this->language->get('error_comment'), 10, 255, $this->url->link('information/contact', 'contact_id=' . $sale_info['customer_id'], 'SSL'));
			} else {
				$this->model_account_sales->addSalesHistory($this->request->get['sale_id'], strip_tags_decode($this->request->post));
				$this->data['success'] = $this->language->get('text_history_success');
			}
		}

		$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

		$this->data['histories'] = array();

		$results = $this->model_account_sales->getSalesHistories($this->request->get['sale_id'], ($page - 1) * 10, 10);

		foreach ($results as $result) {
			if (!empty($result['member'])) {
				$author = $result['member'];
			} else if (!empty($result['customer_id'])) {
				$customer_info = $this->model_account_customer->getCustomer($result['customer_id']);
				$author = $customer_info ? $customer_info['lastname'] . ', ' . $customer_info['firstname'] : $this->language->get('text_non_member');
			} else {
				$author = $this->language->get('text_admin');
			}

			$this->data['histories'][] = array(
				'date_added' => date($this->language->get('date_format_medium'), strtotime($result['date_added'])),
				'status'     => $result['status'],
				'member'     => $author,
				'emailed'    => $result['emailed'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'comment'    => utf8_substr(nl2br($result['comment']), 0, 50) . $this->language->get('text_ellipses'),
				'comment_full' => nl2br($result['comment'])
			);
		}

		$history_total = $this->model_account_sales->getTotalSaleHistories($this->request->get['sale_id']);

        $this->data['pagination'] = $this->getPagination($history_total, $page, 12, 'account/order/history', 'sale_id=' . $this->request->get['sale_id']);

		$this->template = '/template/account/order_history.tpl';

		$this->response->setOutput($this->render());
	}

	public function download_sale() {
		// disabled
		if (true || !isset($this->request->get['order_option_id']) || !$this->config->get('member_tab_download')) {
			return false;
		}

        $order_option_id = (int)$this->request->get['order_option_id'];

		$this->load->model('account/sales');

		$option_info = $this->model_account_sales->getSaleOption($this->request->get['sale_id'], $order_option_id);

		if ($option_info && $option_info['type'] == 'file') {
			$file = DIR_DOWNLOAD . $option_info['value'];
			$mask = basename(utf8_substr($option_info['value'], 0, utf8_strrpos($option_info['value'], '.')));

			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Description: File Transfer');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));

					readfile($file, 'rb');
					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			return false;
		}
	}

}
