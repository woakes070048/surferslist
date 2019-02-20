<?php
class ControllerModuleBestSeller extends Controller {
	protected function index($setting) {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('module/bestseller')
		);

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$image_width = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? $this->config->get('config_image_product_width') : $this->config->get('config_image_additional_width');
		$image_height = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? $this->config->get('config_image_product_height') : $this->config->get('config_image_additional_height');

		$this->data['position'] = $setting['position'];

		$this->data['products'] = array();

		// $results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
		$results = $this->model_catalog_product->getPopularProducts($setting['limit']);

		foreach ($results as $result) {
			// adds to $this->data['products'] array
			require(DIR_APPLICATION . 'controller/module//listing_result.inc.php');
		}

		// filter_ids
		$url = '';

		foreach ($this->data['products'] as $listing) {
			$url .= $listing['product_id'] . ',';
		}

		$this->data['more'] = $this->url->link('product/allproducts/more', 'module=true&bestseller=true&filter_listings=' . rtrim($url, ','), 'SSL');

		$this->template = '/template/module/bestseller.tpl';

		$this->render();
	}
}
?>
