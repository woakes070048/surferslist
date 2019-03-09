<?php
class ModelCatalogFilter extends Model {
    private $cache_expires = 60 * 60 * 24 * 30; // 1 month cache expiration

    public function getFiltersByFilterGroupId($filter_group_id = 0) {
        $filter_data = $this->cache->get('filter.filters.' . (int)$this->config->get('config_language_id') . '.' . (int)$filter_group_id);

        if ($filter_data === false) {
            $filter_data = array();

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
                ORDER BY f.filter_group_id, f.sort_order ASC
            ";

            $query = $this->db->query($sql);

            $filter_data = $query->rows;

            $this->cache->set('filter.filters.' . (int)$this->config->get('config_language_id') . '.' . (int)$filter_group_id, $filter_data, $this->cache_expires);
        }

        return $filter_data;
    }

    public function getFilterByFilterId($filter_id = 0) {
        if (!$filter_id) {
            return array();
        }

        $filter_data = $this->cache->get('filter.filter.' . (int)$this->config->get('config_language_id') . '.' . (int)$filter_id);

        if ($filter_data === false) {
            $filter_data = array();

            $sql = "
                SELECT filter_id
                , filter_group_id
                , name
                FROM " . DB_PREFIX . "filter_description
                WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'
                AND filter_id = '" . (int)$filter_id . "'
            ";

            $query = $this->db->query($sql);

            $filter_data = $query->row;

            $this->cache->set('filter.filter.' . (int)$this->config->get('config_language_id') . '.' . (int)$filter_id, $filter_data, $this->cache_expires);
        }

        return $filter_data;
    }

    public function getFilterIdsByFilterGroupId($filter_group_id = 0) {
        $filter_ids = $this->cache->get('filter.filter_ids.' . (int)$this->config->get('config_language_id') . '.' . (int)$filter_group_id);

        if ($filter_ids === false) {
            $filter_ids = array();

            $sql = "
                SELECT f.filter_id
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
                ORDER BY f.filter_group_id, f.sort_order ASC
            ";

            $query = $this->db->query($sql);

            foreach ($query->rows as $result) {
                $filter_ids[] = (int)$result['filter_id'];
            }

            $this->cache->set('filter.filter_ids.' . (int)$this->config->get('config_language_id') . '.' . (int)$filter_group_id, $filter_ids, $this->cache_expires);
        }

        return $filter_ids;
    }

    public function getCategoryFiltersAll($data = array()) {
		$cache = md5(http_build_query($data));

		$filter_group_data = $this->cache->get('category.filters.all.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($filter_group_data === false) {
			$filter_group_data = array();

			if (isset($data['filter_category_filter'])) {
				$implode = array();

				$query = $this->db->query("
                    SELECT filter_id
                    FROM " . DB_PREFIX . "category_filter
                ");

				foreach ($query->rows as $result) {
					$implode[] = (int)$result['filter_id'];
				}
			}

			$sql = "
				SELECT DISTINCT f.filter_group_id
				, fgd.name
				, fg.sort_order
				FROM " . DB_PREFIX . "filter f
				LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id)
				LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id)
			";

			if (isset($data['filter_category_filter'])) {
				$sql .= "
					WHERE f.filter_id IN (" . implode(',', $implode) . ")
					AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				";
			} else {
				$sql .= "
					WHERE fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				";
			}

			$sql .= " GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)";

			$filter_group_query = $this->db->query($sql);

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$sql = "
					SELECT DISTINCT f.filter_id
					, fd.name
					FROM " . DB_PREFIX . "filter f
					LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id)
				";

				if (isset($data['filter_category_filter'])) {
					$sql .= "
						WHERE f.filter_id IN (" . implode(',', $implode) . ")
						AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "'
					";
				} else {
					$sql .= "
						WHERE f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "'
					";
				}

				$sql .= "
					AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "'
					ORDER BY f.sort_order, LCASE(fd.name)
				";

				$filter_query = $this->db->query($sql);

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
						'filter_id' => $filter['filter_id'],
						'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $filter_data
					);
				}
			}

			$this->cache->set('category.filters.all.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $filter_group_data, $this->cache_expires);
		}

		return $filter_group_data;
	}

	public function getCategoryFilters($category_id) {
		$filter_group_data = $this->cache->get('category.filters.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$category_id);

		if ($filter_group_data === false) {
			$filter_group_data = array();
            $implode_categories = array();
			$implode_filters = array();

            $this->load->model('catalog/category');

			$categories = $this->model_catalog_category->getCategories((int)$category_id);

			$implode_categories[] = (int)$category_id;

			foreach ($categories as $category) {
				$implode_categories[] = (int)$category['category_id'];
			}

			$query = $this->db->query("
				SELECT pf.filter_id
				FROM " . DB_PREFIX . "product p
				LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
				LEFT JOIN " . DB_PREFIX . "product_filter pf ON (p.product_id = pf.product_id)
				WHERE p2c.category_id IN (" . implode(',', $implode_categories) . ")
				AND p.status='1'
			");

			foreach ($query->rows as $result) {
				$implode_filters[] = (int)$result['filter_id'];
			}

			if ($implode_filters) {
				$filter_group_query = $this->db->query("
					SELECT DISTINCT f.filter_group_id
					, fgd.name
					, fg.sort_order
					FROM " . DB_PREFIX . "filter f
					LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id)
					LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id)
					WHERE f.filter_id IN (" . implode(',', $implode_filters) . ")
					AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'
					GROUP BY f.filter_group_id
					ORDER BY fg.sort_order, LCASE(fgd.name)
				");

				foreach ($filter_group_query->rows as $filter_group) {
					$filter_data = array();

					$filter_query = $this->db->query("
						SELECT DISTINCT f.filter_id
						, fd.name FROM " . DB_PREFIX . "filter f
						LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id)
						WHERE f.filter_id IN (" . implode(',', $implode_filters) . ")
						AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "'
						AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "'
						ORDER BY f.sort_order, LCASE(fd.name)
					");

					foreach ($filter_query->rows as $filter) {
						$filter_data[] = array(
							'filter_id' => $filter['filter_id'],
							'name'      => $filter['name']
						);
					}

					if ($filter_data) {
						$filter_group_data[] = array(
							'filter_group_id' => $filter_group['filter_group_id'],
							'name'            => $filter_group['name'],
							'filter'          => $filter_data
						);
					}
				}
			}

			$this->cache->set('category.filters.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$category_id, $filter_group_data, $this->cache_expires);
		}

		return $filter_group_data;
	}

}
