<?php
/**
 * 分享积赞模块定义
 *
 * @author 石头鱼
 * @url http://bbs.b2ctui.com
 */
defined('IN_IA') or exit('Access Denied');

class praiseModule extends WeModule {
	public $name = 'praiseModule';
	public $title = '分享集赞';
	public $table_reply  = 'praise_reply';
	public $table_list   = 'praise_list';	
	public $table_data   = 'praise_data';

	public function fieldsFormDisplay($rid = 0) {
		//要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
		global $_W;
		if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->table_reply)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));				
 		} 
		$reply['start_time'] = empty($reply['start_time']) ? strtotime(date('Y-m-d H:i')) : $reply['start_time'];
		$reply['end_time'] = empty($reply['end_time']) ? strtotime("+1 week") : $reply['end_time'];

		include $this->template('praise/form');
		
	}

	public function fieldsFormValidate($rid = 0) {
		//规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
		return '';
	}

	public function fieldsFormSubmit($rid) {
		//规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
		global $_GPC, $_W;
		$weid = $_W['weid'];
		$id = intval($_GPC['reply_id']);
		$insert = array(
			'rid' => $rid,
			'weid' => $weid,
            'title' => $_GPC['title'],			
			'picture' => $_GPC['picture'],
			'praiseurl' => $_GPC['praiseurl'],
			'description' => $_GPC['description'],			
			'content' => $_GPC['content'],	
			'start_time' => strtotime($_GPC['start_time']),
			'end_time' => strtotime($_GPC['end_time']),
			'status' => intval($_GPC['status']),
			'credit' => intval($_GPC['credit']),
			'praisenum' => intval($_GPC['praisenum']),
		);
		if (empty($id)) {
			pdo_insert($this->table_reply, $insert);
		} else {			
			pdo_update($this->table_reply, $insert, array('id' => $id));
		}		

	}

	public function ruleDeleted($rid) {
		//删除规则时调用，这里 $rid 为对应的规则编号
		global $_W;		
		pdo_delete($this->table_reply, "rid = '".$rid."'");
		pdo_delete($this->table_list, "rid = '".$rid."'");
		pdo_delete($this->table_data, "rid = '".$rid."'");
		message('删除活动成功！', referer(), 'success');
		return true;
	}

	public function settingsDisplay($settings) {
		global $_GPC, $_W;
		if(checksubmit()) {
			$cfg = array();
			$cfg['appid'] = $_GPC['appid'];
			$cfg['secret'] = $_GPC['secret'];
			if($this->saveSettings($cfg)) {
				message('保存成功', 'refresh');
			}
		}		
		include $this->template('setting');
	}

	public function dopraiselist($rid = 0) {		
		global $_GPC, $_W;
		

	}
	public function dopraiseranklist($rid, $state) {		
		global $_GPC, $_W;
		checklogin();
		$weid = $_W['weid'];//当前公众号ID
		$id = intval($_GPC['id']);
		$page = $_GPC['page'];
		if (empty($page)){
		  $page = 1;
		}
		if (checksubmit('delete')) {
			pdo_delete($this->table_list, " id IN ('".implode("','", $_GPC['select'])."')");
			message('删除成功！', create_url('site/module', array('do' => 'praiseranklist', 'name' => 'praise', 'id' => $id, 'page' => $_GPC['page'])));
		}
		$reply = pdo_fetch("SELECT title FROM ".tablename($this->table_reply)." WHERE weid = :weid and rid = :rid", array(':weid' => $weid, ':rid' => $id));
		//
		$pindex = max(1, intval($_GPC['page']));
		$psize = 15;

		//取得分享集赞列表
		$list_praise = pdo_fetchall('SELECT a.*,b.realname,b.mobile FROM '.tablename($this->table_list).' as a left join '.tablename('fans').' as b on a.from_user=b.from_user  WHERE a.rid = '.$id.' and a.weid= :weid order by `praisenum` desc LIMIT ' . ($pindex - 1) * $psize . ',' . $psize, array(':weid' => $weid) );
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM '.tablename($this->table_list).' WHERE rid = '.$id.' and weid= :weid order by `id` desc ', array(':weid' => $weid));
		$pager = pagination($total, $pindex, $psize);
		include $this->template('praiseranklist');

	}
	public function dopraisedatalist($rid) {		
		global $_GPC, $_W;
		

	}
	public function dostatus( $rid = 0) {
		global $_GPC;
		$rid = $_GPC['rid'];
		echo $rid;
		$insert = array(
			'status' => $_GPC['status']
		);
		
		pdo_update($this->table_reply,$insert,array('rid' => $rid));
		message('模块操作成功！', referer(), 'success');
	}
	public function dodos( $id = 0) {
		global $_GPC;
		$rid = $_GPC['rid'];
		$id = $_GPC['id'];
		$praiselist = $_GPC['ac'];
		echo $id;
		$insert = array(
			'status' => $_GPC['status']
		);
		
		pdo_update($this->table_list,$insert,array('id' => $id,'rid' => $rid));
		message('屏蔽操作成功！', create_url('site/module/'.$praiselist.'', array('name' => 'praise', 'id' => $rid, 'page' => $_GPC['page'])));
	}	
	
	public function dopraisedata($rid = 0) {		
		global $_GPC, $_W;
		checklogin();
		

	}
	public function dodeldata( $id = 0) {
		global $_GPC;
		$rid = $_GPC['rid'];
		$id = $_GPC['id'];
		if (!empty($id)) {
			pdo_delete($this->table_data, " id = ".$id);
			message('删除成功！', create_url('site/module/praisedata', array('name' => 'praise', 'id' => $rid, 'page' => $_GPC['page'])));
		}		
		
	}

}