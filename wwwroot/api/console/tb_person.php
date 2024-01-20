<?php

require_once dirname(__DIR__) . '/quick.php';

$columns = array();
$columns[] = array(
    'title' => 'data_no',
    'dataIndex' => 'data_no',
    'width' => 80,
    'sorter' => true,
    'form' => 'input',
    'disabled' => true,
    'readonly' => true,
);
$columns[] = array(
    'title' => '姓名',
    'dataIndex' => 'person_name',
    'width' => 160,
    'sorter' => true,
    'sql_where' => 'person_name LIKE ?',
    'form' => 'input',
    'placeholder' => 'please enter person_name',
    'rules' => [['required' => true, 'message' => 'Please enter person_name']],
);
$columns[] = array(
    'title' => '性别',
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
    'rules' => [['required' => true, 'message' => 'Please choose the sex']],
);
$columns[] = array(
    'title' => '生日',
    'dataIndex' => 'birthday',
    'width' => 160,
    'sorter' => true,
    'sql_where' => 'birthday LIKE ?',
    'form' => 'date-picker',
    'type' => 'date',
    'format' => 'YYYY-MM-DD',
    'placeholder' => 'please enter birthday',
    'rules' => [['required' => false, 'message' => 'Please enter birthday']],
);
$columns[] = array(
    'title' => 'Action',
    'fixed' => 'right',
    'width' => 140,
    'actions' => [
        [
            'action' => 'view', 'title' => '查看',
        ],
        [
            'action' => 'edit', 'title' => '编辑',
            'buttons' => [
                ['title' => '取消'],
                ['title' => '保存', 'type' => 'primary'],
            ],
        ],
        [
            'action' => 'delete', 'title' => '删除',
            'popconfirm' => ['title' => 'Are you sure delete?', 'okText' => 'Yes', 'cancelText' => 'Cancel'],
        ],
    ]
);
$data = [
    'buttons' => [
        [
            'type' => 'add', 'title' => '添加',
            'buttons' => [
                ['title' => '取消'],
                ['title' => '添加', 'type' => 'primary'],
            ],
        ],
        [
            'type' => 'delete', 'title' => '删除',
            'popconfirm' => ['title' => '确定要批量删除这些吗？', 'okText' => '确定', 'cancelText' => '取消'],
        ],
    ],
    'sql' => [
        'from' => 'tb_person',
        'where' => [
            "status = '0'",
        ],
        'order' => '',
    ],
    'table' => [
        'columns' => $columns,
        'pagination' => [
            'pageSizeDefault' => 18,
            'pageSizeMax' => 100,
            'pageSizeOptions' => ['10', '20', '50', '100'],
            'showTotalTemplate' => 'Showing {begin} to {end} of {total} items',
        ],
        'rowKey' => 'data_no',
        'rowSelection' => true,
    ],
];
echo json_encode($_C->db('kaoqin')->tableReader($data));
