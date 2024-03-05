<?php

function findIndexByName($children, $name)
{
	foreach ($children as $key => $item) {
		if (!isset($item['name'])) {
			continue;
		}
		if ($item['name'] === $name) {
			return $key;
		}
	}
}
function &getMenu(&$config, $iRoot, $sMenuName)
{
	$aRootChildren = &$config['routes'][$iRoot]['children'];
	$sMenuIndex = findIndexByName($aRootChildren, $sMenuName);
	return $aRootChildren[$sMenuIndex];
}
$route = [
	'path' => '/',
	'component' => 'common/index',
	'children' => [
		[
			'path' => '/:pathMatch(.*)',
			'component' => 'common/404',
		],
		[
			'name' => 'console',
			'label' => 'menu.console',
			'component' => 'common/panel',
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
							'alias' => '/',
						],
					],
				],
			],
		],
		[
			"name" => "register",
			"label" => "sign.register",
			"component" => "common/form",
			"role" => "public"
		],
		[
			"name" => "login",
			"label" => "sign.login",
			"component" => "common/form",
			"role" => "public"
		],
	],
];
$static_dir = '/static';
$setting = [
	'api_ext' => '.php',
	'api_root' => '/api',
	'assets_dir' => $static_dir . '/assets',
	'component_dir' => $static_dir . '/component',
	'component_ext' => '.vue.js',
	'delay' => 0,
	'isDev' => false,
	'static_dir' => $static_dir,
	'title' => 'Quick Antdv',
];
return [
	'setting' => $setting,
	'routes' => [$route],
];
