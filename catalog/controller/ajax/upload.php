<?php
class ControllerAjaxUpload extends Controller {
    use CSRFToken, ValidateField, ValidateTime, Admin;

    public function image() {
		if (!$this->config->get('member_status') || empty($this->request->files['file']['name'])) {
            return false;
		}

		$json = array();

        $this->load->language('account/product');

		// if not logged in, process as anonymous listing posting
		if (!$this->customer->validateLogin()) {
            if (!$this->isAdmin()) {
                $min_time = 5;
                $max_time = 300;

                if (!$this->validatePostTimeMin($min_time)) {
                    $json['error'] = sprintf($this->language->get('error_too_fast'), $min_time);
    				$this->setPostTime();
    				return $this->response->setOutput(json_encode($json));
                }

                if (!$this->validatePostTimeMax($max_time)) {
                    $json['error'] = sprintf($this->language->get('error_timeout'), $min_time);
    				$this->setPostTime();
    				return $this->response->setOutput(json_encode($json));
                }
            }

            if (!$this->validateCSRFToken()) {
                $json['error'] = $this->language->get('error_invalid_token');
				return $this->response->setOutput(json_encode($json));
            }

			$directory_images = clean_path('temp/' . $this->session->data['csrf_token']);
			$directory_path = DIR_IMAGE . 'data/' . $directory_images;

			if (!is_dir($directory_path)) mkdir($directory_path, 0755, true);
		}

        // config membership image settings
        $image_upload_filesize_max = ((int)$this->config->get('member_image_upload_filesize_max') ?: 5120) * 1024; // bytes
		$image_dimensions_min_width = (int)$this->config->get('member_image_dimensions_min_width') ?: 450;
		$image_dimensions_min_height = (int)$this->config->get('member_image_dimensions_min_height') ?: 450;
		$image_dimensions_resize_width = (int)$this->config->get('member_image_dimensions_resize_width') ?: 1200;
        $image_dimensions_resize_height = (int)$this->config->get('member_image_dimensions_resize_height') ?: 1200;
        $image_dimensions_profile_width = (int)$this->config->get('member_image_dimensions_profile_width') ?: 306;
        $image_dimensions_profile_height = (int)$this->config->get('member_image_dimensions_profile_height') ?: 306;
        $image_dimensions_banner_width = (int)$this->config->get('member_image_dimensions_banner_width') ?: 1200;
        $image_dimensions_banner_height = (int)$this->config->get('member_image_dimensions_banner_height') ?: 360;

		$allowed_filetypes = array('jpg', 'jpeg', 'png');
        $allowed_mimetypes = array('image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png');

        list($image_width, $image_height) = getimagesize($this->request->files['file']['tmp_name']);
		$thumb_resize_width = !empty($this->request->get['width']) ? (int)$this->request->get['width'] : $this->config->get('config_image_product_width');
		$thumb_resize_height = !empty($this->request->get['height']) ? (int)$this->request->get['height'] : $this->config->get('config_image_product_height');
		$image_type = isset($this->request->get['type']) ? $this->request->get['type'] : '';
		$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));
		$file_extension = substr(strrchr(strtolower($filename), '.'), 1);  // strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        $image_too_small = $image_type != 'banner' ? ($image_width < $image_dimensions_min_width || $image_height < $image_dimensions_min_height) : ($image_width < $image_dimensions_banner_width);
        $image_too_large = $image_type != 'banner' ? ($image_width > $image_dimensions_resize_width) : ($image_width > $image_dimensions_banner_width);
        $profile_name = $this->customer->getProfileName();
        $type = 'fw';

		if (!$this->validateStringLength($filename, 5, 128)) {
			$json['error'] = sprintf($this->language->get('error_filename'), 5, 128);
		}

		if (!$profile_name) {
			$filename = 'anonpost-image-' . mt_rand() . '.' . $file_extension;
		} else if ($image_type == 'banner') {
			$filename = friendly_url($profile_name) . '-' . mt_rand() . '-user-profile-banner.' . $file_extension;
			$thumb_resize_width = $image_dimensions_banner_width;
			$thumb_resize_height = $image_dimensions_banner_height;
			$type = 'autocrop';
		} else if ($image_type == 'profile') {
			$filename = friendly_url($profile_name) . '-' . mt_rand() . '-user-profile-image.' . $file_extension;
			$thumb_resize_width = $image_dimensions_profile_width;
			$thumb_resize_height = $image_dimensions_profile_height;
			$type = 'autocrop';
		} else if ($image_type == 'listing') {
			$filename = friendly_url($profile_name) . '-listing-image-' . mt_rand() . '.' . $file_extension;
		} else {
			$filename = friendly_url($profile_name) . '-image-' . mt_rand() . '.' . $file_extension;
		}

		if ($this->customer->hasProfile() && $this->customer->getMemberImagesDirectory()) {
			$image_directory = 'data/' . $this->customer->getMemberImagesDirectory();
		// } else if (!empty($this->request->get['directory_images'])) {
		// 	$image_directory = 'data/' . clean_path(urldecode($this->request->get['directory_images']));
		} else if (isset($directory_images)) {
			$image_directory = 'data/' . $directory_images;
		} else {
			$image_directory = 'data/' . clean_path($this->config->get('member_image_upload_directory'));
		}

		if (is_dir(DIR_IMAGE . $image_directory)) {
			$directory = DIR_IMAGE . $image_directory;
		} else {
			$json['error'] = sprintf($this->language->get('error_exists_dir'), $image_directory);
		}

		if ($this->request->files['file']['size'] > $image_upload_filesize_max) {
			$json['error'] = sprintf($this->language->get('error_filesize'), $image_upload_filesize_max / (1024 * 1024));  // MB
		}

		if (!in_array($file_extension, $allowed_filetypes) || !in_array($this->request->files['file']['type'], $allowed_mimetypes)) {
			$json['error'] = $this->language->get('error_filetype');
		}

        if (!$this->isAdmin()) {
            if ($image_type != 'banner') {
                if ($image_too_small) {
        			$json['error'] = sprintf($this->language->get('error_image_dimensions'), $image_dimensions_min_width, $image_dimensions_min_height);
        		}

        		if ($image_height > $image_width * 2) {
        			$json['error'] = $this->language->get('error_image_scale');
        		}
            } else {
                if ($image_too_small) {
        			$json['error'] = sprintf($this->language->get('error_banner_dimensions'), $image_dimensions_banner_width);
        		}

        		if ($image_type == 'banner' && $image_height > $image_width / 2) {
        			$json['error'] = $this->language->get('error_banner_scale');
        		}
            }
        }

		if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
			$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
		}

		if (!isset($json['error'])) {
			if (@move_uploaded_file($this->request->files['file']['tmp_name'], $directory . '/' . $filename)) {
				$this->load->model('tool/image');

                if ($this->isAdmin() && $image_too_small) {
                    $scaled_width = $image_type != 'banner' ? $image_dimensions_min_width : $image_dimensions_banner_width;
                    $scaled_height = $image_type != 'banner' ? $image_dimensions_min_height : $image_dimensions_banner_height;
                }

                if ($image_too_large) {
                    $scaled_width = $image_type != 'banner' ? $image_dimensions_resize_width : $image_dimensions_banner_width;
                    $scaled_height = $image_type != 'banner' ? floor($image_dimensions_resize_width * $image_height / $image_width) : floor($image_dimensions_banner_width * $image_height / $image_width);
                }

				if (isset($scaled_width) && isset($scaled_height)) {
					$this->model_tool_image->edit($directory . '/' . $filename, $scaled_width, $scaled_height);
				}

				$json['filename'] = $image_directory . '/' . $filename;
				$json['thumb'] = $this->model_tool_image->resize($image_directory . '/' . $filename, $thumb_resize_width, $thumb_resize_height, $type);
				$json['success'] = $this->language->get('text_upload');
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
		}

		$this->response->setOutput(json_encode($json));
	}

    public function file_member() {
		if (!$this->customer->validateLogin()
            || !$this->customer->validateProfile()
            || !$this->customer->getMemberPermission('download_enabled')
            || !$this->config->get('member_status')
            || !$this->config->get('member_tab_download')
            || empty($this->request->files['file']['name'])) {
			return false;
		}

        $json = array();

		$this->load->language('account/product_download');

		$filename = basename(html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8'));

		if (!$this->validateStringLength($filename, 5, 128)) {
			$json['error'] = sprintf($this->language->get('error_filename'), 5, 128);
		}

		if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
			$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
		}

		if (!isset($json['error'])) {
			if (is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
				$ext = md5(mt_rand());

				$json['filename'] = $this->customer->getMemberDownloadsDirectory() . '/' . $filename . '.' . $ext;
				$json['mask'] = $filename;

				@move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $this->customer->getMemberDownloadsDirectory() . '/' . $filename . '.' . $ext);
			}

			$json['success'] = $this->language->get('text_upload');
		}

		$this->response->setOutput(json_encode($json));
	}

    public function file_listing() {
        // disabled
        if (true
            || !$this->customer->validateLogin()
            || empty($this->request->files['file']['name'])) {
            return false;
        }

		$json = array();

		$this->load->language('product/product');

		$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

		if (!$this->validateStringLength($filename, 5, 128)) {
			$json['error'] = sprintf($this->language->get('error_filename'), 5, 128);
		}

		// Allowed file extension types
		$allowed = array();

		$filetypes = explode("\n", str_replace(array("\r\n", "\r"), "\n", $this->config->get('config_file_extension_allowed')));

		foreach ($filetypes as $filetype) {
			$allowed[] = trim($filetype);
		}

		if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
			$json['error'] = $this->language->get('error_filetype');
		}

		// Allowed file mime types
		$allowed = array();

		$filetypes = explode("\n", str_replace(array("\r\n", "\r"), "\n", $this->config->get('config_file_mime_allowed')));

		foreach ($filetypes as $filetype) {
			$allowed[] = trim($filetype);
		}

		if (!in_array($this->request->files['file']['type'], $allowed)) {
			$json['error'] = $this->language->get('error_filetype');
		}

		if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
			$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
		}

		if (!$json && is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
			$file = basename($filename) . '.' . hash_rand('md5');

			// hide the uploaded file name so it can't be linked to it directly
			$json['file'] = $this->encryption->encrypt($file);

			@move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $file);

			$json['success'] = $this->language->get('text_upload');
		} else {
            $json['error'] = $this->language->get('error_upload');
        }

		$this->response->setOutput(json_encode($json));
	}

}
