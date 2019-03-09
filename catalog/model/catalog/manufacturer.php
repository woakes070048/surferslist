<?php
class ModelCatalogManufacturer extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month cache expiration

	public function getManufacturer($manufacturer_id) {
		if (!$manufacturer_id) return array();

		$manufacturer_data = $this->cache->get('manufacturer.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$manufacturer_id);

		if ($manufacturer_data === false) {
			$manufacturer_data = array();

			$sql = "
				SELECT *
				, (SELECT COUNT(p.product_id) AS total
					FROM " . DB_PREFIX . "product p
					INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
						AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
					WHERE p.manufacturer_id = m.manufacturer_id
					AND p.status = '1'
					AND p.member_approved = '1'
					AND p.date_expiration >= NOW()
					AND p.date_available <= NOW()) AS product_count
				FROM " . DB_PREFIX . "manufacturer m
				LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id)
					AND md.language_id = '" . (int)$this->config->get('config_language_id') . "'
				LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id)
					AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "'
				AND m.status = '1'
			";

			$query = $this->db->query($sql);

			$manufacturer_data = $query->row;

			if ($manufacturer_data) {
				$manufacturer_data['product_categories'] = $this->getManufacturerProductCategories($manufacturer_id);
			}

			$this->cache->set('manufacturer.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$manufacturer_id, $manufacturer_data, $this->cache_expires);
		}

		return $manufacturer_data;
	}

	public function getManufacturers($data = array(), $cache_results = true) {
		$random = (isset($data['sort']) && $data['sort'] == 'random') ? true : false;

		$cache = md5(http_build_query($data));

		$manufacturer_data = $this->cache->get('manufacturer.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($manufacturer_data === false || $random) {
			$manufacturer_data = array();

			$sql = "
				SELECT m.manufacturer_id
				, m.name
				, m.url
				, m.image
				, m.sort_order
				, m.status
				, m.viewed
				, (SELECT COUNT(p.product_id) AS total
					FROM " . DB_PREFIX . "product p
					INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
						AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
					WHERE p.manufacturer_id = m.manufacturer_id
					AND p.status = '1'
					AND p.member_approved = '1'
					AND p.date_expiration >= NOW()
					AND p.date_available <= NOW()) AS product_count
				FROM " . DB_PREFIX . "manufacturer m
				INNER JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id)
					AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			";

			if (!empty($data['filter_category_id'])) {
				$sql .= "
					INNER JOIN " . DB_PREFIX . "manufacturer_to_category m2c ON (m.manufacturer_id = m2c.manufacturer_id)
				";

				if (!empty($data['include_parent_categories'])) {
					$sql .= "
						INNER JOIN " . DB_PREFIX . "category_path cp ON (m2c.category_id = cp.category_id)
					";
				} else {
					$sql .= "
						INNER JOIN " . DB_PREFIX . "category_path cp ON (m2c.category_id = cp.path_id)
					";
				}
			}

			$sql .= "
				WHERE m.status = '1'
			";

			if (!empty($data['filter_category_id'])) {
				$sql .= "
					AND m2c.category_id = '" . (int)$data['filter_category_id'] . "'
				";
			}

			if (!empty($data['filter_name'])) {
				$sql .= "
					AND LCASE(m.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'
				";
			}

			$sql .= "
				GROUP BY m.manufacturer_id
			";

			$sort_data = array(
				'default' 		=> 'm.sort_order',
				'sort_order' 	=> 'm.sort_order',
				'name'    		=> 'm.name',
				'viewed'  		=> 'm.viewed',
				'product_count' => 'product_count',
				'random'  		=> 'random'
			);

			if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
				if ($random) {
					$sql .= " ORDER BY Rand()";
				} else if ($data['sort'] == 'default' || $data['sort'] == 'sort_order') {
					$sql .= " ORDER BY m.sort_order ASC, LCASE(m.name) ASC";
				} else if ($data['sort'] == 'product_count') {
					$sql .= " ORDER BY product_count DESC, m.viewed DESC, m.sort_order ASC";
				} else if ($data['sort'] == 'viewed') {
					$sql .= " ORDER BY m.viewed DESC, products DESC, m.sort_order ASC";
				} else {
					if ($data['sort'] == 'name') {
						$sql .= " ORDER BY LCASE(m.name)";
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
				$sql .= " ORDER BY LCASE(m.name) ASC, m.sort_order ASC";
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

			$query = $this->db->query($sql);

			if (!empty($data['images_included'])) {
				$this->load->model('tool/image');

				foreach ($query->rows as $result) {
					$image_resized = !empty($result['image']) ? $this->model_tool_image->resize($result['image'], 188, 188, 'fw') : ''; // $this->config->get('config_image_category_width'), $this->config->get('config_image_category_height')

					$manufacturer_data[] = array(
						'manufacturer_id'	=> $result['manufacturer_id'],
						'image_resized'		=> $image_resized,
						'name'				=> $result['name'],
						'product_count'		=> $result['product_count'],
						'sort_order'		=> $result['sort_order']
					);
				}
			} else {
				$manufacturer_data = $query->rows;
			}

			if ($cache_results && !$random) {
				$this->cache->set('manufacturer.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $manufacturer_data, $this->cache_expires);
			}
		}

		return $manufacturer_data;
	}

	private function getManufacturerProductCategories($manufacturer_id) {
		if (empty($manufacturer_id)) return array();

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
			INNER JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id)
			LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (p.manufacturer_id = m2s.manufacturer_id)
				AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
				AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
				AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "category_path cp ON (p2c.category_id = cp.category_id)
			LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.path_id = cd2.category_id)
				AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p2c.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			WHERE p.manufacturer_id = '" . (int)$manufacturer_id . "'
			AND p.date_expiration >= NOW()
			AND p.status = '1'
			AND p.member_approved = '1'
			AND p.date_available <= NOW()
			AND c.status = '1'
			GROUP BY c.category_id
			, cp.category_id
			ORDER BY c.sort_order
			, LCASE(cd.name)
		"; // removed AND c.top = '1'

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalManufacturers($data = array()) {
		$sql = "
			SELECT COUNT(m.manufacturer_id) AS total
			FROM " . DB_PREFIX . "manufacturer m
			LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id)
		";

		if (!empty($data['filter_category_id'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "manufacturer_to_category m2c ON (m.manufacturer_id = m2c.manufacturer_id)";
		}

		$sql .= "
			WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
		";

		if (!empty($data['filter_category_id'])) {
			$sql .= "
				AND m2c.category_id = '" . (int)$data['filter_category_id'] . "'
			";
		}

		if (!empty($data['filter_name'])) {
			$sql .= "
				AND LCASE(m.name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'
			";
		}

		$sql .= "
			AND m.status = '1'
		";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$sql = "
			SELECT COUNT(p.product_id) AS total
			FROM " . DB_PREFIX . "product p
			INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			WHERE p.manufacturer_id = '" . (int)$manufacturer_id . "'
			AND p.status = '1'
			AND p.member_approved = '1'
			AND p.date_expiration >= NOW()
			AND p.date_available <= NOW()
		";

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getManufacturersNames() {
		$manufacturer_names = $this->cache->get('manufacturer.names.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($manufacturer_names === false) {
			$manufacturer_names = array();

			$manufacturers = $this->getManufacturers();

			foreach ($manufacturers as $manufacturer) {
				$manufacturer_name = utf8_strtolower(strip_non_alphanumeric($manufacturer['name']));

				if (utf8_substr($manufacturer_name, -1) == 's' && utf8_substr($manufacturer_name, -3) != 'ies') {
					$manufacturer_name = utf8_substr($manufacturer_name, 0, -1);
				}

				if (!isset($manufacturer_names[$manufacturer_name])) {
					$manufacturer_names[$manufacturer_name] = array($manufacturer['manufacturer_id']);
				} else {
					$manufacturer_names[$manufacturer_name][] = $manufacturer['manufacturer_id'];
				}

				$manufacturer_words = explode(' ', $manufacturer_name);

				foreach ($manufacturer_words as $manufacturer_word) {
					if (!isset($manufacturer_names[$manufacturer_word])) {
						$manufacturer_names[$manufacturer_word] = array($manufacturer['manufacturer_id']);
					} else {
						$manufacturer_names[$manufacturer_word][] = $manufacturer['manufacturer_id'];
					}
				}
			}

			// add synonyms
			$manufacturer_names['merrick'] = $manufacturer_names['channel'];

			// remove common keywords
			unset($manufacturer_names['sport']);
			unset($manufacturer_names['other']);

			$this->cache->set('manufacturer.names.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $manufacturer_names, $this->cache_expires);
		}

		return $manufacturer_names;
	}

	public function updateViewed($manufacturer_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "manufacturer
			SET viewed = (viewed + 1)
			WHERE manufacturer_id = '" . (int)$manufacturer_id . "'
		");
	}

}
?>
