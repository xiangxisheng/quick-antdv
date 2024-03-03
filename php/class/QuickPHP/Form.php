<?php

namespace QuickPHP;

class Form extends PDO
{
	public function __construct($dbConf)
	{
		$this->conn($dbConf['dsn'], $dbConf['user'], $dbConf['pass']);
	}

	public function reader($data)
	{
		$action = isset($_GET['action']) ? $_GET['action'] : '';
		if ($action === 'init') {
			return $data;
		}
		return [
			'message' => ['type' => 'error', 'content' => 'test'],
		];
	}
}
