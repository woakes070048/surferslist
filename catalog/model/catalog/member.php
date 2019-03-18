<?php
class ModelCatalogMember extends Model {
	private $cache_expires = 60 * 60 * 24; // 1 day cache expiration

	public function getMember($member_id) {
		if ((int)$member_id <= 0) return array();

		$member_data = $this->cache->get('member_' . (int)$member_id . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($member_data === false) {
			$member_data = array();

			$sql = "
				SELECT DISTINCT m.*
				, CONCAT(c.firstname, ', ', c.lastname) AS name
				, c.address_id
				, c.email
				, cgd.name AS account_group
				, cmg.member_group_name AS member_group
				, m.date_added AS date_added
				, (SELECT AVG(r1.rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.member_id = m.member_account_id AND r1.status = '1' GROUP BY r1.member_id) AS rating
				, (SELECT COUNT(r2.review_id) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.member_id = m.member_account_id AND r2.status = '1' GROUP BY r2.member_id) AS reviews
				, (SELECT COUNT(q.question_id) AS total FROM " . DB_PREFIX . "question q WHERE q.member_id = m.member_account_id AND q.status = '1' AND product_id = '0' GROUP BY q.member_id) AS questions
				, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'member_id=" . (int)$member_id . "') AS keyword
				, (SELECT COUNT(p.product_id) AS total
					FROM " . DB_PREFIX . "product p
					INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
						AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
					LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
					WHERE pm.member_account_id = m.member_account_id
					AND p.status = '1'
					AND p.member_approved = '1'
					AND p.date_expiration >= NOW()
					AND p.date_available <= NOW()) AS product_count
				FROM " . DB_PREFIX . "customer_member_account m
				LEFT JOIN " . DB_PREFIX . "customer c ON (m.customer_id = c.customer_id)
					AND c.status = '1'
					AND c.member_enabled = '1'
					AND c.approved = '1'
				LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)
					AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (m.member_group_id = cmg.member_group_id)
				WHERE m.member_account_id = '" . (int)$member_id . "'
			";

			$query = $this->db->query($sql);

			$member_data = $query->row;

			if ($member_data) {
				$member_data['product_categories'] = $this->getMemberProductCategories($member_id);
				$member_data['product_manufacturers'] = $this->getMemberProductManufacturers($member_id);
			}

			$this->cache->set('member_' . (int)$member_id . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $member_data, $this->cache_expires);
		}

		return $member_data;
	}

	public function getMemberByCustomerId($customer_id) {
		$member_id = $this->getMemberIdByCustomerId($customer_id);

		return $this->getMember($member_id);
	}

	public function getMemberIdByCustomerId($customer_id) {
		if ((int)$customer_id <= 0) return 0;

		$query = $this->db->query("
			SELECT member_account_id
			FROM " . DB_PREFIX . "customer_member_account
			WHERE customer_id = '" . (int)$customer_id . "'
			AND customer_id <> 0
		");

		return $query->num_rows ? $query->row['member_account_id'] : 0;
	}

	public function getCustomerIdMemberId($member_id) {
		if ((int)$member_id <= 0) return 0;

		$query = $this->db->query("
			SELECT customer_id
			FROM " . DB_PREFIX . "customer_member_account
			WHERE member_account_id = '" . (int)$member_id . "'
		");

		return $query->num_rows ? $query->row['customer_id'] : 0;
	}

	public function getMembers($data = array()) {
		$random = (isset($data['sort']) && $data['sort'] == 'random') ? true : false;

		$cache = md5(http_build_query($data));

		$member_data = $this->cache->get('member.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($member_data === false || $random) {
			$sql = "
				SELECT m.member_account_id,
		 		m.customer_id,
				m.member_account_name,
				m.member_account_image,
				m.member_group_id,
				m.member_custom_field_01,
				m.member_custom_field_02,
				m.member_custom_field_03,
				m.member_custom_field_04,
				m.member_custom_field_05,
				m.member_custom_field_06,
				m.viewed,
				m.date_added AS date_added,
				m.sort_order AS sort_order,
				IF(m.customer_id = 0, 0, 1) AS customer_account_exists,
				IF(CHAR_LENGTH(IFNULL(m.member_account_image, '')) > 0, 1, 0) AS member_image_exists,
				CONCAT(c.firstname, ', ', c.lastname) AS fullname,
				cgd.name AS account_group,
				cmg.member_group_name AS member_group,
				(SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.member_id = m.member_account_id AND r1.status = '1' GROUP BY r1.member_id) AS rating,
				(SELECT COUNT(r2.review_id) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.member_id = m.member_account_id AND r2.status = '1' GROUP BY r2.member_id) AS reviews,
				(SELECT COUNT(p.product_id) AS total
					FROM " . DB_PREFIX . "product p
					INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
						AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
					LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
					WHERE pm.member_account_id = m.member_account_id
					AND p.status = '1'
					AND p.member_approved = '1'
					AND p.date_expiration >= NOW()
					AND p.date_available <= NOW()) AS product_count
				FROM " . DB_PREFIX . "customer_member_account m
				LEFT JOIN " . DB_PREFIX . "customer c ON m.customer_id = c.customer_id
					AND c.status = '1'
					AND c.member_enabled = '1'
					AND c.approved = '1'
				LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)
					AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (m.member_group_id = cmg.member_group_id)
				WHERE m.member_account_id <> '" . (int)$this->config->get('config_member_id') . "'
			";

			if (!empty($data['filter_country_id'])) {
				$sql .= "
					AND m.member_country_id = '" . (int)$data['filter_country_id'] . "'
				";
			}

			if (!empty($data['filter_zone_id'])) {
				$sql .= "
					AND m.member_zone_id = '" . (int)$data['filter_zone_id'] . "'
				";
			}

			if (!empty($data['filter_location'])) {
				$sql .= "
					AND LCASE(m.member_city) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_location'])) . "%'
				";
			}

			if (!empty($data['filter_customer_name'])) {
				$sql .= "
					AND LCASE(CONCAT(c.lastname, ', ', c.firstname)) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_customer_name'])) . "%'
				";
			}

			if (!empty($data['filter_member_account_name']) || !empty($data['filter_tag'])) {
				$sql .= "
					AND (
				";

				if (!empty($data['filter_member_account_name'])) {
					$implode = array();

					$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_member_account_name'])));

					foreach ($words as $word) {
						$implode[] = "
							LCASE(m.member_account_name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'
						";
					}

					if ($implode) {
						$sql .= " " . implode(" AND ", $implode) . "";
					}
					/*
					if (!empty($data['filter_description'])) {
						$sql .= " OR LCASE(m.member_account_description) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
					}
					*/
				}

				if (!empty($data['filter_member_account_name']) && !empty($data['filter_tag'])) {
					$sql .= "
						OR
					";
				}

				if (!empty($data['filter_tag'])) {
					$sql .= "
						LCASE(m.member_tag) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'
					";
				}

				$sql .= ")";
			}

			if (!empty($data['filter_customer_group_id'])) {
				$sql .= "
					AND c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'
				";
			}

			if (!empty($data['filter_member_group_id'])) {
				$sql .= "
					AND m.member_group_id = '" . (int)$data['filter_member_group_id'] . "'
				";
			}

			if (!empty($data['filter_member_group']) && is_array($data['filter_member_group'])) {
				$sql .= "
					AND cmg.customer_group_id IN (" . implode(',', $data['filter_member_group']) . ")
				";
			}

			$sort_data = array(
				'default'	 	=> 'm.sort_order',
				'sort_order' 	=> 'm.sort_order',
				'name'			=> 'm.member_account_name',
				'product_count' => 'product_count',
				'date'    		=> 'm.date_added',
				'rating'  		=> 'rating',
				'random'  		=> 'random'
			);

			if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
				if ($random) {
					$sql .= " ORDER BY Rand()";
				} else if ($data['sort'] == 'product_count' || $data['sort'] == 'default') {
					$sql .= " ORDER BY member_image_exists DESC, customer_account_exists DESC, product_count DESC, m.sort_order ASC, m.date_added ASC";
				} else if ($data['sort'] == 'sort_order') {
					$sql .= " ORDER BY member_image_exists DESC, customer_account_exists DESC, m.sort_order ASC, m.date_added ASC";
				} else if ($data['sort'] == 'rating') {
					$sql .= " ORDER BY rating DESC, reviews DESC, products DESC, m.date_added ASC";
				} else {
					if ($data['sort'] == 'name') {
						$sql .= " ORDER BY LCASE(m.member_account_name)";
					} else {
						$sql .= " ORDER BY " . $sort_data[$data['sort']];
					}

					if (isset($data['order']) && ($data['order'] == 'DESC')) {
						$sql .= " DESC";
					} else {
						$sql .= " ASC";
					}
				}
			} else {
				$sql .= " ORDER BY LCASE(m.member_account_name) ASC";
			}

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 30;
				}

				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$member_data = array();

			$query = $this->db->query($sql);

			$member_data = $query->rows;

			if (!$random) {
				$this->cache->set('member.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $member_data, $this->cache_expires);
			}
		}

		return $member_data;
	}

	public function getTotalMembers($data = array()) {
		$sql = "
			SELECT COUNT(DISTINCT m.member_account_id) AS total
			FROM " . DB_PREFIX . "customer_member_account m
			LEFT JOIN " . DB_PREFIX . "customer c ON m.customer_id = c.customer_id
				AND c.status = '1'
				AND c.member_enabled = '1'
				AND c.approved = '1'
			LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id)
				AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (m.member_group_id = cmg.member_group_id)
			WHERE m.member_account_id <> '" . (int)$this->config->get('config_member_id') . "'
		";

		if (!empty($data['filter_country_id'])) {
			$sql .= " AND m.member_country_id = '" . (int)$data['filter_country_id'] . "'";
		}

		if (!empty($data['filter_zone_id'])) {
			$sql .= " AND m.member_zone_id = '" . (int)$data['filter_zone_id'] . "'";
		}

		if (!empty($data['filter_location'])) {
			$sql .= " AND LCASE(m.member_city) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_location'])) . "%'";
		}

		if (!empty($data['filter_customer_name'])) {
			$sql .= " AND LCASE(CONCAT(c.lastname, ', ', c.firstname)) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_customer_name'])) . "%'";
		}

		if (!empty($data['filter_member_account_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_member_account_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_member_account_name'])));

				foreach ($words as $word) {
					$implode[] = "LCASE(m.member_account_name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
				/*
				if (!empty($data['filter_description'])) {
					$sql .= " OR LCASE(m.member_account_description) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				}
				*/
			}

			if (!empty($data['filter_member_account_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "LCASE(m.member_tag) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$sql .= "
				AND c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'
			";
		}

		if (!empty($data['filter_member_group_id'])) {
			$sql .= "
				AND m.member_group_id = '" . (int)$data['filter_member_group_id'] . "'
			";
		}

		if (!empty($data['filter_member_group']) && is_array($data['filter_member_group'])) {
			$sql .= "
				AND cmg.customer_group_id IN (" . implode(',', $data['filter_member_group']) . ")
			";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	private function getMemberProductCategories($member_id) {
		if (empty($member_id)) return array();

		$sql = "
			SELECT p2c.category_id
			, cd.name
			, c.image
			, c.top
			, COUNT(DISTINCT p2c.product_id) AS product_count
			, GROUP_CONCAT(DISTINCT cp.path_id ORDER BY cp.level SEPARATOR '_') AS path
			, GROUP_CONCAT(DISTINCT cd2.name ORDER BY cp.level SEPARATOR '" . $this->language->get('text_separator') . "') AS path_name
			FROM " . DB_PREFIX . "product_to_category p2c
			INNER JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)
			INNER JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			INNER JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id)
			LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
				AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			LEFT JOIN " . DB_PREFIX . "category_description cd ON (p2c.category_id = cd.category_id)
				AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "category_path cp ON (p2c.category_id = cp.category_id)
			LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.path_id = cd2.category_id)
				AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p2c.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			WHERE pm.member_account_id = '" . (int)$member_id . "'
			AND p.date_expiration >= NOW()
			AND p.status = '1'
			AND p.member_approved = '1'
			AND p.date_available <= NOW()
			AND c.status = '1'
			GROUP BY c.category_id
			, cp.category_id
			ORDER BY c.sort_order
			, LCASE(cd.name)
		"; // removed: AND c.top = '1'

		$query = $this->db->query($sql);

		return $query->rows;
	}

	private function getMemberProductManufacturers($member_id) {
		if (empty($member_id)) return array();

		$sql = "
			SELECT m.manufacturer_id
			, m.name
			, m.image
			, COUNT(DISTINCT p.product_id) AS product_count
			FROM " . DB_PREFIX . "product p
			INNER JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			INNER JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
			INNER JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (p.manufacturer_id = m2s.manufacturer_id)
				AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			WHERE pm.member_account_id = '" . (int)$member_id . "'
			AND p.status = '1'
			AND p.member_approved = '1'
			AND p.date_available <= NOW()
			AND p.date_expiration >= NOW()
			AND m.status = '1'
			GROUP BY m.manufacturer_id
			ORDER BY LCASE(m.name)
			, m.sort_order
		";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getMemberLocations() {
		$sql = "
			SELECT DISTINCT m.member_city AS location
			FROM " . DB_PREFIX . "customer_member_account m
			INNER JOIN " . DB_PREFIX . "customer c ON m.customer_id = c.customer_id
			WHERE c.status = '1'
			AND c.member_enabled = '1'
			AND c.approved = '1'
			ORDER BY m.member_city ASC
			LIMIT 100
		";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getNextPreviousMember($member_name, $member_id, $next_previous = 0) {
		$sql = "
			SELECT DISTINCT m.customer_id
			, m.member_account_name
			, m.member_account_image
			FROM " . DB_PREFIX . "customer_member_account m
			LEFT JOIN " . DB_PREFIX . "customer c ON m.customer_id = c.customer_id
				AND c.member_enabled = '1'
				AND c.approved = '1'
				AND c.status = '1'
		";

		// $sql .= " LEFT JOIN " . DB_PREFIX . "member_to_store m2s ON (m.member_id = m2s.member_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

		if (!empty($data['filter_country_id'])) {
			$sql .= " AND m.member_country_id = '" . (int)$data['filter_country_id'] . "'";
		}

		if (!empty($data['filter_zone_id'])) {
			$sql .= " AND m.member_zone_id = '" . (int)$data['filter_zone_id'] . "'";
		}

		if (!empty($data['filter_location'])) {
			$sql .= " AND LCASE(m.member_city) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_location'])) . "%'";
		}

		if (!empty($data['filter_customer_name'])) {
			$sql .= " AND LCASE(CONCAT(c.lastname, ', ', c.firstname)) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_customer_name'])) . "%'";
		}

		if (!empty($data['filter_member_account_name']) || !empty($data['filter_tag'])) {
			$sql .= " AND (";

			if (!empty($data['filter_member_account_name'])) {
				$implode = array();

				$words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_member_account_name'])));

				foreach ($words as $word) {
					$implode[] = "LCASE(m.member_account_name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
				}

				if ($implode) {
					$sql .= " " . implode(" AND ", $implode) . "";
				}
				/*
				if (!empty($data['filter_description'])) {
					$sql .= " OR LCASE(m.member_account_description) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
				}
				*/
			}

			if (!empty($data['filter_member_account_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}

			if (!empty($data['filter_tag'])) {
				$sql .= "LCASE(m.member_tag) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'";
			}

			$sql .= ")";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$sql .= "
				AND c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'
			";
		}

		if (!empty($data['filter_member_group_id'])) {
			$sql .= "
				AND m.member_group_id = '" . (int)$data['filter_member_group_id'] . "'
			";
		}

		if (!empty($data['filter_member_group']) && is_array($data['filter_member_group'])) {
			$sql .= "
				AND cmg.customer_group_id IN (" . implode(',', $data['filter_member_group']) . ")
			";
		}

		if ($next_previous == 1) {
			$sql .= "
				AND m.member_account_name > '" . $this->db->escape($member_name) . "'
				ORDER BY m.name ASC
				LIMIT 1
			";
		} else {
			$sql .= "
				AND m.member_account_name < '" . $this->db->escape($member_name) . "'
				ORDER BY m.name DESC
				LIMIT 1
			";
		}

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			return array(
				'member_id'  			=> $query->row['customer_id'],
				'member_account_name'	=> $query->row['member_account_name'],
				'member_account_image'	=> $query->row['member_account_image']
			);
		} else {
			return false;
		}
	}

	public function getMemberAddress($customer_id, $address_id) {
		if ((int)$customer_id <= 0 || (int)$address_id <= 0) return false;

		$address_query = $this->db->query("
			SELECT a.*
			, c.name AS country_name
			, c.iso_code_2 AS country_iso_code_2
			, c.iso_code_3 AS country_iso_code_3
			, c.address_format AS country_address_format
			, z.name AS zone_name
			, z.code AS zone_code
			FROM " . DB_PREFIX . "address a
			LEFT JOIN " . DB_PREFIX . "country c ON a.country_id = c.country_id
			LEFT JOIN " . DB_PREFIX . "zone z ON a.zone_id = z.zone_id
			WHERE a.address_id = '" . (int)$address_id . "'
			AND a.customer_id = '" . (int)$customer_id . "'
		");

		if (!$address_query->num_rows) {
			return false;
		} else {
			$address_data = array(
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'company_id'     => $address_query->row['company_id'],
				'tax_id'         => $address_query->row['tax_id'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $address_query->row['zone_name'], // $zone,
				'zone_code'      => $address_query->row['zone_code'], // $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $address_query->row['country_name'], // $country,
				'iso_code_2'     => $address_query->row['country_iso_code_2'], // $iso_code_2,
				'iso_code_3'     => $address_query->row['country_iso_code_3'], // $iso_code_3,
				'address_format' => $address_query->row['country_address_format'] // $address_format
			);

			return $address_data;
		}
	}

	public function getMembersNames() {
		$member_names = $this->cache->get('member.names.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($member_names === false) {
			$member_names = array();

			$members = $this->getMembers();

			foreach ($members as $member) {
				$member_name = utf8_strtolower(strip_non_alphanumeric($member['member_account_name']));

				if (utf8_substr($member_name, -1) == 's' && utf8_substr($member_name, -3) != 'ies') {
					$member_name = utf8_substr($member_name, 0, -1);
				}

				if (!isset($member_names[$member_name])) {
					$member_names[$member_name] = array($member['member_account_id']);
				} else {
					$member_names[$member_name][] = $member['member_account_id'];
				}

				$member_words = explode(' ', $member_name);

				foreach ($member_words as $member_word) {
					if (!isset($member_names[$member_word])) {
						$member_names[$member_word] = array($member['member_account_id']);
					} else {
						$member_names[$member_word][] = $member['member_account_id'];
					}
				}
			}

			// remove common keywords
			unset($member_names['shop']);
			unset($member_names['sport']);
			unset($member_names['other']);

			$this->cache->set('member.names.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $member_names, $this->cache_expires);
		}

		return $member_names;
	}

	// embed header
	public function getMemberEmbedSettings($customer_id) {
		if ((int)$customer_id <= 0) return '';

		$sql = "
			SELECT m.member_custom_field_06 AS embed_settings
			FROM " . DB_PREFIX . "customer_member_account m
			INNER JOIN " . DB_PREFIX . "customer c ON (m.customer_id = c.customer_id)
			WHERE m.customer_id = '" . (int)$customer_id . "'
			AND c.status = '1'
			AND c.member_enabled = '1'
			AND c.approved = '1'
		";

		$query = $this->db->query($sql);

		return $query->num_rows ? $query->row['embed_settings'] : '';
	}

	public function updateViewed($member_id) {
		if ((int)$member_id <= 0) return false;

		$this->db->query("
			UPDATE " . DB_PREFIX . "customer_member_account
			SET viewed = (viewed + 1)
			WHERE member_account_id = '" . (int)$member_id . "'
		");
	}

}
?>
