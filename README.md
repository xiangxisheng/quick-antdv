# 快速中控台的开发框架
新建一个config.php文件放在根目录
```
<?php


$dbs = [
	'test' => [
		'dsn' => 'mysql:dbname=test;host=127.0.0.1;port=3306',
		'user' => 'test',
		'pass' => '',
	],
];

$dbs['default'] = $dbs['test'];

return [
	'dbs' => $dbs
];
```
