<?php
class ControllerModuleBestSeller extends Controller {
	protected function index($setting) {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('module/bestseller')
		);

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$this->data['position'] = $setting['position'];

		$listings = array();

		$results = $this->model_catalog_product->getPopularProducts($setting['limit']);  // getBestSellerProducts

		foreach ($results as $result) {
			$listings[] = $this->getChild('product/data/info', $result);

			$url .= $result['product_id'] . ',';
		}

		$this->data['more'] = $this->url->link('ajax/product/more', 'module=true&bestseller=true&filter_listings=' . rtrim($url, ','));

		$this->data['products'] = $this->getChild('product/data/list_module', array('products' => $listings, 'position' => $setting['position']));

		$this->template = '/template/module/bestseller.tpl';

		$this->render();
	}
}
?>
