<?php
/**
 * 摇一摇抽奖模块
 *
 * [WeEngine System] 更多模块请浏览：BBS.b2ctui.com
 */
defined('IN_IA') or exit('Access Denied');

class YyyModuleSite extends WeModuleSite {

	public function doWebFormDisplay() {
		global $_W, $_GPC;
		$result = array('error' => 0, 'message' => '', 'content' => '');
		$result['content']['id'] = $GLOBALS['id'] = 'add-row-news-'.$_W['timestamp'];
		$result['content']['html'] = $this->template('item', TEMPLATE_FETCH);
		exit(json_encode($result));
	}

	public function doWebAwardlist() {
		global $_GPC, $_W;
		checklogin();
		$id = intval($_GPC['id']);
		if (checksubmit('delete')) {
			pdo_delete('yyy_winner', " id  IN  ('".implode("','", $_GPC['select'])."')");
			message('删除成功！', $this->createWebUrl('awardlist', array('id' => $id, 'page' => $_GPC['page'])));
		}
		if (!empty($_GPC['wid'])) {
			$wid = intval($_GPC['wid']);
			
			pdo_update('yyy_winner', array('status' => intval($_GPC['status'])), array('id' => $wid));
			message('标识领奖成功！', $this->createWebUrl('awardlist', array('id' => $id, 'page' => $_GPC['page'])));
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$where = '';
		$starttime = !empty($_GPC['start']) ? strtotime($_GPC['start']) : TIMESTAMP;
		$endtime = !empty($_GPC['start']) ? strtotime($_GPC['end']) : TIMESTAMP;
		if (!empty($starttime) && $starttime == $endtime) {
			$endtime = $endtime + 86400 - 1;
		}
		$condition = array(
			'isregister' => array(
				'',
				" AND b.realname <> ''",
				" AND b.realname = ''",
			),
			
			'qq' => " AND b.qq ='{$_GPC['profilevalue']}'",
			'mobile' => " AND b.mobile ='{$_GPC['profilevalue']}'",
			'realname' => " AND b.realname ='{$_GPC['profilevalue']}'",
			'starttime' => " AND a.createtime >= '$starttime'",
			'endtime' => " AND a.createtime <= '$endtime'",
		);
		if (!isset($_GPC['isregister'])) {
			$_GPC['isregister'] = 1;
		}
		$where .= $condition['isregister'][$_GPC['isregister']];
		
		if (!empty($_GPC['profile'])) {
			$where .= $condition[$_GPC['profile']];
		}
		if (!empty($_GPC['award'])) {
			$where .= $condition[$_GPC['award']];
		}
		if (!empty($starttime)) {
			$where .= $condition['starttime'];
		}
		if (!empty($endtime)) {
			$where .= $condition['endtime'];
		}
		$sql = "SELECT a.id,a.status,   a.createtime, b.realname, b.mobile, b.qq FROM ".tablename('yyy_winner')." AS a
				LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE a.rid = '$id'  $where ORDER BY a.createtime DESC LIMIT ".($pindex - 1) * $psize.",{$psize}";
		$list = pdo_fetchall($sql);
		if (!empty($list)) {
			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('yyy_winner')." AS a
				LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE a.rid = '$id' $where");
			$pager = pagination($total, $pindex, $psize);
		}
		include $this->template('awardlist');
	}

	public function doWebDelete() {
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$sql = "SELECT id FROM " . tablename('yyy_award') . " WHERE `id`=:id";
		$row = pdo_fetch($sql, array(':id'=>$id));
		if (empty($row)) {
			message('抱歉，奖品不存在或是已经被删除！', '', 'error');
		}
		if (pdo_delete('yyy_award', array('id' => $id))) {
			message('删除奖品成功', '', 'success');
		}
	}

	public function getCovers() {
		return array(
			array('title' => '第一期摇一摇', 'url' => $this->createWebUrl('first')),
		);
	}

	public function getHomeTiles() {
		global $_W;
		$urls = array();
		$list = pdo_fetchall("SELECT name, id FROM ".tablename('rule')." WHERE weid = '{$_W['weid']}' AND module = 'yyy'");
		if (!empty($list)) {
			foreach ($list as $row) {
				$urls[] = array('title'=>$row['name'], 'url'=> $this->createMobileUrl('lottery', array('id' => $row['id'])));
			}
		}
		return $urls;
	}

	public function doMobileYyy() {
		$useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
		if(strpos($useragent, 'MicroMessenger') === false && strpos($useragent, 'Windows Phone') === false ){
			//echo "404";
			//exit;
		}
		global $_GPC, $_W;
		$title = '摇一摇抽奖';
		if (empty($_W['fans']['from_user'])) {
			message('非法访问，请重新发送消息进入摇一摇页面！');
		}
		$fromuser = $_W['fans']['from_user'];
		$profile = fans_require($fromuser, array('realname', 'mobile', 'qq'), '需要完善资料后才能摇一摇.');
		
		$id = intval($_GPC['id']);
		$yyy = pdo_fetch("SELECT id, rule,starttime,description,shakespace,shakestrong,shaketimes,ruletype FROM ".tablename('yyy_reply')." WHERE rid = '$id' LIMIT 1");
		
		if (empty($yyy)) {
			message('非法访问，请重新发送消息进入摇一摇页面！');
		}
		
		if ($yyy['starttime']>time()){
			$yyy['lefttime']=$yyy['starttime']-time();
		
		}else{
			$yyy['lefttime']=3;
		
		}
		
		
		$totay=strtotime(date('y-m-d',time()));
		$sql="SELECT count FROM ".tablename('yyy_winner')." WHERE  from_user = '$fromuser' AND createtime >$totay AND status=2 ";
		$isaward = pdo_fetchcolumn($sql);
		if (intval($isaward)) {
			message('你已经中奖过了,不能再玩了哦');
		}
		
		$sql="SELECT count FROM ".tablename('yyy_winner')." WHERE  from_user = '$fromuser' AND rid = '$id' ";
		$total = pdo_fetchcolumn($sql);
		$total=intval($total);
		//$member = fans_search($fromuser);
		$ruletype=intval($yyy['ruletype']);
		if ($ruletype){
			//include $this->template('yyys');
			include $this->template('yyyc');
		}else{
			include $this->template('yyyc');
		}
	}
	
	public function doMobilePostJson() {
		global $_GPC, $_W;
		if (empty($_W['fans']['from_user'])) {
			message('非法访问，请重新发送消息进入摇一摇页面！');
		}
		$fromuser = $_W['fans']['from_user'];
		$id = intval($_GPC['id']);
		$yyy = pdo_fetch("SELECT id FROM ".tablename('yyy_reply')." WHERE rid = '$id' LIMIT 1");
		if (empty($yyy)) {
			message('非法访问，请重新发送消息进入摇一摇页面！');
		}
		$count=$_GPC['ucount'];
		$data=array('rid'=>$id,'from_user'=>$fromuser,'count'=>$count,'createtime'=>$_W['timestamp'],'endtime'=>$_W['timestamp']);
		
		
		if ($count==0){
			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('yyy_winner')." WHERE  from_user = '$fromuser'  AND rid = '$id'");
			if (!intval($total)){
				pdo_insert('yyy_winner', $data);
			}
		}else{
		
			$updata =" count= $count  , endtime=".$_W['timestamp'] ;
			$sql="UPDATE ".tablename('yyy_winner')." SET $updata  WHERE from_user='$fromuser' AND rid = '$id' limit 1";
			pdo_query($sql);
		}
		
		message($count, '');
	}
	
	
	public function doWebBigscreen() {
		global $_GPC, $_W;
		
		checklogin();
		$id = intval($_GPC['id']);
		$reply = pdo_fetch("SELECT * FROM ".tablename('yyy_reply')." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));
		
		
		if (intval($reply['starttime'])>10){
			$reply['lefttime']=intval($reply['starttime'])-time()-2;
		
		}else{
			$reply['lefttime']=3;
		
		}
		$reply['keyword']=pdo_fetchcolumn("SELECT content FROM ".tablename('rule_keyword')." WHERE rid = '{$reply['rid']}'");
		$sql = "SELECT a.id, a.createtime, b.realname, b.mobile, b.qq ,(a.endtime -a.createtime  ) as usertime  FROM ".tablename('yyy_winner')." AS a
				LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE a.rid = '$id' and b.weid = '{$_W['weid']}'  $where ORDER BY count,a.createtime DESC LIMIT 10";
		
		$list = pdo_fetchall($sql);
		
		include $this->template('screen');
	}
	
	public function doWebScreenJson() {
		global $_GPC, $_W;
		
		
		$id = intval($_GPC['id']);
		
		$sql = "SELECT a.count,b.realname, (a.endtime -a.createtime  ) as usertime  FROM ".tablename('yyy_winner')." AS a
				LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE a.rid = '$id' and b.weid = '{$_W['weid']}' $where ORDER BY a.count DESC, a.endtime ASC LIMIT 10";
		
		$list = pdo_fetchall($sql);
		
		$templist=array(
				'count' => 0,
				
				'realname' => '-',
				'usertime' => 0,
			);
		
		$list_nums=count($list);
		while($list_nums<10)
		{
			$list[]=$templist;
			$list_nums++;
		}
		
		
		echo json_encode($list);
		
		
	}
	

	public function doMobileRegister() {
		global $_GPC, $_W;
		$title = '摇一摇领奖登记个人信息';
		if (!empty($_GPC['submit'])) {
			if (empty($_W['fans']['from_user'])) {
				message('非法访问，请重新发送消息进入摇一摇页面！');
			}
			$data = array(
				'realname' => $_GPC['realname'],
				'mobile' => $_GPC['mobile'],
				'qq' => $_GPC['qq'],
			);
			if (empty($data['realname'])) {
				die('<script>alert("请填写您的真实姓名！");location.reload();</script>');
			}
			if (empty($data['mobile'])) {
				die('<script>alert("请填写您的手机号码！");location.reload();</script>');
			}
			fans_update($_W['fans']['from_user'], $data);
			die('<script>alert("登记成功！");location.href = "'.$this->createMobileUrl('lottery', array('id' => $_GPC['id'])).'";</script>');
		}
		include $this->template('register');
	}

}
