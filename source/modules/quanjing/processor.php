<?php
/**
 * 3D展示模块处理程序
 *
 * @author hy
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class QuanjingModuleProcessor extends WeModuleProcessor {
	public $name = 'QuanjingModuleProcessor';

	public function isNeedInitContext() {
		return 0;
	}
	
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$sql = "SELECT * FROM " . tablename('3d_reply') . " WHERE `rid`=:rid LIMIT 1";
		$row = pdo_fetch($sql, array(':rid' => $rid));
		$sql_r = "SELECT * FROM " . tablename('rule_keyword') . " WHERE `rid`=:rid LIMIT 1";
		$row_r = pdo_fetch($sql_r, array(':rid' => $rid));
		$content = trim($this->message['content']);
		if ($content == $row_r['content']) {
			$response['FromUserName'] = $this->message['to'];
			$response['ToUserName'] = $this->message['from'];
			$response['MsgType'] = 'news';
			$response['ArticleCount'] = 1;
			$response['Articles'] = array();
			$response['Articles'][] = array(
				'Title' => $row['title'],
				'Description' => $row['description'],
				'PicUrl' => $_W['attachurl'] . $row['thumb'],
				'Url' => $_W['siteroot'] .create_url('index/module', array('do' => 'userlist', 'name' => 'quanjing', 'weid'=>$_W['weid'])),
				'TagName' => 'item',
			);
		}
		return $response;
	}
	public function isNeedSaveContext() {
		return false;
	}
}