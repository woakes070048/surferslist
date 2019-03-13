<?php
class ModelToolImage extends Model {
	/**
	*
	*	@param filename string
	*	@param width
	*	@param height
	*	@param type char [default, w, h, fw, fh, fwch, fhcw, autocrop]
	*				default = scale with white space,
	*				w = fill with whitespace according to width,
	*				h = fill with whitespace according to height,
	*				fw = fix width and scale height accordingly,
	*				fh = fix height and scale width accordingly,
	*				fwch = fix width and crop excess width,
	*				fhcw = fix height and crop excess height,
	*				autocrop = fix width and fix height and crop at center
	*
	*/

	private $url_image_cache = 'image/cache/';

	public function __construct($registry) {
		parent::__construct($registry);

		if ($this->config->get('config_cdn_enabled')) {
			$this->url_image_cache = $this->config->get('config_cdn_url_image_cache');
		} else if ($this->request->isSecure()) {
			$this->url_image_cache = $this->config->get('config_ssl') . 'image/cache/';
		} else {
			$this->url_image_cache = $this->config->get('config_url') . 'image/cache/';
		}
	}

	public function resize($filename, $width, $height, $type = "") {
		if (!$filename || !is_file(DIR_IMAGE . $filename)) {
			if (is_file(DIR_IMAGE . 'no_image.jpg')) {
				$filename = 'no_image.jpg';
			} else {
				return '';
			}
		}

		// height of each image uploaded shouldn't be more than twice its width
		$min_ratio = 0.5; // 0.618; // or crop to golden_ratio?

		$filename_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		// $info = pathinfo($filename);
		// $extension = $info['extension'];

		$type_ext = $type ? '-' . $type : '';

		$original_image = $filename;
		$new_image = utf8_substr($filename, 0, utf8_strrpos($filename, '.')) . '-' . $width . 'x' . $height . $type_ext .'.' . $filename_extension;

		if (!is_file(DIR_IMAGE_CACHE . $new_image) || (filectime(DIR_IMAGE . $original_image) > filectime(DIR_IMAGE_CACHE . $new_image))) {
			$path = '';

			$directories = explode('/', dirname(str_replace('../', '', $new_image)));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!file_exists(DIR_IMAGE_CACHE . $path)) {
					@mkdir(DIR_IMAGE_CACHE . $path, 0755);
				}
			}

			list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $original_image);

			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image(DIR_IMAGE . $original_image);

				$ratio = $width / $height;
				$ratio_orig = $width_orig / $height_orig;

				// no overly tall images
				$resize_ratio = $ratio_orig > $min_ratio ? $ratio_orig : $min_ratio;

				// added fixed height, fixed width options (cropped and not cropped)
				// if (($type == 'autocrop') || ($type == 'fw' && $resize_ratio == $min_ratio)  || ($type == 'fh' && $resize_ratio == $min_ratio)) {  // use with golden ratio only
				if (($type == 'autocrop')) {
					if ($ratio > $resize_ratio) {
						// more landscape: resize to width, scale height, crop extra height
						$resize_width = $width;
						$resize_height = ceil($width / $resize_ratio);
						$image->resize($resize_width, $resize_height, 'h');

						$y_adj = abs($height - $resize_height) / 2;
						$image->crop(0, $y_adj, $width, $height + $y_adj);
					} else if ($ratio < $resize_ratio) {
						// more portrait: resize to height, scale width, crop extra width
						$resize_height = $height;
						$resize_width = ceil($height * $resize_ratio);
						$image->resize($resize_width, $resize_height, 'w');

						$x_adj = abs($width - $resize_width) / 2;
						$image->crop($x_adj, 0, $width + $x_adj, $height);
					} else {
						// same ratio, resize without scaling
						$resize_width = $width;
						$resize_height = $height;

						$image->resize($resize_width, $resize_height);
						$image->crop(0, 0, $width, $height);
					}
				} else if ($type == 'fw') {
			        $resize_height = ceil($width / $resize_ratio);
			        $image->resize($width, $resize_height, 'h');
				} else if ($type == 'fh') {
					$resize_width = ceil($height * $resize_ratio);
					$image->resize($resize_width, $height, 'w');
				} else if ($type == 'fhcw' && $resize_ratio > 1) {
					$resize_width = ceil($height * $resize_ratio);
					$image->resize($resize_width, $height, 'w');
					$image->crop(0, 0, $width, $height);
					//$image->crop($top_x, $top_y, $bottom_x, $bottom_y);
				} else if ($type == 'fwch' && $resize_ratio < 1) {
					$resize_height = ceil($width / $resize_ratio);
					$image->resize($width, $resize_height, 'h');
					$image->crop(0, 0, $width, $height);
				} else {
					$image->resize($width, $height, $type); // original line of code
				}

				// watermark large listing images
				if ($width >= $this->config->get('config_image_popup_width')
					&& (strpos($filename, 'member') !== false || strpos($filename, 'catalog') !== false)
					&& !strpos($filename, '-image.')
					&& !strpos($filename, '-banner.')) {

					$image->watermark(DIR_IMAGE . 'watermark.png', 'bottomright');
				}

				$image->save(DIR_IMAGE_CACHE . $new_image);
			} else {
				copy(DIR_IMAGE . $original_image, DIR_IMAGE_CACHE . $new_image);
			}
		}

		$new_image = implode('/', array_map('rawurlencode', explode('/', $new_image)));

		return $this->url_image_cache . $new_image;
	}

	public function getFileInfo($url) {
		if (!$url) return false;

		$file_info = array();

		$filename = substr($url, strlen($this->url_image_cache));

		if ($filename && is_file(DIR_IMAGE_CACHE . $filename)) {
			$file_info = getimagesize(DIR_IMAGE_CACHE . $filename);
		}

		return $file_info;
	}

	public function edit($filepath, $width, $height, $dimension = '') {
		if (!is_file($filepath)) {
			return;
		}

		$filepath_extension = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));

		$image = new Image($filepath);
		
		$image->resize($width, $height, $dimension);

		if ($image->getMimeType() == 'image/jpeg' && ($filepath_extension == 'jpg' || $filepath_extension == 'jpeg')) {
			// fix orientation if necessary (iPhone!)
			$image_exif = @exif_read_data($filepath);

			if (!empty($image_exif['Orientation'])) {
				$image_orientation = $image_exif['Orientation'];

				switch ($image_orientation) {
					case 8:
						$image->rotate(90);
						break;
					case 3:
						$image->rotate(180);
						break;
					case 6:
						$image->rotate(-90);
						break;
				}
			}
		}

		$image->save($filepath);
	}

	/*
	- $source - path of source image
	- $dir - directory of destination
	- $rename - new file name
	- $keep - keep original or not
	- $destination - returns full path of new image or false (if failed)
	*/
	function move($source, $dir, $rename, $keep = false) {
		if (empty($source) || empty($dir) || empty($rename)) {
			return false;
		}

		// truncate DIR_IMAGE, in case full filesystem path(s) is provided
		if (substr($source, 0, strlen(DIR_IMAGE)) == DIR_IMAGE) {
		    $source = substr($source, strlen(DIR_IMAGE));
		}

		if (substr($dir, 0, strlen(DIR_IMAGE)) == DIR_IMAGE) {
		    $dir = substr($dir, strlen(DIR_IMAGE));
		}

		if (!is_file(DIR_IMAGE . $source)) {
			return false;
		}

		if (!is_dir(DIR_IMAGE . $dir)) {
			mkdir(DIR_IMAGE . $dir, 0755, true);
		}

		$extension = substr(strrchr(strtolower($source), '.'), 1);

		$destination = $dir . '/' . $rename . '.' . $extension;

		if ($keep) {
			if (!copy(DIR_IMAGE . $source, DIR_IMAGE . $destination)) {
				return false;
			}
		} else {
			if (!rename(DIR_IMAGE . $source, DIR_IMAGE . $destination)) {
				return false;
			}
		}

		return $destination;
	}

}
?>
