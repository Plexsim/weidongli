<?php
/**
 * 语音回复处理类
 * 
 * [WeEngine System] 更多模块请浏览：BBS.b2ctui.com
 */
defined('IN_IA') or exit('Access Denied');

class bigpanModuleProcessor extends WeModuleProcessor {
	
	public $name = 'bigpanModuleProcessor';

	public function isNeedInitContext() {
		return 0;
	}
	
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$sql = "SELECT * FROM " . tablename('bigpan_reply') . " WHERE `rid`=:rid LIMIT 1";
		$row = pdo_fetch($sql, array(':rid' => $rid));
		if (empty($row['id'])) {
			return array();
		}
		$title = pdo_fetchcolumn("SELECT name FROM ".tablename('rule')." WHERE id = :rid LIMIT 1", array(':rid' => $rid));
		$response['FromUserName'] = $this->message['to'];
		$response['ToUserName'] = $this->message['from'];
		$response['MsgType'] = 'news';
		$response['ArticleCount'] = 1;
		$response['Articles'] = array();
		$response['Articles'][] = array(
			'Title' => $title,
			'Description' => $row['description'],
			'PicUrl' => $_W['attachurl'] . $row['picture'],
			'Url' => $_W['siteroot'] . create_url('mobile/module/lottery', array('name' => 'bigpan', 'id' => $rid,'state'=>'','weid'=>$_W['weid'])),
			'TagName' => 'item',
		);
		return $response;
	}

	public function isNeedSaveContext() {
		return false;
	}
}
