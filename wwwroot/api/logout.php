<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$_C = require_once __DIR__ . DS . 'quick.php';
$response = $_C->auth()->logout();
exit(json_encode($response));
