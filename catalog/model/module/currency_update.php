<?php
class ModelModuleCurrencyUpdate extends Model {
	protected $log_instance;

	public function __construct($registry) {
		parent::__construct($registry);
		$this->log_instance = new Log('currency_update.log');
	}

	public function update($currency_codes = array()) {
		if ($this->config->get('module_currency_update_status') == 0 || !$currency_codes) {
			return false;
		}

		$comission = $this->config->get('module_currency_update_comission') > 0
			? (float) $this->config->get('module_currency_update_comission') / 100
			: false;

		$base_currency = $this->config->get('config_currency');

		$response = $this->curlRequest('http://data.fixer.io/api/latest?access_key=' . $this->config->get('config_fixer_access_key') . '&symbols=' . implode(',', $currency_codes));

		if ($response) {
			$json = json_decode($response);

			if ($json->success == false) {
				$this->log('ERROR: ' . $json->error->code . ' | ' . $json->error->type);
				return false;
			}

			foreach ($currency_codes as $code) {
				$value = $comission
					? (float) ($json->rates->{$code} + ((float) $json->rates->{$code} * $comission))/$json->rates->{$base_currency}
					: (float) $json->rates->{$code}/$json->rates->{$base_currency};

				$this->db->query("
					UPDATE " . DB_PREFIX . "currency
					SET value = '" . $this->db->escape($value) . "'
					, date_modified = '" .  $this->db->escape(date('Y-m-d H:i:s')) . "'
					WHERE code = '" . $this->db->escape($code) . "'
				");
			}
		} else {
			return false;
		}

		$this->cache->delete('currency');

		return true;
	}

	private function curlRequest($url, $options = array()) {
		$this->log('Curl init : '.$url);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($ch);
		$info = curl_getinfo($ch);

		if (curl_error($ch)) {
			$this->log('Curl error : '.curl_error($ch));
			return false;
		}

		if (in_array($info['http_code'], array(401,403,404))) {
			$this->log('Curl error : '.$info['http_code'].' header status');
			return false;
		}

		return $result;
	}

	private function log($str) {
		if ($this->config->get('module_currency_update_debug') == 1) {
			$this->log_instance->write($str);
		}
	}
}
