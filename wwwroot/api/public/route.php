<?php

$route = array();
$route['path'] = '/';
$route['component'] = 'page/index';
$route['role'] = 'public';
$route['children'] = array(
  array(
    'path' => '/:pathMatch(.*)',
    'component' => 'component/404',
  ),
  array(
    'name' => 'console',
    'alias' => '/',
    'label' => '控制台',
    'component' => 'page/panel',
    'role' => 'user',
    'children' => array(
      array(
        'name' => 'tb_event',
        'label' => '签到查询',
        'component' => 'component/table',
        'role' => 'user',
      ),
      array(
        'name' => 'tb_person',
        'label' => 'person',
        'component' => 'component/table',
        'role' => 'user',
      ),
      array(
        'name' => 'system_i18n_data',
        'label' => 'system_i18n_data',
        'component' => 'component/table',
        'role' => 'user',
      ),
    ),
  ),
);
echo json_encode(array($route));
