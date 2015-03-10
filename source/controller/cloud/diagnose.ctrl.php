<?php 



/**



 * 云服务诊断



 * [52media System]



 */







if(empty($_W['isfounder'])) {



	message('访问非法.');



}







if(checksubmit()) {



	include model('setting');



	setting_save('', 'site');



	message('成功清除站点记录.', 'refresh');



}



if(empty($_W['setting']['site'])) {



	$_W['setting']['site'] = array();



}



template('cloud/diagnose');



