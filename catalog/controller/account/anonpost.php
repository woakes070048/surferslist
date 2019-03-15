<?php
class ControllerAccountAnonPost extends Controller {
	use Captcha, CSRFToken, ValidateField, ValidateTime, Admin;

	private $image = '';

	public function __construct($registry) {
		parent::__construct($registry);

		$this->setAdmin($registry);

		$this->setCaptchaStatus($this->config->get('config_captcha_anonpost') && !$this->customer->isLogged() && !$this->isAdmin());
	}

	public function index() {
		$this->data = $this->load->language('account/product');
		$this->data['entry_image'] = $this->language->get('heading_image');

		$this->data['admin'] = $this->isAdmin();
		$this->data['logged'] = $this->customer->isLogged();

		$this->load->model('account/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('catalog/filter');
		$this->load->model('localisation/language');
		$this->load->model('localisation/currency');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$this->load->model('tool/image');

		$csrf_token_set = isset($this->session->data['csrf_token']) ? $this->session->data['csrf_token'] : '';

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$data = $this->request->post;

			if ($this->isAdmin() && !empty($this->request->files['image_file'])) {
				$data['image_file'] = $this->request->files['image_file'];
			}

			if (!$this->validateCaptcha()) {
				$this->setError('captcha', $this->getCaptchaError());
				$this->setError('warning', $this->getCaptchaError());
			}

			if (!$this->isAdmin()) {
				if (!$this->validatePostTimeMin()) {
					$this->setError('warning', sprintf($this->language->get('error_too_fast'), $min_time));
				}

				if (!$this->validatePostTimeMax()) {
					$this->setError('warning', sprintf($this->language->get('error_timeout'), $min_time));
				}
			}

			if (!$this->hasError()) {
				if (!$this->validateCSRFToken()) {
					$this->setError('warning', $this->language->get('error_invalid_token'));
				};
			}

			if (!$this->hasError()) {
				$this->validateForm($data);
			}

			if (!$this->hasError() && $this->isAdmin()) {
				if (!empty($data['image_file']['tmp_name'])) {
					$this->validateImageFile($data['image_file']['tmp_name']);
				} else if (!empty($data['image_url'])) {
					$new_filename = $this->getImageFromUrl($data['image_url'], $csrf_token_set);

					if ($new_filename) {
						$this->validateImageFile($new_filename);
					}
				}
			}

			if (!$this->hasError()) {
				$this->prepareData($data, $csrf_token_set);

				// don't trigger email notification of new listing for admin users
				if ($this->isAdmin()) {
					$data['notify'] = false;
				}

				if ($this->model_account_product->addAnonListing($data)) {
					$this->session->data['success'] = $this->customer->isLogged() ? $this->language->get('text_success_new') : $this->language->get('text_anonpost_success');
					unset($this->session->data['warning']);
				}

				$this->removeTempImages($csrf_token_set);

				$this->redirect($this->url->link('account/anonpost', '', 'SSL'));
			}
		}

		$this->setCSRFToken();
		$this->setPostTime();

		$moved_image = '';

		if (!empty($this->request->post['image'])) {
			$keep_img = array(
				'image_path' => clean_path(rtrim(DIR_IMAGE . $this->request->post['image'])),
				'csrf_token'  => $this->getCSRFToken()
			);

			$moved_image = $this->removeTempImages($csrf_token_set, $keep_img);
		}

		// if (!$this->getError('warning') && !isset($this->session->data['warning']) && !isset($this->session->data['success']) && !isset($this->session->data['notification'])) {
		// 	$this->session->data['notification'] = $this->language->get('text_all_fields_required');
		// }

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('heading_anonpost'), $this->url->link('account/anonpost'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->document->setTitle($this->language->get('heading_anonpost'));
		$this->document->setDescription($this->language->get('text_anonpost_meta_description'));
		$this->document->setKeywords($this->language->get('text_anonpost_meta_keyword'));

		$this->data['heading_title'] = $this->isAdmin() ? $this->language->get('heading_adminpost') : $this->language->get('heading_anonpost');
		$this->data['heading_sub_title'] = $this->language->get('heading_anonpost_sub');
		$this->data['text_anonpost_intro'] = $this->isAdmin()
			? sprintf($this->language->get('text_adminpost_intro'), $this->url->link('account/anonpost/import'))
			: (!$this->customer->isLogged()
				? $this->language->get('text_anonpost_intro') . sprintf($this->language->get('text_anonpost_intro_signin'), $this->url->link('account/login'))
				: $this->language->get('text_anonpost_intro') . sprintf($this->language->get('text_anonpost_intro_post'), $this->url->link('account/product')));
		$this->data['text_anonpost_footer'] = sprintf($this->language->get('text_anonpost_footer'), $this->url->link('account/login'));

		// Errors
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = false;
		}

		$this->data['warning'] = $this->getError('warning');

		if ($this->data['warning']) {
			$this->session->data['warning'] = $this->data['warning'];
		} else {
			unset($this->session->data['warning']);
		}

		$data_field_errors = array(
			'name'				=>	'error_name',
			'description'		=>	'error_description',
			'tag'				=>	'error_tag',
			'image'				=>	'error_image',
			'image_url'			=>	'error_image_url',
			'image_file'		=>	'error_image_file',
			'size'				=>	'error_size',
			'category'			=>	'error_category',
			'category_sub'		=>	'error_category_sub',
			'manufacturer'		=>	'error_manufacturer',
			'model'				=>	'error_model',
			'year'				=>	'error_year',
			'link'				=>  'error_link',
			'price'				=>  'error_price',
			'captcha'           =>  'error_captcha'
		);

		foreach ($data_field_errors as $data_field => $error_name) {
			$this->data[$error_name] = $this->getError($data_field);
		}

        // Help
		$image_upload_filesize_max = $this->config->get('member_image_upload_filesize_max') ? $this->config->get('member_image_upload_filesize_max') / 1024 : 5; // kB to MB
		$image_dimensions_min_width = $this->config->get('member_image_dimensions_min_width') ? $this->config->get('member_image_dimensions_min_width') : 245;
		$image_dimensions_min_height = $this->config->get('member_image_dimensions_min_height') ? $this->config->get('member_image_dimensions_min_height') : 245;

		$this->data['help_description'] = sprintf($this->language->get('help_description'), $this->config->get('member_data_field_description_min'), $this->config->get('member_data_field_description_max'));
        $this->data['help_image'] = sprintf($this->language->get('help_image'), $image_dimensions_min_width, $image_dimensions_min_height, $image_upload_filesize_max);

		// Name, Description, Tags
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		$this->data['product_description'] = isset($this->request->post['product_description']) ? $this->request->post['product_description'] : array();

		// Primary Image
		$this->data['thumb'] = $this->model_tool_image->resize($moved_image, $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
		$this->data['image'] = $moved_image;

		// Initialize Base Data Fields
		$config_data_fields_empty = array('image_url', 'model', 'year', 'size', 'link', 'captcha', 'captcha_widget_id', 'g-recaptcha-response');

		// Assign String Data Fields
        foreach ($config_data_fields_empty as $config_data_field) {
            $this->data[$config_data_field] = isset($this->request->post[$config_data_field]) ? $this->request->post[$config_data_field] : '';
        }

		$this->data['help_unauthorized'] = !$this->isAdmin() && !$this->customer->isLogged() ? sprintf($this->language->get('help_unauthorized'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL')) : '';

		// Categories
		$categories_complete = array();

		if ($this->config->get('member_data_field_category')) {
			$this->data['categories'] = $this->model_catalog_category->getCategories(0);
			$this->data['category_id'] = isset($this->request->post['category_id']) ? $this->request->post['category_id'] : 0;
			$this->data['sub_categories'] = $this->data['category_id'] ? $this->model_catalog_category->getCategories($this->data['category_id']) : array();
			$this->data['sub_category_id'] = isset($this->request->post['sub_category_id']) ? $this->request->post['sub_category_id'] : 0;
			$this->data['third_categories'] = $this->data['sub_category_id'] ? $this->model_catalog_category->getCategories($this->data['sub_category_id']) : array();
			$this->data['third_category_id'] = isset($this->request->post['third_category_id']) ? $this->request->post['third_category_id'] : 0;
			$this->data['category_name'] = isset($this->request->post['category_name']) ? $this->request->post['category_name'] : '';

			$categories_complete = $this->model_catalog_category->getAllCategoriesComplete();
		}

		$this->data['categories_complete'] = htmlspecialchars(json_encode($categories_complete), ENT_COMPAT);

		// Manufacturers (Brands)
		$manufacturers = array();
		$manufacturer_id = isset($this->request->post['manufacturer_id']) ? $this->request->post['manufacturer_id'] : 0;
		$manufacturer_name = '';
		$manufacturer_thumb = '';

		if ($this->config->get('member_data_field_manufacturer')) {
			if ($manufacturer_id) {
				$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
				$manufacturer_name = $manufacturer_info['name'];
				$manufacturer_thumb = $this->model_tool_image->resize($manufacturer_info['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'), 'fw');
			}

			$manufacturers = $this->model_catalog_manufacturer->getManufacturers();
		}

		$this->data['manufacturer_id'] = $manufacturer_id;
		$this->data['manufacturer_name'] = $manufacturer_name;
		$this->data['manufacturer_thumb'] = $manufacturer_thumb;
		$this->data['manufacturers'] = $manufacturers;
		$this->data['manufacturers_all'] = htmlspecialchars(json_encode($manufacturers), ENT_COMPAT);

		$this->data['currency'] = $this->model_localisation_currency->getCurrencyByCode($this->currency->getCode());
		$this->data['price'] = isset($this->request->post['price']) ? $this->request->post['price'] : '';
		$this->data['conditions'] = $this->model_catalog_filter->getFiltersByFilterGroupId($this->config->get('config_filter_group_condition_id'));
		$this->data['condition_id'] = isset($this->request->post['condition_id']) ? $this->request->post['condition_id'] : 0;

		// Admin Fields
		$this->data['display_more_options'] = $this->isAdmin() || $this->hasError() ? true : false;
		$this->data['status'] = $this->isAdmin() && isset($this->request->post['status']) ? $this->request->post['status'] : 1;
		$this->data['approved'] = $this->isAdmin() && isset($this->request->post['approved']) ? $this->request->post['approved'] : 1;
		$this->data['price'] = isset($this->request->post['price']) ? $this->request->post['price'] : '';

		$this->data['csrf_token'] = $this->getCSRFToken();
		$this->data['captcha_enabled'] = $this->getCaptchaStatus();

		$this->document->addStyle('catalog/view/root/ui/jquery-ui.min.css');
		$this->document->addScript('catalog/view/root/ui/jquery-ui.min.js');
		$this->document->addScript('catalog/view/root/javascript/ajaxupload.js');
		$this->document->addScript('catalog/view/root/javascript/post.js');

		$this->data['page'] = $this->url->link('account/anonpost', '', 'SSL');
		$this->data['action'] = $this->url->link('account/anonpost', '', 'SSL');
		$this->data['cancel'] = $this->url->link('common/home', '', 'SSL');
		$this->data['continue'] = $this->url->link('account/product', '', 'SSL');

		$this->template = 'template/account/anonpost_form.tpl';

		$this->children = array(
			'common/notification',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}

	public function import() {
		if (!$this->isAdmin()) {
			return $this->redirect($this->url->link('account/anonpost', '', 'SSL'));
		}

		$this->data = $this->load->language('account/product');

		$this->load->model('account/product');
		$this->load->model('catalog/category');
		$this->load->model('catalog/manufacturer');
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$this->load->model('account/member');
		$this->load->model('tool/image');

		$csrf_token_set = isset($this->session->data['csrf_token']) ? $this->session->data['csrf_token'] : '';

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateImportFile()) {
			$listings = array();
			$row_number = 0;
			$import_count = 0;
			$import_limit = 50;
			$import_max = (int)$this->request->post['max'];
			$status = isset($this->request->post['status']) ? $this->request->post['status'] : 1;
			$approved = isset($this->request->post['approved']) ? $this->request->post['approved'] : 0;
			$member_id = !empty($this->request->post['member']['member_id']) ? $this->request->post['member']['member_id'] : 0;
			$member_data = array();

			$data_required = array(
				'link',
				'category_id',
				'sub_category_id',
				'manufacturer_id',
				'model',
				'size'
			);

			$data_optional = array(
				'skip',
				'approved',
				'status',
				'third_category_id',
				'image',
				'image_url',
				'year',
				'name',
				'description',
				'tag',
				'product_description',
				'price',
				'condition_id',
				'location',
				'zone_id',
				'country_id'
			); // product_description[language_id][name, description, tag]

			// member profile selected
			if ($member_id) {
				$member_info = $this->model_account_member->getMemberByMemberId($member_id);

				$member_data['member_account_id'] = $member_id;
				$member_data['member_customer_id'] = $member_info['customer_id'];
				$member_data['member_directory_images'] = $member_info['member_directory_images'];
			} else {
				$member_data['member_customer_id'] = 0;
			}

			// read posted file
			if (is_uploaded_file($this->request->files['import']['tmp_name']) && file_exists($this->request->files['import']['tmp_name'])) {
				$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['import']['name'], ENT_QUOTES, 'UTF-8')));
				$filename_ext = strtolower(strrchr($filename, '.'));

				if (!defined('DIR_IMPORT')) define('DIR_IMPORT', str_replace('/image/', '/import/', DIR_IMAGE));

				move_uploaded_file($this->request->files['import']['tmp_name'], DIR_IMPORT . $filename);

				if ($filename_ext == '.json') {
					$listings = json_decode(file_get_contents(DIR_IMPORT . $filename), true);
				} else if ($filename_ext == '.csv') {
					// parse CSV file into an array of arrays of values from each line in the CSV
					$listings = array_map('str_getcsv', file(DIR_IMPORT . $filename));

					// add keys using the first row column headers (transforms into array of associative arrays)
				    array_walk($listings, function(&$a) use ($listings) {
						if (count($a) != count($listings[0])) {
							$this->log->write('CSV DATA ERROR:  COLUMN MISMATCH!');
							$this->log->write('keys: ' . json_encode($listings[0]));
							$this->log->write('values: ' . json_encode($a));
							$a = array('skip' => 1);
						} else {
							$a = array_combine($listings[0], $a);
						}
				    });

					// remove the first array from the transformed array, which are the column headers
				    array_shift($listings);
				}

				//$this->log->write(json_encode($listings));

				rename(DIR_IMPORT . $filename, DIR_IMPORT . $filename . '.' . $csrf_token_set);
			}

			foreach ($listings as $data) {
				// reset/clear image
				$this->image = '';
				$this->setError('image_url', '');

				// increment row counter
				$row_number++;

				// count/skip
				if (!empty($data['skip']) || $import_count >= $import_max || $import_count >= $import_limit) {
					continue;
				}

				// validation
				if (!$this->validateFields($data, $data_required, $data_optional, $member_id, $row_number)) {
					continue;
				}

				// name/title
				if (!empty($data['name'])) {
					$data['product_description'][1]['name'] = $data['name'];
				}

				if (!empty($data['description'])) {
					$data['product_description'][1]['description'] = $data['description'];
				}

				if (!empty($data['tag'])) {
					$data['product_description'][1]['tag'] = $data['tag'];
				}

				// set listing status to form values if no column value exists
				if (!isset($data['status'])) {
					$data['status'] = $status;
				}

				if (!isset($data['approved'])) {
					$data['approved'] = $approved;
				}

				// process image url to file
				if (!empty($data['image_url'])) {
					// downloads and saves image location to $this->image if successful, else sets error for 'image_url'
					$new_filename = $this->getImageFromUrl($data['image_url'], $csrf_token_set);

					if ($new_filename) {
						$image_url_is_valid = $this->validateImageFile($new_filename, $row_number);
					}
				}

				// process image file
				if (!$this->image && !empty($data['image'])) {
					if (!is_file(DIR_IMAGE . $data['image'])) {
						$this->appendError('input_data', 'IMAGE NOT FOUND: ' . $data['image']);
						continue;
					}

					if (utf8_strpos($data['image'], 'temp') !== false) {
						// triggers move when prepareData() is called below
						$this->image = $data['image'];
					}
				}

				// no image set and image url error, skip
				if (!$this->image && ($this->getError('image_url') || !$image_url_is_valid)) {
					 $this->appendError('input_data', $this->getError('image_url'));
					 continue;
				}

				$data = array_merge($data, $member_data);

				// prepare data for import
				// ...and moves $this->image, updates $data['image'] with new/final location
				// (e.g. data/catalog/category_main/category_sub/category_third/brandname-size-model-year-123456s.jpg)
				$this->prepareData($data, $csrf_token_set);

				// disable if no image assigned yet
				if (empty($data['image'])) {
					$data['status'] = 0;
				}

				// don't trigger email notification of new listing
				$data['notify'] = false;

				// add listing
				if ($this->model_account_product->addAnonListing($data)) {
					$import_count++;
				}
			}

			if ($import_count) {
				$this->session->data['success'] = sprintf($this->language->get('text_success_import'), $import_count);
			} else {
				unset($this->session->data['success']);
			}

			if ($this->getError('input_data')) {
				$this->session->data['warning'] = implode('<br />', $this->getError('input_data'));
			} else {
				unset($this->session->data['warning']);
			}

			$this->removeTempImages($csrf_token_set);

			$this->redirect($this->url->link('account/anonpost/import', '', 'SSL'));
		}

		$this->setCSRFToken();

		$this->addBreadcrumb($this->language->get('text_home'), $this->url->link('common/home'));
		$this->addBreadcrumb($this->language->get('heading_anonpost'), $this->url->link('account/anonpost'));
		$this->addBreadcrumb($this->language->get('heading_import'), $this->url->link('account/anonpost/import'));

		$this->data['breadcrumbs'] = $this->getBreadcrumbs();

		$this->document->setTitle($this->language->get('heading_import'));

		$this->data['heading_title'] = $this->language->get('heading_import');
		$this->data['heading_sub_title'] = $this->language->get('heading_import_sub');
		$this->data['text_import_intro'] = $this->language->get('text_import_intro');

		// error/success messages
		$this->data['error_import'] = $this->getError('import');
		$this->data['success'] = isset($this->session->data['success']) ? $this->session->data['success'] : false;
		$this->data['warning'] = isset($this->session->data['warning'])
			? $this->session->data['warning']
			: $this->getError('warning');

		$this->data['max'] = isset($this->request->post['max']) ? $this->request->post['max'] : 40;
		$this->data['status'] = isset($this->request->post['status']) ? $this->request->post['status'] : 1;
		$this->data['approved'] = isset($this->request->post['approved']) ? $this->request->post['approved'] : 1;
		$this->data['member'] = isset($this->request->post['member']) ? $this->request->post['member'] : array();

		$this->data['csrf_token'] = $this->getCSRFToken();

		$this->data['page'] = $this->url->link('account/anonpost/import', '', 'SSL');
		$this->data['action'] = $this->url->link('account/anonpost/import', '', 'SSL');

		$this->document->addStyle('catalog/view/root/ui/jquery-ui.min.css');
		$this->document->addScript('catalog/view/root/ui/jquery-ui.min.js');
		$this->document->addScript('catalog/view/root/javascript/contact.js');

		$this->template = 'template/account/import_form.tpl';

		$this->children = array(
			'common/notification',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}

	private function validateImportFile() {
		if (!$this->validateCSRFToken()) {
			$this->setError('warning', $this->language->get('error_invalid_token'));
			return false;
		};

		if (!empty($this->request->files['import']['name'])) {
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['import']['name'], ENT_QUOTES, 'UTF-8')));

			$allowed_mimetypes = array(
				'application/json',
				'text/csv'
			);

			$allowed_ext = array(
				'.json',
				'.csv'
			);

			if (!in_array($this->request->files['import']['type'], $allowed_mimetypes) || !in_array(strtolower(strrchr($filename, '.')), $allowed_ext)) {
				$this->setError('import', $this->language->get('error_import_filetype'));
			}

			if ($this->request->files['import']['size'] > $this->config->get('member_image_upload_filesize_max') * 1024) {
				$this->setError('import', sprintf($this->language->get('error_file_size'), $this->config->get('member_image_upload_filesize_max')));
			}

			if (!$this->validateStringLength($filename, 5, 128)) {
				$this->setError('import', sprintf($this->language->get('error_filename'), 5, 128));
			}

			if ($this->request->files['import']['error'] != UPLOAD_ERR_OK) {
				$this->setError('import', $this->language->get('error_upload_' . $this->request->files['import']['error']));
			}
		} else {
			$this->setError('import', $this->language->get('error_import'));
		}

		return !$this->hasError();
	}

	private function validateFields(&$data, $required, $optional, $skip_exists = false, $row = 0) {
		$missing = array();

		// ensure all required data fields are present
		if (!empty(array_diff_key($required, array_keys(array_intersect_key($data, array_flip($required)))))) {
			$this->appendError('input_data', $this->language->get('error_fields_missing') . ($row ? ': Row ' . $row : '!'));
			//$this->log->write('DATA INCOMPLETE: ' . json_encode($data) . "\nFIELDS FOUND:" . json_encode(array_keys(array_intersect_key($data, array_flip($required)))));
			return false;
		}

		// clean
		foreach ($data as $key => $value) {
			if (!in_array($key, array_merge($required, $optional), true)) {
				// remove all extra fields
				unset($data[$key]);
			} else if (in_array($key, $required, true) && empty($data[$key])) {
				// ensure all required data fields contain a value
				$missing[] = $key;
			} else if ($row > 0) {
				// clean for data imported from file
				$data[$key] = $this->request->clean($value);
			}
		}

		if ($missing) {
			$this->appendError('input_data', $this->language->get('error_fields_missing') . ($row ? ': Row ' . $row : '!'));
			//$this->log->write('DATA INCOMPLETE: ' . json_encode($data) . "\nEMPTY FIELDS: " . json_encode($missing));
			return false;
		}

		// check if a similar active listing already exists for anon/catalog listings (i.e. not linked to a profile)
		// (brand, model, year, size, category, and sub-category (and language and store))
		if (!$skip_exists) {
			$product_exists = $this->model_account_product->getAnonListingByData($data); // returns array with product_id and name

			if ($product_exists) {
				$product_exists_url = $this->url->link('product/product', 'product_id=' . $product_exists['product_id'], 'SSL');
				$this->appendError('input_data', sprintf($this->language->get('error_anonpost_exists'), $product_exists_url, $product_exists['name']));
				$this->log->write('LISTING EXISTS (' . $product_exists_url . '): ' . json_encode($data));
				return false;
			}
		}

		return true;
	}

	private function validateForm(&$data) {
		$product_description_data = array();

		$data_required = array(
			'link',
			'category_id',
			'sub_category_id',
			'manufacturer_id',
			'model',
			'size'
		);

		$data_optional = array(
			'third_category_id',
			'year',
			'condition_id',
			'price',
			'product_description'
		);

		if (!$this->isAdmin()) {
			$data_required[] = 'image';
		} else {
			$data_optional_admin = array(
				'approved',
				'status',
				'image',
				'image_url',
				'image_file'
			);

			$data_optional = array_merge($data_optional, $data_optional_admin);
		}

		if (!empty($data['product_description'])) {
			foreach ($data['product_description'] as $language_id => $value) {
				if (!empty($value['name'])) {
					if (!$this->validateStringLength($value['name'], 3, 255) || !preg_match('/^[a-zA-Z0-9-_ \(\)\.\'\"\/\&]*$/', htmlspecialchars_decode($value['name']))) {
						$this->appendError('name', sprintf($this->language->get('error_name'), 3, 255), $language_id);
					}

					/* enforce unique Product Names */
					// if ($this->model_account_product->getTotalProductsByName($value['name'], $language_id)) {
					//  $this->appendError('name', $this->language->get('error_name_exists'), $language_id);
					// }
				}

				if (!empty($value['description'])) {
					if (!$this->validateStringLength($value['description'], $this->config->get('member_data_field_description_min'), $this->config->get('member_data_field_description_max'))) {
						$this->appendError('description', sprintf($this->language->get('error_description'), $this->config->get('member_data_field_description_min'), $this->config->get('member_data_field_description_max')), $language_id);
					}
				}

				// if (!$value['tag']) {
				//  $this->appendError('tag', $this->language->get('error_tag_required'), $language_id);
				// } else
				if (!$this->validateStringLength($value['tag'], 0, 255)) {
					$this->appendError('name', $this->language->get('error_tag'), $language_id);
				}
			}
		}

		if (empty($data['link']) || !$this->validateUrl($data['link'])) {
			$this->setError('link', $this->language->get('error_link'));
		}

		if ($this->config->get('member_data_field_image')) {
			if (empty($data['image']) && empty($data['image_url']) && empty($data['image_file']['tmp_name'])) {
				$this->setError('image', $this->language->get('error_image'));
			} else if (!empty($data['image'])) {
				if (!is_file(DIR_IMAGE . $data['image'])) {
					$this->setError('image', $this->language->get('error_exists'));
				} else if (!$this->validateStringLength($data['image'], 5, 255)) {
					$this->setError('image', $this->language->get('error_image'));
				} else {
					$this->image = $data['image'];  // sub-path to image uploaded via ajax
				}
			} else if ($this->isAdmin() && !empty($data['image_file']['tmp_name'])) {
				if ($data['image_file']['error'] != UPLOAD_ERR_OK) {
					$this->setError('image_file', $this->language->get('error_upload_' . $data['image_file']['error']));
				} else if (!is_uploaded_file($data['image_file']['tmp_name']) || !file_exists($data['image_file']['tmp_name'])) {
					$this->setError('image_file', $this->language->get('error_upload'));
				}
			} else if ($this->isAdmin() && !empty($data['image_url'])) {
				if (!$this->validateUrl($data['image_url'])) {
					$this->setError('image_url', $this->language->get('error_link'));
				}
			}
		}

		if ($this->config->get('member_data_field_category')) {
			if (empty($data['category_id']) || !$this->validateNumeric($data['category_id'], true)) {
				$this->setError('category', $this->language->get('error_category'));
			}

			if (empty($data['sub_category_id']) || !$this->validateNumeric($data['sub_category_id'], true)) {
				$this->setError('category', $this->language->get('error_category'));
				$this->setError('category_sub', $this->language->get('error_category_sub'));
			}
		}

		if ($this->config->get('member_data_field_manufacturer')) {
			if (empty($data['manufacturer_id']) || !$this->validateNumeric($data['manufacturer_id'], true)) {
				$this->setError('manufacturer', $this->language->get('error_manufacturer'));
			}
		}

	    if (empty($data['model']) || !$this->validateStringLength($this->request->post['model'], 1, 128)) {
			$this->setError('model', $this->language->get('error_model'));
	    }

		if (empty($data['size']) || !$this->validateStringLength($this->request->post['size'], 1, 128)) {
			$this->setError('size', $this->language->get('error_size'));
		}

		if (!empty($data['price']) && !$this->validatePrice($data['price'])) {
			$this->setError('price', $this->language->get('error_price'));
		}

		// if (!$data['year'] || !$this->validateYear($data['year'])) {
		// 	$this->setError('year', $this->language->get('error_year_required'));
		// }

		// multiple geographical markets
		// if ($this->config->get('member_data_field_store') && empty($data['product_store'])) {
		// 	$this->setError('product_store', $this->language->get('error_product_store'));
		// }

		// strips out any invalid post data fields
		if (!$this->validateFields($data, $data_required, $data_optional)) {
			$this->setError('warning', implode('<br />', $this->getError('input_data')));
		}

		if ($this->hasError() && !$this->getError('warning')) {
			$this->setError('warning', $this->language->get('error_warning'));
		}

		return !$this->hasError();
	}

	private function validateImageFile($filepath, $row = 0) {
		if (empty($filepath)) return false;

		$image_mimetypes_valid = array('image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png');
		$image_dimensions_min_width = $this->config->get('member_image_dimensions_min_width') ? (int)$this->config->get('member_image_dimensions_min_width') : 450;
		$image_dimensions_min_height = $this->config->get('member_image_dimensions_min_height') ? (int)$this->config->get('member_image_dimensions_min_height') : 450;
		$image_dimensions_resize_width = (int)$this->config->get('member_image_dimensions_resize_width');
		$image_dimensions_ratio_min = 0.5;
		$image_dimensions_ratio_max = 2;
		$resize_image = false;
		$validate_file_error = false;

		// first, validate mime type
		$source_mime_type = mime_content_type(DIR_IMAGE . $filepath);

		if (!in_array($source_mime_type, $image_mimetypes_valid)) {
			$validate_file_error = $this->language->get('error_filetype');
		} else {
			list($image_width, $image_height) = getimagesize(DIR_IMAGE . $filepath);
			$image_dimensions_ratio = (float)($image_width / $image_height);

			// image dimensions ratio not within range?
			if ($image_dimensions_ratio < $image_dimensions_ratio_min || $image_dimensions_ratio > $image_dimensions_ratio_max) {
				if ($this->isAdmin()) {
					if ($image_dimensions_ratio < $image_dimensions_ratio_min) {
						$resized_width = (int)($image_height * 0.618);
						$resized_height = $image_height;
					} else {
						$resized_width = $image_width;
						$resized_height = (int)($image_width * 0.618);
					}

					$resize_image = true;
					$image_width = $resized_width;
					$image_height = $resized_height;
				} else {
					$validate_file_error = 'Image width to height ratio must be between 1:2 and 2:1!'; // sprintf('Image height to width ratio must be between %d and %d!', $image_dimensions_ratio_min, $image_dimensions_ratio_max);
				}
			}

			// image dimensions not withing range?
			if ($image_width < $image_dimensions_min_width || $image_height < $image_dimensions_min_height) {
				if ($this->isAdmin() && ($image_dimensions_min_width - $image_width <= 100) && ($image_dimensions_min_height - $image_height <= 100)) {
					$resized_width = $image_width < $image_dimensions_min_width ? $image_dimensions_min_width : $image_width;
					$resized_height = $image_height < $image_dimensions_min_height ? $image_dimensions_min_height : $image_height;

					$resize_image = true;
				} else {
					$validate_file_error = 'Image must be at least ' . $image_dimensions_min_width . 'px by ' . $image_dimensions_min_height . 'px!';
				}
			} else if ($image_dimensions_resize_width > 0 && $image_width > $image_dimensions_resize_width) {
				$resized_width = $image_dimensions_resize_width;
				$resized_height = floor($image_dimensions_resize_width * $image_height / $image_width);

				$resize_image = true;
			}
		}

		if (!$validate_file_error) {
			if ($resize_image) {
				$this->model_tool_image->edit(DIR_IMAGE . $filepath, $resized_width, $resized_height);
			}

			$this->image = $filepath;

			return true;
		} else {
			if (strpos($filepath, 'anonpost-image-url') !== false) {
				if ($row > 0) {
					$validate_file_error .= ' | Row ' . ($row + 1);
				}

				$this->setError('image_url', $validate_file_error);
			} else {
				$this->setError('image_file', $validate_file_error);
			}

			return false;
		}
	}

	private function prepareData(&$data, $csrf_token_set) {
		if (empty($data)) return;

		$data = strip_tags_decode($data);

		$listing_link = str_replace(' ', '%20', $data['link']);
		$listing_categories = array($data['category_id'], $data['sub_category_id']);

		$product_category_main = $this->model_catalog_category->getCategory($data['category_id']);
		$product_category_sub = $this->model_catalog_category->getCategory($data['sub_category_id']);

		if (!empty($data['third_category_id'])) {
			$listing_categories[] = $data['third_category_id'];
			$product_category_third = $this->model_catalog_category->getCategory($data['third_category_id']);
		}

		$product_manufacturer = (int)$data['manufacturer_id'] > 1 ? $this->model_catalog_manufacturer->getManufacturer($data['manufacturer_id']) : '';

		$listing_brand = $product_manufacturer ? ucwords($product_manufacturer['name']) : '';
		$listing_model = ucwords($data['model']);
		$listing_size = ucwords($data['size']);
		$listing_year = !empty($data['year']) ? $data['year'] : '0000';
		$listing_price = !empty($data['price']) ? round($data['price'], 2) : 0;
		$listing_condition_id = !empty($data['condition_id']) ? (int)($data['condition_id']) : 0;
		$listing_location = !empty($data['location']) ? $data['location'] : '';
		$listing_customer_id = !empty($data['member_customer_id']) ? $data['member_customer_id'] : ($this->customer->isLogged() ? $this->customer->getId() : 0);

		// location
		if (!empty($data['zone_id']) && !empty($data['country_id'])) {
			$listing_country = $this->model_localisation_country->getCountry($data['country_id']);
			$listing_zone = $this->model_localisation_zone->getZone($data['zone_id']);
			$listing_location_full = $listing_zone['name'] . ', ' . $listing_country['name'];

			if ($listing_location) {
				$listing_location_full = $listing_location . ', ' . $listing_location_full;
			}
		} else {
			$data['country_id'] = 0;
			$data['zone_id'] = 0;
			$listing_location = '';
			$listing_location_full = '';
		}

		// build name/title
		$listing_name_parts = array();

		if ($listing_brand) {
			$listing_name_parts[] = $listing_brand;
		}

		if (utf8_strpos(trim($listing_size), ' ') === false) {
			$listing_name_parts[] = $listing_size;
		}

		$listing_name_parts[] = $listing_model;

		if ($listing_year && $listing_year != '0000' && utf8_strpos($listing_model, $listing_year) === false) {
			$listing_name_parts[] = $listing_year;
		}

		$listing_name = implode(' ', $listing_name_parts);

		$listing_keyword = friendly_url(html_entity_decode($listing_name, ENT_QUOTES, 'UTF-8'));

		$data['link'] = $listing_link;
		$data['product_category'] = $listing_categories;
		$data['keyword'] = 'listing-' . $listing_keyword . '-' . mt_rand(); // substr(md5(mt_rand()), 0, 10)
		$data['model'] = $listing_model;
		$data['size'] = $listing_size;
		$data['year'] = $listing_year;
		$data['price'] = $listing_price;
		$data['location'] = $listing_location;
		$data['member_customer_id'] = $listing_customer_id;
		$data['quantity'] = -1; // $listing_price ? 0 : -1;

		if (empty($data['product_description'])) {
			$data['product_description'][1] = array();
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$data['product_description'][$language_id]['name'] = !empty($value['name']) ? $value['name'] : $listing_name;
			$data['product_description'][$language_id]['description'] = !empty($value['description']) ? $data['link'] . "\n\r\n\r" . $value['description'] : $data['link'];
			$data['product_description'][$language_id]['tag'] = !empty($value['tag']) ? preg_replace('/[\s,#]+/', ', $1', trim(strtolower($value['tag']), " \t\n\r\0\x0B,#")) : '';
			$data['product_description'][$language_id]['meta_keyword'] = !empty($value['tag']) ? strtolower($value['tag']) : '';

			// $meta_description = $this->language->get('entry_name') . ': ' . ucwords($value['name']) . '; ';
			$meta_description = $product_manufacturer ? $this->language->get('entry_manufacturer') . ': ' . $product_manufacturer['name'] . '; ' : '';
			$meta_description .= $this->language->get('entry_model') . ': ' . $data['model'] . '; ';
			$meta_description .= ($listing_year && $listing_year != '0000') ? $this->language->get('entry_year') . ': ' . $data['year'] . '; ' : '';
			$meta_description .= $this->language->get('entry_size') . ': ' . $data['size'] . '; ';
			$meta_description .= $this->language->get('entry_category') . ': ' . $product_category_main['name'] . ', ' . $product_category_sub['name'];
			$meta_description .= !empty($product_category_third['name']) ? ', ' . $product_category_third['name'] . '; ' : ';';
			$meta_description .= $listing_location_full ? $this->language->get('entry_location') . ': ' . $listing_location_full . '; ' : '';
			// $meta_description .= $this->language->get('entry_description') . ': ' . utf8_substr(strip_tags_decode($value['description']), 0, 100) . ';';

			$data['product_description'][$language_id]['meta_description'] = $meta_description;
		}

		$data['product_store'] = array('0'); // (int)$this->config->get('config_store_id')
		$data['date_available'] = date('Y-m-d H:i:s', time() - (60 * 60 * 24)); // now minus one day to ensure ASAP
		$data['date_expiration'] = $this->isAdmin()
			? date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 365 * 10))
			: date('Y-m-d H:i:s', time() + (60 * 60 * 24 * 365 * 5)); // 5 years; 90 days = (90 * 24 * 60 * 60)
		$data['mpn'] = '';
		$data['sku'] = '';
		$data['upc'] = '';
		$data['ean'] = '';
		$data['jan'] = '';
		$data['isbn'] = $this->isAdmin() ? 'adminpost' : 'anonpost';
		$data['stock_status_id'] = $this->config->get('config_stock_status_id');
		$data['sort_order'] = '10'; // lowest priority (1-10 scale)
		$data['tax_class_id'] = '0'; // None
		$data['status'] = $this->isAdmin() && isset($data['status']) ? $data['status'] : '1'; // auto-enabled by default
		$data['approved'] = $this->isAdmin() && isset($data['approved'])
			? $data['approved']
			: ($this->customer->isLogged() ? '1' : '0'); // pending approval by default, unless logged in

		// Filters
		$data['product_filters'] = array();

		if ($listing_condition_id > 0) {
			$data['product_filters'][] = $listing_condition_id; // condition 6 - 10 (New, Excellent, Good, Fair, Poor)
		}

		if ($listing_year && $listing_year != '0000') {
			$listing_age = (int)date('Y') - (int)$data['year']; // this year minus listing year

			if ($listing_age < 2) {
				$data['product_filters'][] = 11;
			} else if ($listing_age <= 5) {
				$data['product_filters'][] = 12;
			} else if ($listing_age <= 10) {
				$data['product_filters'][] = 13;
			} else  {
				$data['product_filters'][] = 14; // greater than 10 years old
			}
		} else {
			$data['product_filters'][] = 15; // unknown
		}

		$price_rounded = (int)$listing_price;

		if ($price_rounded > 0) {
			if ($price_rounded < 50) {
				$data['product_filters'][] = 1;
			} else if ($price_rounded <= 100) {
				$data['product_filters'][] = 2;
			} else if ($price_rounded <= 500) {
				$data['product_filters'][] = 3;
			} else if ($price_rounded <= 1000) {
				$data['product_filters'][] = 4;
			} else {
				$data['product_filters'][] = 5; // more than $1,000
			}
		}

		// Image
		if ($this->image) {
			if (!empty($data['member_directory_images'])) {
				$destination_directory = 'data/' . clean_path($data['member_directory_images']);
			} else {
				// get/create category sub-directory path
				$categories_path = friendly_url($product_category_main['name']) . '/' . friendly_url($product_category_sub['name']);
				$categories_path .= !empty($product_category_third) ?  '/' . friendly_url($product_category_third['name'])  : '';
				$destination_directory = 'data/catalog/' . clean_path($categories_path);
			}

			$new_image = $this->model_tool_image->move($this->image, $destination_directory, $listing_keyword . '-' . $csrf_token_set, false);

			if ($new_image) {
				$data['image'] = $new_image; // e.g. /data/catalog/category/sub-category/brandname-size-model-year-123456s.jpg
			}
		}
	}

	private function getImageFromUrl($url, $destination_directory) {
		if (!$this->isAdmin()) {
			$this->setError('image_url', $this->language->get('error_image_url_restricted'));
			return false;
		}

		$url = str_replace(' ', '%20', $url);

		// strip off any query params before stripping file extension
		$image_url = utf8_strpos($url, '?') !== false ? utf8_substr($url, 0, utf8_strpos($url, '?')) : $url;
		$image_url_ext = substr(strrchr(strtolower($image_url), '.'), 1);
		$image_url_filename = substr(strrchr(strtolower($image_url), '/'), 1);

		// first, check file extension and filename length before downloading
		$allowed_filetypes = array('jpg', 'jpeg', 'png');

		if (!in_array($image_url_ext, $allowed_filetypes)) {
			$this->setError('image_url', $this->language->get('error_filetype'));
		} else if ((utf8_strlen($image_url_filename) < 5) || (utf8_strlen($image_url_filename) > 128)) {
			$this->setError('image_url', sprintf($this->language->get('error_filename'), 5, 128));
		}

		// image url is a valid link and is a file with a valid filename length and valid extension
		// ...now try to download it
		if (!$this->getError('image_url')) {
			// copy image from url and save to a token sub-dir in /image/data/temp/ (same location as for file upload)
			$image_directory = 'data/' . clean_path('temp/' . $destination_directory);
			if (!is_dir(DIR_IMAGE . $image_directory)) mkdir(DIR_IMAGE . $image_directory, 0755, true);
			$new_filename = $image_directory . '/' . 'anonpost-image-url-' . mt_rand() . '.' . $image_url_ext;

			// stream_context_set_default(
			// 	array(
			//         'http' => array(
			//             'method' => 'HEAD'
			//         )
			//     )
			// );

			$context = stream_context_create(array(
				'http' => array(
					'method' => 'GET',
					'header' => array('User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; rv:2.2) Gecko/20110201')
				)
			));

			if (!@copy($image_url, DIR_IMAGE . $new_filename, $context)) {
				$this->setError('image_url', $this->language->get('error_upload'));
			}

			// $image_url_headers = get_headers($image_url, 1);
            //
			// if (!$image_url_headers) {
			// 	$this->setError('image_url', $this->language->get('error_image_url'));
			// } else {
			// 	$image_url_header_status = is_array($image_url_headers[0]) ? end($image_url_headers[0]) : $image_url_headers[0];
			// 	$image_url_header_status_code = intval(substr($image_url_header_status, 9, 3));
            //
			// 	if ($image_url_header_status_code == 0 || $image_url_header_status_code >= 400) {
			// 		$this->setError('image_url', $this->language->get('error_image_url') . ' ' . $image_url_header_status_code);
			// 	} else if (!@copy($image_url, DIR_IMAGE . $new_filename)) {
			// 		$this->setError('image_url', $this->language->get('error_upload'));
			// 	}
			// }
		}

		if (!$this->getError('image_url')) {
			return $new_filename;
		} else {
			return false;
		}
	}

	private function removeTempImages($dir_token, $keep_img = array()) {
		if (empty($dir_token)) {
			return false;
		}

		$return = false;
		$time_now = time();

		$path_temp_token = clean_path(rtrim(DIR_IMAGE . 'data/temp/' . $dir_token));
		$path_temp_token_cache = clean_path(rtrim(DIR_IMAGE_CACHE . 'data/temp/' . $dir_token));

		$paths_token = array_merge(
			glob_recursive($path_temp_token . '/*', GLOB_NOSORT),
			glob_recursive($path_temp_token_cache . '/*', GLOB_NOSORT)
		);

		$dirs_temp = array_merge(
			glob_recursive(DIR_IMAGE . 'data/temp/*', GLOB_ONLYDIR|GLOB_NOSORT),
			glob_recursive(DIR_IMAGE_CACHE . 'data/temp/*', GLOB_ONLYDIR|GLOB_NOSORT)
		);

		if (!empty($keep_img['image_path'])) {
			$return = $this->model_tool_image->move($keep_img['image_path'], 'data/temp/' . clean_path($keep_img['csrf_token']), 'anonpost-image-' . mt_rand(), false);
		}

		foreach ($paths_token as $path) {
			if (file_exists($path) && is_file($path)) {
				unlink($path);
				// $this->log->write('AUTO DELETED temp img: ' . $path);
			}
		}

		if (file_exists($path_temp_token) && is_dir($path_temp_token)) {
			rmdir($path_temp_token);
			// $this->log->write('AUTO DELETED temp dir: ' . $path_temp_token);
		}

		// also remove all dirs and files in ../image/data/temp that are older than 15 minutes
		foreach ($dirs_temp as $dir) {
			if (file_exists($dir) && is_dir($dir)) {
				$dir_age = $time_now - filemtime($dir . '/.');
				//$this->log->write($dir . '|' . $time_now . '|' . $dir_age);

				if ($dir_age >= 60 * 15) {
					$files = array_filter(glob(rtrim($dir) . '/*', GLOB_NOSORT), 'is_file');

					foreach ($files as $file) {
						unlink($file);
						// $this->log->write('AUTO DELETED temp img: ' . $file);
					}

					rmdir($dir);
					// $this->log->write('AUTO DELETED temp dir: ' . $dir);
				}
			}
		}

		return $return;
	}

}
?>
