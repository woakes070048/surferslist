<?php
trait Location {
	protected $location_code;
	protected $location_country;
	protected $location_zone;
	protected $location_city;

	protected function setLocation() {
		$location_session_vars = array(
			'iso_code_3' => 'shipping_country_iso_code_3',
			'country_id' => 'shipping_country_id',
			'zone_id' => 'shipping_zone_id',
			'city' => 'shipping_location'
		);

		foreach ($location_session_vars as $key => $value) {
			${$key} = isset($this->session->data[$value]) ? $this->session->data[$value] : '';
		}

		if ($country_id || $zone_id) {
			$this->load->model('localisation/country');
			$this->load->model('localisation/zone');
		}

		$country = $country_id ? $this->model_localisation_country->getCountry($country_id) : '';
		$zone = $zone_id ? $this->model_localisation_zone->getZone($zone_id) : '';

		$this->location_code = $iso_code_3 ?: ($country ? $country['iso_code_3'] : '');
		$this->location_country = $country;
		$this->location_zone = $zone;
		$this->location_city = $city;
	}

	protected function getLocationCode() {
		if (!isset($this->location_code)) {
			$this->setLocation();
		}

		return $this->location_code;
	}

	protected function getLocationCountry() {
		if (!isset($this->location_country)) {
			$this->setLocation();
		}

		return $this->location_country;
	}

	protected function getLocationCountryName() {
		$country = $this->getLocationCountry();

		return isset($country['name']) ? $country['name'] : '';
	}

	protected function getLocationCountryCode() {
		$country = $this->getLocationCountry();

		return isset($country['iso_code_3']) ? $country['iso_code_3'] : '';
	}

	protected function getLocationZone() {
		if (!isset($this->location_zone)) {
			$this->setLocation();
		}

		return $this->location_zone;
	}

	protected function getLocationZoneName() {
		$zone = $this->getLocationZone();

		return isset($zone['name']) ? $zone['name'] : '';
	}

	protected function getLocationZoneCode() {
		$zone = $this->getLocationZone();

		return isset($zone['code']) ? $zone['code'] : '';
	}

	protected function getLocationCity() {
		if (!isset($this->location_city)) {
			$this->setLocation();
		}

		return $this->location_city;
	}

	protected function getLocationName($type) {
		if (!$type || ($type !== 'long' && $type !== 'short')) {
			return '';
		}

		$name = '';

		$country = $this->getLocationCountry();
		$zone = $this->getLocationZone();
		$city = $this->getLocationCity();

		if ($zone && $city) {
			$name = ucwords($city) . ', ' . $zone['code'];
		} else if ($zone) {
			$name = $zone['name'];
		}

		if ($name && $type === 'long') {
			$name .= ', ' . $country['iso_code_3'];
		}

		if (!$name && $country) {
			$name = $country['name'];
		}

		return $name;
	}
}
?>
