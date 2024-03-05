<?php

namespace QuickPHP;

class Config
{
	private $mConf;
	private $auth;

	public function __construct()
	{
		$this->mConf = $this->GetConfig();
	}

	public function auth()
	{
		if ($this->auth) {
			return $this->auth;
		}
		$this->auth = new Auth($this->mConf);
		return $this->auth;
	}

	public function db($dbName = '')
	{
		$dbs = $this->mConf['dbs'];
		$mDbconf = $dbs[$dbName];
		return new TableCrud($mDbconf);
	}

	public function form($dbName = '')
	{
		$dbs = $this->mConf['dbs'];
		$mDbconf = $dbs[$dbName];
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
		if ($this->mConf) {
			return $this->mConf;
		}
		$site_name = $this->GetSiteName();
		$config_dir = ROOT_DIR . DS . 'config';
		$mSiteConf = require($config_dir . DS . 'sites' . DS . $site_name . '.php');
		$mCommonConf = require(ROOT_DIR . DS . 'config.php');
		return array_merge($mCommonConf, $mSiteConf);
	}
}
