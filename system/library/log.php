<?php
class Log {
	private $filename;

	public function __construct($filename, $append = true) {
		$this->filename = $filename;

		if (!$append) {
			$this->delete($filename);
		}
	}

	public function write($message) {
		$file = DIR_LOGS . $this->filename;

		$handle = fopen($file, 'a');

		fwrite($handle, date('Y-m-d G:i:s') . ' - ' . $message . "\n");

		fclose($handle);
	}

	protected function delete($filename) {
		$file = DIR_LOGS . $filename;

		if (file_exists($file)) {
			unlink($file);
		}
	}
}
