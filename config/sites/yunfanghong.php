<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$config = require(__DIR__ . DS . 'default.php');
$defaRoute = &$config['routes'][0]['children'];
array_unshift($defaRoute[2]['children'], [
	'name' => 'yunfanghong',
	'label' => 'menu.yunfanghong',
	'children' => []
]);
$config['setting']['title'] = '云防红';
return $config;
