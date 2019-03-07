<?php
final class Cache {
	private $expire = 3600;
	private $prefix = 'cache.';
	private $memory_cache_enabled = false;
	private $memory_cache_files = array(); // key is cache name/title, value is expiration time

	public function __construct($memcache = true) {
		$this->memory_cache_enabled = $memcache;

		$files = glob_recursive(DIR_CACHE . '*');

		if ($files) {
			foreach ($files as $file) {
				if (is_file($file)) {
					$expiration = substr(strrchr($file, '.'), 1);

					$file_expired = $expiration < time() ? true : false;

					if ($this->memory_cache_enabled && !$file_expired) {
						// files are `dir_cache/dir/prefix.name.expiration` and keys `name`
						$name = substr($file, strrpos($file, '/') + strlen($this->prefix) + 1, -(strlen($expiration) + 1));
			        	$this->setMemCache($name, $expiration);
					}

					if ($file_expired) {
						unlink($file);
					}
				}
			}
		}

		clearstatcache();
	}

	public function get($key) {
		$cache = false;
		$file = false;

		$dir = $this->getDir($key);
		$filepath = $this->getFilepath($key, $dir);

		if ($this->memory_cache_enabled) {
			$file = $filepath . '.' . $this->getMemCache($key);  // cache value is expiration time
		} else {
			$files = glob($filepath . '.*');

			if ($files) {
				$file = $files[0];
			}
		}

		if ($file && is_file($file)) {
			$file_length = filesize($file);

			if ($file_length) {
				$handle = fopen($file, 'r');
				flock($handle, LOCK_SH);
				$data = fread($handle, $file_length);
				flock($handle, LOCK_UN);
				fclose($handle);

				$cache = json_decode($data, true);
			}
		}

		return $cache;
	}

	public function set($key, $value, $expire = null) {
		if (!$expire) $expire = $this->expire;

		$dir = $this->getDir($key);

		$this->delete($key, $dir);

		$expiration = time() + $expire;

		$file = $this->getFilepath($key, $dir) . '.' . $expiration;

        if ($dir && !is_dir(DIR_CACHE . $dir)) {
            mkdir(DIR_CACHE . $dir, 0755, true);
        }

		$handle = fopen($file, 'w');

		if ($handle) {
			flock($handle, LOCK_EX);
			fwrite($handle, json_encode($value));
			fflush($handle);
			flock($handle, LOCK_UN);
			fclose($handle);

			if ($this->memory_cache_enabled) {
	        	$this->setMemCache($key, $expiration);
			}
		}
	}

	public function delete($key, $dir = '') {
		// needs to support multiple levels (e.g. $key of 'category' => delete 'category.1', 'category.2', 'category.2.1', etc.)

		if ($this->memory_cache_enabled) {
			foreach ($this->memory_cache_files as $index => $expiration) {
				// index `name` starts with key
				if (substr($index, 0, strlen($key)) === $key) {
					$file = $this->getFilepath($index, $dir) . '.' . $expiration;

					if (is_file($file)) {
						unlink($file);
						unset($this->memory_cache_files[$index]);
					}
				}
			}
		} else {
			$files = glob($this->getFilepath($key, $dir) . '.*');

			if ($files) {
				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					}
				}
			}
		}

		clearstatcache();
	}

	private function getFilepath($key, $dir = '') {
		return DIR_CACHE . $dir . '/' . $this->prefix . preg_replace('/[^A-Z0-9\._-]/i', '', $key);
	}

	private function getMemCache($key) {
		return isset($this->memory_cache_files[$key]) ? $this->memory_cache_files[$key] : null;
	}

	private function setMemCache($key, $value) {
		$this->memory_cache_files[$key] = $value;
	}

	private function getDir($key) {
		return strtok(strtok($key, '.'), '_'); // preg_replace('/[^A-Z-]/i', '', strtok($key, '.'));
	}
}
