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
$data['buttons'][] = array('type' => 'add', 'title' => '添加');
$data['buttons'][] = array('type' => 'delete', 'title' => '删除', 'popconfirm' => ['title' => '确定要批量删除这些吗？', 'okText' => '确定', 'cancelText' => '取消']);
$data['columns'] = array();
$data['columns'][] = array(
    'title' => 'data_no',
    'dataIndex' => 'data_no',
    'width' => 80,
    'sorter' => true,
    'form' => 'input',
    'disabled' => true,
    'readonly' => true,
);
$data['columns'][] = array(
    'title' => 'person_name',
    'dataIndex' => 'person_name',
    'width' => 160,
    'sorter' => true,
    'sql_where' => 'person_name LIKE ?',
    'form' => 'input',
    'placeholder' => 'please enter person_name',
);
$data['columns'][] = array(
    'title' => 'sex',
    'dataIndex' => 'sex',
    'width' => 80,
    'sorter' => true,
    'valueFunc' => function ($v) {
        if ($v === 1) {
            return '男';
        }
        if ($v === 2) {
            return '女';
        }
    },
    'form' => 'select',
    'options' => [
        ['value' => 1, 'title' => '男'],
        ['value' => 2, 'title' => '女'],
    ],
    'placeholder' => "Please choose the sex",
);
$data['columns'][] = array(
    'title' => 'Action',
    'fixed' => 'right',
    'width' => 140,
    'actions' => [
        ['action' => 'view', 'title' => '查看'],
        ['action' => 'edit', 'title' => '编辑'],
        ['action' => 'delete', 'title' => '删除', 'popconfirm' => ['title' => 'Are you sure delete?', 'okText' => 'Yes', 'cancelText' => 'Cancel']],
    ]
);
echo json_encode($_C->db('kaoqin')->tableReader($data));
