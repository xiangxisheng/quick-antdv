<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$config = require(__DIR__ . DS . 'default.php');
$defaRoute = &$config['routes'][0]['children'];
array_unshift($defaRoute, [
	"name" => "home",
	"alias" => "/",
	"label" => "首页",
	"component" => "common/home",
	"role" => "public",
]);
$config['setting']['title'] = '飞儿云';
return $config;
