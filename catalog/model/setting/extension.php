<?php
class ModelSettingExtension extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month

	function getExtensions($type) {
		$extension_type_data = $this->cache->get('extension.type.' . (string)$type);

		if ($extension_type_data === false) {
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "extension
				WHERE `type` = '" . $this->db->escape($type) . "'
			");

			$extension_type_data = $query->rows;

			$this->cache->set('extension.type.' . (string)$type, $extension_type_data, $this->cache_expires);
		}

		return $extension_type_data;
	}

}

