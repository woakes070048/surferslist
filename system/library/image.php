<?php
class Image {
	private $file;
	private $image;
	private $info;

	public function __construct($file) {
		if (file_exists($file)) {
			$this->file = $file;

			$info = getimagesize($file);

			$this->info = array(
				'width'  => $info[0],
				'height' => $info[1],
				'bits'   => $info['bits'],
				'mime'   => $info['mime']
			);

			$this->image = $this->create($file);
		} else {
			exit('Error: Could not load image ' . $file . '!');
		}
	}

	private function create($image, $mime = '') {
		if (!$mime) {
			$mime = $this->info['mime'];
		}

		if ($mime == 'image/gif') {
			return imagecreatefromgif($image);
		} elseif ($mime == 'image/png') {
			return imagecreatefrompng($image);
		} elseif ($mime == 'image/jpeg') {
			return imagecreatefromjpeg($image);
		} else {
			return false;
		}
	}

	// default quality 85, per https://developers.google.com/speed/docs/insights/OptimizeImages
	public function save($file, $quality = 85) {
		$info = pathinfo($file);

		$extension = strtolower($info['extension']);

		if (is_resource($this->image)) {
			if ($extension == 'jpeg' || $extension == 'jpg') {
				imagejpeg($this->image, $file, $quality);
			} elseif($extension == 'png') {
				imagepng($this->image, $file);
			} elseif($extension == 'gif') {
				imagegif($this->image, $file);
			}

			imagedestroy($this->image);
		}
	}

	/**
	*
	*	@param width
	*	@param height
	*	@param default char [default, w, h]
	*				   default = scale by filling with white space,
	*				   w = fill according to width,
	*				   h = fill according to height
	*
	*/
	public function resize($width = 0, $height = 0, $dimension = '') {
		if (!$this->info['width'] || !$this->info['height'] || $this->info['mime'] == 'image/vnd.microsoft.icon') {
			return;
		}

		$xpos = 0;
		$ypos = 0;
		$scale = 1;

		$scale_w = $width / $this->info['width'];
		$scale_h = $height / $this->info['height'];

		if ($dimension == 'w') {
			$scale = $scale_w;
		} elseif ($dimension == 'h'){
			$scale = $scale_h;
		} else {
			$scale = min($scale_w, $scale_h);
		}

		if ($scale == 1 && $scale_h == $scale_w && $this->info['mime'] != 'image/png') {
			return;
		}

		$new_width = (int)($this->info['width'] * $scale);
		$new_height = (int)($this->info['height'] * $scale);
		$xpos = (int)(($width - $new_width) / 2);
		$ypos = (int)(($height - $new_height) / 2);

		$this->reCreate($width, $height, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);

		$this->info['width']  = $width;
		$this->info['height'] = $height;
	}

	public function watermark($file, $position = 'bottomright') {
		$watermark = $this->create($file, mime_content_type($file));

		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);

		switch ($position) {
			case 'center':
				$watermark_pos_x = ($this->info['width'] - $watermark_width) / 2;
				$watermark_pos_y = ($this->info['height'] - $watermark_height) / 2;
				break;
			case 'top':
				$watermark_pos_x = ($this->info['width'] - $watermark_width) / 2;
				$watermark_pos_y = 0;
				break;
			case 'bottom':
				$watermark_pos_x = ($this->info['width'] - $watermark_width) / 2;
				$watermark_pos_y = $this->info['height'] - $watermark_height;
				break;
			case 'topleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = 0;
				break;
			case 'topright':
				$watermark_pos_x = $this->info['width'] - $watermark_width;
				$watermark_pos_y = 0;
				break;
			case 'bottomleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = $this->info['height'] - $watermark_height;
				break;
			case 'bottomright':
				$watermark_pos_x = $this->info['width'] - $watermark_width;
				$watermark_pos_y = $this->info['height'] - $watermark_height;
				break;
			default:
				$watermark_pos_x = ($this->info['width'] - $watermark_width) / 2;
				$watermark_pos_y = ($this->info['height'] - $watermark_height) / 2;
				break;
		}

		$opacity = $this->info['mime'] == 'image/png' ? 65 : 75;

		imagecolortransparent($watermark, imagecolorat($watermark, 0, 0));

		imagecopymerge($this->image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height, $opacity);

		imagedestroy($watermark);
	}

	public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
		$width = $bottom_x - $top_x;
		$height = $bottom_y - $top_y;

		$this->reCreate($width, $height, 0, 0, $top_x, $top_y, $width, $height, $width, $height);

		$this->info['width'] = $width;
		$this->info['height'] = $height;
	}

	public function rotate($degree, $color = 'FFFFFF') {
		$rgb = $this->html2rgb($color);

		$this->image = imagerotate($this->image, $degree, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));

		$this->info['width'] = imagesx($this->image);
		$this->info['height'] = imagesy($this->image);
	}

	public function getMimeType() {
		return $this->info['mime']; // e.g. 'image/jpeg', 'image/png', 'image/gif'
	}

	private function reCreate($width, $height, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {
		$image_old = $this->image;
		$this->image = imagecreatetruecolor($width, $height);

		if (!$this->image) return;

		if ($this->info['mime'] == 'image/png') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}

		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);
		imagecopyresampled($this->image, $image_old, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

		imagedestroy($image_old);
	}

	private function filter($filter) {
		imagefilter($this->image, $filter);
	}

	private function text($text, $x = 0, $y = 0, $size = 5, $color = '000000') {
		$rgb = $this->html2rgb($color);

		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]));
	}

	private function merge($file, $x = 0, $y = 0, $opacity = 100) {
		$merge = $this->create($file);

		$merge_width = imagesx($merge);
		$merge_height = imagesy($merge);

		imagecopymerge($this->image, $merge, $x, $y, 0, 0, $merge_width, $merge_height, $opacity);
	}

	private function html2rgb($color) {
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		if (strlen($color) == 6) {
			list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
		} elseif (strlen($color) == 3) {
			list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
		} else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return array($r, $g, $b);
	}
}
?>
