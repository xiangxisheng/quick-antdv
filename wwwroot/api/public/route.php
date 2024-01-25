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
							'role' => 'user',
						],
						[
							'name' => 'role',
							'label' => 'menu.role',
							'component' => 'component/table',
							'role' => 'user',
						],
						[
							'name' => 'user',
							'label' => 'menu.user',
							'component' => 'component/table',
							'role' => 'user',
						],
					],
				],
			],
		],
	],
];
echo json_encode([$route]);
