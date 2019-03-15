<?php
class ControllerInformationInformation extends Controller {
	public function index() {
		$this->data = $this->load->language('information/information');

		$this->load->model('catalog/information');

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));

		$information_id = isset($this->request->get['information_id']) ? (int)$this->request->get['information_id'] : 0;

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$this->document->setTitle($information_info['title']);

			if (!empty($information_info['meta_description'])) {
				$information_info_meta_description = $information_info['meta_description'];
			} else {
				$information_info_meta_description = utf8_substr(strip_tags(html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8')), 0, 160);
			}

			$this->document->setDescription($information_info_meta_description);
			$this->document->setKeywords($information_info['meta_keyword']);

			$this->addBreadcrumb($information_info['title'], $this->url->link('information/information', 'information_id=' .  $information_id));

			$this->data['heading_title'] = $information_info['title'];

			$this->data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

			$this->data['continue'] = $this->url->link('common/home');
			$this->data['page'] = $this->url->link('information/information', 'information_id=' .  $information_id);

			$this->model_catalog_information->updateViewed($this->request->get['information_id']);

			$this->template = '/template/information/information.tpl';
		} else {
			$this->addBreadcrumb($this->language->get('text_error'), $this->url->link('information/information', 'information_id=' . $information_id));

			$this->document->setTitle($this->language->get('text_error'));

			$this->data['heading_title'] = $this->language->get('text_error');

			$this->data['search'] = $this->url->link('product/search');
			$this->data['continue'] = $this->url->link('common/home');

			$this->template = '/template/error/not_found.tpl';

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
		}

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->children = array(
			'common/notification',
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}

	public function info() {
		$this->load->model('catalog/information');

		$information_id = isset($this->request->get['information_id']) ? (int)$this->request->get['information_id'] : 0;

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$output = '<div class="bgWhite">' . "\n";
			$output .= '<h1>' . $information_info['title'] . '</h1>' . "\n";
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
			$output .= '</div>' . "\n";

			$this->model_catalog_information->updateViewed($this->request->get['information_id']);

			$this->response->setOutput($output);
		}
	}
}
?>
