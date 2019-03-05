<?php
// load and check cache first
require_once('config.php');
require_once(DIR_SYSTEM . 'helper/helper.php');
require_once(DIR_SYSTEM . 'library/cache.php');

$cache = new Cache();
$minify = $cache->get('minify');
$core_css_min = '';
$core_js_min = '';

if ($minify !== false) {
	if (isset($minify['css']) && is_file(DIR_TEMPLATE . $minify['css'])) {
		$core_css_min = $minify['css'];
	}

	if (isset($minify['js']) && is_file(DIR_TEMPLATE . $minify['js'])) {
		$core_js_min = $minify['js'];
	}
}

// if both cache files are cached and exist, then exit
if ($core_css_min && $core_js_min) {
	// echo 'exists';
	exit();
}

// next check directories
$core_css_mins = glob(DIR_TEMPLATE . 'root/stylesheet/core-*.min.css');
$core_js_mins = glob(DIR_TEMPLATE . 'root/javascript/core-*.min.js');

$core_css_min = ($core_css_mins != false
	&& is_array($core_css_mins)
	&& !empty($core_css_mins))
	&& is_file(DIR_TEMPLATE . 'root/stylesheet/' . basename($core_css_mins[0]))
	? 'root/stylesheet/' . basename($core_css_mins[0])
	: '';

$core_js_min = ($core_js_mins != false
	&& is_array($core_js_mins)
	&& !empty($core_js_mins))
	&& is_file(DIR_TEMPLATE . 'root/javascript/' . basename($core_js_mins[0]))
	? 'root/javascript/' . basename($core_js_mins[0])
	: '';

// if both files in the dirs, then cache and exit
if ($core_css_min && $core_js_min) {
	$cache->set('minify', array(
		'css' => $core_css_min,
		'js' => $core_js_min
	), 60 * 60 * 24 * 365);

	echo 'cached';
	exit();
}

// minified files not found, so load and run minify
require_once(DIR_SYSTEM . 'library/config.php');
require_once(DIR_SYSTEM . 'minify/minify/src/Minify.php');
require_once(DIR_SYSTEM . 'minify/minify/src/CSS.php');
require_once(DIR_SYSTEM . 'minify/minify/src/JS.php');
require_once(DIR_SYSTEM . 'minify/minify/src/Exception.php');
require_once(DIR_SYSTEM . 'minify/minify/src/Exceptions/BasicException.php');
require_once(DIR_SYSTEM . 'minify/minify/src/Exceptions/FileImportException.php');
require_once(DIR_SYSTEM . 'minify/minify/src/Exceptions/IOException.php');
require_once(DIR_SYSTEM . 'minify/path-converter/src/ConverterInterface.php');
require_once(DIR_SYSTEM . 'minify/path-converter/src/Converter.php');

$config = new Config();
$config->load('minify');

use MatthiasMullie\Minify;

$core_css_min_new = '';
$core_js_min_new = '';

// minify css
if (!$core_css_min && is_array($config->get('config_minify_css_files'))) {
	$css_filepaths = $config->get('config_minify_css_files');
	$first_css_filepath = array_shift($css_filepaths);

	$minified_css = new Minify\CSS(DIR_TEMPLATE . $first_css_filepath);

	foreach ($css_filepaths as $css_filepath) {
		if (is_file(DIR_TEMPLATE . $css_filepath)) {
			$minified_css->add(DIR_TEMPLATE . $css_filepath);
		}
	}

	$core_css_min_new = 'root/stylesheet/core-' . substr(md5(mt_rand()), 0, 10) . '.min.css';
    $minified_css->minify(DIR_TEMPLATE . $core_css_min_new);
}

// minify js
if (!$core_js_min && is_array($config->get('config_minify_js_files'))) {
	$js_filepaths = $config->get('config_minify_js_files');
	$first_js_filepath = array_shift($js_filepaths);

	$minified_js = new Minify\JS(DIR_TEMPLATE . $first_js_filepath);

	foreach ($js_filepaths as $js_filepath) {
		if (is_file(DIR_TEMPLATE . $js_filepath)) {
			$minified_js->add(DIR_TEMPLATE . $js_filepath);
		}
	}

    $core_js_min_new = 'root/javascript/core-' . substr(md5(mt_rand()), 0, 10) . '.min.js';
	$minified_js->minify(DIR_TEMPLATE . $core_js_min_new);
}

// save to cached json file with 1 year expiration
$cache->set('minify', array(
	'css' => $core_css_min_new ?: $core_css_min,
	'js' => $core_js_min_new ?: $core_js_min
), 60 * 60 * 24 * 365);

// display results to browser
$paragraphs = array();

if (isset($core_css_min_new)) {
    $paragraphs[] = '<p>' . $core_css_min_new . '</p>';
}

if (isset($core_js_min_new)) {
    $paragraphs[] = '<p>' . $core_js_min_new . '</p>';
}

$body = '';
$html = <<<EOT
    <html>
    <head><title>Minify CSS and JS</title></head>
    <body>%s</body>
    </html>
EOT;

foreach ($paragraphs as $paragraph) {
    $body .= $paragraph;
}

echo sprintf($html, $body);
