<?php
class ControllerModuleSpecial extends Controller {
	protected function index($setting) {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('product/special')
		);

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$this->data['position'] = $setting['position'];

		$listings = array();

		$sort = 'random'; // $this->config->get('apac_products_sort_default') ? $this->config->get('apac_products_sort_default') : 'p.date_added'; // 'pd.name', 'random', 'p.date_added'
		$order = (($sort == 'p.date_added') ? 'DESC' : 'ASC'); // if sorted by date, then show newest first, otherwise sort ascending
		$url = '';

		$data = array(
			'filter_country_id' => (isset($this->session->data['shipping_country_id']) ? $this->session->data['shipping_country_id'] : ''),
			'sort'  => $sort,
			'order' => $order,
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_catalog_product->getProductSpecials($data);

		foreach ($results as $result) {
			$listings[] = $this->getChild('product/data/info', $result);

			$url .= $result['product_id'] . ',';
		}

		$this->data['more'] = $this->url->link('ajax/product/more', 'module=true&special=true&filter_listings=' . rtrim($url, ','), 'SSL');

		$this->data['products'] = $this->getChild('product/data/list_module', array('products' => $listings, 'position' => $setting['position']));

		$this->template = '/template/module/special.tpl';

		$this->render();
	}
}
?>
