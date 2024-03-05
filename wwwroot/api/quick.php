<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT_DIR')) {
	define('ROOT_DIR', dirname(dirname(__DIR__)));
}
require(ROOT_DIR . DS . 'php' . DS . 'class.php');
$_C = new \QuickPHP\Config();
$_C->auth()->check();
return $_C;
