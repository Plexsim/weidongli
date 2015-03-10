<?php
/**
 * 分享积赞模块定义
 *
 * @author 石头鱼
 * @url http://bbs.b2ctui.com
 */
defined('IN_IA') or exit('Access Denied');

class praiseModuleProcessor extends WeModuleProcessor {
	public $name = 'praiseModuleProcessor';
	public $table_reply = 'praise_reply';
	public $table_list   = 'praise_list';

	public function isNeedInitContext() {
		return 0;
	}
	
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$from= $this->message['from'];
		$tag = $this->message['content'];
		$weid = $_W['weid'];//当前公众号ID

				//$praiser = $this->check();
				$insert = array(
					'weid' => $weid,
				    'from_user' => $from,
					'praisetime' => time(),
				);
				//if(empty($praiser)){
				//pdo_insert($this->table_list, $insert);
				//}
				//推送分享图文内容
				$sql = "SELECT * FROM " . tablename($this->table_reply) . " WHERE `rid`=:rid LIMIT 1";
				$row = pdo_fetch($sql, array(':rid' => $rid));
					if (empty($row['id'])) {
						return array();
					}
					//查询是否被屏蔽
					$lists = pdo_fetch("SELECT status,praisenum FROM ".tablename($this->table_list)." WHERE from_user = '".$from."' and weid = '".$weid."' and rid= '".$rid."' limit 1" );
					if(!empty($lists)){//查询是否有记录
					  if($lists['status']==0){
						$message = "亲，".$row['title']."活动中您可能有作弊行为已被管理员暂停了！请联系".$_W['account']['name']."";
						return $this->respText($message);					
					  }					
					  
					}
					$now = time();
					if($now >= $row['start_time'] && $now <= $row['end_time']){						
						if ($row['status']==0){
						    $message = "亲，".$row['title']."活动暂停了！";
						    return $this->respText($message);
						}else{
						    return $this->respNews(array(
							    'Title' => $row['title'],
							    'Description' => htmlspecialchars_decode($row['description']).$zhongjiang,
							    'PicUrl' => $_W['attachurl'] . $row['picture'],
							    'Url' => $this->createMobileUrl('praise', array('id' => $rid, 'from_user' => base64_encode(authcode($this->message['from'], 'ENCODE')))),
						    ));
						}
					}else{
						$message = "亲，".$row['title']."活动没有开始或已结束了！";
						return $this->respText($message);				
					}
	}

	public function isNeedSaveContext() {
		return false;
	}
	private function check() {
		global $_W;
		$weid = $_W['weid'];//当前公众号ID
		$rid = $this->rule;
		$from= $this->message['from'];		
		$praiser = pdo_fetch("SELECT * FROM ".tablename($this->table_list)." WHERE from_user = '".$from."' and weid = '".$weid."' limit 1" );		
		return $praiser;

	}

}