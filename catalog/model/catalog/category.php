<?php
class ModelCatalogCategory extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month cache expiration

	public function getCategory($category_id) {
		$category_data = $this->cache->get('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$category_id);

		if ($category_data === false) {
			$sql = "
				SELECT DISTINCT c.*
				, cd.*
				, GROUP_CONCAT(DISTINCT cp.path_id ORDER BY cp.level SEPARATOR '_') AS path
				, GROUP_CONCAT(DISTINCT m2c.manufacturer_id) AS manufacturer_ids
				, (SELECT COUNT(p.product_id) AS total
					FROM " . DB_PREFIX . "product p
					INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
						AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
					LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
					WHERE p2c.category_id = c.category_id
					AND p.status = '1'
					AND p.member_approved = '1'
					AND p.date_expiration >= NOW()
					AND p.date_available <= NOW()) AS product_count
				FROM " . DB_PREFIX . "category c
				LEFT JOIN " . DB_PREFIX . "category_path cp ON (c.category_id = cp.category_id)
				LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
				LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
				LEFT JOIN " . DB_PREFIX . "manufacturer_to_category m2c ON (cp.path_id = m2c.category_id AND cp.level = 0)
				WHERE c.category_id = '" . (int)$category_id . "'
				AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				AND c.status = '1'
				GROUP BY cp.category_id
			";

			$query = $this->db->query($sql);

			$category_data = $query->row;

			if ($category_data) {
				$category_data['path_name'] = $this->getCategoyPathName($category_id);
			}

			$this->cache->set('category.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$category_id, $category_data, $this->cache_expires);
		}

		return $category_data;
	}

	public function getCategories($parent_id = 0) {
		$category_data = $this->cache->get('category.children.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$parent_id);

		if ($category_data === false) {
			$category_data = array();

			$sql = "
				SELECT c.category_id
				FROM " . DB_PREFIX . "category c
				LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
					AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
					AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				WHERE c.parent_id = '" . (int)$parent_id . "'
				AND c.status = '1'
				ORDER BY c.sort_order, LCASE(cd.name)
			";

			$query = $this->db->query($sql);

			foreach ($query->rows as $result) {
				$category_data[] = $this->getCategory($result['category_id']);
			}

			$this->cache->set('category.children.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$parent_id, $category_data, $this->cache_expires);
		}

		return $category_data;
	}

	public function getAllCategories($data) {
		$cache = md5(http_build_query($data));
		$all_category_data = $this->cache->get('category.all.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($all_category_data === false) {
			$all_category_data = array();

			$sql = "
				SELECT cp.category_id AS category_id
				, cd2.name AS name
				, c1.status
				, c1.image
				, c1.parent_id
				, c1.sort_order
				, cp.level
				, (SELECT COUNT(p.product_id) AS total
					FROM " . DB_PREFIX . "product p
					INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
						AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
					LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
					WHERE p2c.category_id = c1.category_id
					AND p.status = '1'
					AND p.member_approved = '1'
					AND p.date_expiration >= NOW()
					AND p.date_available <= NOW()) AS product_count
				, GROUP_CONCAT(cd1.name ORDER BY cp.level SEPARATOR '" . $this->language->get('text_separator') . "') AS path_name
				, GROUP_CONCAT(cp.path_id ORDER BY cp.level SEPARATOR '_') AS path
				, GROUP_CONCAT(c2.sort_order ORDER BY cp.level SEPARATOR '" . $this->language->get('text_separator') . "') AS sort_order_path_display
				, GROUP_CONCAT(LPAD(c2.sort_order,10,'0') ORDER BY cp.level) AS sort_order_path
				FROM " . DB_PREFIX . "category_path cp
				LEFT JOIN " . DB_PREFIX . "category c1 ON (cp.category_id = c1.category_id)
				LEFT JOIN " . DB_PREFIX . "category c2 ON (cp.path_id = c2.category_id)
				LEFT JOIN " . DB_PREFIX . "category_description cd1 ON (cp.path_id = cd1.category_id)
					AND cd1.language_id = '" . (int)$this->config->get('config_language_id') . "'
				LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (cp.category_id = cd2.category_id)
					AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'
				WHERE 1 = 1
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
			} else if (!empty($data['filter_complete'])) {
				$sql .= "
					AND c1.status = '1'
				";
			}

			if (!empty($data['filter_level_max'])) {
				$sql .= "
					AND cp.level <= '" . (int)$data['filter_level_max'] . "'
				";
			}

			$sql .= "
				GROUP BY cp.category_id
			";

			// if (!empty($data['filter_path'])) {
			// 	$sql .= " HAVING path = '" . $this->db->escape($data['filter_path']) . "'";
			// }

			$sort_data = array(
				'name',
				'path_name',
				'sort_order',
				'sort_order_path'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY name";
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

			if (!empty($data['filter_complete'])) {
				$this->load->model('catalog/manufacturer');
			}

			foreach ($query->rows as $result) {
				$category_children = !empty($data['filter_complete']) ? $this->getCategories($result['category_id']) : array();

				if (!empty($data['filter_complete']) && $result['level'] == '0' && $result['category_id'] == $result['path']) {
					$manufacturers_data = array(
						'filter_category_id' 		=> $result['category_id'],
						'include_parent_categories' => true,
						'images_included' 			=> true
					);

					$category_manufacturers = $this->model_catalog_manufacturer->getManufacturers($manufacturers_data);
				} else {
					$category_manufacturers = array();
				}

				$all_category_data[] = array(
					'category_id' 				=> $result['category_id'],
					'parent_id'   				=> $result['parent_id'],
					'name'        				=> $result['name'],
					'path'        				=> $result['path'],
					'path_name'	  				=> $result['path_name'],
					'image'  	  				=> $result['image'],
					'children'	  				=> $category_children,
					'manufacturers'				=> $category_manufacturers,
					'product_count' 			=> $result['product_count'],
					'sort_order'  				=> $result['sort_order'],
					'sort_order_path'			=> $result['sort_order_path'],
					'sort_order_path_display'	=> $result['sort_order_path_display'],
					'status'  	  				=> $result['status']
				);
			}

			$this->cache->set('category.all.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $all_category_data, $this->cache_expires);
		}

		return $all_category_data;
	}

	public function getAllCategoriesComplete() {
		$data = array(
			'filter_complete' => true
		);

		return $this->getAllCategories($data);
	}

	public function getCategoriesNames() {
		$category_names = $this->cache->get('category.names.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($category_names === false) {
			$category_names = array();

			$data = array(
				'filter_status'		 => 1,
				'sort'               => $this->config->get('apac_categories_sort_default') ? $this->config->get('apac_categories_sort_default') : 'sort_order_path',
				'order'              => 'ASC',
				'start'              => 0,
				'limit'              => 999
			);

			$categories = $this->getAllCategories($data);

			foreach ($categories as $category) {
				$category_name = utf8_strtolower(strip_non_alphanumeric($category['name']));

				if (utf8_substr($category_name, -1) == 's' && utf8_substr($category_name, -3) != 'ies') {
					$category_name = utf8_substr($category_name, 0, -1);
				}

				$category_words = explode(' ', $category_name);

				foreach ($category_words as $category_word) {
					if (!isset($category_names[$category_word])) {
						$category_names[$category_word] = array($category['category_id']);
					} else {
						$category_names[$category_word][] = $category['category_id'];
					}
				}
			}

			// add synonyms
			if (is_array($this->config->get('config_category_synonyms'))) {
				foreach ($this->config->get('config_category_synonyms') as $key => $value) {
					// remove common keywords
					if (empty($value)) {
						unset($category_names[$key]);
						continue;
					}

					$synonyms = explode(',', $value);

					foreach ($synonyms as $synonym) {
						if (isset($category_names[$synonym])) {
							$category_names[$synonym] = array_merge($category_names[$synonym], $category_names[$key]);
						} else {
							$category_names[$synonym] = $category_names[$key];
						}
					}
				}
			}

			$this->cache->set('category.names.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $category_names, $this->cache_expires);
		}

		return $category_names;
	}

	public function getCategoyPathName($category_id) {
		$query = $this->db->query("
			SELECT name
			, parent_id
			FROM " . DB_PREFIX . "category c
			LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
				AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			WHERE c.category_id = '" . (int)$category_id . "'
			ORDER BY c.sort_order, cd.name ASC
		");

		if ($query->num_rows) {
			if ($query->row['parent_id']) {
				return $this->getCategoyPathName($query->row['parent_id']) . $this->language->get('text_separator') . $query->row['name'];
			} else {
				return $query->row['name'];
			}
		} else {
			return '';
		}
	}

	public function getTotalCategories() {
		$query = $this->db->query("
			SELECT COUNT(*) AS total
			FROM " . DB_PREFIX . "category c
			LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
			WHERE c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			AND c.status = '1'
		");

		return $query->row['total'];
	}

	public function getCategoryIdsByParentId($category_id) {
		$category_data = array();

		$category_query = $this->db->query("
			SELECT category_id
			FROM " . DB_PREFIX . "category
			WHERE parent_id = '" . (int)$category_id . "'
		");

		foreach ($category_query->rows as $category) {
			$category_data[] = $category['category_id'];

			$children = $this->getCategoryIdsByParentId($category['category_id']);

			if ($children) {
				$category_data = array_merge($children, $category_data);
			}
		}

		return $category_data;
	}

	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("
			SELECT COUNT(*) AS total
			FROM " . DB_PREFIX . "category c
			LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
			WHERE c.parent_id = '" . (int)$parent_id . "'
			AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			AND c.status = '1'
		");

		return $query->row['total'];
	}

}
?>
