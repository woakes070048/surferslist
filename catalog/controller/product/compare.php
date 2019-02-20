<?php
class ControllerProductCompare extends Controller {
	public function index() {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/compare')
		);

		$this->load->model('catalog/product');
		$this->load->model('localisation/zone');
		$this->load->model('localisation/country');
		$this->load->model('tool/image');

		if (!isset($this->session->data['compare'])) {
			$this->session->data['compare'] = array();
		}

		if (isset($this->request->get['remove'])) {
			$key = array_search($this->request->get['remove'], $this->session->data['compare']);

			if ($key !== false) {
				unset($this->session->data['compare'][$key]);
			}

			$this->session->data['success'] = $this->language->get('text_remove');

			$this->redirect($this->url->link('product/compare', '', 'SSL'));
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));

		if ($this->config->get('apac_status') && $this->config->get('apac_categories_status') && $this->config->get('apac_categories_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_categories'), $this->url->link('product/allcategories'));
		}

		if ($this->config->get('apac_status') && $this->config->get('apac_products_status') && $this->config->get('apac_products_breadcrumb')) {
			$this->addBreadcrumb($this->language->get('text_all_products'), $this->url->link('product/allproducts'));
		}

		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('product/compare'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$url = '';

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$products = array();

		$this->data['products'] = array();

		$this->data['attribute_groups'] = array();

		foreach ($this->session->data['compare'] as $key => $product_id) {
			$result = $this->model_catalog_product->getProduct($product_id);

			if ($result) {
				$price = false;
				$special = false;
				$salebadges = false;
				$savebadges = false;
				$tax = false;
				$attribute_data = array();

				$attribute_groups = $this->model_catalog_product->getProductAttributes($product_id);

				foreach ($attribute_groups as $attribute_group) {
					$this->data['attribute_groups'][$attribute_group['attribute_group_id']]['name'] = $attribute_group['name'];

					foreach ($attribute_group['attribute'] as $attribute) {
						$attribute_data[$attribute['attribute_id']] = $attribute['text'];
						$this->data['attribute_groups'][$attribute_group['attribute_group_id']]['attribute'][$attribute['attribute_id']]['name'] = $attribute['name'];
					}
				}

			    if (!$this->config->get('config_customer_price') || $this->customer->isLogged()) {
			        $price = $result['price'] != 0
			            ? $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')))
			            : $this->language->get('text_free');
			    }

			    if ((float)$result['special']) {
			        $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
			        $salebadges = round((($result['price'] - $result['special']) / $result['price']) * 100, 0);
			        $savebadges = $this->currency->format(($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))) - ($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'))));
			    }

			    if ($this->config->get('config_tax')) {
			        $tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
			    }

				$thumb = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height'), 'autocrop');
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'), 'autocrop');

				$location_zone = $this->model_localisation_zone->getZone($result['zone_id']);
				$location_country = $this->model_localisation_country->getCountry($result['country_id']);

			    $result_product_data = array(
			        'href'              => $this->url->link('product/product', 'product_id=' . $result['product_id'], 'SSL'),
			        'product_id'        => $result['product_id'],
			        'manufacturer_id'   => $result['manufacturer_id'],
			        'manufacturer'      => $result['manufacturer'],
			        'manufacturer_image' => !empty($result['manufacturer_image']) && $result['manufacturer_id'] != 1 ? $this->model_tool_image->resize($result['manufacturer_image'], 100, 40, "fh") : false,
			        'manufacturer_href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'], 'SSL'),
			        'customer_id'       => $result['customer_id'],
			        'member_id'         => $result['member_id'],
			        'member'            => isset($result['member']) ? $result['member'] : '',
			    	'member_href'       => $this->url->link('product/member/info', 'member_id=' . $result['member_id'], 'SSL'),
			    	'member_contact'    => $this->url->link('information/contact', 'contact_id=' . $result['customer_id'], 'SSL'),
			        'type_id'           => $result['type_id'],
			    	'type'              => $result['type_id'] == 1 ? $this->language->get('text_buy_now') : ($result['type_id'] == 0 ? $this->language->get('text_classified') : $this->language->get('text_shared')),
			        'name'              => $result['name'],
			        'model'             => $result['model'],
			        'size'              => $result['size'],
			        'year'              => $result['year'],
			        'thumb'             => $thumb,
			        'image'             => $image,
			        'description'       => utf8_substr(remove_links(preg_replace('/\s+/', ' ', strip_tags_decode($result['description']))), 0, 80) . $this->language->get('text_ellipses'),
			        'quantity'          => $result['quantity'],
			        'price'             => $price,
			        'price_value'       => $result['price'],
			        'special'           => $special,
			        'special_value'     => $result['special'],
			        'salebadges'        => $salebadges,
			        'savebadges'        => $savebadges,
			        'featured'          => $result['featured'],
			        'tax'               => $tax,
			        'location'          => isset($result['location']) ? $result['location'] : '',
			        'location_href'     => isset($result['country_id']) && isset($result['zone_id']) ? $this->url->link('product/search', 'country=' . $result['country_id'] . '&state=' . $result['zone_id'], 'SSL') : '',
			        'zone_id'           => isset($result['zone_id']) ? $result['zone_id'] : '',
			    	'location_zone'     => !empty($location_zone) ? $location_zone['name'] : '',
			        'country_id'        => isset($result['country_id']) ? $result['country_id'] : '',
			    	'location_country'  => !empty($location_country) ? $location_country['iso_code_3'] : '',
			    	'remove'            => $this->url->link('product/compare', 'remove=' . $result['product_id'], 'SSL')
			    );

				$this->data['products'][$result['product_id']] = $result_product_data;
			} else {
				unset($this->session->data['compare'][$key]);
			}
		}

		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('product/allproducts', '', 'SSL');
		$this->data['search'] = $this->url->link('product/search', '', 'SSL');
		$this->data['reset'] = $this->url->link('product/featured', '', 'SSL');
		$this->data['continue'] = $this->url->link('common/home', '', 'SSL');

		$this->template = '/template/product/compare.tpl';

		$this->children = array(
			'common/notification',
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}
}
?>
