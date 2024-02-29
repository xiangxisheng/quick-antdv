<?php

$config = require_once dirname(__DIR__) . '/quick.php';

$i18n = new QuickPHP\I18n($config);
$langPath = ROOT_DIR . '/wwwroot/static/data/lang';
$i18n->generateLangpack($langPath, 'asiafort');
