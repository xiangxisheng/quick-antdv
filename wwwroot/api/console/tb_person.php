<?php

require_once dirname(__DIR__) . '/quick.php';

$columns = array();
$columns[] = array(
    'title' => '工号',
    'dataIndex' => 'person_key',
    'width' => 80,
    'sorter' => true,
    'form' => 'input',
    'rules' => [['required' => true, 'message' => 'Please enter person_key']],
    'sql_where' => 'person_key LIKE ?',
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
    'placeholder' => "Please choose the sex",
    'rules' => [['required' => true, 'message' => 'Please choose the sex']],
    'sql_where' => 'sex=?',
);
$columns[] = array(
    'title' => '生日',
    'dataIndex' => 'birthday',
    'width' => 160,
    'sorter' => true,
    'sql_where' => 'birthday=?',
    'form' => 'date-picker',
    'type' => 'date',
    'format' => 'YYYY-MM-DD',
    'placeholder' => 'please enter birthday',
    'rules' => [['required' => false, 'message' => 'Please enter birthday']],
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
echo json_encode($_C->db('kaoqin')->tableReader($data));
