<?php
class ControllerAjaxFileManager extends Controller {
	use ValidateField;

	public function index() {
		if (!$this->validateUser()) {
			return false;
		}

		if (!$this->request->checkReferer($this->config->get('config_url')) && !$this->request->checkReferer($this->config->get('config_ssl'))) {
			$this->session->data['redirect'] = $this->url->link('account/product', '', 'SSL');
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}

		$this->data = $this->load->language('account/filemanager');

		$this->data['title'] = $this->language->get('heading_title');

		$this->data['base'] = $this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url'); // HTTPS_SERVER vs HTTP_SERVER

		$this->data['dir_member_image'] = $this->customer->getMemberImagesDirectory(); // dir_member_image, e.g. 'members/temp/'
		$this->data['url_member_image'] = $this->data['base'] . 'image/data/' . $this->customer->getMemberImagesDirectory();

		$this->load->model('tool/image');

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'), 'autocrop'); // 205 x 295

		$this->data['field'] = isset($this->request->get['field']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '', $this->request->get['field']) : '';
		$this->data['text_editor'] = isset($this->request->get['texteditorfuncnum']) ? (int)$this->request->get['texteditorfuncnum'] : false;

		$minify = $this->cache->get('minify');
		$js_min = isset($minify['js']) ? $minify['js'] : '';

		$this->data['fingerprint'] = $js_min ? '?v=' . rtrim(substr($js_min, strpos($js_min, '-') + 1), '.min.js') : '';
		$this->data['server'] = CDN_SERVER ?: ($this->request->isSecure() ? $this->config->get('config_ssl') : $this->config->get('config_url'));

		$this->template = 'template/account/filemanager.tpl';

		$this->response->setOutput($this->render());
	}

	public function image() {
		if (!$this->validateUser() || empty($this->request->get['image'])) {
			return false;
		}

		$image = (string)$this->request->get['image'];

		if (strpos($image, 'data/') === 0 && strpos(ltrim($image, 'data/'), $this->customer->getMemberImagesDirectory()) === 0) {
			// e.g. "data/member/m/member-name/listing-001.jpg"
			$image = $this->request->get['image'];
		} else {
			// e.g. "/listing-001.jpg"
			$image = 'data/' . $this->customer->getMemberImagesDirectory() . $this->request->get['image'];
		}

		if (!is_file(DIR_IMAGE . $image)) {
			return false;
		}

		$this->load->model('tool/image');

		$field = isset($this->request->get['field']) ? $this->request->get['field'] : '';
		$width = !empty($this->request->get['width']) ? (int)$this->request->get['width'] : $this->config->get('config_image_product_width');
		$height = !empty($this->request->get['height']) ? (int)$this->request->get['height'] : $this->config->get('config_image_product_height');

		if ($field === 'member_account_banner') {
			$width = 1000;
			$height = 300;
		} else if ($field === 'member_account_image') {
			$width = 250;
			$height = 250;
		}

		$json = $this->model_tool_image->resize(html_entity_decode($image, ENT_QUOTES, 'UTF-8'), $width, $height, 'autocrop');

		$this->response->setOutput(json_encode($json));
	}

	public function directory() {
		if (!$this->validateUser() || !isset($this->request->post['directory'])) {
			return false;
		}

		$json = array();

		$directories = glob(rtrim(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory() . clean_path($this->request->post['directory']), '/') . '/*', GLOB_ONLYDIR);

		if ($directories) {
			$i = 0;

			foreach ($directories as $directory) {
				$json[$i]['data'] = basename($directory);
				$json[$i]['attributes']['directory'] = utf8_substr($directory, utf8_strlen(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory()));

				$children = glob(rtrim($directory, '/') . '/*', GLOB_ONLYDIR);

				if ($children)  {
					$json[$i]['children'] = ' ';
				}

				$i++;
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function files() {
		if (!$this->validateUser() || !isset($this->request->post['directory'])) {
			return false;
		}

		$json = array();

		$allowed = array(
			'.jpg',
			'.jpeg',
			'.png',
			'.gif'
		);

		$suffix = array(
			'B',
			'KB',
			'MB',
			'GB',
			'TB',
			'PB',
			'EB',
			'ZB',
			'YB'
		);

		$files = $this->customer->getMemberImages(clean_path($this->request->post['directory']), false);

		if (!$files) {
			return false;
		}

		$this->load->model('tool/image');
		$count = 0;
		$max_age = $this->config->get('member_image_orphan_max_age');

		// sort by age desc
		array_multisort(array_map('filemtime', $files), SORT_NUMERIC, SORT_DESC, $files);

		$image_ophans = $this->customer->getMemberImageOrphans($files);

		foreach ($files as $file) {
			if (!is_file($file)) {
				continue;
			}

			$ext = strrchr($file, '.');

			if (!in_array(strtolower($ext), $allowed)) {
				continue;
			}

			$size = filesize($file);

			$i = 0;

			while (($size / 1024) > 1) {
				$size = $size / 1024;
				$i++;
			}

			$filename = basename($file);
			$file_short = utf8_substr($file, utf8_strlen(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory()));
			$file_last_modified = filemtime($file);  // date('M d, Y', filemtime($file))
			$file_age = $file_last_modified ? time() - $file_last_modified : 'unknown';
			$file_old = ($file_age > $max_age || $file_age === 'unknown');
			$file_orphaned = in_array($file, $image_ophans);

			$json[] = array(
				'img'	   => $count >= $this->config->get('config_catalog_limit') ? '' : $this->model_tool_image->resize('data/' . $this->customer->getMemberImagesDirectory() . $file_short, $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'), 'autocrop'),
				'filename' => utf8_strlen($filename) <= 65 ? $filename : utf8_substr($filename, 65) . $this->language->get('text_ellipses'),
				'file'     => $file_short,
				'size'     => round(utf8_substr($size, 0, utf8_strpos($size, '.') + 4), 2) . $suffix[$i],
				'orphaned' => $file_orphaned,
				'expired'  => ($file_orphaned && $file_old)
			);

			$count++;
		}

		$this->response->setOutput(json_encode($json));
	}

	public function create() {
		if (!$this->validateUser()) {
			return false;
		}

		$this->load->language('account/filemanager');

		$json = array();

    	// if (!$this->validateCreate()) {
      	// 	$json['error'] = $this->language->get('error_permission');
    	// }

		// deny all requests (temp)
		$json['error'] = $this->language->get('error_permission');

		if (empty($this->request->post['name'])) {
			$json['error'] = $this->language->get('error_name');
		}

		if (empty($this->request->post['directory'])) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!isset($json['error'])) {
			$directory = rtrim(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory() . clean_path($this->request->post['directory']), '/');

			if (!is_dir($directory)) {
				$json['error'] = $this->language->get('error_directory');
			}

			if (file_exists($directory . '/' . clean_path($this->request->post['name']))) {
				$json['error'] = $this->language->get('error_exists');
			}
		}

		if (!isset($json['error'])) {
			mkdir($directory . '/' . clean_path($this->request->post['name']), 0777);

			$json['success'] = $this->language->get('text_create');
		}

		$this->response->setOutput(json_encode($json));
	}

	public function rename() {
		$this->load->language('account/filemanager');

		$json = array();

		if (!$this->validateUser()) {
			$json['error'] = $this->language->get('error_permission');
		}

		// deny all requests (temp)
		// $json['error'] = $this->language->get('error_permission');

		if (!isset($json['error']) && !isset($this->request->post['path'])) {
			$json['error'] = $this->language->get('error_file');
		}

		if (!isset($json['error']) && !isset($this->request->post['name'])) {
			$json['error'] = $this->language->get('error_name');
		}

		if (!isset($json['error']) && !$this->validateStringLength($this->request->post['name'], 5, 255)) {
			$json['error'] = sprintf($this->language->get('error_filename'), 5, 255);
		}

		if (!isset($json['error'])) {
			$path_relative = 'data/' . $this->customer->getMemberImagesDirectory() . clean_path(html_entity_decode($this->request->post['path'], ENT_QUOTES, 'UTF-8'));
			$path_ext = substr(strrchr(strtolower($path_relative), '.'), 1);  // jpg (file extension)
			$path = rtrim(DIR_IMAGE . $path_relative, '/');

			if (!is_file($path)) {
				$json['error'] = $this->language->get('error_file');
			}

			if ($path == rtrim(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory(), '/') || $path == rtrim(DIR_IMAGE . 'data/', '/')) {
				$json['error'] = $this->language->get('error_delete');
			}

			$filename = friendly_url(clean_path(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8')));
			$new_filename = 'data/' . $this->customer->getMemberImagesDirectory() . '/' . $filename . '.' . $path_ext;
			$path_new = DIR_IMAGE . $new_filename;  // web-root-home-dir/image/data/member/m/member-name/new-filename-001.jpg

			if (file_exists($path_new)) {
				$json['error'] = $this->language->get('error_exists');
			}

			// get all member images in use
			$images_active = $this->customer->getMemberImagesInUse();

			// check if the requested path is in use
			if (preg_grep("/^" . preg_quote($path_relative, '/') . ".*/", $images_active)) {
				$json['error'] = sprintf($this->language->get('error_in_use'), basename($path_relative));
			}
		}

		if (!isset($json['error'])) {
			// move image
			if (rename($path, $path_new)) {
				$json['success'] = sprintf($this->language->get('text_rename'), basename($path_new));
			} else {
				$json['error'] = $this->language->get('error_rename');
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function delete() {
		$this->load->language('account/filemanager');

		$json = array();

    	if (!$this->validateUser()) {
      		$json['error'] = $this->language->get('error_permission');
    	}

		// deny all requests (temp)
		// $json['error'] = $this->language->get('error_permission');

		if (!isset($json['error']) && !isset($this->request->post['path'])) {
			$json['error'] = $this->language->get('error_file');
		}

		if (!isset($json['error'])) {
			$path_relative = 'data/' . $this->customer->getMemberImagesDirectory() . clean_path(html_entity_decode($this->request->post['path'], ENT_QUOTES, 'UTF-8'));

			$path = rtrim(DIR_IMAGE . $path_relative, '/');

			if (!file_exists($path)) {
				$json['error'] = $this->language->get('error_file');
			}

			if ($path == rtrim(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory(), '/') || $path == rtrim(DIR_IMAGE . 'data/', '/')) {
				$json['error'] = $this->language->get('error_delete');
			}

			// get all member images in use
			$images_active = $this->customer->getMemberImagesInUse();

			// check if the requested path is in use
			if (preg_grep("/^" . preg_quote($path_relative, '/') . ".*/", $images_active)) {
				$json['error'] = sprintf($this->language->get('error_in_use'), basename($path_relative));
			}
		}

		if (!isset($json['error'])) {
			if (is_file($path)) {
				unlink($path);
			} elseif (is_dir($path)) {
				$this->recursiveDelete($path);
			}

			$json['success'] = sprintf($this->language->get('text_delete'), basename($path_relative));
		}

		$this->response->setOutput(json_encode($json));
	}

	private function recursiveDelete($directory) {
		if (file_exists($directory) && strpos($directory, DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory()) !== false) {
			if (is_dir($directory)) {
				$handle = opendir($directory);
			}

			if (!$handle) {
				return false;
			}

			while (false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..') {
					if (!is_dir($directory . '/' . $file)) {
						unlink($directory . '/' . $file);
					} else {
						$this->recursiveDelete($directory . '/' . $file);
					}
				}
			}

			closedir($handle);

			rmdir($directory);

			return true;
		} else {
			return false;
		}
	}

	public function copy() {
		$this->load->language('account/filemanager');

		$json = array();

    	if (!$this->validateUser()) {
      		$json['error'] = $this->language->get('error_permission');
    	}

		// deny all requests (temp)
		$json['error'] = $this->language->get('error_permission');

		if (!isset($json['error']) && (!isset($this->request->post['path']) || !isset($this->request->post['name']))) {
			$json['error'] = $this->language->get('error_file');
		}

		if (!isset($json['error']) && isset($this->request->post['path']) && isset($this->request->post['name'])) {
			if (!$this->validateStringLength($this->request->post['name'], 5, 255)) {
				$json['error'] = sprintf($this->language->get('error_filename'), 5, 255);
			}

			$old_name = rtrim(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory() . clean_path(html_entity_decode($this->request->post['path'], ENT_QUOTES, 'UTF-8')), '/');

			if (!file_exists($old_name) || $old_name == DIR_IMAGE . 'data') {
				$json['error'] = $this->language->get('error_copy');
			}

			if (is_file($old_name)) {
				$ext = strrchr($old_name, '.');
			} else {
				$ext = '';
			}

			$new_name = dirname($old_name) . '/' . clean_path(html_entity_decode($this->request->post['name'], ENT_QUOTES, 'UTF-8') . $ext);

			if (file_exists($new_name)) {
				$json['error'] = $this->language->get('error_exists');
			}
		}

		if (!isset($json['error'])) {
			if (is_file($old_name)) {
				copy($old_name, $new_name);
			} else {
				$this->recursiveCopy($old_name, $new_name);
			}

			$json['success'] = $this->language->get('text_copy');
		}

		$this->response->setOutput(json_encode($json));
	}

	private function recursiveCopy($source, $destination) {
		$directory = opendir($source);

		@mkdir($destination);

		while (false !== ($file = readdir($directory))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($source . '/' . $file)) {
					$this->recursiveCopy($source . '/' . $file, $destination . '/' . $file);
				} else {
					copy($source . '/' . $file, $destination . '/' . $file);
				}
			}
		}

		closedir($directory);
	}

	public function folders() {
		if (!$this->validateUser()) {
			return false;
		}

		$this->response->setOutput($this->recursiveFolders(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory()));
	}

	private function recursiveFolders($directory) {
		if (!$directory) {
			return false;
		}

		$output = '';

		$sub_directory = utf8_substr($directory, utf8_strlen(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory()));

		if ($sub_directory) {
			$output .= '<option value="' . $sub_directory . '">' . $sub_directory . '</option>';
		}

		$directories = glob(rtrim(clean_path($directory), '/') . '/*', GLOB_ONLYDIR);

		foreach ($directories  as $directory) {
			$output .= $this->recursiveFolders($directory);
		}

		return $output;
	}

	public function upload() {
		$this->load->language('account/filemanager');

		$json = array();

    	if (!$this->validateUser()) {
      		$json['error'][] = $this->language->get('error_permission');
    	}

		if (!isset($json['error']) && isset($this->request->post['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory() . clean_path($this->request->post['directory']), '/');

			if (!is_dir($directory)) {
				$json['error'][] = $this->language->get('error_directory');
			}

			if (isset($this->request->files['image']) && $this->request->files['image']['tmp_name']) {

				$allowed = array(
					'image/jpeg',
					'image/pjpeg',
					'image/png',
					'image/x-png'
				);

				$allowed_ext = array(
					'.jpg',
					'.jpeg',
					'.png'
				);

				$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['image']['name'], ENT_QUOTES, 'UTF-8')));

				if (!in_array($this->request->files['image']['type'], $allowed) || !in_array(strtolower(strrchr($filename, '.')), $allowed_ext) || !getimagesize($this->request->files['image']['tmp_name'])) {
					$json['error'][] = $this->language->get('error_file_type');
				} else {
					list($image_width, $image_height) = getimagesize($this->request->files['image']['tmp_name']);

					if ($image_width < $this->config->get('member_image_dimensions_min_width') && $image_height < $this->config->get('member_image_dimensions_min_height')) {
						$json['error'][] = sprintf($this->language->get('error_file_dimensions_small'), $this->config->get('member_image_dimensions_min_width'), $this->config->get('member_image_dimensions_min_height'));
					}

					if ($image_height > $image_width * 2) {
						$json['error'][] = 'Image height cannot be more than double its width!';
					}
				}

				if ($this->request->files['image']['size'] > $this->config->get('member_image_upload_filesize_max') * 1024) {
					$json['error'][] = sprintf($this->language->get('error_file_size'), $this->config->get('member_image_upload_filesize_max'));
				}

				if (!$this->validateStringLength($filename, 5, 255)) {
					$json['error'][] = sprintf($this->language->get('error_filename'), 5, 255);
				}

				if ($this->request->files['image']['error'] != UPLOAD_ERR_OK) {
					$json['error'][] = 'error_upload_' . $this->request->files['image']['error'];
				}
			} else {
				$json['error'][] = $this->language->get('error_file');
			}
		} else {
			$json['error'][] = $this->language->get('error_directory');
		}

		if (empty($json['error'])) {
			if (@move_uploaded_file($this->request->files['image']['tmp_name'], $directory . '/' . $filename)) {
				if ($image_width > $this->config->get('member_image_dimensions_resize_width')) {
					$this->load->model('tool/image');

					$scaled_width = (int)($this->config->get('member_image_dimensions_resize_width'));
					$scaled_height = floor($this->config->get('member_image_dimensions_resize_width') * $image_height / $image_width);

					$this->model_tool_image->edit($directory . '/' . $filename, $scaled_width, $scaled_height, 'autocrop');

					$json['success'] = $this->language->get('success_image_uploaded'); // sprintf($this->language->get('success_file_dimensions_large'), $scaled_width, $scaled_height);
				} else {
					$json['success'] = $this->language->get('text_uploaded');
				}
			} else {
				$json['error'][] = $this->language->get('error_uploaded');
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function uploadMultiple() {
		$this->load->language('account/filemanager');

		$json = array();

		if (!$this->validateUser()) {
			$json['error'][] = $this->language->get('error_permission');
		}

		if (!isset($json['error']) && isset($this->request->post['directory'])) {
			$directory = rtrim(DIR_IMAGE . 'data/' . $this->customer->getMemberImagesDirectory() . clean_path($this->request->post['directory']), '/');

			if (!is_dir($directory)) {
				$json['error'][] = $this->language->get('error_directory');
			}

			if (isset($this->request->files['image']) && $this->request->files['image']['tmp_name']) {
				$allowed = array(
					'image/jpeg',
					'image/pjpeg',
					'image/png',
					'image/x-png',
					'image/gif'
				);

				$allowed_ext = array(
					'.jpg',
					'.jpeg',
					'.gif',
					'.png'
				);

				for ($i = 0; $i < count($this->request->files['image']['name']); $i++) {
					$json['error'][$i] = array();
					// $json['success'][$i] = '';

					$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['image']['name'][$i], ENT_QUOTES, 'UTF-8')));

					if (!in_array($this->request->files['image']['type'][$i], $allowed) || !in_array(strtolower(strrchr($filename, '.')), $allowed_ext) || !getimagesize($this->request->files['image']['tmp_name'][$i])) {
						$json['error'][$i][] = sprintf($this->language->get('error_file_type_multiple'), $filename);
					} else {
						list($image_width, $image_height) = getimagesize($this->request->files['image']['tmp_name'][$i]);

						if ($image_width < $this->config->get('member_image_dimensions_min_width') && $image_height < $this->config->get('member_image_dimensions_min_height')) {
							$json['error'][$i][] = sprintf($this->language->get('error_file_dimensions_small_multiple'), $filename, $this->config->get('member_image_dimensions_min_width'), $this->config->get('member_image_dimensions_min_height'));
						}

						if ($image_height > $image_width * 2) {
							$json['error'][$i][] = 'Height of image \'' . $filename . '\' cannot be more than double its width!';
						}
					}

					if ($this->request->files['image']['size'][$i] > $this->config->get('member_image_upload_filesize_max') * 1024) {
						$json['error'][$i][] = sprintf($this->language->get('error_file_size_multiple'), $filename, $this->config->get('member_image_upload_filesize_max'));
					}

					if (!$this->validateStringLength($filename, 5, 255)) {
						$json['error'][$i][] = sprintf($this->language->get('error_filename_multiple'), $filename, 5, 255);
					}

					if ($this->request->files['image']['error'][$i] != UPLOAD_ERR_OK) {
						$json['error'][$i][] = 'error_upload_' . $this->request->files['image']['error'][$i];
					}

					if (empty($json['error'][$i])) {
						if (@move_uploaded_file($this->request->files['image']['tmp_name'][$i], $directory . '/' . $filename)) {
							if ($image_width > $this->config->get('member_image_dimensions_resize_width')) {
								$this->load->model('tool/image');

								$scaled_width = (int)($this->config->get('member_image_dimensions_resize_width'));
								$scaled_height = floor($this->config->get('member_image_dimensions_resize_width') * $image_height / $image_width);

								$this->model_tool_image->edit($directory . '/' . $filename, $scaled_width, $scaled_height, 'autocrop');
							}

							$json['success'][$i] = sprintf($this->language->get('success_text_uploaded_multiple'), $filename);
							$json['image'][] = rtrim('data/' . $this->customer->getMemberImagesDirectory() . clean_path($this->request->post['directory']), '/') . '/' . $filename;
						} else {
							$json['error'][$i][] = sprintf($this->language->get('error_uploaded_multiple'), $filename);
						}
					}

				}
			} else {
				$json['error'][] = $this->language->get('error_file');
			}
		} else {
			$json['error'][] = $this->language->get('error_directory');
		}

		$this->response->setOutput(json_encode($json));
	}

  	protected function validateUser() {
		return $this->customer->validateLogin() && $this->customer->validateProfile() && $this->customer->getMemberImagesDirectory();
  	}
}
?>
