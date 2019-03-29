<?php
class ModelAccountAddress extends Model {
	public function addAddress($data) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "address
			SET customer_id = '" . (int)$this->customer->getId() . "'
			, firstname = '" . $this->db->escape($data['firstname']) . "'
			, lastname = '" . $this->db->escape($data['lastname']) . "'
			, company = '" . $this->db->escape($data['company']) . "'
			, company_id = '" . $this->db->escape(isset($data['company_id']) ? $data['company_id'] : '') . "'
			, tax_id = '" . $this->db->escape(isset($data['tax_id']) ? $data['tax_id'] : '') . "'
			, address_1 = '" . $this->db->escape($data['address_1']) . "'
			, address_2 = '" . $this->db->escape($data['address_2']) . "'
			, postcode = '" . $this->db->escape($data['postcode']) . "'
			, city = '" . $this->db->escape($data['city']) . "'
			, zone_id = '" . (int)$data['zone_id'] . "'
			, country_id = '" . (int)$data['country_id'] . "'
		");

		$address_id = $this->db->countAffected() ? $this->db->getLastId() : 0;

		if ($address_id && !empty($data['default'])) {
			$this->db->query("
				UPDATE " . DB_PREFIX . "customer
				SET address_id = '" . (int)$address_id . "'
				WHERE customer_id = '" . (int)$this->customer->getId() . "'
			");
		}

		return $address_id;
	}

	public function editAddress($address_id, $data) {
		$this->db->query("
			UPDATE " . DB_PREFIX . "address
			SET firstname = '" . $this->db->escape($data['firstname']) . "'
			, lastname = '" . $this->db->escape($data['lastname']) . "'
			, company = '" . $this->db->escape($data['company']) . "'
			, company_id = '" . $this->db->escape(isset($data['company_id']) ? $data['company_id'] : '') . "'
			, tax_id = '" . $this->db->escape(isset($data['tax_id']) ? $data['tax_id'] : '') . "'
			, address_1 = '" . $this->db->escape($data['address_1']) . "'
			, address_2 = '" . $this->db->escape($data['address_2']) . "'
			, postcode = '" . $this->db->escape($data['postcode']) . "'
			, city = '" . $this->db->escape($data['city']) . "'
			, zone_id = '" . (int)$data['zone_id'] . "'
			, country_id = '" . (int)$data['country_id'] . "'
			WHERE address_id  = '" . (int)$address_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");

		if (!empty($data['default'])) {
			$this->db->query("
				UPDATE " . DB_PREFIX . "customer
				SET address_id = '" . (int)$address_id . "'
				WHERE customer_id = '" . (int)$this->customer->getId() . "'
			");
		}
	}

	public function deleteAddress($address_id) {
		$this->db->query("
			DELETE FROM " . DB_PREFIX . "address
			WHERE address_id = '" . (int)$address_id . "'
			AND customer_id = '" . (int)$this->customer->getId() . "'
		");
	}

	public function getAddress($address_id) {
		$address_query = $this->db->query("
			SELECT a.*
			, c.name AS country_name
			, c.iso_code_2 AS country_iso_code_2
			, c.iso_code_3 AS country_iso_code_3
			, c.address_format AS country_address_format
			, z.name AS zone_name
			, z.code AS zone_code
			FROM " . DB_PREFIX . "address a
			LEFT JOIN " . DB_PREFIX . "country c ON a.country_id = c.country_id
			LEFT JOIN " . DB_PREFIX . "zone z ON a.zone_id = z.zone_id
			WHERE a.address_id = '" . (int)$address_id . "'
			AND a.customer_id = '" . (int)$this->customer->getId() . "'
		");

		if (!$address_query->num_rows) {
			return false;
		} else {
			$address_data = array(
				'firstname'      => $address_query->row['firstname'],
				'lastname'       => $address_query->row['lastname'],
				'company'        => $address_query->row['company'],
				'company_id'     => $address_query->row['company_id'],
				'tax_id'         => $address_query->row['tax_id'],
				'address_1'      => $address_query->row['address_1'],
				'address_2'      => $address_query->row['address_2'],
				'postcode'       => $address_query->row['postcode'],
				'city'           => $address_query->row['city'],
				'zone_id'        => $address_query->row['zone_id'],
				'zone'           => $address_query->row['zone_name'], // $zone,
				'zone_code'      => $address_query->row['zone_code'], // $zone_code,
				'country_id'     => $address_query->row['country_id'],
				'country'        => $address_query->row['country_name'], // $country,
				'iso_code_2'     => $address_query->row['country_iso_code_2'], // $iso_code_2,
				'iso_code_3'     => $address_query->row['country_iso_code_3'], // $iso_code_3,
				'address_format' => $address_query->row['country_address_format'] // $address_format
			);

			return $address_data;
		}
	}

	public function getAddresses() {
		$address_data = array();

		$query = $this->db->query("
			SELECT a.*
			, c.name AS country_name
			, c.iso_code_2 AS country_iso_code_2
			, c.iso_code_3 AS country_iso_code_3
			, c.address_format AS country_address_format
			, z.name AS zone_name
			, z.code AS zone_code
			FROM " . DB_PREFIX . "address a
			LEFT JOIN " . DB_PREFIX . "country c ON a.country_id = c.country_id
			LEFT JOIN " . DB_PREFIX . "zone z ON a.zone_id = z.zone_id
			WHERE a.customer_id = '" . (int)$this->customer->getId() . "'
			ORDER BY c.name ASC, z.name ASC, a.city ASC
		");

		foreach ($query->rows as $result) {
			$address_data[$result['address_id']] = array(
				'address_id'     => $result['address_id'],
				'firstname'      => $result['firstname'],
				'lastname'       => $result['lastname'],
				'company'        => $result['company'],
				'company_id'     => $result['company_id'],
				'tax_id'         => $result['tax_id'],
				'address_1'      => $result['address_1'],
				'address_2'      => $result['address_2'],
				'postcode'       => $result['postcode'],
				'city'           => $result['city'],
				'zone_id'        => $result['zone_id'],
				'zone'           => $result['zone_name'], // $zone,
				'zone_code'      => $result['zone_code'], // $zone_code,
				'country_id'     => $result['country_id'],
				'country'        => $result['country_name'], // $country,
				'iso_code_2'     => $result['country_iso_code_2'], // $iso_code_2,
				'iso_code_3'     => $result['country_iso_code_3'], // $iso_code_3,
				'address_format' => $result['country_address_format'] // $address_format
			);
		}

		return $address_data;
	}

	public function getTotalAddresses() {
		$query = $this->db->query("
			SELECT COUNT(address_id) AS total
			FROM " . DB_PREFIX . "address
			WHERE customer_id = '" . (int)$this->customer->getId() . "'
		");

		return $query->row['total'];
	}
}

