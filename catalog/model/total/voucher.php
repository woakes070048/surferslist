<?php
class ModelTotalVoucher extends Model {
	public function getTotal(&$total_data, &$total, &$taxes) {
		if (isset($this->session->data['voucher'])) {
			$this->load->language('total/voucher');

			$this->load->model('checkout/voucher');

			$voucher_info = $this->model_checkout_voucher->getVoucher($this->session->data['voucher']);

			$amount = 0;

			if ($voucher_info && !empty($voucher_info['amount'])) {
				$amount += $voucher_info['amount'];
			}

			if (!empty($this->session->data['current_voucher_value'])) {
				$amount += $this->session->data['current_voucher_value'];
			}

			if ($amount) {
				if ($amount > $total) {
					$amount = $total;
				}

				$total_data[] = array(
					'code'       => 'voucher',
					'title'      => sprintf($this->language->get('text_voucher'), $this->session->data['voucher']),
					'text'       => $this->currency->format(-$amount),
					'value'      => -$amount,
					'sort_order' => $this->config->get('voucher_sort_order')
				);

				$total -= $amount;
			}
		}
	}

	public function confirm($order_info, $order_total) {
		$code = '';

		$start = strpos($order_total['title'], '(') + 1;
		$end = strrpos($order_total['title'], ')');

		if ($start && $end) {
			$code = substr($order_total['title'], $start, $end - $start);
		}

		$this->load->model('checkout/voucher');

		$voucher_info = $this->model_checkout_voucher->getVoucher($code);

		if ($voucher_info) {
			$this->model_checkout_voucher->redeem($voucher_info['voucher_id'], $order_info['order_id'], $order_total['value']);
		}
	}
}
