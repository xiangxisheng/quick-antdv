<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$config = require(__DIR__ . DS . 'default.php');
$mMenuConsole = &getMenu($config, 0, 'console');
array_unshift($mMenuConsole['children'], [
	'name' => 'asiafort',
	'label' => 'menu.asiafort',
	'children' => [
		[
			'alias' => '/console',
			'name' => 'tb_event',
			'label' => 'menu.signin_records',
			'component' => 'common/table',
			'role' => 'user',
			'alias' => '/',
		],
		[
			'name' => 'tb_person',
			'label' => 'menu.person_list',
			'component' => 'common/table',
			'role' => 'user',
		],
		[
			'name' => 'hr_depts',
			'label' => 'menu.depts',
			'component' => 'common/table',
			'role' => 'user',
		],
	]
]);
$config['setting']['title'] = 'AsiaFort';
return $config;
