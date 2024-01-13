<?php

require_once dirname(__DIR__) . '/F.php';

$data = array();
$data['info'] = array();
$data['info']['pageSizeMax'] = 100;
$data['sql'] = array();
$data['sql']['from'] = 'tb_event';
$data['sql']['where'] = array();
$data['sql']['where'][] = "event_name = 'acs.acs.eventType.successFace'";
$data['sql']['where'][] = 'job_number IS NOT NULL';
$data['sql']['group'] = 'job_number,event_time::date';
$data['sql']['order'] = 'event_time';
$data['columns'] = array();
$data['columns'][] = array('title' => 'Date', 'dataIndex' => 'event_date', 'sql_select' => "event_time::date", 'sql_where' => 'event_time::date = ?');
$data['columns'][] = array('title' => 'Time', 'dataIndex' => 'event_time', 'sql_select' => "to_char(TO_TIMESTAMP(MIN(utc_event_time)/1000),'HH24:MI:SS')");
$data['columns'][] = array('title' => 'Number', 'dataIndex' => 'job_number');
$data['columns'][] = array('title' => 'Name', 'dataIndex' => 'person_name', 'sql_select' => "MIN(person_name)");
echo json_encode($pgSQL->tableReader($data));
