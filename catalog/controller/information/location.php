<?php
class ControllerInformationLocation extends Controller {
	public function index() {
		$this->data = $this->load->language('information/location');

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link('information/location'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		// redirect back to url
		$redirect_path = false;

		if (isset($this->request->get['redirect_path'])) {
			$redirect_path = $this->request->get['redirect_path'];
		} else if (isset($this->request->post['redirect_path'])) {
			$redirect_path = $this->request->post['redirect_path'];
		} else if (isset($this->session->data['redirect'])) {
			if (strpos($this->session->data['redirect'], $this->config->get('config_ssl')) === 0) {
				$redirect_path = str_replace($this->config->get('config_ssl'), '', $this->session->data['redirect']);
			} else if (strpos($this->session->data['redirect'], $this->config->get('config_url')) === 0) {
				$redirect_path = str_replace($this->config->get('config_url'), '', $this->session->data['redirect']);
			}

			unset($this->session->data['redirect']);
		}

		$redirect_path = str_replace('&amp;', '&', urldecode($redirect_path));

		if (strpos($redirect_path, '?') !== false) {
			list($redirect_path, $redirect_path_query_params) = explode('?', $redirect_path);

			$redirect_path_query_params = array_filter(explode('&', $redirect_path_query_params), function ($i) {
				return (strpos($i, 'location') === false && strpos($i, 'country') === false && strpos($i, 'zone') === false && strpos($i, 'state') === false);
			});

			if ($redirect_path_query_params) {
				$redirect_path .= '?' . implode('&', $redirect_path_query_params);
			}
		}

		// remove location
		if (isset($this->request->get['location']) && $this->request->get['location'] == 'none') {

			// clear session variables and redirect back
			unset($this->session->data['shipping_country_id']);
			unset($this->session->data['shipping_country_iso_code_3']);
			unset($this->session->data['shipping_zone_id']);
			unset($this->session->data['shipping_location']);
			unset($this->session->data['redirect']);

			$this->session->data['success'] = $this->language->get('text_location_reset');

			if ($redirect_path !== false) {
				$this->redirect($this->config->get('config_ssl') . $redirect_path);
			}
		}

		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');

		$country_id = 0;
		$country_code = '';
		$country_info = array();
		$country_colors = array();
		$country_invalid = false;

		$zone_id = 0;
		$zone_code = '';
		$zone_info = array();
		$zone_colors = array();
		$zones = array();
		$zone_invalid = false;

		$location_name = '';
		$notification = array();
		$success = array();

		// Country
		if (isset($this->request->get['country'])) {
			$country_info = strlen($this->request->get['country']) == 2
				? $this->model_localisation_country->getCountryByISO($this->request->get['country'])
				: false;

			$country_invalid = !$country_info && !empty($this->request->get['country']) ? true : false;
		} else if (isset($this->request->post['country'])) {
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country']);

			$country_invalid = !$country_info && !empty($this->request->post['country']) ? true : false;
		} else if (!empty($this->session->data['shipping_country_id'])) {
			$country_info = $this->model_localisation_country->getCountry($this->session->data['shipping_country_id']);
		} else if (!isset($this->session->data['success'])) {
			$notification[] = sprintf($this->language->get('text_location_none'));
		} /* else {
			$country_code = $this->language->get('text_default_iso_code_2');
			$country_id = $this->config->get('config_country_id');
			$zone_id = $this->config->get('config_zone_id');
			$notification[] = sprintf($this->language->get('text_location_notify'), $this->language->get('text_default_country_name'));
		} */

		if ($country_info) {
			$country_id = $country_info['country_id'];
			$country_code = strtolower($country_info['iso_code_2']);

			if (empty($this->session->data['shipping_country_id']) || $country_info['country_id'] != $this->session->data['shipping_country_id']) {
				// Unset/remove the zone and location when new country is set
				unset($this->session->data['shipping_zone_id']);
				unset($this->session->data['shipping_location']);

				$success[] = sprintf($this->language->get('text_location_success_country'), $country_info['name']);
			} else {
				$notification[] = sprintf($this->language->get('text_location_notify_country'), $country_info['name']);
			}
		} else {
			if (!empty($this->session->data['shipping_country_id'])) {
				$success[] = $this->language->get('text_location_reset');
			} else if ($country_invalid) {
				$this->session->data['warning'] = $this->language->get('text_location_error');
			}
		}

		// Zone
		if ($country_id) {
			$zones = $this->model_localisation_zone->getZonesByCountryId($country_id);

			if (isset($this->request->get['zone']) && $this->request->get['zone'] != 'none') {
				$zone_info = strlen($this->request->get['zone']) <= 3
					? $this->model_localisation_zone->getZoneByISO($this->request->get['zone'], $country_id)
					: false;

				$zone_invalid = !$zone_info && !empty($this->request->get['zone']) ? true : false;;
			} else if (isset($this->request->post['zone'])) {
				$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone']);

				$zone_invalid = !$zone_info && !empty($this->request->post['zone']) ? true : false;;
			} else if (!empty($this->session->data['shipping_zone_id'])) {
				$zone_info = $this->model_localisation_zone->getZone($this->session->data['shipping_zone_id']);
			}

			if ($zone_info && !empty($zone_info['zone_id'])) {
				$zone_id = $zone_info['zone_id'];
				$zone_code = strtolower($zone_info['code']);

				if (empty($this->session->data['shipping_zone_id']) || $zone_info['zone_id'] != $this->session->data['shipping_zone_id']) {
					// Unset/remove the location when new zone is set
					unset($this->session->data['shipping_location']);

					$success[] = sprintf($this->language->get('text_location_success_zone'), $zone_info['name']);
				} else {
					$notification[] = sprintf($this->language->get('text_location_notify_zone'), $zone_info['name']);
				}
			} else {
				if (!empty($this->session->data['shipping_zone_id'])) {
					unset($this->session->data['shipping_location']);

					$success[] = $this->language->get('text_location_success_zone_removed');
				} else if ($zone_invalid) {
					$this->session->data['warning'] = $this->language->get('text_location_error');
				}
			}
		}

		// Location
		if ($country_id && $zone_id) {
			if (isset($this->request->get['location'])) {
				$location_name = strtolower(substr($this->request->get['location'], 0, 20));
			} else if (isset($this->request->post['location'])) {
				$location_name = strtolower(substr($this->request->post['location'], 0, 20));
			} else if (!empty($this->session->data['shipping_location'])) {
				$location_name = $this->session->data['shipping_location'];
			}

			if ($location_name) {
				if (empty($this->session->data['shipping_location']) || $location_name != $this->session->data['shipping_location']) {
					$success[] = sprintf($this->language->get('text_location_success_location'), ucwords($location_name));
				} else {
					$notification[] = sprintf($this->language->get('text_location_notify_location'), ucwords($location_name));
				}
			} else {
				if (!empty($this->session->data['shipping_location'])) {
					$success[] = $this->language->get('text_location_success_location_removed');
				}
			}
		}

		// Set the location (i.e. update session variable)
		$this->session->data['shipping_country_id'] = $country_id ? $country_id : '';
		$this->session->data['shipping_country_iso_code_3'] = $country_id && $country_info ? $country_info['iso_code_3'] : '';
		$this->session->data['shipping_zone_id'] = $country_id && $zone_id ? $zone_id : ''; // $zones[0]['zone_id'];
		// $this->session->data['shipping_zone_iso_code'] = $country_id && $zone_id && $zone_info ? $zone_info['code'] : '';
		$this->session->data['shipping_location'] = $country_id && $zone_id && $location_name ? $location_name : '';

		if ($notification && !$success && !isset($this->session->data['warning'])) {
			$this->session->data['notification'] = implode('<br />', $notification);
		} else if ($success) {
			$this->session->data['success'] = implode('<br />', $success);
		}

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ($redirect_path) {
				$this->redirect($this->config->get('config_ssl') . $redirect_path);
			} else {
				$this->redirect($this->url->link('product/search'));
			}
		}

		// Countries List
		$this->data['countries_select'] = array();

		$this->data['countries_select'][] = array(
			'country_id' => 0,
			'name'       => $this->language->get('text_none'),
			'href'       => $this->url->link('information/location', 'location=none'),
			'iso_code_2' => ''
		);

		$enabled_countries = $this->model_localisation_country->getCountries(1);

		foreach ($enabled_countries as $enabled_country) {
			$country_colors[strtolower($enabled_country['iso_code_2'])] = (strtolower($enabled_country['iso_code_2']) == strtolower($country_code))
				? $this->language->get('color_selected')
				: $this->language->get('color_enabled');

			$this->data['countries_select'][] = array(
				'country_id' => $enabled_country['country_id'],
				'name'       => $enabled_country['name'],
				'href'       => $this->url->link('information/location', 'country=' . strtolower($enabled_country['iso_code_2'])),
				'iso_code_2' => strtolower($enabled_country['iso_code_2'])
			);
		}

		$disabled_countries = $this->model_localisation_country->getCountries(0);

		foreach ($disabled_countries as $disabled_country) {
			$country_colors[strtolower($disabled_country['iso_code_2'])] = $this->language->get('color_disabled');
		}

		// Zones List
		$this->data['zones_select'] = array();

		$this->data['zones_select'][] = array(
			'zone_id' 	 => 0,
			'name'       => $this->language->get('text_none'),
			'href'       => $this->url->link('information/location', 'country=' . $country_code . '&zone=none'),
			'iso_code'   => ''
		);

		foreach ($zones as $zone) {
			$zone_colors[strtolower($zone['code'])] = (strtolower($zone['code']) == strtolower($zone_code))
				? $this->language->get('color_selected')
				: $this->language->get('color_enabled');

			$this->data['zones_select'][] = array(
				'zone_id' 	 => $zone['zone_id'],
				'name'       => $zone['name'],
				'href'       => $this->url->link('information/location', 'country=' . strtolower($zone['country_iso_code_2']) . '&zone=' . strtolower($zone['code'])),
				'iso_code' 	 => strtolower($zone['code'])
			);
		}

		$this->document->setTitle($this->language->get('heading_title'));
		$this->document->setDescription($this->language->get('meta_description'));
		$this->document->setKeywords($this->language->get('meta_keyword'));

		$this->data['help_location_logged'] = sprintf($this->language->get('help_location_logged'), $this->url->link('account/login', '', 'SSL'));
		$this->data['help_location_add'] = sprintf($this->language->get('help_location_add'), $this->url->link('information/contact', 'contact_id=0'));

		$this->data['country_id'] = $country_id;
		$this->data['country_code'] = $country_code;
		$this->data['country_colors'] = $country_colors;
		$this->data['zone_id'] = $zone_id;
		$this->data['zone_code'] = $zone_code;
		$this->data['zone_colors'] = $zone_colors;
		$this->data['location_name'] = ucwords($location_name);
		$this->data['redirect_path'] = $redirect_path;

		$this->data['action'] = $this->url->link('information/location', 'redirect_path=' . urlencode($redirect_path)); // for js, do not add any more query params
		$this->data['reset'] = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode($redirect_path));
		$this->data['listings'] = $this->url->link('product/allproducts');
		$this->data['search'] = $this->url->link('product/search');
		$this->data['continue'] = $this->url->link('common/home');

		$this->document->addStyle('catalog/view/root/jqvmap/jqvmap.min.css');
		$this->document->addScript('catalog/view/root/jqvmap/jquery.vmap.min.js');

		if ($country_code) {
			if ($country_code == 'us') {
				$this->data['country_map'] = 'usa_en';
				$this->document->addScript('catalog/view/root/jqvmap/maps/jquery.vmap.usa.js');
			} else if ($country_code == 'ca') {
				$this->data['country_map'] = 'canada_en';
				$this->document->addScript('catalog/view/root/jqvmap/maps/jquery.vmap.canada.js');
			} else if ($country_code == 'br') {
				$this->data['country_map'] = 'brazil_br';
				$this->document->addScript('catalog/view/root/jqvmap/maps/jquery.vmap.brazil.js');
			// } else if ($country_code == 'fr') {
			// 	$this->data['country_map'] = 'france_fr';
			// 	$this->document->addScript('catalog/view/root/jqvmap/maps/jquery.vmap.france.js');
			// } else if ($country_code == 'de') {
			// 	$this->data['country_map'] = 'germany_en';
			// 	$this->document->addScript('catalog/view/root/jqvmap/maps/jquery.vmap.germany.js');
			// } else if ($country_code == 'gb'
			// 	|| $country_code == 'at'
			// 	|| $country_code == 'be'
			// 	|| $country_code == 'dk'
			// 	|| $country_code == 'gr'
			// 	|| $country_code == 'ie'
			// 	|| $country_code == 'it'
			// 	|| $country_code == 'nl'
			// 	|| $country_code == 'pt'
			// 	|| $country_code == 'es'
			// 	|| $country_code == 'ch'
			// 	|| $country_code == 'ic') {
			// 	$this->data['country_map'] = 'europe_en';
			// 	$this->document->addScript('catalog/view/root/jqvmap/maps/jquery.vmap.europe.js');
			// } else if ($country_code == 'au' || $country_code == 'nz') {
			// 	$this->data['country_map'] = 'australia_en';
			// 	$this->document->addScript('catalog/view/root/jqvmap/maps/continents/jquery.vmap.australia.js');
			// } else if ($country_code == 'cr') {
			// 	$this->data['country_map'] = 'south-america_en';
			// 	$this->document->addScript('catalog/view/root/jqvmap/maps/continents/jquery.vmap.south-america.js');
			// } else if ($country_code == 'hk') {
			// 	$this->data['country_map'] = 'asia_en';
			// 	$this->document->addScript('catalog/view/root/jqvmap/maps/continents/jquery.vmap.asia.js');
			} else {
				$this->data['country_map'] = '';
				$this->document->addScript('catalog/view/root/jqvmap/maps/jquery.vmap.world.js');
			}
		} else {
			$this->data['country_map'] = '';
			$this->document->addScript('catalog/view/root/jqvmap/maps/jquery.vmap.world.js');
		}

		$this->document->addScript('catalog/view/root/javascript/location.js');

		$this->template = 'template/information/location.tpl';

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

