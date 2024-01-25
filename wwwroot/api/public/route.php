<?php

$route = array();
$route['path'] = '/';
$route['component'] = 'page/index';
$route['role'] = 'public';
$route['children'] = array(
	array(
		'path' => '/:pathMatch(.*)',
		'component' => 'component/404',
	),
	array(
		'name' => 'console',
		'alias' => '/',
		'label' => 'menu.console',
		'component' => 'page/panel',
		'role' => 'user',
		'children' => array(
			array(
				'alias' => '/console',
				'name' => 'tb_event',
				'label' => 'menu.signin_records',
				'component' => 'component/table',
				'role' => 'user',
			),
			array(
				'name' => 'tb_person',
				'label' => 'menu.person_list',
				'component' => 'component/table',
				'role' => 'user',
			),
			array(
				'name' => 'system_i18n_data',
				'label' => 'menu.language_setting',
				'component' => 'component/table',
				'role' => 'user',
			),
		),
	),
);
echo json_encode(array($route));
