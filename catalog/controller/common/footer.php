<?php
class ControllerCommonFooter extends Controller {
	protected function index() {
		$this->data = $this->load->language('common/footer');
		$this->data['language_id'] = (int)$this->config->get('config_language_id');

		$server = $this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url');

		$minify = $this->cache->get('minify');
		$js_min = isset($minify['js']) ? $minify['js'] : '';

		$this->data['minify'] = is_file(DIR_TEMPLATE . $js_min);
		$this->data['server'] = CDN_SERVER ?: $server;
		$this->data['js_min'] = $js_min;
		$this->data['fingerprint'] = $js_min ? '?v=' . rtrim(substr($js_min, strpos($js_min, '-') + 1), '.min.js') : '';

		$this->data['scripts'] = $this->document->getScripts();

		$this->data['social_buttons'] = isset($this->request->get['route'])
			&& ($this->request->get['route'] == 'product/product' || $this->request->get['route'] == 'product/member/info')
			? true : false;

		$this->data['text_powered'] = sprintf($this->language->get('text_powered'), '2015 - ' . date('Y', time()), $this->config->get('config_name'));
		$this->data['contact_email'] = $this->config->get('config_email');
		$this->data['logo_img'] = $this->config->get('config_logo_img');
		$this->data['text_logo_footer'] = sprintf($this->language->get('text_logo_footer'), $this->config->get('config_name'));

		// Information
		$this->data['about'] = $this->url->link('information/information', 'information_id=4', 'SSL'); // About Us
		$this->data['informations'] = $this->cache->get('information.primary.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));
		$this->data['informations_extra'] = $this->cache->get('information.other.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'));

		if ($this->data['informations'] === false || $this->data['informations_extra'] === false) {
			$this->load->model('catalog/information');
		}

		if (!$this->data['informations']) {
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

		if (!$this->data['informations_extra']) {
			$this->data['informations_extra'] = array();

			foreach ($this->model_catalog_information->getInformations() as $result) {
				if ($result['bottom'] && $result['sort_order'] >= 90) {
					$this->data['informations_extra'][] = array(
						'title' => $result['title'],
						'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
					);
				}
			}

			$this->cache->set('information.other.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'), $this->data['informations_extra'], 60 * 60 * 24 * 30); // 1 month cache expiration
		}

		$this->data['home'] = $this->url->link('common/home', '', 'SSL');
		$this->data['location'] = $this->url->link('information/location', '', 'SSL');

		// Listings Links
		$this->data['product'] = $this->url->link('product/allproducts', '', 'SSL');
		$this->data['category'] = $this->url->link('product/allcategories', '', 'SSL');
		$this->data['manufacturer'] = $this->url->link('product/manufacturer', '', 'SSL');
		$this->data['member'] = $this->url->link('product/member', '', 'SSL');
		$this->data['search'] = $this->url->link('product/search', '', 'SSL');
		$this->data['featured'] = $this->url->link('product/featured', '', 'SSL');
		$this->data['special'] = $this->url->link('product/special', '', 'SSL');

		// Account Links
		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['products'] = $this->url->link('account/product', '', 'SSL');
		$this->data['wishlist'] = $this->url->link('account/wishlist', '', 'SSL');
		$this->data['post'] = $this->customer->isLogged() ? $this->url->link('account/product/insert', '', 'SSL') : $this->url->link('account/anonpost', '', 'SSL');
		// $this->data['newsletter'] = $this->url->link('account/newsletter', '', 'SSL');
		$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
		$this->data['compare'] = $this->url->link('product/compare', '', 'SSL');
		$this->data['profile'] = $this->customer->isLogged() && $this->customer->hasProfile()
			? $this->customer->getProfileUrl()
			: $this->url->link('account/member', '', 'SSL');
		$this->data['logout'] = $this->url->link('account/logout', '', 'SSL');
		$this->data['register'] = $this->url->link('account/register', '', 'SSL');
		$this->data['login'] = $this->url->link('account/login', '', 'SSL');

		// Site Links
		$this->data['contact'] = $this->url->link('information/contact', '', 'SSL');
		$this->data['sitemap'] = $this->url->link('information/sitemap', '', 'SSL');

		$this->data['logged'] = $this->customer->isLogged();

		// Socials
		$this->data['fb_app_id'] = $this->config->get('config_fb_app_id');
		$this->data['social_links'] = is_array($this->config->get('config_social_links')) ? $this->config->get('config_social_links') : array();

		// Who's Online
		if ($this->config->get('config_customer_online')) {
			$this->load->model('tool/online');

			$ip = isset($this->request->server['REMOTE_ADDR']) ? $this->request->server['REMOTE_ADDR'] : '';

			$url = isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI']) ? 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'] : '';

			$referer = isset($this->request->server['HTTP_REFERER']) ? $this->request->server['HTTP_REFERER'] : '';

			$this->model_tool_online->whosonline($ip, $this->customer->getId(), $url, $referer);
		}

		$this->template = '/template/common/footer.tpl';
		$this->render();
	}
}
?>
