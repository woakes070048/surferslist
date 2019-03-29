<?php
class Cart {
	private $config;
	private $db;
	private $data = array();

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');
		$this->tax = $registry->get('tax');
		$this->weight = $registry->get('weight');

		if (!isset($this->session->data['cart']) || !is_array($this->session->data['cart'])) {
			$this->session->data['cart'] = array();
		}
	}

	public function getProducts() {
		if (!$this->data) {
			$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

			foreach ($this->session->data['cart'] as $key => $quantity) {
				$product = explode(':', $key);
				$product_id = $product[0];
				$stock = true;

				// Options
				$options = isset($product[1]) ? unserialize(base64_decode($product[1])) : array();

				$cart_product_quantity = 0;

				foreach ($this->session->data['cart'] as $key_2 => $quantity_2) {
					$product_2 = explode(':', $key_2);

					if ($product_2[0] == $product_id) {
						$cart_product_quantity += $quantity_2;
					}
				}

				$sql_product = "
					SELECT p.product_id
					, pd.name
					, p.model
					, p.size
					, p.year
					, p.manufacturer_id
					, p.shipping
					, p.image
					, p.minimum
					, p.subtract
					, p.points
					, p.tax_class_id
					, p.weight
					, p.weight_class_id
					, p.length
					, p.length_class_id
					, p.width
					, p.height
					, p.price
					, p.quantity
					, pm.customer_id AS member_customer_id
					, m.name AS manufacturer
					, m.image AS manufacturer_image
					, cma.member_account_id AS member_id
					, cma.member_account_name AS member
					, cma.member_commission_rate
					, cma.member_account_image AS m_image
					, (SELECT price
						FROM " . DB_PREFIX . "product_discount pd2
						WHERE pd2.product_id = p.product_id
						AND pd2.customer_group_id = '" . (int)$customer_group_id . "'
						AND pd2.quantity <= '" . (int)$cart_product_quantity . "'
						AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW()))
						ORDER BY pd2.quantity DESC, pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount
					, (SELECT price
						FROM " . DB_PREFIX . "product_special ps
						WHERE ps.product_id = p.product_id
						AND ps.customer_group_id = '" . (int)$customer_group_id . "'
						AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
						ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special
					, (SELECT points
						FROM " . DB_PREFIX . "product_reward pr
						WHERE pr.product_id = p.product_id
						AND customer_group_id = '" . (int)$customer_group_id . "') AS reward
					FROM " . DB_PREFIX . "product p
					LEFT JOIN " . DB_PREFIX . "product_member pm ON (p.product_id = pm.product_id)
					LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
						AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
					LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)
					LEFT JOIN " . DB_PREFIX . "customer_member_account cma ON (pm.member_account_id = cma.member_account_id)
					WHERE p.product_id = '" . (int)$product_id . "'
					AND p.date_available <= NOW()
					AND p.date_expiration >= NOW()
					AND p.member_approved = '1'
				";

				if (!isset($this->session->data['manual'])) {
					$sql_product .= "
						AND p.status = '1'
					";
				}

				$product_query = $this->db->query($sql_product);

				if ($product_query->num_rows) {
					$option_price = 0;
					$option_points = 0;
					$option_weight = 0;

					$option_data = array();

					foreach ($options as $product_option_id => $option_value) {
						$option_query = $this->db->query("
							SELECT po.product_option_id
							, po.option_id
							, od.name
							, o.type
							FROM " . DB_PREFIX . "product_option po
							LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id)
							LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id)
								AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'
							WHERE po.product_option_id = '" . (int)$product_option_id . "'
							AND po.product_id = '" . (int)$product_id . "'
						");

						if ($option_query->num_rows) {
							if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio' || $option_query->row['type'] == 'image') {
								$option_value_query = $this->db->query("
									SELECT pov.option_value_id
									, ovd.name, pov.quantity
									, pov.subtract
									, pov.price
									, pov.price_prefix
									, pov.points
									, pov.points_prefix
									, pov.weight
									, pov.weight_prefix
									FROM " . DB_PREFIX . "product_option_value pov
									LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id)
									LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id)
									WHERE pov.product_option_value_id = '" . (int)$option_value . "'
									AND pov.product_option_id = '" . (int)$product_option_id . "'
									AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'
								");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $option_value,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'option_value'            => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								} else {
									$this->remove($key);
									continue 2;
								}
							} elseif ($option_query->row['type'] == 'checkbox' && is_array($option_value)) {
								foreach ($option_value as $product_option_value_id) {
									$option_value_query = $this->db->query("
										SELECT pov.option_value_id
										, ovd.name
										, pov.quantity
										, pov.subtract
										, pov.price
										, pov.price_prefix
										, pov.points
										, pov.points_prefix
										, pov.weight
										, pov.weight_prefix
										FROM " . DB_PREFIX . "product_option_value pov
										LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id)
										LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id)
											AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'
										WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "'
										AND pov.product_option_id = '" . (int)$product_option_id . "'
									");

									if ($option_value_query->num_rows) {
										if ($option_value_query->row['price_prefix'] == '+') {
											$option_price += $option_value_query->row['price'];
										} elseif ($option_value_query->row['price_prefix'] == '-') {
											$option_price -= $option_value_query->row['price'];
										}

										if ($option_value_query->row['points_prefix'] == '+') {
											$option_points += $option_value_query->row['points'];
										} elseif ($option_value_query->row['points_prefix'] == '-') {
											$option_points -= $option_value_query->row['points'];
										}

										if ($option_value_query->row['weight_prefix'] == '+') {
											$option_weight += $option_value_query->row['weight'];
										} elseif ($option_value_query->row['weight_prefix'] == '-') {
											$option_weight -= $option_value_query->row['weight'];
										}

										if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
											$stock = false;
										}

										$option_data[] = array(
											'product_option_id'       => $product_option_id,
											'product_option_value_id' => $product_option_value_id,
											'option_id'               => $option_query->row['option_id'],
											'option_value_id'         => $option_value_query->row['option_value_id'],
											'name'                    => $option_query->row['name'],
											'option_value'            => $option_value_query->row['name'],
											'type'                    => $option_query->row['type'],
											'quantity'                => $option_value_query->row['quantity'],
											'subtract'                => $option_value_query->row['subtract'],
											'price'                   => $option_value_query->row['price'],
											'price_prefix'            => $option_value_query->row['price_prefix'],
											'points'                  => $option_value_query->row['points'],
											'points_prefix'           => $option_value_query->row['points_prefix'],
											'weight'                  => $option_value_query->row['weight'],
											'weight_prefix'           => $option_value_query->row['weight_prefix']
										);
									} else {
										$this->remove($key);
										continue 3;
									}
								}
							} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => '',
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => '',
									'name'                    => $option_query->row['name'],
									'option_value'            => $option_value,
									'type'                    => $option_query->row['type'],
									'quantity'                => '',
									'subtract'                => '',
									'price'                   => '',
									'price_prefix'            => '',
									'points'                  => '',
									'points_prefix'           => '',
									'weight'                  => '',
									'weight_prefix'           => ''
								);
							}
						} else {
							$this->remove($key);
							continue 2;
						}
					}

					$price = $product_query->row['special']
						? $product_query->row['special']
						: ($product_query->row['discount'] ? $product_query->row['discount'] : $product_query->row['price']);

					$reward = $product_query->row['reward'] ? $product_query->row['reward'] : 0;

					// Downloads (disabled)
					$download_data = array();

					// $download_query = $this->db->query("
					// 	SELECT *
					// 	FROM " . DB_PREFIX . "product_to_download p2d
					// 	LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id)
					// 	LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id)
					// 	WHERE p2d.product_id = '" . (int)$product_id . "'
					// 	AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'
					// ");
					//
					// foreach ($download_query->rows as $download) {
					// 	$download_data[] = array(
					// 		'download_id' => $download['download_id'],
					// 		'name'        => $download['name'],
					// 		'filename'    => $download['filename'],
					// 		'mask'        => $download['mask'],
					// 		'remaining'   => $download['remaining']
					// 	);
					// }

					// Stock
					if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $quantity) || ($product_query->row['quantity'] < $cart_product_quantity)) {
						$stock = false;
					}

					$this->data[$key] = array(
						'key'             => $key,
						'product_id'      => $product_query->row['product_id'],
						'name'            => $product_query->row['name'],
						'model'           => $product_query->row['model'],
						'size'            => $product_query->row['size'],
						'year'            => $product_query->row['year'],
						'manufacturer_id' => $product_query->row['manufacturer_id'],
						'manufacturer'    => $product_query->row['manufacturer'],
						'manufacturer_image'       => $product_query->row['manufacturer_image'],
						'member_id'       => $product_query->row['member_id'],
						'member_customer_id' => $product_query->row['member_customer_id'],
						'member'          => $product_query->row['member'],
						'member_commission_rate' => $product_query->row['member_commission_rate'],
						'shipping'        => $product_query->row['shipping'],
						'image'           => $product_query->row['image'],
						'option'          => $option_data,
						'download'        => $download_data,
						'quantity'        => $quantity,
						'available'       => $product_query->row['quantity'],
						'minimum'         => $product_query->row['minimum'],
						'subtract'        => $product_query->row['subtract'],
						'stock'           => $stock,
						'price'           => ($price + $option_price),
						'total'           => ($price + $option_price) * $quantity,
						'reward'          => $reward * $quantity,
						'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $quantity : 0),
						'tax_class_id'    => $product_query->row['tax_class_id'],
						'weight'          => ($product_query->row['weight'] + $option_weight) * $quantity,
						'weight_class_id' => $product_query->row['weight_class_id'],
						'length'          => $product_query->row['length'],
						'width'           => $product_query->row['width'],
						'height'          => $product_query->row['height'],
						'length_class_id' => $product_query->row['length_class_id']
					);
				} else {
					$this->remove($key);
				}
			}
		}

		return $this->data;
	}

	public function add($product_id, $qty = 1, $option = array()) {
		if (!$option || !is_array($option)) {
			$key = (int)$product_id;
		} else {
			$key = (int)$product_id . ':' . base64_encode(serialize($option));
		}

		if ((int)$qty && ((int)$qty > 0)) {
			if (!isset($this->session->data['cart'][$key])) {
				$this->session->data['cart'][$key] = (int)$qty;
			} else {
				$this->session->data['cart'][$key] += (int)$qty;
			}
		}

		$this->data = array();
	}

	public function update($key, $qty) {
		if ((int)$qty && ((int)$qty > 0) && isset($this->session->data['cart'][$key])) {
			$this->session->data['cart'][$key] = (int)$qty;
		} else {
			$this->remove($key);
		}

		$this->data = array();
	}

	public function remove($key) {
		if (isset($this->session->data['cart'][$key])) {
			unset($this->session->data['cart'][$key]);
		}

		$this->data = array();
	}

	public function clear() {
		$this->session->data['cart'] = array();

		if (isset($this->session->data['customer_id'])) {
			$this->db->query("
				UPDATE " . DB_PREFIX . "customer
				SET cart = ''
				WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "'
			");
		}

		$this->data = array();
	}

	public function getWeight() {
		$weight = 0;

		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}

		return $weight;
	}

	public function getWeightsForPackages() {
		$weight = array();

    	foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				if (isset($weight[$product['member_customer_id']])) {
					$weight[$product['member_customer_id']] += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
				} else {
					$weight[$product['member_customer_id']] = $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
				}
			}
		}

		return $weight;
	}

	public function getCubicDimensionsForPackages() {
		$dimensions = array();

		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				if (isset($dimensions[$product['member_customer_id']])) {
					$dimensions[$product['member_customer_id']] += $this->length->convert(($product['length'] * $product['width'] * $product['height']) * $product['quantity'], $product['length_class_id'], $this->config->get('config_length_class_id'));
				} else {
					$dimensions[$product['member_customer_id']] = $this->length->convert(($product['length'] * $product['width'] * $product['height']) * $product['quantity'], $product['length_class_id'], $this->config->get('config_length_class_id'));
				}
			}
		}

		return $dimensions;
	}

	public function getPackages() {
		$packages = array();

		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				if (isset($packages[$product['member_customer_id']]['weight'])) {
					$packages[$product['member_customer_id']]['weight'] += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
				} else {
					$packages[$product['member_customer_id']]['weight'] = $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
				}

				if (isset($packages[$product['member_customer_id']]['dimensions'])) {
					$packages[$product['member_customer_id']]['dimensions']['max_width'] = $this->length->convert(max($product['width'], $packages[$product['member_customer_id']]['dimensions']['max_width']), $product['length_class_id'], $this->config->get('config_length_class_id'));
					$packages[$product['member_customer_id']]['dimensions']['max_length'] = $this->length->convert(max($product['length'], $packages[$product['member_customer_id']]['dimensions']['max_length']), $product['length_class_id'], $this->config->get('config_length_class_id'));
					$packages[$product['member_customer_id']]['dimensions']['max_height'] = $this->length->convert(max($product['height'], $packages[$product['member_customer_id']]['dimensions']['max_height']), $product['length_class_id'], $this->config->get('config_length_class_id'));
					$packages[$product['member_customer_id']]['dimensions']['volume'] += $this->length->convert(($product['length'] * $product['width'] * $product['height']) * $product['quantity'], $product['length_class_id'], $this->config->get('config_length_class_id'));  // use round float
				} else {
					$packages[$product['member_customer_id']]['dimensions']['max_width'] = $this->length->convert($product['width'], $product['length_class_id'], $this->config->get('config_length_class_id')); // length & width required by Member
					$packages[$product['member_customer_id']]['dimensions']['max_length'] = $this->length->convert($product['length'], $product['length_class_id'], $this->config->get('config_length_class_id'));
					$packages[$product['member_customer_id']]['dimensions']['max_height'] = $this->length->convert($product['height'], $product['length_class_id'], $this->config->get('config_length_class_id')); // may be zero
					$packages[$product['member_customer_id']]['dimensions']['volume'] = $this->length->convert(($product['length'] * $product['width'] * $product['height']) * $product['quantity'], $product['length_class_id'], $this->config->get('config_length_class_id'));
				}
			}
		}

		return $packages;
	}

	public function getSubTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $product['total'];
		}

		return $total;
	}

	public function getTaxes() {
		$tax_data = array();

		foreach ($this->getProducts() as $product) {
			if ($product['tax_class_id']) {
				$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
		}

		return $tax_data;
	}

	public function getTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
		}

		return $total;
	}

	public function countProducts() {
		$product_total = 0;

		$products = $this->getProducts();

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}

		return $product_total;
	}

	public function hasProducts() {
		return count($this->session->data['cart']);
	}

	public function hasStock() {
		$stock = true;

		foreach ($this->getProducts() as $product) {
			if (!$product['stock']) {
				$stock = false;
			}
		}

		return $stock;
	}

	public function hasShipping() {
		$shipping = false;

		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				$shipping = true;

				break;
			}
		}

		return $shipping;
	}

	public function hasDownload() {
		$download = false;

		foreach ($this->getProducts() as $product) {
			if ($product['download']) {
				$download = true;

				break;
			}
		}

		return $download;
	}
}
