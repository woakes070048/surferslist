<?php
class ModelAccountProductDownload extends Model {

	public function addDownload($data) {
      	$this->db->query("
			INSERT INTO " . DB_PREFIX . "download
			SET filename = '" . $this->db->escape($data['filename']) . "'
			, mask = '" . $this->db->escape($data['mask']) . "'
			, remaining = '" . (int)$data['remaining'] . "'
			, member_customer_id = '" . (int)$this->customer->getId() . "'
			, date_added = NOW()
		");

      	$download_id = $this->db->countAffected() ? $this->db->getLastId() : 0;

		if (!$download_id) {
			return false;
		}

      	foreach ($data['download_description'] as $language_id => $value) {
        	$this->db->query("
				INSERT INTO " . DB_PREFIX . "download_description
				SET download_id = '" . (int)$download_id . "'
				, language_id = '" . (int)$language_id . "'
				, name = '" . $this->db->escape($value['name']) . "'
			");
      	}
	}

	public function editDownload($download_id, $data) {
        $this->db->query("
			UPDATE " . DB_PREFIX . "download
			SET filename = '" . $this->db->escape($data['filename']) . "'
			, mask = '" . $this->db->escape($data['mask']) . "'
			, remaining = '" . (int)$data['remaining'] . "'
			WHERE download_id = '" . (int)$download_id . "'
			AND member_customer_id = '" . (int)$this->customer->getId() . "'
		");

		if (!$this->db->countAffected()) {
			return;
		}

      	$this->db->query("
			DELETE FROM " . DB_PREFIX . "download_description
			WHERE download_id = '" . (int)$download_id . "'
		");

		if (!$this->db->countAffected()) {
			return;
		}

      	foreach ($data['download_description'] as $language_id => $value) {
        	$this->db->query("
				INSERT INTO " . DB_PREFIX . "download_description
				SET download_id = '" . (int)$download_id . "'
				, language_id = '" . (int)$language_id . "'
				, name = '" . $this->db->escape($value['name']) . "'
			");
      	}
	}

	public function deleteDownload($download_id) {
      	$this->db->query("
			DELETE FROM " . DB_PREFIX . "download
			WHERE download_id = '" . (int)$download_id . "'
			AND member_customer_id = '" . (int)$this->customer->getId() . "'
		");

		if ($this->db->countAffected()) {
			$this->db->query("
				DELETE FROM " . DB_PREFIX . "download_description
				WHERE download_id = '" . (int)$download_id . "'
			");
		}
	}

	public function getDownload($download_id) {
		$query = $this->db->query("
			SELECT DISTINCT *
			FROM " . DB_PREFIX . "download
			WHERE download_id = '" . (int)$download_id . "'
			AND member_customer_id = '" . (int)$this->customer->getId() . "'
		");

		return $query->row;
	}

	public function getDownloads($data = array()) {
		$sql = "
			SELECT *
			FROM " . DB_PREFIX . "download d
			LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id)
			WHERE dd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			AND d.member_customer_id = '" . (int)$this->customer->getId() . "'
		";

		$sort_data = array(
			'dd.name',
			'd.remaining'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY dd.name";
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

		return $query->rows;
	}

	public function getDownloadDescriptions($download_id) {
		$download_description_data = array();

		$query = $this->db->query("
			SELECT dd.*
			FROM " . DB_PREFIX . "download_description dd
			RIGHT JOIN " . DB_PREFIX . "download d ON (dd.download_id = d.download_id)
			WHERE d.member_customer_id = '" . (int)$this->customer->getId() . "'
		");

		foreach ($query->rows as $result) {
			$download_description_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $download_description_data;
	}

	public function getTotalDownloads() {
      	$query = $this->db->query("
			SELECT COUNT(download_id) AS total
			FROM " . DB_PREFIX . "download
			WHERE member_customer_id = '" . (int)$this->customer->getId() ."'
		");

		return $query->row['total'];
	}
}
?>
