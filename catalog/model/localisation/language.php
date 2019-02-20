<?php
class ModelLocalisationLanguage extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month

	public function getLanguage($language_id, $status = '1') {
		$language_data = $this->cache->get('language.' . (int)$language_id);

		if ($language_data === false) {
			$sql = "
				SELECT *
				FROM " . DB_PREFIX . "language
				WHERE language_id = '" . (int)$language_id . "'
			";

			if (!is_null($status)) {
				$sql .= "
					AND status = '" . (int)$status . "'
				";
			}

			$query = $this->db->query($sql);

			$language_data = $query->row;

			$this->cache->set('language.' . (int)$language_id, $language_data, $this->cache_expires);
		}

		return $language_data;
	}

	public function getLanguages() {
		$language_data = $this->cache->get('language');

		if ($language_data === false) {
			$language_data = array();

			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "language
				ORDER BY sort_order, name
			");

			foreach ($query->rows as $result) {
				$language_data[$result['code']] = array(
					'language_id' => $result['language_id'],
					'name'        => $result['name'],
					'code'        => $result['code'],
					'locale'      => $result['locale'],
					'image'       => $result['image'],
					'directory'   => $result['directory'],
					'filename'    => $result['filename'],
					'sort_order'  => $result['sort_order'],
					'status'      => $result['status']
				);
			}

			$this->cache->set('language', $language_data, $this->cache_expires);
		}

		return $language_data;
	}

}
?>
