<?php
class ControllerModuleSpecial extends Controller {
	protected function index($setting) {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/special')
		);

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$image_width = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? $this->config->get('config_image_product_width') : $this->config->get('config_image_additional_width');
		$image_height = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? $this->config->get('config_image_product_height') : $this->config->get('config_image_additional_height');

		$this->data['position'] = $setting['position'];

		$this->data['products'] = array();

		$sort = 'random'; // $this->config->get('apac_products_sort_default') ? $this->config->get('apac_products_sort_default') : 'p.date_added'; // 'pd.name', 'random', 'p.date_added'
		$order = (($sort == 'p.date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending

		$data = array(
			'filter_country_id' => (isset($this->session->data['shipping_country_id']) ? $this->session->data['shipping_country_id'] : ''),
			'sort'  => $sort,
			'order' => $order,
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_catalog_product->getProductSpecials($data);

		foreach ($results as $result) {
			// adds to $this->data['products'] array
			require(DIR_APPLICATION . 'controller/module/listing_result.inc.php');
		}

		// $product_total = $this->model_catalog_product->getTotalProductSpecials($data);

		// filter_ids
		$url = '';

		foreach ($this->data['products'] as $listing) {
			$url .= $listing['product_id'] . ',';
		}

		$this->data['more'] = $this->url->link('product/allproducts/more', 'module=true&special=true&filter_listings=' . rtrim($url, ','), 'SSL');


		$this->template = '/template/module/special.tpl';

		$this->render();
	}
}
?>
