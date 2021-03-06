<?php

/**
 * 来吧来吧
 */
defined('IN_IA') or exit('Access Denied');

class Ewei_comeonModule extends WeModule {
  
    public $weid;
    public function __construct() {
        $this->weid = IMS_VERSION<0.6?$_W['weid']:$_W['uniacid'];
    }
  
  public function fieldsFormDisplay($rid = 0) {
        global $_W;
 
        if (!empty($rid)) {
            $reply = pdo_fetch("SELECT * FROM " . tablename('ewei_comeon_reply') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
            if(!empty($reply)){
                $rules = pdo_fetchall("SELECT * FROM " . tablename('ewei_comeon_rule') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
                $awards = pdo_fetchall("SELECT * FROM " . tablename('ewei_comeon_award') . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
            }
        }
        if (!$reply) {
            $now = time();
            $reply = array(
                
                "starttime" => $now,
                "endtime" => strtotime(date("Y-m-d H:i", $now + 7 * 24 * 3600)),
                "tel_rename" => "手机号",
                "other_one_times"=>1,
                "other_one_day_times"=>1,
                "other_day_times"=>1,
                "self_day_times"=>1,
                "rank_num"=>10,
                
                
            );
        }
        if(IMS_VERSION>=0.6){
            load()->func('tpl');
        }

        $version = IMS_VERSION<0.6?'':'_advance';
        include $this->template('form'.$version);
    }

    public function fieldsFormValidate($rid = 0) {
        //规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
        return '';
    }

    public function fieldsFormSubmit($rid) {
        global $_GPC, $_W;
        $id = intval($_GPC['reply_id']);

        $insert = array(
            'rid' => $rid,
            'weid' =>$this->weid,
            'title' => $_GPC['title'],
            'thumb' => $_GPC['thumb'],
            'ticket_information' => $_GPC['ticket_information'],
            'description' => $_GPC['description'],
            'content' => htmlspecialchars_decode($_GPC['content']),
            'joincontent' => htmlspecialchars_decode($_GPC['joincontent']),
            'overcontent' => htmlspecialchars_decode($_GPC['overcontent']),
            'type' => intval($_GPC['type']),
            'show_num' => intval($_GPC['show_num']),
            'show_rank' => intval($_GPC['show_rank']),
            'rank_num' => intval($_GPC['rank_num']),
            'awardtype' => intval($_GPC['awardtype']),
            'info_tips' => $_GPC['info_tips'],
            'help_tips' => $_GPC['help_tips'],
            'invite_tips' => $_GPC['invite_tips'],
            'rank_tips' => $_GPC['rank_tips'],
            'bgcolor' => $_GPC['bgcolor'],
            'fontcolor' => $_GPC['fontcolor'],
            'btnfontcolor' => $_GPC['btnfontcolor'],
            'btncolor' => $_GPC['btncolor'],
            'toppic' => $_GPC['toppic'],
            'unit' => $_GPC['unit'],
            'tips' => $_GPC['tips'],
            'join_tips' => $_GPC['join_tips'],
            'self_times' => intval($_GPC['self_times']),
            'self_day_times' =>  intval($_GPC['self_day_times']),
            'other_times' =>  intval($_GPC['other_times']),
            'other_day_times' =>  intval($_GPC['other_day_times']),
            'other_one_times' =>  intval($_GPC['other_one_times']),
            'other_one_day_times' =>  intval($_GPC['other_one_day_times']),
            
            'tel_rename' => $_GPC['tel_rename'],
            'createtime' => time(),
            'copyright' => $_GPC['copyright'],
            'share_title' => $_GPC['share_title'],
            'share_desc' => $_GPC['share_desc'],
            'share_url' => $_GPC['share_url'],
            'share_txt' => $_GPC['share_txt'],
            
            'start' => $_GPC['start'],
            'end' => $_GPC['end'],
        );
        	if(IMS_VERSION<0.6){
           $insert['starttime'] = strtotime($_GPC['datelimit-start']);
           $insert['endtime'] = strtotime($_GPC['datelimit-end']);
           	}else{
           		$d = $_GPC['datelimit'];
           	 
           	$insert['starttime'] = strtotime($d['start']);
           $insert['endtime'] = strtotime($d['end']);
           }
      
        if (empty($id)) {
            if ($insert['starttime'] <= time()) {
                $insert['isshow'] = 1;
            } else {
                $insert['isshow'] = 0;
            }
            $id = pdo_insert('ewei_comeon_reply', $insert);
        } else {
            pdo_update('ewei_comeon_reply', $insert, array('id' => $id));
            
        }
        
        
          //奖品
        $awardids = array();
        $award_ids = $_GPC['award_id'];
        $award_names = $_GPC['award_name'];
        $award_points = $_GPC['award_point'];
        $award_nums = $_GPC['award_num'];
        if(is_array($award_ids)){
            foreach($award_ids as $key =>$value){
                $value = intval($value);
                $d = array(
                    "rid"=>$rid,
                    "point"=>$award_points[$key],
                    "name"=>$award_names[$key],
                    "num"=>$award_nums[$key],
                );
               
                if(empty($value)){
                    pdo_insert('ewei_comeon_award',$d);
                    $awardids[] = pdo_insertid();
                }
                else{
                    pdo_update('ewei_comeon_award',$d,array("id"=>$value));
                    $awardids[] = $value;
                }
            }
            if(count($awardids)>0){
                pdo_query("delete from ".tablename('ewei_comeon_award')." where rid='{$rid}' and id not in (".implode(",",$awardids).")");
            }
            else{
                pdo_query("delete from ".tablename('ewei_comeon_award')." where rid='{$rid}'");
            }
        }
        
        
        //规则
        $ruleids = array();
        $rule_ids = $_GPC['rule_id'];
        $rule_points = $_GPC['rule_point'];
        $rule_starts = $_GPC['rule_start'];
        $rule_ends = $_GPC['rule_end'];
        if(is_array($rule_ids)){
            foreach($rule_ids as $key =>$value){
                $value = intval($value);
                $d = array(
                    "rid"=>$rid,
                    "point"=>$rule_points[$key],
                    "start"=>$rule_starts[$key],
                    "end"=>$rule_ends[$key],
                );
                if(empty($value)){
                    pdo_insert('ewei_comeon_rule',$d);
                    $ruleids[] = pdo_insertid();
                }
                else{
                    pdo_update('ewei_comeon_rule',$d,array("id"=>$value));
                    $ruleids[] = $value;
                }
            }
            if(count($ruleids)>0){
                pdo_query("delete from ".tablename('ewei_comeon_rule')." where rid='{$rid}' and id not in (".implode(",",$ruleids).")");
            }
            else{
                pdo_query("delete from ".tablename('ewei_comeon_rule')." where rid='{$rid}'");
            }
        }
        
      
        
        return true;
    }

    public function ruleDeleted($rid) {
        pdo_delete('ewei_comeon_award', array('rid' => $rid));
        pdo_delete('ewei_comeon_rule', array('rid' => $rid));
        pdo_delete('ewei_comeon_reply', array('rid' => $rid));
        pdo_delete('ewei_comeon_fans', array('rid' => $rid));
    }
}
