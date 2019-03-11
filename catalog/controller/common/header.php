<?php
class ControllerCommonHeader extends Controller {
	protected function index() {
		$this->data = $this->load->language('common/header');

		$server = $this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url');

		if (isset($this->request->server['REQUEST_URI'])) {
			$request_uri = strtolower(urldecode($this->request->server['REQUEST_URI']));
			$request_path = parse_url($request_uri, PHP_URL_PATH);
			$request_params = parse_url($request_uri, PHP_URL_QUERY);
		} else {
			$request_path = '';
			$request_params = '';
		}

		$alternate = $server . str_replace('%2F', '/', urlencode(ltrim($request_path, "/")));

		$this->data['title'] = $this->document->getTitle();

		if ($this->config->has('config_seo_title_prefix') && isset($this->request->get['route']) && $this->request->get['route'] !== 'common/home') {
			$this->data['title'] .= " " . $this->config->get('config_seo_title_prefix');
		}

		if ($this->config->has('config_seo_title_suffix')) {
			$this->data['title'] .= " " . $this->config->get('config_seo_title_suffix');
		}

		if (!$this->document->hasUrl() && $request_path) {
			$this->document->setUrl($alternate);
		}

		if (!$this->document->hasImage() && is_file(DIR_IMAGE . $this->config->get('config_banner_image'))) {
			$this->load->model('tool/image');
			$image = $this->model_tool_image->resize($this->config->get('config_banner_image'), 2000, 800);
			$image_info = $this->model_tool_image->getFileInfo($image);

			if ($image_info) {
				$this->document->setImage($image, $image_info['mime'], $image_info[0], $image_info[1]);
			}
		}

		$minify = $this->cache->get('minify');
		$css_min = isset($minify['css']) ? $minify['css'] : '';

		$this->data['minify'] = is_file(DIR_TEMPLATE . $css_min);
		$this->data['server'] = CDN_SERVER ?: $server;
		$this->data['css_min'] = $css_min;
		$this->data['page'] = !isset($this->request->get['route']) || $this->request->get['route'] == 'common/home' || ($request_path == '/')	? 'home' : friendly_url($request_path);;
		$this->data['alternate'] = $request_params ? $alternate . '?' . $request_params : $alternate;

		$this->data['name'] = $this->config->get('config_name');
		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['open_graph'] = $this->document->getOpenGraph();
		$this->data['links'] = $this->document->getLinks();
		$this->data['styles'] = $this->document->getStyles();
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		$this->data['dns_prefetch'] = is_array($this->config->get('config_dns_prefetch')) ? $this->config->get('config_dns_prefetch') : array();
		$this->data['app'] = $this->config->get('config_app');
		$this->data['ascii_art'] = $this->config->get('config_ascii_art');
		$this->data['logo_img'] = $this->config->get('config_logo_img');
		$this->data['favicon'] = $this->config->get('config_favicon');
		$this->data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		$this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');

		$this->data['location_code'] = $this->getLocationCode();
		$this->data['location_geo'] = $this->getLocationName('short') ?: $this->language->get('text_location_change');

		$this->data['home'] = $this->url->link('common/home');
		$this->data['product'] = $this->url->link('product/allproducts', '', 'SSL');
		$this->data['product_search'] = $this->url->link('product/search', '', 'SSL');
		$this->data['category'] = $this->url->link('product/allcategories', '', 'SSL');
		$this->data['manufacturer'] = $this->url->link('product/manufacturer', '', 'SSL');
		$this->data['member'] = $this->url->link('product/member', '', 'SSL');
		$this->data['featured'] = $this->url->link('product/featured', '', 'SSL');
		$this->data['special'] = $this->url->link('product/special', '', 'SSL');
		$this->data['post'] = $this->customer->isLogged() ? $this->url->link('account/product/insert', '', 'SSL') : $this->url->link('account/anonpost', '', 'SSL');
		$this->data['post_link'] = $this->url->link('account/anonpost', '', 'SSL');
		$this->data['about'] = $this->url->link('information/information', 'information_id=4', 'SSL'); // About Us
		$this->data['faq'] = $this->url->link('information/information', 'information_id=11', 'SSL'); // FAQ
		// $this->data['blog'] = $this->url->link('blog/blog_home', '', 'SSL');
		$this->data['register'] = $this->url->link('account/register');
		$this->data['login'] = $this->url->link('account/login');
		$this->data['logout'] = $this->url->link('account/logout');
		$this->data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$this->data['compare'] = $this->url->link('product/compare', '', 'SSL');
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['listings'] = $this->url->link('account/product', '', 'SSL');
		$this->data['activated'] = $this->customer->hasProfile();
		$this->data['activate'] = $this->url->link('account/member', '', 'SSL');
		$this->data['profile'] = $this->customer->getId() ? $this->customer->getProfileUrl() : $this->url->link('account/member', '', 'SSL');
		$this->data['shopping_cart'] = $this->url->link('checkout/cart');
		$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
		$this->data['location'] = $this->url->link('information/location', 'redirect_path=' . urlencode(ltrim($request_path . '?' . $request_params, '/')), 'SSL');
		$this->data['location_remove'] = $this->url->link('information/location', 'location=none&redirect_path=' . urlencode(ltrim($request_path . '?' . $request_params, '/')), 'SSL');
		$this->data['contact'] = $this->url->link('information/contact', '', 'SSL');

		$this->data['stores'] = array();

		// Search
		if (isset($this->request->get['search'])) {
			$this->data['search'] = $this->request->get['search'];
		} else if (isset($this->request->get['s'])) {
			$this->data['search'] = $this->request->get['s'];
		} else {
			$this->data['search'] = '';
		}

		// Categories
		$this->data['categories'] = $this->cache->get('category.menu.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($this->data['categories'] === false) {
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');

			$this->data['categories'] = array();

			$categories = $this->model_catalog_category->getCategories(0);

			foreach ($categories as $category) {
				if ($category['top']) {
					if (utf8_strpos($category['name'], $this->language->get('heading_more')) !== false) {
						$category_name = ucwords($this->language->get('heading_more'));
					} else if (utf8_strpos($category['name'], $this->language->get('heading_other')) !== false) {
						$category_name = ucwords($this->language->get('heading_other'));
					} else {
						$category_name = ucwords($category['name']);
					}

					if ($this->config->get('config_product_count')) {
						$data_category = array(
							'filter_category_id'  => $category['category_id'],
							'filter_sub_category' => true
						);

						$product_total_category = $this->model_catalog_product->getTotalProducts($data_category);
					}

					$this->data['categories'][] = array(
						'name' 			=> $category_name . ($this->config->get('config_product_count') ? ' <u>' . $product_total_category . '</u>' : ''),
						'alt' 			=> friendly_url($category['name']),
						'href' 			=> $this->url->link('product/category', 'path=' . $category['category_id']),
						'image' 		=> true,
						'id' 			=> $category['category_id']
					);
				}
			}

			$this->cache->set('category.menu.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data['categories'], 60 * 60 * 24 * 30); // 1 month cache expiration
		}

		// Information
		$this->data['informations'] = $this->cache->get('information.primary.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($this->data['informations'] === false) {
			$this->load->model('catalog/information');

			foreach ($this->model_catalog_information->getInformations() as $result) {
				if ($result['bottom'] && $result['sort_order'] < 50) {
					$this->data['informations'][] = array(
						'title' => $result['title'],
						'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
					);
				}
			}

			$this->cache->set('information.primary.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data['informations'], 60 * 60 * 24 * 30); // 1 month cache expiration
		}

		$this->children = array(
			'module/language',
			'module/currency',
			'module/cart'
		);

		$this->template = '/template/common/header.tpl';

		$this->render();
	}
}
?>
