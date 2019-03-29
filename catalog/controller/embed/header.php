<?php
class ControllerEmbedHeader extends Controller {
	protected function index() {
		// for now, allow embed pages to be rendered on any other website
		// (NEXT: limit to url saved with profile and pass through ifram request query string)
		if (!isset($this->session->data['parent_domain'])) {
			if (!empty($this->request->server['HTTP_REFERER'])) {
				$referring_domain_parts = parse_url($this->request->server['HTTP_REFERER']);
				$this->session->data['parent_domain'] = $referring_domain_parts['scheme'] . '://' . $referring_domain_parts['host'];
			}
		}

		if (isset($this->session->data['parent_domain'])) {
			$this->response->addHeader('X-Frame-Options: allow-from ' . $this->session->data['parent_domain']);
			$this->response->addHeader('Content-Security-Policy: frame-ancestors ' . $this->session->data['parent_domain']);
		} else {
			header_remove('X-Frame-Options');
		}

		$this->data = $this->load->language('common/header');

		$this->data['title'] = $this->document->getTitle();

		$minify = $this->cache->get('minify');

		$this->data['server'] = $this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url');
		$this->data['css_min'] = isset($minify['css']) ? $minify['css'] : '';

		$this->data['name'] = $this->config->get('config_name');
		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['links'] = $this->document->getLinks();
		$this->data['styles'] = $this->document->getStyles();
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		$this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');

		// Search
		if (isset($this->request->get['search'])) {
			$this->data['search'] = $this->request->get['search'];
		} else if (isset($this->request->get['s'])) {
			$this->data['search'] = $this->request->get['s'];
		} else {
			$this->data['search'] = '';
		}

		$this->data['config_name'] = $this->config->get('config_name');

		// custom color query params - added to css in header
		// (e.g. '&customcolor=true&color_primary=66D9EF&color_secondary=A6E22E&color_featured=FD971F&color_special=F92672')
		$embed_settings = array();

		$embed_options_default = array(
			'color_primary' => '333333',
			'color_secondary' => '787878',
			'color_featured' => 'ffcc00',
			'color_special' => 'dc313e'
		);

		// Profile Embed Settings
		if (!empty($this->request->get['profile_id']) && (int)$this->request->get['profile_id'] > 0) {
			$this->load->model('catalog/member');

			$profile_embed_settings_string = $this->model_catalog_member->getMemberEmbedSettings($this->request->get['profile_id']);

			if ($profile_embed_settings_string) {
				parse_str($profile_embed_settings_string, $embed_settings);
			}
		}

		if (isset($this->request->get['customcolor'])) {
			$custom_color = $this->request->get['customcolor'] == 'true' ? true : false;
		} else if (isset($embed_settings['customcolor'])) {
			$custom_color = $embed_settings['customcolor'] == 'true' ? true : false;
		} else {
			$custom_color = false;
		}

		foreach ($embed_options_default as $key => $value) {
			if (isset($this->request->get[$key]) && is_hex_color($this->request->get[$key])) {
				$this->data[$key] = $this->request->get[$key];
			} else if (isset($embed_settings[$key]) && is_hex_color($embed_settings[$key])) {
				$this->data[$key] = $embed_settings[$key];
			} else {
				$this->data[$key] = $value;
			}
		}

		$this->data['customcolor'] = $custom_color;

		$this->template = 'template/embed/header.tpl';
		$this->render();
	}
	
}

