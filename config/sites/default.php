<?php

$route = [
	'path' => '/',
	'component' => 'page/index',
	'role' => 'public',
	'children' => [
		[
			'path' => '/:pathMatch(.*)',
			'component' => 'component/404',
		],
		[
			'name' => 'console',
			'alias' => '/',
			'label' => 'menu.console',
			'component' => 'page/panel',
			'role' => 'user',
			'children' => [
				[
					'name' => 'system',
					'label' => 'menu.system',
					'children' => [
						[
							'name' => 'i18n_data',
							'label' => 'menu.language_setting',
							'component' => 'component/table',
							'role' => 'sysadmin',
						],
						[
							'name' => 'roles',
							'label' => 'menu.roles',
							'component' => 'component/table',
							'role' => 'sysadmin',
						],
						[
							'name' => 'users',
							'label' => 'menu.users',
							'component' => 'component/table',
							'role' => 'sysadmin',
						],
					],
				],
			],
		],
	],
];
$setting = [
	'title' => 'Quick Antdv',
	'static_dir' => '/static',
];
return [
	'setting' => $setting,
	'route' => $route,
];
