<?php

namespace QuickPHP;

class PDO extends \PDO
{
	private $dsn_uri;
	public function conn($dsn, $user, $pass)
	{
		$this->dsn_uri = parse_url($dsn);
		$options = [];
		if ($this->dsn_uri['scheme'] === 'mysql') {
			$options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'utf8mb4';";
		}
		parent::__construct($dsn, $user, $pass, $options);
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function begin()
	{
		$this->beginTransaction();
	}

	public function execute($sql, $param)
	{
		$stmt = $this->prepare($sql);
		$stmt->execute($param);
		return $stmt;
	}

	public function fetchAll($sql, $param)
	{
		$this->begin();
		$stmt = $this->execute($sql, $param);
		$this->rollBack();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function fetch($sql, $param)
	{
		$this->begin();
		$stmt = $this->execute($sql, $param);
		$this->rollBack();
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function fetchNum($sql, $param)
	{
		$this->begin();
		$stmt = $this->execute($sql, $param);
		$this->rollBack();
		return $stmt->fetch(PDO::FETCH_NUM);
	}

	public function fieldQuote($name)
	{
		$driverName = isset($this->dsn_uri['scheme']) ? $this->dsn_uri['scheme'] : '';
		if ($driverName === 'mysql') {
			return "`{$name}`";
		}
		if ($driverName === 'pgsql') {
			return "\"{$name}\"";
		}
		return $name;
	}
}
