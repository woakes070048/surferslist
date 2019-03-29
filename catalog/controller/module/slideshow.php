<?php
class ControllerModuleSlideshow extends Controller {
	protected function index($setting) {
		static $module = 0;

		$server = $this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url');

		$limit = !empty($setting['limit']) ? $setting['limit'] : 1;
		$scroll = !empty($setting['scroll']) ? $setting['scroll'] : 1;
		$layout = $setting['layout_id'] == 1 ? 'home' : 'other';

		$options_slick = array(
			'zIndex' => 198,
			'infinite' => true,
			'arrows' => true,
			'dots' => false,
			'initialSlide' => 0,
			'prevArrow' => '<a class="slideshow-prev"><i class="fa fa-angle-left"></i></a>',
			'nextArrow' => '<a class="slideshow-next"><i class="fa fa-angle-right"></i></a>'
		);

		if ($layout == 'home') {
			$options_slick = array_merge($options_slick, array(
				'slidesToShow' => 1,
				'slidesToScroll' => 1,
				'autoplay' => true,
				'autoplaySpeed' => 5000,
				'pauseOnHover' => true,
				'speed' => 500
				// 'fade' => true
			));
		} else {
			$options_slick = array_merge($options_slick, array(
				'centerMode' => true,
				'centerPadding' => '30px',
				'respondTo' => 'slider',
				'responsive' => array(
					array(
						'breakpoint' => 1024,
						'settings' => array(
							'slidesToShow' => $limit,
							'slidesToScroll' => $scroll
						)
					),
					array(
						'breakpoint' => 600,
						'settings' => array(
							'slidesToShow' => $limit - 1,
							'slidesToScroll' => $scroll - 1
						)
					),
					array(
						'breakpoint' => 400,
						'settings' => array(
							'slidesToShow' => 1,
							'slidesToScroll' => 1
						)
					)
				)
			));
		}

		$cache = md5(http_build_query($setting));

		$this->data['banners'] = $this->cache->get('banner.module.slideshow.' . (int)$this->config->get('config_language_id') . '.' . $cache);

		if ($this->data['banners'] === false) {
			$this->load->model('design/banner');
			$this->load->model('tool/image');

			$this->data['banners'] = array();

			if (isset($setting['banner_id'])) {
				$results = $this->model_design_banner->getBanner($setting['banner_id']);

				foreach ($results as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height'], 'autocrop');
						// $retina = $this->model_tool_image->resize($result['image'], $setting['width'] * 2, $setting['height'] * 2, 'autocrop');
						//list($width, $height, $type, $attr) = getimagesize($image);
						// $imageSize = getimagesize(dirname(DIR_IMAGE) . parse_url($image, PHP_URL_PATH));

						switch(substr($result['sort_order'], -1, 1)) {
							case "1":
						        $position = "top";
						        break;
						    case "5":
						        $position = "middle";
						        break;
						    case "9":
						        $position = "bottom";
						        break;
						    default:
						        $position = "bottom";
						}

						$this->data['banners'][] = array(
							'title' => $result['title'],
							'link'  => $result['link'],
							'image' => $image,
							'sort_order' => $result['sort_order'],
							'position' => $position
							// 'retina' => $retina,
							// 'width' => $setting['width'],
							// 'height' => $setting['height'],
							// 'size' => $imageSize[3]
						);
					}
				}
			}

			$this->cache->set('banner.module.slideshow.' . (int)$this->config->get('config_language_id') . '.' . $cache, $this->data['banners'], 60 * 60 * 24 * 30); // 1 month cache expiration
		}

		$this->data['module'] = $module++;

		$this->data['base'] = $server;
		$this->data['options'] = $options_slick;
		$this->data['position'] = $setting['position'];  // content_top/bottom, column_left/right
		$this->data['width'] = $setting['width'];
		$this->data['height'] = $setting['height'];
		$this->data['limit'] = $limit;
		$this->data['scroll'] = $scroll;
		$this->data['layout'] = $layout;

		$this->document->addScript('catalog/view/root/slick/slick.min.js');

		$this->template = 'template/module/slideshow.tpl';

		$this->render();
	}
}

