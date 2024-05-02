<?php

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
define('COMPONENT_DIR', __DIR__ . DS . 'static' . DS . 'component');

if (isset($argv[1])) {
	if ($argv[1] === 'build') {
		$startTime = microtime(true);
		component_build();
		$executionTime = ceil((microtime(true) - $startTime) * 1000);
		echo "Component file successfully generated. ({$executionTime}ms)\r\n";
	}
}

function getstr1($strall, $str1, $str2)
{
	//从前面开始找
	$html_charset = 'utf-8';
	$i1 = mb_strpos($strall, $str1, 0, $html_charset);
	if (!is_int($i1)) {
		return '';
	} //str1没找到！
	$i1R = $i1 + mb_strlen($str1, $html_charset);
	if (!$str2) {
		return (mb_substr($strall, $i1R, NULL, $html_charset));
	}
	$i2 = mb_strpos($strall, $str2, $i1 + mb_strlen($str1, $html_charset), $html_charset);
	if (!is_int($i2)) {
		return '';
	} //str2都没找到！
	return (mb_substr($strall, $i1R, $i2 - $i1R, $html_charset));
}

function getstr2($strall, $str1, $str2)
{
	//从后面开始找
	$html_charset = 'utf-8';
	if ($str2 != "") {
		$i2 = mb_strrpos($strall, $str2, 0, $html_charset);
	}
	if (!is_int($i2)) {
		return '';
	} //str2都没找到！
	if ($str1 != "") {
		$i1 = mb_strrpos($strall, $str1, -(mb_strlen($strall, $html_charset) - $i2), $html_charset);
	}
	if (!is_int($i1)) {
		return '';
	} //str1没找到！
	return mb_substr($strall, $i1 + mb_strlen($str1, $html_charset), $i2 - $i1 - mb_strlen($str1, $html_charset));
}

function getVueFiles($folder_path, $ext = 'vue')
{
	$ret_files = [];
	$files = scandir($folder_path);
	foreach ($files as $file) {
		if ($file === '.' || $file === '..') {
			continue;
		}
		$full_path = $folder_path . DS . $file;
		if (is_dir($full_path)) {
			// 如果是文件夹，则递归调用该函数
			$ret_files = array_merge($ret_files, getVueFiles($full_path));
		} elseif (pathinfo($full_path, PATHINFO_EXTENSION) === $ext) {
			// 如果是 $ext 文件，则添加到结果数组中
			$ret_files[] = $full_path;
		}
	}
	return $ret_files;
}

function getJs($vueFile)
{
	$html = file_get_contents($vueFile);
	$template = json_encode(getstr2($html, "<template>", "</template>"));
	$script_define = getstr1($html, '<script define>', '</script>');
	$script_setup = getstr1($html, '<script setup>', '</script>');
	$js = "{$script_define}\r\nexport default async (oTopRoute) => ({\r\n\ttemplate: {$template},\r\n\tcomponents,\r\n\tsetup() {{$script_setup}},\r\n});";
	return $js;
}

function component_build()
{
	global $mConfig;
	$vueFiles = getVueFiles(COMPONENT_DIR, $mConfig['setting']['component_vue_ext']);
	foreach ($vueFiles as $vueFile) {
		$jsData = getJs($vueFile);
		$jsPath = $vueFile . '.' . $mConfig['setting']['component_js_ext'];
		if (!empty($mConfig['setting']['component_gz_ext'])) {
			$jsPath .= '.' . $mConfig['setting']['component_gz_ext'];
			$jsData = gzencode($jsData, 9);
		}
		file_put_contents($jsPath, $jsData);
	}
}
