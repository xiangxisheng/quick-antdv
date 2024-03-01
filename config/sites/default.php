<?php

$route = [
	'path' => '/',
	'component' => 'common/index',
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
			"component" => "common/home",
			"role" => "public"
		],
		[
			'name' => 'console',
			'alias' => '/',
			'label' => 'menu.console',
			'component' => 'common/panel',
			'role' => 'user',
			'children' => [
				[
					'name' => 'system',
					'label' => 'menu.system',
					'children' => [
						[
							'name' => 'i18n_data',
							'label' => 'menu.language_setting',
							'component' => 'common/table',
							'role' => 'sysadmin',
						],
						[
							'name' => 'roles',
							'label' => 'menu.roles',
							'component' => 'common/table',
							'role' => 'sysadmin',
						],
						[
							'name' => 'users',
							'label' => 'menu.users',
							'component' => 'common/table',
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
$static_dir = '/static';
$setting = [
	'assets_dir' => $static_dir . '/assets',
	'component_dir' => $static_dir . '/component',
	'component_ext' => '.vue.js',
	'isDev' => false,
	'static_dir' => $static_dir,
	'title' => 'Quick Antdv',
];
return [
	'setting' => $setting,
	'routes' => [$route],
];
