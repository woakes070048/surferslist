<?php
class ModelToolSeoUrl extends Model {
	private $debug = false;

	public function getKeyword($url_query) {
		$url_keyword = '';

		$query = $this->db->query("
			SELECT keyword
			FROM " . DB_PREFIX . "url_alias
			WHERE query = '" . $this->db->escape($url_query) . "'
			LIMIT 1
		");

		if ($query->num_rows && $query->row['keyword']) {
			$url_keyword = $query->row['keyword'];
		}

		if ($this->debug) $this->log->write('getKeyword(\'' . $url_query . '\'): ' . ($url_keyword ?: 'not found'));

		return $url_keyword;
	}

	public function getQuery($url_keyword) {
		$url_query = '';

		$query = $this->db->query("
			SELECT query
			FROM " . DB_PREFIX . "url_alias
			WHERE LOWER(keyword) = '" . $this->db->escape(utf8_strtolower($url_keyword)) . "'
		");

		if ($query->num_rows) {
			// handle duplicates
			if ($query->num_rows > 1) {
				$url_query = array();

				foreach ($query->rows as $row) {
					if (!empty($row['query'])) {
						$url_query[] = $row['query'];
					}
				}
			} else if ($query->row['query']) {
				$url_query = $query->row['query'];
			}
		}

		if ($this->debug) $this->log->write('getQuery(\'' . $url_keyword . '\'): ' . ($url_query ?: 'not found'));

		return $url_query;
	}

	public function getProductKeyword($url_query) {
		$url_keyword = '';

		$data = $this->cache->get('product.route') ?: $this->getUrlAlias('product');

		if (array_key_exists($url_query, $data)) {
			$url_keyword = $data[$url_query];
		} else {
			$url_keyword = $this->getKeyword($url_query);

			// if (!$url_keyword) {
			// 	// getProductRetiredById()
			// }
		}

		return $url_keyword;
	}

	public function getProductQuery($url_keyword) {
		$data = $this->cache->get('product.route') ?: $this->getUrlAlias('product');

		if (in_array($url_keyword, $data, true)) {
			return array_search($url_keyword, $data);
		} else {
			return $this->getQuery($url_keyword);
		}
	}

	public function getUrlAlias($type = 'default') {
		$data = $this->cache->get($type . '.route');

		if ($data === false) {
			$data = array();

			$expires = 60 * 60; // default 1 hour cache expiration

			switch ($type) {
				case 'manufacturer':
					$where = "
						WHERE query LIKE 'manufacturer_id=%'
					";
					$expires = 60 * 60 * 24; // 1 day cache expiration
					break;
				case 'member':
					$where = "
						WHERE query LIKE 'member_id=%'
					";
					break;
				case 'product':
					$where = "
						WHERE query LIKE 'product_id=%'
					";
					break;
				case 'default':
				default:
					$where = " WHERE query NOT LIKE 'product_id=%'";
			}

			$query = $this->db->query("
				SELECT query, keyword
				FROM " . DB_PREFIX . "url_alias
			" . $where);

			foreach ($query->rows as $result) {
				$data[$result['query']] = $result['keyword'];
			}

			$this->cache->set($type . '.route', $data, $expires);

			if ($this->debug) $this->log->write('getUrlAlias(\'' . $type . '\'): ' . count($data));
		}

		return $data;
	}

	public function getProductsRetired() {
		$data = array();

		$query = $this->db->query("
			SELECT product_id
			, member_customer_id
			, manufacturer_id
			, keyword
			FROM " . DB_PREFIX . "product_retired
		");

		foreach ($query->rows as $result) {
			$data[$result['keyword']] = $result;
		}

		$this->cache->set('product.route.retired', $data);

		if ($this->debug) $this->log->write('getProductsRetired(): ' . count($data));

		return $data;
	}

	public function getProductData($data) {
		if (empty($data['route']) || $data['route'] != 'product/product' || empty($data['product_id'])) {
			return array();
		}

		$product_data = array();

		// product_id.min cache files generated in product/product/getProductData
		$product_cache = $this->cache->get('product_' . (int)$data['product_id'] . '.min.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)($this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id')));

		if ($product_cache === false) {
			$sql = sprintf('
				SELECT		GROUP_CONCAT(`cp`.`path_id` ORDER BY `cp`.`level` SEPARATOR "_") AS `path`
							, `p`.`manufacturer_id`
				FROM		`%1$sproduct` `p`
				LEFT JOIN	`%1$sproduct_to_category` `p2c` ON `p`.`product_id` = `p2c`.`product_id`
				LEFT JOIN	`%1$scategory_to_store` `c2s` ON `p2c`.`category_id` = `c2s`.`category_id`
				LEFT JOIN	`%1$scategory` `c` ON `p2c`.`category_id` = `c`.`category_id`
				LEFT JOIN	`%1$scategory_path` `cp` ON `p2c`.`category_id` = `cp`.`category_id`
				WHERE		`p`.`product_id` = %2$s
				AND			(`c2s`.`store_id` = %3$s OR `c2s`.`store_id` IS NULL)
				AND			(`c`.`status` = 1 OR `c`.`status` IS NULL)
				GROUP BY	`cp`.`category_id`
				ORDER BY	`path` DESC
				LIMIT 1
				',
				DB_PREFIX,
				(int) $data['product_id'],
				(int) $this->config->get('config_store_id')
			);

			$result = $this->db->query($sql);

			if ($result->num_rows) {
				$product_data = $result->row;
			}
		} else {
			$product_data = array(
				'path'				=> $product_cache['path'],
				'manufacturer_id'	=> $product_cache['manufacturer_id']
			);
		}

		$product_data['route'] = 'product/product';
		$product_data['product_id'] = $data['product_id'];

		if (!empty($data['tracking'])) {
			$product_data['tracking'] = $data['tracking'];
		}

		if ($this->debug) $this->log->write('getProductData(' . $data['product_id'] . '): ' . (!empty($product_cache) ? 'cached' : 'queried'));

		return $product_data;
	}

	public function getProductRetired($url_keyword) {
		$data = array();

		$retired = $this->cache->get('product.route.retired') ?: $this->getProductsRetired();

		if (array_key_exists($url_keyword, $retired)) {
			$data = $retired[$url_keyword];
		}

		if ($this->debug) $this->log->write('getProductRetired(\'' . $url_keyword . '\'): ' . count($data));

		return $data;
	}

	public function categoryHasParent($category_id, $parent_id) {
		$result = $this->db->query("
			SELECT category_id
			FROM " . DB_PREFIX . "category
			WHERE category_id = '" . (int)$category_id . "'
			AND parent_id = '" . (int)$parent_id . "'
		");

		return $result->num_rows;
	}

	public function productHasManufacturer($product_id, $manufacturer_id) {
		$result = $this->db->query("
			SELECT product_id
			FROM " . DB_PREFIX . "product
			WHERE product_id = '" . (int)$product_id . "'
			AND manufacturer_id = '" . (int)$manufacturer_id . "'
		");

		return $result->num_rows;
	}
}
?>
