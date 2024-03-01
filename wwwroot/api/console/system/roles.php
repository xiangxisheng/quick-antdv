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
	'title' => 'table.title',
	'dataIndex' => 'title',
	'width' => 80,
	'sorter' => true,
	'form' => 'input',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
	'sql_where' => '`title` LIKE ?',
];
$columns[] = [
	'title' => 'table.sequence',
	'dataIndex' => 'orderNo',
	'default' => 100,
	'width' => 100,
	'sorter' => true,
	'sql_where' => '`orderNo` LIKE ?',
	'form' => 'input',
	'placeholder' => 'table.please_enter',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
];
$columns[] = [
	'title' => 'table.status',
	'dataIndex' => 'status',
	'width' => 80,
	'default' => 0,
	'valueFunc' => function ($v) {
		if ($v === -1) {
			return 'table.disabled';
		}
		if ($v === 0) {
			return 'table.normal';
		}
	},
	'form' => 'select',
	'options' => [
		['value' => -1, 'title' => 'table.disabled'],
		['value' => 0, 'title' => 'table.normal'],
	],
	'sql_where' => 'status=?',
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
		'from' => 'system_roles',
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
];
echo json_encode($_C->db()->tableReader($data));
