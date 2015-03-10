<?php
/**
 * 违章查询  qq280594236
 *
 * @author Yoby
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class YobyweizhangModuleProcessor extends WeModuleProcessor {
	public function respond() {
				global $_W;

				$key=$_W['account']['modules']['yobyweizhang']['config']['kkk'];
		$city=$_W['account']['modules']['yobyweizahng']['config']['city'];
		


return $this->respText("<a href=mobile.php?act=module&weid=".$_W['weid']."&name=yobyweizhang&do=detail'>违章查询</a>");

		
	}
}