<?php
class ControllerModuleManufacturer extends Controller {
	protected function index($setting) {
		$this->data = $this->load->language('module/manufacturer');

		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$image_width = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? $this->config->get('config_image_product_width') : 210;
		$image_height = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? $this->config->get('config_image_product_height') : 210;
		$image_crop = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? 'fw' : '';
		$limit = $setting['total'];
		$count = 0;

		if ($setting['position'] == 'column_right' || $setting['position'] == 'column_left') {
			$this->document->addScript('catalog/view/root/slick/slick.min.js');
		}

		$options_slick = $this->config->get('config_slick_options') ?: array(
			'zIndex' => 198,
			'infinite' => true,
			'arrows' => true,
			'dots' => false,
			'initialSlide' => 0,
			'prevArrow' => '<a class="slideshow-prev"><i class="fa fa-angle-left"></i></a>',
			'nextArrow' => '<a class="slideshow-next"><i class="fa fa-angle-right"></i></a>',
			'slidesToShow' => 1,
			'slidesToScroll' => 1,
			'autoplay' => true,
			'autoplaySpeed' => 5000,
			'pauseOnHover' => true,
			'speed' => 500,
			'fade' => true
		);

		$this->data['slider_options'] = htmlspecialchars(json_encode($options_slick), ENT_COMPAT);

		$this->data['position'] = $setting['position'];
		$this->data['limit'] = $setting['limit'];
		$this->data['scroll'] = $setting['scroll'];
		$this->data['image_width'] = $image_width;
		$this->data['image_height'] = $image_height;

		$cache = md5(http_build_query($setting));

		$data = array(
			'sort'  => 'product_count',
			'order' => 'DESC',
			'start' => 0,
			'limit' => $limit + 1
		);

		$this->data['manufacturers'] = $this->cache->get('manufacturer.featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if ($this->data['manufacturers'] === false) {
			$this->data['manufacturers'] = array();

			$results = $this->model_catalog_manufacturer->getManufacturers($data);

			foreach ($results as $result) {
				if ($result['manufacturer_id'] == '1' || $count >= $limit) continue; // skip "Other"

				if (!$this->config->get('config_product_count') || $result['product_count'] > 0) {
					$image = $this->model_tool_image->resize($result['image'], $image_width, $image_height, $image_crop);

					$this->data['manufacturers'][] = array(
						'manufacturer_id' 	=> $result['manufacturer_id'],
						'image'   	 		=> $image,
						'name'    	 		=> $result['name'],
						'product_count' 	=> $this->config->get('config_product_count') ? $result['product_count'] : false,
						'text_products' 	=> $this->config->get('config_product_count') ? sprintf($this->language->get('text_products'), $result['product_count']) : '',
						'href'    	 		=> $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
					);

					$count++;
				}
			}

			$this->cache->set('manufacturer.featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $this->data['manufacturers'], 60 * 60 * 24); // 1 day cache expiration
		}

		$this->data['more'] = $this->url->link('product/manufacturer');
		$this->data['text_more'] = $this->language->get('text_view_all') . ' ' . $this->language->get('text_all_manufacturers');

		$this->template = '/template/module/manufacturer.tpl';

		$this->render();
	}
}
?>
