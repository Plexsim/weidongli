<?php
/**
 * 幸运大抽奖
 * 作者:迷失卍国度
 * qq : 15595755
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class ilotteryv1ModuleProcessor extends WeModuleProcessor {
	
	public $name = 'ilotteryv1ModuleProcessor';

	public function isNeedInitContext() {
		return 0;
	}
	
	public function respond() {
		global $_W;
        $rid = $this->rule;
        $sql = "SELECT * FROM " . tablename('ilotteryv1_reply') . " WHERE `rid`=:rid LIMIT 1";
        $row = pdo_fetch($sql, array(':rid' => $rid));
        $title = pdo_fetchcolumn("SELECT name FROM ".tablename('rule')." WHERE id = :rid LIMIT 1", array(':rid' => $rid));
        $response['FromUserName'] = $this->message['to'];
        $response['ToUserName'] = $this->message['from'];
        $response['MsgType'] = 'news';
        $response['ArticleCount'] = 1;
        $response['Articles'] = array();
        $response['Articles'][] = array(
            'Title' => $title,
            'Description' => $row['description'],
            'PicUrl' =>empty($row['picture'])?'':($_W['attachurl'] . $row['picture']),
            'Url' => $_W['siteroot'] . create_url('mobile/module', array('do' => 'userinfo', 'name' => 'ilotteryv1', 'weid' => $row['weid'], 'rid' => $row['rid'], 'from_user' => base64_encode(authcode($this->message['from'], 'ENCODE')))),
            'TagName' => 'item',
        );
        return $response;
	}

	public function isNeedSaveContext() {
		return false;
	}
}
