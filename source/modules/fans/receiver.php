<?php

/**

 * 粉丝管理模块订阅器

 *

 * [52media System]

 *  /forum.php?mod=forumdisplay&fid=36&filter=typeid&typeid=1

 */

defined('IN_IA') or exit('Access Denied');



class FansModuleReceiver extends WeModuleReceiver {

	public function receive() {

		$type = $this->message['type'];

		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看 文档来编写你的代码

		//退订

		if($this->message['event'] == 'unsubscribe') {

			pdo_update('fans', array(

				'follow' => 0,

				'createtime' => TIMESTAMP,

			), array('from_user' => $this->message['fromusername'], 'weid' => $GLOBALS['_W']['weid']));

		} else {

			fans_update($this->message['fromusername'], array(

				'weid' => $GLOBALS['_W']['weid'],

				'follow' => 1,

				'from_user' => $this->message['fromusername'],

				'createtime' => TIMESTAMP,

			));

		}

	}

}

