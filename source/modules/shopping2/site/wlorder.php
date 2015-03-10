<?php
/**
 * 我的订单
 *
 * @author 超级无聊
 * @url
 */	if($_GPC['d']=='checkout'){
			//保存订单
			$cart = pdo_fetchall("SELECT id,goodsid,price,total FROM ".tablename('shopping2_cart')." WHERE  weid = '{$weid}' AND from_user = '{$_W['fans']['from_user']}'");
			if($cart==false){
				$return=array(
					'status'=>0,
					'msg'=>'抱歉，您的购物车里没有任何商品，请先购买！',
					'url'=>$this->createMobileUrl('wlindex'),
				);
				die(json_encode($return));
			}else{

				$totalnum=0;
				$totalprice=0;
				foreach ($cart as $v){
					$_goods=pdo_fetch("SELECT title,total FROM ".tablename('shopping2_goods')." WHERE id = {$v['goodsid']}");
					if($v['total']>$_goods['total'] && $_goods['total']>-1){
						//更改订单
						if($_goods['total']>0){
							pdo_update('shopping2_cart',array('total'=>$_goods['total']),array('id'=>$v['id']));
						}else{
							pdo_delete('shopping2_cart',array('id'=>$v['id']));
						}
						$return=array(
							'status'=>0,
							'msg'=>$_goods['title'].'的库存不足，目前仅有'.$_goods['total'].'件,返回购物车查看。',
						);
						die(json_encode($return));
 					}
					$totalnum=$totalnum+intval($v['total']);		
					$totalprice=$totalprice+(intval($v['total'])*floatval($v['price']));
				}
				//加上快递费
				$totalprice=$totalprice+floatval($_GPC['expressprice']);
	 
				//更新地址库
				if($_GPC['AId']==0){
					//保存新订单
					$data = array(
						'weid' => $weid,
						'from_user' => $_W['fans']['from_user'],
						'username' => $_GPC['username'],
						'tel' => $_GPC['tel'],
						'address' => $_GPC['resideprovince'].$_GPC['residecity'].$_GPC['residedist'].$_GPC['address'],
					);
					pdo_insert('shopping2_address', $data);
					$_GPC['AId'] = pdo_insertid();
					//更新用户表
					fans_update($_W['fans']['from_user'],array('realname'=>$_GPC['username'],'mobile'=>$_GPC['tel'],'address'=>$_GPC['address']));
				}
 				//保存新订单
				$data = array(
						'weid' => $weid,
						'from_user' => $_W['fans']['from_user'],
						'ordersn' => date('md') .sprintf("%04d", $_W['fans']['id']) .random(4, 1),
						'totalnum' => $totalnum,
						'totalprice' => $totalprice,
						'status' => 0,
						'sendtype' => 0,
						'paytype' => intval($_GPC['paytype']),
						'goodstype' =>0,
						'sendtype'=>$_GPC['sendtype'],
						'expressprice'=>$_GPC['expressprice'],						
						'aid'=>$_GPC['AId'],
						'remark'=>$_GPC['Remark'],
						'createtime' => TIMESTAMP,
						'secretid'=>random(4,1)
						//打印状态
						//'print_sta'=>0,
						//'print_usr'=>0,
					);
				pdo_insert('shopping2_order', $data);
				$orderid = pdo_insertid();	
				
				//保存新订单商品
				foreach ($cart as $row) {
						if (empty($row)) {
							continue;
						}
						pdo_insert('shopping2_order_goods', array(
							'weid' => $weid,
							'goodsid' => $row['goodsid'],
							'orderid' => $orderid,
							'total' => $row['total'],
							'createtime' => TIMESTAMP,
						));
					}
				//清空购物车
				pdo_delete('shopping2_cart', array('weid' => $weid, 'from_user' => $_W['fans']['from_user']));
				$return=array(
					'status'=>1,
					'OrderId'=>$orderid,
				);
 
				
				die(json_encode($return));
				//message('提交订单成功，现在跳转至付款页面...', $this->createMobileUrl('pay', array('orderid' => $orderid)), 'success');
			}
		}elseif($_GPC['d']=='payment'){
			//考虑库存 by 超级无聊 2014-2-14
			
			$orderid = intval($_GPC['OrderId']);
			$order = pdo_fetch("SELECT * FROM ".tablename('shopping2_order')." WHERE id = :id", array(':id' => $orderid));
			//获取地址
			include $this->template('wl_payment');
		}