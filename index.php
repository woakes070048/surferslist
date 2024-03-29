<?php
// Version
define('VERSION', '1.5.5.1-ce.rc2.gc');

// Config
require_once('config.php');

// Startup
require_once(DIR_SYSTEM . 'startup.php');

// Library
require_once(DIR_SYSTEM . 'library/customer.php');
require_once(DIR_SYSTEM . 'library/affiliate.php');
require_once(DIR_SYSTEM . 'library/currency.php');
require_once(DIR_SYSTEM . 'library/tax.php');
require_once(DIR_SYSTEM . 'library/weight.php');
require_once(DIR_SYSTEM . 'library/length.php');
require_once(DIR_SYSTEM . 'library/cart.php');
require_once(DIR_SYSTEM . 'library/recaptcha/src/autoload.php');

// Trait
require_once(DIR_SYSTEM . 'trait/admin.php');
require_once(DIR_SYSTEM . 'trait/captcha.php');
require_once(DIR_SYSTEM . 'trait/validate_error.php');
require_once(DIR_SYSTEM . 'trait/validate_field.php');
require_once(DIR_SYSTEM . 'trait/validate_time.php');
require_once(DIR_SYSTEM . 'trait/contact.php');

// Registry
$registry = new Registry();

// Loader
$loader = new Loader($registry);
$registry->set('load', $loader);

// Config
$config = new Config();
$registry->set('config', $config);

// Cache
$cache = new Cache();
$registry->set('cache', $cache);

// Database
$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
$registry->set('db', $db);

// Store
/* deactivated multi-store */
// if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
// 	$store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`ssl`, 'www.', '') = '" . $db->escape('https://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
// } else {
// 	$store_query = $db->query("SELECT * FROM " . DB_PREFIX . "store WHERE REPLACE(`url`, 'www.', '') = '" . $db->escape('http://' . str_replace('www.', '', $_SERVER['HTTP_HOST']) . rtrim(dirname($_SERVER['PHP_SELF']), '/.\\') . '/') . "'");
// }
//
// if ($store_query->num_rows) {
// 	$config->set('config_store_id', $store_query->row['store_id']);
// } else {
// 	$config->set('config_store_id', 0);
// }
//
// if (!$store_query->num_rows) {
// 	$config->set('config_url', HTTP_SERVER);
// 	$config->set('config_ssl', HTTPS_SERVER);
// }

$config->set('config_store_id', 0);
$config->set('config_url', HTTP_SERVER);
$config->set('config_ssl', HTTPS_SERVER);

// Settings
$setting_data = $cache->get('setting.' . (int)$config->get('config_store_id'));

if (!$setting_data) {
	$query = $db->query("
		SELECT *
		FROM " . DB_PREFIX . "setting
		WHERE store_id = '0'
		ORDER BY store_id ASC
	");
	// OR store_id = '" . (int)$config->get('config_store_id') . "'

	$setting_data = $query->rows;

	$cache->set('setting.' . (int)$config->get('config_store_id'), $setting_data, 60 * 60 * 24); // 1 day cache expiration
}

foreach ($setting_data as $setting) {
	if (!$setting['serialized']) {
		$config->set($setting['key'], $setting['value']);
	} else {
		$config->set($setting['key'], unserialize($setting['value']));
	}
}

// Load Config Files
$config_files = array(
	'cdn',
	'route',
	'smtp',
	'fixer',
	'social',
	'captcha',
	'misc'
);

foreach ($config_files as $config_file) {
	$config->load($config_file);
}

// Url
$url = new Url($config->get('config_url'), $config->get('config_secure') ? $config->get('config_ssl') : $config->get('config_url'));
$registry->set('url', $url);

// Log
$log = new Log($config->get('config_error_filename'));
$registry->set('log', $log);

function error_handler($errno, $errstr, $errfile, $errline) {
	global $log, $config;

	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			$error = 'Notice';
			break;
		case E_WARNING:
		case E_USER_WARNING:
			$error = 'Warning';
			break;
		case E_ERROR:
		case E_USER_ERROR:
			$error = 'Fatal Error';
			break;
		default:
			$error = 'Unknown';
			break;
	}

	if ($config->get('config_error_display')) {
		echo '<b>' . $error . '</b>: ' . $errstr . ' in <b>' . $errfile . '</b> on line <b>' . $errline . '</b>';
	}

	if ($config->get('config_error_log')) {
		$log->write('PHP ' . $error . ':  ' . $errstr . ' in ' . $errfile . ' on line ' . $errline);
	}

	return true;
}

// Error Handler
set_error_handler('error_handler');

// Request
$request = new Request();
$registry->set('request', $request);

// Response
$response = new Response();
$response->addHeader('Content-Type: text/html; charset=utf-8');
$response->addHeader('X-Frame-Options: SAMEORIGIN');

if ($config->get('config_content_security_policy') && is_array($config->get('config_content_security_policy'))) {
	$csp_header = $config->get('config_content_security_policy_report_only') === false
		? 'Content-Security-Policy'
		: 'Content-Security-Policy-Report-Only';
	$response->addHeader($csp_header . ': ' . implode('; ', $config->get('config_content_security_policy')));
}

$response->setCompression($config->get('config_compression'));
$registry->set('response', $response);

// Session
$session = new Session();
$registry->set('session', $session);

// Language Detection
/* deactivated multi-language */
// $languages = array();

// $query = $db->query("
// 	SELECT *
// 	FROM `" . DB_PREFIX . "language`
// 	WHERE status = '1'
// ");
//
// foreach ($query->rows as $result) {
// 	$languages[$result['code']] = $result;
// }
//
// $detect = '';
//
// if (isset($request->server['HTTP_ACCEPT_LANGUAGE']) && $request->server['HTTP_ACCEPT_LANGUAGE']) {
// 	$browser_languages = explode(',', $request->server['HTTP_ACCEPT_LANGUAGE']);
//
// 	foreach ($browser_languages as $browser_language) {
// 		foreach ($languages as $key => $value) {
// 			if ($value['status']) {
// 				$locale = explode(',', $value['locale']);
//
// 				if (in_array($browser_language, $locale)) {
// 					$detect = $key;
// 				}
// 			}
// 		}
// 	}
// }
//
// if (isset($session->data['language'])
// 	&& array_key_exists($session->data['language'], $languages)
// 	&& $languages[$session->data['language']]['status']) {
// 	$code = $session->data['language'];
// } elseif (isset($request->cookie['language'])
// 	&& array_key_exists($request->cookie['language'], $languages)
// 	&& $languages[$request->cookie['language']]['status']) {
// 	$code = $request->cookie['language'];
// } elseif ($detect) {
// 	$code = $detect;
// } else {
// 	$code = $config->get('config_language');
// }

$languages = $config->get('config_languages');
$code = $config->get('config_language');

if (!isset($session->data['language']) || $session->data['language'] != $code) {
	$session->data['language'] = $code;
}

if ((!isset($request->cookie['language']) || $request->cookie['language'] != $code) && isset($request->server['HTTP_HOST'])) {
	setcookie('language', $code, time() + 60 * 60 * 24 * 30, '/', $request->server['HTTP_HOST']);
}

$config->set('config_language_id', $languages[$code]['language_id']);
$config->set('config_language', $languages[$code]['code']);

// Language
$language = new Language($languages[$code]['directory']);
$language->load($languages[$code]['filename']);
$registry->set('language', $language);

// Document
$registry->set('document', new Document());

// Customer
$registry->set('customer', new Customer($registry));

// Affiliate
$registry->set('affiliate', new Affiliate($registry));

if (isset($request->get['tracking'])) {
	setcookie('tracking', $request->get['tracking'], time() + 60 * 60 * 24 * 1000, '/');
}

// Currency
$registry->set('currency', new Currency($registry));

// Tax
$registry->set('tax', new Tax($registry));

// Weight
$registry->set('weight', new Weight($registry));

// Length
$registry->set('length', new Length($registry));

// Cart
$registry->set('cart', new Cart($registry));

// Encryption
$registry->set('encryption', new Encryption($config->get('config_encryption')));

// Front Controller
$controller = new Front($registry);

// SEO URLs
$controller->addPreAction(new Action('common/seo_url'));

// Maintenance Mode
$controller->addPreAction(new Action('common/maintenance'));

// Router
if (isset($request->get['route'])) {
	$action = new Action($request->get['route']);
} else {
	$action = new Action('common/home');
}

// Dispatch
$controller->dispatch($action, new Action('error/not_found'));

// Output
$response->output();
