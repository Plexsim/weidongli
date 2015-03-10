<?php
/**
 * 2014-2-24
 * 购物车 分类管理 
 * 支持二级分类 来自微动力
 * @author 超级无聊
 * @url
 */
$operation = !empty($_GPC['op']) ? $_GPC['op'] : 'display';
if ($operation == 'display') {
	$pindex = max(1, intval($_GPC['page']));
	$psize = 12;
	$condition = '';
	if (!empty($_GPC['keyword'])) {
		$condition .= " AND a.title LIKE '%{$_GPC['keyword']}%'";
	}
	if (!empty($_GPC['cate_2'])) {
		$cid = intval($_GPC['cate_2']);
		$condition .= " AND a.ccate = '{$cid}'";
	} elseif (!empty($_GPC['cate_1'])) {
		$cid = intval($_GPC['cate_1']);
		$condition .= " AND a.pcate = '{$cid}'";
	}

	if (isset($_GPC['status'])) {
		$condition .= " AND a.status = '".intval($_GPC['status'])."'";
	}
	$list = pdo_fetchall("SELECT a.*,b.username,b.tel FROM ".tablename('shopping2_order')." as a left join ".tablename('shopping2_address')." as b on a.aid=b.id WHERE a.weid = '{$_W['weid']}' $condition ORDER BY a.id desc, a.createtime DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
	$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('shopping2_order') . " as a WHERE a.weid = '{$_W['weid']}' $condition");
	$pager = pagination($total, $pindex, $psize);

 	$express = pdo_fetchall("SELECT id,express_name FROM ".tablename('shopping2_express')." WHERE weid = '{$_W['weid']}'",array(),'id');
	$express['0']=array('express_name'=>'未选择');

} elseif ($operation == 'detail') {
	//流程 第一步确认付款 第二步确认订单 第三步，完成订单
	$id = intval($_GPC['id']);

	if (checksubmit('finish')) {
		pdo_update('shopping2_order', array('status' => 3, 'remark' => $_GPC['remark']), array('id' => $id));
		message('订单操作成功！', referer(), 'success');
	}

	if (checksubmit('cancel')) {
		pdo_update('shopping2_order', array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
		message('取消完成订单操作成功！', referer(), 'success');
	}
	
	if (checksubmit('confirm')) {
		$temp=pdo_update('shopping2_order', array('status' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
		if($temp==false){
			message('确认订单操作失败！', referer(), 'error');
		}else{
		
			message('确认订单操作成功！', referer(), 'success');
		}
	}
	if (checksubmit('cancelpay')) {
		pdo_update('shopping2_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
		message('取消订单付款操作成功！', referer(), 'success');
	}

	if (checksubmit('confrimpay')) {
		pdo_update('shopping2_order', array('status' => 2, 'remark' => $_GPC['remark']), array('id' => $id));
		message('确认订单付款操作成功！', referer(), 'success');
	}

	if (checksubmit('close')) {
		pdo_update('shopping2_order', array('status' => -1, 'remark' => $_GPC['remark']), array('id' => $id));
		message('订单关闭操作成功！', referer(), 'success');
	}

	if (checksubmit('open')) {
		pdo_update('shopping2_order', array('status' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
		message('开启订单操作成功！', referer(), 'success');
	}
	if (checksubmit('send')) {
		//给客户发短信
		$temp=pdo_update('shopping2_order', array('sendstatus' => 1, 'remark' => $_GPC['remark']), array('id' => $id));
		if($temp==false){
			message('此订单已确认配送失败，稍后再试!', referer(), 'error');
		}else{
			$set = pdo_fetch("SELECT sms_text,sms_status,sms_account,sms_secret FROM ".tablename('shopping2_set')." WHERE weid = :weid", array(':weid' => $_W['weid']));
			if($set['sms_status']==1){
				//获取客户的手机号码
				$order = pdo_fetch("SELECT a.totalprice,a.ordersn,b.username,b.tel FROM ".tablename('shopping2_order')." as a left join ".tablename('shopping2_address')." as b on a.aid=b.id WHERE a.weid =".$_W['weid']." AND a.id=".$id."");
				 
				if($order!=false){
					$set['sms_text']=str_replace('[sn]',$order['ordersn'],$set['sms_text']);
					$set['sms_text']=str_replace('[name]',$order['username'],$set['sms_text']);
					$set['sms_text']=str_replace('[date]',date('Y-m-d H:i:s'),$set['sms_text']);
					$set['sms_text']=str_replace('[totalprice]',$order['totalprice'],$set['sms_text']);
 					if(strlen($order['tel'])==11){
						$this->_sendsms($set['sms_text'],$order['tel'],$id,$set['sms_account'],$set['sms_secret']);
					}
				}
			}
 			message('此订单已确认配送!', referer(), 'success');
		}
	}
	if (checksubmit('cancelsend')) {
		pdo_update('shopping2_order', array('sendstatus' => 0, 'remark' => $_GPC['remark']), array('id' => $id));
		message('此订单已取消配送!', referer(), 'success');
	}	

	$item = pdo_fetch("SELECT * FROM ".tablename('shopping2_order')." WHERE id = :id", array(':id' => $id));
	$address=pdo_fetch("SELECT * FROM ".tablename('shopping2_address')." WHERE id = :id", array(':id' => $item['aid']));
	$item['user'] = fans_search($item['from_user'], array('realname', 'resideprovince', 'residecity', 'residedist', 'address', 'mobile', 'qq'));
	$goodsid = pdo_fetchall("SELECT goodsid, total FROM ".tablename('shopping2_order_goods')." WHERE orderid = '{$item['id']}'", array(), 'goodsid');
	$goods = pdo_fetchall("SELECT * FROM ".tablename('shopping2_goods')."  WHERE id IN ('".implode("','", array_keys($goodsid))."')");
	
	$express = pdo_fetchall("SELECT id,express_name FROM ".tablename('shopping2_express')." WHERE weid = '{$_W['weid']}'",array(),'id');
	$express['0']=array('express_name'=>'未选择');

	$item['goods'] = $goods;
} elseif ($operation == 'delete') {
	$id = intval($_GPC['id']);
	$order = pdo_fetch("SELECT id  FROM ".tablename('shopping2_order')." WHERE id = '$id' AND weid=".$_W['weid']."");
	if (empty($order)) {
		message('抱歉，订单不存在，或者已经删除！', $this->createWebUrl('order', array('op' => 'display')), 'error');
	}
	//删除订单里面的东西
	pdo_delete('shopping2_order_goods', array('orderid' => $id));
	pdo_delete('shopping2_order', array('id' => $id));
	message('订单删除成功！', $this->createWebUrl('order', array('order' => 'display')), 'success');
} elseif ($operation == 'cart') {
	//
	pdo_query("delete from ".tablename('shopping2_cart')." where weid = ".$weid." AND create_time<".strtotime("+1 week"));
	message('订单删除成功！', $this->createWebUrl('order', array('order' => 'display')), 'success');
}
include $this->template('web/order');