<?php
/**
 * 砸蛋抽奖模块
 *
 * [WeEngine System] 更多模块请浏览：BBS.b2ctui.com
 */
defined('IN_IA') or exit('Access Denied');

class LxybigpanModuleSite extends WeModuleSite {
	public function getProfileTiles() {

	}

	public function getHomeTiles() {
	}

	public function doMobileLottery() {	
		
		global $_W, $_GPC;
		$title = '大转盘抽奖';
		$fromuser = authcode(base64_decode($_GPC['from_user']), 'DECODE');
		if (empty($fromuser)) {
			exit('非法参数！');
		}
		$id = intval($_GPC['id']);
		//$pan 存储图文首页
        $pan = pdo_fetch("SELECT * FROM ".tablename('lxy_bigpan_reply')." WHERE rid = '$id' LIMIT 1");
        
        if($pan['zppic']!='')
        {
            $pan['zppic'] =$_W['attachurl'].$pan['zppic'];
        }
        

		if (empty($pan)) {
			exit('非法参数！');
		}
 		
        //取得粉丝表中的本人信息
        $member = pdo_fetch("SELECT * FROM ".tablename('fans')." WHERE from_user = '{$fromuser}'");
        
        if($member['realname']==''||$member['qq']=='' ||$member['mobile']=='')
        {
        include $this->template('register');    
        }
		else
        {  
        //$total存储我的中奖数量
         $total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('lxy_bigpan_winner')." WHERE createtime > '".strtotime(date('Y-m-d'))."' AND rid = '{$id}' AND from_user = '$fromuser' AND status <> 3" );
     
 
         //$myward存储我的中奖信息
		 $myaward = pdo_fetchall("SELECT award, description FROM ".tablename('lxy_bigpan_winner')." WHERE from_user = '{$fromuser}' AND award <> '' AND rid = '{$id}' ORDER BY createtime DESC");
		    
         // 复杂显示模式，匹配 lottery_v_source.html
        /*
         $sql = "SELECT a.award, b.realname FROM ".tablename('bigpan_winner')." AS a
				    LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE b.mobile <> '' AND b.realname <> '' AND
				    a.from_user <> '{$fromuser}' AND a.award <> '' AND a.rid = '$id' ORDER BY a.createtime DESC LIMIT 20";
		 $otheraward = pdo_fetchall($sql);
         */
		    
         //$lottery 存储奖品信息
		// $lottery = pdo_fetchall("SELECT * FROM ".tablename('bigpan_award')." WHERE rid = '{$id}' ORDER BY probalilty ASC");
		 include $this->template('lottery');     
        }
	}

}
