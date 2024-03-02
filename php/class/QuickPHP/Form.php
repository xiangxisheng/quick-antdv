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
		return $data;
	}
}
