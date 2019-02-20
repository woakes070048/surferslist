<?php
function friendly_url($string) {
	$string = strtolower($string);

	$patterns = $replacements = array();
	$patterns[0] = '/(&quot;|\"|&apos;|\')/i';
	$replacements[0] = '';
	$patterns[1] = '/(&amp;|&)/i';
	$replacements[1] = '-and-';
	$patterns[2] = '/[^a-zA-Z01-9]/i';
	$replacements[2] = '-';
	$patterns[3] = '/(-+)/i';
	$replacements[3] = '-';
	$patterns[4] = '/(-$|^-)/i';
	$replacements[4] = '';

	return preg_replace($patterns, $replacements, $string);
}

function strip_tags_decode($data) {
	if (is_array($data)) {
		foreach ($data as $key => $value) {
			unset($data[$key]);

			$data[strip_tags_decode($key)] = strip_tags_decode($value);
		}
	} else {
	    $data = str_replace("&nbsp;", " ", $data);
		$data = trim(strip_tags(htmlspecialchars_decode($data, ENT_NOQUOTES)));
	}

	return $data;
}

function strip_non_alphanumeric($string, $spaces = false) {
	$string = trim(strip_tags(htmlspecialchars_decode($string, ENT_QUOTES)));

	$patterns = array(
		0	=> '/[^A-Za-z0-9 ]/',
		1	=> '/\s+/'
	);

	$replacements = array(
		0	=> '',
		1	=> $spaces ? '' : ' '
	);

	return preg_replace($patterns, $replacements, $string);
}

function is_url($string) {
	if (!$string) {
		return false;
	}

	// $pattern = '@^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)@';
	$pattern = '@^https?:\/\/(?:[\w]+)(?:[-\w]*)\.([a-zA-Z\.]{2,6})([\/\w\.-]*)*\/?@';

	return preg_match($pattern, $string);
}

function replace_urls($string, $replacement) {
	if (!$string) {
		return '';
	}

	$pattern = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';

	return preg_replace($pattern, $replacement, $string);
}

function convert_links($string) {
	return replace_urls($string, '<a href="http$2://$4" target="_blank" title="$0">$0</a>');
}

function remove_links($string) {
	return replace_urls($string, '');
}

function glob_recursive($pattern, $flags = 0) {
	$files = glob($pattern, $flags);

	foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
		$files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
	}

	return $files;
}

function clean_path($path = '') {
	if (!$path) {
		return '';
	}

	while (strstr($path, "../") || strstr($path, "..\\") || strstr($path, "..")) {
		$path = str_replace(array("../", "..\\", ".."), '', $path);
	}

	return $path;
}

function generate_password($length) {
	$password = '';

	for ($i = 0; $i < $length; $i++) {
		do {
			$char = chr(mt_rand(48, 122));
		} while (!preg_match('/[a-zA-Z0-9]/', $char));

		$password .= $char;
	}

	return $password;
}

function is_hex_color($color) {
	return preg_match('/^([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', htmlspecialchars_decode($color));
}
