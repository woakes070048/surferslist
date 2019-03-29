<?php
class ModelAccountOrder extends Model {
	use Contact;

	public function getOrder($order_id) {
		$order_query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "order
			WHERE order_id = '" . (int)$order_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
			AND order_status_id > '0'
		");

		if (!$order_query->num_rows) {
			$order_query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "order
				WHERE order_no = '" . (int)$order_id . "'
				AND customer_id = '" . (int)$this->customer->getId() . "'
				AND order_status_id > '0'
			");
		}

		$order_info = $order_query->num_rows ? $order_query->row : array();

		if (!$order_info) {
			return false;
		}

		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$this->load->model('localisation/language');

		$payment_country_info = $this->model_localisation_country->getCountry($order_info['payment_country_id'], null);
		$shipping_country_info = $this->model_localisation_country->getCountry($order_info['shipping_country_id'], null);
		$payment_zone_info = $this->model_localisation_zone->getZone($order_info['payment_zone_id'], null);
		$shipping_zone_info = $this->model_localisation_zone->getZone($order_info['shipping_zone_id'], null);
		$language_info = $this->model_localisation_language->getLanguage($order_info['language_id'], null);

		return array(
			'order_id'                => $order_info['order_id'],
			'order_no'                => $order_info['order_no'],
			'invoice_no'              => $order_info['invoice_no'],
			'invoice_prefix'          => $order_info['invoice_prefix'],
			'store_id'                => $order_info['store_id'],
			'store_name'              => $order_info['store_name'],
			'store_url'               => $order_info['store_url'],
			'customer_id'             => $order_info['customer_id'],
			'firstname'               => $order_info['firstname'],
			'lastname'                => $order_info['lastname'],
			'telephone'               => $order_info['telephone'],
			'fax'                     => $order_info['fax'],
			'email'                   => $order_info['email'],
			'payment_firstname'       => $order_info['payment_firstname'],
			'payment_lastname'        => $order_info['payment_lastname'],
			'payment_company'         => $order_info['payment_company'],
			'payment_company_id'      => $order_info['payment_company_id'],
			'payment_tax_id'          => $order_info['payment_tax_id'],
			'payment_address_1'       => $order_info['payment_address_1'],
			'payment_address_2'       => $order_info['payment_address_2'],
			'payment_postcode'        => $order_info['payment_postcode'],
			'payment_city'            => $order_info['payment_city'],
			'payment_zone_id'         => $order_info['payment_zone_id'],
			'payment_zone'            => $order_info['payment_zone'],
			'payment_zone_code'       => $payment_zone_info ? $payment_zone_info['code'] : '',
			'payment_country_id'      => $order_info['payment_country_id'],
			'payment_country'         => $order_info['payment_country'],
			'payment_iso_code_2'      => $payment_country_info ? $payment_country_info['iso_code_2'] : '',
			'payment_iso_code_3'      => $payment_country_info ? $payment_country_info['iso_code_3'] : '',
			'payment_address_format'  => $order_info['payment_address_format'],
			'payment_method'          => $order_info['payment_method'],
			'payment_code'            => $order_info['payment_code'],
			'shipping_firstname'      => $order_info['shipping_firstname'],
			'shipping_lastname'       => $order_info['shipping_lastname'],
			'shipping_company'        => $order_info['shipping_company'],
			'shipping_address_1'      => $order_info['shipping_address_1'],
			'shipping_address_2'      => $order_info['shipping_address_2'],
			'shipping_postcode'       => $order_info['shipping_postcode'],
			'shipping_city'           => $order_info['shipping_city'],
			'shipping_zone_id'        => $order_info['shipping_zone_id'],
			'shipping_zone'           => $order_info['shipping_zone'],
			'shipping_zone_code'      => $shipping_zone_info ? $shipping_zone_info['code'] : '',
			'shipping_country_id'     => $order_info['shipping_country_id'],
			'shipping_country'        => $order_info['shipping_country'],
			'shipping_iso_code_2'     => $shipping_country_info ? $shipping_country_info['iso_code_2'] : '',
			'shipping_iso_code_3'     => $shipping_country_info ? $shipping_country_info['iso_code_3'] : '',
			'shipping_address_format' => $order_info['shipping_address_format'],
			'shipping_method'         => $order_info['shipping_method'],
			'shipping_code'           => $order_info['shipping_code'],
			'comment'                 => $order_info['comment'],
			'total'                   => $order_info['total'],
			'order_status_id'         => $order_info['order_status_id'],
			'language_id'             => $order_info['language_id'],
			'language_code'           => $language_info ? $language_info['code'] : '',
			'language_filename'       => $language_info ? $language_info['filename'] : '',
			'language_directory'      => $language_info ? $language_info['directory'] : '',
			'currency_id'             => $order_info['currency_id'],
			'currency_code'           => $order_info['currency_code'],
			'currency_value'          => $order_info['currency_value'],
			'ip'                      => $order_info['ip'],
			'forwarded_ip'            => $order_info['forwarded_ip'],
			'user_agent'              => $order_info['user_agent'],
			'accept_language'         => $order_info['accept_language'],
			'date_modified'           => $order_info['date_modified'],
			'date_added'              => $order_info['date_added']
		);
	}

	public function getOrderStatusId($order_id) {
		$query = $this->db->query("
			SELECT order_status_id
			FROM " . DB_PREFIX . "order
			WHERE order_id = '" . (int)$order_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");

		return $query->num_rows ? $query->row['order_status_id'] : 0;
	}

	public function getOrderStatusIdByOrderNo($order_no) {
		$query = $this->db->query("
			SELECT order_status_id
			FROM " . DB_PREFIX . "order
			WHERE order_no = '" . (int)$order_no . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");

		return $query->num_rows ? $query->row['order_status_id'] : 0;
	}

	public function getOrders($start = 0, $limit = 20) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 1;
		}

		$sql = "
			SELECT DISTINCT o.order_id
			, o.order_no
			, o.firstname
			, o.lastname
			, os.name as status
			, o.date_added
			, o.total
			, o.currency_code
			, o.currency_value
			, m.customer_id AS member_customer_id
			, m.member_account_id AS member_id
			, m.member_account_name AS member
			FROM " . DB_PREFIX . "order o
			LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id)
				AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id)
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (op.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "customer_member_account m ON (pm.member_account_id = m.member_account_id)
			WHERE o.customer_id = '" . (int)$this->customer->getId() . "'
			AND o.order_status_id > '0'
			ORDER BY o.order_id DESC
			LIMIT " . (int)$start . "," . (int)$limit
		;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getOrderProducts($order_id) {
		$query = $this->db->query("
			SELECT p.*
			, op.*
			, op.image AS image
			, op.name AS name
			, m.name AS manufacturer
			, m.image AS manufacturer_image
			, member.member_account_id AS member_id
			, member.member_account_name
			FROM " . DB_PREFIX . "order_product op
			LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id = p.product_id)
			LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (op.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "customer_member_account member ON (pm.member_account_id = member.member_account_id)
			WHERE order_id = '" . (int)$order_id . "'
		");

		return $query->rows;
	}

	public function getOrderOptions($order_id, $order_product_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "order_option
			WHERE order_id = '" . (int)$order_id . "'
			AND order_product_id = '" . (int)$order_product_id . "'
		");

		return $query->rows;
	}

	public function getOrderVouchers($order_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "order_voucher
			WHERE order_id = '" . (int)$order_id . "'
		");

		return $query->rows;
	}

	public function getOrderTotals($order_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "order_total
			WHERE order_id = '" . (int)$order_id . "'
			ORDER BY sort_order
		");

		return $query->rows;
	}

	public function getOrderDownloads($order_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "order_download
			WHERE order_id = '" . (int)$order_id . "'
			ORDER BY name
		");

		return $query->rows;
	}

	public function getTotalOrders() {
		$query = $this->db->query("
			SELECT COUNT(order_id) AS total
			FROM " . DB_PREFIX . "order
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
			AND order_status_id > '0'
		");

		return $query->row['total'];
	}

	public function getTotalOrderProductsByOrderId($order_id) {
		$query = $this->db->query("
			SELECT COUNT(order_product_id) AS total
			FROM " . DB_PREFIX . "order_product
			WHERE order_id = '" . (int)$order_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalOrderVouchersByOrderId($order_id) {
		$query = $this->db->query("
			SELECT COUNT(order_voucher_id) AS total
			FROM " . DB_PREFIX . "order_voucher
			WHERE order_id = '" . (int)$order_id . "'
		");

		return $query->row['total'];
	}

	public function addOrderHistory($order_no, $data) {
		$this->load->model('checkout/order');
		$order_id = $this->model_checkout_order->getOrderIdByOrderNo($order_no);

		if (!$order_id) {
			return;
		}

		// Add order history
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "order_history
			SET order_id = '" . (int)$order_id . "'
			, order_status_id = '" . (int)$data['order_status_id'] . "'
			, notify = '" . (isset($data['emailed']) ? (int)$data['emailed'] : '0') . "'
			, comment = '" . $this->db->escape(strip_tags_decode(htmlspecialchars_decode($data['comment']))) . "'
			, member_customer_id = '" . (int)$this->customer->getId() . "'
			, date_added = NOW()
		");

		$order_info = $this->getOrder($order_id);

		// Email any gift voucher mails
		if ($this->config->get('config_complete_status_id') == $data['order_status_id']) {
			$this->load->model('checkout/voucher');
			$this->model_checkout_voucher->confirm($order_id);
		}

		// email notification
		if ($data['emailed']) {
			$language = new Language($order_info['language_directory']);
			$language->load($order_info['language_filename']);
			$language->load('mail/order');

			$message = sprintf($language->get('text_update_greeting'), $order_no, date($language->get('date_format_long'), strtotime($order_info['date_added']))) . "\n\n";
			/*
			if ($order_info['customer_id']) {
				$message .= $language->get('text_update_link') . "\n";
				$message .= html_entity_decode($order_info['store_url'] . 'index.php?route=account/order/info&order_no=' . $order_no, ENT_QUOTES, 'UTF-8') . "\n\n";
			}
			*/
			if ($data['comment']) {
				$message .= $language->get('text_update_comment') . "\n";
				$message .= strip_tags_decode(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
			}

			$message .= $language->get('text_update_reply') . "\n\n";
			$message .= "---\n\n";
			$message .= $language->get('text_update_footer') . "\n\n";

			$bcc = array();

			if ($this->config->get('member_email_customers')) {
				$this->load->model('checkout/order');
				$order_member_info = $this->model_checkout_order->getOrderMember($order_id);

				if (!empty($order_member_info['email'])) {
					$bcc[] = $order_member_info['email'];
				}

				if ($order_info['email'] != $this->customer->getEmail()) {
					$bcc[] = $this->customer->getEmail();
				}
			}

			$mail_sent = $this->sendEmail(array(
				'to' 		=> $order_info['email'],
				'from' 		=> $this->config->get('config_email_noreply'),
				'sender' 	=> $order_info['store_name'],
				'subject' 	=> sprintf($language->get('text_update_subject'), $order_info['store_name'], $order_no),
				'message' 	=> $message,
				'bcc' 		=> $bcc,
				'reply' 	=> $this->config->get('config_email_noreply'),
				'admin'		=> true
			));
		}
	}

	public function getOrderHistories($order_no, $start = 0, $limit = 10) {
		$this->load->model('checkout/order');
		$order_id = $this->model_checkout_order->getOrderIdByOrderNo($order_no);

		if (!$order_id) {
			return array();
		}

		$sql = "
			SELECT oh.date_added
			, os.name AS status
			, oh.comment
			, oh.notify AS emailed
			, oh.member_customer_id AS customer_id
			, member.member_account_id AS member_id
			, member.member_account_name AS member
			FROM " . DB_PREFIX . "order_history oh
			LEFT JOIN " . DB_PREFIX . "order_status os ON oh.order_status_id = os.order_status_id
				AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "customer_member_account member ON oh.member_customer_id = member.customer_id
				AND oh.member_customer_id <> 0
			WHERE oh.order_id = '" . (int)$order_id . "'
			ORDER BY oh.date_added DESC
		";

		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$sql .= " LIMIT " . (int)$start . "," . (int)$limit;

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalOrderHistories($order_no) {
		$this->load->model('checkout/order');
		$order_id = $this->model_checkout_order->getOrderIdByOrderNo($order_no);

		if (!$order_id) {
			return 0;
		}

		$query = $this->db->query("
			SELECT COUNT(order_history_id) AS total
			FROM " . DB_PREFIX . "order_history
			WHERE order_id = '" . (int)$order_id . "'
		");

		return $query->row['total'];
	}
}

