<?php
class ModelLocalisationCountry extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month

	public function getCountry($country_id, $status = '1') {
		$country_data = $this->cache->get('country.' . (int)$country_id);

		if ($country_data === false) {
			$sql = "
				SELECT *
				FROM " . DB_PREFIX . "country
				WHERE country_id = '" . (int)$country_id . "'
			";

			if (!is_null($status)) {
				$sql .= "
					AND status = '" . (int)$status . "'
				";
			}

			$query = $this->db->query($sql);

			$country_data = $query->row;

			$this->cache->set('country.' . (int)$country_id, $country_data, $this->cache_expires);
		}

		return $country_data;
	}

	public function getCountryByISO($iso_code_2) {
		$country_data = $this->cache->get('country.iso_code_2.' . $this->db->escape($iso_code_2));

		if ($country_data === false) {
			$query = $this->db->query("
				SELECT DISTINCT *
				FROM " . DB_PREFIX . "country
				WHERE iso_code_2 = '" . $this->db->escape($iso_code_2) . "'
				AND status = '1'
			");

			$country_data = $query->row;

			$this->cache->set('country.iso_code_2.' . $this->db->escape($iso_code_2), $country_data, $this->cache_expires);
		}

		return $country_data;
	}

	public function getCountries($status = '1', $primary_country_id = '223') {
		$country_data = $this->cache->get('country.status.' . $status);

		if ($country_data === false) {
			$country_data = array();

			$sql = "
				SELECT *
				FROM " . DB_PREFIX . "country
			";

			if ($status) {
				$sql .= "
					WHERE status = '1'
				";
			} else {
				$sql .= "
					WHERE status = '0'
				";
			}

			$sql .= "
				ORDER BY name ASC
			";

			$query = $this->db->query($sql);

			foreach ($query->rows as $result) {
				if ($result['country_id'] == $primary_country_id) {
					$primary_country = $result;
					continue;
				}

				$country_data[] = array(
					'country_id'   		=> $result['country_id'],
					'name'         		=> $result['name'],
					'iso_code_2'        => $result['iso_code_2'],
					'iso_code_3'   		=> $result['iso_code_3'],
					'address_format'  	=> $result['address_format'],
					'postcode_required' => $result['postcode_required'],
					'status'        	=> $result['status']
				);
			}

			if ($country_data && isset($primary_country)) {
				array_unshift($country_data, $primary_country);
			}

			$this->cache->set('country.status.' . $status, $country_data, $this->cache_expires);
		}

		return $country_data;
	}

}

