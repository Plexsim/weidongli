<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html<?php  if($initNG) { ?> ng-app<?php  } ?>>
<head>
	<title><?php  if($title) { ?><?php  echo $title;?><?php  } else { ?><?php  if(!empty($_W['account']['name'])) { ?><?php  echo $_W['account']['name'];?><?php  } ?><?php  } ?><?php  if($_W['account']['siteinfo']['sitename']) { ?><?php  echo $_W['account']['siteinfo']['sitename'];?><?php  } ?></title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<!-- Mobile Devices Support @begin -->
	<meta content="application/xhtml+xml;charset=UTF-8" http-equiv="Content-Type">
	<meta content="no-cache,must-revalidate" http-equiv="Cache-Control">
	<meta content="no-cache" http-equiv="pragma">
	<meta content="0" http-equiv="expires">
	<meta content="telephone=no, address=no" name="format-detection">
	<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" /> <!-- apple devices fullscreen -->
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
	<!-- Mobile Devices Support @end -->
	<meta name="keywords" content="<?php  if(empty($_W['setting']['copyright']['keywords'])) { ?>微动力,微信,微信公众平台<?php  } else { ?><?php  echo $_W['setting']['copyright']['keywords'];?><?php  } ?><?php  if($_W['account']['siteinfo']['keywords']) { ?><?php  echo $_W['account']['siteinfo']['keywords'];?><?php  } ?>" />
	<meta name="description" content="<?php  if(empty($_W['setting']['copyright']['description'])) { ?>微信公众平台自助引擎，简称微动力，微动力是一款免费开源的微信公众平台管理系统。<?php  } else { ?><?php  echo $_W['setting']['copyright']['description'];?><?php  } ?><?php  if($_W['account']['siteinfo']['description']) { ?><?php  echo $_W['account']['siteinfo']['description'];?><?php  } ?>" />
	<script type="text/javascript" src="./resource/script/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="./resource/script/common.js"></script>
	<?php  if($bootstrap_type == 3) { ?>
	<link type="text/css" rel="stylesheet" href="./themes/mobile/default/style/bootstrap.css">
	<script type="text/javascript" src="./themes/mobile/default/script/bootstrap.min.js"></script>
	<?php  } else { ?>
	<link type="text/css" rel="stylesheet" href="./resource/style/bootstrap.css">
	<script type="text/javascript" src="./resource/script/bootstrap.js"></script>
	<?php  } ?>
<?php  if($initNG) { ?><script type="text/javascript" src="./resource/script/angular.min.js"></script><?php  } ?>
	<link type="text/css" rel="stylesheet" href="./resource/style/font-awesome.min.css" />
	<link type="text/css" rel="stylesheet" href="./themes/mobile/default/style/common.mobile.css">
	<script type="text/javascript" src="./resource/script/cascade.js"></script>
	<script type="text/javascript" src="./themes/mobile/default/script/jquery.touchwipe.js"></script>
	<script type="text/javascript" src="./themes/mobile/default/script/swipe.js"></script>
</head>
<?php  if($bootstrap_type != 3) { ?>
<style>
/*重定义bootstrap样式*/
select, textarea, input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input{width:100%; margin-bottom:0; box-sizing:border-box; -webkit-box-sizing:border-box; -moz-box-sizing:border-box;}
input[type="text"], input[type="password"], input[type="datetime"], input[type="datetime-local"], input[type="date"], input[type="month"], input[type="time"], input[type="week"], input[type="number"], input[type="email"], input[type="url"], input[type="search"], input[type="tel"], input[type="color"], .uneditable-input{height:30px;}
.input-append, .input-prepend{width:100%; margin-bottom:0;}
select{padding:0 5px; line-height:28px; -webkit-appearance:button;}
.checkbox.inline{margin-top:0;}
.checkbox.inline + .checkbox.inline {margin-left:0;}
.checkbox input[type="checkbox"]{filter:alpha(opacity:0); opacity:0;}
.file{position:relative;}
.file input[type="file"]{position:absolute; top:0; left:0; width:100%; filter:alpha(opacity:0); opacity:0;}
.file button{width:100%; text-align:left;}
.form-item{border-left:3px #ED2F2F solid; padding-left:10px; height:30px; line-height:30px; background:#F7F7F7; margin-bottom:10px;}
</style>
<?php  } ?>
<body>
<?php  if(empty($main_off)) { ?><div class="main"><?php  } ?>