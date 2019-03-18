<?php
class ModelAccountMember extends Model {
	use Contact;

	public function getMember() {
		$sql = "
			SELECT DISTINCT *
			, c.customer_group_id
			, cgd.name AS account_group
			, cmg.member_group_name AS member_group
			, CONCAT(c.firstname, ', ', c.lastname) AS name
			, m.date_added AS date_added
			, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'member_id=" . (int)$this->customer->getProfileId() . "' LIMIT 1) AS member_url_alias
			, (SELECT AVG(r1.rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.member_id = m.member_account_id AND r1.status = '1' GROUP BY r1.member_id) AS rating
			, (SELECT COUNT(r2.review_id) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.member_id = m.member_account_id AND r2.status = '1' GROUP BY r2.member_id) AS reviews
			FROM " . DB_PREFIX . "customer_member_account m
			INNER JOIN " . DB_PREFIX . "customer c ON m.customer_id = c.customer_id
			LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON c.customer_group_id = cgd.customer_group_id
				AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (m.member_group_id = cmg.member_group_id)
			WHERE m.customer_id = '" . (int)$this->customer->getId() . "'
			AND c.status = '1'
			AND c.member_enabled = '1'
			AND c.approved = '1'
		";

		$query = $this->db->query($sql);

		return $query->row;
	}

	public function addMember($data) {
		$sql = "
			INSERT INTO " . DB_PREFIX . "customer_member_account
			SET customer_id = '" . (int)$this->customer->getId() . "'
		";

		if (!empty($data['member_account_image'])) {
			$sql .= "
				, member_account_image = '" . $this->db->escape($data['member_account_image']) . "'
			";
		}

		if (!empty($data['member_account_banner'])) {
			$sql .= "
				, member_account_banner = '" . $this->db->escape($data['member_account_banner']) . "'
			";
		}

		if (!empty($data['member_directory_images'])) {
			$sql .= "
				, member_directory_images = '" .  $this->db->escape($data['member_directory_images']) . "'
			";
		} else {
			$sql .= "
				, member_directory_images = '" . $this->config->get('member_image_upload_directory')  . "'
			";
		}

		if (!empty($data['member_directory_downloads'])) {
			$sql .= "
				, member_directory_downloads = '" . $this->db->escape($data['member_directory_downloads']) . "'
			";
		} else {
			$sql .= "
				, member_directory_downloads = '" . $this->config->get('member_download_directory') . "'
			";
		}

		$sql .= "
			, member_account_name = '" . $this->db->escape($data['member_account_name']) . "'
			, member_account_description = '" . $this->db->escape($data['member_account_description']) . "'
			, member_tag = '" . $this->db->escape($data['member_tag']) . "'
			, member_city = '" . $this->db->escape($data['member_city']) . "'
			, member_zone_id = '" . (int)$data['member_zone_id'] . "'
			, member_country_id = '" . (int)$data['member_country_id'] . "'
			, member_group_id = '" . (int)$data['member_group_id'] . "'
			, member_custom_field_01 = '" . $this->db->escape($data['member_custom_field_01']) . "'
			, member_custom_field_02 = '" . $this->db->escape($data['member_custom_field_02']) . "'
			, member_custom_field_03 = '" . $this->db->escape($data['member_custom_field_03']) . "'
			, member_custom_field_04 = '" . $this->db->escape($data['member_custom_field_04']) . "'
			, member_custom_field_05 = '" . $this->db->escape($data['member_custom_field_05']) . "'
			, member_custom_field_06 = '" . $this->db->escape($data['member_custom_field_06']) . "'
			, member_paypal_account = '" . $this->db->escape($data['member_paypal_account']) . "'
			, member_max_products = '" . (int)$this->config->get('member_products_max') . "'
			, member_commission_rate = '" . (float)$this->config->get('member_commission_rate') . "'
			, sort_order = '" . (int)$data['sort_order'] . "'
			, viewed = '" . (int)$data['viewed'] . "'
			, date_added = NOW()
		";

		$this->db->query($sql);

		$member_account_id = $this->db->countAffected() ? $this->db->getLastId() : 0;

		if (!$member_account_id) {
			return 0;
		}

		// Add an incomplete address if no default exists yet?
		// if (!$this->customer->getAddressId()) {
		// 	$this->db->query("
		// 		INSERT INTO " . DB_PREFIX . "address
		// 		SET customer_id = '" . (int)$this->customer->getId() . "'
		// 		, firstname = '" . $this->customer->getFirstName() . "'
		// 		, lastname = '" . $this->customer->getLastName() . "'
		// 		, company = ''
		// 		, company_id = ''
		// 		, tax_id = ''
		// 		, address_1 = ''
		// 		, address_2 = ''
		// 		, city = '" . $this->db->escape($data['member_city']) . "'
		// 		, postcode = ''
		// 		, country_id = '" . (int)$data['member_country_id'] . "'
		// 		, zone_id = '" . (int)$data['member_zone_id'] . "'
		// 	");
		//
		// 	$address_id = $this->db->countAffected() ? $this->db->getLastId() : 0;
		//
		//  if ($address_id) {
		// 		$this->db->query("
		// 			UPDATE " . DB_PREFIX . "customer
		// 			SET address_id = '" . (int)$address_id . "'
		// 			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		// 		");
		//  }
		// }

		$this->db->query("
			INSERT INTO " . DB_PREFIX . "customer_notify (`customer_id`, `date_modified`)
			VALUES('" . (int)$this->customer->getId() . "', NOW())
		"); // all other columns have defaults of "0" or "1"

		// members automatically enabled for new accounts,
		// but account approval is still dependent upon Customer Group settings
		$this->db->query("
			UPDATE " . DB_PREFIX . "customer
			SET member_enabled = '1'
			, customer_group_id = '" . (int)$data['customer_group_id'] . "'
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		");

		if (!empty($data['keyword'])) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "url_alias
				SET query = 'member_id=" . (int)$member_account_id . "'
				, keyword = '" . $this->db->escape($data['keyword']) . "'
			");
		}

		// multi-language and multi-store (to-do)
		/*
		foreach ($data['meta_description'] as $language_id => $value) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "customer_member_account_description
				SET member_account_id = '" . (int)$member_account_id . "'
				, language_id = '" . (int)$language_id . "'
				, name = '" . $this->db->escape($value['name']) . "'
				, description = '" . $this->db->escape($value['description']) . "'
				, title = '" . $this->db->escape($value['title']) . "'
				, meta_description = '" . $this->db->escape($value['meta_description']) . "'
				, meta_keywords = '" . $this->db->escape($value['meta_keywords']) . "'
				, tag = '" . $this->db->escape($value['tag']) . "'
			");
		}

		if (isset($data['member_store'])) {
			foreach ($data['member_store'] as $store_id) {
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "customer_member_account_to_store
					SET member_account_id = '" . (int)$member_account_id . "'
					, store_id = '" . (int)$store_id . "'
				");
			}
		}
		*/

		$this->cache->delete('member.');

		// email notification
		$this->load->language('mail/member');
		$this->load->model('account/customer_group');

		$member_group_info = $this->model_account_customer_group->getCustomerMemberGroup($data['member_group_id']);

		$customer_name = $this->customer->getLastName() . ', ' . $this->customer->getFirstName();
		$profile_name = $data['member_account_name'];
		$profile_url = $this->url->link('product/member/info', 'member_id=' . $member_account_id, 'SSL');
		$profile_type = $member_group_info['member_group_name'];
		$profile_paypal = !empty($data['member_paypal_account']) ? $data['member_paypal_account'] : $this->language->get('text_none');

		$mail_sent = $this->sendEmail(array(
			'to' 		=> $this->config->get('config_email'),
			'from' 		=> $this->config->get('config_email_noreply'),
			'sender' 	=> $this->config->get('config_name'),
			'subject' 	=> sprintf($this->language->get('text_profile_activated'), $this->config->get('config_name'), $data['member_account_name']),
			'message' 	=> sprintf($this->language->get('text_profile_mail'), $customer_name, $profile_name, $profile_url, $profile_type, $profile_paypal),
			'reply' 	=> $this->config->get('config_email_noreply'),
			'admin'		=> true
		));

		return $member_account_id;
	}

	public function editMember($data) {
		$sql = "
			UPDATE " . DB_PREFIX . "customer_member_account
			SET member_account_name = '" . $this->db->escape($data['member_account_name']) . "'
		";

		if (isset($data['member_group_id'])) {
			$sql .= "
				, member_group_id = '" . (int)$data['member_group_id'] . "'
			";
		}

		if (isset($data['member_account_image'])) {
			$sql .= "
				, member_account_image = '" . $this->db->escape($data['member_account_image']) . "'
			";
		}

		if (isset($data['member_account_banner'])) {
			$sql .= "
				, member_account_banner = '" . $this->db->escape($data['member_account_banner']) . "'
			";
		}

		if (isset($data['member_paypal_account'])) {
			$sql .= "
				, member_paypal_account = '" . $this->db->escape($data['member_paypal_account']) . "'
			";
		}

		if (isset($data['sort_order'])) {
			$sql .= "
				, sort_order = '" . (int)$data['sort_order'] . "'
			";
		}

		$sql .= "
			, member_account_description = '" . $this->db->escape($data['member_account_description']) . "'
			, member_tag = '" . $this->db->escape($data['member_tag']) . "'
			, member_city = '" . $this->db->escape($data['member_city']) . "'
			, member_zone_id = '" . (int)$data['member_zone_id'] . "'
			, member_country_id = '" . (int)$data['member_country_id'] . "'
			, member_custom_field_01 = '" . $this->db->escape($data['member_custom_field_01']) . "'
			, member_custom_field_02 = '" . $this->db->escape($data['member_custom_field_02']) . "'
			, member_custom_field_03 = '" . $this->db->escape($data['member_custom_field_03']) . "'
			, member_custom_field_04 = '" . $this->db->escape($data['member_custom_field_04']) . "'
			, member_custom_field_05 = '" . $this->db->escape($data['member_custom_field_05']) . "'
			, member_custom_field_06 = '" . $this->db->escape($data['member_custom_field_06']) . "'
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		";

		$this->db->query($sql);

		// membership type
		if (isset($data['customer_group_id'])) {
			$this->db->query("
				UPDATE " . DB_PREFIX . "customer
				SET customer_group_id = '" . (int)$data['customer_group_id'] . "'
				WHERE customer_id = '" . (int)$this->customer->getId() . "'
			");
		}

		// update all of member's for-sale listings to classified if PayPal removed
		if (isset($data['member_paypal_account']) && !$data['member_paypal_account']) {
			$this->db->query("
				UPDATE " . DB_PREFIX . "product
				SET quantity = '0'
				WHERE member_customer_id = '" . (int)$this->customer->getId() . "'
				AND quantity > 0
			");
		}

		// multi-language and multi-store (to-do)
		/*
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "customer_member_account_description
			WHERE member_id = '" . (int)$member_id . "'
		");

		foreach ($data['member_description'] as $language_id => $value) {
			$this->db->query("
				INSERT INTO " . DB_PREFIX . "customer_member_account_description
				SET member_account_id = '" . (int)$member_account_id . "'
				, language_id = '" . (int)$language_id . "'
				, name = '" . $this->db->escape($value['name']) . "'
				, description = '" . $this->db->escape($value['description']) . "'
				, title = '" . $this->db->escape($value['title']) . "'
				, meta_description = '" . $this->db->escape($value['meta_description']) . "'
				, meta_keywords = '" . $this->db->escape($value['meta_keywords']) . "'
				, tag = '" . $this->db->escape($value['tag']) . "'
			");
		}

		if (isset($data['member_store'])) {
			$this->db->query("
				DELETE FROM " . DB_PREFIX . "customer_member_account_to_store
				WHERE member_account_id = '" . (int)$member_account_id . "'
			");

			foreach ($data['member_store'] as $store_id) {
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "customer_member_account_to_store
					SET member_account_id = '" . (int)$member_account_id . "'
					, store_id = '" . (int)$store_id . "'
				");
			}
		}
		*/

		// url alias changes to member profile
		if (!empty($data['member_url_alias'])) {
			$this->db->query("
				DELETE FROM " . DB_PREFIX . "url_alias
				WHERE query = 'member_id=" . (int)$this->customer->getProfileId() . "'
			");

			$this->db->query("
				INSERT INTO " . DB_PREFIX . "url_alias
				SET query = 'member_id=" . (int)$this->customer->getProfileId() . "'
				, keyword = '" . $this->db->escape($data['member_url_alias']) . "'
			");
		}

		$this->cache->delete('member.');
		$this->cache->delete('member_' . (int)$this->customer->getProfileId());
	}

	public function getMemberIdByName($name, $zone_id = 0, $country_id = 0) {
		if (empty($name)) return 0;

		$query = $this->db->query("
			SELECT member_account_id
			FROM " . DB_PREFIX . "customer_member_account
			WHERE LOWER(member_account_name) = '" . $this->db->escape(utf8_strtolower($name)) . "'
			AND member_zone_id = '" . (int)$zone_id . "'
			AND member_country_id = '" . (int)$country_id . "'
			LIMIT 1
		");

		return $query->num_rows ? $query->row['member_account_id'] : 0;
	}

	public function getMemberByCustomerId($customer_id) {
		if ((int)$customer_id <= 0) return array();

		$query = $this->db->query("
			SELECT m.customer_id
			, m.member_account_id
			, m.member_account_name
			, m.member_directory_images
			, m.member_directory_downloads
			, m.member_city
			, m.member_zone_id
			, m.member_country_id
			, cmg.member_group_name
			, cmg.auto_renew_enabled
			, cmg.inventory_enabled
			, cn.email_contact
			, cn.email_post
			, cn.email_discuss
			, cn.email_review
			, cn.email_flag
			FROM " . DB_PREFIX . "customer_member_account m
			LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (m.member_group_id = cmg.member_group_id)
			LEFT JOIN " . DB_PREFIX . "customer_notify cn ON (m.customer_id = cn.customer_id)
			WHERE m.customer_id = '" . (int)$customer_id . "'
		");

		return $query->row;
	}

	public function getMemberByMemberId($member_id) {
		if ((int)$member_id <= 0) return array();

		$query = $this->db->query("
			SELECT m.customer_id
			, m.member_account_id
			, m.member_account_name
			, m.member_directory_images
			, m.member_directory_downloads
			, m.member_city
			, m.member_zone_id
			, m.member_country_id
			, cmg.member_group_name
			, cmg.auto_renew_enabled
			, cmg.inventory_enabled
			, cn.email_contact
			, cn.email_post
			, cn.email_discuss
			, cn.email_review
			, cn.email_flag
			FROM " . DB_PREFIX . "customer_member_account m
			LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (m.member_group_id = cmg.member_group_id)
			LEFT JOIN " . DB_PREFIX . "customer_notify cn ON (m.customer_id = cn.customer_id)
			WHERE m.member_account_id = '" . (int)$member_id . "'
		");

		return $query->row;
	}

	public function getTotalMembersByName($name) {
		if (empty($name)) return 0;

		$query = $this->db->query("
			SELECT COUNT(member_account_id) AS total
			FROM " . DB_PREFIX . "customer_member_account
			WHERE LOWER(member_account_name) = '" . $this->db->escape(utf8_strtolower($name)) . "'
		");

		return $query->row['total'];
	}

	public function getLastMemberSortOrder() {
		$query = $this->db->query("
			SELECT IFNULL(sort_order, 0) AS last_sort_order
			FROM " . DB_PREFIX . "customer_member_account
			ORDER BY last_sort_order DESC
			LIMIT 1
		");

		return $query->row['last_sort_order'];
	}

}
?>
