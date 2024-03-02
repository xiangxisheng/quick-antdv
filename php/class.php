<?php

namespace QuickPHP;

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
define('CLASS_DIR', __DIR__ . DS . 'class');
spl_autoload_register(function ($class) {
	$class = str_replace("\\", DS, $class);
	require_once CLASS_DIR . DS . $class . '.php';
});
