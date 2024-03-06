<?php

namespace QuickPHP;

class Config
{
	private $mConf = [];
	private $auth = null;
	private $mPDOs = [];

	public function __construct()
	{
		$this->mConf = $this->GetConfig();
	}

	public function getSetting($name)
	{
		return $this->mConf['setting'][$name];
	}

	public function getRoutes()
	{
		return $this->mConf['routes'];
	}

	public function auth()
	{
		if ($this->auth) {
			return $this->auth;
		}
		$this->auth = new Auth($this);
		return $this->auth;
	}

	public function db($dbName)
	{
		// 修改默认数据库
		$this->mConf['setting']['db'] = $dbName;
		return $this;
	}

	public function pdo($_sDbName = null)
	{
		// 可以给 $_sDbName 指定一个临时的数据库名，仅针对本次操作
		$sDbName = $_sDbName ? $_sDbName : $this->mConf['setting']['db'];
		if (isset($this->mPDOs[$sDbName])) {
			return $this->mPDOs[$sDbName];
		}
		$dbConf = $this->mConf['dbs'][$sDbName];
		$this->mPDOs[$sDbName] = new PDO();
		$this->mPDOs[$sDbName]->conn($dbConf['dsn'], $dbConf['user'], $dbConf['pass']);
		return $this->mPDOs[$sDbName];
	}

	public function tableCrud()
	{
		return new TableCrud($this->pdo());
	}

	public function form()
	{
		return new Form($this->pdo());
	}

	public function GetRequestHostName()
	{
		$host_parts = explode(':', $_SERVER['HTTP_HOST']);
		return $host_parts[0];
	}

	private function GetSiteName()
	{
		$host_name = $this->GetRequestHostName();
		$hosts = require(ROOT_DIR . DS . 'config' . DS . 'hosts.php');
		if (isset($hosts[$host_name])) {
			return $hosts[$host_name];
		}
		return 'default';
	}

	public function GetConfig()
	{
		if ($this->mConf) {
			return $this->mConf;
		}
		$site_name = $this->GetSiteName();
		$config_dir = ROOT_DIR . DS . 'config';
		$mSiteConf = require($config_dir . DS . 'sites' . DS . $site_name . '.php');
		if (!isset($mSiteConf['setting']['db'])) {
			$mSiteConf['setting']['db'] = $site_name;
		}
		$mCommonConf = require(ROOT_DIR . DS . 'config.php');
		return array_merge($mCommonConf, $mSiteConf);
	}
}
