<?php
/**
 * 首页
 *
 * @author 超级无聊
 * @url
 */
 
	//获取最新的8件商品
	$list = pdo_fetchall("SELECT * FROM ".tablename('shopping2_goods')." WHERE weid = '{$weid}' AND status = '1' AND isindex=1 $condition ORDER BY displayorder DESC, id DESC LIMIT 8");
	$set = pdo_fetch("SELECT * FROM ".tablename('shopping2_set')." WHERE weid = :weid", array(':weid' => $weid));
	$title=$set['shop_name'];
	$thumbArr=explode('|',$set['thumb']);
	$t=array();
	$u=array();
	foreach($thumbArr as $v){
		$t[]='1';
		$u[]='#';
	}
	$flash=array(
		"PId"=> "9",
		"SId"=> "1",
		"MemberId"=> "16",
		"ContentsType"=> "1",
		"Title"=>$t,
		"ImgPath"=>$thumbArr,
		"Url"=>$u,
		"Postion"=> "t01",
		"Width"=> "640",
		"Height"=> "320",
		"NeedLink"=>"0",
	);
 	include $this->template('wl_index');