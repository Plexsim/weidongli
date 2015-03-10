<?php



/**



 * 微站管理



 * [52media System]



 */



define('IN_MOBILE', true);



require 'source/bootstrap.inc.php';



include model('mobile');







$actions = array('channel', 'module', 'auth', 'oauth', 'entry', 'cash');



if (in_array($_GPC['act'], $actions)) {



	$action = $_GPC['act'];



} else {



	$action = 'channel';



}







$controller = 'mobile';



require router($controller, $action);