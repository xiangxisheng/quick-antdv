<?php

require_once dirname(__DIR__) . '/quick.php';

$data = array();
$data['info'] = array();
$data['info']['pageMax'] = 100;
$data['info']['rowSelection'] = true;
$data['info']['rowKey'] = 'data_no';
$data['sql'] = array();
$data['sql']['from'] = 'tb_person';
$data['sql']['where'] = array();
$data['sql']['where'][] = "status = '0'";
$data['sql']['order'] = '';
$data['buttons'] = array();
$data['buttons'][] = array('type' => 'add');
$data['buttons'][] = array('type' => 'delete');
$data['columns'] = array();
$data['columns'][] = array(
    'title' => 'data_no',
    'dataIndex' => 'data_no',
    'sorter' => true,
);
$data['columns'][] = array(
    'title' => 'person_name',
    'dataIndex' => 'person_name',
    'sorter' => true,
);
$data['columns'][] = array(
    'title' => 'sex',
    'dataIndex' => 'sex',
    'sorter' => true,
    'valueFunc' => function ($v) {
        if ($v === 1) {
            return '男';
        }
        if ($v === 2) {
            return '女';
        }
    },
);
echo json_encode($_C->db('kaoqin')->tableReader($data));
