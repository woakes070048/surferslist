<?php
class ControllerAjaxCart extends Controller {

    public function add() {
		if (!isset($this->request->post['product_id'])) {
            return false;
		}

        $json = array();

        $product_id = (int)$this->request->post['product_id'];

        $this->load->language('checkout/cart');
        $this->load->model('catalog/product');

        $product_info = $this->model_catalog_product->getProduct($product_id);

        if ($product_info) {
            if (isset($this->request->post['quantity'])) {
                $quantity = (int)$this->request->post['quantity'];
            } else {
                $quantity = $product_info['minimum'] ?: 1;
            }

            if (isset($this->request->post['option'])) {
                $option = array_filter($this->request->post['option']);
            } else {
                $option = array();
            }

            if ($product_info['quantity'] == 0) {
                $json['error'] = sprintf($this->language->get('error_classified'), $this->url->link('information/contact', 'contact_id=' . $product_info['customer_id']));
            } else {
                $product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

                foreach ($product_options as $product_option) {
                    if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
                        $json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
                        $json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']));
                    }
                }

                if ($this->cart->hasProducts()) {
                    $products = $this->cart->getProducts();

                    foreach ($products as $product) {
                        if ($product['product_id'] == $this->request->post['product_id']) {
                            if ($product['quantity'] + $quantity > $product['available']) {
                                $json['error'] = sprintf($this->language->get('error_quantity'), $product['quantity'] + $quantity);
                            }

                            if ($product['quantity'] + $quantity < $product['minimum']) {
                                $json['error'] = sprintf($this->language->get('error_minimum'), $product_info['name'], $product['minimum']);
                            }

                        } else if ($product['member_customer_id'] != $product_info['customer_id']) {
                            $json['error'] = $this->language->get('error_member');
                        }
                    }
                } else {
                    if ($quantity > $product_info['quantity']) {
                        $json['error'] = sprintf($this->language->get('error_quantity'), $quantity);
                    } else if ($quantity < $product_info['minimum']) {
                        $json['error'] = sprintf($this->language->get('error_minimum'), $product_info['name'], $product_info['minimum']);
                    }
                }
            }

            if (!$json) {
                $this->cart->add($this->request->post['product_id'], $quantity, $option);

                $json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

                unset($this->session->data['insurance']);
                unset($this->session->data['shipping_method']);
                unset($this->session->data['shipping_methods']);
                unset($this->session->data['payment_method']);
                unset($this->session->data['payment_methods']);

                // Totals
                $this->load->model('setting/extension');

                $total_data = array();
                $total = 0;
                $taxes = $this->cart->getTaxes();

                // Display prices
                if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $sort_order = array();

                    $results = $this->model_setting_extension->getExtensions('total');

                    foreach ($results as $key => $value) {
                        $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                    }

                    array_multisort($sort_order, SORT_ASC, $results);

                    foreach ($results as $result) {
                        if ($this->config->get($result['code'] . '_status')) {
                            $this->load->model('total/' . $result['code']);

                            $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
                        }
                    }

                    $sort_order = array();

                    foreach ($total_data as $key => $value) {
                        $sort_order[$key] = $value['sort_order'];
                    }

                    array_multisort($sort_order, SORT_ASC, $total_data);
                }

                $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
            }
        }

        $this->response->setOutput(json_encode($json));
    }

    public function shipping() {
        $this->load->language('checkout/cart');

        $json = array();

        if (!$this->cart->hasProducts()) {
            $json['error']['warning'] = $this->language->get('error_product');
        }

        if (!$this->cart->hasShipping()) {
            $json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
        }

        if ($this->request->post['country_id'] == '') {
            $json['error']['country'] = $this->language->get('error_country');
        }

        if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '' || !is_numeric($this->request->post['zone_id'])) {
            $json['error']['zone'] = $this->language->get('error_zone');
        }

        if (!$json) {
            $this->load->model('localisation/country');

            $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

            if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
                $json['error']['postcode'] = $this->language->get('error_postcode');
            }
        }

        if (!$json) {
            $this->tax->setShippingAddress($this->request->post['country_id'], $this->request->post['zone_id']);

            // Default Shipping Address
            $this->session->data['shipping_country_id'] = $this->request->post['country_id'];
            $this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
            $this->session->data['shipping_postcode'] = $this->request->post['postcode'];

            if ($country_info) {
                $country = $country_info['name'];
                $iso_code_2 = $country_info['iso_code_2'];
                $iso_code_3 = $country_info['iso_code_3'];
                $address_format = $country_info['address_format'];
            } else {
                $country = '';
                $iso_code_2 = '';
                $iso_code_3 = '';
                $address_format = '';
            }

            $this->load->model('localisation/zone');

            $zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);

            if ($zone_info) {
                $zone = $zone_info['name'];
                $zone_code = $zone_info['code'];
            } else {
                $zone = '';
                $zone_code = '';
            }

            $address_data = array(
                'firstname'      => '',
                'lastname'       => '',
                'company'        => '',
                'address_1'      => '',
                'address_2'      => '',
                'postcode'       => $this->request->post['postcode'],
                'city'           => '',
                'zone_id'        => $this->request->post['zone_id'],
                'zone'           => $zone,
                'zone_code'      => $zone_code,
                'country_id'     => $this->request->post['country_id'],
                'country'        => $country,
                'iso_code_2'     => $iso_code_2,
                'iso_code_3'     => $iso_code_3,
                'address_format' => $address_format
            );

            $quote_data = array();

            $this->load->model('setting/extension');

            $results = $this->model_setting_extension->getExtensions('shipping');

            foreach ($results as $result) {
                if ($this->config->get($result['code'] . '_status')) {
                    $this->load->model('shipping/' . $result['code']);

                    $quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data);

                    if ($quote) {
                        $quote_data[$result['code']] = array(
                            'title'      => $quote['title'],
                            'quote'      => $quote['quote'],
                            'sort_order' => $quote['sort_order'],
                            'error'      => $quote['error']
                        );
                    }
                }
            }

            $sort_order = array();

            foreach ($quote_data as $key => $value) {
                $sort_order[$key] = $value['sort_order'];
            }

            array_multisort($sort_order, SORT_ASC, $quote_data);

            $this->session->data['shipping_methods'] = $quote_data;

            if ($this->session->data['shipping_methods']) {
                $json['shipping_method'] = $this->session->data['shipping_methods'];
            } else {
                $json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
            }
        }

        $this->response->setOutput(json_encode($json));
    }

}
