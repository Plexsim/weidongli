<?php
/**
 * 2014-2-24
 * 购物车 分类管理 
 * 支持二级分类 来自微动力
 * @author 超级无聊
 * @url
 */
if($_GPC['action']=='test'){
 
	$title="这里是测试平台，给你发送邮件";
	$content="超级无聊祝您生意兴隆，财源广进.";
	//$temp=$this->_sendmail($title,$content);
	if($temp==1){
		message('邮件发送成功，您的邮件设置成功', $this->createWebUrl('Mailset'), 'success');
	}else{
		message('邮件发送成功，您的邮件设置成功,错误原因:'.$temp);
	}
}
if (checksubmit('submit')) {
	$insert=array(
		'weid'=>$_W['weid'],
		'sms_status'=>trim($_GPC['sms_status']),
		'sms_type'=>trim($_GPC['sms_type']),
		'sms_from'=>trim($_GPC['sms_from']),
		'sms_secret'=>trim($_GPC['sms_secret']),
		'sms_phone'=>trim($_GPC['sms_phone']),
		'sms_text'=>trim($_GPC['sms_text']),
		'sms_account'=>trim($_GPC['sms_account']),
		'sms_resgister'=>intval($_GPC['sms_resgister']),
		'sms_customer'=>intval($_GPC['sms_customer']),
		'sms_verifytxt'=>trim($_GPC['sms_verifytxt']),
		'sms_paytxt'=>trim($_GPC['sms_paytxt']),
		'sms_bosstxt'=>trim($_GPC['sms_bosstxt']),
		
 	);
	if (empty($_GPC['id'])) {
		pdo_insert('shopping2_set', $insert);
	} else {
		pdo_update('shopping2_set', $insert, array('weid'=>$_W['weid'],'id' => $_GPC['id']));
	}
	message('短信数据保存成功', $this->createWebUrl('Smsset'), 'success');
}
$set = pdo_fetch("SELECT * FROM ".tablename('shopping2_set')." WHERE weid = :weid", array(':weid' => $_W['weid']));
if($set==false){
	$set=array(
		'id'=>0,
	);
}
if(empty($set['sms_verifytxt'])){
	$set['sms_verifytxt']='您的验证码是：【变量】。请不要把验证码泄露给其他人。如非本人操作，可不用理会！';
}
if(empty($set['sms_text'])){
	$set['sms_text']='您的订单编码：【变量】。如需帮助请联系客服。';
}
if(empty($set['sms_paytxt'])){
	$set['sms_paytxt']='您的订单：【变量】，已于【变量】付款成功。感谢您的购买！';
}
if(empty($set['sms_bosstxt'])){
	$set['sms_bosstxt']='您的订单：【变量】，已于【变量】付款成功。感谢您的购买！';
}
include $this->template('web/smsset');