<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT_DIR')) {
	define('ROOT_DIR', dirname(__DIR__));
}
require(ROOT_DIR . DS . 'php' . DS . 'class.php');
$mConf = require(ROOT_DIR . DS . 'config.php');
return new \QuickPHP\Config($mConf);
