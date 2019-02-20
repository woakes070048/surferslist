<?php
class ModelTotalInsurance extends Model {

	public function getTotal(&$total_data, &$total, &$taxes) {
		$this->load->language('total/insurance');
		$classname = str_replace('vq2-catalog_model_total_', '', basename(__FILE__, '.php'));
		if (isset($this->session->data['insurance']) && $this->config->get($classname . '_status')) {

		 	// Get Address Data (Model)
		    $address = array();
			if (isset($this->session->data['payment_address_id']) && $this->session->data['payment_address_id']) { // Normal checkout
				$this->load->model('account/address');
				$address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
			} else { // Guest checkout
				$address = (isset($this->session->data['guest'])) ? $this->session->data['guest'] : array();
			}

			$country_id	= (isset($address['country_id'])) ? $address['country_id'] : 0;
			$zone_id 	= (isset($address['zone_id'])) ? $address['zone_id'] : 0;
			//

      		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get($classname . '_geo_zone_id') . "' AND country_id = '" . (int)$country_id . "' AND (zone_id = '" . (int)$zone_id . "' OR zone_id = '0')");
			if (!$this->config->get($classname . '_geo_zone_id')) {
        		$status = TRUE;
      		} elseif ($query->num_rows) {
      		  	$status = TRUE;
      		} else {
     	  		$status = FALSE;
			}

		 	if (!$status) { return; }
		 	//

			$cost = 0;
			$subtotal = $this->cart->getSubTotal();

			if (strpos($this->config->get($classname . '_rate'), ':') === false) {
				$cost = $this->config->get($classname . '_rate'); // single rate
			} else {
				$rates = explode(',', $this->config->get($classname . '_rate'));

				foreach ($rates as $rate) {
					$data = explode(':', $rate);

					if ($data[0] >= $subtotal) {
						if (isset($data[1])) {
							$cost = $data[1];
							if (strpos($cost, '%')) {
								$cost = trim($cost, '%');
								$cost = $subtotal * ($cost/100);
							}
						}

						break;
					}
				}
      		}

			$total_data[] = array(
				'code'		 => $classname, //v15x
        		'title'      => $this->language->get('text_' . $classname),
	    		'text'       => '+' . $this->currency->format($cost),
        		'value'      => $cost,
				'sort_order' => $this->config->get($classname . '_sort_order')
      		);

			if ($this->config->get($classname . '_tax_class_id')) {
				if (method_exists($this->document, 'addBreadcrumb')) { // v14x
					if (!isset($taxes[$this->config->get($classname . '_tax_class_id')])) {
						$taxes[$this->config->get($classname . '_tax_class_id')] = $cost / 100 * $this->tax->getRate($this->config->get($classname . '_tax_class_id'));
					} else {
						$taxes[$this->config->get($classname . '_tax_class_id')] += $cost / 100 * $this->tax->getRate($this->config->get($classname . '_tax_class_id'));
					}
				} else { // v15x
					$tax_rates = $this->tax->getRates($cost, $this->config->get($classname . '_tax_class_id'));

					foreach ($tax_rates as $tax_rate) {
						if (!isset($taxes[$tax_rate['tax_rate_id']])) {
							$taxes[$tax_rate['tax_rate_id']] = $tax_rate['amount'];
						} else {
							$taxes[$tax_rate['tax_rate_id']] += $tax_rate['amount'];
						}
					}
				}
			}

			$total += $cost;
		}
	}
}
?>