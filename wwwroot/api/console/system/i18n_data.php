<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$_C = require_once dirname(dirname(__DIR__)) . DS . 'quick.php';

$columns = [];
$columns[] = [
	'title' => 'id',
	'dataIndex' => 'id',
	'width' => 80,
	'sorter' => true,
	'sql_where' => '`id`=?',
	'form' => 'input',
	'disabled' => true,
	'readonly' => true,
];
$columns[] = [
	'title' => 'group',
	'dataIndex' => 'group',
	'width' => 80,
	'sorter' => true,
	'form' => 'input',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
	'sql_where' => '`group` LIKE ?',
];
$columns[] = [
	'title' => 'name',
	'dataIndex' => 'name',
	'width' => 100,
	'sorter' => true,
	'sql_where' => '`name` LIKE ?',
	'form' => 'input',
	'placeholder' => 'table.please_enter',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
];
$columns[] = [
	'title' => 'lang.en_us',
	'dataIndex' => 'locale_en_us',
	'width' => 160,
	'sorter' => true,
	'sql_where' => 'locale_en_us LIKE ?',
	'form' => 'input',
	'placeholder' => 'table.please_enter',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
];
$columns[] = [
	'title' => 'lang.zh_cn',
	'dataIndex' => 'locale_zh_cn',
	'width' => 160,
	'sorter' => true,
	'sql_where' => 'locale_zh_cn LIKE ?',
	'form' => 'input',
	'placeholder' => 'table.please_enter',
];
$columns[] = [
	'title' => 'lang.km_kh',
	'dataIndex' => 'locale_km_kh',
	'width' => 160,
	'sorter' => true,
	'sql_where' => 'locale_km_kh LIKE ?',
	'form' => 'input',
	'placeholder' => 'table.please_enter',
];
$columns[] = [
	'title' => 'table.operates',
	'fixed' => 'right',
	'width' => 140,
	'operates' => [
		[
			'action' => 'view', 'title' => 'table.view',
		],
		[
			'action' => 'edit', 'title' => 'table.edit',
			'buttons' => [
				['title' => 'table.cancel'],
				['title' => 'table.save', 'type' => 'primary'],
			],
		],
		[
			'action' => 'delete', 'title' => 'table.delete',
			'popconfirm' => ['title' => 'table.popconfirm_delete', 'okText' => 'table.delete', 'cancelText' => 'table.cancel'],
		],
	]
];
$data = [
	'buttons' => [
		[
			'type' => 'add', 'title' => 'table.add',
			'buttons' => [
				['title' => 'table.cancel'],
				['title' => 'table.add', 'type' => 'primary'],
			],
		],
		[
			'type' => 'delete', 'title' => 'table.delete',
			'popconfirm' => ['title' => 'table.popconfirm_delete_batch', 'okText' => 'table.delete', 'cancelText' => 'table.cancel'],
		],
	],
	'sql' => [
		'from' => 'system_i18n_data',
		'where' => [],
		'order' => '',
	],
	'table' => [
		'columns' => $columns,
		'pagination' => [
			'pageSizeDefault' => 18,
			'pageSizeMax' => 100,
			'pageSizeOptions' => ['10', '20', '50', '100'],
			'showTotalTemplate' => 'table.showTotalTemplate',
		],
		'rowKey' => 'id',
		'rowSelection' => true,
	],
	'onEffected' => function () use ($_C) {
		$langPath = ROOT_DIR . '/wwwroot/static/data/lang';
		$_C->i18n()->generateLangpack($langPath);
	},
];
echo json_encode($_C->tableCrud()->tableReader($data));
