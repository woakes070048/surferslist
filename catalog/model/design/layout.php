<?php
class ModelDesignLayout extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month

	private $route_layout = array();

	public function __construct($registry) {
		parent::__construct($registry);
		$this->route_layout = $this->config->get('config_route_layout');
	}

	public function getLayout($route) {
		if (array_key_exists($route, $this->route_layout)) {
			return $this->route_layout[$route];
		} else if (array_key_exists(substr($route, 0, strrpos($route, '/') + 1), $this->route_layout)) {
			return $this->route_layout[substr($route, 0, strrpos($route, '/') + 1)];
		}

		$query = $this->db->query("
			SELECT layout_id
			FROM " . DB_PREFIX . "layout_route
			WHERE '" . $this->db->escape($route) . "' LIKE CONCAT(route, '%')
			AND store_id = '" . (int)$this->config->get('config_store_id') . "'
			ORDER BY route DESC
			LIMIT 1
		");

		return $query->num_rows ? $query->row['layout_id'] : $this->config->get('config_layout_id');
	}

	public function getLayouts($data = array()) {
		$cache = md5(http_build_query($data));

		$layouts_data = $this->cache->get('layouts.' . $cache);

		if ($layouts_data === false) {
			$sql = "
				SELECT *
				FROM " . DB_PREFIX . "layout
			";

			$sort_data = array('name');

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

			$layouts_data = $query->rows;

			$this->cache->set('layouts.' . $cache, $layouts_data, $this->cache_expires);
		}

		return $layouts_data;
	}

	public function getCategoryLayoutId($category_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "category_to_layout
			WHERE category_id = '" . (int)$category_id . "'
			AND store_id = '" . (int)$this->config->get('config_store_id') . "'
		");

		return $query->num_rows ? $query->row['layout_id'] : 0;
	}

	public function getProductLayoutId($product_id) {
		if (empty($product_id)) return 0;

		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "product_to_layout
			WHERE product_id = '" . (int)$product_id . "'
			AND store_id = '" . (int)$this->config->get('config_store_id') . "'
		");

		return $query->num_rows ? $query->row['layout_id'] : 0;
	}

	public function getInformationLayoutId($information_id) {
		$query = $this->db->query("
			SELECT *
			FROM " . DB_PREFIX . "information_to_layout
			WHERE information_id = '" . (int)$information_id . "'
			AND store_id = '" . (int)$this->config->get('config_store_id') . "'
		");

		return $query->num_rows ? $query->row['layout_id'] : 0;
	}

}
?>
