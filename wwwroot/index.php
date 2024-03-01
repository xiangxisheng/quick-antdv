<?php

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_DIR', dirname(__DIR__));

function GetRequestHostName()
{
	$host_parts = explode(':', $_SERVER['HTTP_HOST']);
	return $host_parts[0];
}

function GetSiteName($host_name)
{
	$hosts = require(ROOT_DIR . DS . 'config' . DS . 'hosts.php');
	if (isset($hosts[$host_name])) {
		return $hosts[$host_name];
	}
	return 'default';
}

function GetConfig($host_name)
{
	$site_name = GetSiteName($host_name);
	$config_dir = ROOT_DIR . DS . 'config';
	return require($config_dir . DS . 'sites' . DS . $site_name . '.php');
}

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

function GetHTML($name)
{
	$host_name = GetRequestHostName();
	$target = __DIR__ . DS . $name . '.html';
	$cache_timeout = isLocal($host_name) ? 0 : -1;
	if (isCached($target, $cache_timeout)) {
		return file_get_contents($target);
	}
	$html = file_get_contents(__DIR__ . DS . $name . '.hbs');
	$config = GetConfig($host_name);
	$data = [
		'title' => $config['setting']['title'],
		'config' => json_encode($config),
	];
	foreach ($data as $key => $value) {
		$html = preg_replace('/{{\s*' . preg_quote($key) . '\s*}}/', $value, $html);
	}
	file_put_contents($target, $html);
	return $html;
}

echo GetHTML('index');
