<?php
class ControllerModuleLanguage extends Controller {
	protected function index() {
		if (isset($this->request->post['language_code'])) {
			$this->session->data['language'] = $this->request->post['language_code'];

			if (isset($this->request->post['redirect']) &&
				(strpos($this->request->post['redirect'], $this->config->get('config_url')) === 0
				|| strpos($this->request->post['redirect'], $this->config->get('config_ssl')) === 0)) {
				$this->redirect($this->request->post['redirect']);
			} else {
				$this->redirect($this->url->link('common/home'));
			}
		}

		$this->load->language('module/language');

		$this->data['text_language'] = $this->language->get('text_language');

		if ($this->request->isSecure()) {
			$connection = 'SSL';
		} else {
			$connection = 'NONSSL';
		}

		$this->data['action'] = $this->url->link('module/language', '', $connection);

		$this->data['language_code'] = $this->session->data['language'];

		$this->load->model('localisation/language');

		$this->data['languages'] = array();

		$results = $this->model_localisation_language->getLanguages();

		foreach ($results as $result) {
			if ($result['status']) {
				$this->data['languages'][] = array(
					'name'  => $result['name'],
					'code'  => $result['code'],
					'image' => $result['image']
				);
			}
		}

		if (!isset($this->request->get['route'])) {
			$this->data['redirect'] = $this->url->link('common/home');
		} else {
			$data = $this->request->get;

			unset($data['_route_']);

			$route = $data['route'];

			unset($data['route']);

			// Remove customer and affiliate anti-CSRF tokens
			unset($data['customer_token']);
			unset($data['affiliate_token']);

			$url = '';

			if ($data) {
				$url = '&' . urldecode(http_build_query($data, '', '&'));
			}

			$this->data['redirect'] = $this->url->link($route, $url, $connection);
		}

		$this->template = 'template/module/language.tpl';

		$this->render();
	}
}
?>
