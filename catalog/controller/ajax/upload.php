<?php
class ControllerAjaxUpload extends Controller {
    use CSRFToken, ValidateField, ValidateTime;

    public function image() {
		if (!$this->config->get('member_status') || empty($this->request->files['file']['name'])) {
            return false;
		}

		$json = array();

		$this->load->language('account/member');

		// if not logged in, process as anonymous listing posting
		if (!$this->customer->validateLogin()) {
            $min_time = 5;
            $max_time = 300;

            if (!$this->validatePostTimeMin($min_time)) {
                $json['error'] = sprintf($this->language->get('error_too_fast'), $min_time);
				$this->setPostTime();
				return $this->response->setOutput(json_encode($json));;
            }

            if (!$this->validatePostTimeMax($max_time)) {
                $json['error'] = sprintf($this->language->get('error_timeout'), $min_time);
				$this->setPostTime();
				return $this->response->setOutput(json_encode($json));;
            }

            if (!$this->validateCSRFToken()) {
                $json['error'] = $this->language->get('error_invalid_token');
				return $this->response->setOutput(json_encode($json));;
            }

			$directory_images = clean_path('temp/' . $this->session->data['csrf_token']);
			$directory_path = DIR_IMAGE . 'data/' . $directory_images;
			if (!is_dir($directory_path)) mkdir($directory_path, 0755, true);
		}

		$thumb_resize_width = !empty($this->request->get['width']) ? (int)$this->request->get['width'] : 205;
		$thumb_resize_height = !empty($this->request->get['height']) ? (int)$this->request->get['height'] : 205;
		$image_type = isset($this->request->get['type']) ? $this->request->get['type'] : '';
		$resize_type = 'fw';
		$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

		if (!$this->validateStringLength($filename, 5, 128)) {
			$json['error'] = sprintf($this->language->get('error_filename'), 5, 128);
		}

		$file_extension = substr(strrchr(strtolower($filename), '.'), 1);
		// $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

		$profile_name = $this->customer->getProfileName();

		if (!$profile_name) {
			$filename = 'anonpost-image-' . mt_rand() . '.' . $file_extension;
		} else if ($image_type == 'banner') {
			$filename = friendly_url($profile_name) . '-' . mt_rand() . '-user-profile-banner.' . $file_extension;
			$resize_type = 'autocrop';
			$thumb_resize_width = 1200;
			$thumb_resize_height = 360;
		} else if ($image_type == 'profile') {
			$filename = friendly_url($profile_name) . '-' . mt_rand() . '-user-profile-image.' . $file_extension;
			$resize_type = 'autocrop';
			$thumb_resize_width = 306;
			$thumb_resize_height = 306;
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
			$json['error'] = 'Directory "' . $image_directory . '" does not exist or is invalid!';
		}

		$image_upload_filesize_max = $this->config->get('member_image_upload_filesize_max') ? $this->config->get('member_image_upload_filesize_max') * 1024 : 5120 * 1024; // bytes

		if ($this->request->files['file']['size'] > $image_upload_filesize_max) {
			$json['error'] = 'Image file size must be less than ' . $image_upload_filesize_max / (1024 * 1024) . 'MB!'; // MB
		}

		$allowed_filetypes = array('jpg', 'jpeg', 'png');

		if (!in_array($file_extension, $allowed_filetypes)) {
			$json['error'] = $this->language->get('error_filetype');
		}

		$allowed_mimetypes = array('image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png');

		if (!in_array($this->request->files['file']['type'], $allowed_mimetypes)) {
			$json['error'] = $this->language->get('error_filetype');
		}

		list($image_width, $image_height) = getimagesize($this->request->files['file']['tmp_name']);
		$image_dimensions_min_width = $this->config->get('member_image_dimensions_min_width') ? (int)$this->config->get('member_image_dimensions_min_width') : 600;
		$image_dimensions_min_height = $this->config->get('member_image_dimensions_min_height') ? (int)$this->config->get('member_image_dimensions_min_height') : 600;
		$image_dimensions_resize_width = (int)$this->config->get('member_image_dimensions_resize_width');

		if (($image_type != 'banner' && $image_width < $image_dimensions_min_width) || ($image_type != 'banner' && $image_height < $image_dimensions_min_height)) {
			$json['error'] = 'Image must be at least ' . $image_dimensions_min_width . 'px by ' . $image_dimensions_min_height . 'px!';
		}

		if ($image_type != 'banner' && $image_height > $image_width * 2) {
			$json['error'] = 'Image height cannot be more than twice the width!';
		}

		if ($image_type == 'banner' && $image_width < 1000) {
			$json['error'] = 'Width of banner image must be at least 1000px!';
		}

		if ($image_type == 'banner' && $image_height > $image_width / 2) {
			$json['error'] = 'Width of banner image must be at least twice its height!';
		}

		if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
			$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
		}

		if (!isset($json['error'])) {
			if (@move_uploaded_file($this->request->files['file']['tmp_name'], $directory . '/' . $filename)) {
				$this->load->model('tool/image');

				if ($image_type != 'banner') {
					if ($image_dimensions_resize_width > 0 && $image_width > $image_dimensions_resize_width) {
						$scaled_width = $image_dimensions_resize_width;
						$scaled_height = floor($image_dimensions_resize_width * $image_height / $image_width);
					}
				} else {
					$scaled_width = 1200;
					$scaled_height = floor(1200 * $image_height / $image_width);
				}

				if (isset($scaled_width) && isset($scaled_height)) {
					$this->model_tool_image->edit($directory . '/' . $filename, $scaled_width, $scaled_height, $resize_type);
				}

				$json['filename'] = $image_directory . '/' . $filename;
				$json['thumb'] = $this->model_tool_image->resize($image_directory . '/' . $filename, $thumb_resize_width, $thumb_resize_height);
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
