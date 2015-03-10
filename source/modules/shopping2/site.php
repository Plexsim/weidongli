<?php
/**
 * 微商城模块微站定义
 *
 * @author 更多模块请浏览bbs.b2ctui.com
 * @url
 */

defined('IN_IA') or exit('Access Denied');
class Shopping2ModuleSite extends WeModuleSite {
	public function __construct(){

	}
	
	//后台程序 web文件夹下
	public function __web($f_name){
		global $_W,$_GPC;
		checklogin();
		$weid=$_W['weid'];
		//每个页面都要用的公共信息.后期考虑用缓存2014-2-7
		include_once  'web/wl'.strtolower(substr($f_name,5)).'.php';
	}
	//后台-分类管理
	public function doWebCategory() {
 		$this->__web(__FUNCTION__);
	}
	//后台-商品管理
	public function doWebGoods() {
		$this->__web(__FUNCTION__);
	}
 	//后台-订单管理
	public function doWebOrder() {
		$this->__web(__FUNCTION__);
	}
 	//后台-物流管理
	public function doWebexpress(){
		$this->__web(__FUNCTION__);
	}
 	//后台-基本设置
	public function doWebShopset(){
		$this->__web(__FUNCTION__);
	}
 	//后台-邮件功能设置
	public function doWebmailset(){
		$this->__web(__FUNCTION__);
	}	
 	//后台-打印机功能设置
	public function doWebprintset(){
		$this->__web(__FUNCTION__);
	}		
 	//后台-短信参数功能设置
	public function doWebsmsset(){
		$this->__web(__FUNCTION__);
	}	 
	public function doWebPrint(){
		global $_W,$_GPC;
 		$weid=$_W['weid'];
		include_once  'web/wlprint.php';
	}
 
	public function doWebDeleteImage() {
		global $_GPC, $_W;
		$setting = $_W['account']['modules'][$this->_saveing_params['mid']]['config'];
		$setting['picurl'] = '';
		$this->saveSettings($setting);
		message('删除图片成功！', '', 'success');

	}
	
	//手机端 前台
	public function __comm($f_name){
		global $_W,$_GPC;

		//2014-2-26 实现wap页登录
		//$this->checkAuth();
		//获取的方式分2种，url的openid，或者$_W['fans'] 后者优先
		
		$weid=isset($_W['weid'])?$_W['weid']:$_GPC['weid'];
		if(empty($weid)){message('参数错误，进入微商城');}
		$from=isset($_W['fans']['from_user'])?$_W['fans']['from_user']:$_GPC['openid'];
		
		
		$subcp = $_GPC['subcp']?$_GPC['subcp']:NULL;
		//每个页面都要用的公共信息.后期考虑用缓存2014-2-7
		$category = pdo_fetchall("SELECT * FROM ".tablename('shopping2_category')." WHERE weid = '{$_W['weid']}' ORDER BY parentid ASC, displayorder DESC");
		foreach ($category as $index => $row) {
			if (!empty($row['parentid'])){
				$children[$row['parentid']][] = $row;
				unset($category[$index]);
			}
		}		
		$title='微商城';
		include_once  'site/'.strtolower(substr($f_name,8)).'.php';
	}
	//商城首页
	public function doMobilewlindex(){	
		$this->__comm(__FUNCTION__);
	}
	//商品列表
	public function doMobilewllist(){
		$this->__comm(__FUNCTION__);
	}
 
	//商品列表
	public function doMobilewldetail() {
		$this->__comm(__FUNCTION__);
	}
	//商品详情
	public function doMobilewldescription() {
		$this->__comm(__FUNCTION__);
	}
			
	//购物车
	public function doMobilewlcart(){
		$this->__comm(__FUNCTION__);
	}
	//提交订单
	public function doMobilewlorder(){
		$this->__comm(__FUNCTION__);	
	}
	//我的订单
	public function doMobilewlmember(){
		$this->__comm(__FUNCTION__);
	}
	//注册
	public function doMobilewllogin(){
		$this->__comm(__FUNCTION__);
	}
	//注册
	public function doMobilewlregister(){
		$this->__comm(__FUNCTION__);
	}	
	 
	
	public function doMobilewlpayment(){
		//回调地址http://2014.mmghome.com/payment/alipay/return.php?out_trade_no=1%3A2&request_token=requestToken&result=success&trade_no=2014020808163727&sign=9dadbf4ec093526f436fd6bcee0f5293&sign_type=MD5
		global $_W, $_GPC;
		$this->checkAuth();
		
		$orderid = intval($_GPC['orderid']);
		//考虑看库存
		$temp=$this->_checkstock($orderid);
		if($temp==false){
			message('订单中某些产品库存不足，订单已取消，请联系客服。', $this->createMobileUrl('wlmember',array('weid'=>$_GET['weid'])), 'error');
		}
		//更新付款方式
		pdo_update('shopping2_order', array('paytype' => $_GPC['paytype']), array('id' => $orderid));
		
		$order = pdo_fetch("SELECT * FROM ".tablename('shopping2_order')." WHERE id = :id", array(':id' => $orderid));
		if ($order['status'] != '0') {
			message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('wlorder'), 'error');
		}
		if($_GPC['paytype']==3){
			$this->_assist(2,$orderid);
			//选择货到付款，跳转到会员页面
			message('您选择货到付款，您的订单我们正在处理中！', $this->createMobileUrl('wlmember'));
		}
		if (checksubmit('paytype1')) {
			if ($order['paytype'] != 1) {
				message('抱歉，您的支付方式不正确，请重新提交订单！', $this->createMobileUrl('wlorder'), 'error');
			}
			if ($_W['fans']['credit2'] < $order['totalprice']) {
				message('抱歉，您帐户的余额不够支付该订单，请充值！', create_url('mobile/module/charge', array('name' => 'member', 'weid' => $_W['weid'])), 'error');
			}
			if (pdo_update('card_members', array('credit2' => $profile['credit2'] - $order['totalprice']), array('from_user' => $_W['fans']['from_user']))) {
				pdo_update('shopping2_order', array('status' => 2), array('id' => $orderid));
				message('余额付款成功！', $this->createMobileUrl('wlorder'), 'success');
			} else {
				message('余额付款失败，请重试！', $this->createMobileUrl('wlorder'), 'error');
			}
		}
		if (checksubmit()) {
			$params['tid'] = $orderid;
			$params['user'] = $_W['fans']['from_user'];
			$params['fee'] = $order['totalprice'];
			$params['title'] = $_W['account']['name'] . "商城订单";
			$params['ordersn'] = $order['ordersn'];
			$params['virtual'] = $order['goodstype'] == 2 ? true : false;			
			
			$bootstrap_type = 3;
			include $this->template('header');
			$this->pay($params);
			include $this->template('footerbar');

		}
		include $this->template('pay');
	}
	public function doMobilelist() {
		global $_GPC, $_W;
		$this->checkAuth();
		$pindex = max(1, intval($_GPC['page']));
		$psize = 20;
		$condition = '';
		if (!empty($_GPC['ccate'])) {
			$cid = intval($_GPC['ccate']);
			$condition .= " AND ccate = '{$cid}'";
		} elseif (!empty($_GPC['pcate'])) {
			$cid = intval($_GPC['pcate']);
			$condition .= " AND pcate = '{$cid}'";
		}
		$children = array();
		$category = pdo_fetchall("SELECT * FROM ".tablename('shopping2_category')." WHERE weid = '{$_W['weid']}' ORDER BY parentid ASC, displayorder DESC");
		foreach ($category as $index => $row) {
			if (!empty($row['parentid'])){
				$children[$row['parentid']][] = $row;
				unset($category[$index]);
			}
		}
		$list = pdo_fetchall("SELECT * FROM ".tablename('shopping2_goods')." WHERE weid = '{$_W['weid']}' AND status = '1' $condition ORDER BY displayorder DESC, id DESC LIMIT ".($pindex - 1) * $psize.','.$psize);
		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('shopping2_goods') . " WHERE weid = '{$_W['weid']}' AND status = '1' $condition");
		$pager = pagination($total, $pindex, $psize, $url = '', $context = array('before' => 0, 'after' => 0, 'ajaxcallback' => ''));
		include $this->template('list');
	}

	public function doMobileUpdateCart() {

		global $_GPC, $_W;

		$result = array('status' => 0, 'message' => '');

		$operation = $_GPC['op'];

		$goodsid = intval($_GPC['goodsid']);
		$goods = pdo_fetch("SELECT id, type, total FROM ".tablename('shopping2_goods')." WHERE id = :id", array(':id' => $goodsid));
		if (empty($goods)) {
			$result['message'] = '抱歉，该商品不存在或是已经被删除！';
			message($result, '', 'ajax');
		}

		if ($goods['total'] == 0) {
			$result['message'] = '抱歉，该商品库存不足！';
			message($result, '', 'ajax');
		}

		if ($goods['type'] == '1') {
			if (pdo_fetchcolumn("SELECT id, total FROM ".tablename('shopping2_cart')." WHERE from_user = :from_user AND weid = '{$_W['weid']}' AND goodstype = '2'", array(':from_user' => $_W['fans']['from_user']))) {
				$result['message'] = '抱歉，虚拟物品与实体物品请分开购买！';
				message($result, '', 'ajax');
			}

		} elseif ($goods['type'] == '2') {
			if (pdo_fetchcolumn("SELECT id, total FROM ".tablename('shopping2_cart')." WHERE from_user = :from_user AND weid = '{$_W['weid']}' AND goodstype = '1'", array(':from_user' => $_W['fans']['from_user']))) {
				$result['message'] = '抱歉，虚拟物品与实体物品请分开购买！';
				message($result, '', 'ajax');
			}
		}

		$row = pdo_fetch("SELECT id, total FROM ".tablename('shopping2_cart')." WHERE from_user = :from_user AND weid = '{$_W['weid']}' AND goodsid = :goodsid", array(':from_user' => $_W['fans']['from_user'], ':goodsid' => $goodsid));
		if (empty($row['id'])) {
			if ($operation == 'reduce') {
				$result['message'] = '您并没有购买此商品！';
				$result['total'] = 0;
				message($result, '', 'ajax');
			}

			$data = array(
				'weid' => $_W['weid'],
				'goodsid' => $goodsid,
				'goodstype' => $goods['type'],
				'from_user' => $_W['fans']['from_user'],
				'total' => '1',
			);
			pdo_insert('shopping2_cart', $data);
		} else {
			$row['total'] = $operation == 'reduce' ? ($row['total'] - 1) : ($row['total'] + 1);
			if ($goods['total'] > -1 && $row['total'] > $goods['total']) {
				$result['message'] = '抱歉，该商品库存不足！';
				message($result, '', 'ajax');
			}
			if (empty($row['total'])) {
				pdo_delete('shopping2_cart', array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['weid'], 'goodsid' => $goodsid));
			} else {
				$data = array(
					'total' => $row['total'],
				);
				pdo_update('shopping2_cart', $data, array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['weid'], 'goodsid' => $goodsid));
			}
		}
		$result['status'] = 1;
		$result['message'] = '商品数据更新成功！';
		$result['total'] = intval($data['total']);
		message($result, '', 'ajax');

	}

	public function doMobileMyCart() {
		global $_W, $_GPC;
		$this->checkAuth();
		if (checksubmit('submit')) {
			$goodstype = intval($_GPC['goodstype']) ? intval($_GPC['goodstype']) : 1;
			$cart = pdo_fetchall("SELECT * FROM ".tablename('shopping2_cart')." WHERE goodstype = '{$goodstype}' AND weid = '{$_W['weid']}' AND from_user = '{$_W['fans']['from_user']}'", array(), 'goodsid');
			if (!empty($cart)) {
				$goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit, total FROM ".tablename('shopping2_goods')." WHERE id IN ('".implode("','", array_keys($cart))."')");
				if (!empty($goods)) {
					foreach ($goods as $row) {
						if (empty($cart[$row['id']]['total'])) {
							continue;
						}
						if ($row['total'] != -1 && $row['total'] < $cart[$row['id']]['total']) {
							message('抱歉，“'.$row['title'].'”此商品库存不足！', $this->createMobileUrl('mycart'), 'error');
						}
						$price += (floatval($row['marketprice']) * intval($cart[$row['id']]['total']));
					}
				}
				$data = array(
					'weid' => $_W['weid'],
					'from_user' => $_W['fans']['from_user'],
					'ordersn' => date('md') . random(4, 1),
					'totalprice' => $price,
					'status' => 0,
					'sendtype' => intval($_GPC['sendtype']),
					'paytype' => intval($_GPC['paytype']),
					'goodstype' => intval($_GPC['goodstype']),
					'createtime' => TIMESTAMP,
				);

				pdo_insert('shopping2_order', $data);

				$orderid = pdo_insertid();

				//插入订单商品

				foreach ($goods as $row) {
					if (empty($row)) {
						continue;
					}
					pdo_insert('shopping2_order_goods', array(
						'weid' => $_W['weid'],
						'goodsid' => $row['id'],
						'orderid' => $orderid,
						'total' => $cart[$row['id']]['total'],
						'createtime' => TIMESTAMP,
					));
				}

				//清空购物车
				pdo_delete('shopping2_cart', array('weid' => $_W['weid'], 'from_user' => $_W['fans']['from_user']));
				//变更商品库存
				if (!empty($goods)) {
					foreach ($goods as $row) {
						if (empty($cart[$row['id']]['total'])) {
							continue;
						}
						pdo_query("UPDATE ".tablename('shopping2_goods')." SET total = :total WHERE id = :id AND total > 0", array(':total' => $row['total'] - $cart[$row['id']]['total'], ':id' => $row['id']));
					}
				}

				//更新粉丝资料

				fans_update($_W['fans']['from_user'], array(
					'realname' => $_GPC['realname'],
					'resideprovince' => $_GPC['resideprovince'],
					'residecity' => $_GPC['residecity'],
					'residedist' => $_GPC['residedist'],
					'address' => $_GPC['address'],
					'mobile' => $_GPC['mobile'],
					'qq' => $_GPC['qq'],
				));
				message('提交订单成功，现在跳转至付款页面...', $this->createMobileUrl('pay', array('orderid' => $orderid)), 'success');
			} else {
				message('抱歉，您的购物车里没有任何商品，请先购买！', $this->createMobileUrl('list'), 'error');
			}
		}

		$goodstype = 1;

		$cart = pdo_fetchall("SELECT * FROM ".tablename('shopping2_cart')." WHERE goodstype = '{$goodstype}' AND weid = '{$_W['weid']}' AND from_user = '{$_W['fans']['from_user']}'", array(), 'goodsid');
		if (empty($cart)) {
			$goodstype = 2;
			$cart = pdo_fetchall("SELECT * FROM ".tablename('shopping2_cart')." WHERE goodstype = '{$goodstype}' AND weid = '{$_W['weid']}' AND from_user = '{$_W['fans']['from_user']}'", array(), 'goodsid');
		}
		if (!empty($cart)) {
			$goods = pdo_fetchall("SELECT id, title, thumb, marketprice, unit FROM ".tablename('shopping2_goods')." WHERE id IN ('".implode("','", array_keys($cart))."')");
		}

		if (empty($goods)) {

			message('抱歉，您的购物车里没有任何商品，请先购买！', $this->createMobileUrl('list'), 'error');

		}

		$profile = fans_search($_W['fans']['from_user'], array('realname', 'resideprovince', 'residecity', 'residedist', 'address', 'mobile', 'qq'));

		include $this->template('cart');

	}

	public function doMobilePay() {
		global $_W, $_GPC;
		$this->checkAuth();
		$orderid = intval($_GPC['orderid']);
		$order = pdo_fetch("SELECT * FROM ".tablename('shopping2_order')." WHERE id = :id", array(':id' => $orderid));
		if ($order['status'] != '0') {
			message('抱歉，您的订单已经付款或是被关闭，请重新进入付款！', $this->createMobileUrl('myorder'), 'error');
		}
		if (checksubmit('paytype1')) {
			if ($order['paytype'] != 1) {
				message('抱歉，您的支付方式不正确，请重新提交订单！', $this->createMobileUrl('myorder'), 'error');
			}
			if ($_W['fans']['credit2'] < $order['totalprice']) {
				message('抱歉，您帐户的余额不够支付该订单，请充值！', create_url('mobile/module/charge', array('name' => 'member', 'weid' => $_W['weid'])), 'error');
			}
			if (pdo_update('card_members', array('credit2' => $profile['credit2'] - $order['totalprice']), array('from_user' => $_W['fans']['from_user']))) {
				pdo_update('shopping2_order', array('status' => 1), array('id' => $orderid));
				message('余额付款成功！', $this->createMobileUrl('myorder'), 'success');
			} else {
				message('余额付款失败，请重试！', $this->createMobileUrl('myorder'), 'error');

			}

		}

		if (checksubmit()) {
			$params['tid'] = $orderid;
			$params['user'] = $_W['fans']['from_user'];
			$params['fee'] = $order['totalprice'];;
			$params['title'] = $_W['account']['name'] . "商城订单{$order['ordersn']}";
			$this->pay($params);
		}
		include $this->template('pay');
	}

	public function doMobileClear() {

		global $_W, $_GPC;

		$this->checkAuth();

		//清空购物车

		pdo_delete('shopping2_cart', array('weid' => $_W['weid'], 'from_user' => $_W['fans']['from_user']));

		message('清空购物车成功！', $this->createMobileUrl('list'), 'success');

	}

	public function doMobileMyOrder() {

		global $_W, $_GPC;

		$this->checkAuth();

		$pindex = max(1, intval($_GPC['page']));

		$psize = 20;

		$list = pdo_fetchall("SELECT * FROM ".tablename('shopping2_order')." WHERE weid = '{$_W['weid']}' AND from_user = '{$_W['fans']['from_user']}' ORDER BY id DESC LIMIT ".($pindex - 1) * $psize.','.$psize, array(), 'id');

		$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('shopping2_order') . " WHERE weid = '{$_W['weid']}' AND from_user = '{$_W['fans']['from_user']}'");

		$pager = pagination($total, $pindex, $psize);



		if (!empty($list)) {

			foreach ($list as &$row) {

				$goodsid = pdo_fetchall("SELECT goodsid FROM ".tablename('shopping2_order_goods')." WHERE orderid = '{$row['id']}'", array(), 'goodsid');

				$goods = pdo_fetchall("SELECT id, title, thumb, unit, marketprice FROM ".tablename('shopping2_goods')."  WHERE id IN ('".implode("','", array_keys($goodsid))."')");

				$row['goods'] = $goods;

			}

		}

		include $this->template('order');

	}


	public function doMobileDetail() {

		global $_W, $_GPC;

		$this->checkAuth();

		$goodsid = intval($_GPC['id']);

		$goods = pdo_fetch("SELECT * FROM ".tablename('shopping2_goods')." WHERE id = :id", array(':id' => $goodsid));

		if (empty($goods)) {

			message('抱歉，商品不存在或是已经被删除！');
		}

		if (checksubmit('submit')) {

			$_GPC['count'] = intval($_GPC['count']);

			if ($goods['total'] > -1 && $goods['total'] < $_GPC['count']) {

				message('抱歉，该商品库存不足！');

			}

			$row = pdo_fetch("SELECT id, total FROM ".tablename('shopping2_cart')." WHERE from_user = :from_user AND weid = '{$_W['weid']}' AND goodsid = :goodsid", array(':from_user' => $_W['fans']['from_user'], ':goodsid' => $goodsid));
			if (empty($row['id'])) {
				$data = array(
					'weid' => $_W['weid'],
					'goodsid' => $goodsid,
					'goodstype' => $goods['type'],
					'from_user' => $_W['fans']['from_user'],
					'total' => $_GPC['count'],
				);
				pdo_insert('shopping2_cart', $data);
			} else {
				$data = array(
					'total' => $row['total'] + $_GPC['count'],
				);
				if ($goods['total'] > -1 && $goods['total'] < $data['total']) {
					message('抱歉，该商品库存不足！');
				}
				pdo_update('shopping2_cart', $data, array('from_user' => $_W['fans']['from_user'], 'weid' => $_W['weid'], 'goodsid' => $goodsid));
			}
			message('添加商品成功，请去“我的购物车”提交订单！', $this->createMobileUrl('list'), 'success');
		}

		include $this->template('detail');

	}
	public function checklogin($_from){
		global $_GPC;
		$_weid=$_GPC['weid'];
		if(empty($_weid)){
			message('非法入口，登录错误');
			return false;
		}
		if(empty($_from)){
			$this->doMobilewllogin();
		}else{
			$user = pdo_fetch("SELECT id FROM ".tablename('shopping2_fans')." WHERE weid = '{$_weid}'  AND from_user='".$_from."'");
			if($user==false){
				//提示绑定信息
				$this->doMobilewllogin();
			}else{
				return true;
			}

		}
	}

	private function checkAuth() {

		global $_W;
		if (empty($_W['fans']['from_user'])) {

			message('非法访问，请重新点击链接进入个人中心！');

		}

	}


	public function payResult($params) {
		
		$order=pdo_fetch("SELECT status FROM ".tablename('shopping2_order')." WHERE id = {$params['tid']}");
		if($order['status']!=2){
			//计算库存
			$this->_inventory($params['tid']);
			$this->_assist(2,$params['tid']);
			
		}
		pdo_update('shopping2_order', array('status' => 2), array('id' => $params['tid']));
		
		if ($params['from'] == 'return') {
			message('支付成功！', '../../' . $this->createMobileUrl('wlmember'), 'success');
		}

	}

	//更新库存
	public function _inventory($_oid,$_do='reduce'){
 		$_goodslist = pdo_fetchall("SELECT * FROM ".tablename('shopping2_order_goods')." WHERE orderid = {$_oid}");
		
		foreach($_goodslist as $row){
			$_goods=pdo_fetch("SELECT total,sellnums FROM ".tablename('shopping2_goods')." WHERE id = {$row['goodsid']}");
			if($_goods['total']<0){
				pdo_update('shopping2_goods',array('sellnums'=>$_goods['sellnums']+$row['total']),array('id'=>$row['goodsid']));
			}else{
				$temp=pdo_update('shopping2_goods',array('sellnums'=>($_goods['sellnums']+$row['total']),'total'=>($_goods['total']-$row['total'])),array('id'=>$row['goodsid']));
			}
		}
	}	
	//检查库存
	public function _checkstock($_oid){
		$return =true;
 		$_goodslist = pdo_fetchall("SELECT * FROM ".tablename('shopping2_order_goods')." WHERE orderid = {$_oid}");
		$_nostock=array();
		foreach($_goodslist as $row){
			$_goods=pdo_fetch("SELECT title,total,sellnums FROM ".tablename('shopping2_goods')." WHERE id = {$row['goodsid']}");
			if($row['total']>$_goods['total']&& $_goods['total']>-1){
				//更改订单
				pdo_update('shopping2_order',array('status'=>-1),array('id'=>$_oid));
				message($_goods['title'].'的库存不足，目前仅有'.$_goods['total'].'件,订单取消，请联系客服。',$this->createMobileUrl('wlmember',array('weid'=>$_GET['weid'])),'error');				
 				$return =false;
			}
		}
		return true;
	}	

	//$_status=1 确认订单，$_status=2 付款，
	public function _assist($_status=0,$_oid){
		global $_W;
		$set = pdo_fetch("SELECT * FROM ".tablename('shopping2_set')." WHERE weid = :weid", array(':weid' => $_W['weid']));
		if($set==false){
			return '';
		}
		$order = pdo_fetch("SELECT id,secretid,paytype FROM ".tablename('shopping2_order')." WHERE  id={$_oid}");
		$txt="您有新订单,总价".$order['totalprice']."详情".$_W['siteroot'].$this->createMobileUrl('show',array('orderid'=>$order['id'],'secretid'=>$order['secretid']));
		//辅助系统，发邮件
		$this->_sendmail('您有新的订单了,订单号:'.$_oid,$txt);
		//辅助系统，发短信
		//发送短信内容
		//尊敬的商户，您有一条新订单，链接地址mobile.php?act=module&name=shopping2&do=show&weid=1&orderid=23&secretid=4261
		//验证是否开启
		if($set['sms_status']==1 && !empty($set['sms_phone'])){
			//模板
			if(empty($set['sms_bosstxt'])){
				$set['sms_bosstxt']='您的订单：[sn]，已于[date]付款成功。感谢您的购买！';
			}
			//替换rnd的code 和phone的电话
 			$set['sms_bosstxt']=str_replace('[sn]',$_oid,$set['sms_bosstxt']);
			//1为余额，2为在线，3为到付
			if($order['paytype']==1){
				$order['paystr']='余已';
			}elseif($order['paytype']==2){
				$order['paystr']='在已';
			}elseif($order['paytype']==3){
				$order['paystr']='货未';
			}else{
				$order['paystr']='其他';
			}
 			$set['sms_bosstxt']=str_replace('[date]',date('h-d H:i').'['.$order['paystr'].']',$set['sms_bosstxt']);

			$temp=$this->_sendsms($set['sms_bosstxt'],$set['sms_phone'],0,$set['sms_account'],$set['sms_secret']);
			
		}  
		
		//辅助系统，打印
		if($_status==2 && $set['print_status']==1){
			//付款完成，然后开启打印的时候
			//更改订单状态 
			pdo_update('shopping2_order',array('print_sta'=>-1),array('id'=>$_oid));
		}
	}
	public function _sendsms($_txt,$_phone,$_oid=0,$_uid="",$_key=""){
		//http://sms.webchinese.cn/web_api/SMS/?Action=SMS_Num&Uid=xmeimei&Key=e3221e82955cfea34f3c&smsMob=手机号码&smsText=短信内容"
		global $_W;
		if(empty($_txt)||empty($_phone)){
			return '';
		}
	 	if(empty($_uid) || empty($_key) ){
			$sms = pdo_fetch("SELECT sms_account,sms_secret FROM ".tablename('shopping2_set')." WHERE weid = :weid" , array(':weid' => $_W['weid']));
			if($sms==false){
				return '';
			}else{
				$_uid=$mail['sms_account'];
				$_key=$mail['sms_secret'];
			}
		} 
		if(empty($_uid) ||empty($_key)){
			return '';
		}
		//$sms_url="http://sms.webchinese.cn/web_api/SMS/?Action=SMS_Num&Uid=xmeimei&Key=e3221e82955cfea34f3c&smsMob=".$_phone."&smsText=".$_txt;
		$target = "http://www.dxton.com/webservice/sms.asmx/Submit";
		//替换成自己的测试账号,参数顺序和wenservice对应
		$post_data = array(
			"account"=>$_uid,
			"password"=>$_key,
			"mobile"=>$_phone,
			"content"=>$_txt,
		);
  		$result=ihttp_request($target,$post_data);
		if($result['code']=='200'){
			$obj = simplexml_load_string($result['content'], 'SimpleXMLElement', LIBXML_NOCDATA);
			if($obj instanceof SimpleXMLElement) {
				$result['result'] = strval($obj->result);
				$result['message'] = strval($obj->message);
			} 		
		}
 
		$return['sms']=$result['message'];
 		if($_oid>0){
			if(empty($result['message'])){
				$result['message']='已经发送';
			}
			//记录订单发送状态
			pdo_update('shopping2_order',array('sms_sta'=>$result['message']),array('id'=>$_oid));
		}
		return true;
	}
	public function _sendmail($_title='测试标题',$_content='测试内容',$_tomail="",$_Host="",$_Username="",$_Password=""){
		global $_W;
		//获取系统中的邮件资料
		if(empty($_Password) || empty($_Username) ){
			$mail = pdo_fetch("SELECT mail_smtp,mail_user,mail_psw,mail_to,mail_status FROM ".tablename('shopping2_set')." WHERE weid = :weid" , array(':weid' => $_W['weid']));
			if($mail['mail_status']==0){
				return '后台发送邮件功能未开启';
			}
			if($mail!=false){
				$_Host=$mail['mail_smtp'];
				$_Username=$mail['mail_user'];
				$_Password=$mail['mail_psw'];
				$_tomail=$mail['mail_to'];
			}
		}
		if(empty($_Password) || empty($_Username) ){
			$_Host="smtp.163.com";
			$_Username="we7cc123@163.com";
			$_Password="11qqaazz";
			$_tomail="a40039885@qq.com";
		}
		if(trim($_Host)=="smtp.qq.com"){
			$_Host="ssl://smtp.qq.com";
			$_Port = 465;
			$_Authmode= 1;			
		}else{
			$_Port = 25;
		}
		
		if ($_Authmode==1) {
			if (!extension_loaded('openssl')) {
				return '请开启 php_openssl 扩展！';
			}
		}
		
		include_once 'class/class.phpmailer.php';
		try {
			$mail = new PHPMailer(true); //New instance, with exceptions enabled
			$body			  =$_content;
			$body             = preg_replace('/\\\\/','', $body); //Strip backslashes

			$mail->IsSMTP();       
			$mail->Charset='UTF-8';			// tell the class to use SMTP
			$mail->SMTPAuth   = true;                  // enable SMTP authentication
			$mail->Port       = $_Port;                    // set the SMTP server port
			$mail->Host       = $_Host; // SMTP server
			$mail->Username   = $_Username;     // SMTP server username
			$mail->Password   = $_Password;            // SMTP server password
			if($_Authmode==1){
				$mailer->SMTPSecure = 'ssl';
			}
			//$mail->IsSendmail();  // tell the class to use Sendmail

			$mail->AddReplyTo($_Username,"First Last");
			$mail->From       = $_Username;
			$mail->FromName   = $_W['account']['name']."-微商城".date('m-d H:i');
			$to = $_tomail;

			$mail->AddAddress($to);

			$mail->Subject  = $_title;
			$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
			$mail->WordWrap   = 80; // set word wrap
			$mail->MsgHTML($body);
			$mail->IsHTML(true); // send as HTML

			$mail->Send();
			return true;
		} catch (phpmailerException $e) {
			return $e->errorMessage();
		}
	}	
	private  function _formatstr($sstr,$slen=0,$isleft=true){
		if($slen==0 || $sstr=='') return $str;
		$sstr=iconv("UTF-8","GB2312//IGNORE",$sstr);
		if(strlen($sstr)>$slen){
			for($i=0;$i<$slen;$i++){
				$sb=$sb."_";
			}
			$sstr=$sstr.'%%'.$sb;
		}else{
			for($i=strlen($sstr);$i<$slen;$i++){
				$sb=$sb." ";
			}
			$sstr=$isleft?($sstr.$sb):($sb.$sstr);
		}		
		return $sstr;
	}
	
	//商户处理订单
	public function doMobileshow() {
		global $_GPC;
		$orderid=intval($_GPC['orderid']);
		$weid=intval($_GPC['weid']);
		$secretid=$_GPC['secretid'];
		if(!empty($_GPC['status'])){
			$temp=pdo_update('shopping2_order',array('status'=>$_GPC['status']),array());
			if($temp==false){
				message('修改订单信息失败');
			}else{
				message("修改订单成功",$this->createMobileUrl('show',array('orderid'=>$orderid,'secretid'=>$secretid)));
			}
		}
		if($_GPC['sendstatus']==1){
			//给客户发短信
			$temp=pdo_update('shopping2_order', array('sendstatus' => 1), array('id' => $orderid));
 			if($temp==false){
				message('此订单已确认配送失败，稍后再试!', referer(), 'error');
			}else{
				$set = pdo_fetch("SELECT sms_text,sms_status,sms_account,sms_secret FROM ".tablename('shopping2_set')." WHERE weid = :weid", array(':weid' => $weid));
 
				if($set['sms_status']==1){
					//获取客户的手机号码
					$order = pdo_fetch("SELECT a.totalprice,a.ordersn,b.username,b.tel FROM ".tablename('shopping2_order')." as a left join ".tablename('shopping2_address')." as b on a.aid=b.id WHERE a.weid =".$weid." AND a.id=".$orderid."");
					if($order!=false&& empty($order['sms_sta'])){
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
		$condition="   id={$orderid} AND secretid='{$secretid}'";
		$order = pdo_fetch("SELECT * FROM ".tablename('shopping2_order')." WHERE    $condition ");
		 
		$row= pdo_fetchall("SELECT a.*,b.title,b.thumb,b.marketprice FROM ".tablename('shopping2_order_goods')." as a left join  ".tablename('shopping2_goods')." as b on a.goodsid=b.id WHERE a.weid = '{$weid}' and a.orderid={$order['id']}");
		$address=pdo_fetch("SELECT * FROM ".tablename('shopping2_address')." WHERE   id={$order['aid']}");
		include $this->template('wl_show');
	}

}

