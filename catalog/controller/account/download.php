<?php
class ControllerAccountDownload extends Controller {
	public function __construct($registry) {
		parent::__construct($registry);

		if (!$this->customer->validateLogin()) {
			$this->session->data['redirect'] = $this->url->link('account/download', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		if (!$this->customer->validateProfile() || !$this->customer->getMemberPermission('download_enabled') || !$this->config->get('member_tab_download')) {
			$this->redirect($this->url->link('account/account', '', 'SSL'));
		}
	}

	public function index() {

		$this->data = $this->load->language('account/download');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('text_account'), $this->url->link('account/account'));
		$this->addBreadcrumb($this->language->get('text_downloads'), $this->url->link('account/download'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->load->model('account/download');

		$download_total = $this->model_account_download->getTotalDownloads();

		if ($download_total) {
			$this->data['button_continue'] = $this->language->get('button_back');

			$page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;

			$this->data['downloads'] = array();

			$results = $this->model_account_download->getDownloads(($page - 1) * $this->config->get('config_catalog_limit'), $this->config->get('config_catalog_limit'));

			foreach ($results as $result) {
				if (file_exists(DIR_DOWNLOAD . $result['filename'])) {
					$size = filesize(DIR_DOWNLOAD . $result['filename']);

					$i = 0;

					$suffix = array(
						'B',
						'KiB',
						'MiB',
						'GiB',
						'TiB',
						'PiB',
						'EiB',
						'ZiB',
						'YiB'
					);

					while (($size / 1024) > 1) {
						$size = $size / 1024;
						$i++;
					}

					$this->data['downloads'][] = array(
						'order_id'   => $result['order_no'] ?: $result['order_id'],
						'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
						'name'       => $result['name'],
						'remaining'  => $result['remaining'],
						'size'       => round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i],
						'href'       => $this->url->link('account/download/download', 'order_download_id=' . $result['order_download_id'], 'SSL')
					);
				}
			}

			$this->data['pagination'] = $this->getPagination($download_total, $page, 10, 'account/download');

			$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

			$this->template = 'template/account/download.tpl';

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);

			$this->response->setOutput($this->render());
		} else {
			$this->data['text_error'] = $this->language->get('text_empty');

			$this->data['search'] = $this->url->link('product/search', '', 'SSL');
			$this->data['button_continue'] = $this->language->get('button_back');
			$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

			$this->template = 'template/error/not_found.tpl';

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);

			$this->response->setOutput($this->render());
		}
	}

	public function download() {
		$this->load->model('account/download');

		$order_download_id = isset($this->request->get['order_download_id']) ? (int)$this->request->get['order_download_id'] : 0;

		$download_info = $this->model_account_download->getDownload($order_download_id);

		if ($download_info) {
			$file = DIR_DOWNLOAD . $download_info['filename'];
			$mask = basename($download_info['mask']);

			if (!headers_sent()) {
				if (file_exists($file)) {
					header('Content-Type: application/octet-stream');
					header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Pragma: public');
					header('Content-Length: ' . filesize($file));

					if (ob_get_level()) {
						ob_end_clean();
					}

					readfile($file, 'rb');

					$this->model_account_download->updateRemaining($this->request->get['order_download_id']);

					exit;
				} else {
					exit('Error: Could not find file ' . $file . '!');
				}
			} else {
				exit('Error: Headers already sent out!');
			}
		} else {
			$this->redirect($this->url->link('account/download', '', 'SSL'));
		}
	}

}

