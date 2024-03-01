<?php

define('DS', DIRECTORY_SEPARATOR);
define('COMPONENT_DIR', __DIR__ . DS . 'component');

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
		$i1 = mb_strrpos($strall, $str1, - (mb_strlen($strall, $html_charset) - $i2), $html_charset);
	}
	if (!is_int($i1)) {
		return '';
	} //str1没找到！
	return mb_substr($strall, $i1 + mb_strlen($str1, $html_charset), $i2 - $i1 - mb_strlen($str1, $html_charset));
}

function getVueFiles($folder_path, $ext = 'vue')
{
	$php_files = [];
	$files = scandir($folder_path);
	foreach ($files as $file) {
		if ($file === '.' || $file === '..') {
			continue;
		}
		$full_path = $folder_path . DS . $file;
		if (is_dir($full_path)) {
			// 如果是文件夹，则递归调用该函数
			$php_files = array_merge($php_files, getVueFiles($full_path));
		} elseif (pathinfo($full_path, PATHINFO_EXTENSION) === 'vue') {
			// 如果是 PHP 文件，则添加到结果数组中
			$php_files[] = $full_path;
		}
	}
	return $php_files;
}

function getJs($vueFile)
{
	$html = file_get_contents($vueFile);
	$template = json_encode(getstr2($html, "<template>\r\n\t", "\r\n</template>"));
	$script_define = getstr1($html, '<script define>', '</script>');
	$script_setup = getstr1($html, '<script setup>', '</script>');
	$js = "{$script_define}\r\nexport default async (oTopRoute) => ({\r\n\ttemplate: {$template},\r\n\tcomponents,\r\n\tsetup() {{$script_setup}},\r\n});";
	return $js;
}

$vueFiles = getVueFiles(COMPONENT_DIR);
foreach ($vueFiles as $vueFile) {
	file_put_contents($vueFile . '.js', getJs($vueFile));
}
echo "Component file successfully generated.\r\n";
