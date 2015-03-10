<?php
/**
 * 微官网模块处理程序
 *
 * @author WeEngine Team
 * @url bbs.b2ctui.com
 */
defined('IN_IA') or exit('Access Denied');

class QuickShare3ModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W;
		$content = $this->message['content'];
		$from_user = $this->message['from'];

    if ($content == '查看积分') {
			$user = pdo_fetch("SELECT track_id as from_user, SUM(credit) as total_credit FROM ".tablename('quickshare3_share_track')." WHERE weid={$_W['weid']} AND track_id='{$from_user}' GROUP BY track_id");
			$fans = fans_search($from_user);
			$user['total_credit'] += 0; // prevent null value
			$response = array(
				'title' => '查积分，换奖品',
				'description' => "您通过转发文章累计获得积分：".$user['total_credit']."分。\n账户中可用于兑奖积分：".$fans['credit1']. "分。\n\n点击兑换奖品>>",
				'picurl' => $_W['siteroot'] . 'source/modules/quickshare3/images/chakanjifen.jpg',
				'url' => create_url('mobile/module', array('weid' => $_W['weid'], 'do' => 'goods', 'name' => 'quickcredit' ))
				);
			return $this->respNews($response);
		} else if ($content == '我的排名') {
			$top_users = pdo_fetchall("SELECT track_id as from_user, SUM(credit) as total_credit FROM ".tablename('quickshare3_share_track')." WHERE weid={$_W['weid']} GROUP BY from_user ORDER BY total_credit DESC LIMIT 0, 1000");
			$top_user_count = count($top_users);
			$user_click = pdo_fetch("SELECT COUNT(*) as click_count FROM ".tablename('quickshare3_share_track'). " WHERE weid={$_W['weid']} AND track_id='{$from_user}'");
			$click_count = 0 + $user_click['click_count'];// prevent null value
			if ($click_count > 0) {
				for ($i = 0; $i < $top_user_count; $i++) {
					if ($top_users[$i]['from_user'] == $from_user) {
						$desc = "亲，你分享的图文一共被点击".$click_count."次，积分排名为第".($i+1)."名。\n\n挣积分，换奖品，点击查看奖品>>";
						$response = array(
							'title' => '查排名，换奖品',
							'description' => $desc,
							'picurl' => $_W['siteroot'] . 'source/modules/quickshare3/images/wodepaiming.jpg',
							'url' => create_url('mobile/module', array('weid' => $_W['weid'], 'do' => 'goods', 'name' => 'quickcredit' ))
						);
						return $this->respNews($response);
						break;
					}
				}
			}
			$desc = "亲，你分享的图文一共被点击了".$click_count."次，积分排名还在千里之外。继续加油哦！\n\n挣积分，换奖品，点击查看奖品>>";
			$response = array(
				'title' => '查排名，换奖品',
				'description' => $desc,
				'picurl' => $_W['siteroot'] . 'source/modules/quickshare3/images/wodepaiming.jpg',
				'url' => create_url('mobile/module', array('weid' => $_W['weid'], 'do' => 'goods', 'name' => 'quickcredit' ))
			);
			return $this->respNews($response);
		}


    
    //这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
		$isfill = pdo_fetchcolumn("SELECT isfill FROM ".tablename('quickshare3_article_reply')." WHERE rid =:rid AND articleid = '0'", array(':rid' => $this->rule));
		$reply = pdo_fetchall("SELECT * FROM ".tablename('quickshare3_article_reply')." WHERE rid = :rid", array(':rid' => $this->rule));
		if (!empty($reply)) {
			foreach ($reply as $row) {
				$ids[$row['articleid']] = $row['articleid'];
			}
			$article = pdo_fetchall("SELECT id, title, thumb, content, description, linkurl FROM ".tablename('quickshare3_article')." WHERE id IN (".implode(',', $ids).")", array(), 'id');
		}
		if ($isfill && ($count = 8 - count($reply)) > 0) {
			$articlefill = pdo_fetchall("SELECT id, title, thumb, content, description, linkurl FROM ".tablename('quickshare3_article')." WHERE weid = '{$_W['weid']}' AND id NOT IN (".implode(',', $ids).") ORDER BY id DESC LIMIT $count", array(), 'id');
			if (!empty($articlefill)) {
				foreach ($articlefill as $row) {
					$article[$row['id']] = $row;
					$reply[]['articleid'] = $row['id'];
				}
				unset($articlefill);
			}
		}
		if (!empty($reply)) {
			$response = array();
			foreach ($reply as $row) {
				$row = $article[$row['articleid']];
				if(!empty($row)) {
					$response[] = array(
						'title' => $row['title'],
						'description' => $row['description'],
						'picurl' => $row['thumb'],
						'url' => $this->buildSiteUrl($this->createMobileUrl('detail', array('id' => $row['id'], 'track_id' => $from_user, 'track_type' => 'click', 'linkurl'=>$row['linkurl']))),
					);
				}
			}
		}
		return $this->respNews($response);
	}
}
