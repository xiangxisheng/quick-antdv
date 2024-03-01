<?php

define('DS', DIRECTORY_SEPARATOR);
$root_dir = dirname(dirname(dirname(__DIR__)));
$config_dir = $root_dir . DS . 'config';
$hosts = file_get_contents($config_dir . DS . 'hosts.php');
$site_files = glob($config_dir . DS . 'sites' . DS . '*.php');
foreach ($site_files as $site_file) {
	$site_name = pathinfo($site_file, PATHINFO_FILENAME);
	$config_site = require($site_file);
	file_put_contents('config' . DS . $site_name . '.json', json_encode($config_site));
}
echo "Configuration file successfully generated.\r\n";
