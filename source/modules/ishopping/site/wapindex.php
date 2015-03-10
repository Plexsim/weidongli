<?php
/**
 * 首页
 *
 * 作者:迷失卍国度
 *
 * qq : 15595755
 */
$type = intval($_GPC['type']);

if($type == 2){
    $list = pdo_fetchall("SELECT * FROM ".tablename('ishopping_goods')." WHERE weid = '{$_W['weid']}' AND status = '1' AND recommend=1 $condition ORDER BY displayorder DESC, id DESC LIMIT 8");
} else {
    $list = pdo_fetchall("SELECT * FROM ".tablename('ishopping_goods')." WHERE weid = '{$_W['weid']}' AND status = '1' $condition ORDER BY displayorder DESC, id DESC LIMIT 8");
}
$setting = pdo_fetch("SELECT * FROM ".tablename('ishopping_setting')." WHERE weid = :weid", array(':weid' => $_W['weid']));
$title = $setting['shop_name'];
$thumbArr = explode('|',$setting['thumb']);
$arr_title = array();
$arr_url = array();

foreach($thumbArr as $value){
    $arr_title[]='1';
    $arr_url[]='#';
}

$flash = array(
    "PId" => "9",
    "SId" => "1",
    "MemberId" => "16",
    "ContentsType" => "1",
    "Title" => $arr_title,
    "ImgPath" => $thumbArr,
    "Url" => $arr_url,
    "Postion" => "t01",
    "Width" => "640",
    "Height" => "320",
    "NeedLink" => "0",
);
include $this->template('wap_index');