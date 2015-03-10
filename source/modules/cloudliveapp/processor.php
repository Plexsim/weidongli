<?php
/**
 * @cloudliveapp.com
 */
defined('IN_IA') or exit('Access Denied');

class cloudliveappModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
		$reply = pdo_fetchall("SELECT * FROM ".tablename('cloudliveapp_reply')." WHERE rid = :rid", array(':rid' => $this->rule));
		if (!empty($reply)) {
			foreach ($reply as $row) {
				$cloudliveappids[$row['cloudliveappid']] = $row['cloudliveappid'];
			}
			$cloudliveapp = pdo_fetchall("SELECT id, title, thumb, content FROM ".tablename('cloudliveapp')." WHERE id IN (".implode(',', $cloudliveappids).")", array(), 'id');
			$response = array();
			foreach ($reply as $row) {
				$row = $cloudliveapp[$row['cloudliveappid']];
				$response[] = array(
					'title' => $row['title'],
					'description' => $row['content'],
					'picurl' => $row['thumb'],
					'url' => $this->buildSiteUrl($this->createMobileUrl('detail', array('id' => $row['id']))),
				);
			}
			return $this->respNews($response);
		}
	}
}