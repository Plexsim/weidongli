<?php
/**
 * 分享积赞模块定义
 *
 * @author 石头鱼
 * @url http://bbs.b2ctui.com
 */
defined('IN_IA') or exit('Access Denied');

class praiseModuleSite extends WeModuleSite {	
	
	public $table_reply = 'praise_reply';
	public $table_list   = 'praise_list';	
	public $table_data   = 'praise_data';

	public function doMobilelisthome() {
		//这个操作被定义用来呈现 微站首页导航图标
		$this->doMobilelistentry();	
	}
	
   
	
	public function doMobilepraise() {
		//分享集赞分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['weid'];//当前公众号ID
		$s = 0;
		$profile_s = 0;
		$rid = $_GPC['id'];
		$fromuser = $_W['fans']['from_user'];
		//取得分享人的数据
		$ztotal = 0;
		
      	if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));			
			$praisenum = $reply['praisenum'];
			
			if ($reply['status']==0) {
				$statpraisetitle = '<h1>活动暂停！请稍候再试！</h1>';
			}
			if (time()<$reply['start_time']) {//判断活动是否已经开始
				$statpraisetitle = '<h1>活动未开始！</h1>';
			}elseif (time()>$reply['end_time']) {//判断活动是否已经结束
				$statpraisetitle = '<h1>活动已结束！</h1>';
			}
 		}
		

		
		
		if(!empty($fromuser)) {
			$lists = pdo_fetch("SELECT id,status,praisenum FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid= '".$rid."' limit 1" );	
			if(!empty($lists)){
			   $uid= $lists['id'];
			   $ztotal = $lists['praisenum'];
			   if ($lists['status']==0){
				   $statpraisetitle = '<h1>因作弊行为被管理员屏蔽，请您联系 '.$_W['account']['name'].' 管理员!</h1>'; 
			   }
			}
		}
		//取得所有分享排名100
		$listshare = pdo_fetchall('SELECT a.*,b.realname,b.mobile FROM '.tablename($this->table_list).' as a left join '.tablename('fans').' as b on a.from_user=b.from_user  WHERE a.weid= :weid AND a.rid = :rid order by `praisenum` desc LIMIT 20', array(':weid' => $weid,':rid' => $rid));
		
		$count= pdo_fetch("SELECT count(id) as dd FROM ".tablename($this->table_list)." WHERE weid=".$weid." and rid= ".$rid." and praisenum > ".$ztotal."");

		$sharepm=$count['dd'];//排名
		$count = pdo_fetch("SELECT count(id) as dd FROM ".tablename($this->table_list)." WHERE weid=".$weid." AND rid= ".$rid."");
		$listtotal = $count['dd'];//总参与人数
		//取得分享集赞数据
		
		if(!empty($uid)) {
		    $list = pdo_fetchall('SELECT * FROM '.tablename($this->table_data).'  WHERE weid= :weid and uid = :uid and rid = :rid order by `praisetime` desc LIMIT 20', array(':weid' => $_W['weid'], ':uid' => $uid, ':rid' => $rid) );		
		}	
		//整理数据进行页面显示
		//判断是否绑定
		$profile = fans_search($fromuser, array('follow', 'realname', 'mobile'));
		if (!empty($profile['realname']) AND !empty($profile['mobile']) AND $profile['follow']==1) {
			$profile_s=1;
		}
		//判断是否绑定
		$imgurl=$_W['attachurl'] . $reply['picture'];
      	$title = $reply['title'];
		$regurl=$this->createMobileUrl('regpraise', array('fromuser' => $fromuser));
		$staturl=$_W['siteroot'].$this->createMobileUrl('statpraise', array('rid' => $rid,'fromuser' => $fromuser));

		
		include $this->template('praise');

	}

	public function doMobileRegpraise() {
		//分享集赞分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['weid'];//当前公众号ID
		$rid  = $_GPC['rid'];//当前规则ID		
		$fromuser = $_GPC['fromuser'];
		//查询用户是否为关注用户
		if(!empty($fromuser)) {
		    $profile  = fans_search($fromuser, array('follow'));
		}else{
		    $result = "您访问的分享异常,请联系公众号技术人员！";
			echo $result;
			exit;
		}
		
		//取得分享集赞数据
		if(!empty($fromuser)) {
			//关注用户　注册转发
			$rs = pdo_fetch("SELECT id FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid = '".$rid."' limit 1" );			

			if(empty($rs['id'])){			
					fans_update($fromuser, array(
					'realname' => $_GPC['realname'],
					'mobile' => $_GPC['mobile'],
				    ));
					$result='注册信息提交成功，立即分享吧！';
			}else{
					$result='您已注册过信息，可直接分享！';
			}			
		}
		echo $result;	
	}
	
	
	
	public function doMobilestatpraise() {
	    //分享集赞分享页面显示。
		global $_GPC,$_W;
		$weid = $_W['weid'];//当前公众号ID
		$fromuser =  $_GPC['fromuser'];
		$from_user = $_W['fans']['from_user'];
		$rid  = $_GPC['rid'];//当前规则ID

		$urlcookie = $_W['siteroot'].$this->createMobileUrl('statpraiseshow', array('rid' => $rid,'fromuser' => $fromuser,'from_user' => $from_user,'nickname' => $nickname,'headimgurl' => $headimgurl));
	    header("location:$urlcookie");

	}

	public function doMobilestatpraiseshow() {
	    //分享集赞分享页面显示。
		global $_GPC,$_W;
		$weid      = $_W['weid'];//当前公众号ID
		$fromuser  = $_GPC['fromuser'];
		$from_user = $_GPC['from_user'];
		$rid       = $_GPC['rid'];//当前规则ID
		$headimgurl= $_GPC['headimgurl'];//用户头像
		$nickname  = $_GPC['nickname'];//用户昵称

		if(empty($rid)){
		    echo '<h1>分享rid出错，请联系管理员!</h1>';
			exit;
		}
		if(empty($fromuser)){
		    echo '<h1>分享人出错，请联系管理员!</h1>';
			exit;
		}
		//查询分享活动规则	查询是否开始或暂停
      	if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
			
			if ($reply['status']==0) {
				$statpraisetitle = '<h1>活动暂停！请稍候再试！</h1>';
			}
			if (time()<$reply['start_time']) {//判断活动是否已经开始
				$statpraisetitle = '<h1>活动未开始！</h1>';
			}elseif (time()>$reply['end_time']) {//判断活动是否已经结束
				$statpraisetitle = '<h1>活动已结束！</h1>';
			}
			
 		}
				
		
		//取得分享人的数据查询是否屏蔽
		if(!empty($fromuser)) {
			$lists = pdo_fetch('SELECT id,status,praisenum FROM '.tablename($this->table_list).' WHERE from_user = :fromuser and weid = :weid and rid = :rid limit 1', array(':fromuser' => $fromuser,':weid' => $weid,':rid' => $rid));
			if(!empty($lists)){
			   $uid= $lists['id'];
			   $ztotal = $lists['praisenum'];
			   if ($lists['status']==0){
				   $statpraisetitle = '<h1>因作弊行为被管理员屏蔽，请告知您的朋友联系 '.$_W['account']['name'].' 管理员!</h1>'; 
			   }
			}else{
			   $uid= 0;
			   $ztotal = 0;
			}
		}
		//取得所有分享排名100
		$listshare = pdo_fetchall('SELECT a.*,b.realname,b.mobile FROM '.tablename($this->table_list).' as a left join '.tablename('fans').' as b on a.from_user=b.from_user  WHERE a.weid= :weid AND a.rid = :rid order by `praisenum` desc LIMIT 20', array(':weid' => $weid,':rid' => $rid));
		$count= pdo_fetch('SELECT count(id) as dd FROM '.tablename($this->table_list).' WHERE weid=:weid and rid= :rid and praisenum > :praisenum', array(':weid' => $weid,':rid' => $rid,':praisenum' => $ztotal));
		$sharepm=$count['dd'];//排名
		$count = pdo_fetch('SELECT count(id) as dd FROM '.tablename($this->table_list).' WHERE weid=:weid and rid= :rid', array(':weid' => $weid,':rid' => $rid));
		$listtotal = $count['dd'];//总参与人数
		//取得分享集赞数据
		if(!empty($uid)) {
		    $list = pdo_fetchall('SELECT * FROM '.tablename($this->table_data).'  WHERE weid= :weid and uid = :uid and rid = :rid order by `praisetime` desc LIMIT 20', array(':weid' => $_W['weid'], ':uid' => $uid, ':rid' => $rid) );			
		}		
		//整理数据进行页面显示
		//判断是否为关注用户
		$profiles = fans_search($fromuser, array('realname', 'mobile'));
		if (empty($profiles['realname'])){
			$statpraisetitle="没有此用户!";
		}
		$profile  = fans_search($from_user, array('realname', 'mobile'));
		//判断是否为关注用户
		$imgurl=$_W['attachurl'] . $reply['picture'];
      	$title = $reply['title'];
		$regurl=$this->createMobileUrl('statupdata');
		$staturl=$_W['siteroot'].$this->createMobileUrl('statpraise', array('rid' => $rid,'fromuser' => $fromuser));
		$jumpurl = $reply['praiseurl'];
		
			include $this->template('statpraise');

	}

	public function doMobileStat() {
		//分享集赞分享页面显示。
		global $_GPC,$_W;
        $result = array('status' => 0, 'message' => '', 'total' => 0, 'dataid' => 0);
		$operation = $_GPC['op'];
		$rid = intval($_GPC['rid']);
		$fromuser =  $_GPC['fromuser'];
		$from_user = $_GPC['from_user'];
		$headimgurl= $_GPC['headimgurl'];//用户头像
		$nickname  = $_GPC['nickname'];//用户昵称
		$weid = $_W['weid'];//当前公众号ID	
		$praiseip = getip();
		$now = time();		
		if(!empty($rid)) {
		  $reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
		  $jumpurl = $reply['praiseurl'];
		  $staturl=$_W['siteroot'].$this->createMobileUrl('stat', array('rid' => $rid,'fromuser' => $fromuser));
		  
		}
		//未关注用户用cookie作为唯一值		
		if(empty($from_user)) {			
			$from_user=$now;
		}
		//分享人和查看人为同一人时，不参与加分直接跳转
		if($from_user==$fromuser){
		$result['message'] = '自己不能给自己点赞呀！';
        message($result, '', 'ajax');
		}else{
		    //取得分享集赞数据
		    if(!empty($fromuser)) {
		 	  $list = pdo_fetch("SELECT * FROM ".tablename($this->table_list)." WHERE from_user = '".$fromuser."' and weid = '".$weid."' and rid = '".$rid."' limit 1" );		
		    }else{
		       $result['message'] = '系统出错，求赞人未知';
               message($result, '', 'ajax');
		    }
				if(!empty($list)){
					$praiseid = $list['id'];
					//取得分享详细数据，判断浏览者是否是同一人24小时内同一IP访问
						$praise_data = pdo_fetch("SELECT * FROM ".tablename($this->table_data)." WHERE uid = '".$praiseid."' and rid = '".$rid."' and weid = '".$weid."' and praiseip= '".$praiseip."' limit 1" );	
					
					if(!empty($praise_data)){
						$sid		=	$praise_data['id'];
						$praisetime	=	$praise_data['praisetime'];
						$updatetime	=	$now-$praisetime;
						//访问如果是在24小时后，更新分享数据，更新分享数
						if($updatetime >= (24*3600)){
							$zannum = $list['praisenum']+1;
							$updatedata = array(
								'viewnum'   => $praise_data['viewnum']+1,
								'praisetime' => $now								
								);	
							$updatelist = array(
								'praisenum' => $list['praisenum']+1,
								'praisetime' => $now
								);							
							pdo_update($this->table_data,$updatedata,array('id' => $sid));
							$dataid = $sid;//取分享点赞人的id
							pdo_update($this->table_list,$updatelist,array('id' => $praiseid));							
							
						}else{
							//转化为时间
							$num = ((24*3600)-$updatetime);
							$hour = floor($num/3600);
  							$minute = floor(($num-3600*$hour)/60);
  							$second = floor((($num-3600*$hour)-60*$minute)%60);
  							$num = $hour.'小时'.$minute.'分'.$second.'秒';

						    $zannum = $list['praisenum'];
							$result['status'] = 0;
							$result['message'] = '您已点过赞了，请于'.$num.'后再来投赞吧!';
							$result['total'] = $zannum;
							message($result, '', 'ajax');
						}
					}else{
							$zannum = $list['praisenum']+1;							
							$insertdata = array(
								'weid'      => $weid,
								'from_user' => $from_user,
								'avatar'    => $headimgurl,
								'realname'  => $nickname,
								'rid'       => $rid,
								'uid'       => $praiseid,
								'praiseip'	=> $praiseip,
								'praisetime'=> $now
								);	
							$updatelist = array(
								'praisenum' => $list['praisenum']+1,
								'praisetime' => $now
								);	
							pdo_insert($this->table_data, $insertdata);
							$dataid = pdo_insertid();//取分享点赞人的id
							pdo_update($this->table_list,$updatelist,array('id' => $praiseid));							
					}
				}else{					
					//添加分享集赞记录
					$zannum = 1;
					$insertlistdata = array(
						'weid'      => $weid,
						'from_user' => $fromuser,
						'rid'       => $rid,
						'praisenum'  => 1,
						'praisetime' => $now
					);
					pdo_insert($this->table_list, $insertlistdata);
					$uid = pdo_insertid();//取分享集赞记录id号
					//添加分享集赞记录
					//添加分享记录
					$insertdata = array(
						'weid'      => $weid,
						'from_user' => $from_user,
						'avatar'    => $headimgurl,
						'realname'  => $nickname,
						'rid'       => $rid,
						'uid'       => $uid,
						'praiseip'	=> $praiseip,
						'praisetime' => $now
					);
					pdo_insert($this->table_data, $insertdata);
					$dataid = pdo_insertid();//取分享点赞人的id					
				}			
		        $result['status'] = 1;
		        $result['message'] = '点赞成功！';
		        $result['total'] = $zannum;
				$result['dataid'] = $dataid;
		        message($result, '', 'ajax');
		}
	}

	
	
}