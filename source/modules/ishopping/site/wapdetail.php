<?php
/**
 * 商品详细信息
 *
 * 作者:迷失卍国度
 *
 * qq : 15595755
 */
$goodsid = intval($_GPC['goodsid']);
$goods = pdo_fetch("SELECT * FROM ".tablename('ishopping_goods')." WHERE weid={$_W['weid']} and  id = {$goodsid}");
if (empty($goods)) {
	message('抱歉，商品不存在或是已经被删除！');
}else{
	if(!empty($goods['thumb_url'])){
		$goods['thumbArr'] = explode('|',$goods['thumb_url']);
		array_unshift($goods['thumbArr'],$_W['attachurl'].$goods['thumb']);
	}else{
		$goods['thumbArr'] = array($_W['attachurl'].$goods['thumb']);
	}
}
$title = $goods['title'];
pdo_update('ishopping_goods',array('hits'=>$goods['hits']+1),array('id'=>$goods['id']));
include $this->template('wap_detail');