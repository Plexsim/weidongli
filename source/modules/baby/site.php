<?php
defined('IN_IA') or exit('Access Denied');

class babyModuleSite extends WeModuleSite {
	
	public function doMobileIndex(){
		global $_GPC, $_W;
		include $this->template ( 'detail' );
	}
	
}
