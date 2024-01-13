<?php

require_once dirname(__DIR__) . '/F.php';
$sql = <<<EOF
SELECT
	to_date( event_time :: TEXT, 'yyyy-mm-dd' ) event_date,
	MIN ( event_time ) event_time,
	job_number,
	MIN ( person_name ) person_name
FROM
	tb_event
WHERE
	event_name = 'acs.acs.eventType.successFace'
	AND to_date( event_time :: TEXT, 'yyyy-mm-dd' ) = ?
	AND job_number IS NOT NULL
GROUP BY
	job_number,
	event_date
ORDER BY
	event_time
EOF;
$date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$data = array();
$data['rows'] = $pgSQL->fetchAll($sql, array($date));
$data['columns'] = array();
$data['columns'][] = array('title' => 'event_date', 'dataIndex' => 'event_date');
$data['columns'][] = array('title' => 'event_time', 'dataIndex' => 'event_time');
$data['columns'][] = array('title' => 'job_number', 'dataIndex' => 'job_number');
$data['columns'][] = array('title' => 'person_name', 'dataIndex' => 'person_name');
echo json_encode($data);
