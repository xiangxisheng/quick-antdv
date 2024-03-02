<?php

namespace QuickPHP;

class Config
{
	private $c_dbs;

	public function __construct($mConf)
	{
		$this->c_dbs = $mConf['dbs'];
	}

	public function db($dbName = '')
	{
		$mDbconf = $this->c_dbs[$dbName];
		return new TableCrud($mDbconf);
	}

	public function form($dbName = '')
	{
		$mDbconf = $this->c_dbs[$dbName];
		return new Form($mDbconf);
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
		$site_name = $this->GetSiteName();
		$config_dir = ROOT_DIR . DS . 'config';
		return require($config_dir . DS . 'sites' . DS . $site_name . '.php');
	}
}
