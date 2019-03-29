<?php
class ControllerCommonMaintenance extends Controller {
	use Admin;

	public function index() {
		if ($this->config->get('config_maintenance')) {
			$route = '';

			if (isset($this->request->get['route'])) {
				$part = explode('/', $this->request->get['route']);

				if (isset($part[0])) {
					$route .= $part[0];
				}
			}

			// show if logged in as admin
			if (($route != 'payment') && !$this->isAdmin()) {
				return $this->forward('common/maintenance/info');
			}
		}
	}

	public function info() {
		if (!$this->config->get('config_maintenance')) {
			return $this->redirect($this->url->link('common/home'));
		}

		$this->data = $this->load->language('common/maintenance');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->breadcrumbs = array();

		$this->document->breadcrumbs[] = array(
			'text'      => $this->language->get('text_maintenance'),
			'href'      => $this->url->link('common/maintenance'),
			'separator' => false
		);

		$this->data['message'] = $this->language->get('text_message');

		$server = $this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url');

		$this->data['logo'] = $this->config->get('config_logo') ? $server . 'image/' . $this->config->get('config_logo') : '';

		$this->data['full_page'] = $this->config->get('config_maintenance_full_page');

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . '/1.1 503 Service Temporarily Unavailable');
		$this->response->addHeader('Retry-After: ' . gmdate('D, d M Y H:i:s T', time() + 60 * 60 * 24));

		$this->template = 'template/common/maintenance.tpl';

		$this->children = array(
			'common/header'
		);

		if (!$this->config->get('config_maintenance_full_page')) {
			$this->children[] = 'common/footer';
		}

		$this->response->setOutput($this->render());
	}
}

