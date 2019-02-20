<?php
class ControllerModuleLatest extends Controller {
	protected function index($setting) {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('module/latest')
		);

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$image_width = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? $this->config->get('config_image_product_width') : $this->config->get('config_image_additional_width');
		$image_height = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? $this->config->get('config_image_product_height') : $this->config->get('config_image_additional_height');
		$image_crop = 'fw';

		$this->data['position'] = $setting['position'];

		$filter_country_id = isset($this->session->data['shipping_country_id']) ? $this->session->data['shipping_country_id'] : '';

		$featured_product_ids = explode(',', $this->config->get('featured_product'));

		$cache = md5(http_build_query(array_merge($setting, $featured_product_ids)));

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$this->data['products'] = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$filter_country_id . '.' . (int)$customer_group_id . '.' . $cache);

		if ($this->data['products'] === false) {
			$this->data['products'] = array();

			// latest
			$sort_latest = 'p.date_added'; // $this->config->get('apac_products_sort_default') ? $this->config->get('apac_products_sort_default') : 'p.date_added'; // 'pd.name', 'random', 'p.date_added'
			$order_latest = (($sort_latest == 'p.date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
			$limit_latest = !empty($setting['limit']) ? $setting['limit'] : 5;

			$data = array(
				'filter_member_exists' => true,
				'filter_country_id' => $filter_country_id,
				'sort'  => $sort_latest,
				'order' => $order_latest,
				'start' => 0,
				'limit' => $limit_latest
			);

			$results_latest = $this->model_catalog_product->getProducts($data);

			// add each new latest listing to $this->data['products']
			foreach ($results_latest as $result) {
				require(DIR_APPLICATION . 'controller/module/listing_result.inc.php');
			}

			// featured
			$featured_products = array();
			$count_featured = 0;
			$limit_featured = !empty($setting['limit']) ? $setting['limit'] * 2 : 10;

			// get ALL featured listings
			foreach ($featured_product_ids as $product_id) {
				$result = $this->model_catalog_product->getProduct($product_id);

				if ($result && !array_key_exists($result['product_id'], $this->data['products'])) {
					$featured_products[$result['product_id']] = $result;
				}
			}

			// sort featured listings by date added descending
			$sort_order_featured = array();

			foreach ($featured_products as $key => $value) {
				$sort_order_featured[$key] = $value['date_added'];
			}

			array_multisort($sort_order_featured, SORT_DESC, $featured_products);

			// add each new featured listing to $this->data['products']
			foreach ($featured_products as $result) {
				if ($count_featured >= $limit_featured) break;

				require(DIR_APPLICATION . 'controller/module/listing_result.inc.php');

				$count_featured++;
			}

			// specials
			$sort_special = 'p.date_added'; // $this->config->get('apac_products_sort_default') ? $this->config->get('apac_products_sort_default') : 'p.date_added'; // 'pd.name', 'random', 'p.date_added'
			$order_special = (($sort_special == 'p.date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
			$limit_special = !empty($setting['limit']) ? $setting['limit'] : 5;

			$data_special = array(
				'filter_country_id' => $filter_country_id,
				'sort'  => $sort_special,
				'order' => $order_special,
				'start' => 0,
				'limit' => $limit_special
			);

			$results_special = $this->model_catalog_product->getProductSpecials($data_special);

			// add each new special listing to $this->data['products']
			foreach ($results_special as $result) {
				if ($result && !array_key_exists($result['product_id'], $this->data['products'])) {
					require(DIR_APPLICATION . 'controller/module/listing_result.inc.php');
				}
			}

			// order by date_added DESC
			$sort_order = array();

			foreach ($this->data['products'] as $key => $value) {
				$sort_order[$key] = $value['date_added'];
			}

			array_multisort($sort_order, SORT_DESC, $this->data['products']);

			// limit
			$this->data['products'] = array_slice($this->data['products'], 0, $limit_featured + $limit_latest + $limit_special);

			$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$filter_country_id . '.' . (int)$customer_group_id . '.' . $cache, $this->data['products'], 60 * 60); // 1 hr cache expiration
		}

		// filter_ids
		$url = '';

		foreach ($this->data['products'] as $listing) {
			$url .= $listing['product_id'] . ',';
		}

		$this->data['more'] = $this->url->link('product/allproducts/more', 'module=true&filter_listings=' . rtrim($url, ','), 'SSL');

		$this->template = '/template/module/latest.tpl';

		$this->render();
	}
}
?>
