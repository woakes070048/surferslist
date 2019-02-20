<?php
class ControllerEmbedFooter extends Controller {
	protected function index() {
		$this->data = $this->load->language('common/footer');

		$minify = $this->cache->get('minify');

		$this->data['server'] = $this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url');
		$this->data['js_min'] = isset($minify['js']) ? $minify['js'] : '';

		$this->data['scripts'] = $this->document->getScripts();

		// temp disabled
		$this->data['social_buttons'] = false; // isset($this->request->get['route']) && $this->request->get['route'] == 'embed/profile' ? true : false;
		$this->data['contact_enabled'] = false; // Google reCaptcha js included if true

		$this->template = '/template/embed/footer.tpl';
		$this->render();
	}
}
?>
