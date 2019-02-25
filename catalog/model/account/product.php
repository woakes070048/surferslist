<?php
class ModelAccountProduct extends Model {
	use Contact;

	public function addAnonListing($data) {
		if (empty($data)) {
			return false;
		}

		$sql = "
			INSERT INTO " . DB_PREFIX . "product
			SET model = '" . $this->db->escape($data['model']) . "'
			, size = '" . $this->db->escape($data['size']) . "'
			, sku = '" . $this->db->escape($data['sku']) . "'
			, upc = '" . $this->db->escape($data['upc']) . "'
			, ean = '" . $this->db->escape($data['ean']) . "'
			, jan = '" . $this->db->escape($data['jan']) . "'
			, isbn = '" . $this->db->escape($data['isbn']) . "'
			, mpn = '" . $this->db->escape($data['mpn']) . "'
			, location = '" . $this->db->escape($data['location']) . "'
			, zone_id = '" . (int)$data['zone_id'] . "'
			, country_id = '" . (int)$data['country_id'] . "'
			, quantity = '" . (int)$data['quantity'] . "'
			, stock_status_id = '" . (int)$data['stock_status_id'] . "'
			, date_available = '" . $this->db->escape($data['date_available']) . "'
			, date_expiration = '" . $this->db->escape($data['date_expiration']) . "'
			, year = '" . $this->db->escape($data['year']) . "'
			, manufacturer_id = '" . (int)$data['manufacturer_id'] . "'
			, shipping = '0'
			, price = '" . (float)$data['price'] . "'
			, status = '" . (int)$data['status'] . "'
			, tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "'
			, sort_order = '" . (int)$data['sort_order'] . "'
			, member_customer_id = '" . (int)$data['member_customer_id'] . "'
			, member_approved = '" . (int)$data['approved'] . "'
			, date_added = NOW()
			, date_modified = NOW()
		";

		if (!empty($data['image'])) {
			$sql .= "
				, image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "'
			";
		}

		$this->db->query($sql);

		$product_id = $this->db->countAffected() ? $this->db->getLastId() : 0;

		if (!$product_id) {
			return false;
		}

		if (!empty($data['member_account_id'])) {
			$this->insertProductMember($product_id, $data['member_account_id'], $data['member_customer_id']);
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$this->insertProductDescription($product_id, $language_id, $value);
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->insertProductToStore($product_id, $store_id);
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->insertProductToCategory($product_id, $category_id);
			}
		}

		if (isset($data['product_filters'])) {
			foreach ($data['product_filters'] as $filter_id) {
				$this->insertProductFilter($product_id, $filter_id);
			}
		}

		if ($data['keyword']) {
			$this->insertProductKeyword($product_id, $data['keyword']);
		}

		// if ($this->customer->isLogged() && $this->customer->hasProfile()) {
		// 	$this->insertCustomerReward($product_id, 1);
		// }

		// prepare to send notification email, unless 'notify' is set and is not false
		if (!isset($data['notify']) || $data['notify'] !== false) {
			// New product notification email
			$this->load->language('mail/product');

			$mail_sent = $this->sendEmail(array(
				'to' 		=> $this->config->get('config_email'),
				'from' 		=> $this->config->get('config_email_noreply'),
				'sender' 	=> $this->config->get('config_name'),
				'subject' 	=> sprintf(
					$this->language->get('text_subject'),
					$this->config->get('config_name'),
					$data['product_description'][$this->config->get('config_language_id')]['name'],
					$this->language->get('text_anonpost')
				),
				'message' 	=> sprintf(
					$this->language->get('text_anonpost_mail'),
					$data['product_description'][$this->config->get('config_language_id')]['name'],
					$this->url->link('product/product', 'product_id=' . $product_id, 'SSL')
				),
				'reply' 	=> $this->config->get('config_email_noreply'),
				'admin'		=> true
			));
		}

		// delete listing cache
		$this->cache->delete('product');

		return $product_id;
	}

	public function getAnonListingByData($data) {
		$language_id = (int)$this->config->get('config_language_id');

		$name = isset($data['product_description'][$language_id]['name'])
			? $data['product_description'][$language_id]['name']
			: (isset($data['name']) ? $data['name'] : '');

		$sql = "
			SELECT DISTINCT p.product_id
			, pd.name
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
				AND pd.language_id = '" . (int)$language_id . "'
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_category p2c2 ON (p.product_id = p2c2.product_id)
			LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
			WHERE LCASE(p.model) = '" . utf8_strtolower(trim(preg_replace('/\s+/', ' ', $this->db->escape($data['model'])))) . "'
			AND LCASE(p.size) = '" . utf8_strtolower(trim(preg_replace('/\s+/', ' ', $this->db->escape($data['size'])))) . "'
			AND p2c.category_id = '" . (int)$data['category_id'] . "'
			AND p2c2.category_id = '" . (int)$data['sub_category_id'] . "'
			AND m.manufacturer_id = '" . (int)$data['manufacturer_id'] . "'
			AND p.status = '1'
			AND p.date_available <= NOW()
			AND p.date_expiration >= NOW()
			AND pm.member_account_id IS NULL
		";
		// AND p.member_customer_id = '0'
		// AND p.member_approved = '1'

		if (!empty($data['year'])) {
			$sql .= "
				AND LCASE(p.year) = '" . utf8_strtolower(trim(preg_replace('/\s+/', ' ', $this->db->escape($data['year'])))) . "'
			";
		}

		if ($name) {
			$sql .= "
				AND LCASE(pd.name) = '" . utf8_strtolower(trim(preg_replace('/\s+/', ' ', $this->db->escape($name)))) . "'
			";
		}

		$query = $this->db->query($sql);

		return $query->row;
	}

	public function addProduct($data, $to_member_info = array()) {
		if (empty($data)) {
			return false;
		}

		// copy or transfer
		if (empty($to_member_info)) {
			$member_customer_id = $this->customer->getId();
			$member_account_id = $this->customer->getProfileId();
		} else {
			if (empty($to_member_info['customer_id']) || empty($to_member_info['member_account_id'])) {
				return false;
			}

			$member_customer_id = $to_member_info['customer_id'];
			$member_account_id = $to_member_info['member_account_id'];
		}

		$sql = "
			INSERT INTO " . DB_PREFIX . "product
			SET model = '" . $this->db->escape($data['model']) . "'
				, size = '" . $this->db->escape($data['size']) . "'
				, sku = '" . $this->db->escape($data['sku']) . "'
				, upc = '" . $this->db->escape($data['upc']) . "'
				, ean = '" . $this->db->escape($data['ean']) . "'
				, jan = '" . $this->db->escape($data['jan']) . "'
				, isbn = '" . $this->db->escape($data['isbn']) . "'
				, mpn = '" . $this->db->escape($data['mpn']) . "'
				, location = '" . $this->db->escape($data['location']) . "'
				, zone_id = '" . (int)$data['zone_id'] . "'
				, country_id = '" . (int)$data['country_id'] . "'
				, quantity = '" . (int)$data['quantity'] . "'
				, minimum = '" . (int)$data['minimum'] . "'
				, subtract = '" . (int)$data['subtract'] . "'
				, stock_status_id = '" . (int)$data['stock_status_id'] . "'
				, date_available = '" . $this->db->escape($data['date_available']) . "'
				, date_expiration = '" . $this->db->escape($data['date_expiration']) . "'
				, year = '" . $this->db->escape($data['year']) . "'
				, manufacturer_id = '" . (int)$data['manufacturer_id'] . "'
				, shipping = '" . (int)$data['shipping'] . "'
				, price = '" . (float)$data['price'] . "'
				, points = '" . (int)$data['points'] . "'
				, weight = '" . (float)$data['weight'] . "'
				, weight_class_id = '" . (int)$data['weight_class_id'] . "'
				, length = '" . (float)$data['length'] . "'
				, width = '" . (float)$data['width'] . "'
				, height = '" . (float)$data['height'] . "'
				, length_class_id = '" . (int)$data['length_class_id'] . "'
				, status = '" . (int)$data['status'] . "'
				, tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "'
				, sort_order = '" . (int)$data['sort_order'] . "'
				, member_customer_id = '" . (int)$member_customer_id . "'
				, member_approved = '" . ($this->config->get('member_auto_approve_products') ? '1' : '0') . "'
				, date_added = NOW()
				, date_modified = NOW()
		";

		if (isset($data['image'])) {
			$sql .= "
				, image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "'
			";
		}

		$this->db->query($sql);

		$product_id = $this->db->countAffected() ? $this->db->getLastId() : 0;

		if (!$product_id) {
			return false;
		}

		$this->insertProductMember($product_id, $member_account_id, $member_customer_id);

		if (isset($data['product_shipping'])) {
			$this->editProductShipping($product_id, $data['product_shipping']);
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$this->insertProductDescription($product_id, $language_id, $value);
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->insertProductToStore($product_id, $store_id);
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				$this->editProductAttribute($product_id, $product_attribute);
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				$this->insertProductOption($product_id, $product_option);
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->insertProductDiscount($product_id, $product_discount);
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->insertProductSpecial($product_id, $product_special);
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->insertProductImage($product_id, $product_image);
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $product_download) {
				$this->editProductDownload($product_id, $product_download);
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->insertProductToCategory($product_id, $category_id);
			}
		}

		if (isset($data['product_filters'])) {
			foreach ($data['product_filters'] as $filter_id) {
				$this->insertProductFilter($product_id, $filter_id);
			}
		}

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->deleteProductRelated($product_id, $related_id);
				$this->insertProductRelated($product_id, $related_id);
				$this->deleteProductRelated($related_id, $product_id);
				$this->insertProductRelated($related_id, $product_id);
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				$this->insertProductReward($product_id, $customer_group_id, $product_reward['points']);
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout) {
				$this->insertProductToLayout($product_id, $store_id, $layout['layout_id']);
			}
		}

		if ($data['keyword']) {
			$keyword_prefix = 'listing';

			if (strpos($data['keyword'], $keyword_prefix) !== 0) {
				$data['keyword'] = $keyword_prefix . '-' . $data['keyword'] . '-' . mt_rand();
			}

			$this->insertProductKeyword($product_id, $data['keyword']);
		}

		// prepare to send notification email, unless 'notify' is set and is not false
		if (!isset($data['notify']) || $data['notify'] !== false) {
			$this->load->language('mail/product');

			$mail_sent = $this->sendEmail(array(
				'to' 		=> $this->config->get('config_email'),
				'from' 		=> $this->config->get('config_email_noreply'),
				'sender' 	=> $this->config->get('config_name'),
				'subject' 	=> sprintf(
					$this->language->get('text_subject'),
					$this->config->get('config_name'),
					$data['product_description'][$this->config->get('config_language_id')]['name'],
					$this->customer->getProfileName()
				),
				'message' 	=> sprintf(
					$this->language->get('text_new_listing_mail'),
					$data['product_description'][$this->config->get('config_language_id')]['name'],
					$this->url->link('product/product', 'product_id=' . $product_id, 'SSL'),
					$this->customer->getProfileName(),
					$this->customer->getProfileUrl(),
					(!$this->config->get('member_auto_approve_products') ? $this->language->get('text_approval') : $this->language->get('text_auto_approval'))
				),
				'bcc' 		=> $this->customer->getEmailNotifySetting('email_post') ? array($this->customer->getEmail()) : array(),
				'reply' 	=> $this->config->get('config_email_noreply'),
				'admin'		=> true
			));
		}

		$this->cache->delete('product');

		return $product_id;
	}

	public function editProduct($product_id, $data) {
		$sql = "
			UPDATE " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			SET p.model = '" . $this->db->escape($data['model']) . "'
			, p.size = '" . $this->db->escape($data['size']) . "'
			, p.sku = '" . $this->db->escape($data['sku']) . "'
			, p.upc = '" . $this->db->escape($data['upc']) . "'
			, p.ean = '" . $this->db->escape($data['ean']) . "'
			, p.jan = '" . $this->db->escape($data['jan']) . "'
			, p.isbn = '" . $this->db->escape($data['isbn']) . "'
			, p.mpn = '" . $this->db->escape($data['mpn']) . "'
			, p.location = '" . $this->db->escape($data['location']) . "'
			, p.zone_id = '" . (int)$data['zone_id'] . "'
			, p.country_id = '" . (int)$data['country_id'] . "'
			, p.quantity = '" . (int)$data['quantity'] . "'
			, p.minimum = '" . (int)$data['minimum'] . "'
			, p.subtract = '" . (int)$data['subtract'] . "'
			, p.stock_status_id = '" . (int)$data['stock_status_id'] . "'
			, p.date_available = '" . $this->db->escape($data['date_available']) . "'
			, p.date_expiration = '" . $this->db->escape($data['date_expiration']) . "'
			, p.year = '" . $this->db->escape($data['year']) . "'
			, p.manufacturer_id = '" . (int)$data['manufacturer_id'] . "'
			, p.shipping = '" . (int)$data['shipping'] . "'
			, p.price = '" . (float)$data['price'] . "'
			, p.points = '" . (int)$data['points'] . "'
			, p.weight = '" . (float)$data['weight'] . "'
			, p.weight_class_id = '" . (int)$data['weight_class_id'] . "'
			, p.length = '" . (float)$data['length'] . "'
			, p.width = '" . (float)$data['width'] . "'
			, p.height = '" . (float)$data['height'] . "'
			, p.length_class_id = '" . (int)$data['length_class_id'] . "'
			, p.status = '" . (int)$data['status'] . "'
			, p.tax_class_id = '" . $this->db->escape($data['tax_class_id']) . "'
			, p.sort_order = '" . (int)$data['sort_order'] . "'
			, p.date_modified = NOW()
		";

		if (isset($data['image'])) {
			$sql .= "
				, p.image = '" . $this->db->escape(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')) . "'
			";
		}

		$sql .= "
			WHERE p.product_id = '" . (int)$product_id . "'
			AND pm.customer_id = '" . (int)$this->customer->getId() . "'
		";

		$this->db->query($sql);

		if ($this->db->countAffected() <= 0) {
			return false;
		}

		if (isset($data['product_shipping'])) {
			$this->editProductShipping($product_id, $data['product_shipping']);
		}

		$this->deleteProductDescription($product_id);

		foreach ($data['product_description'] as $language_id => $value) {
			$this->insertProductDescription($product_id, $language_id, $value);
		}

		$this->deleteProductToStore($product_id);

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->insertProductToStore($product_id, $store_id);
			}
		}

		$this->deleteProductAttribute($product_id);

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				$this->editProductAttribute($product_id, $product_attribute);
			}
		}

		$this->deleteProductOption($product_id);

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				$this->insertProductOption($product_id, $product_option);
			}
		}

		$this->deleteProductDiscount($product_id);

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->insertProductDiscount($product_id, $product_discount);
			}
		}

		$this->deleteProductSpecial($product_id);

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->insertProductSpecial($product_id, $product_special);
			}
		}

		$this->deleteProductImage($product_id);

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->insertProductImage($product_id, $product_image);
			}
		}

		$this->deleteProductToDownload($product_id);

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $product_download) {
				$this->editProductDownload($product_id, $product_download);
			}
		}

		$this->deleteProductToCategory($product_id);

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->insertProductToCategory($product_id, $category_id);
			}
		}

		$this->deleteProductFilter($product_id);

		if (isset($data['product_filters'])) {
			foreach ($data['product_filters'] as $filter_id) {
				$this->insertProductFilter($product_id, $filter_id);
			}
		}

		$this->deleteProductRelated($product_id, null);
		$this->deleteProductRelated(null, $product_id);

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->deleteProductRelated($product_id, $related_id);
				$this->insertProductRelated($product_id, $related_id);
				$this->deleteProductRelated($related_id, $product_id);
				$this->insertProductRelated($related_id, $product_id);
			}
		}

		$this->deleteProductReward($product_id);

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				$this->insertProductReward($product_id, $customer_group_id, $value['points']);
			}
		}

		$this->deleteProductToLayout($product_id);

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout) {
				$this->insertProductToLayout($product_id, $store_id, $layout['layout_id']);
			}
		}

		if (!empty($data['keyword'])) {
			$this->deleteProductKeyword($product_id);
			$this->insertProductKeyword($product_id, $data['keyword']);
		}

		$this->cache->delete('product_' . (int)$product_id);
		$this->cache->delete('product');

		return true;
	}

	public function copyProductToCustomer($product_id, $from_customer_id, $to_customer_id) {
		if (empty($product_id) || empty($from_customer_id) || empty($to_customer_id)) {
			return false;
		}

		$this->load->model('account/member');
		$to_member_info = $this->model_account_member->getMemberByCustomerId($to_customer_id);

		if (!$to_member_info) {
			return false;
		}

		$data = $this->getProductByCustomerId($product_id, $from_customer_id);

		if (!$data) {
			return false;
		}

		$from_member_info = array(
			'member_account_id' => $data['member_account_id'],
			'member_account_name' => $data['member_account_name']
		);

		$this->copyProductData($data);
		$this->copyProductMember($data, $to_member_info, $from_member_info);
		$this->copyProductImages($data, $to_member_info['member_directory_images']);

		return $this->addProduct($data, $to_member_info);
	}

	public function copyProduct($product_id) {
		if (empty($product_id)) {
			return false;
		}

		$data = $this->getProductByCustomerId($product_id, $this->customer->getId());

		if (!$data) {
			return false;
		}

		$this->copyProductData($data);

		$this->load->language('mail/product');

		foreach ($data['product_description'] as $language_id => $value) {
			$data['product_description'][$language_id]['meta_description'] = '';  // clear meta description; re-created upon next user save
			$data['product_description'][$language_id]['name'] .= $this->language->get('text_copied');
			$data['product_description'][$language_id]['description'] .= "\n\n" . $this->language->get('text_copied');
		}

		$this->copyProductImages($data, $this->customer->getMemberImagesDirectory());

		$data['date_expiration'] = $this->customer->getMemberPermission('auto_renew_enabled')
			? date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 365 * 10))
			: date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 90)); // 10 years : 90 days

		return $this->addProduct($data);
	}

	private function copyProductData(&$data) {
		if (empty($data['product_id']) || empty($data['name'])) {
			return;
		}

		$data = array_merge($data, array('product_attribute' => $this->getProductAttributes($data['product_id'])));
		$data = array_merge($data, array('product_description' => $this->getProductDescriptions($data['product_id'])));
		$data = array_merge($data, array('product_discount' => $this->getProductDiscounts($data['product_id'])));
		$data = array_merge($data, array('product_image' => $this->getProductImages($data['product_id'])));
		$data = array_merge($data, array('product_option' => $this->getProductOptions($data['product_id'])));
		// $data = array_merge($data, array('product_related' => $this->getMemberProductRelated())); // $this->getProductRelated($data['product_id'])));
		$data = array_merge($data, array('product_reward' => $this->getProductRewards($data['product_id'])));
		$data = array_merge($data, array('product_special' => $this->getProductSpecials($data['product_id'])));
		$data = array_merge($data, array('product_category' => $this->getProductCategories($data['product_id'])));
		$data = array_merge($data, array('product_filters' => $this->getProductFilters($data['product_id'])));
		$data = array_merge($data, array('product_download' => $this->getProductDownloads($data['product_id'])));
		$data = array_merge($data, array('product_layout' => $this->getProductLayouts($data['product_id'])));
		$data = array_merge($data, array('product_store' => $this->getProductStores($data['product_id'])));
		$data = array_merge($data, array('product_shipping' => $this->getProductShipping($data['product_id'])));

		$data['keyword'] = (!empty($data['year']) && $data['year'] != '0000' && strpos($data['name'], $data['year']) === false)
		  ? friendly_url(clean_path(html_entity_decode($data['name'] . '-' . $data['year'], ENT_QUOTES, 'UTF-8')))
		  : friendly_url(clean_path(html_entity_decode($data['name'], ENT_QUOTES, 'UTF-8')));

		$data['viewed'] = '0'; // reset views
		$data['status'] = '0';  // disable (private only)
		$data['quantity'] = '-1'; // set to Not For Sale type
		$data['shipping'] = '0'; // deactivate shipping (but save rates, if they exist)
		$data['date_available'] = date('Y-m-d H:i:s', time() - (60 * 60 * 24)); // make immediately available, -(60 * 60 * 24) sec == -1 day
		$data['copied'] = true; // important for copying images between directories!
		$data['notify'] = false; // do NOT trigger email notification(s)!
	}

	private function copyProductMember(&$data, $to_member_info, $from_member_info) {
		if (empty($to_member_info) || empty($from_member_info)) {
			return;
		}

		$this->load->language('mail/product');

		$data['location'] = $to_member_info['member_city'];
		$data['zone_id'] = $to_member_info['member_zone_id'];
		$data['country_id'] = $to_member_info['member_country_id'];
		$data['date_expiration'] = !empty($to_member_info['auto_renew_enabled'])
			? date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 365 * 10))
			: date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 90)); // 10 years : 90 days

		foreach ($data['product_description'] as $language_id => $value) {
			$data['product_description'][$language_id]['meta_description'] = '';  // clear meta description; re-created upon next user save
			$data['product_description'][$language_id]['name'] .= sprintf($this->language->get('text_received_from'), $from_member_info['member_account_name']);
			$data['product_description'][$language_id]['description'] .= "\n\n" . sprintf($this->language->get('text_received_from'), $this->url->link('product/member/info', 'member_id=' . $from_member_info['member_account_id'], 'SSL'));
		}

		if (!isset($data['notify'])) {
			$data['notify'] = $to_member_info['email_post']; // member email notification setting
		}
	}

	private function copyProductImages(&$data, $to_directory) {
		if (empty($data['image']) || empty($data['keyword']) || $to_directory = '') {
			return;
		}

		$this->load->model('tool/image');

		$image = $this->model_tool_image->move($data['image'], 'data/' . $to_directory, $data['keyword'] . '-' . mt_rand(), true);

		if ($image) {
			$data['image'] = $image;
		}

		if ($this->config->get('member_tab_image') && !empty($data['product_image'])) {
			foreach ($data['product_image'] as $key => $product_image) {
				$image = $this->model_tool_image->move($product_image['image'], 'data/' . $to_directory, $data['keyword'] . '-' . ($key + 2) . '-' . mt_rand(), true);

				if ($image) {
					$data['product_image'][$key]['image'] = $image;
				}
			}
		}
	}

	public function transferProduct($data, $to_member_info) {
		if (empty($data) || empty($to_member_info)) {
			return false;
		}

		// $this->load->model('account/member');
		// $from_member_info = $this->model_account_member->getMemberByCustomerId($from_customer_id);
		$from_member_info = array(
			'member_account_id' => $this->customer->getProfileId(),
			'member_account_name' => $this->customer->getProfileName()
		);

		$this->copyProductData($data);
		$this->copyProductMember($data, $to_member_info, $from_member_info);
		$this->copyProductImages($data, $to_member_info['member_directory_images']);

		if ($this->addProduct($data, $to_member_info)) {
			return $this->retireProduct($data['product_id']);
		}
	}

	public function retireProduct($product_id) {
		if (!$product_id) {
			return false;
		}

		$sql = sprintf('
			SELECT	`p`.*
			, `pm`.*
			, (SELECT `keyword`
				FROM `%1$surl_alias`
				WHERE `query` = "product_id=%2$s") AS keyword
			FROM `%1$sproduct` `p`
			LEFT JOIN `%1$sproduct_member` `pm` ON (`p`.`product_id` = `pm`.`product_id`)
			WHERE `p`.`product_id` = %2$s
			AND `pm`.`customer_id` = %3$s
			LIMIT 1
			',
			DB_PREFIX,
			(int)$product_id,
			(int)$this->customer->getId()
		);

		$query = $this->db->query($sql);

		$data = $query->num_rows ? $query->row : array();

		if (!$data) {
			return false;
		}

		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_retired
			SET product_id = '" . (int)$product_id . "'
			, keyword = '" . $this->db->escape($data['keyword']) . "'
			, image = '" . $this->db->escape($data['image']) . "'
			, model = '" . $this->db->escape($data['model']) . "'
			, size = '" . $this->db->escape($data['size']) . "'
			, sku = '" . $this->db->escape($data['sku']) . "'
			, upc = '" . $this->db->escape($data['upc']) . "'
			, ean = '" . $this->db->escape($data['ean']) . "'
			, jan = '" . $this->db->escape($data['jan']) . "'
			, isbn = '" . $this->db->escape($data['isbn']) . "'
			, mpn = '" . $this->db->escape($data['mpn']) . "'
			, location = '" . $this->db->escape($data['location']) . "'
			, zone_id = '" . (int)$data['zone_id'] . "'
			, country_id = '" . (int)$data['country_id'] . "'
			, quantity = '" . (int)$data['quantity'] . "'
			, minimum = '" . (int)$data['minimum'] . "'
			, subtract = '" . (int)$data['subtract'] . "'
			, stock_status_id = '" . (int)$data['stock_status_id'] . "'
			, date_available = '" . $this->db->escape($data['date_available']) . "'
			, date_expiration = '" . $this->db->escape($data['date_expiration']) . "'
			, year = '" . $this->db->escape($data['year']) . "'
			, manufacturer_id = '" . (int)$data['manufacturer_id'] . "'
			, shipping = '" . (int)$data['shipping'] . "'
			, price = '" . (float)$data['price'] . "'
			, points = '" . (int)$data['points'] . "'
			, weight = '" . (float)$data['weight'] . "'
			, weight_class_id = '" . (int)$data['weight_class_id'] . "'
			, length = '" . (float)$data['length'] . "'
			, width = '" . (float)$data['width'] . "'
			, height = '" . (float)$data['height'] . "'
			, length_class_id = '" . (int)$data['length_class_id'] . "'
			, status = '" . (int)$data['status'] . "'
			, tax_class_id = '" . (int)$data['tax_class_id'] . "'
			, sort_order = '" . (int)$data['sort_order'] . "'
			, viewed = '" . (int)$data['viewed'] . "'
			, member_customer_id = '" . (int)$data['member_customer_id'] . "'
			, member_approved = '" . (int)$data['member_approved'] . "'
			, date_added = '" . $this->db->escape($data['date_added']) . "'
			, date_modified = '" . $this->db->escape($data['date_modified']) . "'
			, date_retired = NOW()
		");

		if ($this->db->countAffected() <= 0) {
			return false;
		}

		$this->deleteProductKeyword($product_id);

		// disable delete and use expire until retired listing system is completed
		// $this->deleteProduct($product_id);
		$this->expireProduct($product_id);

		$this->cache->delete('product_' . (int)$product_id);
		$this->cache->delete('product');

		return true;
	}

	public function enableProduct($product_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			SET p.status = '1'
			WHERE p.product_id = '" . (int)$product_id . "'
			AND pm.customer_id = '" . (int)$this->customer->getId() . "'
		");

		if ($this->db->countAffected() <= 0) {
			return false;
		}

		$this->cache->delete('product_' . (int)$product_id);
		$this->cache->delete('product');

		return true;
	}

	public function disableProduct($product_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			SET p.status = '0'
			WHERE p.product_id = '" . (int)$product_id . "'
			AND pm.customer_id = '" . (int)$this->customer->getId() . "'
		");

		if ($this->db->countAffected() <= 0) {
			return false;
		}

		$this->cache->delete('product_' . (int)$product_id);
		$this->cache->delete('product');

		return true;
	}

	public function renewProduct($product_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			SET p.status = '1'
			, p.date_expiration = '" . date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 90)) . "'
			WHERE p.product_id = '" . (int)$product_id . "'
			AND pm.customer_id = '" . (int)$this->customer->getId() . "'
		");

		if ($this->db->countAffected() <= 0) {
			return false;
		}

		$this->cache->delete('product_' . (int)$product_id);
		$this->cache->delete('product');

		return true;
	}

	private function expireProduct($product_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			SET p.status = '0'
			, p.date_expiration = '" . date('Y-m-d H:i:s', time() - (60 * 60 * 24)) . "'
			WHERE p.product_id = '" . (int)$product_id . "'
			AND pm.customer_id = '" . (int)$this->customer->getId() . "'
		");

		if ($this->db->countAffected() <= 0) {
			return false;
		}

		$this->cache->delete('product_' . (int)$product_id);
		$this->cache->delete('product');

		return true;
	}

	private function deleteProduct($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product
			WHERE product_id = '" . (int)$product_id . "'
			AND member_customer_id = '" . (int)$this->customer->getId() . "'
		");

		if ($this->db->countAffected() <= 0) {
			return false;
		}

		$this->deleteProductMember($product_id);
		$this->deleteProductDescription($product_id);
		$this->deleteProductDiscount($product_id);
		$this->deleteProductOption($product_id);
		$this->deleteProductAttribute($product_id);
		$this->deleteProductImage($product_id);
		$this->deleteProductSpecial($product_id);
		$this->deleteProductRelated($product_id, null);
		$this->deleteProductRelated(null, $product_id);
		$this->deleteProductReward($product_id);
		$this->deleteProductToCategory($product_id);
		$this->deleteProductFilter($product_id);
		$this->deleteProductToDownload($product_id);
		$this->deleteProductToLayout($product_id);
		$this->deleteProductToStore($product_id);
		$this->deleteProductShipping($product_id);
		$this->deleteProductKeyword($product_id);

		$this->cache->delete('product_' . (int)$product_id);
		$this->cache->delete('product');

		return true;
	}

	private function getProductByCustomerId($product_id, $customer_id) {
		$query = $this->db->query("
			SELECT DISTINCT p.*
			, pd.*
			, cma.member_account_id
			, cma.member_account_name
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
				AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			LEFT JOIN " . DB_PREFIX . "customer_member_account cma ON (pm.member_account_id = cma.member_account_id)
			WHERE p.product_id = '" . (int)$product_id . "'
			AND p.member_customer_id = '" . (int)$customer_id . "'
		");

		return $query->row;
	}

	public function getProduct($product_id) {
		$query = $this->db->query("
			SELECT DISTINCT p.*
			, pd.*
			, (SELECT keyword
				FROM " . DB_PREFIX . "url_alias
				WHERE query = 'product_id=" . (int)$product_id . "') AS keyword
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			WHERE p.product_id = '" . (int)$product_id . "'
			AND p.date_available <= NOW()
			AND p.date_expiration >= NOW()
			AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			AND pm.customer_id = '" . (int)$this->customer->getId() . "'
		");

		return $query->row;
	}

	public function getProducts($data = array()) {
		if (!$data) {
			$product_data = $this->cache->get('product.member.' . (int)$this->customer->getId() . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

			if ($product_data === false) {
				$query = $this->db->query("
					SELECT p.*
					, pd.*
					FROM " . DB_PREFIX . "product p
					LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
					LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
					WHERE pm.customer_id = '" . (int)$this->customer->getId() . "'
					AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
					ORDER BY pd.name ASC
				");

				$product_data = $query->rows;

				$this->cache->set('product.member.' . (int)$this->customer->getId() . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $product_data);
			}

			return $product_data;
		} else {
			$sql = "
				SELECT p.*
				, pd.*
				FROM " . DB_PREFIX . "product p
				LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
			";

			if (!empty($data['filter_category_id'])) {
				$sql .= "
					LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
				";
			}

			$sql .= "
				WHERE pm.customer_id = '" . (int)$this->customer->getId() . "'
				AND p.date_available <= NOW()
				AND p.date_expiration >= NOW()
				AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			";

			if (!empty($data['filter_name'])) {
				$sql .= " AND LCASE(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
			}

			if (!empty($data['filter_model'])) {
				$sql .= " AND LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_model'])) . "%'";
			}

			if (!empty($data['filter_price'])) {
				$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
			}

			if (isset($data['filter_type'])) {
				if ($data['filter_type'] == '1') {
					$sql .= " AND p.quantity > '0'";
				} else if ($data['filter_type'] == '0') {
					$sql .= " AND p.quantity = '0'";
				} else if ($data['filter_type'] == '-1') {
					$sql .= " AND p.quantity < '0'";
				}
			}

			if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
				$sql .= " AND p.quantity = '" . $this->db->escape($data['filter_quantity']) . "'";
			}

			if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
				$sql .= " AND p.member_approved = '" . (int)$data['filter_approved'] . "'";
			}

			if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}

			if (!empty($data['filter_category_id'])) {
				if (!empty($data['filter_sub_category'])) {
					$implode_data = array();

					$implode_data[] = "category_id = '" . (int)$data['filter_category_id'] . "'";

					$this->load->model('catalog/category');

					$categories = $this->model_catalog_category->getCategories($data['filter_category_id']);

					foreach ($categories as $category) {
						$implode_data[] = "p2c.category_id = '" . (int)$category['category_id'] . "'";
					}

					$sql .= " AND (" . implode(' OR ', $implode_data) . ")";
				} else {
					$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
				}
			}

			$sql .= " GROUP BY p.product_id";

			$sort_data = array(
				'name' => 'pd.name',
				'model' => 'p.model',
				'price' => 'p.price',
				'type' => 'type',
				'quantity' => 'p.quantity',
				'approved' => 'p.member_approved',
				'created' => 'p.date_added',
				'expires' => 'p.date_expiration',
				'status' => 'p.status',
				'default' => 'p.sort_order'
			);

			if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $sort_data[$data['sort']];
			} else {
				$sql .= " ORDER BY pd.name";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 10;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			return $query->rows;
		}
	}

	public function getTotalProducts($data = array()) {
		$sql = "
			SELECT COUNT(DISTINCT p.product_id) AS total
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
		";

		if (!empty($data['filter_category_id'])) {
			$sql .= "
				LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
			";
		}

		$sql .= "
			WHERE pm.customer_id = '" . (int)$this->customer->getId() . "'
			AND p.date_available <= NOW()
			AND p.date_expiration >= NOW()
			AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND LCASE(p.model) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_model'])) . "%'";
		}

		if (!empty($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_type'])) {
			if ($data['filter_type'] == '1') {
				$sql .= " AND p.quantity > '0'";
			} else if ($data['filter_type'] == '0') {
				$sql .= " AND p.quantity = '0'";
			} else if ($data['filter_type'] == '-1') {
				$sql .= " AND p.quantity < '0'";
			}
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . $this->db->escape($data['filter_quantity']) . "'";
		}

		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$sql .= " AND p.member_approved = '" . (int)$data['filter_approved'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$implode_data = array();

				$implode_data[] = "p2c.category_id = '" . (int)$data['filter_category_id'] . "'";

				$this->load->model('catalog/category');

				$categories = $this->model_catalog_category->getCategories($data['filter_category_id']);

				foreach ($categories as $category) {
					$implode_data[] = "p2c.category_id = '" . (int)$category['category_id'] . "'";
				}

				$sql .= " AND (" . implode(' OR ', $implode_data) . ")";
			} else {
				$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
			}
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "product_description
			WHERE product_id = '" . (int)$product_id . "'
		");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_keyword'     => $result['meta_keyword'],
				'meta_description' => $result['meta_description'],
				'tag'              => $result['tag']
			);
		}

		return $product_description_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("
			SELECT pa.attribute_id
			, ad.name
			FROM " . DB_PREFIX . "product_attribute pa
			LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id)
			LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id)
			WHERE pa.product_id = '" . (int)$product_id . "'
			AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'
			GROUP BY pa.attribute_id
		");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "product_attribute
				WHERE product_id = '" . (int)$product_id . "'
				AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'
			");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'name'                          => $product_attribute['name'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("
			SELECT po.*
			FROM " . DB_PREFIX . "product_option po
			LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id)
			LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id)
			WHERE po.product_id = '" . (int)$product_id . "'
			AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'
			ORDER BY o.sort_order
		");

		foreach ($product_option_query->rows as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				$product_option_value_data = array();

				$product_option_value_query = $this->db->query("
					SELECT *
					FROM " . DB_PREFIX . "product_option_value pov
					LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id)
					LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id)
					WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "'
					AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'
					ORDER BY ov.sort_order
				");

				foreach ($product_option_value_query->rows as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'name'                    => $product_option_value['name'],
						'image'                   => $product_option_value['image'],
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

				$product_option_data[] = array(
					'product_option_id'    => $product_option['product_option_id'],
					'option_id'            => $product_option['option_id'],
					'name'                 => $product_option['name'],
					'type'                 => $product_option['type'],
					'product_option_value' => $product_option_value_data,
					'required'             => $product_option['required']
				);
			} else {
				$product_option_data[] = array(
					'product_option_id' => $product_option['product_option_id'],
					'option_id'         => $product_option['option_id'],
					'name'              => $product_option['name'],
					'type'              => $product_option['type'],
					'option_value'      => $product_option['option_value'],
					'required'          => $product_option['required']
				);
			}
		}

		return $product_option_data;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("
			SELECT pi.*
			FROM " . DB_PREFIX . "product_image pi
			WHERE pi.product_id = '" . (int)$product_id . "'
		");

		return $query->rows;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("
			SELECT pd.*
			FROM " . DB_PREFIX . "product_discount pd
			WHERE pd.product_id = '" . (int)$product_id . "'
			ORDER BY pd.quantity
			, pd.priority
			, pd.price
		");

		return $query->rows;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("
			SELECT ps.*
			FROM " . DB_PREFIX . "product_special ps
			WHERE ps.product_id = '" . (int)$product_id . "'
			ORDER BY ps.priority
			, ps.price
		");

		return $query->rows;
	}

	public function getProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db->query("
			SELECT pr.*
			FROM " . DB_PREFIX . "product_reward pr
			WHERE pr.product_id = '" . (int)$product_id . "'
		");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}

	public function getProductDigitalDownloads($product_id) {
		$sql = "
			SELECT p2d.*
			, d.*
			, dd.*
			FROM " . DB_PREFIX . "product_to_download p2d
			INNER JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id)
			INNER JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id)
			WHERE p2d.product_id = '" . (int)$product_id . "'
			AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			AND d.member_customer_id = '" . (int)$this->customer->getId() . "'
			ORDER BY dd.name ASC
		";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("
			SELECT p2d.*
			FROM " . DB_PREFIX . "product_to_download p2d
			WHERE p2d.product_id = '" . (int)$product_id . "'
		");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("
			SELECT p2s.*
			FROM " . DB_PREFIX . "product_to_store p2s
			WHERE p2s.product_id = '" . (int)$product_id . "'
		");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("
			SELECT p2l.*
			FROM " . DB_PREFIX . "product_to_layout p2l
			WHERE p2l.product_id = '" . (int)$product_id . "'
		");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$sql = "
			SELECT p2c.category_id
			FROM " . DB_PREFIX . "product_to_category p2c
			WHERE p2c.product_id = '" . (int)$product_id . "'
		";

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductCategoryTop($product_id) {
		$sql = "
			SELECT DISTINCT p2c.category_id
			FROM " . DB_PREFIX . "product_to_category p2c
			INNER JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id)
			WHERE p2c.product_id = '" . (int)$product_id . "'
			AND c.top = '1'
		";

		$query = $this->db->query($sql);

		return $query->num_rows ? $query->row['category_id'] : 0;
	}

	public function getProductCategorySub($product_id, $parent_category_id) {
		$sql = "
			SELECT DISTINCT p2c.category_id
			FROM " . DB_PREFIX . "product_to_category p2c
			INNER JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id)
			WHERE p2c.product_id = '" . (int)$product_id . "'
			AND c.parent_id = '" . (int)$parent_category_id . "'
		";

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			return $query->num_rows ? $query->row['category_id'] : 0;
		} else {
			return false;
		}
	}

	public function getProductFilters($product_id, $filter_group_id = 0) {
		$product_filter_data = array();

		$sql = "
			SELECT pf.*
			, f.*
			FROM " . DB_PREFIX . "product_filter pf
			INNER JOIN " . DB_PREFIX . "filter f ON (pf.filter_id = f.filter_id)
			WHERE pf.product_id = '" . (int)$product_id . "'
		";

		if ($filter_group_id) {
			$sql .= "
				AND f.filter_group_id = '" . (int)$filter_group_id . "'
				ORDER BY f.filter_group_id
				, f.sort_order ASC
			";
		}

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("
			SELECT pr.*
			FROM " . DB_PREFIX . "product_related pr
			WHERE pr.product_id = '" . (int)$product_id . "'
		");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getMemberProductRelated($customer_id = 0) {
		$member_customer_id = $this->customer->getId() ?: $customer_id;

		$product_related_data = array();

		$query = $this->db->query("
			SELECT p.*
			, p2s.*
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			WHERE pm.customer_id = '" . (int)$member_customer_id . "'
			AND p.status = '1'
			AND p.date_available <= NOW()
			AND p.date_expiration >= NOW()
			AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
		");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['product_id'];
		}

		return $product_related_data;
	}

	public function getAllMemberCategories($data = array()) {
		$cache = md5(http_build_query($data));
		$all_member_category_data = $this->cache->get('category.member.all.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($all_member_category_data === false) {
			$all_member_category_data = array();

			$sql = "
				SELECT cp.category_id AS category_id
				, cd2.name AS name
				, c1.status
				, c1.image
				, c1.parent_id
				, c1.sort_order
				, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '" . $this->language->get('text_separator') . "') AS path_name
				, GROUP_CONCAT(c2.sort_order ORDER BY cp.level SEPARATOR '" . $this->language->get('text_separator') . "') AS sort_order_path_display
				, GROUP_CONCAT(LPAD(c2.sort_order,10,'0') ORDER BY cp.level) AS sort_order_path
				FROM " . DB_PREFIX . "category_path cp
				LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id)
				LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id)
				LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id)
				LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id)
				WHERE cd1.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'
			";

			if (!empty($data['filter_name'])) {
				$sql .= "
					AND cd2.name LIKE '" . $this->db->escape($data['filter_name']) . "%'
				";
			}

			if (!empty($data['filter_status'])) {
				$sql .= "
					AND c1.status = '" . (int)$data['filter_status'] . "'
				";
			}

			// if (!empty($data['filter_member_account_id'])) {
			// 	$sql .= "
			// 		AND member.member_account_id = '" . (int)$data['filter_member_account_id'] . "'
			// 	";
			// }

			$sql .= "
				GROUP BY cp.category_id
			";

			$sort_data = array(
				'name',
				'path_name',
				'sort_order',
				'sort_order_path'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY sort_order_path";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}
				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			foreach ($query->rows as $result) {
				$all_member_category_data[] = array(
					'category_id' 	=> $result['category_id'],
					'name'        	=> $result['name'],
					'path_name'		=> $result['path_name'],
					'image'  	  	=> $result['image'],
					'sort_order'  	=> $result['sort_order'],
					'sort_order_path'  => $result['sort_order_path'],
					'sort_order_path_display'  => $result['sort_order_path_display'],
					'status'  	  	=> $result['status']
				);
			}

			$this->cache->set('category.member.all.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $all_member_category_data);
		}

		return $all_member_category_data;
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product
			WHERE tax_class_id = '" . (int)$tax_class_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product
			WHERE stock_status_id = '" . (int)$stock_status_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product
			WHERE weight_class_id = '" . (int)$weight_class_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product
			WHERE length_class_id = '" . (int)$length_class_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product_to_download
			WHERE download_id = '" . (int)$download_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product
			WHERE manufacturer_id = '" . (int)$manufacturer_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product_attribute
			WHERE attribute_id = '" . (int)$attribute_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product_option
			WHERE option_id = '" . (int)$option_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product_to_layout
			WHERE layout_id = '" . (int)$layout_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByName($name, $language_id) {
		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product_description
			WHERE LOWER(`name`) = '" . $this->db->escape(utf8_strtolower($name)) . "'
			AND language_id = '" . (int)$language_id . "'
		");

		return $query->row['total'];
	}

	public function getCustomerIdByProduct($product_id) {
		$query = $this->db->query("
			SELECT customer_id
			FROM " . DB_PREFIX . "product_member
			WHERE product_id = '" . (int)$product_id . "'
		");

		return $query->num_rows ? $query->row['customer_id'] : 0;
	}

	public function getMemberIdByProduct($product_id) {
		$query = $this->db->query("
			SELECT member_account_id
			FROM " . DB_PREFIX . "product_member
			WHERE product_id = '" . (int)$product_id . "'
		");

		return $query->num_rows ? $query->row['member_account_id'] : 0;
	}

	public function getFilters($filter_group_id = 0) {
		$sql = "
			SELECT *
			FROM " . DB_PREFIX . "filter f
			LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id)
			WHERE fd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		if ($filter_group_id) {
			$sql .= "
				AND f.filter_group_id = '" . (int)$filter_group_id . "'
			";
		}

		$sql .= "
			ORDER BY f.filter_group_id
			, f.sort_order ASC
		";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getOptions($data = array()) {
		$sql = "
			SELECT *
			FROM `" . DB_PREFIX . "option` o
			LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id)
			WHERE od.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$sql .= " AND LCASE(od.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}

		$sort_data = array(
			'od.name',
			'o.type',
			'o.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY od.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getOptionValues($option_id) {
		$option_value_data = array();

		$option_value_query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "option_value ov
			LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id)
			WHERE ov.option_id = '" . (int)$option_id . "'
			AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			ORDER BY ov.sort_order ASC
		");

		foreach ($option_value_query->rows as $option_value) {
			$option_value_data[] = array(
				'option_value_id' => $option_value['option_value_id'],
				'name'            => $option_value['name'],
				'image'           => $option_value['image'],
				'sort_order'      => $option_value['sort_order']
			);
		}

		return $option_value_data;
	}

	public function getAttributes($data = array()) {
		$sql = "
			SELECT *
			, (SELECT agd.name
				FROM " . DB_PREFIX . "attribute_group_description agd
				WHERE agd.attribute_group_id = a.attribute_group_id
				AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS attribute_group
			FROM " . DB_PREFIX . "attribute a
			LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id)
			WHERE ad.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(ad.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}

		if (!empty($data['filter_attribute_group_id'])) {
			$sql .= " AND a.attribute_group_id = '" . $this->db->escape($data['filter_attribute_group_id']) . "'";
		}

		$sort_data = array(
			'ad.name',
			'attribute_group',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY attribute_group, ad.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTaxClasses($data = array()) {
    	if ($data) {
			$sql = "
				SELECT *
				FROM " . DB_PREFIX . "tax_class
			";

			$sql .= " ORDER BY title";

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

	  		$query = $this->db->query($sql);

			return $query->rows;
		} else {
			$tax_class_data = $this->cache->get('tax_class.member');

			if ($tax_class_data === false) {
				$query = $this->db->query("
					SELECT *
					FROM " . DB_PREFIX . "tax_class
				");

				$tax_class_data = $query->rows;

				$this->cache->set('tax_class.member', $tax_class_data);
			}

			return $tax_class_data;
		}
	}

	public function getStockStatuses($data = array()) {
		if ($data) {
			$sql = "
				SELECT *
				FROM " . DB_PREFIX . "stock_status
				WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'
			";

			$sql .= " ORDER BY name";

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			return $query->rows;
		} else {
			$stock_status_data = $this->cache->get('stock_status.member.' . (int)$this->config->get('config_language_id'));

			if ($stock_status_data === false) {
				$query = $this->db->query("
					SELECT stock_status_id
					, name FROM " . DB_PREFIX . "stock_status
					WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'
					ORDER BY name
				");

				$stock_status_data = $query->rows;

				$this->cache->set('stock_status.member.' . (int)$this->config->get('config_language_id'), $stock_status_data);
			}

			return $stock_status_data;
		}
	}

	public function getWeightClasses($data = array()) {
		$sql = "
			SELECT *
			FROM " . DB_PREFIX . "weight_class wc
			LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id)
			WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		if ($data) {
			$sort_data = array(
				'title',
				'unit',
				'value'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY title";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			return $query->rows;
		} else {
			$weight_class_data = $this->cache->get('weight_class.' . (int)$this->config->get('config_language_id'));

			if ($weight_class_data === false) {
				$weight_class_data = array();

				$query = $this->db->query($sql);

				// $weight_class_data = $query->rows;

				foreach ($query->rows as $result) {
					$weight_class_data[$result['weight_class_id']] = array(
						'weight_class_id' => $result['weight_class_id'],
						'title'           => $result['title'],
						'unit'            => $result['unit'],
						'value'           => $result['value']
					);
				}

				$this->cache->set('weight_class.' . (int)$this->config->get('config_language_id'), $weight_class_data);
			}

			return $weight_class_data;
		}
	}

	public function getLengthClasses($data = array()) {
		$sql = "
			SELECT *
			FROM " . DB_PREFIX . "length_class lc
			LEFT JOIN " . DB_PREFIX . "length_class_description lcd ON (lc.length_class_id = lcd.length_class_id)
			WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		if ($data) {
			$sort_data = array(
				'title',
				'unit',
				'value'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY title";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			return $query->rows;
		} else {
			$length_class_data = $this->cache->get('length_class.' . (int)$this->config->get('config_language_id'));

			if ($length_class_data === false) {
				$length_class_data = array();

				$query = $this->db->query($sql);

				// $length_class_data = $query->rows;

				foreach ($query->rows as $result) {
					$length_class_data[$result['length_class_id']] = array(
						'length_class_id' => $result['length_class_id'],
						'title'           => $result['title'],
						'unit'            => $result['unit'],
						'value'           => $result['value']
					);
				}

				$this->cache->set('length_class.' . (int)$this->config->get('config_language_id'), $length_class_data);
			}

			return $length_class_data;
		}
	}

	public function getGeoZones() {
		$geo_zone_data = $this->cache->get('geo_zone');

		if ($geo_zone_data === false) {
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "geo_zone
				ORDER BY geo_zone_id ASC
			");

			$geo_zone_data = $query->rows;

			$this->cache->set('geo_zone', $geo_zone_data);
		}

		return $geo_zone_data;
	}

	public function getZonesByGeoZoneId($geo_zone_id) {
		$geo_zone_zones_data = $this->cache->get('zone.geo_zone.' . (int)$geo_zone_id);

		if ($geo_zone_zones_data === false) {
			$sql = "
				SELECT zgz.zone_id
				, z.name
				, z.code
				FROM " . DB_PREFIX . "zone_to_geo_zone zgz
				INNER JOIN " . DB_PREFIX . "zone z ON (zgz.zone_id = z.zone_id)
				INNER JOIN " . DB_PREFIX . "country c ON (zgz.country_id = c.country_id)
				INNER JOIN " . DB_PREFIX . "geo_zone gz ON (zgz.geo_zone_id = gz.geo_zone_id)
				WHERE zgz.geo_zone_id = '" . (int)$geo_zone_id . "'
				AND z.status = '1'
				AND c.status = '1'
				ORDER BY z.name ASC
			";

			$query = $this->db->query($sql);

			$geo_zone_zones_data = $query->rows;

			$this->cache->set('zone.geo_zone.' . (int)$geo_zone_id, $geo_zone_zones_data);
		}

		return $geo_zone_zones_data;
	}

	public function getProductShipping($product_id) {
		$query = $this->db->query("
			SELECT DISTINCT ps.`geo_zone_id`
			, MAX(ps.`first`) AS `first`
			, MAX(ps.`additional`) AS `additional`
			FROM `" . DB_PREFIX . "product_shipping` ps
			LEFT JOIN `slgc_geo_zone` gz ON ps.geo_zone_id = gz.geo_zone_id
			WHERE `product_id` = '" . (int)$product_id . "'
			GROUP BY ps.`geo_zone_id`
			ORDER BY gz.`name`
		");

		$data = array();

		foreach ($query->rows as $result) {
			$data[$result['geo_zone_id']] = array(
				'geo_zone_id'	=> $result['geo_zone_id'],
				'first'			=> $result['first'],
				'additional'	=> $result['additional']
			);
		}

		return $data;
	}

	public function getCustomerGroups($data = array()) {
		$sql = "
			SELECT *
			FROM " . DB_PREFIX . "customer_group cg
			LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (cg.customer_group_id = cgd.customer_group_id)
			WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		$sort_data = array(
			'cgd.name',
			'cg.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cgd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	private function editProductShipping($product_id, $product_shipping) {
		if ($product_shipping) {
			$this->db->query("
				DELETE ps
				FROM " . DB_PREFIX . "product_shipping ps
				LEFT JOIN " . DB_PREFIX . "product_member pm ON (ps.product_id = pm.product_id)
				WHERE ps.product_id = '" . (int)$product_id . "'
				AND pm.customer_id = '" . (int)$this->customer->getId() . "'
			");

			foreach ($product_shipping as $key => $value) {
				if (isset($value['first']) && trim($value['first']) !== '') {
					$first = $value['first'];
					$additional = isset($value['additional']) ? $value['additional'] : $value['first'];

					$this->db->query("
						INSERT INTO " . DB_PREFIX . "product_shipping
						SET product_id = '" . (int)$product_id . "'
						, geo_zone_id = '" . (int)$value['geo_zone_id'] . "'
						, first = '" . $this->db->escape($first) . "'
						, additional = '" . $this->db->escape($additional) . "'
					");
				}
			}
		}
	}

	private function deleteProductShipping($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_shipping
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductMember($product_id, $member_id, $customer_id) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_member
			SET product_id = '" . (int)$product_id . "'
			, member_account_id = '" . (int)$member_id . "'
			, customer_id = '" . (int)$customer_id . "'
		");
	}

	private function deleteProductMember($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_member
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductDescription($product_id, $language_id, $value) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_description
			SET product_id = '" . (int)$product_id . "'
			, language_id = '" . (int)$language_id . "'
			, name = '" . $this->db->escape($value['name']) . "'
			, meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'
			, meta_description = '" . $this->db->escape($value['meta_description']) . "'
			, description = '" . $this->db->escape($value['description']) . "'
			, tag = '" . $this->db->escape($value['tag']) . "'
		");
	}

	private function deleteProductDescription($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_description
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductToCategory($product_id, $category_id) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_to_category
			SET product_id = '" . (int)$product_id . "'
			, category_id = '" . (int)$category_id . "'
		");
	}

	private function deleteProductToCategory($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_to_category
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function editProductDownload($product_id, $product_download) {
		// product_download is multi-dimensional for new and edited products
		if (is_array($product_download)) {
			// if the download already exists, it will contain the index download_id; delete before re-insert
			if (!empty($product_download['download_id'])) {
				$this->db->query("
					DELETE FROM " . DB_PREFIX . "download
					WHERE download_id = '" . (int)$product_download['download_id'] . "'
				");

				$this->db->query("
					DELETE FROM " . DB_PREFIX . "download_description
					WHERE download_id = '" . (int)$product_download['download_id'] . "'
				");

				$this->db->query("
					DELETE FROM " . DB_PREFIX . "product_to_download
					WHERE download_id = '" . (int)$product_download['download_id'] . "'
				");
			}

			// insert new
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "download
				SET filename = '" . $this->db->escape($product_download['filename']) . "'
				, mask = '" . $this->db->escape($product_download['mask']) . "'
				, remaining = '" . (int)$product_download['remaining'] . "'
				, member_customer_id = '" . (int)$this->customer->getId() . "'
				, date_added = NOW()
			");

			$download_id = $this->db->countAffected() ? $this->db->getLastId() : 0;

			$this->db->query("
				INSERT INTO " . DB_PREFIX . "download_description
				SET download_id = '" . (int)$download_id . "'
				, language_id = '" . (int)$this->config->get('config_language_id') . "'
				, name = '" . $this->db->escape($product_download['name']) . "'
			");

			$this->db->query("
				INSERT INTO " . DB_PREFIX . "product_to_download
				SET product_id = '" . (int)$product_id . "'
				, download_id = '" . (int)$download_id . "'
			");
		} else {
			// product_download is simply array of download_id's for new products created by copyProduct;
			// link the existing downloads to the new copy product
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "product_to_download
				SET product_id = '" . (int)$product_id . "'
				, download_id = '" . (int)$product_download . "'
			");
		}
	}

	private function deleteProductToDownload($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_to_download
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductToLayout($product_id, $store_id, $layout_id) {
		if ($layout_id) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "product_to_layout
				SET product_id = '" . (int)$product_id . "'
				, store_id = '" . (int)$store_id . "'
				, layout_id = '" . (int)$layout_id . "'
			");
		}
	}

	private function deleteProductToLayout($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_to_layout
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductToStore($product_id, $store_id) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_to_store
			SET product_id = '" . (int)$product_id . "'
			, store_id = '" . (int)$store_id . "'
		");
	}

	private function deleteProductToStore($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_to_store
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductFilter($product_id, $filter_id) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_filter
			SET product_id = '" . (int)$product_id . "'
			, filter_id = '" . (int)$filter_id . "'
		");
	}

	private function deleteProductFilter($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_filter
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductImage($product_id, $product_image) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_image
			SET product_id = '" . (int)$product_id . "'
			, image = '" . $this->db->escape(html_entity_decode($product_image['image'], ENT_QUOTES, 'UTF-8')) . "'
			, sort_order = '" . (int)$product_image['sort_order'] . "'
		");
	}

	private function deleteProductImage($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_image
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductOption($product_id, $product_option) {
		if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "product_option
				SET product_id = '" . (int)$product_id . "'
				, option_id = '" . (int)$product_option['option_id'] . "'
				, required = '" . (int)$product_option['required'] . "'
			");

			$product_option_id = $this->db->countAffected() ? $this->db->getLastId() : 0;

			if (isset($product_option['product_option_value'])) {
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$this->db->query("
						INSERT INTO " . DB_PREFIX . "product_option_value
						SET product_option_id = '" . (int)$product_option_id . "'
						, product_id = '" . (int)$product_id . "'
						, option_id = '" . (int)$product_option['option_id'] . "'
						, option_value_id = '" . (int)$product_option_value['option_value_id'] . "'
						, quantity = '" . (int)$product_option_value['quantity'] . "'
						, subtract = '" . (int)$product_option_value['subtract'] . "'
						, price = '" . (float)$product_option_value['price'] . "'
						, price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "'
						, points = '" . (int)$product_option_value['points'] . "'
						, points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "'
						, weight = '" . (float)$product_option_value['weight'] . "'
						, weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'
					");
				}
			}
		} else {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "product_option
				SET product_id = '" . (int)$product_id . "'
				, option_id = '" . (int)$product_option['option_id'] . "'
				, option_value = '" . $this->db->escape($product_option['option_value']) . "'
				, required = '" . (int)$product_option['required'] . "'
			");
		}
	}

	private function deleteProductOption($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_option
			WHERE product_id = '" . (int)$product_id . "'
		");

		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_option_value
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function editProductAttribute($product_id, $product_attribute) {
		if ($product_attribute['attribute_id']) {
			$this->db->query("
				DELETE FROM " . DB_PREFIX . "product_attribute
				WHERE product_id = '" . (int)$product_id . "'
				AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'
			");

			foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "product_attribute
					SET product_id = '" . (int)$product_id . "'
					, attribute_id = '" . (int)$product_attribute['attribute_id'] . "'
					, language_id = '" . (int)$language_id . "'
					, text = '" .  $this->db->escape($product_attribute_description['text']) . "'
				");
			}
		}
	}

	private function deleteProductAttribute($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_attribute
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductDiscount($product_id, $product_discount) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_discount
			SET product_id = '" . (int)$product_id . "'
			, customer_group_id = '" . (int)$product_discount['customer_group_id'] . "'
			, quantity = '" . (int)$product_discount['quantity'] . "'
			, priority = '" . (int)$product_discount['priority'] . "'
			, price = '" . (float)$product_discount['price'] . "'
			, date_start = '" . $this->db->escape($product_discount['date_start']) . "'
			, date_end = '" . $this->db->escape($product_discount['date_end']) . "'
		");
	}

	private function deleteProductDiscount($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_discount
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductSpecial($product_id, $product_special) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_special
			SET product_id = '" . (int)$product_id . "'
			, customer_group_id = '" . (int)$product_special['customer_group_id'] . "'
			, priority = '" . (int)$product_special['priority'] . "'
			, price = '" . (float)$product_special['price'] . "'
			, date_start = '" . $this->db->escape($product_special['date_start']) . "'
			, date_end = '" . $this->db->escape($product_special['date_end']) . "'
		");
	}

	private function deleteProductSpecial($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_special
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductRelated($product_id, $related_id) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_related
			SET product_id = '" . (int)$product_id . "'
			, related_id = '" . (int)$related_id . "'
		");
	}

	private function deleteProductRelated($product_id = null, $related_id = null) {
		$sql = "
			DELETE FROM " . DB_PREFIX . "product_related
			WHERE product_id = '" . (int)$product_id . "'
		";

		if (!is_null($related_id)) {
			$sql .= "
				AND related_id = '" . (int)$related_id . "'
			";
		}

		$this->db->query($sql);

		$sql = "
			DELETE FROM " . DB_PREFIX . "product_related
			WHERE related_id = '" . (int)$related_id . "'
		";

		if (!is_null($product_id)) {
			$sql .= "
				AND product_id = '" . (int)$product_id . "'
			";
		}

		$this->db->query($sql);
	}

	private function insertProductReward($product_id, $customer_group_id, $points) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "product_reward
			SET product_id = '" . (int)$product_id . "'
			, customer_group_id = '" . (int)$customer_group_id . "'
			, points = '" . (int)$points . "'
		");
	}

	private function deleteProductReward($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "product_reward
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	private function insertProductKeyword($product_id, $keyword) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "url_alias
			SET query = 'product_id=" . (int)$product_id . "'
			, keyword = '" . $this->db->escape($keyword) . "'
		");
	}

	private function deleteProductKeyword($product_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "url_alias
			WHERE query = 'product_id=" . (int)$product_id. "'
		");
	}

	private function insertCustomerReward($product_id, $points) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "customer_reward
			SET customer_id = '" . (int)$this->customer->getId() . "'
			, description = '" . $this->db->escape(sprintf('Anon Post - Listing ID %s', (int)$product_id)) . "'
			, points = '" . (float)$points . "'
			, date_added = NOW()
		");
	}

}
?>
