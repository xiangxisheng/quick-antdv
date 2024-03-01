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
			"name" => "home",
			"alias" => "/",
			"label" => "首页",
			"component" => "page/home",
			"role" => "public"
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
	'title' => 'Quick Antdv',
	'static_dir' => '/static',
];
return [
	'setting' => $setting,
	'routes' => [$route],
];
