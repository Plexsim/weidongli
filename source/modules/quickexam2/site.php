<?php
/**
 * 微试卷
 * QQ群：304081212
 * 作者：微动力, 547753994
 *
 * 网站：www.xuehuar.com
 */

defined('IN_IA') or exit('Access Denied');
function my_quickexam2_display_order_sort($a, $b) {
	if ($a['order'] == $b['order']) {
		return 0;
	}
	return ($a['order'] > $b['order']) ? 1 : -1;
}

class QuickExam2ModuleSite extends WeModuleSite {
	public $table_paper = 'quickexam2_paper';
	public $table_score = 'quickexam2_score_record';
	protected $modulename;

	function __construct() {	
	}
	
	public function doWebQuery() {
		global $_W, $_GPC;
		$kwd = $_GPC['keyword'];
		$sql = 'SELECT * FROM ' . tablename($this->table_paper) . ' WHERE `weid`=:weid AND `title` LIKE :title';
		$params = array();
		$params[':weid'] = $_W['weid'];
		$params[':title'] = "%{$kwd}%";
		$ds = pdo_fetchall($sql, $params);
		include $this->template('query');
	}
	

	public function doWebChoice() {
		global $_W;
		global $_GPC; // 获取query string中的参数
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		
		// 首次使用时没有试卷，直接进入新建试题界面
		if (empty($_GPC['op']) && $this->isChoiceLibraryEmpty()) {
			$operation = 'post';
		}	

		if ($operation == 'post') {
			$choice_id = intval($_GPC['choice_id']);
			if (!empty($choice_id)) {
				$item = pdo_fetch("SELECT * FROM ".tablename('quickexam2_choice')." WHERE choice_id =".$choice_id);
				if (empty($item)) {
					message('抱歉，题目不存在或是已经删除！', '', 'error');
				}
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) {
					message('请输入题干');
				}
				if (empty($_GPC['body'])) {
					message('请输入选项！');
				}
				if (empty($_GPC['answer'])) {
					message('请输入答案！');
				}
				$data = array(
					'weid' => $_W['weid'],
					'title' => $_GPC['title'],
					'mark' => intval($_GPC['mark']),
					'body' => $_GPC['body'],
					'answer' => $_GPC['answer'],
					'explain' => $_GPC['explain'],
				);
				if (!empty($choice_id)) {
					pdo_update('quickexam2_choice', $data, array('choice_id' => $choice_id));
				} else {
					pdo_insert('quickexam2_choice', $data);
				}
				message('更新成功！', $this->createWebUrl('choice', array('op' => 'display')), 'success');
			}
		}
		else if ($operation == 'delete') {
			$choice_id = intval($_GPC['choice_id']);
			$row = pdo_fetch("SELECT choice_id FROM ".tablename('quickexam2_choice')." WHERE choice_id = ".$choice_id);
			if (empty($row)) {
				message('抱歉，题目不存在或是已经被删除！');
			}
			pdo_delete('quickexam2_choice', array('choice_id' => $choice_id));
			message('删除成功！', referer(), 'success');
		} else if ($operation == 'display') {
			$condition = '';
			$list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_choice')." WHERE weid = '{$_W['weid']}' $condition ORDER BY choice_id DESC");
		}
		include $this->template('choice');
	}

	private function isChoiceLibraryEmpty() {
		global $_W;
		$result = pdo_fetch("SELECT count(*) as cnt FROM ".tablename('quickexam2_choice')." WHERE weid = '{$_W['weid']}'");
		return ($result['cnt'] <= 0);
	}

	public function doWebPaper() {
		global $_W;
		global $_GPC;
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		
		// 首次使用时没有试卷，直接进入新建试题界面
		if (empty($_GPC['op']) && $this->isPaperLibraryEmpty()) {
			$operation = 'post';
		}

		if ($operation == 'post') {
			$paper_id = intval($_GPC['paper_id']);
			$choice_ids = array();
			if (!empty($paper_id)) {
				$paper = pdo_fetch("SELECT * FROM ".tablename('quickexam2_paper')." WHERE paper_id =".$paper_id);
				if (empty($paper)) {
					message('抱歉，试卷不存在或是已经删除！', '', 'error');
				}
				$choice_ids = iunserializer($paper['choice_ids']);
				$choice_ids_seq = iunserializer($paper['choice_ids_seq']);
			}
			if (checksubmit('submit')) {
				if (empty($_GPC['title'])) {
					message('请输入试卷标题');
				}
				if (empty($_GPC['max_user_cnt'])) {
					message('请输入最多发出多少份试卷！');
				}

				$new_choice_ids = array();
				foreach($_GPC['choice_id'] as $choice_id => $on)
				{
					$new_choice_ids[] = $choice_id;
				}
				$new_choice_ids_seq = array();
				foreach($_GPC['choice_ids_seq'] as $t_cid => $t_seq) {
					// 41_1 - choice 41 is the first one
					// 43_2 - choice 43 is the second one
					$new_choice_ids_seq[$t_cid] = $t_seq;
				}

				$data = array(
					'weid' => $_W['weid'],
					'title' => $_GPC['title'],
					'logo' => $_GPC['logo'],
					'max_user_cnt' => intval($_GPC['max_user_cnt']),
					'max_participate_cnt' => intval($_GPC['max_participate_cnt']),
					'explain' => $_GPC['explain'],
					'redirect_cond' => intval($_GPC['redirect_cond']),
					'credit_award' => intval($_GPC['credit_award']),
					'redirect_url' => $_GPC['redirect_url'],
					'redirect_msg' => $_GPC['redirect_msg'],
					'choice_ids' => iserializer($new_choice_ids),
					'choice_ids_seq' => iserializer($new_choice_ids_seq),
				);
				if (!empty($paper_id)) {
					pdo_update('quickexam2_paper', $data, array('paper_id' => $paper_id));
				} else {
					pdo_insert('quickexam2_paper', $data);
				}
				message('更新成功！', $this->CreateWebUrl('Paper', array('op' => 'display')), 'success');
			}
			$condition = '';
			$choice_list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_choice')." WHERE weid = '{$_W['weid']}' $condition ORDER BY choice_id DESC");
		} else if ($operation == 'delete') { //删除
			$paper_id = intval($_GPC['paper_id']);
			$row = pdo_fetch("SELECT paper_id FROM ".tablename('quickexam2_paper')." WHERE paper_id = ".$paper_id);
			if (empty($row)) {
				message('抱歉，试卷不存在或是已经被删除！');
			}
			pdo_delete('quickexam2_paper', array('paper_id' => $paper_id));
			message('删除成功！', referer(), 'success');
		} else if ($operation == 'display') {
			$condition = '';
			$paper_list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_paper')." WHERE weid = '{$_W['weid']}' $condition ORDER BY paper_id DESC");
			// 计算已经回收的试卷份数
			foreach($paper_list as $key => $item) {
				$done = pdo_fetch("SELECT COUNT(*) as done FROM (SELECT from_user FROM " . tablename('quickexam2_score_record') . "WHERE  weid = '{$_W['weid']}' AND paper_id = {$item['paper_id']}) as a");
				$paper_list[$key]['done'] = $done['done'];
			}
		}
		
		include $this->template('paper');
	}
		
	private function isPaperLibraryEmpty() {
		global $_W;
		$result = pdo_fetch("SELECT count(*) as cnt FROM ".tablename('quickexam2_paper')." WHERE weid = '{$_W['weid']}'");
		return ($result['cnt'] <= 0);
	}

	public function doWebPaperExport() {
		global $_W, $_GPC;
		$operation = empty($_GPC['op']) ? 'export' : $_GPC['op'];
		$paper_id = intval($_GPC['paper_id']);
		$paper = $this->getPaper($paper_id);
		if (empty($paper)) {
			message('抱歉，试卷不存在或是已经被删除！', referer(), 'error');
		}
    $choice_ids = iunserializer($paper['choice_ids']);
    if (count($choice_ids) > 0) {
      $choice_list = pdo_fetchall("SELECT * FROM " .tablename('quickexam2_choice') . " WHERE choice_id IN (". join(',', $choice_ids) .")");
      $choice_list = $this->parseBodyChoices($choice_list);
    }
		include $this->template('paper_export');
	}

	public function doWebMarkExport() {
		global $_W, $_GPC;
		$operation = empty($_GPC['op']) ? 'export' : $_GPC['op'];
		$paper_id = intval($_GPC['paper_id']);
		$paper = $this->getPaper($paper_id);
		if (empty($paper)) {
			message('抱歉，试卷不存在或是已经被删除！', referer(), 'error');
		}
		$records = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_score_record')."as a," . tablename('fans') . " as b WHERE a.weid={$_W['weid']} AND b.weid={$_W['weid']} AND a.from_user=b.from_user AND paper_id = ".$paper_id);

		include $this->template('mark_export');
	}

	// 数据分析功能包括：
	// （1）单卷分析：每张纸卷多少人做题，每个选项的选中率
	// （2）答题分析：每个人做题的答案是什么
	// （3）答题记录：每个做题的人多少分。单卷分析页面中删除单人答题记录
	// （4）试卷列表：显示所有试卷，及其完成度
	public function doWebMarkManagement() {
		global $_W, $_GPC;
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'paper_list';
		if ($operation == 'user_delete') { // 删除单个用户的调研记录
			$record_id = intval($_GPC['record_id']);
			if (empty($record_id)) {
				message('要删除的用户数据不存在', '', 'error');
			}
			pdo_delete('quickexam2_score_record', array('record_id' => $record_id));
			message('删除答题记录成功', referer(), 'success');
		} else if ($operation == 'paper_view') { // 查看指定调研的答题统计情况
			$paper_id = intval($_GPC['paper_id']);
			$paper = $this->getPaper($paper_id);
			$choice_stat = $this->doChoiceStat($paper);
      $choice_ids = iunserializer($paper['choice_ids']);
      if (count($choice_ids) > 0) {
        $choice_list = pdo_fetchall("SELECT * FROM " .tablename('quickexam2_choice') . " WHERE choice_id IN (". join(',', $choice_ids) .")");
        $choice_list = $this->parseBodyChoices($choice_list);
      }
		} else if ($operation == 'user_view') { // ？？
			$paper_id = intval($_GPC['paper_id']);
			$paper = $this->getPaper($paper_id);
			if (empty($paper)) {
				message('抱歉，试卷不存在或是已经被删除！', referer(), 'error');
			}
			$records = pdo_fetchall("SELECT a.from_user as from_user, record_id, b.realname as realname, a.usermark as usermark, b.mobile as mobile, a.createtime as createtime FROM ".tablename('quickexam2_score_record')."as a," . tablename('fans') . " as b WHERE a.weid={$_W['weid']} AND b.weid={$_W['weid']} AND a.from_user=b.from_user AND paper_id = ".$paper_id . " ORDER BY createtime DESC");
		} else if ($operation == 'paper_list') {
			$paper_list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_paper')." WHERE weid = '{$_W['weid']}' ORDER BY paper_id DESC");
			// 计算已经回收的试卷份数
			foreach($paper_list as $key => $item) {
				$done = pdo_fetch("SELECT COUNT(*) as done FROM (SELECT from_user FROM " . tablename('quickexam2_score_record') . "WHERE  weid = '{$_W['weid']}' AND paper_id = {$item['paper_id']} GROUP BY from_user) as a");
				$paper_list[$key]['done'] = $done['done'];
			}
		} else {
			message('未知请求', '', 'error');
		}
		include $this->template('mark_management');
	}

	public function doWebHelp() {
		global $_W;
		include $this->template('help');
	}

	private function doChoiceStat($paper) {
		global $_W;
		$paper_id = $paper['paper_id'];
		$answer_list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_score_record')." WHERE weid={$_W['weid']}  AND paper_id = ".$paper_id);

		$answer_stat = array();
		foreach($answer_list as &$r) {
			$r['user_choices'] = iunserializer( $r['user_choices'] );
			foreach($r['user_choices'] as $id => $choice) {
				foreach($choice as $c) {
					$answer_stat[$id][] = $c;
				}
			}
		}

		foreach($answer_stat as $id => &$value) {
			$value = array_count_values($answer_stat[$id]);
		}
		/*
		foreach($answer_stat as $id => &$value) {
			print_r($id);
			print_r($value);
			echo '<br/>';
		}*/
		return $answer_stat;
	}
	public function doWebPaperResult() {
		global $_W;
		global $_GPC;
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		if ($operation == 'delete') { //删除
			$paper_id = intval($_GPC['paper_id']);
			$row = pdo_fetch("SELECT paper_id FROM ".tablename('quickexam2_paper')." WHERE paper_id = ".$paper_id);
			if (empty($row)) {
				message('抱歉，试卷不存在或是已经被删除！');
			}
			pdo_delete('quickexam2_paper', array('paper_id' => $paper_id));
			message('删除成功！', referer(), 'success');
		} else if ($operation == 'display') {
			$condition = '';
			$paper_list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_paper')." WHERE weid = '{$_W['weid']}' $condition ORDER BY paper_id DESC");
		}
		include $this->template('Paper');
	}

	public function doMobilePaper() {
		global $_W, $_GPC;
		$preview = intval($_GPC['preview']);
		$record_id = intval($_GPC['record_id']);
		$choice_id = intval($_GPC['choice_id']);
		$paper_id = intval($_GPC['paper_id']);
	
		if (!$preview) {
			$this->checkPaperState();	
		}
	
		// 检查用户权限		
		if (!$preview) {
			$this->checkAuth();
			$fans = fans_search($_W['fans']['from_user']);
		} else {
			$fans = fans_search($_GPC['from_user']);
		}

		// support choice and paper
		if ($preview && !empty($choice_id)) {
			
			
			$where = "weid = '{$_W['weid']}' AND choice_id = $choice_id";
			$list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_choice')." WHERE {$where}", array(), "choice_id");

		} else if (!empty($paper_id)) {
			
			
			$where = "weid = '{$_W['weid']}'";
			$paper = pdo_fetch("SELECT * FROM ".tablename('quickexam2_paper'). "WHERE {$where} AND paper_id=$paper_id");
			if (empty($paper)) {
				message('抱歉，试卷不存在或是已经删除！', '', 'error');
			}else {
				$choice_ids_str = join(',', iunserializer($paper['choice_ids']));
        if (!empty($choice_ids_str)) {
          $list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_choice')." WHERE {$where} AND choice_id in ($choice_ids_str)", array(), "choice_id");
        }
			}

			$ids_seq = iunserializer($paper['choice_ids_seq']);
			//按照ids_seq排序试卷
			foreach($list as &$t_elem) {
				$t_elem['order'] = (empty($ids_seq[$t_elem['choice_id']]) ? 0 : $ids_seq[$t_elem['choice_id']]); 
			}

			if ($preview && !empty($record_id)) {
				$record = pdo_fetch("SELECT * FROM ".tablename('quickexam2_score_record')." WHERE weid={$_W['weid']} AND paper_id={$paper_id} AND record_id={$record_id}");
				$user_choices = iunserializer($record['user_choices']);
				foreach($user_choices as $key => $value) {
					$list[$key]['user_choices'] = $value;
				}
			}
			usort($list, "my_quickexam2_display_order_sort");
		} else {
			message('必须指定试卷！', '', 'error');
		}

		// 显示试卷
		$list = $this->parseBodyChoices($list);


		include $this->template('choice');
	}

	public function doMobileMyPaper() {
		global $_W, $_GPC;
		$this->checkAuth();
		// 用户必须输入个人信息后方可继续
		$profile = fans_require($_W['fans']['from_user'], array('realname', 'mobile'));
		$mypaper_list = pdo_fetchall("SELECT * FROM " . tablename('quickexam2_score_record') . " WHERE weid=:weid AND from_user=:from_user ORDER BY createtime DESC",
			array(":weid"=>$_W['weid'], ":from_user"=>$_W['fans']['from_user']));
		$paper_ids = array();
		foreach ($mypaper_list as $p) {
			$paper_ids[] = $p['paper_id'];
		}
		$paper_list = array();
		if (count($paper_ids) > 0) {
			$paper_list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_paper')." WHERE weid={$_W['weid']} AND paper_id in (" . join(',', $paper_ids) . ")",
				array(),
				'paper_id'
			);
			foreach($paper_list as $key => &$item) {
				$item['explain'] = $this->deleteSpace(strip_tags(htmlspecialchars_decode($item['explain'])));
				$item['logo'] = $this->getPicUrl($item['logo']);
			}
		}
		include $this->template('my_paper');
	}


	public function doMobileCover() {
		global $_W, $_GPC;
		//$this->checkAuth();
		$profile = fans_search($_W['fans']['from_user']);
		$paper_list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_paper')." WHERE weid={$_W['weid']}");
		foreach($paper_list as &$item) {
			$item['explain'] = $this->deleteSpace(strip_tags(htmlspecialchars_decode($item['explain'])));
			$item['logo'] = $this->getPicUrl($item['logo']);
		}
		include $this->template('cover');
	}

	private function calcUserMark($userChoices) {
		$usermark = 0;
		if (count($userChoices) <= 0)
			return $usermark;

		$choice_ids = array_keys($userChoices);
		$ref = pdo_fetchAll("SELECT * FROM " . tablename('quickexam2_choice') . " WHERE choice_id in (" . join(',', $choice_ids) . ")", 
			array(), 'choice_id');
		foreach($userChoices as $choice_id => $choice_arr) {
			if (count(array_diff(explode(',', $ref[$choice_id]['answer']) ,  $choice_arr)) > 0) {
				// different answer set, wrong!
			} else {
				$usermark += $ref[$choice_id]['mark'];
			}
		}
		return $usermark;
	}
	
	public function doMobileScoreSubmit() {
		global $_W, $_GPC;


		// 检查人数是否超过上限
		$this->checkAuth();
		$this->checkPaperState();	

		$usermark = $this->calcUserMark($_GPC['choice']);
		$scoreRecord = array(
			'from_user' => $_W['fans']['from_user'],
			'paper_id' =>$_GPC['paper_id'],
			'paper_title' => $_GPC['paper_title'],
			'choice_ids' => iserializer($_GPC['answer']),
			'user_choices' => iserializer($_GPC['choice']),
			'usermark' => $usermark,
			'createtime' => time(),
			'weid' => $_W['weid']);
		pdo_insert($this->table_score, $scoreRecord);

		// URL自动获取逻辑参考~/weixin/source/controller/site/nav.ctrl.php +145
		$paper = $this->getPaper($_GPC['paper_id']);
		if ($paper['redirect_cond'] <= $usermark) {
			if ($paper['credit_award'] > 0) {
				$msg = "本卷得分{$usermark}分<br> 获得{$paper['credit_award']}积分的奖励。";
				$fans = fans_search($_W['fans']['from_user'], array('credit1'));
				fans_update($_W['fans']['from_user'], array('credit1' => $fans['credit1'] + $paper['credit_award'])); 
			} else {
				$msg = "本卷得分{$usermark}分";
			}
		} else {
			message("您的得分是{$usermark}，没有达到{$paper['redirect_cond']}分的及格线。将自动跳转到试题解析页面...",
				$this->createMobileUrl('MyPaper'),
				'error');
		}

		include $this->template('result');
	}

	private function doMobileQuickExam2() {
		//$this->checkAuth();
		global $_W, $_GPC;
		$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
		$choice_id = intval($_GPC['choice_id']);
		if ($operation == 'post') {
			var_dump($_GPC['choice']);
			var_dump($_GPC['answer']);
		} else {
			$where = "weid = '{$_W['weid']}'";
			if (!empty($choice_id)) {
				$where = $where . " AND choice_id = $choice_id";
			}
			$list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_choice')." WHERE {$where}");
			$list = $this->parseBodyChoices($list);
		}
		include $this->template('choice');
	}

	private function getPicUrl($url) {
		global $_W;
		if (empty($url)) {
			$r = $_W['siteroot'] . "/source/modules/quickexam2/images/default_cover.jpg";
		} else {
			$r = strpos($url, 'http://') === FALSE ? $_W['attachurl'] . $url : $url;
		}
		return $r;
	}

	private function checkAuth() {
		global $_W;
		if (empty($_W['fans']['from_user'])) {
			include $this->template('auth');
			exit;
		} else {
			checkauth(); // fallback to org check
		}
	}


	private function getPaper($paper_id) {
		global $_W;
		$paper_id = intval($paper_id);
		$where = "weid = '{$_W['weid']}'  AND paper_id={$paper_id}";
		$sql_paper_info = "SELECT * FROM " . tablename('quickexam2_paper') . "WHERE {$where}";	
		$paper = pdo_fetch($sql_paper_info);
		if (empty($paper)) {
			message('对不起，试卷不存在，可能已经被删除！', '', 'error');
		}
		return $paper;
	}

	private function checkPaperState() {
		global $_W, $_GPC;

		$where = "weid = '{$_W['weid']}'  AND paper_id={$_GPC['paper_id']}";
		$sql_paper_info = "SELECT * FROM " . tablename('quickexam2_paper') . "WHERE {$where}";	
		$sql_answered_count = "SELECT COUNT(*) as done FROM (SELECT from_user FROM " . tablename('quickexam2_score_record') . "WHERE {$where} GROUP BY from_user) as a";
		$sql_check_user_answered = "SELECT count(*) as done FROM ".tablename('quickexam2_score_record'). "WHERE {$where} AND from_user='{$_W['fans']['from_user']}'";
	
		$paper = pdo_fetch($sql_paper_info);
		$stat = pdo_fetch($sql_answered_count);
		$duplicate = pdo_fetch($sql_check_user_answered);

		if (empty($paper)) {
			message('对不起，试卷不存在，可能已经被删除！', '', 'error');
		} else if ($stat['done'] >= $paper['max_user_cnt']) {
			message('您好，本试卷已发放完，达目标人数，顺利结束！', '', 'success');
		} else if ($duplicate['done'] >= $paper['max_participate_cnt']) {
			message("您已经参加过本试卷，无需再次答题。最多允许回答{$paper['max_participate_cnt']}次.<br><a href='" . $this->createMobileUrl('MyPaper') ."'>点击这里</a>进入答题本查看你的答题记录", '', 'success');
		}
	}


	private function parseBodyChoices($choice_list) {
		// 显示试卷
		foreach($choice_list as &$list_item) {
			$options = explode("\n", $list_item['body']);
			foreach($options as $op) {
				$option = array();
				$option['body'] = $op;
				$option['seq'] = substr($op, 0, 1);
				$list_item['options'][] = $option;
			}
		}
		return $choice_list;
	}

	private function deleteSpace($str) {
		$str = trim($str);
		$str = strtr($str,"\t","");
		$str = strtr($str,"\r\n","");
		$str = strtr($str,"\r","");
		$str = strtr($str,"\n","");
		$str = strtr($str," ","");
		$str = str_replace('&nbsp;', "",$str);
		return trim($str);
	}

        public function getCategoryTiles() {
                global $_W;
		$paper_list = pdo_fetchall("SELECT * FROM ".tablename('quickexam2_paper')." WHERE weid={$_W['weid']}");
		$urls[] = array('title' => '我要做题', 'url' => $this->createMobileUrl('Cover'));
		if (!empty($paper_list)) {
			foreach($paper_list as $item) {
				$urls[] = array('title' => $item['title'], 'url' => $this->createMobileUrl('Paper', array("paper_id" => $item['paper_id'])));		
			}
		}
		return $urls;
	}
}
