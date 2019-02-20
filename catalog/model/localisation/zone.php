<?php
class ModelLocalisationZone extends Model {
	private $cache_expires = 60 * 60 * 24 * 30; // 1 month

	public function getZone($zone_id, $status = '1') {
		$zone_data = $this->cache->get('zone.' . (int)$zone_id);

		if ($zone_data === false) {
			$sql = "
				SELECT *
				FROM " . DB_PREFIX . "zone
				WHERE zone_id = '" . (int)$zone_id . "'
			";

			if (!is_null($status)) {
				$sql .= "
					AND status = '" . (int)$status . "'
				";
			}

			$query = $this->db->query($sql);

			$zone_data = $query->row;

			// fallback for incorrectly configured countries without zones
			if (!$zone_data && $zone_id == 0) {
				$zone_data = array(
					'zoned_id'		=> 0,
					'country_id'	=> 0,
					'name'			=> '',
					'code'			=> '',
					'status'		=> 1
				);
			}

			$this->cache->set('zone.' . (int)$zone_id, $zone_data, $this->cache_expires);
		}

		return $zone_data;
	}

	public function getZoneByISO($iso_code, $country_id) {
		$zone_data = $this->cache->get('zone.country.iso_code.' . (int)$country_id . '.' . $this->db->escape($iso_code));

		if ($zone_data === false) {
			$query = $this->db->query("
				SELECT DISTINCT z.*
				, c.iso_code_2 AS country_iso_code_2
				, c.iso_code_3 AS country_iso_code_3
				FROM " . DB_PREFIX . "zone z
				INNER JOIN " . DB_PREFIX . "country c ON z.country_id = c.country_id
				WHERE z.country_id = '" . (int)$country_id . "'
				AND z.code = '" . $this->db->escape($iso_code) . "'
				AND z.status = '1'
				AND c.status = '1'
			");

			$zone_data = $query->row;

			$this->cache->set('zone.country.iso_code.' . (int)$country_id . '.' . $this->db->escape($iso_code), $zone_data, $this->cache_expires);
		}

		return $zone_data;
	}

	public function getZonesByCountryId($country_id) {
		if ((int)$country_id <= 0) {
			return array();
		}

		$zone_data = $this->cache->get('zone.country.id.' . (int)$country_id);

		if ($zone_data === false) {
			$query = $this->db->query("
				SELECT z.*
				, c.iso_code_2 AS country_iso_code_2
				, c.iso_code_3 AS country_iso_code_3
				FROM " . DB_PREFIX . "zone z
				INNER JOIN " . DB_PREFIX . "country c ON z.country_id = c.country_id
				WHERE z.country_id = '" . (int)$country_id . "'
				AND z.status = '1'
				AND c.status = '1'
				ORDER BY name
			");

			$zone_data = $query->rows;

			$this->cache->set('zone.country.id.' . (int)$country_id, $zone_data, $this->cache_expires);
		}

		return $zone_data;
	}

	public function getGeoZones() {
		$geo_zone_data = $this->cache->get('zone.geo_zones');

		if ($geo_zone_data === false) {
			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "geo_zone
				ORDER BY geo_zone_id ASC
			");

			$geo_zone_data = $query->rows;

			$this->cache->set('zone.geo_zones', $geo_zone_data, $this->cache_expires);
		}

		return $geo_zone_data;
	}

	public function getZonesByGeoZoneId($geo_zone_id) {
		$geo_zone_zones_data = $this->cache->get('zone.geo_zone.' . (int)$geo_zone_id);

		if ($geo_zone_zones_data === false) {
			$sql = "
				SELECT zgz.zone_id, z.name, z.code
				FROM " . DB_PREFIX . "zone_to_geo_zone zgz
				INNER JOIN " . DB_PREFIX . "zone z ON (zgz.zone_id = z.zone_id)
				INNER JOIN " . DB_PREFIX . "country c ON (zgz.country_id = c.country_id)
				INNER JOIN " . DB_PREFIX . "geo_zone gz ON (zgz.geo_zone_id = gz.geo_zone_id)
				WHERE zgz.geo_zone_id = '" . (int)$geo_zone_id . "'
				AND z.status = '1'
				AND c.status = '1'
				ORDER BY z.name ASC
			";

			$query = $this->db->query($sql);

			$geo_zone_zones_data = $query->rows;

			$this->cache->set('zone.geo_zone.' . (int)$geo_zone_id, $geo_zone_zones_data, $this->cache_expires);
		}

		return $geo_zone_zones_data;
	}

}
?>
