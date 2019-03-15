<?php
class ControllerModuleLatest extends Controller {
	protected function index($setting) {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('module/latest')
		);

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$this->data['position'] = $setting['position'];

		$cache = md5(http_build_query($setting));

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$filter_country_id = isset($this->session->data['shipping_country_id']) ? $this->session->data['shipping_country_id'] : '';

		$listings = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$filter_country_id . '.' . (int)$customer_group_id . '.' . $cache);

		if ($listings === false) {
			$listings = array();

			$limit = !empty($setting['limit']) ? $setting['limit'] : 5;

			// latest
			$data = array(
				'filter_member_exists' => true,
				'filter_country_id' => $filter_country_id,
				'sort'  => 'p.date_added',
				'order' => 'DESC',
				'start' => 0,
				'limit' => $limit
			);

			$results_latest = $this->model_catalog_product->getProducts($data, false);

			foreach ($results_latest as $result) {
				$listings[$result['product_id']] = $this->getChild('product/data/info', $result);
			}

			// featured
			$data = array(
				'filter_listings' => array_keys($listings),
				'filter_country_id' => $filter_country_id,
				'sort'  => 'random',
				'order' => 'ASC',
				'start' => 0,
				'limit' => $limit * 2
			);

			$results_featured = $this->model_catalog_product->getProductFeatured($data, false);

			foreach ($results_featured as $result) {
				$listings[$result['product_id']] = $this->getChild('product/data/info', $result);
			}

			// specials
			$data = array(
				'filter_listings' => array_keys($listings),
				'filter_country_id' => $filter_country_id,
				'sort'  => 'random',
				'order' => 'ASC',
				'start' => 0,
				'limit' => $limit
			);

			$results_special = $this->model_catalog_product->getProductSpecials($data, false);

			// add each new special listing to $listings
			foreach ($results_special as $result) {
				if ($result && !array_key_exists($result['product_id'], $listings)) {
					$listings[$result['product_id']] = $this->getChild('product/data/info', $result);
				}
			}

			// order by date_added DESC
			$sort_order = array();

			foreach ($listings as $key => $value) {
				$sort_order[$key] = $value['date_added'];
			}

			array_multisort($sort_order, SORT_DESC, $listings);

			// limit
			$listings = array_slice($listings, 0, ($limit * 4));

			$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$filter_country_id . '.' . (int)$customer_group_id . '.' . $cache, $listings, 60 * 60); // 1 hr cache expiration
		}

		// filter_ids
		$url = '';

		foreach ($listings as $listing) {
			$url .= $listing['product_id'] . ',';
		}

		$this->data['more'] = $this->url->link('ajax/product/more', 'module=true&filter_listings=' . rtrim($url, ','));

		$this->data['products'] = $this->getChild('product/data/list_module', array('products' => $listings, 'position' => $setting['position']));

		$this->template = '/template/module/latest.tpl';

		$this->render();
	}
}
?>
