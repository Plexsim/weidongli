<?php
/**
 * 砸蛋抽奖模块
 *
 * [WeEngine System] Copyright (c) 2013mms WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class bigpanModule extends WeModule {
	public $name = 'bigpanModule';
	public $title = '大转盘抽奖';
	public $ability = '';
	public $tablename = 'bigpan_reply';

	public function fieldsFormDisplay($rid = 0) {
		global $_W;
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			$award = pdo_fetchall("SELECT * FROM ".tablename('bigpan_award')." WHERE rid = :rid ORDER BY `id` ASC", array(':rid' => $rid));
			if (!empty($award)) {
				foreach ($award as &$pointer) {
					if (!empty($pointer['activation_code'])) {
						$pointer['activation_code'] = implode("\n", iunserializer($pointer['activation_code']));
					}
				}
			}
		} else {
			$reply = array(
				'maxlottery' => 1,
			);
		}
		include $this->template('bigpan/form');
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
			'zppic'=> $_GPC['picture2'],
			'description' => $_GPC['description'],
			'lostdeg' => $_GPC['lostdeg'],
			'prizedeg' => $_GPC['prizedeg'],
			'maxlottery' => intval($_GPC['maxlottery']),
			'rule' => $_GPC['rule'],
			'default_tips' => $_GPC['default_tips'],
			'hitcredit' => intval($_GPC['hitcredit']),
			'misscredit' => intval($_GPC['misscredit']),
		);
		if (empty($id)) {
			pdo_insert($this->tablename, $insert);
		} else {
			if (!empty($_GPC['picture'])) {
				file_delete($_GPC['picture-old']);
				
			} else {
				unset($insert['picture']);
				
			}
			if (!empty($_GPC['picture2'])) {
				
				file_delete($_GPC['zppic-old']);
			} else {
				
				unset($insert['zppic']);
			}
			
			pdo_update($this->tablename, $insert, array('id' => $id));
		}
		if (!empty($_GPC['award-title'])) {
			foreach ($_GPC['award-title'] as $index => $title) {
				if (empty($title)) {
					continue;
				}
				$update = array(
					'title' => $title,
					'description' => $_GPC['award-description'][$index],
					'probalilty' => $_GPC['award-probalilty'][$index],
					//'inkind' =>$_GPC['award-inkind'][$index],
					'total' => $_GPC['award-total'][$index],
					'activation_code' => '',
					'activation_url' => '',
					'level'=>$_GPC['award-level'][$index],
				);
				if (empty($update['inkind']) && !empty($_GPC['award-activation-code'][$index])) {
					$activationcode = explode("\n", $_GPC['award-activation-code'][$index]);
					$update['activation_code'] = iserializer($activationcode);
					$update['total'] = count($activationcode);
					
					$update['activation_url'] = $_GPC['award-activation-url'][$index];
				}
				pdo_update('bigpan_award', $update, array('id' => $index));
			}
		}
		//处理添加
		if (!empty($_GPC['award-title-new'])) {
			foreach ($_GPC['award-title-new'] as $index => $title) {
				if (empty($title)) {
					continue;
				}
				$insert = array(
					'rid' => $rid,
					'title' => $title,
					'description' => $_GPC['award-description-new'][$index],
					'probalilty' => $_GPC['award-probalilty-new'][$index],
					'inkind' => intval($_GPC['award-inkind-new'][$index]),
					'total' => intval($_GPC['award-total-new'][$index]),
					'level'=>intval($_GPC['award-level-new'][$index]),
					'activation_code' => '',
					'activation_url' => '',
				);

				if (empty($insert['inkind'])) {
					$activationcode = explode("\n", $_GPC['award-activation-code-new'][$index]);
					$insert['activation_code'] = iserializer($activationcode);
					$insert['total'] = count($activationcode);
					$insert['activation_url'] = $_GPC['award-activation-url-new'][$index];
				}
				pdo_insert('bigpan_award', $insert);
			}
		}
	}

	public function ruleDeleted($rid = 0) {
		global $_W;
		$replies = pdo_fetchall("SELECT id, thumb FROM ".tablename($this->tablename)." WHERE rid = '$rid'");
		$deleteid = array();
		if (!empty($replies)) {
			foreach ($replies as $index => $row) {
				file_delete($row['thumb']);
				$deleteid[] = $row['id'];
			}
		}
		pdo_delete($this->tablename, "id IN ('".implode("','", $deleteid)."')");
		return true;
	}
}