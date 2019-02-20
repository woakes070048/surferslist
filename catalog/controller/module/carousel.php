<?php
class ControllerModuleCarousel extends Controller {
	protected function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$banners = array();

		$cache = md5(http_build_query($setting));

		$banners = $this->cache->get('banner.module.carousel.' . (int)$this->config->get('config_language_id') . $cache);

		if ($banners === false) {
			$banners = array();

			$results = $this->model_design_banner->getBanner($setting['banner_id']);

			foreach ($results as $result) {
				$banners[] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'], 'autocrop') // fwch
				);
			}

			$this->cache->set('banner.module.carousel.' . (int)$this->config->get('config_language_id') . '.' . $cache, $banners, 60 * 60 * 24 * 30); // 1 month cache expiration
		}

		if (count($banners) > 1) {
			$this->document->addScript('catalog/view/root/jcarousel/js/jquery.jcarousel.min.js');
			// $this->document->addStyle('catalog/view/root/jcarousel/css/carousel.css'); // moved to main stylesheet
		}

		$this->data['banners'] = $banners;

		$this->data['limit'] = $setting['limit'];
		$this->data['scroll'] = $setting['scroll'];
		$this->data['position'] = $setting['position'];  // content_top/bottom, column_left/right
		$this->data['width'] = $setting['width'];
		$this->data['height'] = $setting['height'];

		$this->data['module'] = $module++;

		$this->template = '/template/module/carousel.tpl';
		$this->render();
	}
}
?>
