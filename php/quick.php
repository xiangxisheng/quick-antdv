<?php

define('ROOT_DIR', dirname(__DIR__));
require(ROOT_DIR . '/php/class.php');
$mConf = require(ROOT_DIR . '/config.php');
return new \QuickPHP\Config($mConf);
