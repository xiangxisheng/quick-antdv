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
					'name' => 'asiafort',
					'label' => 'menu.asiafort',
					'children' => [
						[
							'alias' => '/console',
							'name' => 'tb_event',
							'label' => 'menu.signin_records',
							'component' => 'component/table',
							'role' => 'user',
						],
						[
							'name' => 'tb_person',
							'label' => 'menu.person_list',
							'component' => 'component/table',
							'role' => 'user',
						],
						[
							'name' => 'hr_depts',
							'label' => 'menu.depts',
							'component' => 'component/table',
							'role' => 'user',
						],
					]
				],
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
		[
			"name" => "sign-up",
			"label" => "sign_up",
			"component" => "page/sign",
			"role" => "public"
		],
		[
			"name" => "sign-in",
			"label" => "sign_in",
			"component" => "page/sign",
			"role" => "public"
		],
	],
];
$setting = [
	'isDev' => false,
	'title' => 'AsiaFort',
	'static_dir' => '/static',
];
return [
	'setting' => $setting,
	'routes' => [$route],
];
