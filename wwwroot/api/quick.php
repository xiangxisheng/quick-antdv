<?php

define('ROOT_DIR', dirname(dirname(__DIR__)));
require(ROOT_DIR . '/php/class.php');
$mConf = require(ROOT_DIR . '/config.php');
$_C = new \QuickPHP\Config($mConf);
