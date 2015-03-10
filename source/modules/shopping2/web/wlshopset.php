<?php
/**
 * 2014-2-24
 * 购物车 分类管理 
 * 支持二级分类 来自微动力
 * @author 超级无聊
 * @url
 */
if (checksubmit('submit')) {
	$insert=array(
		'weid'=>$_W['weid'],
		'shop_name'=>$_GPC['shop_name'],
		'thumb'=>implode('|',$_GPC['thumb_url']),
	);
	if (empty($_GPC['id'])) {
		pdo_insert('shopping2_set', $insert);
	} else {
		pdo_update('shopping2_set', $insert, array('id' => $_GPC['id']));
	}
	message('商城数据保存成功', $this->createWebUrl('Shopset'), 'success');
}
$set = pdo_fetch("SELECT * FROM ".tablename('shopping2_set')." WHERE weid = :weid", array(':weid' => $_W['weid']));
if($set==false){
	$set=array(
		'id'=>0,
	);
}else{
	$set['thumbArr']=explode('|',$set['thumb']);	
	unset($set['thumb']);
}
include $this->template('web/shopset');