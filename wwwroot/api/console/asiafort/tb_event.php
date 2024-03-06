<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$_C = require_once dirname(dirname(__DIR__)) . DS . 'quick.php';

$columns = array();
$columns[] = array(
	'title' => 'table.sequence',
	'width' => 80,
	'type' => 'sequence',
);
$columns[] = array(
	'title' => 'event_id',
	'dataIndex' => 'event_id',
	'sql_select' => 'MIN(event_id)',
	'form' => 'input',
	'disabled' => true,
	'readonly' => true,
);
$columns[] = array(
	'title' => 'table.date',
	'dataIndex' => 'event_date',
	'width' => 110,
	'type' => 'date',
	'format' => 'YYYY-MM-DD',
	'sorter' => true,
	'sql_selone' => "event_time::date",
	'sql_select' => "event_time::date",
	'sql_where' => 'event_time::date = ?',
	'form' => 'date-picker',
);
$columns[] = array(
	'title' => 'table.time',
	'dataIndex' => 'event_time',
	'width' => 90,
	'sorter' => true,
	'sql_selone' => "to_char(TO_TIMESTAMP(utc_event_time/1000),'HH24:MI:SS')",
	'sql_select' => "to_char(TO_TIMESTAMP(MIN(utc_event_time)/1000),'HH24:MI:SS')",
	'form' => 'input',
);
$columns[] = array(
	'title' => 'Number',
	'dataIndex' => 'job_number',
	'width' => 100,
	'sorter' => true,
	'sql_where' => 'job_number LIKE ?',
	'form' => 'input',
);
$columns[] = array(
	'title' => 'table.person_name',
	'dataIndex' => 'person_name',
	'width' => 160,
	'sorter' => true,
	'sql_select' => "MIN(person_name)",
	'sql_where' => 'person_name LIKE ?',
	'form' => 'input',
);
$columns[] = array(
	'title' => 'table.dept_name',
	'dataIndex' => 'dept_name',
	'width' => 160,
	'sorter' => true,
	'sql_select' => "MIN(dept_name)",
	'sql_where' => 'dept_name LIKE ?',
	'form' => 'input',
);
$columns[] = array(
	'title' => 'table.operates',
	'fixed' => 'right',
	'width' => 80,
	'operates' => [
		[
			'action' => 'view', 'title' => 'table.view',
		],
	]
);
$data = [
	'buttons' => [
		[
			'type' => 'export', 'title' => 'table.export',
		],
	],
	'sql' => [
		'from' => 'tb_event',
		'where' => [
			"event_name = 'acs.acs.eventType.successFace'",
			'job_number IS NOT NULL',
		],
		'group' => 'job_number,event_time::date',
		'order' => 'event_date DESC,event_time',
	],
	'table' => [
		'columns' => $columns,
		'pagination' => [
			'pageSizeDefault' => 20,
			'pageSizeMax' => 1000,
			'pageSizeOptions' => ['10', '20', '50', '100', '200', '500', '1000'],
			'showTotalTemplate' => 'table.showTotalTemplate',
		],
		'rowKey' => 'event_id',
		'rowSelection' => false,
	],
];
echo json_encode($_C->db('acs_acsdb')->tableCrud()->tableReader($data));
