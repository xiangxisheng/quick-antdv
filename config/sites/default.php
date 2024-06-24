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
			'role' => 'user',
			'children' => [
				[
					'name' => 'system',
					'label' => 'menu.system',
					'role' => 'sysadmin',
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
		[
			"name" => "logout",
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
	'component_vue_ext' => 'vue',
	'component_js_ext' => 'js',
	'component_gz_ext' => '',
	'hashHistory' => true,
	'delay' => 0,
	'isDev' => false,
	'static_dir' => $static_dir,
	'title' => 'Quick Antdv',
	'i18n_locales' => [
		["name" => "en_us", "title" => "English",],
		["name" => "zh_cn", "title" => "简体中文",],
		["name" => "km_kh", "title" => "ខ្មែរ",]
	],
];
$setting['assets_dir'] = $static_dir . '/assets';
$setting['assets_dir'] = 'https://quick-antdv-assets.pages.dev';
return [
	'setting' => $setting,
	'routes' => [$route],
];
