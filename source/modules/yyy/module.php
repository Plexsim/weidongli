<?php
/**
 * 摇一摇抽奖模块
 *
 * [WeEngine System] 更多模块请浏览：BBS.b2ctui.com
 */
defined('IN_IA') or exit('Access Denied');

class YyyModule extends WeModule {
	public $tablename = 'yyy_reply';

	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			
		} else {
			$reply = array(
				'shaketimes' => 60,
				'shakespace' => 100,
				'shakestrong' => 3000,
				'ruletype'=>1
			);
		}
		
		if(intval($reply['starttime'])==0){
			$reply['starttime']=time();
			
		}
		
		if(intval($reply['endtime'])<intval($reply['starttime'])){
			$reply['endtime']=intval($reply['starttime']) + 3600*24;
			
		}
		if(intval($reply['shaketimes'])<5){
			$reply['shaketimes']=60;
			
		}
		if(intval($reply['shakespace'])<10){
			$reply['shakespace']=100;
			
		}
		if(intval($reply['shakestrong'])<10){
			$reply['shakestrong']=3000;
			
		}
		if(intval($reply['ruletype'])>0){
			$reply['ruletype']=1;
			
		}
		
		include $this->template('form');
	}

	public function fieldsFormValidate($rid = 0) {
		return true;
	}

	public function fieldsFormSubmit($rid = 0) {
		global $_GPC, $_W;
		$id = intval($_GPC['reply_id']);
		$insert = array(
			'rid' => $rid,
			'picture' => $_GPC['picture'],
			'qrcode' => $_GPC['qrcode'],
			'screenpic' => $_GPC['screenpic'],
			'description' => $_GPC['description'],
			'shaketimes' => intval($_GPC['shaketimes']),
			'shakespace' => intval($_GPC['shakespace']),
			'shakestrong' => intval($_GPC['shakestrong']),
			'ruletype'=>intval($_GPC['ruletype']),
			'starttime' => strtotime($_GPC['starttime']),
			'rule' => htmlspecialchars_decode($_GPC['rule']),
			
		);
		//var_dump($insert);
		//exit;
		if (empty($id)) {
			pdo_insert($this->tablename, $insert);
		} else {
			if (!empty($_GPC['picture'])) {
				file_delete($_GPC['picture-old']);
			} else {
				unset($insert['picture']);
			}
			if (!empty($_GPC['screenpic'])) {
				file_delete($_GPC['screenpic-old']);
			} else {
				unset($insert['screenpic']);
			}
			if (!empty($_GPC['qrcode'])) {
				file_delete($_GPC['qrcode-old']);
			} else {
				unset($insert['qrcode']);
			}
			pdo_update($this->tablename, $insert, array('id' => $id));
		}
		
		
	}
	
	
	

	public function ruleDeleted($rid = 0) {
		global $_W;
		$replies = pdo_fetchall("SELECT id, picture,qrcode FROM ".tablename($this->tablename)." WHERE rid = '$rid'");
		$deleteid = array();
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				file_delete($row['picture']);
				file_delete($row['qrcode']);
				file_delete($row['screenpic']);
				$deleteid[] = $row['id'];
			}
		}
		pdo_delete($this->tablename, "id IN ('".implode("','", $deleteid)."')");
		return true;
	}
}
