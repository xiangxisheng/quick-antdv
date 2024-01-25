<?php

namespace QuickPHP;

define('CLASS_DIR', __DIR__ . '/class');
spl_autoload_register(function ($class) {
	require_once CLASS_DIR . '/' . $class . '.php';
});
