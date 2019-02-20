<?php
//==============================================//
// Product:	Per Product Shipping              	//
// Author: 	Joel Reeds                        	//
// Company: OpenCart Addons                  	//
// Website: http://opencartaddons.com        	//
// Contact: http://opencartaddons.com/contact  	//
//==============================================//

class ModelShippingOCAPPS extends Model {
	private $extension 				= 'ocapps';
	private $extensionType 			= 'shipping';
	private $db_table				= 'product_shipping';
	private $debugStatus			= true;

	public function getQuote($address) {
		$this->load->language($this->extensionType . '/' . $this->extension);

		$this->debugStatus = $this->getField('debug');

		if ($this->getField('status') && $this->cart->hasProducts() && $address) {
			$language_code = isset($this->session->data['language']) ? $this->session->data['language'] : $this->config->get('config_language');

			$quote_data = array();
			$method_data = array();

			if ($this->debugStatus) {
				$debug  = $this->language->get('text_title');
				$debug .= ' | Status: ENABLED';
				$debug .= ' | LanguageCode: ' . strtoupper($language_code);
			}

			$geo_zone_id = 0;

			$query = $this->db->query("
				SELECT *
				FROM " . DB_PREFIX . "geo_zone
				ORDER BY name
			");

			foreach ($query->rows as $result) {
				$query = $this->db->query("
					SELECT *
					FROM " . DB_PREFIX . "zone_to_geo_zone
					WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "'
					AND country_id = '" . (int)$address['country_id'] . "'
					AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')
				");

				if ($query->num_rows) {
					if ($this->debugStatus) {
						$debug .= ' | GeoZone: ' . $result['name'];
					}

					$geo_zone_id = $result['geo_zone_id'];

					break;
				}
			}

			if ($this->debugStatus && $geo_zone_id == 0) {
				$debug .= ' | GeoZone: All Other Zones';
			}

			$products = array();

			foreach ($this->cart->getProducts() as $product) {
				if (isset($products[$product['product_id']])) {
					$products[$product['product_id']]['quantity'] += $product['quantity'];
				} else {
					$products[$product['product_id']] = array(
						'name'			=> $product['name'],
						'product_id'	=> (int)$product['product_id'],
						'member_id'		=> (int)$product['member_customer_id'],
						'shipping'		=> (int)$product['shipping'],
						'quantity'		=> (int)$product['quantity'],
					);
				}
			}

			$settings = $this->getField('settings');

			$cost = 0;
			$cost_not_set = true;
			$cost_missing = false;
			$cost_missing_products = array();
			$member_id = 0;

			if ($products) {
				foreach ($products as $key => $value) {
					if ($this->debugStatus) {
						$debug .= ' | ProductName: ' . $value['name'];
						$debug .= ' | ProductQuantity: ' . $value['quantity'];
						$debug .= ' | ProductMemberId: ' . $value['member_id'];
					}

					$query = $this->db->query("
						SELECT DISTINCT *
						FROM " . DB_PREFIX . $this->db_table . "
						WHERE product_id = '" . (int)$key . "'
						AND geo_zone_id = '" . (int)$geo_zone_id . "'
					");


					if (!$query->row || empty($query->row['first'])) {
						$query = $this->db->query("
							SELECT DISTINCT *
							FROM " . DB_PREFIX . $this->db_table . "
							WHERE product_id = '" . (int)$key . "'
							AND geo_zone_id = '0'
						");
					}

					if ($query->row && !empty($query->row['first'])) {
						$multiplier = isset($settings[$geo_zone_id]['multiplier']) ? $settings[$geo_zone_id]['multiplier'] : 1;

						if ($value['quantity'] > 1) {
							if (!empty($query->row['additional'])) {
								$product_shipping_cost = ($query->row['first'] * $multiplier) + (($query->row['additional'] * $multiplier) * ($value['quantity'] - 1));
							} else {
								$product_shipping_cost = ($query->row['first'] * $multiplier) * ($value['quantity']);
							}
						} else {
							$product_shipping_cost = $query->row['first'] * $multiplier;
						}

						if ($this->debugStatus) {
							$debug	.= ' | ProductShippingCost: ' . $product_shipping_cost;
						}

						$cost += (float) $product_shipping_cost;

						$cost_not_set = false;
					} else if (!$query->row && $value['shipping']) {
						if ($this->debugStatus) {
							$debug .= ' | ProductShippingCost: NO COSTS FOUND FOR ANY ZONE';
						}

						$member_id = $value['member_id'];

						$cost_missing_products[] = $value['name'];
						
						$cost_not_set = true;
					} else {
						if ($this->debugStatus) {
							$debug .= ' | ProductShippingCost: NO COSTS FOUND';
						}

						$cost_not_set = true;
					}
				}

				if ($cost_missing_products && count($cost_missing_products) < count($products)) {
					$cost_missing = true;
				}
			} else {
				if ($this->debugStatus) {
					$debug .= ' | NO PRODUCTS FOUND';
				}
			}

			if ($cost_not_set == false) {
				if (!$cost_missing) {
					$cost = (float)$cost;
					$cost_min = isset($settings[$geo_zone_id]['cost_min']) ? (float)$settings[$geo_zone_id]['cost_min'] : 0;
					$cost_max = isset($settings[$geo_zone_id]['cost_max']) ? (float)$settings[$geo_zone_id]['cost_max'] : 0;

					if ($this->debugStatus) {
						$debug .= ' | ' . ucfirst($this->extensionType) . 'Cost: ' . $cost;
					}

					if ($cost_min > 0 && $cost < $cost_min) {
						$cost = $cost_min;

						if ($this->debugStatus) {
							$debug .= ' | CostAdjusted: MIN ' . $cost;
						}
					}

					if ($cost_max > 0 && $cost > $cost_max) {
						$cost = $cost_max;

						if ($this->debugStatus) {
							$debug .= ' | CostAdjusted: MAX ' . $cost;
						}
					}

					if (isset($settings[$geo_zone_id]['handling_fee']) && $settings[$geo_zone_id]['handling_fee']) {
						if (strpos($settings[$geo_zone_id]['handling_fee'], '%')) {
							$cost += $cost * ($settings[$geo_zone_id]['handling_fee'] / 100);
						} else {
							$cost += $settings[$geo_zone_id]['handling_fee'];
						}

						if ($this->debugStatus) {
							$debug .= ' | HandlingFee: ' . $settings[$geo_zone_id]['handling_fee'];
						}
					}

					if ($this->debugStatus) {
						$debug .= ' | Total' . ucfirst($this->extensionType) . 'Cost: ' . $cost;
					}

					// $name = $this->getField('name');
					// $name = !empty($name[$language_code]) ? $name[$language_code] : $this->language->get('text_name');

					$single_data = array(
						'title'			=> $this->language->get('text_name'),
						'sort_order'	=> 1,
						'tax_class_id'	=> $this->getField('tax_class_id'),
						'cost'			=> $cost,
					);

					$quote_data[$this->extension] = $this->getQuoteData($single_data);
				} else {
					// $title = $this->getField('title');
					// $title = !empty($title[$language_code]) ? $title[$language_code] : $this->language->get('text_title');

					$method_data = array(
						'id'         	=> $this->extension,
						'code'       	=> $this->extension,
						'title'      	=> $this->language->get('text_title'),
						'quote'      	=> $quote_data,
						'sort_order' 	=> $this->getField('sort_order'),
						'error'      	=> sprintf('No shipping rate has been set for <b>' . implode("</b>, <b>", $cost_missing_products) . '</b>.')
					);
				}
			}

			if ($this->debugStatus) {
				$this->writeDebug($debug);
			}

			if ($quote_data) {
				// $title = $this->getField('title');
				// $title = !empty($title[$language_code]) ? $title[$language_code] : $this->language->get('text_title');

				$method_data = array(
					'id'       		=> $this->extension,
					'code'       	=> $this->extension,
					'title'      	=> $this->language->get('text_title'),
					'quote'      	=> $quote_data,
					'sort_order' 	=> $this->getField('sort_order'),
					'error'      	=> false
				);
			}

			return $method_data;
		} else if ($this->debugStatus) {
			$debug  = $this->language->get('text_title');
			$debug .= ' | FAILED TO INITIALIZE';

			if ($this->getField('status')) {
				$debug .= ' | ExtensionStatus: ENABLED';
			} else {
				$debug .= ' | ExtensionStatus: DISABLED';
			}

			if ($this->cart->hasProducts()) {
				$debug .= ' | ProductsInCart: EMPTY';
			} else {
				$debug .= ' | ProductsInCart: ' . count($this->cart->hasProducts()) . ' FOUND';
			}

			$this->writeDebug($debug);
		}
	}

	private function getField($field) {
		$key = $this->config->get($this->extension . '_' . $field);

		if (is_string($key) && strpos($key, 'a:') === 0) {
			$value = unserialize($key);
		} else {
			$value = $key;
		}

		return $value;
	}

	private function getQuoteData($data) {
		$text = $data['cost']
			? $this->currency->format($this->tax->calculate($data['cost'], $data['tax_class_id'], $this->config->get('config_tax')))
			: $this->language->get('text_free');

		return array(
			'id'		   => $this->extension . '.' . $this->extension,
			'code'		   => $this->extension . '.' . $this->extension,
			'title'        => $data['title'],
			'cost'         => $data['cost'],
			'value'        => $data['cost'],
			'text'         => $text,
			'sort_order'   => $data['sort_order'],
			'tax_class_id' => $data['tax_class_id'],
		);
	}

	private function writeDebug($debug) {
		$write 	= date('Y-m-d h:i:s');
		$write .= ' - ';
		$write .= $debug;
		$write .= "\n";

		$file	= DIR_LOGS . $this->extension . '.txt';

		file_put_contents ($file, $write, FILE_APPEND);
	}
}
?>
