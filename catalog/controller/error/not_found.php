<?php
class ControllerErrorNotFound extends Controller {
	public function index() {
		$this->load->language('error/not_found');

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

			$this->addBreadcrumb($this->language->get('heading_title'), $this->url->link($route, '', ($this->request->isSecure() ? 'SSL' : 'NONSSL')));
		}

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_error'] = $this->language->get('text_error');

		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_search'] = $this->language->get('button_search');

		$this->data['continue'] = $this->url->link('common/home', '', 'SSL');
		$this->data['search'] = $this->url->link('product/search', '', 'SSL');

		$this->template = '/template/error/not_found.tpl';

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
?>
