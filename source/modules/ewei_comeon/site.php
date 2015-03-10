<?php

/**
 * 来吧来吧
 *
 */
defined('IN_IA') or exit('Access Denied');

class Ewei_comeonModuleSite extends WeModuleSite {

    public $weid;
    public function __construct() {
        $this->weid = IMS_VERSION<0.6?$_W['weid']:$_W['uniacid'];
    }
    public function is_follow(){
        global $_W;
    
        $from_user = $_W['fans']['from_user'];
   
        if(empty($from_user)){
            return false;
        }
        else{
            if(IMS_VERSION<0.6){
                $fans = fans_search( $from_user ,array("follow"));
            }
            else{
                $fans = pdo_fetch("select follow from ".tablename('mc_mapping_fans')." where openid=:openid limit 1",array(":openid"=>$from_user));
            }
            if(empty($fans)){
                return false;
            }
            if(empty($fans['follow'])){
                return false;
            }
        }
        return true;
    }
    public function doWebManage() {
        
        global $_GPC, $_W;
   
        $pindex = max(1, intval($_GPC['page']));
        $psize = 20;
        $sql =IMS_VERSION>=0.6?"uniacid = :weid AND `module` = :module":"weid = :weid AND `module` = :module";
        $params = array();
        $params[':weid'] = $this->weid;
        $params[':module'] = 'ewei_comeon';
        if(IMS_VERSION>=0.6){
                 load()->model('reply');
                 load()->func('tpl');
        }
        
        if (isset($_GPC['keywords'])) {
            $sql .= ' AND `name` LIKE :keywords';
            $params[':keywords'] = "%{$_GPC['keywords']}%";
        }
        $list =array();
   
        if(IMS_VERSION>=0.6){
            $list = reply_search($sql, $params, $pindex, $psize, $total);
        }
        else{
        	include model('rule');
            $list = rule_search($sql, $params, $pindex, $psize, $total);    
        }
     
       $pager = pagination($total, $pindex, $psize);
       
        if (!empty($list)) {
            foreach ($list as &$item) {
                $condition = "`rid`={$item['id']}";
                 if(IMS_VERSION>=0.6){
                   $item['keywords'] = reply_keywords_search($condition);
                }
                else{
                    $item['keywords'] = rule_keywords_search($condition);    
                }
           
                $comeon = pdo_fetch("SELECT fansnum, viewnum,starttime,endtime,isshow FROM " . tablename('ewei_comeon_reply') . " WHERE rid = :rid " , array(':rid' => $item['id']));
                $item['fansnum'] = $comeon['fansnum'];
                $item['viewnum'] = $comeon['viewnum'];
                $item['starttime'] = date('Y-m-d H:i', $comeon['starttime']);
                $endtime = $comeon['endtime'] + 86399;
                $item['endtime'] = date('Y-m-d H:i', $endtime);
                $nowtime = time();
                if ($comeon['starttime'] > $nowtime) {
                    $item['status'] = "<span class=\"label label-warning\">未开始</span>";
                    $item['show'] = 1;
                } elseif ($endtime < $nowtime) {
                    $item['status'] = "<span class=\"label label-default\">已结束</span>";
                    $item['show'] = 0;
                } else {
                    if ($comeon['isshow'] == 1) {
                        $item['status'] = "<span class=\"label label-success\">已开始</span>";
                        $item['show'] = 2;
                    } else {
                        $item['status'] = "<span class=\"label \">已暂停</span>";
                        $item['show'] = 1;
                    }
                }
                $item['isshow'] = $comeon['isshow'];
            }
            unset($item);
        }
        $version = IMS_VERSION<0.6?'':'_advance';
        include $this->template('manage'.$version);
    }

    public function doWebdelete() {
        global $_GPC, $_W;
        $rid = intval($_GPC['rid']);
        $field = IMS_VERSION>=0.6?'uniacid':'weid';
        $rule = pdo_fetch("SELECT id, module FROM " . tablename('rule') . " WHERE id = :id and {$field}=:weid", array(':id' => $rid, ':weid' => $this->weid));
        if (empty($rule)) {
            message('抱歉，要修改的规则不存在或是已经被删除！');
        }
        if (pdo_delete('rule', array('id' => $rid))) {
            pdo_delete('rule_keyword', array('rid' => $rid));
            //删除统计相关数据
            pdo_delete('stat_rule', array('rid' => $rid));
            pdo_delete('stat_keyword', array('rid' => $rid));
            //调用模块中的删除
            $module = WeUtility::createModule($rule['module']);
            if (method_exists($module, 'ruleDeleted')) {
                $module->ruleDeleted($rid);
            }
        }


        message('规则操作成功！', $this->createWebUrl('manage',array('name'=>'ewei_comeon')), 'success');
    }

    public function doWebdeleteAll() {
        global $_GPC, $_W;

        foreach ($_GPC['idArr'] as $k => $rid) {
            $rid = intval($rid);
            if ($rid == 0)
                continue;
            $rule = pdo_fetch("SELECT id, module FROM " . tablename('rule') . " WHERE id = :id and weid=:weid", array(':id' => $rid, ':weid' => $this->weid));
            if (empty($rule)) {
                $this->webmessage('抱歉，要修改的规则不存在或是已经被删除！');
            }
            if (pdo_delete('rule', array('id' => $rid))) {
                pdo_delete('rule_keyword', array('rid' => $rid));
                //删除统计相关数据
                pdo_delete('stat_rule', array('rid' => $rid));
                pdo_delete('stat_keyword', array('rid' => $rid));
                //调用模块中的删除
                $module = WeUtility::createModule($rule['module']);
                if (method_exists($module, 'ruleDeleted')) {
                    $module->ruleDeleted($rid);
                }
            }
        }
        $this->webmessage('规则操作成功！', '', 0);
    }

    public function doWebfanslist() {
        global $_GPC, $_W;
        $rid = intval($_GPC['rid']);
        if (empty($rid)) {
            message('抱歉，传递的参数错误！', '', 'error');
        }
        $where = '';
        $params = array(':rid' => $rid);
        if ($_GPC['status']!='') {
            $where.=' and status=:status';
            $params[':status'] = intval($_GPC['status']);
        }
        if (!empty($_GPC['keywords'])) {
            $where.=' and mobile<>\'\' and mobile like :mobile';
            $params[':mobile'] = "%{$_GPC['keywords']}%";
        }
        else{
           $where.=" and mobile<>:mobile";    
           $params[':mobile'] = "";
        }
        

        $total = pdo_fetchcolumn("SELECT count(*) FROM " . tablename('ewei_comeon_fans') . " WHERE rid = :rid " . $where . "", $params);
        $pindex = max(1, intval($_GPC['page']));
        $psize = 12;
        $pager = pagination($total, $pindex, $psize);
        $start = ($pindex - 1) * $psize;
        $limit .= " LIMIT {$start},{$psize}";
        $list = pdo_fetchall("SELECT * FROM " . tablename('ewei_comeon_fans') . " WHERE rid = :rid " . $where . " ORDER BY points DESC " . $limit, $params);
 
        $awards = pdo_fetchall("select * from ".tablename('ewei_comeon_award')." where rid=:rid ",array(":rid"=>$rid));
        foreach($list as &$row){
            $awardnames = array();    
            foreach($awards as $award){
                if($row['points']>=$award['point']){
                      $awardnames[] = $award;
                }
            } 
           $row['awardnames'] = $awardnames;
           
            if(!empty($row['awardid'])){
                $row['awardname'] = pdo_fetchcolumn("select name from ".tablename('ewei_comeon_award')." where id=:id limit 1 ",array(":id"=>$row['awardid']));
            }
        }
        unset($row);
        
        
        $gifts = array();
   
        $version = IMS_VERSION<0.6?'':'_advance';
        include $this->template('fanslist'.$version);
    }

    public function doWebdownload() {
           require_once 'download.php';
    }

    public function doWebsetshow() {
        global $_GPC, $_W;
        $rid = intval($_GPC['rid']);
        $isshow = intval($_GPC['isshow']);

        if (empty($rid)) {
            message('抱歉，传递的参数错误！', '', 'error');
        }
        $temp = pdo_update('ewei_comeon_reply', array('isshow' => $isshow), array('rid' => $rid));
        message('状态设置成功！', $this->createWebUrl('manage',array('name'=>'ewei_comeon')), 'success');
    }

    public function doWebsetstatus() {
        global $_GPC, $_W;
        $id = intval($_GPC['id']);
        $status = intval($_GPC['status']);
        if (empty($id)) {
            message('抱歉，传递的参数错误！', '', 'error');
        }
        $p = array('status' => $status);
        if ($status == 2) {
            $p['awardtime'] = time();
        }
        $temp = pdo_update('ewei_comeon_award', $p, array('id' => $id, 'weid' => $this->weid));
        if ($temp == false) {
            message('抱歉，刚才操作数据失败！', '', 'error');
        } else {
            message('状态设置成功！', $this->createWebUrl('awardlist',array('name'=>'ewei_comeon','rid' => $_GPC['rid'])), 'success');
        }
    }
 
    public function doWebPoints(){
        
        global $_GPC, $_W;
        $rid = intval($_GPC['rid']);
        $fansid = intval($_GPC['fansid']);
        $points = floatval($_GPC['points']);
        pdo_update('ewei_comeon_fans', array('points'=>$points) , array('rid' => $rid, 'id' => $fansid));
        
         //中奖状态
        $this->set_status($rid, $fansid);
        
        die(json_encode(array("result"=>1)));
    }
       public function doWebHelps(){
        
        global $_GPC, $_W;
        $rid = intval($_GPC['rid']);
        $fansid = intval($_GPC['fansid']);
        $helps = floatval($_GPC['helps']);
        pdo_update('ewei_comeon_fans', array('helps'=>$helps) , array('rid' => $rid, 'id' => $fansid));
        die(json_encode(array("result"=>1)));
    }
    public function webmessage($error, $url = '', $errno = -1) {
        $data = array();
        $data['errno'] = $errno;
        if (!empty($url)) {
            $data['url'] = $url;
        }
        $data['error'] = $error;
        echo json_encode($data);
        exit;
    }

  
    public function doMobileIndex() {
        global $_GPC, $_W;
   
        $id = intval($_GPC['id']);
    
        if (empty($id)) {
            message('抱歉，参数错误！', '', 'error');
        }
        $reply = pdo_fetch("SELECT * FROM " . tablename('ewei_comeon_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));
        if ($reply == false) {
            message('抱歉，活动已经结束，下次再来吧！', '', 'error');
        }
        $fans = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and from_user=:from_user limit 1",array(":rid"=>$id,":from_user"=>$_W['fans']['from_user']));
 
        if(!empty($fans)){
            $points = empty($reply['type'])? number_format( intval($fans['points']) ):$fans['points'];
         
            $info_tips = $reply['info_tips'];
            $info_tips = str_replace("[P]" ,"<b class='n'>".$points."</b>" , $info_tips );
            $info_tips = str_replace("[U]" ,$reply['unit'] , $info_tips );
          
      
        }
        $fansid = intval($fans['id']);
       
        
        //如果是分享的
        $share_fansid = intval($_GPC['fansid']);
        if(!empty($share_fansid)){
            $share_fans = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and id=:id limit 1",array(":rid"=>$id,":id"=>$share_fansid));
            $points = empty($reply['type'])? number_format( intval($share_fans['points']) ):$share_fans['points'];
            $info_tips = $reply['info_tips'];
            $info_tips = str_replace("[P]" ,"<b class='n'>".$points."</b>" , $info_tips );
            $info_tips = str_replace("[U]" ,$reply['unit'] , $info_tips );
        }
        
        //获得关键词
        $keyword = pdo_fetch("select content from ".tablename('rule_keyword')." where rid=:rid and type=1",array(":rid"=>$id));
        $reply['keyword']=  $keyword['content'];
  
        //浏览次数
        pdo_update("ewei_comeon_reply",array("viewnum"=>$reply['viewnum'] + 1),array("rid"=>$id));
        
        //分享信息
        $sharelink = $_W['siteroot'] . $this->createMobileUrl('index', array('id' => $id,'fansid'=>$fansid));
        $sharetitle =  empty($reply['share_title']) ? $reply['title']:$reply['share_title'];
        $sharedesc =  empty($reply['share_desc']) ? str_replace("\r\n"," ",$reply['description']):str_replace("\r\n"," ", $reply['share_desc']);
        
        $shareimg =  toimage($reply['thumb']);
      
        $joinurl = empty($reply['joincontent']) ? $reply['share_url'] : ($this->createMobileUrl('intro', array('rid' => $id,'fansid'=>$fansid)));
        $is_follow = $this->is_follow();
        include $this->template('index');
        
    }
    
    public function doMobileDetail() {
        global $_GPC, $_W;
        $id = intval($_GPC['rid']);
  
        if (empty($id)) {
            message('抱歉，参数错误！', '', 'error');
        }
        $reply = pdo_fetch("SELECT * FROM " . tablename('ewei_comeon_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));
        if ($reply == false) {
            message('抱歉，活动已经结束，下次再来吧！', '', 'error');
        }
        $awards = pdo_fetchall("select * from ".tablename('ewei_comeon_award')." where rid=:rid ",array(":rid"=>$reply['rid']));
         $fans = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and from_user=:from_user limit 1",array(":rid"=>$id,":from_user"=>$_W['fans']['from_user']));
 
        //分享信息
        $sharelink = $_W['siteroot'] . $this->createMobileUrl('index', array('id' => $id, 'fansid' => $fans['id']));
        $sharetitle =  empty($reply['share_title']) ? $reply['title']:$reply['share_title'];
        $sharedesc =  empty($reply['share_desc']) ? str_replace("\r\n"," ",$reply['description']):str_replace("\r\n"," ", $reply['share_desc']);
    
        $shareimg =  toimage($reply['thumb']);
        
        include $this->template('detail');
        
    }
    
    
      public function doMobileIntro() {
        global $_GPC, $_W;
        $id = intval($_GPC['rid']);
  
        if (empty($id)) {
            message('抱歉，参数错误！', '', 'error');
        }
        $reply = pdo_fetch("SELECT * FROM " . tablename('ewei_comeon_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));
        if ($reply == false) {
            message('抱歉，活动已经结束，下次再来吧！', '', 'error');
        }
        $fans = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and from_user=:from_user limit 1",array(":rid"=>$id,":from_user"=>$_W['fans']['from_user']));
 
        //分享信息
        $sharelink =  $_W['siteroot'] . $this->createMobileUrl('index', array('id' => $id, 'fansid' => $fans['id']));
        $sharetitle =  empty($reply['share_title']) ? $reply['title']:$reply['share_title'];
        $sharedesc =  empty($reply['share_desc']) ? str_replace("\r\n"," ",$reply['description']):str_replace("\r\n"," ", $reply['share_desc']);
        $shareimg =  toimage($reply['thumb']);
        
        include $this->template('intro');
        
    }
    
    private function get_points($reply,$fans,$first = false){
        $points = 0 ;
        $fans_points = $fans['points'];
        
        //默认的
        $start  = $reply['start'];$end = $reply['end'];
        if($reply['type']==0){
            //整数
            $points = rand(intval($start) , intval($end));
        }
        else{
            $points = rand(intval($start*100) , intval($end*100));
            $points/=100;
        }
        if(!$first){
            
                $rules = pdo_fetchall("select * from ".tablename('ewei_comeon_rule')." where rid=:rid order by point desc",array(":rid"=>$reply['rid']));
            
                foreach($rules as $rule){
                    if($fans_points>=$rule['point']) {
                        $start  = $rule['start'];$end = $rule['end'];
                        
                        if($reply['type']==0){
                            //整数
                            $points = rand(intval($start) , intval($end));
                        }
                        else{
                            $points = rand(intval($start*100) , intval($end*100));
                            $points/=100;
                        }
                        break;
                    }
                }
        }
       
        return $points;
    }
    public function doMobileJoin(){
        
        global $_W,$_GPC;
        
        $rid = intval($_GPC['rid']);
        $reply = pdo_fetch("SELECT * FROM " . tablename('ewei_comeon_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
        
        if ($reply == false) {
           die(json_encode(array('result'=>0,"msg"=>"未找到活动哦!")));
        }
      
        if(empty($_W['fans']['from_user'])){
            die(json_encode(array('result'=>0,"msg"=>"您需要关注才能参加活动哦!")));
        }
         $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
        if(strpos($useragent, 'MicroMessenger') === false){
	    	die(json_encode(array('result'=>0,"msg"=>"您只能在微信客户端参加活动哦!")));
		}
        if ($reply['isshow'] == 0) {
            die(json_encode(array('result'=>0,"msg"=>"活动暂停 ,请稍后再来哦!")));
        }
         if ($reply['starttime'] > time()) {
           die(json_encode(array('result'=>0,"msg"=>"活动还未开始，还不能参加哦!")));
        }
        if ($reply['endtime'] < time()) {
           die(json_encode(array('result'=>0,"msg"=>"活动已经结束，不能参加啦，请等待下次活动哦!")));
        }
        
        
        $mobile = $_GPC['mobile'];
        if(empty($mobile)){
            die(json_encode(array('result'=>0,"msg"=>"请填写您的".$reply['tel_rename']."!")));
        }
        $fansd = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and mobile=:mobile limit 1",array(":rid"=>$rid,":mobile"=>$mobile));
        if(!empty($fansd)){
           die(json_encode(array('result'=>0,"msg"=>"{$mobile} 已经参加活动了哦~")));    
        }
        
        $fans = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and from_user=:from_user limit 1",array(":rid"=>$rid,":from_user"=>$_W['fans']['from_user']));
        if(!empty($fans)){
           die(json_encode(array('result'=>0,"msg"=>"您已经参加活动了哦，请邀请好友助力吧!")));    
        }
       
        $points = $this->get_points($reply,$fans,true);
        $fans = array(
            "rid"=>$rid,
            "from_user"=>$_W['fans']['from_user'],
            "mobile"=>$mobile,
            "points"=>$points,
            "helps"=>0,
            "createtime"=>time()
        );
        pdo_insert("ewei_comeon_fans",$fans);
        $fansid = pdo_insertid();
        
        $info_tips = $reply['info_tips'];
        $info_tips = str_replace("[P]" ,$points , $info_tips );
        $info_tips = str_replace("[U]" ,$reply['unit'] , $info_tips );
        
        //参数次数
        pdo_update("ewei_comeon_reply",array("fansnum"=>$reply['fansnum'] + 1),array("rid"=>$rid));
        
        //中奖状态
        $this->set_status($rid, $fans);
        
        die(json_encode(array('result'=>1,"msg"=>"成功参加活动，{$info_tips}，现在就邀请好友助力吧 !")));
    }
    
    
    public function doMobileHelp(){
        
        global $_W,$_GPC;
        $useragent = addslashes($_SERVER['HTTP_USER_AGENT']);
     
		if(strpos($useragent, 'MicroMessenger') === false){
	    	die(json_encode(array('result'=>0,"msg"=>"您只能在微信客户端给 TA 助力哦!")));
		}
        $rid = intval($_GPC['rid']);
        $reply = pdo_fetch("SELECT * FROM " . tablename('ewei_comeon_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
        
        if ($reply == false) {
           die(json_encode(array('result'=>0,"msg"=>"未找到活动哦!")));
        }
        if ($reply['starttime'] > time()) {
           die(json_encode(array('result'=>0,"msg"=>"活动还未开始，不能给 TA 助力啦!")));
        }
        if ($reply['endtime'] < time()) {
           die(json_encode(array('result'=>0,"msg"=>"活动已经结束，不能再给 TA 助力啦!")));
        }
        if ($reply['isshow'] == 0) {
            die(json_encode(array('result'=>0,"msg"=>"活动暂停，请稍后再给 TA 助力哦!")));
        }
     
      
        $is_follow = $this->is_follow();
 
        $fansid = intval($_GPC['fansid']);
        if(empty($fansid)){
            die(json_encode(array('result'=>0,"msg"=>"您要助力谁呀?")));
        }
        $fans = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and id=:id limit 1",array(":rid"=>$rid,":id"=>$fansid));
        if(empty($fans)){
            die(json_encode(array('result'=>0,"msg"=>"没找到人, 我不知道助力谁呀?")));
        }
     
        $today = date('Y-m-d',time());
        
           if($reply['self_times']>0){
                //检测助力总限制
                $count = pdo_fetchcolumn("select count(*) from ".tablename('ewei_comeon_fans_help')." where rid=:rid and fansid=:fansid ",array(":rid"=>$rid,":fansid"=>$fansid));    
                if($count>=$reply['self_times']){
                   die(json_encode(array('result'=>0,"msg"=>"TA 已经被助力 {$count} 次啦，不能再给 TA 助力啦!")));
                }
            }

            if($reply['self_day_times']>0){
                //检测每天助力限制
                $count = pdo_fetchcolumn("select count(*) from ".tablename('ewei_comeon_fans_help')." where rid=:rid and fansid=:fansid and date=:date ",array(":rid"=>$rid,":fansid"=>$fansid,":date"=>$today));    
                if($count>=$reply['self_day_times']){
                   die(json_encode(array('result'=>0,"msg"=>"今天 TA 已经被助力 {$count} 次啦，不能再给 TA 助力啦!")));
                }
            }

            
            
        if( (!$is_follow && !empty($_W['fans']['from_user'])) || $is_follow){
            
           
            
            //以前关注过,但现在取消关注的，已经关注的
             if($reply['other_times']>0){
                  //检测助力别人总限制
                  $count = pdo_fetchcolumn("select count(*) from ".tablename('ewei_comeon_fans_help')." where rid=:rid and from_user=:from_user  ",array(":rid"=>$rid,":from_user"=>$_W['fans']['from_user']));    
                  if($count>=$reply['other_times']){
                     die(json_encode(array('result'=>0,"msg"=>"您已经给别人助力 {$count} 次啦，不能再给 TA 助力啦!")));
                  }
              }

               if($reply['other_day_times']>0){
                  //检测每天助力限制
                  $count = pdo_fetchcolumn("select count(*) from ".tablename('ewei_comeon_fans_help')." where rid=:rid and from_user=:from_user and date=:date  ",array(":rid"=>$rid,":from_user"=>$_W['fans']['from_user'],":date"=>$today));    
                  if($count>=$reply['other_day_times']){
                     die(json_encode(array('result'=>0,"msg"=>"今天您已经给别人助力 {$count} 次啦，不能再给 TA 助力啦!")));
                  }
              }


              if($reply['other_one_times']>0){
                  //检测助力别人总限制
                  $count = pdo_fetchcolumn("select count(*) from ".tablename('ewei_comeon_fans_help')." where rid=:rid and fansid=:fansid and from_user=:from_user  ",array(":rid"=>$rid,":fansid"=>$fansid,":from_user"=>$_W['fans']['from_user']));    
                  if($count>=$reply['other_one_times']){
                     die(json_encode(array('result'=>0,"msg"=>"您已经给 TA 助力 {$count} 次啦，不能再给 TA 助力啦!")));
                  }
              }

               if($reply['other_one_day_times']>0){
                  //检测每天助力限制
                  $count = pdo_fetchcolumn("select count(*) from ".tablename('ewei_comeon_fans_help')." where rid=:rid and fansid=:fansid and from_user=:from_user and date=:date  ",array(":rid"=>$rid,":fansid"=>$fansid,":from_user"=>$_W['fans']['from_user'],":date"=>$today));    
                  if($count>=$reply['other_one_day_times']){
                     die(json_encode(array('result'=>0,"msg"=>"今天您已经给 TA 助力 {$count} 次啦，不能再给 TA 助力啦!")));
                  }
              }
              pdo_insert("ewei_comeon_fans_help",array(
                    "rid"=>$rid,
                    "from_user"=>$_W['fans']['from_user'],
                    "fansid"=>$fansid,
                    "date"=>$today
                ));
        }
        else{
     
            //没有关注的
            session_start();
       
        
        
             if($reply['other_times']>0){
                  //检测助力别人总限制
                  $key = 'ewei_comeon_other_times';
                  $count = intval($_GPC[$key]);
                  if($count>=$reply['other_times']){
                     die(json_encode(array('result'=>0,"msg"=>"您已经给别人助力 {$count} 次啦，不能再给 TA 助力啦!")));
                  }
                   $count++;
                  isetcookie( $key ,$count, 3600 * 24  * 365 );
              }

               if($reply['other_day_times']>0){
                  //检测每天助力限制
                  $key ='ewei_comeon_other_day_times_'.$today;
                  $count =intval($_GPC[$key]);
                  if($count>=$reply['other_day_times']){
                     die(json_encode(array('result'=>0,"msg"=>"今天您已经给别人助力 {$count} 次啦，不能再给 TA 助力啦!")));
                  }
                  $count++;
                  isetcookie( $key ,$count, 3600 * 24  * 365 );
              }


              if($reply['other_one_times']>0){
                  //检测助力相同人总限制
                  $key = 'ewei_comeon_other_one_times_'.$fansid;
                  $count = intval($_GPC[$key]);
                  if($count>=$reply['other_one_times']){
                     die(json_encode(array('result'=>0,"msg"=>"您已经给 TA 助力 {$count} 次啦，不能再给 TA 助力啦!")));
                  }
                  $count++;
                  isetcookie( $key ,$count, 3600 * 24  * 365 );
              }

               if($reply['other_one_day_times']>0){
                  //检测每天助力相同人限制
                  $key = 'ewei_comeon_other_one_day_times_'.$fansid.'_'.$today;
                  $count = intval($_GPC[$key]);
                  if($count>=$reply['other_one_day_times']){
                     die(json_encode(array('result'=>0,"msg"=>"今天您已经给 TA 助力 {$count} 次啦，不能再给 TA 助力啦!")));
                  }
                  $count++;
                   isetcookie( $key ,$count, 3600 * 24  * 365 );
              }
              
              pdo_insert("ewei_comeon_fans_help",array(
                  "rid"=>$rid,
                  "from_user"=>"-1",
                  "fansid"=>$fansid,
                  "date"=>$today
              ));
                 
              
        }
        
   
        
        $points = $this->get_points($reply,$fans);
        pdo_update("ewei_comeon_fans",array('points'=>$fans['points'] + $points,"helps"=>$fans['helps'] + 1),array("id"=>$fansid,"rid"=>$rid));
     
         //中奖状态
        $this->set_status($rid, $fansid);
        
        
        die(json_encode(array('result'=>1,"msg"=>"成功助力, TA 获得了 {$points} {$reply['unit']}，您可以参加活动，大奖在等着您!")));
        
    }
    
    
    public function doMobileSearch(){
        
        global $_W,$_GPC;
        
        $rid = intval($_GPC['rid']);
        $reply = pdo_fetch("SELECT * FROM " . tablename('ewei_comeon_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
        
        if ($reply == false) {
           die(json_encode(array('result'=>0,"msg"=>"未找到活动哦!")));
        }
        if ($reply['starttime'] > time()) {
            die(json_encode(array('result'=>0,"msg"=>"活动还未开始，不能查询哦!")));
        }
        if ($reply['endtime'] > time()) {
            die(json_encode(array('result'=>0,"msg"=>"活动还未结束，不能查询哦!")));
        }
        
        $mobile = $_GPC['mobile'];
        if(empty($mobile)){
            die(json_encode(array('result'=>0,"msg"=>"请填写".$reply['tel_rename']."!")));
        }
        
        $fans = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and mobile=:mobile limit 1",array(":rid"=>$rid,":mobile"=>$mobile));
        if(empty($fans)){
            die(json_encode(array('result'=>0,"msg"=>"此用户并没有参加活动哦!")));
        }
        
   
        $awards = pdo_fetchall("select * from ".tablename('ewei_comeon_award')." where rid=:rid ",array(":rid"=>$rid));
        $gifts = array();
        foreach($awards as $award){
            if($fans['points']>=$award['point'] && $award['num']>0){
                $gifts[] = $award['name'];
            }
        } 
        
        if($gifts<=0){
            die(json_encode(array('result'=>0,"msg"=>"此用户没有中奖哦，请等待下次活动吧!")));
        }
       
        
        if($fans['status']==1){
            die(json_encode(array('result'=>0,"msg"=>"恭喜，此用户已中奖, 奖品为: ".implode("或",$gifts)." ,兑奖方法: ".$reply['ticket_information'])));     
        }
        else if($fans['status']==2){
            die(json_encode(array('result'=>0,"msg"=>"恭喜，此用户已中奖, 奖品为: ".implode("或",$gifts)." ,奖品已经领取!")));     
        }
        else{
             die(json_encode(array('result'=>0,"msg"=>"此用户没有中奖哦，请等待下次活动吧!")));
        }
    }
    
    private function set_status($rid,$fansid ){
        $fans = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and id=:id limit 1",array(":rid"=>$rid,":id"=>$fansid));
        if(empty($fans)){
             return;
        }
        $awards = pdo_fetchall("select * from ".tablename('ewei_comeon_award')." where rid=:rid ",array(":rid"=>$rid));
        foreach($awards as $award){
            if($fans['points']>=$award['point'] && $award['num']>0){
                pdo_update("ewei_comeon_fans",array("status"=>1),array("rid"=>$rid,"id"=>$fansid));
            }
        } 
     
    }
      public function doMobileRank() {
        global $_GPC, $_W;
    
        $id = intval($_GPC['rid']);
  
        if (empty($id)) {
            message('抱歉，参数错误！', '', 'error');
        }
        $reply = pdo_fetch("SELECT * FROM " . tablename('ewei_comeon_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $id));
        if ($reply == false) {
            message('抱歉，活动已经结束，下次再来吧！', '', 'error');
        }
      
        $fans = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and from_user=:from_user limit 1",array(":rid"=>$id,":from_user"=>$_W['fans']['from_user']));
   
        if(!empty($fans)){
           $fans['rank']=pdo_fetchcolumn("select count(*) from ".tablename('ewei_comeon_fans')." where rid=:rid and points>={$fans['points']} and id<>:fansid",array(":rid"=>$id,":fansid"=>$fans['id']));
           $fans['rank']++;
        }
        $rank_num = intval($reply['rank_num']);
        if($rank_num<=0){
            $rank_num = 10;
        }
        $allfans = pdo_fetchall("select * from ".tablename('ewei_comeon_fans')." where rid=:rid order by points desc limit {$rank_num}",array(":rid"=>$id));
        foreach($allfans as &$f){
           $f['mobile'] =substr_replace($f['mobile'],'*****',3,5);
        }
        unset($f);
        //获得关键词
        $keyword = pdo_fetch("select content from ".tablename('rule_keyword')." where rid=:rid and type=1",array(":rid"=>$id));
        $reply['keyword']=  $keyword['content'];
  
        //浏览次数
        pdo_update("ewei_comeon_reply",array("viewnum"=>$reply['viewnum'] + 1),array("rid"=>$id));
        
        //分享信息
        $sharelink = $_W['siteroot'] . $this->createMobileUrl('index', array('id' => $id));
        $sharetitle =  empty($reply['share_title']) ? $reply['title']:$reply['share_title'];
 $sharedesc =  empty($reply['share_desc']) ? str_replace("\r\n"," ",$reply['description']):str_replace("\r\n"," ", $reply['share_desc']);
        $shareimg =  toimage($reply['thumb']);
  
        $is_follow = $this->is_follow();
        include $this->template('rank');
        
    }
     public function doWebgetaward( ){
         global $_W,$_GPC;
    
 
        $rid = $_GPC['id'];
  
        $reply = pdo_fetch("SELECT * FROM " . tablename('ewei_comeon_reply') . " WHERE rid = :rid limit 1", array(':rid' => $rid));
        if(empty($reply)){
            message('未找到活动!','','error');
        }
        
        $fansid = intval($_GPC['fansid']);
        $fans = pdo_fetch("select * from ".tablename('ewei_comeon_fans')." where rid=:rid and id=:id limit 1",array(":rid"=>$rid,":id"=>$fansid));
        if(empty($fans)){
            message('未找到用户!','','error');
        }
        
        $awardid = intval($_GPC['awardid']);
        $award = pdo_fetch("select * from ".tablename('ewei_comeon_award')." where rid=:rid and id=:id limit 1 ",array(":rid"=>$rid,":id"=>$awardid));
         if(empty($award)){
            message('未找到礼品!','','error');
        }
   
        if($award['num']<=0){
            message('礼品数已经不足，无法领取了!','','error');   
        }
        pdo_update("ewei_comeon_award",array('num'=>$award['num']-1),array("id"=>$awardid,"rid"=>$rid));
 
        pdo_update("ewei_comeon_fans",array("status"=>2,"awardid"=>$awardid,"awardtime"=>time()),array("rid"=>$rid,"id"=>$fansid));
        message('领奖成功!',referer(),"success");
    }
}
