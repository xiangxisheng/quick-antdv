<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$config = require(__DIR__ . DS . 'default.php');
$mMenuConsole = &getMenu($config, 0, 'console');
array_unshift($mMenuConsole['children'], [
	'name' => 'duanlianjie',
	'label' => '短链接管理',
	'role' => 'sysadmin',
	'children' => [
		[
			'name' => 'duanlianjie',
			'label' => '短链接管理',
			'component' => 'common/table',
			'role' => 'sysadmin',
			"alias" => "/",
		],
	],
]);
$config['setting']['title'] = '短链接管理系统';
return $config;
