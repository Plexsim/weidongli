<?php
/**
 * 我的订单
 *
 * 作者:迷失卍国度
 *
 * qq : 15595755
 */
    $title = '购物车';

	if($_GPC['d'] == 'checkout'){
	    //保存订单
        $cart = pdo_fetchall("SELECT * FROM ".tablename('ishopping_cart')." WHERE weid = '{$_W['weid']}' AND from_user = '{$_W['fans']['from_user']}'", array(), 'goodsid');//debug
		if($cart == false){
			$return = array(
				'status' => 0,
				'msg' => '抱歉，您的购物车里没有任何商品，请先购买！',
				'url' => $this -> createMobileUrl('wapindex'),
			);
			die(json_encode($return));
		} else {
            $goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total FROM ".tablename('ishopping_goods')." WHERE id IN ('".implode("','", array_keys($cart))."')");
            foreach ($goods as $row) {
                $cart_goods_total = intval($cart[$row['id']]['total']);
                $goods_total = $row['total'];
                if($cart_goods_total <= 0){
                    $return = array(
                        'status' => 0,
                        'msg' => $row['title'].'数量不能为0.',
                        'url' => $this -> createMobileUrl('wapindex')
                    );
                    die(json_encode($return));
                }

                if($cart_goods_total > $goods_total && $goods_total!=-1){
                    $return = array(
                        'status' => 0,
                        'msg' => $row['title'].'库存不足.',
                        'url' => $this -> createMobileUrl('wapindex')
                    );
                    pdo_update('ishopping_cart', array('total' => 1), array('goodsid' => $row['id']));
                    die(json_encode($return));
                }
            }

			$totalnum = 0;
			$totalprice = 0;
			foreach ($cart as $value){
				$totalnum = $totalnum + intval($value['total']);
				$totalprice = $totalprice + (intval($value['total']) * floatval($value['price']));
			}
			//更新地址库
			if($_GPC['AId'] == 0){
				//保存新订单
				$data = array(
					'weid' => $_W['weid'],
					'from_user' => $_W['fans']['from_user'],
					'username' => $_GPC['username'],
					'tel' => $_GPC['tel'],
					'address' => $_GPC['address'],
				);
				pdo_insert('ishopping_address', $data);
				$_GPC['AId'] = pdo_insertid();
			}
			//保存新订单
			$data = array(
				'weid' => $_W['weid'],
				'from_user' => $_W['fans']['from_user'],
				'ordersn' => date('md') .sprintf("%04d", $_W['fans']['id']) .random(4, 1),
				'totalnum' => $totalnum,
				'totalprice' => $totalprice,
				'status' => 0,
				'sendtype' => 0,
				'paytype' => 0,
				'goodstype' => 0,
				'aid' => $_GPC['AId'],
				'remark' => $_GPC['Remark'],
				'createtime' => TIMESTAMP,
			);
			pdo_insert('ishopping_order', $data);
			$orderid = pdo_insertid();
			//保存新订单商品
			foreach ($cart as $row) {
				if (empty($row) || empty($row['total'])) continue;
				pdo_insert('ishopping_order_goods', array(
				    'weid' => $_W['weid'],
					'goodsid' => $row['goodsid'],
					'orderid' => $orderid,
					'total' => $row['total'],
					'createtime' => TIMESTAMP,
				));
			}

			//清空购物车
			pdo_delete('ishopping_cart', array('weid' => $_W['weid'], 'from_user' => $_W['fans']['from_user']));
			$return = array('status' => 1, 'OrderId' => $orderid,);

            //变更商品库存
            if (!empty($goods)) {
                foreach ($goods as $row) {
                    $goods_total = intval($cart[$row['id']]['total']);
                    if ($goods_total <= 0) {
                        continue;
                    }
                    pdo_query("UPDATE ".tablename('ishopping_goods')." SET total = :total WHERE id = :id AND total > 0", array(':total' => $row['total'] - $cart[$row['id']]['total'], ':id' => $row['id']));
                }
            }
            //debug
            //$result = ihttp_email('15595755@qq.com', '订单提醒', '您有新订单哦！');
			die(json_encode($return));
			//message('提交订单成功，现在跳转至付款页面...', $this->createMobileUrl('pay', array('orderid' => $orderid)), 'success');
		}
	} elseif ($_GPC['d'] == 'payment'){
		$orderid = intval($_GPC['OrderId']);
		$order = pdo_fetch("SELECT * FROM ".tablename('ishopping_order')." WHERE id = :id", array(':id' => $orderid));
        $setting = pdo_fetch("SELECT * FROM ".tablename('ishopping_setting')." WHERE weid = :weid", array(':weid' => $_W['weid']));
		//获取地址
		include $this->template('wap_payment');
	}