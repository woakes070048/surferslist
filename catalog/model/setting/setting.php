<?php
class ModelSettingSetting extends Model {
	private $cache_expires = 60 * 60 * 24; // 1 day

	public function getSetting($group, $store_id = 0) {
		$setting_data = $this->cache->get('setting.group.' . (string)$group . '.' . (int)$this->config->get('config_store_id'));

		if ($setting_data === false) {
			$setting_data = array();

			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "setting
				WHERE store_id = '" . (int)$store_id . "'
				AND `group` = '" . $this->db->escape($group) . "'
			");

			foreach ($query->rows as $result) {
				$setting_data[$result['key']] = !$result['serialized'] ? $result['value'] : unserialize($result['value']);
			}

			$this->cache->set('setting.group.' . (string)$group . '.' . (int)$this->config->get('config_store_id'), $setting_data, $this->cache_expires);
		}

		return $setting_data;
	}

	/*
	public function getSettingValue($group = '', $key = '', $store_id = 0) {
		$data = '';

		$query = $this->db->query("SELECT DISTINCT value, serialized FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->db->escape($group) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");

		$result = $query->row;

		if (!$result['serialized']) {
			$data = $result['value'];
		} else {
			$data = unserialize($result['value']);
		}

		return $data;
	}
	*/

}
?>
