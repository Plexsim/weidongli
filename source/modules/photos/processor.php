<?php
/**
 * 照片墙模块处理程序
 *
 * @author 珊瑚海
 * @url http://www.vfanm.com/
 */
defined('IN_IA') or exit('Access Denied');

class PhotosModuleProcessor extends WeModuleProcessor {
	public function respond() {
		global $_W,$_GPC;
		$config = $this->module['config'];
		$isdes = isset($config['isdes']) ? $config['isdes'] : 1;
		$isstatus = isset($config['isstatus']) ? $config['isstatus'] : 1;
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微动力文档来编写你的代码
		if (!$this->inContext) {
			if ($this->message['type'] == 'image') {
				$from_user = $this->message['from'];
				$picurl = "photos/".$_W['account']['account']."/{$from_user}".time().".jpg";
				$pic_data = ihttp_get($this->message['picurl']);
				$upload = file_write($picurl,$pic_data['content']);
				$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
				$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
				$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('photos_data')." WHERE weid = '{$_W['weid']}' AND from_user = '{$from_user}' AND time >= '{$beginToday}' AND time <= '{$endToday}'");
				if (($total < $config['picnum']) || ($config['picnum'] == '-1')) {
					if ($isdes == '1') {
						$this->beginContext(1800);
						$_SESSION['picurl'] = $picurl;
						$_SESSION['from'] = $from_user;
						$_SESSION['time'] = time();
						return $this->respText("您的图片我们已经收到啦，请及时回复该图片的描述哦！");
					}else{
						$data = array(
							'from_user' => $from_user,
							'weid' => $_W['weid'],
							'url' => $picurl,
							'description' => '暂无描述',
							'status' => 1 - $isstatus,
							'time' => time(),
						);
						pdo_insert('photos_data',$data);
						return $this->respText("您的图片我们已经收到，谢谢您的使用！");
					}	
				}else{
					return $this->respText("您今天发送的图片已经超过今天的限制，请明天再来");
				}
			}else{
				return $this->respText('参数错误，请联系开发者');
			}
		}else{
			$word = $this->message['content'];
			if ($word == "退出" || $word == "t") {
				$this->endContext();
				return $this->respText('您已回到普通模式');
			}else{
				$picurl = $_SESSION['picurl'];
				$data = array(
					'from_user' => $_SESSION['from'],
					'weid' => $_W['weid'],
					'url' => $_SESSION['picurl'],
					'description' => $word,
					'status' => 1 - $isstatus,
					'time' => $_SESSION['time'],
					);
				pdo_insert('photos_data',$data);
				$this->endContext();
				return $this->respText("您的描述信息我们收到啦，数据我们已经收录，谢谢！点击<a href=".create_url('mobile/module/display', array('name' => 'photos','weid' => $_W['weid'])).">链接</a>可查看照片墙");
			}			
		}
	}
}