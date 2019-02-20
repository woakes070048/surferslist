<?php
class ModelSettingStore extends Model {
	private $cache_expires = 60 * 60 * 24 * 90; // 3 months

	public function getStores($data = array()) {
		$store_data = $this->cache->get('store');

		if ($store_data === false) {
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "store
				ORDER BY url
			");

			$store_data = $query->rows;

			$this->cache->set('store', $store_data, $this->cache_expires);
		}

		return $store_data;
	}
}
?>
