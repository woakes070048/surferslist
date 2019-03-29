<?php
class ControllerErrorNotFound extends Controller {
	public function index() {
		$this->data =  $this->load->language('error/not_found');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));

		if (isset($this->request->get['route'])) {
			$data = $this->request->get;

			unset($data['_route_']);

			$route = $data['route'];

			unset($data['route']);

			$url = '';

			if ($data) {
				$url = '&' . urldecode(http_build_query($data, '', '&'));
			}

			$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link($route, $url));
		}

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['continue'] = $this->url->link('common/home');
		$this->data['search'] = $this->url->link('product/search');

		$this->template = 'template/error/not_found.tpl';

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/notification',
			'common/footer',
			'common/header'
		);

		$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

		$this->response->setOutput($this->render());
	}
}

