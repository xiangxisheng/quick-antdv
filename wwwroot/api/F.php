<?php

$php_dir = dirname(dirname(__DIR__)) . '/php';
$config = require($php_dir . '/config.php');
require $php_dir . '/pgsql.php';
$c_pg = $config['pgsql'];
$pgSQL = new PgSQL($c_pg['dsn'], $c_pg['user'], $c_pg['pass']);
