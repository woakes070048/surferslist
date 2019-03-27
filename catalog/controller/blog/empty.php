<?php
class ControllerBlogEmpty extends Controller {
	protected function index($data) {
		$this->data = $this->load->language('blog/common');

		$this->data['back'] = ($this->request->checkReferer($this->config->get('config_url')) || $this->request->checkReferer($this->config->get('config_ssl'))) ? $this->request->server['HTTP_REFERER'] : $this->url->link('blog/home');
        $this->data['action'] = str_replace('&amp;', '&', $this->url->link($data['route'], $data['path'] . $this->getQueryString(array('filter', 'type', 'search'))));;
		$this->data['reset'] = $this->getQueryString(array('page')) ? $this->url->link($data['route'], $data['path']) : '';

		$this->data['continue'] = $this->url->link('blog/home');
		$this->data['search'] = $this->url->link('blog/search');

        $this->template = 'template/blog/empty.tpl';

        $this->render();
    }
}
