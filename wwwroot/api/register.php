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
	'title' => 'sign.confirmPassword',
	'label' => 'sign.confirmPassword',
	'dataIndex' => 'password_confirm',
	'form' => 'input-password',
	'placeholder' => 'Please confirm your password!',
	'rules' => [['required' => true, 'message' => 'Please confirm your password!']],
];
$items[] = [
	'form' => 'buttons',
	'wrapper-col' => ['span' => 24],
	'buttons' => [
		['form' => 'button', 'type' => 'link', 'title' => 'sign.login', 'link' => '/login'],
		['form' => 'button', 'type' => 'primary', 'html-type' => 'submit', 'title' => 'sign.submit', 'span' => 8],
	],
];
$data = [
	'headers' => [
		['type' => 'title', 'title' => 'sign.register',],
	],
	'items' => $items,
	'onAction' => function () {
	},
];
echo json_encode($_C->form()->reader($data));
