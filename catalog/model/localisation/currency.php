<?php
class ModelLocalisationCurrency extends Model {
	private $cache_expires = 60 * 60 * 24; // 1 day

	public function getCurrencyByCode($currency) {
		$currency_data = $this->cache->get('currency.code.' . (string)$currency);

		if ($currency_data === false) {
			$currency_data = array();

			$query = $this->db->query("
				SELECT DISTINCT *
				FROM " . DB_PREFIX . "currency
				WHERE code = '" . $this->db->escape($currency) . "'
			");

			$currency_data = $query->row;

			$this->cache->set('currency.code.' . (string)$currency, $currency_data, $this->cache_expires);
		}

		return $currency_data;
	}

	public function getCurrencies() {
		$currency_data = $this->cache->get('currency.all');

		if ($currency_data === false) {
			$currency_data = array();

			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "currency
				ORDER BY title ASC
			");

			foreach ($query->rows as $result) {
				$currency_data[$result['code']] = array(
					'currency_id'   => $result['currency_id'],
					'title'         => $result['title'],
					'code'          => $result['code'],
					'symbol_left'   => $result['symbol_left'],
					'symbol_right'  => $result['symbol_right'],
					'decimal_place' => $result['decimal_place'],
					'value'         => $result['value'],
					'status'        => $result['status'],
					'date_modified' => $result['date_modified']
				);
			}

			$this->cache->set('currency.all', $currency_data, $this->cache_expires);
		}

		return $currency_data;
	}

}
?>
