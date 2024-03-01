<?php

$config = require(__DIR__ . DIRECTORY_SEPARATOR . 'default.php');
$defaRoute = &$config['routes'][0]['children'];
array_unshift($defaRoute[2]['children'], [
	'name' => 'asiafort',
	'label' => 'menu.asiafort',
	'children' => [
		[
			'alias' => '/console',
			'name' => 'tb_event',
			'label' => 'menu.signin_records',
			'component' => 'common/table',
			'role' => 'user',
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
