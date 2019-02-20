<?php
final class Cache {
	private $expire = 3600;
	private $memory_cache_enabled;
	private $memory_cache_files = array(); // key is cache name/title, value is expiration time

	public function __construct($memcache = true) {
		$this->memory_cache_enabled = $memcache;

		$files = glob(DIR_CACHE . 'cache.*');

		if ($files) {
			foreach ($files as $index => $file) {
				if (is_file($file)) {
					$expiration = substr(strrchr($file, '.'), 1);

					$file_expired = $expiration < time() ? true : false;

					if ($this->memory_cache_enabled && !$file_expired) {
						$key = substr($file, strlen(DIR_CACHE . 'cache.'), -(strlen($expiration) + 1));  // cache name/title

			        	$this->memory_cache_files[$key] = $expiration;
					}

					if ($file_expired) {
						unlink($file);
					}
				}
			}
		}

		// debug
		// if ($this->memory_cache_enabled) {
		// 	$log = new Log('memory_cache.log', false);
		// 	$log->write('memory_cache: ' . json_encode($this->memory_cache_files));
		// }

		clearstatcache();
	}

	public function get($key) {
		$cache = false;
		$file = false;

		$filepath = $this->getFilepath($key);

		if ($this->memory_cache_enabled) {
			if (isset($this->memory_cache_files[$key])) {
				$file = $filepath . '.' . $this->memory_cache_files[$key];  // cache value is expiration time
			}
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

		$this->delete($key);

		$expiration = time() + $expire;

		$file = $this->getFilepath($key) . '.' . $expiration;

		$handle = fopen($file, 'w');

		if ($handle) {
			flock($handle, LOCK_EX);
			fwrite($handle, json_encode($value));
			fflush($handle);
			flock($handle, LOCK_UN);
			fclose($handle);

			if ($this->memory_cache_enabled) {
	        	$this->memory_cache_files[$key] = $expiration;
			}
		}
	}

	public function delete($key) {
		// needs to support multiple levels (e.g. $key of 'category' => delete 'category.1', 'category.2', 'category.2.1', etc.)

		if ($this->memory_cache_enabled) {
			$length = strlen($key);

			foreach ($this->memory_cache_files as $index => $expiration) {
				// index starts with key
				if (substr($index, 0, $length) === $key) {
					$file = $this->getFilepath($index) . '.' . $expiration;

					if (is_file($file)) {
						unlink($file);

						unset($this->memory_cache_files[$index]);
					}
				}
			}
		} else {
			$files = glob($this->getFilepath($key) . '.*');

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

	private function getFilepath($key) {
		return DIR_CACHE . 'cache.' . preg_replace('/[^A-Z0-9\._-]/i', '', $key);
	}

}
