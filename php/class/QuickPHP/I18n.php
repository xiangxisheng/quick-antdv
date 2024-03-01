<?php

namespace QuickPHP;

class I18n
{
	private $localePre = 'locale_';
	private $config;

	function __construct($config)
	{
		$this->config = $config;
	}

	public function generateLangpack($langPath, $dbName = '', $fCallback = null)
	{
		$json_flags = 0;
		$json_flags |= JSON_UNESCAPED_UNICODE;
		$json_flags |= JSON_PRETTY_PRINT;
		if (is_callable($fCallback)) {
			$fCallback('Reading Langpack from Database ...');
		}
		$this->createDirectory($langPath);
		$sql = 'SELECT * FROM system_i18n_data';
		$rows = $this->config->db($dbName)->fetchAll($sql, []);
		if (is_callable($fCallback)) {
			$fCallback(' Done!');
		}
		$locales = $this->get_locales($rows);
		foreach ($locales as $locale) {
			$lp = $this->get_langpack($rows, $locale);
			$path = $langPath . '/' . $locale . '.json';
			if (is_callable($fCallback)) {
				$fCallback("\r\nwrite langpack to {$path} ...");
			}
			file_put_contents($path, json_encode($lp, $json_flags));
			if (is_callable($fCallback)) {
				$fCallback(' Done!');
			}
		}
		if (is_callable($fCallback)) {
			$fCallback("\r\nFinished!\r\n");
		}
	}

	private function createDirectory($path)
	{
		if (!file_exists($path)) {
			$this->createDirectory(dirname($path));
			mkdir($path);
		}
	}

	private function get_locales($rows)
	{
		$locales = [];
		foreach ($rows as $row) {
			$keys = array_keys($row);
			foreach ($keys as $key) {
				if (strpos($key, $this->localePre) === 0) {
					$locales[] = substr($key, strlen($this->localePre));
				}
			}
			break;
		}
		return $locales;
	}

	private function get_langpack($rows, $locale)
	{
		$ret = [];
		foreach ($rows as $row) {
			if (!isset($ret[$row['group']])) {
				$ret[$row['group']] = [];
			}
			$ret[$row['group']][$row['name']] = $row[$this->localePre . $locale];
		}
		return $ret;
	}
}
