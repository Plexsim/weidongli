<?php
/**
 * 照片墙模块定义
 *
 * @author 珊瑚海
 * @url http://www.vfanm.com/
 */
defined('IN_IA') or exit('Access Denied');

class PhotosModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_W,$_GPC;
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			$cfg = array(
				'picnum' => intval($_GPC['picnum']),
				'isdes' => intval($_GPC['isdes']),
				'isstatus' => intval($_GPC['isstatus']),
				'topinfo'=> $_GPC['topinfo'],
			);
			if($this->saveSettings($cfg)) {
				message('保存成功', 'refresh');
			}
		}
		//这里来展示设置项表单
		if(!isset($settings['picnum'])) {
			$settings['picnum'] = '5';
		}
		include $this->template('settings');
	}

}