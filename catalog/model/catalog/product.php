<?php
class ModelCatalogProduct extends Model {
	use Contact;

	public function getProduct($product_id, $preview = false) {
		if (empty($product_id)) return array();

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$product_data = $this->cache->get('product_' . (int)$product_id . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id);

		if ($preview || $product_data === false) {
			$sql = "
				SELECT DISTINCT p.*
				, pd.*
				, pm.customer_id
				, m.name AS manufacturer
				, m.image AS manufacturer_image
				, cma.member_account_id AS member_id
				, cma.member_account_name AS member
				, cma.member_account_image AS member_image
				, cma.member_group_id
				, (SELECT keyword
					FROM " . DB_PREFIX . "url_alias
					WHERE query = 'product_id=" . (int)$product_id . "') AS keyword
			";

			$this->generateGetProductsPrices($sql, $customer_group_id);

			$sql .= "
				, (SELECT points
					FROM " . DB_PREFIX . "product_reward pr
					WHERE pr.product_id = p.product_id
					AND customer_group_id = '" . (int)$customer_group_id . "') AS reward
				, (SELECT ss.name
					FROM " . DB_PREFIX . "stock_status ss
					WHERE ss.stock_status_id = p.stock_status_id
					AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status
				, (SELECT wcd.unit
					FROM " . DB_PREFIX . "weight_class_description wcd
					WHERE p.weight_class_id = wcd.weight_class_id
					AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class
				, (SELECT lcd.unit
					FROM " . DB_PREFIX . "length_class_description lcd
					WHERE p.length_class_id = lcd.length_class_id
					AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class
				, (SELECT COUNT(q.question_id) AS total
					FROM " . DB_PREFIX . "question q
					WHERE q.product_id = p.product_id
					AND q.status = '1'
					GROUP BY q.product_id) AS questions
				FROM " . DB_PREFIX . "product p
				LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
					AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
					AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
				LEFT JOIN " . DB_PREFIX . "customer_member_account cma ON (pm.member_account_id = cma.member_account_id)
				WHERE p.product_id = '" . (int)$product_id . "'
			";

			if (!$preview) {
				$sql .= "
					AND p.status = '1'
					AND p.member_approved = '1'
					AND p.date_available <= NOW()
					AND p.date_expiration >= NOW()
				";
			}

			$query = $this->db->query($sql);

			if ($query->num_rows) {
				$member_info = array();
				$manufacturer_info = array();
				$product_condition = array();
				$location_zone = array();
				$location_country = array();

				// Categories
				$product_categories = $this->getProductCategories($query->row['product_id']);

				// Member
				if ($query->row['member_id']) {
					$this->load->model('catalog/member');
					$member_info = $this->model_catalog_member->getMember($query->row['member_id']);
				}

				// Manufacturer
				if ($query->row['manufacturer_id']) {
					$this->load->model('catalog/manufacturer');
					$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($query->row['manufacturer_id']);
				}

				// Location
				if ($query->row['zone_id'] || $query->row['country_id']) {
					$this->load->model('localisation/zone');
					$this->load->model('localisation/country');
					$location_zone = $this->model_localisation_zone->getZone($query->row['zone_id']);
					$location_country = $this->model_localisation_country->getCountry($query->row['country_id']);
				}

				// Filters and Condition
				$product_filters = $this->getProductFilters($query->row['product_id']);

				$this->load->model('catalog/filter');

				$condition_filter_ids = $this->model_catalog_filter->getFilterIdsByFilterGroupId($this->config->get('config_filter_group_condition_id'));
				$condition_filter_id = current(array_intersect($product_filters, $condition_filter_ids));

				if ($condition_filter_id) {
					$this->load->model('catalog/filter');
					$product_condition = $this->model_catalog_filter->getFilterByFilterId($condition_filter_id);
				}

				$product_data = array(
					'product_id'       => $query->row['product_id'],
					'keyword'		   => $query->row['keyword'],
					'name'             => $query->row['name'],
					'description'      => $query->row['description'],
					'meta_description' => $query->row['meta_description'],
					'meta_keyword'     => $query->row['meta_keyword'],
					'tag'              => $query->row['tag'],
					'model'            => $query->row['model'],
					'size'             => $query->row['size'],
					'sku'              => $query->row['sku'],
					'upc'              => $query->row['upc'],
					'ean'              => $query->row['ean'],
					'jan'              => $query->row['jan'],
					'isbn'             => $query->row['isbn'],
					'mpn'              => $query->row['mpn'],
					'shipping'         => $query->row['shipping'],
					'location'         => $query->row['location'],
					'zone_id'          => $query->row['zone_id'],
					'zone'			   => $location_zone ? $location_zone['name'] : '',
					'country_id'       => $query->row['country_id'],
					'country'		   => $location_country ? $location_country['iso_code_3'] : '',
					'quantity'         => $query->row['quantity'],
					'type_id'          => $query->row['quantity'] > 0 ? 1 : ($query->row['quantity'] == 0 ? 0 : -1),
					'stock_status'     => $query->row['stock_status'],
					'stock_status_id'  => $query->row['stock_status_id'],
					'image'            => $query->row['image'],
					'images'		   => $this->getProductImages($query->row['product_id']),
					'member_id' 	   => $query->row['member_id'],
					'customer_id'      => $query->row['customer_id'],
					'member'           => $query->row['member'],
					'member_info'	   => $member_info,
					'member_image'     => $query->row['member_image'],
					'member_group_id'  => $query->row['member_group_id'],
					'manufacturer_id'  => $query->row['manufacturer_id'],
					'manufacturer_info' => $manufacturer_info,
					'manufacturer'	   => $query->row['manufacturer'],
					'manufacturer_image' => $query->row['manufacturer_image'],
					'categories'	   => $product_categories,
					'category_ids'	   => array_map(function ($item) { return $item['category_id']; }, $product_categories),
					'path'			   => array_reduce($product_categories, function ($carry, $item) { return ($carry ? $carry . '_' . $item['category_id'] : $item['category_id']); }),
					'filters'		   => $product_filters,
					'condition_filter_id' => $condition_filter_id,
					'condition'		   => $product_condition ? $product_condition['name'] : '',
					'price'            => $query->row['discount'] ? $query->row['discount'] : $query->row['price'],
					'special'          => $query->row['special'],
					'discounts'		   => $this->getProductDiscounts($query->row['product_id']),
					'featured'         => in_array($query->row['product_id'], explode(',', $this->config->get('featured_product'))),
					'options'		   => $this->getProductOptions($query->row['product_id']),
					'attributes'	   => $this->getProductAttributes($query->row['product_id']),
					'reward'           => $query->row['reward'],
					'points'           => $query->row['points'],
					'tax_class_id'     => $query->row['tax_class_id'],
					'date_available'   => $query->row['date_available'],
					'date_expiration'  => $query->row['date_expiration'],
					'year'             => $query->row['year'],
					'weight'           => $query->row['weight'],
					'weight_class_id'  => $query->row['weight_class_id'],
					'length'           => $query->row['length'],
					'width'            => $query->row['width'],
					'height'           => $query->row['height'],
					'length_class_id'  => $query->row['length_class_id'],
					'subtract'         => $query->row['subtract'],
					'questions'        => $query->row['questions'] ?: 0,
					'minimum'          => $query->row['minimum'] ?: 1,
					'sort_order'       => $query->row['sort_order'],
					'status'           => $query->row['status'],
					'date_added'       => $query->row['date_added'],
					'date_modified'    => $query->row['date_modified'],
					'viewed'           => $query->row['viewed']
				);
			} else {
				$product_data = array();
			}

			if (!$preview) {
				$this->cache->set('product_' . (int)$product_id . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id, $product_data);
			}
		}

		return $product_data;
	}

	private function getProductShort($product_id) {
		if (empty($product_id)) return array();

		$product_data = array();
		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$sql = "
			SELECT p.product_id
		";

		$this->generateGetProductsPrices($sql, $customer_group_id);

		$sql .= "
			, p.image
			, pm.customer_id
			, p.price
			, p.model
			, p.size
			, p.year
			, p.quantity
			, p.location
			, p.zone_id
			, p.country_id
			, p.tax_class_id
			, p.sort_order
			, p.date_added
			, pd.name AS name
			, pd.description
			, m.manufacturer_id
			, m.name AS manufacturer
			, pm.member_account_id AS member_id
			, GROUP_CONCAT(cp.path_id ORDER BY cp.level SEPARATOR '_') AS path
			FROM " . DB_PREFIX . "product p
			INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
				AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
			LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
			LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (p2c.category_id = c2s.category_id)
				AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			LEFT JOIN " . DB_PREFIX . "category_path cp ON (p2c.category_id = cp.category_id)
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			WHERE p.product_id = '" . (int)$product_id . "'
			AND p.status = '1'
			AND p.member_approved = '1'
			AND p.date_available <= NOW()
			AND p.date_expiration >= NOW()
			GROUP BY cp.category_id
			ORDER BY path DESC
			LIMIT 1
		";

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			$product_data = array(
				'product_id'       => $query->row['product_id'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'image'            => $query->row['image'],
				'model'            => $query->row['model'],
				'size'             => $query->row['size'],
				'year'             => $query->row['year'],
				'quantity'         => $query->row['quantity'],
				'type_id'          => $query->row['quantity'] > 0 ? 1 : ($query->row['quantity'] == 0 ? 0 : -1),
				'customer_id'      => $query->row['customer_id'],
				'member_id' 	   => $query->row['member_id'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer'     => $query->row['manufacturer'],
				'path'			   => $query->row['path'],
				'location'		   => $query->row['location'],
				'zone_id'          => $query->row['zone_id'],
				'country_id'       => $query->row['country_id'],
				'sort_order'       => $query->row['sort_order'],
				'date_added'       => $query->row['date_added'],
				'featured'         => in_array($query->row['product_id'], explode(',', $this->config->get('featured_product')))
			);
		}

		return $product_data;
	}

	public function getProductRetired($product_id) {
		if (empty($product_id)) return false;

		$sql = "
			SELECT DISTINCT p.product_id
			, p.keyword
			, p.image
			, pm.customer_id AS member_customer_id
			, p.price, p.model
			, p.size, p.year
			, p.sort_order
			, p.date_added
			, p.date_modified
			, p.date_retired
			, p.quantity
			, p.tax_class_id
			, pd.name AS name
			, pd.description
			, m.manufacturer_id
			, m.name AS manufacturer
			, m.image AS manufacturer_image
			, cma.member_account_id AS member_id
			, cma.member_account_name AS member
			, cma.member_account_image AS member_image
			FROM " . DB_PREFIX . "product_retired p
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
				AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
			LEFT JOIN " . DB_PREFIX . "customer_member_account cma ON (pm.member_account_id = cma.member_account_id)
			WHERE p.product_id = '" . (int)$product_id . "'
			AND p.member_approved = '1'
			AND p.date_retired <= NOW()
		";

		$query = $this->db->query($sql);

		if ($query->num_rows) {
			// Categories
			$product_categories = $this->getProductCategories($query->row['product_id']);

			// Member Info
			if ($query->row['member_id']) {
				$this->load->model('catalog/member');
				$member_info = $this->model_catalog_member->getMember($query->row['member_id']);
			} else {
				$member_info = array();
			}

			// Manufacturer Info
			if ($query->row['manufacturer_id']) {
				$this->load->model('catalog/manufacturer');
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($query->row['manufacturer_id']);
			} else {
				$manufacturer_info = array();
			}

			return array(
				'product_id'       => $query->row['product_id'],
				'customer_id' 	   => $query->row['member_customer_id'],
				'member_id'        => $query->row['member_id'],
				'member'      	   => $query->row['member'],
				'member_info'	   => $member_info,
				'member_image'     => $query->row['member_image'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'quantity'         => $query->row['quantity'],
				'image'            => $query->row['image'],
				'manufacturer_id'  => $query->row['manufacturer_id'],
				'manufacturer_info' => $manufacturer_info,
				'manufacturer'     => $query->row['manufacturer'],
				'manufacturer_image'        => $query->row['manufacturer_image'],
				'categories'	   => $product_categories,
				'category_ids'	   => array_map(function ($item) { return $item['category_id']; }, $product_categories),
				'path'			   => array_reduce($product_categories, function ($carry, $item) { return ($carry ? $carry . '_' . $item['category_id'] : $item['category_id']); }),
				'model'            => $query->row['model'],
				'size'             => $query->row['size'],
				'year'             => $query->row['year'],
				'sort_order'       => $query->row['sort_order'],
				'date_added'       => $query->row['date_added'],
				'price'            => $query->row['price'],
				'tax_class_id'     => $query->row['tax_class_id']
			);
		} else {
			return false;
		}
	}

	public function getProducts($data = array(), $cache_results = true) {
		$search_log = array();
		$cache = md5(http_build_query($data));
		$random = (isset($data['sort']) && $data['sort'] == 'random') ? true : false;
		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$product_data = $cache_results ? $this->cache->get('product.products.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache) : false;

		if ($product_data === false || $random) {
			$product_data = array();

			$sql = "
				SELECT p.product_id
			";

			if (isset($data['sort']) && $data['sort'] == 'p.price') {
				$this->generateGetProductsPrices($sql, $customer_group_id);
			}

			$this->generateGetProductsJoins($sql, $data);
			$this->generateGetProductsConditions($sql, $data, $search_log);

			$sql .= "
				GROUP BY p.product_id
			";

			$this->generateGetProductsSortOrderSql($sql, $data);

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = $this->config->get('config_catalog_limit') ? $this->config->get('config_catalog_limit') : 30;
				}

				$sql .= "
					LIMIT " . (int)$data['start'] . "," . (int)$data['limit'] . "
				";
			}

			$query = $this->db->query($sql);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProductShort($result['product_id']);
			}

			if ($cache_results && !$random) {
				$this->cache->set('product.products.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache, $product_data);
			}
		}

		// search logging
		if (isset($data['is_search']) && $data['is_search'] === true && $search_log) {
			$search_log[] = 'SEARCH RESULTS: ' . count($product_data);
			$search_log[] = 'SEARCH PARAMS: ' . json_encode(array_filter($data, function ($item) { return $item !== "" && $item !== []; }));

			if ($search_log && ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl')))) {
				$search_log_file = new Log('search.log');
				$search_log_file->write(array_reduce($search_log, function ($carry, $item) { return $carry . $item . ' | '; }, ''));
				// $search_log_file->write(preg_replace('/[^\S\n]/', ' ', $sql));
			}
		}

		return $product_data;
	}

	public function getProductsIndexes($data, $cache_results = true) {
		if (empty($data)) return array();

		unset($data['start']);
		unset($data['limit']);

		if (!isset($data['sort'])) {
			$data['sort'] = $this->config->get('apac_products_sort_default');
		}

		if (!isset($data['order'])) {
			$data['order'] = (($data['sort'] == 'p.date_added') ? 'DESC' : 'ASC');
		}

		if (!isset($data['filter_sub_category'])) {
			$data['filter_sub_category'] = true;
		}

		$cache = md5(http_build_query($data));

		$product_indexes = !$cache_results ? false : $this->cache->get('product.indexes.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($product_indexes === false) {
			$product_indexes = array();

			$sql = "
				SELECT p.product_id
			";

			if ($data['sort'] == 'p.price') {
				$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');
				$this->generateGetProductsPrices($sql, $customer_group_id);
			}

			$sql .= "
				, p.quantity
				, p.manufacturer_id
				, p.location
				, p.zone_id
				, p.country_id
				, p.shipping
				, p.year
				, pm.customer_id AS member_customer_id
				, pm.member_account_id
				, pd.name
				, cma.member_group_id
				, cmg.customer_group_id
				, GROUP_CONCAT(DISTINCT p2c.category_id) AS category_ids
				, GROUP_CONCAT(DISTINCT pf.filter_id) AS filter_ids
				FROM " . DB_PREFIX . "category_path cp
				LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)
				LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id)
				LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				  	AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
				LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
				LEFT JOIN " . DB_PREFIX . "customer_member_account cma ON (pm.member_account_id = cma.member_account_id)
				LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (cma.member_group_id = cmg.member_group_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
					AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			";
			// , (SELECT keyword
			// 	FROM " . DB_PREFIX . "url_alias
			// 	WHERE SUBSTRING_INDEX(query, 'product_id=', -1) = p.product_id) AS keyword

			$this->generateGetProductsConditions($sql, $data);

			$sql .= "
				GROUP BY p.product_id
			";

			$this->generateGetProductsSortOrderSql($sql, $data);

			$query = $this->db->query($sql);

			// $this->log->write(json_encode($data) . "\r\n" . "num_rows: {$query->num_rows}" . "\r\n" . $sql);

			foreach ($query->rows as $result) {
				$is_featured = in_array($result['product_id'], explode(',', $this->config->get('featured_product')));

				// if (!empty($data['filter_featured'])) {
				// 	if (!$is_featured) continue;
				// }

				$product_indexes[] = array(
					'product_id'       => $result['product_id'],
					'name'             => $result['name'],
					// 'keyword'		   => $result['keyword'],
					'shipping'         => $result['shipping'],
					'location'         => $result['location'],
					'year'             => $result['year'],
					'zone_id'          => $result['zone_id'],
					'country_id'       => $result['country_id'],
					'quantity'         => $result['quantity'],
					'type_id'          => $result['quantity'] > 0 ? 1 : ($result['quantity'] == 0 ? 0 : -1),
					'customer_id'      => $result['member_customer_id'],
					'member_id' 	   => $result['member_account_id'],
					'member_group_id'  => $result['member_group_id'],
					'customer_group_id' => $result['customer_group_id'],
					'manufacturer_id'  => $result['manufacturer_id'],
					'category_ids'	   => explode(',', $result['category_ids']),
					'filter_ids'	   => explode(',', $result['filter_ids']),
					'featured'         => $is_featured
				);
			}

			if ($cache_results) {
				$this->cache->set('product.indexes.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $product_indexes);
			}
		}

		return $product_indexes;
	}

	public function getTotalProducts($data = array()) {
		$cache = md5(http_build_query($data));

		$product_total = $this->cache->get('product.total.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($product_total === false) {
			$sql = "
				SELECT COUNT(DISTINCT p.product_id) AS total
			";

			$this->generateGetProductsJoins($sql, $data);
			$this->generateGetProductsConditions($sql, $data);

			$query = $this->db->query($sql);

			$product_total = $query->row['total'];

			$this->cache->set('product.total.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $product_total);
		}

		return $product_total;
	}

	private function generateGetProductsPrices(&$sql, $customer_group_id) {
		$sql .= "
			, (SELECT price
				FROM " . DB_PREFIX . "product_discount pd2
				WHERE pd2.product_id = p.product_id
				AND p.quantity >= 0
				AND pd2.customer_group_id = '" . (int)$customer_group_id . "'
				AND pd2.quantity = '1'
				AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW()))
				ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount
			, (SELECT price
				FROM " . DB_PREFIX . "product_special ps
				WHERE ps.product_id = p.product_id
				AND p.quantity >= 0
				AND ps.customer_group_id = '" . (int)$customer_group_id . "'
				AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
				ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special
		";
	}

	private function generateGetProductsJoins(&$sql, $data) {
		if (!empty($data['filter_category_id']) || !empty($data['filter_name'])) {
			if (!empty($data['filter_sub_category']) || !empty($data['filter_name'])) {
				$sql .= "
					FROM " . DB_PREFIX . "category_path cp
					LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (cp.category_id = p2c.category_id)
				";
			} else {
				$sql .= "
					FROM " . DB_PREFIX . "product_to_category p2c
				";
			}

			if (!empty($data['filter_filter'])) {
				$sql .= "
					LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p2c.product_id = pf.product_id)
					LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)
				";
			} else {
				$sql .= "
					LEFT JOIN " . DB_PREFIX . "product p ON (p2c.product_id = p.product_id)
				";
			}
		} else if (!empty($data['filter_filter'])) {
			$sql .= "
				FROM " . DB_PREFIX . "product_filter pf
				LEFT JOIN " . DB_PREFIX . "product p ON (pf.product_id = p.product_id)
			";
		} else {
			$sql .= "
				FROM " . DB_PREFIX . "product p
			";
		}

		$sql .= "
			LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
				AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		";

		if (!empty($data['filter_member_group']) || !empty($data['filter_name'])) {
			$sql .= "
				LEFT JOIN " . DB_PREFIX . "customer_member_account cma ON (pm.member_account_id = cma.member_account_id)
			";

			if (!empty($data['filter_member_group'])) {
				$sql .= "
					LEFT JOIN " . DB_PREFIX . "customer_member_group cmg ON (cma.member_group_id = cmg.member_group_id)
				";
			}

			if (!empty($data['filter_name'])) {
				$sql .= "
					LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
				";
			}
		}

		$sql .= "
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
			  	AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
		";
	}

	private function generateGetProductsConditions(&$sql, $data, &$search_log = null) {
		$sql .= "
			WHERE p.status = '1'
			AND p.member_approved = '1'
			AND p.date_available <= NOW()
			AND p.date_expiration >= NOW()
		";

		if (!empty($data['filter_category_id'])) {
			if (!empty($data['filter_sub_category'])) {
				$sql .= "
					AND cp.path_id = '" . (int)$data['filter_category_id'] . "'
				";
			} else {
				$sql .= "
					AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'
				";
			}
		}

		if (!empty($data['filter_filter']) && !is_array($data['filter_filter'])) {
			$sql .= $this->generateFilterSql($data['filter_filter']);
		}

		if (!empty($data['filter_listings']) && !is_array($data['filter_listings'])) {
			$implode = array();
			$filter_listings = explode(',', $data['filter_listings']);

			foreach ($filter_listings as $listing_id) {
				$implode[] = (int)$listing_id;
			}

			$sql .= "
				AND p.product_id NOT IN (" . implode(',', $implode) . ")
			";
		}

		// search
		if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
			$keywords = array();
			$keywords_quoted = array();
			$filter_phrase = '';
			$filter_tag = '';
			$next_logical_operator = '';
			$expand_search = (isset($data['expand_search']) && $data['expand_search'] === true);
			$search_special_keywords = !$expand_search;

			// cleanup search keywords and phrase
			if (!empty($data['filter_name']) && !is_array($data['filter_name'])) {
				$filter_phrase =  utf8_strtolower(strip_tags_decode($data['filter_name'])); // utf8_strtolower(htmlspecialchars_decode($data['filter_name'], ENT_NOQUOTES));

				if ($filter_phrase) {
					if ($search_log !== null) $search_log[] = "SEARCH PHRASE: `{$filter_phrase}`";

					// check for double quote phrases to treat as single keyword
					if (preg_match('/"([^"]+)"/', htmlspecialchars_decode($filter_phrase, ENT_QUOTES), $matches, PREG_OFFSET_CAPTURE)) {
						// add a keyword for each double-quoted phrase found
						foreach ($matches as $match) {
							$double_quote_keyword = $match[0];

							if (!in_array($double_quote_keyword, $keywords_quoted)) {
								$keywords_quoted[] = $double_quote_keyword;

								if ($search_log !== null) $search_log[] = "SEARCH QUOTED: \"{$double_quote_keyword}\"";
							}
						}

						// strip out all the double-quoted keywords and double quotes
						$filter_phrase = trim(str_replace(array_merge($keywords_quoted, array('&quot;', '"')), '', $filter_phrase));
						// !important: $filter_phrase could end up empty string here
					} else if ($expand_search) {
						// add a default full phrase check with double quotes
						$keywords_quoted[] = '"' . $filter_phrase . '"';
					}

					$keywords = $keywords_quoted;

					if ($filter_phrase) {
						// add a keyword (without double quotes) for the entire search phrase
						$keywords = array_merge($keywords, array($filter_phrase));

						// contains space
						if (strpos($filter_phrase, ' ') !== false) {
							// add a keyword for each word in the search phrase
							$keywords = array_merge($keywords, explode(' ', $filter_phrase));
						}
					}

					if ($search_log !== null && $keywords) $search_log[] = "SEARCH FOR KEYWORD(S): " . json_encode($keywords) . "";
				}
			}

			// tag (should be single word)
			if (!empty($data['filter_tag']) && !is_array($data['filter_tag'])) {
				$filter_tag = utf8_strtolower(strip_non_alphanumeric($data['filter_tag'], true));

				if ($search_log !== null && $filter_tag) $search_log[] = "SEARCH TAG: `{$filter_tag}`";
			}

			// include tag selected with search
			if ($filter_tag && $keywords) {
				$sql .= "
					AND LCASE(pd.tag) LIKE '%" . $this->db->escape($filter_tag) . "%'
				";
			}

			// start large `AND` grouping for filter_name and filter_tag
			if ($keywords || $filter_tag) {
				$sql .= "
					AND (
				";
			}

			// tag without search
			if ($filter_tag && !$keywords && !$filter_phrase) {
				$sql .= "
					LCASE(pd.tag) LIKE '%" . $this->db->escape($filter_tag) . "%'
				";
			}

			if ($keywords) {
				$implode_categories = $implode_members = $implode_manufacturers = $implode_title = $implode_model = $implode_location = array();
				$keyword_special = false; // at least one category, member and/or brand keyword found
				$keyword_non_special = false; // at least one non-category, non-member, non-brand keyword exists
				$keyword_brand_and_profile = false; // at least one keyword matches both a brand and a profile

				if ($search_special_keywords) {
					$this->load->model('catalog/category');
					$this->load->model('catalog/manufacturer');
					$this->load->model('catalog/member');

					$category_names = $this->model_catalog_category->getCategoriesNames();
					$manufacturer_names = $this->model_catalog_manufacturer->getManufacturersNames();
					$member_names = $this->model_catalog_member->getMembersNames();
				}

				foreach ($keywords as $keyword) {
					$keyword_singular = (utf8_substr($keyword, -1) == 's' && utf8_substr($keyword, -3) != 'ies')
						? utf8_substr($keyword, 0, -1)
						: $keyword;

					if ($search_special_keywords) {
						if (isset($category_names[$keyword_singular])) {
							$implode_categories[] = "
								cp.category_id IN (" . implode(',', $category_names[$keyword_singular]) . ")
							";

							$keyword_special = true;

							if ($search_log !== null) $search_log[] = "SEARCH CATEGORY KEYWORD FOUND: `{$keyword_singular}`";
							continue; // strip out keyword
						}

						if (isset($member_names[$keyword_singular]) && isset($manufacturer_names[$keyword_singular])) {
							$implode_manufacturers[] = "
								m.manufacturer_id IN (" . implode(',', $manufacturer_names[$keyword_singular]) . ")
							";

							$implode_members[] = "
								pm.member_account_id IN (" . implode(',', $member_names[$keyword_singular]) . ")
							";

							$keyword_special = true;
							$keyword_brand_and_profile = true;

							if ($search_log !== null) $search_log[] = "SEARCH BRAND AND PROFILE KEYWORD FOUND: `{$keyword_singular}`";
							continue; // strip out keyword
						}

						if (isset($manufacturer_names[$keyword_singular])) {
							$implode_manufacturers[] = "
								m.manufacturer_id IN (" . implode(',', $manufacturer_names[$keyword_singular]) . ")
							";

							$keyword_special = true;

							if ($search_log !== null) $search_log[] = "SEARCH BRAND KEYWORD FOUND: `{$keyword_singular}`";
							continue; // strip out keyword
						}

						if (isset($member_names[$keyword_singular])) {
							$implode_members[] = "
								pm.member_account_id IN (" . implode(',', $member_names[$keyword_singular]) . ")
							";

							$keyword_special = true;

							if ($search_log !== null) $search_log[] = "SEARCH PROFILE KEYWORD FOUND: `{$keyword_singular}`";
							continue; // strip out keyword
						}
					}

					if (strpos($filter_phrase, ' ') === false || $keyword != $filter_phrase) {
						$implode_title[] = "
							LCASE(pd.name) LIKE '%" . $this->db->escape($keyword_singular) . "%'
						";

						$implode_model[] = "
							LCASE(p.model) LIKE '%" . $this->db->escape($keyword_singular) . "%'
						";

						$implode_location[] = "
							LCASE(p.location) LIKE '%" . $this->db->escape($keyword_singular) . "%'
						";

						if ($search_log !== null) $search_log[] = "SEARCH KEYWORD NON-SPECIAL: `{$keyword_singular}`";
						$keyword_non_special = true;
					}
				}

				if ($search_special_keywords) {
					// and a keyword is part of a category name
					if ($implode_categories) {
						$sql .= "
							(" . implode(" AND ", $implode_categories) . ")
						";

						$next_logical_operator = 'AND';
					}

					if ($keyword_brand_and_profile) {
						$sql .= "
							{$next_logical_operator} (
								(" . implode(" AND ", $implode_manufacturers) . ")
								OR (" . implode(" AND ", $implode_members) . ")
							)
						";

						$next_logical_operator = 'AND';
					} else {
						if ($implode_manufacturers) {
							// and a non-category, non-member keyword is part of a manufacturer/brand name
							$sql .= "
								{$next_logical_operator} (" . implode(" AND ", $implode_manufacturers) . ")
							";

							$next_logical_operator = 'AND';
						}

						if ($implode_members) {
							// and a non-category keyword is part of a member/profile name
							$sql .= "
								{$next_logical_operator} (" . implode(" AND ", $implode_members) . ")
							";

							$next_logical_operator = 'AND';
						}
					}
				}

				// and a non-category, non-member keyword, non-manufacturer is a part of a product name, model, or location
				if ($keyword_non_special) {
					$sql .= "
						{$next_logical_operator} (
							" . implode(" OR ", $implode_title) . "
							OR " . implode(" OR ", $implode_model) . "
							OR " . implode(" OR ", $implode_location) . "
						)
					";
				}

				$next_logical_operator = $keyword_special || $keyword_non_special ? 'OR' : '';
			}

			if ($filter_phrase && !$keyword_special) {
				$filter_phrase_concat = str_replace(' ', '', $filter_phrase);

				// if no category, profile, or brand keyword found, then check keyword for partial match on profile and brand names
				$sql .= "
					{$next_logical_operator} REPLACE(LCASE(cma.member_account_name), ' ', '') LIKE '%" . $this->db->escape($filter_phrase_concat) . "%'
					OR REPLACE(LCASE(m.name), ' ', '') LIKE '%" . $this->db->escape($filter_phrase_concat) . "%'
				";
			}

			if ($filter_phrase && !empty($data['filter_description'])) {
				$sql .= "
					OR LCASE(pd.description) LIKE '%" . $this->db->escape($filter_phrase) . "%'
				";
			}

			// only check part numbers if single keyword search
			if ($filter_phrase && strpos($filter_phrase, ' ') === false && !$keyword_special && !$keyword_non_special) {
				$sql .= "
					OR LCASE(p.sku) = '" . $this->db->escape($filter_phrase) . "'
				";

				$sql .= "
					OR LCASE(p.upc) = '" . $this->db->escape($filter_phrase) . "'
				";

				$sql .= "
					OR LCASE(p.ean) = '" . $this->db->escape($filter_phrase) . "'
				";

				$sql .= "
					OR LCASE(p.jan) = '" . $this->db->escape($filter_phrase) . "'
				";

				// $sql .= "
				// 	OR LCASE(p.isbn) = '" . $this->db->escape($filter_phrase) . "'
				// ";

				$sql .= "
					OR LCASE(p.mpn) = '" . $this->db->escape($filter_phrase) . "'
				";
			}

			// end large `AND` grouping for filter_name and filter_tag
			if ($keywords || $filter_tag) {
				$sql .= "
					)
				";
			}
		}

		if (!empty($data['filter_manufacturer_id'])) {
			$sql .= "
				AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'
			";
		}

		if (!empty($data['filter_member_account_id'])) {
			$sql .= "
				AND pm.member_account_id = '" . (int)$data['filter_member_account_id'] . "'
			";
		} else if (isset($data['filter_member_account_id']) && is_null($data['filter_member_account_id'])) {
			$sql .= "
				AND pm.member_account_id IS NULL
			"; // none; AND p.member_customer_id < '0'
		} else if (isset($data['filter_member_exists'])) {
			$sql .= "
				AND pm.member_account_id IS NOT NULL
			"; // AND p.member_customer_id > '0'
		}

		if (!empty($data['filter_country_id'])) {
			$sql .= "
				AND p.country_id = '" . (int)$data['filter_country_id'] . "'
			";
		}

		if (!empty($data['filter_zone_id'])) {
			$sql .= "
				AND p.zone_id = '" . (int)$data['filter_zone_id'] . "'
			";
		}

		if (!empty($data['filter_location']) && !is_array($data['filter_location'])) {
			$filter_location = utf8_strtolower(strip_non_alphanumeric($data['filter_location']));

			$sql .= "
				AND LCASE(p.location) LIKE '%" . $this->db->escape($filter_location) . "%'
			";
		}

		if (!empty($data['filter_listing_type']) && is_array($data['filter_listing_type'])) {
			$classified = in_array('0', $data['filter_listing_type']);
			$buy_now = in_array('1', $data['filter_listing_type']);
			$shared = in_array('-1', $data['filter_listing_type']);

			if ($classified && $buy_now && $shared) {
				$sql .= "";
			} else if ($classified && $buy_now) {
				$sql .= "
					AND (p.quantity >= 0)
				";
			} else if ($classified && $shared) {
				$sql .= "
					AND (p.quantity <= 0)
				";
			} else if ($shared && $buy_now) {
				$sql .= "
					AND (p.quantity != 0)
				";
			} else if ($classified) {
				$sql .= "
					AND (p.quantity = 0)
				";
			} else if ($buy_now) {
				$sql .= "
					AND (p.quantity > 0)
				";
			} else if ($shared) {
				$sql .= "
					AND (p.quantity < 0)
				";
			}
		}

		if (!empty($data['filter_member_group']) && is_array($data['filter_member_group'])) {
			$sql .= "
				AND cmg.customer_group_id IN (" . implode(',', $data['filter_member_group']) . ")
			";
		}
	}

	private function generateGetProductsSortOrderSql(&$sql, $data) {
		$sort_data = array(
			'pd.name',
			'p.model',
			'p.quantity',
			'p.price',
			'p.sort_order',
			'p.date_added',
			'random'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			switch ($data['sort']) {
				case 'p.sort_order':
					// to-do: sort order interger assignment algorithm on Listings
					$sql .= "
						ORDER BY YEAR(p.date_added) DESC
						, IF(pm.member_account_id IS NULL, 0, 1) DESC
						, IF(pm.customer_id = 0, 0, 1) DESC
						, IF(p.quantity = -1, 0, 1) DESC
						, IF(p.year = 0000, 0, 1) DESC
						, p.date_added DESC
					";
					break;
				case 'pd.name':
				case 'p.model':
					$sql .= "
						ORDER BY LCASE(" . $data['sort'] . ")
					";
					break;
				case 'p.price':
					$sql .= "
						ORDER BY IF(p.quantity = -1, 0, 1) DESC
						, (
							CASE
								WHEN special IS NOT NULL THEN special
								WHEN discount IS NOT NULL THEN discount
								ELSE p.price
							END
						)
					";
					break;
				case 'random':
					$sql .= "
						ORDER BY Rand()
					";
					break;
				default:
					$sql .= "
						ORDER BY " . $data['sort'] . "
					";
					break;
			}
		} else {
			$sql .= "
				ORDER BY p.sort_order
			";
		}

		if (!isset($data['sort']) || ($data['sort'] != 'p.sort_order')) {
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= "
					DESC, LCASE(pd.name) DESC
				";
			} else {
				$sql .= "
					ASC, LCASE(pd.name) ASC
				";
			}
		}
	}

	public function getProductFeatured($data = array(), $cache_results = true) {
		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$cache = md5(http_build_query($data));

		$product_data = !$cache_results ? false : $this->cache->get('product.featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache);

		if ($product_data === false) {
			$product_data = array();
			$featured_dne = array();
			$filter_ids = array();
			$product_ids = array();
			$filter_listing_ids = array();
			$filter_category_id = 0;
			$classified = $buy_now = $shared = false;

			$products = explode(',', $this->config->get('featured_product'));

			if (!empty($data['filter_listings']) && !is_array($data['filter_listings'])) {
				$filter_listings = explode(',', $data['filter_listings']);

				foreach ($filter_listings as $filter_listing_id) {
					$filter_listing_ids[] = (int)$filter_listing_id;
				}
			}

			if (!empty($data['filter_listing_type']) && is_array($data['filter_listing_type'])) {
				$classified = in_array('0', $data['filter_listing_type']);
				$buy_now = in_array('1', $data['filter_listing_type']);
				$shared = in_array('-1', $data['filter_listing_type']);
			}

			if (!empty($data['filter_filter']) && !is_array($data['filter_filter'])) {
				$filters = explode(',', $data['filter_filter']);

				foreach ($filters as $filter_id) {
					$filter_ids[] = (int)$filter_id;
				}

				$product_ids = $this->getProductsByFilters($filter_ids);
			}

			foreach ($products as $product_id) {
				$result = $this->getProductShort($product_id);

				if ($result) {
					if ((!empty($data['filter_location']) && utf8_strpos(utf8_strtolower($result['location']), utf8_strtolower($data['filter_location'])) === false)
						|| (!empty($data['filter_country_id']) && $result['country_id'] != $data['filter_country_id'])
						|| (!empty($data['filter_zone_id']) && $result['zone_id'] != $data['filter_zone_id'])
						|| (!empty($data['filter_manufacturer_id']) && $result['manufacturer_id'] != $data['filter_manufacturer_id'])
						|| (!empty($data['filter_name']) && utf8_strpos(utf8_strtolower($result['name']), utf8_strtolower($data['filter_name'])) === false)
						|| (!empty($data['filter_filter']) && !in_array($product_id, $product_ids))
						|| (!empty($data['filter_listings']) && in_array($product_id, $filter_listing_ids))) {
						continue;
					}

					if (!empty($data['filter_listing_type'])) {
						if (($classified && $buy_now && $result['quantity'] < 0)
							|| ($classified && $shared && $result['quantity'] > 0)
							|| ($shared && $buy_now && $result['quantity'] == 0)
							|| ($classified && $result['quantity'] != 0)
							|| ($buy_now && $result['quantity'] <= 0)
							|| ($shared && $result['quantity'] >= 0)) {
							continue;
						}
					}

					if (!empty($data['filter_category_id']) && !in_array((int)$data['filter_category_id'], explode('_', $result['path']))) {
						continue;
					}

					// $this->getProductFilters($product_id);
					// $this->checkProductHasFilterInFilterGroup($product_id, $filter_group_id = 0, $filters = array())


					$product_data[$product_id] = $result;
				} else {
					$featured_dne[] = $product_id; // does not exist (need to remove)
				}
			}

			if ($featured_dne) $this->log->write('Featured DNE: ' . implode(', ', $featured_dne));

			// sort
			$sort_order = array();

			$sort_data = array(
				'name',
				'model',
				'price',
				'sort_order',
				'date_added',
				'random'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				if ($data['sort'] == 'random') {
					shuffle($product_data);
				} else {
					if (in_array($data['sort'], $sort_data)) {
						if ($data['sort'] == 'name' || $data['sort'] == 'model') {
							foreach ($product_data as $key => $value) {
								$sort_order[$key] = utf8_strtolower($value[$data['sort']]);
							}
						} elseif ($data['sort'] == 'price') {
							foreach ($product_data as $key => $value) {
								if ($value['special']) {
									$sort_order[$key] = $value['special'];
								} else {
									$sort_order[$key] = $value['price'];
								}
							}
						} else {
							foreach ($product_data as $key => $value) {
								$sort_order[$key] = $value[$data['sort']];
							}
						}
					} else {
						foreach ($product_data as $key => $value) {
							$sort_order[$key] = $value['sort_order'];
						}
					}

					if (isset($data['order']) && $data['order'] == 'DESC') {
						array_multisort($sort_order, SORT_DESC, $product_data);
					} else {
						array_multisort($sort_order, SORT_ASC, $product_data);
					}
				}
			}

			// limit listings based on limit and page
			if (isset($data['start']) || isset($data['limit'])) {
				$data['start'] = $data['start'] < 0 ? 0 : (int)$data['start'];
				$data['limit'] = $data['limit'] < 1 ? 20 : (int)$data['limit'];

				$product_data = array_slice($product_data, $data['start'], $data['limit'], true);
			}

			if ($cache_results) {
				$this->cache->set('product.featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache, $product_data);
			}
		}

		return $product_data;
	}

	public function getProductSpecials($data = array(), $cache_results = true) {
		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$cache = md5(http_build_query($data));

		$product_data = !$cache_results ? false : $this->cache->get('product.special.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache);

		if ($product_data === false) {
			$sql = "
				SELECT DISTINCT ps.product_id
				FROM " . DB_PREFIX . "product_special ps
				LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
				LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
				WHERE p.status = '1'
				AND p.quantity >= 0
				AND p.member_approved = '1'
				AND p.date_available <= NOW()
				AND p.date_expiration >= NOW()
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				AND ps.customer_group_id = '" . (int)$customer_group_id . "'
				AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
			";

			if (!empty($data['filter_country_id'])) {
				$sql .= "
					AND p.country_id = '" . (int)$data['filter_country_id'] . "'
				";
			}

			if (!empty($data['filter_zone_id'])) {
				$sql .= "
					AND p.zone_id = '" . (int)$data['filter_zone_id'] . "'
				";
			}

			if (!empty($data['filter_location'])) {
				$sql .= "
					AND LCASE(p.location) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_location'])) . "%'
				";
			}

			if (!empty($data['filter_listings']) && !is_array($data['filter_listings'])) {
				$implode = array();
				$filter_listings = explode(',', $data['filter_listings']);

				foreach ($filter_listings as $listing_id) {
					$implode[] = (int)$listing_id;
				}

				$sql .= "
					AND p.product_id NOT IN (" . implode(',', $implode) . ")
				";
			}

			$sql .= "
				GROUP BY ps.product_id
			";

			$sort_data = array(
				'pd.name',
				'p.model',
				'p.quantity',
				'ps.price',
				'p.sort_order',
				'p.date_added',
				'random'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				if ($data['sort'] == 'p.sort_order') {
					$sql .= "
						ORDER BY YEAR(p.date_added) DESC
						, IF(pm.member_account_id IS NULL, 0, 1) DESC
						, IF(pm.customer_id = 0, 0, 1) DESC
						, IF(p.quantity = -1, 0, 1) DESC
						, IF(p.year = 0000, 0, 1) DESC
						, p.date_added DESC
					";
				} else if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
					$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
				} else if ($data['sort'] == 'random') {
					$sql .= " ORDER BY Rand()";
				} else {
					$sql .= " ORDER BY " . $data['sort'];
				}
			} else {
				$sql .= " ORDER BY p.sort_order";
			}

			if (!isset($data['sort']) || ($data['sort'] != 'p.sort_order')) {
				if (isset($data['order']) && ($data['order'] == 'DESC')) {
					$sql .= " DESC, LCASE(pd.name) DESC";
				} else {
					$sql .= " ASC, LCASE(pd.name) ASC";
				}
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

			$product_data = array();

			$query = $this->db->query($sql);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProductShort($result['product_id']);
			}

			if ($cache_results) {
				$this->cache->set('product.special.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache, $product_data);
			}
		}

		return $product_data;
	}

	public function getProductCategoryIds($product_id) {
		if (empty($product_id)) return array();

		$product_category_data = array();

		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "product_to_category
			WHERE product_id = '" . (int)$product_id . "'
		");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductCategories($product_id) {
		if (empty($product_id)) return array();

		$sql = "
			SELECT c.category_id
			, cd.name
			, c.image
			, cp.level
			, COUNT(DISTINCT p2c.product_id) AS product_count
			, GROUP_CONCAT(cp.path_id ORDER BY cp.level SEPARATOR '_') AS path
			FROM " . DB_PREFIX . "product_to_category p2c
			INNER JOIN " . DB_PREFIX . "product_to_store p2s ON (p2c.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			LEFT JOIN " . DB_PREFIX . "category c ON (p2c.category_id = c.category_id)
			LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id)
				AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id)
				AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			LEFT JOIN " . DB_PREFIX . "category_path cp ON (p2c.category_id = cp.category_id)
			WHERE p2c.product_id = '" . (int)$product_id . "'
			AND c.status = '1'
			GROUP BY cp.category_id
			ORDER BY c.sort_order, LCASE(cd.name)
		";

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductFilters($product_id) {
		if (empty($product_id)) return array();

		$product_filter_data = array();

		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "product_filter
			WHERE product_id = '" . (int)$product_id . "'
		");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	private function generateFilterSql($filter_filter = '') {
		$filter_ids = array();

		$filters = explode(',', $filter_filter);

		foreach ($filters as $filter_id) {
			$filter_ids[] = (int)$filter_id;
		}

		$sql = "
			AND pf.filter_id IN (" . implode(',', $filter_ids) . ")
		";

		$product_ids = $this->getProductsByFilters($filter_ids);

		if ($product_ids) {
			$sql .= "
				AND p.product_id IN (" . implode(',', $product_ids) . ")
			";
		} else {
			$sql .= "
				AND 1 = 0
			"; // ensures NO results returned
		}

		return $sql;
	}

	private function getProductsByFilters($filter_ids = array()) {
		$cache = md5(http_build_query($filter_ids));

		$product_ids = $this->cache->get('product.filter.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($product_ids === false) {
			$product_ids = array();

			// get all products with at least one filter selected and with at least one filter in each filter groups selected
			$query = $this->db->query("
				SELECT id.product_id
				FROM (SELECT product_id
				  	, COUNT(products_filters_selected.product_id) AS product_filter_groups_count
					, filter_groups_selected.filter_groups_count AS selected_filter_groups_count
					FROM (SELECT pf.filter_id
						, pf.product_id
						, f1.filter_group_id
						FROM ". DB_PREFIX ."product_filter pf
						LEFT JOIN ". DB_PREFIX ."filter f1 ON pf.filter_id = f1.filter_id
						WHERE f1.filter_id IN (" . implode(',', $filter_ids) . ")
						GROUP BY pf.product_id, f1.filter_group_id) AS products_filters_selected
					  	, (SELECT COUNT(DISTINCT filter_group_id) AS filter_groups_count
							FROM ". DB_PREFIX ."filter f2
							WHERE f2.filter_id in (" . implode(',', $filter_ids) . ")) AS filter_groups_selected
					GROUP BY product_id
					HAVING product_filter_groups_count = selected_filter_groups_count) AS id
			");

			foreach ($query->rows as $result) {
				$product_ids[] = $result['product_id'];
			}

			$this->cache->set('product.filter.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $product_ids);
		}

		return $product_ids;
	}

	private function checkProductHasAttribute($product_id, $attribute_id = 0, $attribute_text = '') {
		if (empty($product_id)) return false;

		$has_attribute = false;

		$product_attributes = $this->getProductAttributes($product_id);

		foreach ($product_attributes as $product_attribute) {
			foreach ($product_attributes['attribute'] as $product_attribute_data) {
				if ($product_attribute_data['attribute_id'] == $attribute_id) {
					$has_attribute = true;
				}

				// check product attribute text value
				/*
				if ($product_attribute_data['text'] == $attribute_text) {
					$has_attribute = true;
				}
				* */
			}
		}

		return $has_attribute;
	}

	private function checkProductHasOption($product_id, $option_value_id = 0, $option_name = '') {
		if (empty($product_id)) return false;

		$has_option = false;

		$product_options = $this->getProductOptions($product_id);

		foreach ($product_options as $product_option) {
			foreach ($product_option['option_value'] as $product_option_data) {
				if ($product_option_data['option_value_id'] == $option_value_id) {
					$has_option = true;
				}

				// check product option name value
				/*
				if ($product_option_data['name'] == $option_name) {
					$has_option = true;
				}
				* */
			}
		}

		return $has_option;
	}

	private function checkProductHasFilterInFilterGroup($product_id, $filter_group_id = 0, $filters = array()) {
		if (empty($product_id)) return false;

		$has_filter = false;

		$product_filters = $this->getProductFiltersByFilterGroup($product_id, $filter_group_id);

		foreach ($product_filters as $product_filter_data) {
			if (in_array($product_filter_data['filter_id'], $filters)) {
				$has_filter = true;
			}
		}

		return $has_filter;
	}

	private function getProductFiltersByFilterGroup($product_id, $filter_group_id) {
		if (empty($product_id)) return array();

		$product_filter_data = array();

		$product_filter_data_query = $this->db->query("
			SELECT fd.filter_id
			, fd.filter_group_id
			, fd.name
			FROM " . DB_PREFIX . "product_filter pf
			LEFT JOIN " . DB_PREFIX . "filter_description fd ON (pf.filter_id = fd.filter_id)
				AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			WHERE pf.product_id = '" . (int)$product_id . "'
			AND fd.filter_group_id = '" . (int)$filter_group_id . "'
		");

		foreach ($product_filter_data_query->rows as $product_filter) {
			$product_filter_data[] = array(
				'filter_id'			=> $product_filter['filter_id'],
				'filter_name'		=> $product_filter['name']
			);
		}

		return $product_filter_data;
	}

	public function getProductAttributes($product_id) {
		if (empty($product_id)) return array();

		$product_attribute_group_data = array();

		$product_attribute_group_query = $this->db->query("
			SELECT ag.attribute_group_id
			, agd.name
			FROM " . DB_PREFIX . "product_attribute pa
			LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id)
			LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id)
			LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id)
				AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			WHERE pa.product_id = '" . (int)$product_id . "'
			GROUP BY ag.attribute_group_id
			ORDER BY ag.sort_order, agd.name
		");

		foreach ($product_attribute_group_query->rows as $product_attribute_group) {
			$product_attribute_data = array();

			$product_attribute_query = $this->db->query("
				SELECT a.attribute_id
				, ad.name
				, pa.text
				FROM " . DB_PREFIX . "product_attribute pa
				LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id)
				LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id)
					AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'
				WHERE pa.product_id = '" . (int)$product_id . "'
				AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "'
				AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "'
				ORDER BY a.sort_order, ad.name
			");

			foreach ($product_attribute_query->rows as $product_attribute) {
				$product_attribute_data[] = array(
					'attribute_id' => $product_attribute['attribute_id'],
					'name'         => $product_attribute['name'],
					'text'         => $product_attribute['text']
				);
			}

			$product_attribute_group_data[] = array(
				'attribute_group_id' => $product_attribute_group['attribute_group_id'],
				'name'               => $product_attribute_group['name'],
				'attribute'          => $product_attribute_data
			);
		}

		return $product_attribute_group_data;
	}

	public function getProductOptions($product_id) {
		if (empty($product_id)) return array();

		$product_option_data = array();

		$product_option_query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "product_option po
			LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id)
			LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id)
				AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'
			WHERE po.product_id = '" . (int)$product_id . "'
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
						AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'
					WHERE pov.product_id = '" . (int)$product_id . "'
					AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "'
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
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}

				$product_option_data[] = array(
					'product_option_id' => $product_option['product_option_id'],
					'option_id'         => $product_option['option_id'],
					'name'              => $product_option['name'],
					'type'              => $product_option['type'],
					'option_value'      => $product_option_value_data,
					'required'          => $product_option['required']
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

	public function getProductDiscounts($product_id) {
		if (empty($product_id)) return array();

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "product_discount
			WHERE product_id = '" . (int)$product_id . "'
			AND customer_group_id = '" . (int)$customer_group_id . "'
			AND quantity > 1
			AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW()))
			ORDER BY quantity ASC, priority ASC, price ASC
		");

		return $query->rows;
	}

	public function getProductImages($product_id) {
		if (empty($product_id)) return array();

		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "product_image
			WHERE product_id = '" . (int)$product_id . "'
			ORDER BY sort_order ASC
		");

		return $query->rows;
	}

	public function getProductRelated($product_id) {
		if (empty($product_id)) return array();

		$product_data = array();

		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "product_related pr
			LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			WHERE pr.product_id = '" . (int)$product_id . "'
			AND p.status = '1'
			AND p.member_approved = '1'
			AND p.date_available <= NOW()
			AND p.date_expiration >= NOW()
		");

		foreach ($query->rows as $result) {
			if (!array_key_exists($result['related_id'], $product_data)) {
				$product_data[$result['related_id']] = $this->getProductShort($result['related_id']);
			}
		}

		return $product_data;
	}

	public function getProductRelatedRandom($product_ids, $max = 10) {
		if (!$product_ids) {
			return array();
		}

		$product_related = array();

		$random_keys = array_rand($product_ids, min(count($product_ids), $max));

		if (is_array($random_keys)) {
			foreach ($random_keys as $key) {
				$product_related_id = $product_ids[$key];
				$product_related[$product_related_id] = $this->getProductShort($product_related_id);
			}
		}

		return $product_related;
	}

	public function getProductShipping($product_id) {
		if (empty($product_id)) return array();

		$product_shipping_rate_data = array();

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

		foreach ($query->rows as $result) {
			$product_shipping_rate_data[$result['geo_zone_id']] = array($result['first'], $result['additional']);
		}

		return $product_shipping_rate_data;
	}

	public function getCategories($product_id) {
		if (empty($product_id)) return array();

		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "product_to_category
			WHERE product_id = '" . (int)$product_id . "'
		");

		return $query->rows;
	}

	public function getLatestProducts($limit) {
		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$product_data = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . (int)$limit);

		if ($product_data === false) {
			$query = $this->db->query("
				SELECT p.product_id
				FROM " . DB_PREFIX . "product p
				LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				WHERE p.status = '1'
				AND p.member_approved = '1'
				AND p.date_available <= NOW()
				AND p.date_expiration >= NOW()
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				ORDER BY p.date_added DESC
				LIMIT " . (int)$limit
			);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProductShort($result['product_id']);
			}

			$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . (int)$customer_group_id . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getPopularProducts($limit) {
		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$product_data = $this->cache->get('product.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . (int)$limit);

		if ($product_data === false) {
			$query = $this->db->query("
				SELECT p.product_id
				FROM " . DB_PREFIX . "product p
				LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
					AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				WHERE p.status = '1'
				AND p.member_approved = '1'
				AND p.date_available <= NOW()
				AND p.date_expiration >= NOW()
				ORDER BY p.viewed DESC, p.date_added DESC
				LIMIT " . (int)$limit
			);

			$product_data = array();

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProductShort($result['product_id']);
			}

			$this->cache->set('product.popular.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . (int)$customer_group_id . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getBestSellerProducts($limit) {
		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$product_data = $this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . (int)$customer_group_id . '.' . (int)$limit);

		if ($product_data === false) {
			$product_data = array();

			$query = $this->db->query("
				SELECT op.product_id
				, COUNT(*) AS total
				FROM " . DB_PREFIX . "order_product op
				LEFT JOIN " . DB_PREFIX . "order o ON (op.order_id = o.order_id)
				LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id = p.product_id)
				LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
					AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				WHERE o.order_status_id > '0'
				AND p.status = '1'
				AND p.member_approved = '1'
				AND p.date_available <= NOW()
				AND p.date_expiration >= NOW()
				GROUP BY op.product_id
				ORDER BY total DESC
				LIMIT " . (int)$limit
			);

			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->getProductShort($result['product_id']);
			}

			$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . (int)$customer_group_id . '.' . (int)$limit, $product_data);
		}

		return $product_data;
	}

	public function getTotalProductFeatured($data = array()) {
		unset($data['sort']);
		unset($data['order']);
		unset($data['start']);
		unset($data['limit']);

		$results = $this->getProductFeatured($data);

		return count($results);
	}

	public function getTotalProductSpecials($data = array()) {
		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$sql = "
			SELECT COUNT(DISTINCT ps.product_id) AS total
			FROM " . DB_PREFIX . "product_special ps
			LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id)
			LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)
				AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
			WHERE p.status = '1'
			AND p.member_approved = '1'
			AND p.date_available <= NOW()
			AND p.date_expiration >= NOW()
			AND ps.customer_group_id = '" . (int)$customer_group_id . "'
			AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
		";

		if (!empty($data['filter_country_id'])) {
			$sql .= "
				AND p.country_id = '" . (int)$data['filter_country_id'] . "'
			";
		}

		if (!empty($data['filter_zone_id'])) {
			$sql .= "
				AND p.zone_id = '" . (int)$data['filter_zone_id'] . "'
			";
		}

		if (!empty($data['filter_location'])) {
			$sql .= "
				AND LCASE(p.location) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_location'])) . "%'
			";
		}

		if (!empty($data['filter_listings']) && !is_array($data['filter_listings'])) {
			$implode = array();
			$filter_listings = explode(',', $data['filter_listings']);

			foreach ($filter_listings as $listing_id) {
				$implode[] = (int)$listing_id;
			}

			$sql .= "
				AND p.product_id NOT IN (" . implode(',', $implode) . ")
			";
		}

		// $sql .= " GROUP BY ps.product_id";

		$query = $this->db->query($sql);

		return isset($query->row['total']) ? $query->row['total'] : 0;
	}

	public function getTotalProductsByDownloadId($download_id) {
		if (empty($product_id)) return 0;

		$query = $this->db->query("
			SELECT COUNT(product_id) AS total
			FROM " . DB_PREFIX . "product_to_download
			WHERE download_id = '" . (int)$download_id . "'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByMemberCustomerId($member_customer_id) {
		if (empty($member_customer_id)) return 0;

		$query = $this->db->query("
			SELECT COUNT(p.product_id) AS total
			FROM " . DB_PREFIX . "product p
			INNER JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			WHERE pm.customer_id = '" . (int)$member_customer_id . "'
			AND p.status = '1'
			AND p.member_approved = '1'
		");

		return $query->row['total'];
	}

	public function getTotalProductsByMemberAccountId($member_account_id) {
		if (empty($member_account_id)) return 0;

		$query = $this->db->query("
			SELECT COUNT(p.product_id) AS total
			FROM " . DB_PREFIX . "product p
			INNER JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			WHERE pm.member_account_id = '" . (int)$member_account_id . "'
			AND p.status = '1'
			AND p.member_approved = '1'
		");

		return $query->row['total'];
	}

	public function getMemberByProductId($product_id) {
		if (empty($product_id)) return array();

		$query = $this->db->query("
			SELECT cma.customer_id AS customer_id
				, cma.member_account_id AS member_id
				, cma.member_account_name AS member_name
				, c.email AS member_email
			FROM " . DB_PREFIX . "product p
			INNER JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
			LEFT JOIN " . DB_PREFIX . "customer_member_account cma ON (pm.member_account_id = cma.member_account_id)
			LEFT JOIN " . DB_PREFIX . "customer c ON (cma.customer_id = c.customer_id)
			WHERE p.product_id = '" . (int)$product_id . "'
			LIMIT 1
		");

		return $query->row;
	}

	public function updateViewed($product_id) {
		if (empty($product_id)) return false;

		$this->db->query("
			UPDATE " . DB_PREFIX . "product
			SET viewed = (viewed + 1)
			WHERE product_id = '" . (int)$product_id . "'
		");
	}

	public function flagProduct($product_id) {
		if (empty($product_id)) return false;

		$product_info = $this->getProduct($product_id);

		if ($product_info) {
			$this->db->query("
				UPDATE " . DB_PREFIX . "product
				SET member_approved = '0'
				WHERE product_id = '" . (int)$product_id . "'
			"); // , date_expiration = NOW()

			// Send email notification to the listing member, store admin, and customer flagging the listing
			$this->load->language('mail/product');

			// product
			$product_name = strip_tags_decode(htmlspecialchars_decode($product_info['name']));
			$product_href = $this->url->link('product/product', 'product_id=' . (int)$product_id, 'SSL');

			// member
			if ($product_info['member_id']) {
				$this->load->model('catalog/member');
				$member_name = strip_tags_decode(htmlspecialchars_decode($product_info['member']));
				$member_url = $this->url->link('product/member/info', 'member_id=' . $product_info['member_id'], 'SSL');
				$member_info = $this->model_catalog_member->getMember($product_info['member_id']);
				$member_email = $member_info['email'] ? $member_info['email'] : '';
			} else {
				$member_name = $this->language->get('text_flag_none');
				$member_url = '';
				$member_email = '';
			}

			// customer
			if ($this->customer->hasProfile()) {
				$customer_display = $this->customer->getProfileName() . ' (' . $this->customer->getProfileUrl() . ')';
			} else if ($this->customer->getId()) {
				$customer_display = $this->customer->getLastName() . ', ' . $this->customer->getFirstName();
			} else {
				$customer_display = $this->language->get('text_flag_anon');
			}

			$bcc = array();

			if ($this->config->get('member_email_customers')) {
				// copy profile owner
				if ($member_email) {
					$bcc[] = $member_email;
				}

				// copy flagging user if profile activated and email notification enabled
				if ($this->customer->hasProfile() && $this->customer->getEmailNotifySetting('email_flag') && $member_email != $this->customer->getEmail()) {
					$bcc[] = $this->customer->getEmail();
				}
			}

			$mail_sent = $this->sendEmail(array(
				'to' 		=> $this->config->get('config_email'),
				'from' 		=> $this->config->get('config_email_noreply'),
				'sender' 	=> $this->config->get('config_name'),
				'subject' 	=> sprintf(
					$this->language->get('text_flag_mail_subject'),
					$this->config->get('config_name'),
					$product_name
				),
				'message' 	=> sprintf(
					$this->language->get('text_flag_mail'),
					$product_name,
					$product_href,
					$customer_display,
					$member_name,
					$member_url
				),
				'bcc' 		=> $bcc,
				'reply' 	=> $this->config->get('config_email_noreply'),
				'admin'		=> true
			));

			$this->cache->delete('product_' . (int)$product_id);
			$this->cache->delete('manufacturer_' . (int)$product_info['manufacturer_id']);

			if (!empty($member_info)) {
				$this->cache->delete('member_' . (int)$product_info['member_id']);
			}

			$this->cache->delete('product.');
			$this->cache->delete('category');
        }
	}

}
