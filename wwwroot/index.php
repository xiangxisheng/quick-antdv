<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
define('ROOT_DIR', dirname(__DIR__));
$_C = require (ROOT_DIR . DS . 'php' . DS . 'quick.php');
$mConfig = $_C->GetConfig();

require (__DIR__ . DS . 'component.php');

function isLocal($host_name)
{
	$long = ip2long($host_name);
	if ($long === false) {
		return false;
	}
	return ip2long('127.0.0.1') <= $long && $long <= ip2long('127.255.255.255');
}

function isCached($target, $cache_timeout = -1)
{
	if ($cache_timeout === 0) {
		return false;
	}
	if (!file_exists($target)) {
		return false;
	}
	if ($cache_timeout === -1) {
		return true;
	}
	if (time() - filemtime($target) < $cache_timeout) {
		return true;
	}
	return false;
}

function GetHTML($_C, $name)
{
	$host_name = $_C->GetRequestHostName();
	$target = __DIR__ . DS . $name . '.html';
	$cache_timeout = 1;
	if (isLocal($host_name)) {
		// 本地访问就不要缓存
		$cache_timeout = 0;
	}
	if (isCached($target, $cache_timeout)) {
		// 有缓存就直接return
		return file_get_contents($target);
	}
	// 生成HTML时也要构建组件
	component_build();
	$html = file_get_contents(__DIR__ . DS . $name . '.hbs');
	$config = $_C->GetConfig();
	$data = [
		'title' => $config['setting']['title'],
		'config' => json_encode([
			'setting' => $config['setting'],
			'routes' => $config['routes'],
		]),
	];
	foreach ($data as $key => $value) {
		$html = preg_replace('/{{\s*' . preg_quote($key) . '\s*}}/', $value, $html);
	}
	// 最后写入文件缓存然后返回
	file_put_contents($target, $html);
	return $html;
}

echo GetHTML($_C, 'index');
