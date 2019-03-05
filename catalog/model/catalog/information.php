<?php
class ModelCatalogInformation extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month

	public function updateViewed($information_id) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "information
			SET viewed = (viewed + 1)
			WHERE information_id = '" . (int)$information_id . "'
		");
	}

	public function getInformation($information_id) {
		$information_data = $this->cache->get('information.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$information_id);

		if ($information_data === false) {
			$query = $this->db->query("
				SELECT DISTINCT *
				FROM " . DB_PREFIX . "information i
				LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id)
				LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id)
				WHERE i.information_id = '" . (int)$information_id . "'
				AND id.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				AND i.status = '1'
			");

			$information_data = $query->row;

			$this->cache->set('information.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$information_id, $information_data, $this->cache_expires);
		}

		return $information_data;
	}

	public function getInformations() {
		$information_data = $this->cache->get('information.all.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($information_data === false) {
			$query = $this->db->query("
				SELECT i.*
				, id.title
				, id.meta_description
				, id.meta_keyword
				FROM " . DB_PREFIX . "information i
				LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id)
				LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id)
				WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				AND i.status = '1'
				ORDER BY i.sort_order, LCASE(id.title) ASC
			");

			$information_data = $query->rows;

			$this->cache->set('information.all.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $information_data, $this->cache_expires);
		}

		return $information_data;
	}

}
?>
