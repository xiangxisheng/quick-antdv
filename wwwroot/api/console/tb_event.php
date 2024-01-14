<?php

require_once dirname(__DIR__) . '/F.php';

$data = array();
$data['info'] = array();
$data['info']['pageMax'] = 100;
$data['info']['rowSelection'] = false;
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
    'title' => 'Date',
    'dataIndex' => 'event_date',
    'type' => 'date',
    'format' => 'YYYY-MM-DD',
    'sorter' => true,
    'sql_select' => "event_time::date",
    'sql_where' => 'event_time::date = ?'
);
$data['columns'][] = array(
    'title' => 'Time',
    'dataIndex' => 'event_time',
    'sorter' => true,
    'sql_select' => "to_char(TO_TIMESTAMP(MIN(utc_event_time)/1000),'HH24:MI:SS')"
);
$data['columns'][] = array(
    'title' => 'Number',
    'dataIndex' => 'job_number',
    'sorter' => true,
    'sql_where' => 'job_number LIKE ?'
);
$data['columns'][] = array(
    'title' => 'Name',
    'dataIndex' => 'person_name',
    'sorter' => true,
    'sql_select' => "MIN(person_name)",
    'sql_where' => 'person_name LIKE ?'
);
echo json_encode($pgSQL->tableReader($data));
