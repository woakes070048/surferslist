<?php
class ModelCheckoutOrder extends Model {
	use Contact;

	public function addOrder($data) {
		$order_id = $this->addNewOrder($data);

		if (!$order_id) {
			return 0;
		}

		foreach ($data['products'] as $product) {
			$order_product_id = $this->addOrderProduct($order_id, $product);

			if ($order_product_id) {
				foreach ($product['option'] as $option) {
					$this->addOrderProductOption($order_id, $order_product_id, $option);
				}

				foreach ($product['download'] as $download) {
					$this->addOrderProductDownload($order_id, $order_product_id, $product['quantity'], $download);
				}
			}
		}

		foreach ($data['vouchers'] as $voucher) {
			$this->addOrderVoucher($order_id, $voucher);
		}

		foreach ($data['totals'] as $total) {
			$this->addOrderTotal($order_id, $total);
		}

		return $order_id;
	}

	public function getOrder($order_id) {
		if (!$order_id) {
			return array();
		}

		$order_info = $this->getOrderInfo($order_id);

		if (!$order_info) {
			return array();
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
			'order_status'            => $order_info['order_status'],
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

	public function confirm($order_id, $order_status_id, $comment = '', $notify = false) {
		$order_info = $this->getOrder($order_id);

		if (!$order_info || $order_info['order_status_id']) {
			return;
		}

		$this->load->model('checkout/voucher');

		$this->checkFraud($order_info, $order_status_id);

		$this->checkBanIp($order_info, $order_status_id);

		if (!$this->updateOrderStatus($order_id, $order_status_id, 1, (($comment && $notify) ? $comment : ''))) {
			return;
		}

		$order_products = $this->getOrderProducts($order_id);

		if ($order_products && $this->customer->hasProfile()) {
			$this->load->model('account/product');
		}

		$order_product_customer_ids = array();

		foreach ($order_products as $order_product) {
			if (!in_array($order_product['member_customer_id'], $order_product_customer_ids)) {
				$order_product_customer_ids[] = $order_product['member_customer_id'];
			}

			// copy product to new member account, if profile has been activated by buyer and listing not linked to member profile for website
			if ($this->customer->hasProfile() && $order_product['member_customer_id']
				&& $order_product['member_account_id'] != $this->config->get('config_member_id')
				&& $order_product['member_customer_id'] != $this->config->get('config_customer_id')) {

				$this->model_account_product->copyProductToCustomer($order_product['product_id'], $order_product['member_customer_id'], $order_info['customer_id']);
			}

			// update product quantity and/or status of seller's listing, if necessary
			if (($order_product['member_customer_id'] && !$order_product['inventory_enabled']) || ($order_product['product_quantity'] && $order_product['product_subtract'] == '1')) {
				$this->updateProduct($order_product);
			}

			$order_product_options = $this->getOrderProductOptions($order_id, $order_product['order_product_id']);

			// update listing option quantities
			foreach ($order_product_options as $option) {
				if ($option['subtract'] == '1') {
					$this->updateProductOption($option['product_option_value_id'], $order_product['quantity']);
				}
			}

			$this->cache->delete('product_' . (int)$order_product['order_product_id']);
			$this->cache->delete('manufacturer_' . (int)$order_product['manufacturer_id']);
			$this->cache->delete('member_' . (int)$order_product['member_customer_id']);
		}

		if ($this->customer->hasProfile()) {
			$this->cache->delete('member_' . (int)$this->customer->getProfileId());
		}

		$this->cache->delete('product.');
		$this->cache->delete('category');

		$order_download = $this->hasOrderDownloads($order_id);

		$order_vouchers = $this->getOrderVouchers($order_id);

		foreach ($order_vouchers as $order_voucher) {
			$voucher_id = $this->model_checkout_voucher->addVoucher($order_id, $order_voucher);

			if ($voucher_id) {
				$this->updateOrderVoucher($voucher_id, $order_voucher['order_voucher_id']);
			}
		}

		// Send out any gift voucher mails
		if ($this->config->get('config_complete_status_id') == $order_status_id) {
			$this->model_checkout_voucher->confirm($order_id);
		}

		$order_totals = $this->getOrderTotals($order_id);

		foreach ($order_totals as $order_total) {
			$this->load->model('total/' . $order_total['code']);

			if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
				$this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);
			}
		}

		// Send out order confirmation mail
		$mail_sent = $this->emailOrderConfirm($order_id, $order_status_id, $order_info, $order_products, $order_download, $order_vouchers, $order_totals, $notify, $comment);
	}

	public function update($order_id, $order_status_id, $comment = '', $notify = false) {
		$order_info = $this->getOrder($order_id);

		if (!$order_info || !$order_info['order_status_id']) {
			return;
		}

		$this->checkFraud($order_info, $order_status_id);

		$this->checkBanIp($order_info, $order_status_id);

		if (!$this->updateOrderStatus($order_id, $order_status_id, $notify, $comment)) {
			return;
		}

		// Send email notification for any gift voucher mails
		if ($this->config->get('config_complete_status_id') == $order_status_id) {
			$this->load->model('checkout/voucher');
			$this->model_checkout_voucher->confirm($order_id);
		}

		if ($notify) {
			$mail_sent = $this->emailOrderUpdate($order_id, $order_status_id, $order_info, $comment);
		}
	}

	public function getOrderNoByOrderId($order_id) {
		if (!$order_id) {
			return 0;
		}

		$query = $this->db->query("
			SELECT order_no
			FROM " . DB_PREFIX . "order
			WHERE order_id = '" . (int)$order_id . "'
		");

		return $query->num_rows ? $query->row['order_no'] : 0;
	}

	public function getOrderIdByOrderNo($order_no) {
		if (!$order_no) {
			return 0;
		}

		$query = $this->db->query("
			SELECT order_id
			FROM " . DB_PREFIX . "order
			WHERE order_no = '" . $this->db->escape($order_no) . "'
		");

		return $query->num_rows ? $query->row['order_id'] : 0;
	}

	public function getOrderMember($order_id) {
		if (!$order_id) {
			return array();
		}

		$query = $this->db->query("
			SELECT DISTINCT op.member_customer_id AS customer_id
			, member.member_account_id AS member_id
			, member.member_account_name AS member_name
			, member.member_paypal_account AS member_paypal
			, c.email AS email
			FROM " . DB_PREFIX . "order_product op
			INNER JOIN " . DB_PREFIX . "product_member pm ON (op.product_id = pm.product_id)
			INNER JOIN " . DB_PREFIX . "customer_member_account member ON (pm.member_account_id = member.member_account_id)
			LEFT JOIN " . DB_PREFIX . "customer c ON (pm.customer_id = c.customer_id)
				AND pm.customer_id <> 0
			WHERE op.order_id = '" . (int)$order_id . "'
			LIMIT 1
		");

		return $query->row;
	}

	protected function getOrderInfo($order_id) {
		$query = $this->db->query("
			SELECT *
			, (SELECT os.name
				FROM `" . DB_PREFIX . "order_status` os
				WHERE os.order_status_id = o.order_status_id
				AND os.language_id = o.language_id) AS order_status
			FROM `" . DB_PREFIX . "order` o
			WHERE o.order_id = '" . (int)$order_id . "'
		");

		return $query->num_rows ? $query->row : array();
	}

	protected function addNewOrder($data) {
		$this->db->query("
			INSERT INTO `" . DB_PREFIX . "order`
			SET order_no = '" . $this->db->escape($data['order_no']) . "'
			, invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "'
			, store_id = '" . (int)$data['store_id'] . "'
			, store_name = '" . $this->db->escape($data['store_name']) . "'
			, store_url = '" . $this->db->escape($data['store_url']) . "'
			, customer_id = '" . (int)$data['customer_id'] . "'
			, customer_group_id = '" . (int)$data['customer_group_id'] . "'
			, firstname = '" . $this->db->escape($data['firstname']) . "'
			, lastname = '" . $this->db->escape($data['lastname']) . "'
			, email = '" . $this->db->escape($data['email']) . "'
			, telephone = '" . $this->db->escape($data['telephone']) . "'
			, fax = '" . $this->db->escape($data['fax']) . "'
			, payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "'
			, payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "'
			, payment_company = '" . $this->db->escape($data['payment_company']) . "'
			, payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "'
			, payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "'
			, payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "'
			, payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "'
			, payment_city = '" . $this->db->escape($data['payment_city']) . "'
			, payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "'
			, payment_country = '" . $this->db->escape($data['payment_country']) . "'
			, payment_country_id = '" . (int)$data['payment_country_id'] . "'
			, payment_zone = '" . $this->db->escape($data['payment_zone']) . "'
			, payment_zone_id = '" . (int)$data['payment_zone_id'] . "'
			, payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "'
			, payment_method = '" . $this->db->escape($data['payment_method']) . "'
			, payment_code = '" . $this->db->escape($data['payment_code']) . "'
			, shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "'
			, shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "'
			, shipping_company = '" . $this->db->escape($data['shipping_company']) . "'
			, shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "'
			, shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "'
			, shipping_city = '" . $this->db->escape($data['shipping_city']) . "'
			, shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "'
			, shipping_country = '" . $this->db->escape($data['shipping_country']) . "'
			, shipping_country_id = '" . (int)$data['shipping_country_id'] . "'
			, shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "'
			, shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "'
			, shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "'
			, shipping_method = '" . $this->db->escape($data['shipping_method']) . "'
			, shipping_code = '" . $this->db->escape($data['shipping_code']) . "'
			, comment = '" . $this->db->escape($data['comment']) . "'
			, total = '" . (float)$data['total'] . "'
			, affiliate_id = '" . (int)$data['affiliate_id'] . "'
			, commission = '" . (float)$data['commission'] . "'
			, language_id = '" . (int)$data['language_id'] . "'
			, currency_id = '" . (int)$data['currency_id'] . "'
			, currency_code = '" . $this->db->escape($data['currency_code']) . "'
			, currency_value = '" . (float)$data['currency_value'] . "'
			, ip = '" . $this->db->escape($data['ip']) . "'
			, forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "'
			, user_agent = '" . $this->db->escape($data['user_agent']) . "'
			, accept_language = '" . $this->db->escape($data['accept_language']) . "'
			, date_added = NOW()
			, date_modified = NOW()
		");

		return $this->db->countAffected() ? $this->db->getLastId() : 0;
	}

	protected function addOrderProduct($order_id, $product) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "order_product
			SET order_id = '" . (int)$order_id . "'
			, product_id = '" . (int)$product['product_id'] . "'
			, member_customer_id = '" . (int)$product['member_customer_id'] . "'
			, image = '" . $this->db->escape($product['image']) . "'
			, commission = '" . (float)$product['member_commission'] . "'
			, name = '" . $this->db->escape($product['name']) . "'
			, model = '" . $this->db->escape($product['model']) . "'
			, quantity = '" . (int)$product['quantity'] . "'
			, price = '" . (float)$product['price'] . "'
			, total = '" . (float)$product['total'] . "'
			, tax = '" . (float)$product['tax'] . "'
			, reward = '" . (int)$product['reward'] . "'
		");

		return $this->db->countAffected() ? $this->db->getLastId() : 0;
	}

	protected function addOrderProductOption($order_id, $order_product_id, $option) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "order_option
			SET order_id = '" . (int)$order_id . "'
			, order_product_id = '" . (int)$order_product_id . "'
			, product_option_id = '" . (int)$option['product_option_id'] . "'
			, product_option_value_id = '" . (int)$option['product_option_value_id'] . "'
			, name = '" . $this->db->escape($option['name']) . "'
			, `value` = '" . $this->db->escape($option['value']) . "'
			, `type` = '" . $this->db->escape($option['type']) . "'
		");
	}

	protected function addOrderProductDownload($order_id, $order_product_id, $quantity, $download) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "order_download
			SET order_id = '" . (int)$order_id . "'
			, order_product_id = '" . (int)$order_product_id . "'
			, name = '" . $this->db->escape($download['name']) . "'
			, filename = '" . $this->db->escape($download['filename']) . "'
			, mask = '" . $this->db->escape($download['mask']) . "'
			, remaining = '" . (int)($download['remaining'] * $quantity) . "'
		");
	}

	protected function addOrderVoucher($order_id, $voucher) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "order_voucher
			SET order_id = '" . (int)$order_id . "'
			, description = '" . $this->db->escape($voucher['description']) . "'
			, code = '" . $this->db->escape($voucher['code']) . "'
			, from_name = '" . $this->db->escape($voucher['from_name']) . "'
			, from_email = '" . $this->db->escape($voucher['from_email']) . "'
			, to_name = '" . $this->db->escape($voucher['to_name']) . "'
			, to_email = '" . $this->db->escape($voucher['to_email']) . "'
			, voucher_theme_id = '" . (int)$voucher['voucher_theme_id'] . "'
			, message = '" . $this->db->escape($voucher['message']) . "'
			, amount = '" . (float)$voucher['amount'] . "'
		");
	}

	protected function addOrderTotal($order_id, $total) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "order_total
			SET order_id = '" . (int)$order_id . "'
			, code = '" . $this->db->escape($total['code']) . "'
			, title = '" . $this->db->escape($total['title']) . "'
			, text = '" . $this->db->escape($total['text']) . "'
			, `value` = '" . (float)$total['value'] . "'
			, sort_order = '" . (int)$total['sort_order'] . "'
		");
	}

	protected function updateOrderStatus($order_id, $order_status_id, $notify, $comment) {
		$updated = true;

		$this->db->query("
			UPDATE `" . DB_PREFIX . "order`
			SET order_status_id = '" . (int)$order_status_id . "'
			, date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'
		");

		if ($this->db->countAffected() <= 0) {
			$updated = false;
		}

		// Add order history
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "order_history
			SET order_id = '" . (int)$order_id . "'
			, order_status_id = '" . (int)$order_status_id . "'
			, notify = '" . (int)$notify . "'
			, comment = '" . $this->db->escape($comment) . "'
			, date_added = NOW()
		");

		if ($this->db->countAffected() <= 0) {
			$updated = false;
		}

		return $updated;
	}

	protected function getOrderProductOptions($order_id, $product_id) {
		$query = $this->db->query("
			SELECT oo.*
			, pov.subtract
			FROM " . DB_PREFIX . "order_option oo
			LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (oo.product_option_value_id = pov.product_option_value_id)
			WHERE oo.order_id = '" . (int)$order_id . "'
			AND oo.order_product_id = '" . (int)$product_id . "'
		");

		return $query->rows;
	}

	protected function getOrderStatusName($order_status_id, $language_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "order_status
			WHERE order_status_id = '" . (int)$order_status_id . "'
			AND language_id = '" . (int)$language_id . "'
		");

		return $query->num_rows ? $query->row['name'] : '';
	}

	protected function getOrderProducts($order_id) {
		$query = $this->db->query("
			SELECT op.*
			, pm.member_account_id
			, member.member_account_name AS member
			, member.member_group_id
			, cmg.inventory_enabled
			, m.manufacturer_id
			, m.name AS manufacturer
			, m.image AS manufacturer_image
			, p.quantity AS product_quantity
			, p.subtract AS product_subtract
			FROM " . DB_PREFIX . "order_product op
			LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id = p.product_id)
			LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (op.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "customer_member_account member ON (pm.member_account_id = member.member_account_id)
			LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (member.member_group_id = cmg.member_group_id)
			WHERE order_id = '" . (int)$order_id . "'
		");

		return $query->rows;
	}

	protected function getOrderVouchers($order_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "order_voucher
			WHERE order_id = '" . (int)$order_id . "'
		");

		return $query->rows;
	}

	protected function getOrderTotals($order_id) {
		$query = $this->db->query("
			SELECT *
			FROM `" . DB_PREFIX . "order_total`
			WHERE order_id = '" . (int)$order_id . "'
			ORDER BY sort_order ASC
		");

		return $query->rows;
	}

	protected function hasOrderDownloads($order_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "order_download
			WHERE order_id = '" . (int)$order_id . "'
		");

		return $query->num_rows ? true : false;
	}

	protected function updateProduct($order_product) {
		$remaining = $order_product['product_quantity'];

		$set = false;

		$sql = "
			UPDATE " . DB_PREFIX . "product
		";

		// decrease listing quantity by order amount
		if ($remaining > 0 && $order_product['product_subtract'] == '1') {
			$sql .= "
				SET quantity = (quantity - " . (int)$order_product['quantity'] . ")
			";

			$remaining -= (int)$order_product['quantity'];

			$set = true;
		}

		// expire listing if:
		// - NOT linked to a member account with inventory management enabled (i.e. basic vs. premium membership)
		// - no more quantity available
		if (($order_product['member_customer_id'] && !$order_product['inventory_enabled']) || $remaining <= 0) {
			$sql .= ($set ? ", " : "SET ") . "
				date_expiration = '" . date('Y-m-d H:i:s', time() - 86400) . "'
				, status = '0'
			";
		}

		$sql .= "
			WHERE product_id = '" . (int)$order_product['product_id'] . "'
		";

		$this->db->query($sql);

		return $this->db->countAffected() ? true : false;
	}

	protected function updateProductOption($product_option_value_id, $subtract) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "product_option_value
			SET quantity = (quantity - " . (int)$subtract . ")
			WHERE product_option_value_id = '" . (int)$product_option_value_id . "'
		");
	}

	protected function updateOrderVoucher($voucher_id, $order_voucher_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "order_voucher
			SET voucher_id = '" . (int)$voucher_id . "'
			WHERE order_voucher_id = '" . (int)$order_voucher_id . "'
		");
	}

	protected function emailOrderConfirm($order_id, $order_status_id, $order_info, $order_products, $order_download, $order_vouchers, $order_totals, $notify, $comment) {
		$this->load->model('tool/image');

		$language = new Language($order_info['language_directory']);
		$language->load($order_info['language_filename']);
		$language_data = $language->load('mail/order');

		$order_status = $this->getOrderStatusName($order_status_id, $order_info['language_id']);

		$store_url = $this->config->get('config_secure') ? str_replace('http://', 'https://', $order_info['store_url']) : $order_info['store_url'];

		// html email
		$template = new Template();
		$template->data = $language_data;
		$template->data['title'] = sprintf($language->get('text_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_info['order_no']);
		$template->data['text_greeting'] = sprintf($language->get('text_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
		$template->data['member_enabled'] = $this->config->get('member_status') ? true : false;
		$template->data['logo'] = $this->config->get('config_url') . 'logo/logo-140x60.png'; // logo-315x90.png // 'image/' . $this->config->get('config_logo');
		$template->data['store_name'] = $order_info['store_name'];
		$template->data['store_url'] = $order_info['store_url'];
		$template->data['customer_id'] = $order_info['customer_id'];
		$template->data['link'] = $store_url . 'account-order-info&order_no=' . $order_info['order_no'];
		$template->data['download'] = $order_download ? $store_url . 'index.php?route=account/download' : '';
		$template->data['order_id'] = $order_info['order_no'];
		$template->data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));
		$template->data['payment_method'] = $order_info['payment_method'];
		$template->data['shipping_method'] = $order_info['shipping_method'];
		$template->data['email'] = $order_info['email'];
		$template->data['telephone'] = $order_info['telephone'];
		$template->data['ip'] = $order_info['ip'];
		$template->data['instruction'] = $comment && $notify ? nl2br($comment) : $template->data['instruction'] = '';

		$format = $order_info['payment_address_format'] ?: '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';

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
			'firstname' => $order_info['payment_firstname'],
			'lastname'  => $order_info['payment_lastname'],
			'company'   => $order_info['payment_company'],
			'address_1' => $order_info['payment_address_1'],
			'address_2' => $order_info['payment_address_2'],
			'city'      => $order_info['payment_city'],
			'postcode'  => $order_info['payment_postcode'],
			'zone'      => $order_info['payment_zone'],
			'zone_code' => $order_info['payment_zone_code'],
			'country'   => $order_info['payment_country']
		);

		$template->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

		$format = $order_info['shipping_address_format'] ?: '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';

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
			'firstname' => $order_info['shipping_firstname'],
			'lastname'  => $order_info['shipping_lastname'],
			'company'   => $order_info['shipping_company'],
			'address_1' => $order_info['shipping_address_1'],
			'address_2' => $order_info['shipping_address_2'],
			'city'      => $order_info['shipping_city'],
			'postcode'  => $order_info['shipping_postcode'],
			'zone'      => $order_info['shipping_zone'],
			'zone_code' => $order_info['shipping_zone_code'],
			'country'   => $order_info['shipping_country']
		);

		$template->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

		// products
		$template->data['products'] = array();

		$order_product_option_data = array();

		foreach ($order_products as $product) {
			$order_product_options = $this->getOrderProductOptions($order_id, $product['order_product_id']);

			$option_data = array();

			foreach ($order_product_options as $option) {
				$value = $option['type'] != 'file' ? $option['value'] : utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '...' : $value)
				);
			}

			$order_product_option_data[$product['order_product_id']] = $option_data;

			$template->data['products'][] = array(
				'name'     		=> $product['name'],
				'model'    		=> $product['model'],
				'manufacturer'	=> $product['manufacturer'],
				'member'   		=> (!empty($product['member']) ? $product['member'] : ''),
				'image'    		=> $this->model_tool_image->resize($product['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), 'autocrop'),
				'option'   		=> $option_data,
				'quantity' 		=> $product['quantity'],
				'price'    		=> $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
				'total'    		=> $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
			);
		}

		// vouchers
		$template->data['vouchers'] = array();

		foreach ($order_vouchers as $voucher) {
			$template->data['vouchers'][] = array(
				'description' => $voucher['description'],
				'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
			);
		}

		$template->data['totals'] = $order_totals;
		$template->data['comment'] = $order_info['comment'] ? nl2br($order_info['comment']) : '';

		// text email
		$message  = sprintf($language->get('text_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8')) . "\n\n";
		$message .= $language->get('text_order_id') . ' ' . $order_info['order_no'] . "\n";
		$message .= $language->get('text_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n";
		$message .= $language->get('text_order_status') . ' ' . $order_status . "\n\n";

		if ($comment && $notify) {
			$message .= $language->get('text_instruction') . "\n\n";
			$message .= $comment . "\n\n";
		}

		// products
		$message .= $language->get('text_products') . "\n";

		foreach ($order_products as $product) {
			$message .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . (!empty($product['member']) ? ', ' . $product['member'] : '') . ') ';
			$message .= html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";

			foreach ($order_product_option_data[$product['order_product_id']] as $option) {
				$message .= chr(9) . '-' . $option['name'] . ' ' . $option['value'] . "\n";
			}
		}

		foreach ($order_vouchers as $voucher) {
			$message .= '1x ' . $voucher['description'] . ' ' . $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
		}

		$message .= "\n" . $language->get('text_order_total') . "\n";

		foreach ($order_totals as $total) {
			$message .= $total['title'] . ': ' . html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
		}

		$message .= "\n";

		if ($order_info['customer_id']) {
			$message .= $language->get('text_link') . "\n";
			$message .= $store_url . 'index.php?route=account/order/info&order_no=' . $order_info['order_no'] . "\n\n";
		}

		if ($order_download) {
			$message .= $language->get('text_download') . "\n";
			$message .= $store_url . 'index.php?route=account/download' . "\n\n";
		}

		if ($order_info['comment']) {
			$message .= $language->get('text_comment') . "\n\n";
			$message .= $order_info['comment'] . "\n\n";
		}

		$message .= $language->get('text_footer') . "\n\n";

		return $this->sendEmail(array(
			'to' 		=> $order_info['email'],
			'from' 		=> $this->config->get('config_email'),
			'sender' 	=> $order_info['store_name'],
			'subject' 	=> sprintf($language->get('text_subject'), $order_info['store_name'], $order_info['order_no']),
			'message' 	=> $message,
			'cc' 		=> $this->getOrderProductCustomerEmails($order_id, $order_products),
			'reply' 	=> $this->config->get('config_email'),
			'html' 		=> $template->fetch('/template/mail/order.tpl'),
			'admin'		=> true
		));
	}

	protected function emailOrderUpdate($order_id, $order_status_id, $order_info, $comment) {
		$language = new Language($order_info['language_directory']);
		$language->load($order_info['language_filename']);
		$language->load('mail/order');

		$message  = $language->get('text_update_order') . ' ' . $order_id . "\n";
		$message .= $language->get('text_update_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";

		$order_status = $this->getOrderStatusName($order_status_id, $order_info['language_id']);

		if ($order_status) {
			$message .= $language->get('text_update_order_status') . "\n\n";
			$message .= $order_status . "\n\n";
		}

		if ($order_info['customer_id']) {
			$message .= $language->get('text_update_link') . "\n";
			$message .= ($this->config->get('config_secure') ? str_replace('http://', 'https://', $order_info['store_url']) : $order_info['store_url']) . 'index.php?route=account/order/info&order_no=' . $order_info['order_no'] . "\n\n";
		}

		if ($comment) {
			$message .= $language->get('text_update_comment') . "\n\n";
			$message .= $comment . "\n\n";
		}

		$message .= $language->get('text_update_footer');

		return $this->sendEmail(array(
			'to' 		=> $order_info['email'],
			'from' 		=> $this->config->get('config_email_noreply'),
			'sender' 	=> $order_info['store_name'],
			'subject' 	=> sprintf($language->get('text_update_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id),
			'message' 	=> $message,
			'cc' 		=> $this->getOrderProductCustomerEmails($order_id),
			'reply' 	=> $this->config->get('config_email_noreply'),
			'admin'		=> true
		));
	}

	protected function getOrderProductCustomerEmails($order_id, $order_products = array()) {
		if (!$this->config->get('member_email_customers')) {
			return array();
		}

		if (!$order_products) {
			$order_products = $this->getOrderProducts($order_id);
		}

		// gets email addresses of users who are the owners of each listing on the order
		$this->load->model('account/customer');

		$emails = array();

		$order_product_customer_ids = array();

		foreach ($order_products as $order_product) {
			if (!in_array($order_product['member_customer_id'], $order_product_customer_ids)) {
				$order_product_customer_ids[] = $order_product['member_customer_id'];
			}
		}

		foreach ($order_product_customer_ids as $customer_id) {
			$customer = $this->model_account_customer->getCustomer($customer_id);

			if (!empty($customer['email'])) {
				$emails[] = $customer['email'];
			}
		}

		return $emails;
	}

	protected function checkFraud($order_info, &$order_status_id) {
		if (!$this->config->get('config_fraud_detection')) {
			return;
		}

		$this->load->model('checkout/fraud');

		$risk_score = $this->model_checkout_fraud->getFraudScore($order_info);

		if ($risk_score > $this->config->get('config_fraud_score')) {
			$order_status_id = $this->config->get('config_fraud_status_id');
		}
	}

	protected function checkBanIp($order_info, &$order_status_id) {
		$status = false;

		$this->load->model('account/customer');

		if ($order_info['customer_id']) {

			$results = $this->model_account_customer->getIps($order_info['customer_id']);

			foreach ($results as $result) {
				if ($this->model_account_customer->isBanIp($result['ip'])) {
					$status = true;

					break;
				}
			}
		} else {
			$status = $this->model_account_customer->isBanIp($order_info['ip']);
		}

		if ($status) {
			$order_status_id = $this->config->get('config_order_status_id');
		}
	}

}
?>
