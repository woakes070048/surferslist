<?php
class ControllerModuleMember extends Controller {
	protected function index($setting) {
		$this->data = $this->load->language('module/member');

		$this->load->model('catalog/member');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$image_width = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? $this->config->get('config_image_product_width') : 210;
		$image_height = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? $this->config->get('config_image_product_height') : 210;
		$image_crop = ($setting['position'] == 'content_top' || $setting['position'] == 'content_bottom') ? 'fw' : '';

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

		$cache = md5(http_build_query($setting));

		$filter_country_id = isset($this->session->data['shipping_country_id']) ? $this->session->data['shipping_country_id'] : '';

		$data = array(
			'filter_country_id' => $filter_country_id,
			'sort'  => 'product_count', //$setting['order_by'],
			'order' => 'DESC', //$setting['order'],
			'start' => 0,
			'limit' => $setting['limit']
		);

		$this->data['custom_fields'] = (isset($setting['custom_fields']) ? $setting['custom_fields'] : false);
		$this->data['product_count'] = (isset($setting['product_count']) ? $setting['product_count'] : false);

		$this->data['entry_member_custom_field_01'] = $this->config->get('member_custom_field_01');
		$this->data['entry_member_custom_field_02'] = $this->config->get('member_custom_field_02');
		$this->data['entry_member_custom_field_03'] = $this->config->get('member_custom_field_03');
		$this->data['entry_member_custom_field_04'] = $this->config->get('member_custom_field_04');
		$this->data['entry_member_custom_field_05'] = $this->config->get('member_custom_field_05');
		$this->data['entry_member_custom_field_06'] = $this->config->get('member_custom_field_06');

		$this->data['members'] = $this->cache->get('member.featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$filter_country_id . '.' . $cache);

		if ($this->data['members'] === false) {
			$this->data['members'] = array();

			$results = $this->model_catalog_member->getMembers($data);

			foreach ($results as $result) {
				$total_products = $this->model_catalog_product->getTotalProductsByMemberAccountId($result['member_account_id']);

				if ($total_products > 0) {
					$image = $this->model_tool_image->resize($result['member_account_image'], $image_width, $image_height, $image_crop);

					$this->data['members'][] = array(
						'member_id'  				=> $result['member_account_id'],
						'member_customer_id'  		=> $result['customer_id'],
						'image'   	 				=> $image,
						'name'    	 				=> $result['member_account_name'],
						'text_products' 			=> sprintf($this->language->get('text_products'), (int)$total_products),
						'member_custom_field_01' 	=> (isset($result['member_custom_field_01']) ? $result['member_custom_field_01'] : ''),
						'member_custom_field_02' 	=> (isset($result['member_custom_field_02']) ? $result['member_custom_field_02'] : ''),
						'member_custom_field_03' 	=> (isset($result['member_custom_field_03']) ? $result['member_custom_field_03'] : ''),
						'member_custom_field_04' 	=> (isset($result['member_custom_field_04']) ? $result['member_custom_field_04'] : ''),
						'member_custom_field_05' 	=> (isset($result['member_custom_field_05']) ? $result['member_custom_field_05'] : ''),
						'member_custom_field_06' 	=> (isset($result['member_custom_field_06']) ? $result['member_custom_field_06'] : ''),
						'rating'     				=> $this->config->get('config_review_status') ? round($result['rating']) : false,
						'reviews'    				=> sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
						'help_member_rating' 		=> sprintf($this->language->get('help_member_rating'), number_format($result['rating'], 2), (int)$result['reviews']),
						'href'    	 				=> $this->url->link('product/member/info', 'member_id=' . $result['member_account_id'])
					);
				}
			}

			$this->cache->set('member.featured.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$filter_country_id . '.' . $cache, $this->data['members'], 60 * 60 * 24); // 1 day cache expiration
		}

		$this->data['image_width'] = $image_width;
		$this->data['image_height'] = $image_height;

		$this->data['more'] = $this->url->link('product/member');
		$this->data['text_more'] = $this->language->get('text_view_all') . ' ' . $this->language->get('text_all_members');

		$this->template = '/template/module/member.tpl';

		$this->render();
	}
}
?>
