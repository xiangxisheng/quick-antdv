<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$_C = require_once dirname(dirname(__DIR__)) . DS . 'quick.php';

$columns = array();
$columns[] = array(
	'title' => 'person_key',
	'dataIndex' => 'person_key',
	'width' => 80,
	'sorter' => true,
	'form' => 'input',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
	'sql_where' => 'person_key LIKE ?',
);
$columns[] = array(
	'title' => 'table.person_name',
	'dataIndex' => 'person_name',
	'width' => 160,
	'sorter' => true,
	'sql_where' => 'person_name LIKE ?',
	'form' => 'input',
	'placeholder' => 'table.please_enter',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
);
$columns[] = array(
	'title' => 'table.person_gender',
	'dataIndex' => 'sex',
	'width' => 80,
	'sorter' => true,
	'valueFunc' => function ($v) {
		if ($v === 1) {
			return 'table.male';
		}
		if ($v === 2) {
			return 'table.female';
		}
	},
	'form' => 'select',
	'options' => [
		['value' => 1, 'title' => 'table.male'],
		['value' => 2, 'title' => 'table.female'],
	],
	'placeholder' => "table.please_choose",
	'rules' => [['required' => true, 'message' => 'table.please_choose']],
	'sql_where' => 'sex=?',
);
$columns[] = array(
	'title' => 'table.birth_date',
	'dataIndex' => 'birthday',
	'width' => 160,
	'sorter' => true,
	'sql_where' => 'birthday=?',
	'form' => 'date-picker',
	'type' => 'date',
	'format' => 'YYYY-MM-DD',
	'placeholder' => 'table.please_enter',
	'rules' => [['required' => false, 'message' => 'table.please_enter']],
);
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
$columns[] = array(
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
);
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
		'from' => 'tb_person',
		'where' => [
			//"status = '0'",
		],
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
		'rowKey' => 'person_key',
		'rowSelection' => true,
	],
];
echo json_encode($_C->db('kaoqin')->tableCrud()->tableReader($data));
