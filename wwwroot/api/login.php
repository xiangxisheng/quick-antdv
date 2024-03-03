<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
$_C = require_once __DIR__ . DS . 'quick.php';

$items = [];
$items[] = [
	'title' => 'sign.username',
	'label' => 'sign.username',
	'dataIndex' => 'username',
	'form' => 'input',
	'placeholder' => 'table.please_enter',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
];
$items[] = [
	'title' => 'sign.password',
	'label' => 'sign.password',
	'dataIndex' => 'password',
	'form' => 'input-password',
	'placeholder' => 'table.please_enter',
	'rules' => [['required' => true, 'message' => 'table.please_enter']],
];
$items[] = [
	'title' => 'Remember me',
	'dataIndex' => 'remember',
	'form' => 'checkbox',
	'wrapper-col' => ['span' => 24],
];
$items[] = [
	'form' => 'buttons',
	'wrapper-col' => ['span' => 24],
	'buttons' => [
		['form' => 'button', 'type' => 'link', 'title' => 'sign.register', 'link' => '/register'],
		['form' => 'button', 'type' => 'primary', 'html-type' => 'submit', 'title' => 'sign.submit', 'span' => 8],
	],
];
$data = [
	'headers' => [
		['type' => 'title', 'title' => 'sign.login',],
	],
	'items' => $items,
	'onAction' => function () {
	},
];
echo json_encode($_C->form()->reader($data));
