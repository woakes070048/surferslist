<?php
class ControllerModuleCurrency extends Controller {
	protected function index() {
		if (isset($this->request->post['currency_code'])) {
			if ($this->config->get('module_currency_update_autoupdate')) {
				$currencies_active = $this->getActiveCurrencies();
				$currency_code = $this->request->post['currency_code'];

				if (isset($currencies_active[$currency_code])) {
					if ($currencies_active[$currency_code]['date_modified'] < date('Y-m-d H:i:s', strtotime('-1 day'))) {
						$this->load->model('module/currency_update');
						$currency_codes = array_map(function ($item) { return $item['code']; }, $currencies_active);
						$this->model_module_currency_update->update($currency_codes);
					}
				}
			}

			$this->currency->set($this->request->post['currency_code']);

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);

			if (isset($this->request->post['redirect']) &&
				(strpos($this->request->post['redirect'], $this->config->get('config_url')) === 0
				|| strpos($this->request->post['redirect'], $this->config->get('config_ssl')) === 0)) {
				$this->redirect($this->request->post['redirect']);
			} else {
				$this->redirect($this->url->link('common/home'));
			}
		}

		$this->load->language('module/currency');

		$this->data['text_currency'] = $this->language->get('text_currency');

		if ($this->request->isSecure()) {
			$connection = 'SSL';
		} else {
			$connection = 'NONSSL';
		}

		$this->data['action'] = $this->url->link('module/currency', '', $connection);

		$this->data['logged'] = $this->customer->isLogged();

		$this->data['currency_code'] = $this->currency->getCode();

		$this->data['currencies'] = $this->getActiveCurrencies();

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

		$this->template = 'template/module/currency.tpl';

		$this->render();
	}

	private function getActiveCurrencies() {
		$currencies = array();

		$this->load->model('localisation/currency');

		$results = $this->model_localisation_currency->getCurrencies();

		foreach ($results as $result) {
			if ($result['status']) {
				$currencies[$result['code']] = array(
					'title'         => $result['title'],
					'code'          => $result['code'],
					'date_modified' => $result['date_modified'],
					'symbol_left'   => $result['symbol_left'],
					'symbol_right'  => $result['symbol_right']
				);
			}
		}

		return $currencies;
	}
}

