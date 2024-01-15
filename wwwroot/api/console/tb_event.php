<?php

require_once dirname(__DIR__) . '/quick.php';

$data = array();
$data['info'] = array();
$data['info']['pageMax'] = 100;
$data['info']['rowSelection'] = false;
$data['info']['rowKey'] = 'event_id';
$data['sql'] = array();
$data['sql']['from'] = 'tb_event';
$data['sql']['where'] = array();
$data['sql']['where'][] = "event_name = 'acs.acs.eventType.successFace'";
$data['sql']['where'][] = 'job_number IS NOT NULL';
$data['sql']['group'] = 'job_number,event_time::date';
$data['sql']['order'] = 'event_date DESC,event_time';
$data['buttons'] = array();
//$data['buttons'][] = array('type' => 'add');
$data['columns'] = array();
$data['columns'][] = array(
    'title' => 'event_id',
    'dataIndex' => 'event_id',
    'sql_select' => 'MIN(event_id)',
    'form' => 'input',
    'disabled' => true,
    'readonly' => true,
);
$data['columns'][] = array(
    'title' => 'Date',
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
$data['columns'][] = array(
    'title' => 'Time',
    'dataIndex' => 'event_time',
    'width' => 90,
    'sorter' => true,
    'sql_selone' => "to_char(TO_TIMESTAMP(utc_event_time/1000),'HH24:MI:SS')",
    'sql_select' => "to_char(TO_TIMESTAMP(MIN(utc_event_time)/1000),'HH24:MI:SS')",
    'form' => 'input',
);
$data['columns'][] = array(
    'title' => 'Number',
    'dataIndex' => 'job_number',
    'width' => 100,
    'sorter' => true,
    'sql_where' => 'job_number LIKE ?',
    'form' => 'input',
);
$data['columns'][] = array(
    'title' => 'Name',
    'dataIndex' => 'person_name',
    'width' => 160,
    'sorter' => true,
    'sql_select' => "MIN(person_name)",
    'sql_where' => 'person_name LIKE ?',
    'form' => 'input',
);
$data['columns'][] = array(
    'title' => 'Action',
    'fixed' => 'right',
    'width' => 80,
    'actions' => [
        ['action' => 'view', 'title' => '查看'],
    ]
);
echo json_encode($_C->db('acs_acsdb')->tableReader($data));
