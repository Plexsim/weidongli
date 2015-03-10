<?php
/**
 * 详情
 *
 * 作者:迷失卍国度
 *
 * qq : 15595755
 */
	$goodsid = intval($_GPC['goodsid']);
	$goods = pdo_fetch("SELECT * FROM ".tablename('ishopping_goods')." WHERE weid={$_W['weid']} and  id = {$goodsid}");
	if (empty($goods)) {
		message('抱歉，商品不存在或是已经被删除！');
	}
	include $this -> template('wap_detail');