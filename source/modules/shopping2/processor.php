<?php
/**
 * 
 * 
 * [WeEngine System] ¸ü¶àÄ£¿éÇëä¯ÀÀ£ºBBS.b2ctui.com
 */

defined('IN_IA') or exit('Access Denied');
class Shopping1ModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
		$this->module['config']['picurl'] = $_W['attachurl'] . $this->module['config']['picurl'];
		return $this->respNews($this->module['config']);
	}
}