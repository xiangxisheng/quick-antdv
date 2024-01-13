<?php

$route = array();
$route['path'] = '/';
$route['component'] = 'index';
$route['role'] = 'public';
$route['children'] = array(
  array(
    'path' => '/:pathMatch(.*)',
    'component' => '404',
  ),
  array(
    'name' => 'console',
    'alias' => '/',
    'label' => '控制台',
    'component' => 'panel',
    'role' => 'user',
    'children' => array(
      array(
        'name' => 'tb_event',
        'label' => '签到查询',
        'component' => 'panel/table',
        'role' => 'user',
      ),
    ),
  ),
);
echo json_encode(array($route));
