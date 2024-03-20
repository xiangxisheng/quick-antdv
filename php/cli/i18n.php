<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$_SERVER['HTTP_HOST'] = '127.0.0.1';
$_C = require_once dirname(__DIR__) . DS . 'quick.php';

$i18n = new QuickPHP\I18n($_C);
$langPath = ROOT_DIR . '/wwwroot/static/data/lang';
$i18n->generateLangpack($langPath, function ($msg) {
	echo $msg;
});
