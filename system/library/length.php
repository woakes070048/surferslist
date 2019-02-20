<?php
class Length {
	private $lengths = array();

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');
		$this->cache = $registry->get('cache');

		$length_class_data = $this->cache->get('length_class.' . (int)$this->config->get('config_language_id'));

		if (!$length_class_data) {
			$length_class_data = array();

			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "length_class lc
				LEFT JOIN " . DB_PREFIX . "length_class_description lcd ON (lc.length_class_id = lcd.length_class_id)
				WHERE lcd.language_id = '" . (int)$this->config->get('config_language_id') . "'
			");

			foreach ($query->rows as $result) {
				$length_class_data[$result['length_class_id']] = array(
					'length_class_id' => $result['length_class_id'],
					'title'           => $result['title'],
					'unit'            => $result['unit'],
					'value'           => $result['value']
				);
			}

			$this->cache->set('length_class.' . (int)$this->config->get('config_language_id'), $length_class_data, 60 * 60 * 24 * 365); // 1 year cache expiration
		}

		$this->lengths = $length_class_data;
	}

	public function convert($value, $from, $to) {
		if ($from == $to) {
			return $value;
		}

		if (isset($this->lengths[$from])) {
			$from = $this->lengths[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->lengths[$to])) {
			$to = $this->lengths[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function format($value, $length_class_id, $decimal_point = '.', $thousand_point = ',') {
		if (isset($this->lengths[$length_class_id])) {
			return number_format($value, 2, $decimal_point, $thousand_point) . $this->lengths[$length_class_id]['unit'];
		} else {
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	public function getUnit($length_class_id) {
		if (isset($this->lengths[$length_class_id])) {
			return $this->lengths[$length_class_id]['unit'];
		} else {
			return '';
		}
	}
}
?>
