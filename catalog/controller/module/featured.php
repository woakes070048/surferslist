<?php
class ControllerModuleFeatured extends Controller {
	protected function index($setting) {
		$this->data = array_merge(
			$this->load->language('product/common'),
			$this->load->language('module/featured')
		);

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$is_sidebar = ($setting['position'] == 'column_right' || $setting['position'] == 'column_left') ? true : false; // || ($setting['position'] != 'content_top' && $setting['position'] != 'content_bottom')

		$filter_category_id = isset($this->request->get['filter_category_id']) ? $this->request->get['filter_category_id'] : '';

		if (!empty($this->request->get['path']) && !is_array($this->request->get['path'])) {
			$path = explode('_', (string)$this->request->get['path']);
			$filter_category_id = (int)array_pop($path);
		}

		// if (isset($this->request->get['filter_location'])) {
		// 	$filter_location = $this->request->get['filter_location'];
		// } elseif (isset($this->session->data['shipping_location'])) {
		// 	$filter_location = $this->session->data['shipping_location'];
		// } else {
		// 	$filter_location = '';
		// }
        //
		// if (isset($this->request->get['filter_country_id'])) {
		// 	$filter_country_id = $this->request->get['filter_country_id'];
		// } elseif (isset($this->session->data['shipping_country_id'])) {
		// 	$filter_country_id = $this->session->data['shipping_country_id'];
		// } else {
		// 	$filter_country_id = ''; // $this->config->get('config_country_id');
		// }
        //
		// if (isset($this->request->get['filter_zone_id'])) {
		// 	$filter_zone_id = $this->request->get['filter_zone_id'];
		// } elseif (isset($this->session->data['shipping_zone_id'])) {
		// 	$filter_zone_id = $this->session->data['shipping_zone_id'];
		// } else {
		// 	$filter_zone_id = '';
		// }

		// to-do: add sort to (and remove image dimensions from?) Featured module settings
		$sort = !empty($setting['sort'])? $setting['sort'] : 'random';  // 'random', 'date_added'
		$order = ($sort == 'date_added') ? 'DESC' : 'ASC'; // if sorted by date, then show newest first, otherwise sort ascending
		$limit = $setting['limit'] ?: 6;
		$image_width = !$is_sidebar ? $this->config->get('config_image_product_width') : 210;
		$image_height = !$is_sidebar ? $this->config->get('config_image_product_height') : 210;
		$image_crop = !$is_sidebar ? 'fw' : 'autocrop';

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
		$this->data['image_width'] = $image_width;
		$this->data['image_height'] = $image_height;

		// Listings
		$data = array(
			'filter_category_id' 		=> $filter_category_id,
			// 'filter_country_id'  		=> $filter_country_id,
			// 'filter_zone_id'            => $filter_zone_id,
			// 'filter_location'           => $filter_location,
			// 'filter_listing_type' 		=> $filter_listing_type,
			// 'filter_filter'      		=> $filter,
			'sort'               		=> $sort,
			'order'              		=> $order,
			'start'              		=> 0,
			'limit'              		=> $limit
		);

		$cache = md5(http_build_query(array_merge($setting, $data)));

		$currency_id = $this->currency->getId(); // isset($this->session->data['currency']) ? $this->currencty->getId($this->session->data['currency']) ? 0;

		$customer_group_id = $this->customer->isLogged() ? $this->customer->getCustomerGroupId() : $this->config->get('config_customer_group_id');

		$products = $this->cache->get('product.module.featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . (int)$currency_id . '.' . $cache);

		if ($products === false) {
			$products = array();

			$results = $this->model_catalog_product->getProductFeatured($data);

			foreach ($results as $product_id => $result) {
				$products[$product_id] = $result;
				$products[$product_id]['thumb_alt'] = $this->model_tool_image->resize($result['image'], $image_width, $image_height, $image_crop);
			}

			$this->cache->set('product.module.featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . (int)$currency_id . '.' . $cache, $products, 60 * 60 * 24 * 7); // 1 week cache expiration
		}

		if (!$is_sidebar) {
			// filter_ids
			$url = '';

			foreach ($products as $product) {
				$url .= $product['product_id'] . ',';
			}

			$this->data['more'] = $this->url->link('ajax/product/more', 'module=true&featured=true&filter_listings=' . rtrim($url, ','));
		} else {
			$this->data['more'] = $this->url->link('product/featured');
		}

		if ($is_sidebar) {
			$this->document->addScript('catalog/view/root/slick/slick.min.js');
		}

		$this->data['products'] = $this->getChild('product/data/list_module', array(
			'products' 		=> $products,
			'position' 		=> $setting['position']
		));

		$this->template = 'template/module/featured.tpl';

		$this->render();
	}
}
