<?php

namespace QuickPHP;

class PDO extends \PDO
{
	private $dsn_uri;

	public function __construct()
	{
	}

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

	private function toQueryString($sql)
	{
		$sql = str_replace('{table_pre}', '', $sql);
		return $sql;
	}

	public function execute($sql, $param)
	{
		$stmt = $this->prepare($this->toQueryString($sql));
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

	public function insert($sFrom, $mInserts)
	{
		$aFields = [];
		$aValues = [];
		$mParam = [];
		foreach ($mInserts as $sField => $value) {
			$aFields[] = $this->fieldQuote($sField);
			if (is_array($value)) {
				$aValues[] = $value[0];
			} else {
				$aValues[] = ':' . $sField;
				$mParam[$sField] = $value;
			}
		}
		$sFields = implode(',', $aFields);
		$sValues = implode(',', $aValues);
		$sSql = "INSERT INTO {$sFrom}($sFields)VALUES($sValues)";
		return $this->execute($sSql, $mParam);
	}

	public function update($sFrom, $mSets, $mWheres)
	{
		$mParam = [];
		$aSets = [];
		foreach ($mSets as $sField => $value) {
			if (is_array($value)) {
				$aSets[] = $this->fieldQuote($sField) . '=' . $value[0];
			} else {
				$aSets[] = $this->fieldQuote($sField) . '=:' . $sField;
				$mParam[$sField] = $value;
			}
		}
		$aWheres = [];
		foreach ($mWheres as $sField => $value) {
			if (is_array($value)) {
				$aWheres[] = $this->fieldQuote($sField) . '=' . $value[0];
			} else {
				$aWheres[] = $this->fieldQuote($sField) . '=:' . $sField;
				$mParam[$sField] = $value;
			}
		}
		$sSets = implode(',', $aSets);
		$sWheres = '(' . implode(')AND(', $aWheres) . ')';
		$sSql = "UPDATE {$sFrom} SET {$sSets} WHERE {$sWheres}";
		return $this->execute($sSql, $mParam);
	}
}
