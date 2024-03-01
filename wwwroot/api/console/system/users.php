<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$_C = require_once dirname(dirname(__DIR__)) . DS . 'quick.php';

$columns = [];
$columns[] = [
	'title' => 'UID',
	'dataIndex' => 'uid',
	'width' => 80,
	'sorter' => true,
	'sql_where' => '`uid`=?',
	'form' => 'input',
	'disabled' => true,
	'readonly' => true,
];
$columns[] = [
	'title' => 'table.username',
	'dataIndex' => 'username',
	'width' => 100,
	'sorter' => true,
	'sql_where' => '`username` LIKE ?',
	'form' => 'input',
	'placeholder' => 'table.please_enter',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
];
$columns[] = [
	'title' => 'table.password',
	'dataIndex' => 'password',
	'form' => 'input',
	'placeholder' => 'table.please_enter',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
];
$columns[] = [
	'title' => 'table.nickname',
	'dataIndex' => 'nickname',
	'width' => 80,
	'sorter' => true,
	'form' => 'input',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
	'sql_where' => '`nickname` LIKE ?',
];
$columns[] = [
	'title' => 'table.remark',
	'dataIndex' => 'remark',
	'width' => 100,
	'sorter' => true,
	'sql_where' => '`remark` LIKE ?',
	'form' => 'input',
	'placeholder' => 'table.please_enter',
	'rules' => [['required' => false, 'message' => 'table.please_enter']],
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
		'from' => 'system_users',
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
		'rowKey' => 'uid',
		'rowSelection' => true,
	],
];
echo json_encode($_C->db('asiafort')->tableReader($data));
