<?php
/**
 * 微商城模块定义
 *
 * @author 更多模块请浏览bbs.b2ctui.com
 * @url
 */

defined('IN_IA') or exit('Access Denied');


class Shopping2Module extends WeModule {
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		$setting = $_W['account']['modules'][$this->_saveing_params['mid']]['config'];
		include $this->template('rule');
	}
	
	public function fieldsFormSubmit($rid = 0) {
		global $_GPC, $_W;
		if (!empty($_GPC['title'])) {
			$data = array(
				'title' => $_GPC['title'],
				'description' => $_GPC['description'],
				'picurl' => $_GPC['thumb-old'],
				'url' => create_url('mobile/module/list', array('name' => 'shopping1', 'weid' => $_W['weid'])),
			);

			if (!empty($_GPC['thumb'])) {
				$data['picurl'] = $_GPC['thumb'];
				file_delete($_GPC['thumb-old']);
			}
			$this->saveSettings($data);
		}
		return true;
	}
}

