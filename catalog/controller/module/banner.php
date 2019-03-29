<?php
class ControllerModuleBanner extends Controller {
	protected function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		$banners = array();
		$options = array();

		$cache = md5(http_build_query($setting));

		$banners = $this->cache->get('banner.module.banner.' . (int)$this->config->get('config_language_id') . '.' . $cache);

		if ($banners === false) {
			$banners = array();

			$results = $this->model_design_banner->getBanner($setting['banner_id']);

			foreach ($results as $result) {
				$banners[] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'image' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'], 'autocrop')
				);
			}

			$this->cache->set('banner.module.banner.' . (int)$this->config->get('config_language_id') . '.' . $cache, $banners, 60 * 60 * 24 * 30); // 1 month cache expiration
		}

		if (count($banners) > 1) {
			$options = array(
                'timeout' => 6000,
                'speed' => 900
                // 'fx' => 'custom',
                // 'cssBefore' => array( 'top' => (int)$setting['height'] + 30 ),
                // 'animIn' =>  array( 'top' => 0 ),
                // 'animOut' => array( 'top' => -(int)$setting['height'] - 30 )
			);

			$this->document->addScript('catalog/view/root/javascript/jquery.cycle.lite.js');
		}

		$this->data['banners'] = $banners;
		$this->data['options'] = $options;
		$this->data['position'] = $setting['position'];  // content_top/bottom, column_left/right
		$this->data['width'] = $setting['width'];
		$this->data['height'] = $setting['height'];

		$this->data['module'] = $module++;

		$this->template = 'template/module/banner.tpl';
		$this->render();
	}
}

