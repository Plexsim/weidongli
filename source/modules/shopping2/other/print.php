<?php
/**
 * 微站管理
 * [WeEngine System] 更多模块请浏览：BBS.b2ctui.com
 */
define('IN_SYS', true);
require 'source/bootstrap.inc.php';
 
header('Content-Type: text/html; charset=GBK');

$modulename ='shopping2';

$site = WeUtility::createModuleSite($modulename);
if(!is_error($site)) {
 	$method = 'doWebPrint';
 	//$site->module = array_merge($_W['modules'][$modulename], $_W['account']['modules'][$_W['modules'][$modulename]['mid']]);
	$site->weid = $_W['weid'];
	$site->inMobile = false;
	if (method_exists($site, $method)) {
		exit($site->$method());
	}
}

exit("访问的方法 {$method} 不存在.");
