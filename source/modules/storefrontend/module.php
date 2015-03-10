<?php
/**
 * QQ群：304081212
 * 作者：晓楚, 547753994
 *
 * 网站：www.xuehuar.com
 */

defined('IN_IA') or exit('Access Denied');

class StoreFrontEndModule extends WeModule {
	public function settingsDisplay($settings) {
		global $_GPC, $_W;
		if(checksubmit()) {
			$cfg = array(
        'user' => $_GPC['user'],
        'pass' => $_GPC['pass'],
      );
			if($this->saveSettings($cfg)) {
				message('保存成功', 'refresh');
			}
		}
		include $this->template('setting');
	}
}
