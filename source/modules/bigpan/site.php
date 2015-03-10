<?php
/**
 * 大转盘模块微站定义
 *
 * @author 珊瑚海
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
include_once IA_ROOT . '/source/modules/bigpan/model.php';
class BigpanModuleSite extends WeModuleSite {

	public function getHomeTiles($keyword = '') {
		$urls = array();
		$list = pdo_fetchall("SELECT name, id FROM ".tablename('rule')." WHERE module = 'bigpan'".(!empty($keyword) ? " AND name LIKE '%{$keyword}%'" : ''));
		if (!empty($list)) {
			foreach ($list as $row) {
				$urls[] = array('title'=>$row['name'], 'url'=> $this->createMobileUrl('lottery', array('id' => $row['id'])));
			}
		}
		return $urls;
	}

	public function doMobilelottery() {
		global $_W, $_GPC;
		$title = '大转盘抽奖';
		if (empty($_W['fans']['from_user'])) {
			message('非法访问，请重新发送消息进入抽奖页面！');
		}
		$fromuser = $_W['fans']['from_user'];
		$id = intval($_GPC['id']);
		$pan = pdo_fetch("SELECT * FROM ".tablename('bigpan_reply')." WHERE rid = '$id' LIMIT 1");
		$pan['zppic'] =$_W['attachurl'].$pan['zppic'];
		if (empty($pan)) {
			exit('非法参数！');
		}
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('bigpan_winner')." WHERE createtime > '".strtotime(date('Y-m-d'))."' AND rid = '{$id}' AND from_user = '$fromuser' AND status <> 3" );
		$member = pdo_fetch("SELECT * FROM ".tablename('fans')." WHERE from_user = '{$fromuser}'");
		
		$myaward = pdo_fetchall("SELECT award, description FROM ".tablename('bigpan_winner')." WHERE from_user = '{$fromuser}' AND award <> '' AND rid = '{$id}' ORDER BY createtime DESC");
		$sql = "SELECT a.award, b.realname FROM ".tablename('bigpan_winner')." AS a
				LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE b.mobile <> '' AND b.realname <> '' AND
				a.from_user <> '{$fromuser}' AND a.award <> '' AND a.rid = '$id' ORDER BY a.createtime DESC LIMIT 20";
		$otheraward = pdo_fetchall($sql);
		
		$lottery = pdo_fetchall("SELECT * FROM ".tablename('bigpan_award')." WHERE rid = '$id' ORDER BY probalilty ASC");
		include $this->template('lottery');
	}

	public function doMobilegetAward() {
		global $_GPC, $_W;
		if (empty($_W['fans']['from_user'])) {
			message('非法访问，请重新发送消息进入抽奖页面！');
		}
		$fromuser = $_W['fans']['from_user'];
		$id = intval($_GPC['id']);
		$pan = pdo_fetch("SELECT id, maxlottery, default_tips, misscredit, hitcredit FROM ".tablename('bigpan_reply')." WHERE rid = '$id' LIMIT 1");
		if (empty($pan)) {
			exit('非法参数！');
		}
		$result = array('status' => -1, 'message' => '');
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('bigpan_winner')." WHERE createtime > '".strtotime(date('Y-m-d'))."' AND rid = '{$id}' AND from_user = '$fromuser' AND status <> 3");
		if (!empty($pan['maxlottery']) && $total >= $pan['maxlottery']) {
			$result['message'] = '您已经超过当日转盘次数';
			$result['num'] =$total;
			message($result, '', 'ajax');
		}
		$gifts = pdo_fetchall("SELECT id,level, probalilty FROM ".tablename('bigpan_award')." WHERE rid = '$id' ORDER BY probalilty ASC");
		//计算每个礼物的概率
		$probability = 0;
		$rate = 1;
		$award = array();
		foreach ($gifts as $name => $gift){
			if (empty($gift['probalilty'])) {
				continue;
			}
			if ($gift['probalilty'] < 1) {
				$temp = explode('.', $gift['probalilty']);
				$temp = pow(10, strlen($temp[1]));
				$rate = $temp < $rate ? $rate : $temp;
			}
			$probability = $probability + $gift['probalilty'] * $rate;
			$award[] = array('id' => $gift['id'], 'probalilty' => $probability);
		}
		$all = 100 * $rate;
		if($probability < $all){
			$award[] = array('title' => '','probalilty' => $all);
		}
		mt_srand((double) microtime()*1000000);
		$rand = mt_rand(1, $all);
		foreach ($award as $key => $gift){
			if(isset($award[$key - 1])){
				if($rand > $award[$key -1]['probalilty'] && $rand <= $gift['probalilty']){
					$awardid = $gift['id'];
					break;
				}
			}else{
				if($rand > 0 && $rand <= $gift['probalilty']){
					$awardid = $gift['id'];
					break;
				}
			}
		}
		
		$title = '';
		$result['message'] = empty($pan['default_tips']) ? '很遗憾,您没能中奖！' : $pan['default_tips'];
		$data = array(
			'rid' => $id,
			'from_user' => $fromuser,
			'status' => empty($gift['inkind']) ? 1 : 0,
			'createtime' => TIMESTAMP,
		);
		$credit = array(
			'rid' => $id,
			'aid'=>0,
			'award' => (empty($awardid) ? '未中' : '中') . '奖励积分',
			'from_user' => $fromuser,
			'status' => 3,
			'description' => (empty($awardid) ? $pan['misscredit'] : $pan['hitcredit']),
			'createtime' => TIMESTAMP,
		);
		if (!empty($awardid)) {
			$gift = pdo_fetch("SELECT * FROM ".tablename('bigpan_award')." WHERE rid = '$id' AND id = '$awardid'");
			if ($gift['total'] > 0) {
				$data['award'] = $gift['title'];
				if (!empty($gift['inkind'])) {
					$data['description'] = $gift['description'];
					pdo_query("UPDATE ".tablename('bigpan_award')." SET total = total - 1 WHERE rid = '$id' AND id = '$awardid'");
				} else {
					$gift['activation_code'] = iunserializer($gift['activation_code']);
					$code = array_pop($gift['activation_code']);
					pdo_query("UPDATE ".tablename('bigpan_award')." SET total = total - 1, activation_code = '".iserializer($gift['activation_code'])."' WHERE rid = '$id' AND id = '$awardid'");
					$data['description'] = '兑换码：' . $code . '<br /> 兑换地址：' . $gift['activation_url'];
				}
				
				$result['message'] = '恭喜您，得到"'.$data['award'].'"！' ;
				$result['status'] = 0;
				$result['id'] =$gift['level'];
				$result['sn'] = $code;
			} else {
				$credit['description'] = $pan['misscredit'];
				$credit['award'] = '未中奖励积分';
				
			}
		}
		!empty($credit['description']) && $result['message'] .= ' ' . $credit['award'] . '：'. $credit['description'];
		$data['aid'] = $gift['id'];
		
		if (!empty($credit['description'])) {
			pdo_insert('bigpan_winner', $credit);
		}
		pdo_insert('bigpan_winner', $data);
		
		
		
		
		$result['myaward'] = pdo_fetchall("SELECT award, description FROM ".tablename('bigpan_winner')." WHERE from_user = '{$fromuser}' AND award <> '' AND rid = '$id' ORDER BY createtime DESC");
		
		message($result, '', 'ajax');

	}

	public function doMobileregister() {
		global $_GPC, $_W;
		$title = '大转盘领奖登记个人信息';
		if (empty($_W['fans']['from_user'])) {
			message('非法访问，请重新发送消息进入抽奖页面！');
		}
		$fromuser = $_W['fans']['from_user'];
		$member = pdo_fetch("SELECT id, realname, mobile, qq FROM ".tablename('fans')." WHERE from_user = '{$fromuser}' LIMIT 1");
		if (!empty($_GPC['submit'])) {
			$data = array(
				'realname' => $_GPC['realname'],
				'mobile' => $_GPC['mobile'],
				'qq' => $_GPC['qq'],
				//'credit'
			);
			if (empty($data['realname'])) {
				die('<script>alert("请填写您的真实姓名！");location.reload();</script>');
			}
			if (empty($data['mobile'])) {
				die('<script>alert("请填写您的手机号码！");location.reload();</script>');
			}

			if (empty($member)) {
				$data['from_user'] = $fromuser;
				pdo_insert('fans', $data);
			} else {
				pdo_update('fans', $data, array('from_user' => $fromuser));
			}
			die('<script>alert("登记成功！");location.href = "'.create_url('site/module/lottery', array('name' => 'bigpan','id' => intval($_GPC['id']), 'from_user' => $_GPC['from_user'])).'";</script>');
			
		}
		include $this->template('register');
		
	}

	public function doWebdelete() {
		global $_W,$_GPC;
		$id = intval($_GPC['id']);
		$sql = "SELECT id FROM " . tablename('bigpan_award') . " WHERE `id`=:id";
		$row = pdo_fetch($sql, array(':id'=>$id));
		if (empty($row)) {
			message('抱歉，奖品不存在或是已经被删除！', '', 'error');
		}
		if (pdo_delete('bigpan_award', array('id' => $id))) {
			message('删除奖品成功', '', 'success');
		}
	}

	public function doWebawardlist() {
		global $_GPC, $_W;
		checklogin();
		$id = intval($_GPC['id']);
		if (checksubmit('delete')) {
			pdo_delete('bigpan_winner', " id  IN  ('".implode("','", $_GPC['select'])."')");
			message('删除成功！', create_url('site/module/awardlist', array('name' => 'bigpan', 'id' => $id, 'page' => $_GPC['page'], 'state'=>'')));
		}
		if (!empty($_GPC['wid'])) {
			$wid = intval($_GPC['wid']);
			pdo_update('bigpan_winner', array('status' => intval($_GPC['status'])), array('id' => $wid));
			message('标识领奖成功！', create_url('site/module/awardlist', array('name' => 'bigpan', 'id' => $id, 'page' => $_GPC['page'],'state'=>'')));
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$where = '';
		$starttime = !empty($_GPC['starttime']) ? strtotime($_GPC['starttime']) : 0;
		$endtime = !empty($_GPC['starttime']) ? strtotime($_GPC['endtime']) : 0;
		if (!empty($starttime) && $starttime == $endtime) {
			$endtime = $endtime + 86400 - 1;
		}
		$condition = array(
			'isregister' => array(
				'',
				" AND b.realname <> ''",
				" AND b.realname = ''",
			),
			'isaward' => array(
				'',
				" AND a.award <> ''",
				" AND a.award = ''",
			),
			'qq' => " AND b.qq ='{$_GPC['profilevalue']}'",
			'mobile' => " AND b.mobile ='{$_GPC['profilevalue']}'",
			'realname' => " AND b.realname ='{$_GPC['profilevalue']}'",
			'title' => " AND a.award = '{$_GPC['awardvalue']}'",
			'description' => " AND a.description = '{$_GPC['awardvalue']}'",
			'starttime' => " AND a.createtime >= '$starttime'",
			'endtime' => " AND a.createtime <= '$endtime'",
		);
		if (!isset($_GPC['isregister'])) {
			$_GPC['isregister'] = 1;
		}
		$where .= $condition['isregister'][$_GPC['isregister']];
		if (!isset($_GPC['isaward'])) {
			$_GPC['isaward'] = 1;
		}
		$where .= $condition['isaward'][$_GPC['isaward']];
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
		$sql = "SELECT a.id, a.award, a.description, a.status, a.createtime, b.realname, b.mobile, b.qq FROM ".tablename('bigpan_winner')." AS a
				LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE a.rid = '$id' $where ORDER BY a.createtime DESC, a.status ASC LIMIT ".($pindex - 1) * $psize.",{$psize}";
		$list = pdo_fetchall($sql);
		if (!empty($list)) {
			$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('bigpan_winner')." AS a
				LEFT JOIN ".tablename('fans')." AS b ON a.from_user = b.from_user WHERE a.rid = '$id' $where");
			$pager = pagination($total, $pindex, $psize);
		}
		include $this->template('awardlist');
	}

	public function doWebformDisplay() {
		global $_W, $_GPC;
		$result = array('error' => 0, 'message' => '', 'content' => '');
		$result['content']['id'] = $GLOBALS['id'] = 'add-row-news-'.$_W['timestamp'];
		$result['content']['html'] = $this->template('item', TEMPLATE_FETCH);
		exit(json_encode($result));
	}

}