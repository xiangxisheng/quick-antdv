<?php

require_once dirname(__DIR__) . '/quick.php';

$columns = [];
$columns[] = [
    'title' => 'group',
    'dataIndex' => 'group',
    'width' => 80,
    'sorter' => true,
    'form' => 'input',
    'rules' => [['required' => true, 'message' => 'Please enter group']],
    'sql_where' => '"group" LIKE ?',
];
$columns[] = [
    'title' => 'name',
    'dataIndex' => 'name',
    'width' => 160,
    'sorter' => true,
    'sql_where' => '"name" LIKE ?',
    'form' => 'input',
    'placeholder' => 'please enter name',
    'rules' => [['required' => true, 'message' => 'Please enter name']],
];
$columns[] = [
    'title' => 'locale_en_us',
    'dataIndex' => 'locale_en_us',
    'width' => 160,
    'sorter' => true,
    'sql_where' => 'locale_en_us LIKE ?',
    'form' => 'input',
    'placeholder' => 'please enter locale_en_us',
    'rules' => [['required' => true, 'message' => 'Please enter locale_en_us']],
];
$columns[] = [
    'title' => 'locale_zh_cn',
    'dataIndex' => 'locale_zh_cn',
    'width' => 160,
    'sorter' => true,
    'sql_where' => 'locale_zh_cn LIKE ?',
    'form' => 'input',
    'placeholder' => 'please enter locale_zh_cn',
];
$columns[] = [
    'title' => 'locale_km_kh',
    'dataIndex' => 'locale_km_kh',
    'width' => 160,
    'sorter' => true,
    'sql_where' => 'locale_km_kh LIKE ?',
    'form' => 'input',
    'placeholder' => 'please enter locale_km_kh',
];
$columns[] = [
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
];
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
        'from' => 'system_i18n_data',
        'where' => [],
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
        'rowKey' => 'name',
        'rowSelection' => true,
    ],
];
echo json_encode($_C->db('kaoqin')->tableReader($data));
