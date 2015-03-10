<?php defined('IN_IA') or exit('Access Denied');?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php  if(empty($_W['setting']['copyright']['sitename'])) { ?>微爱云 - 微场景专用管理平台<?php  } else { ?><?php  echo $_W['setting']['copyright']['sitename'];?><?php  } ?></title>
<link href="./resource/mstyle/index/css/global.css" rel="stylesheet" type="text/css" />
<link href="./resource/mstyle/index/css/login.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="./resource/script/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="./resource/mstyle/index/js/jquery-ui.js"></script>
<script>

function formcheck() {
	if($('#remember:checked').length == 1) {
		cookie.set('remember-username', $(':text[name="username"]').val());
	} else {
		cookie.del('remember-username');
	}
	return true;
}
</script>
</head>

<body>
<style>
.top1000{ width:1000px; margin:0 auto;}
.top_l{ padding-top:6px;}
</style>

<div class="main-content">
<div class="main1000" > <img class="phone" alt="phone" src="./resource/mstyle/index/images/phone.png" id="zll" style="top:120px;display: inline;">
    <p class="txt1"><img src="./resource/mstyle/index/images/login_huanyin.png" width="251" height="109" /></p>
    <form action="" method="post" target="_top" >
      <div class="login" id="myLogin">
        <h2>用户登陆</h2>
        <p>
          <label>用户名</label>
          <input  name="username"  type="text"  autocomplete="off" />
        </p>
        <p>
          <label>密&nbsp;码</label>
          <input  name="password"  type="password"  autocomplete="off"/>
        </p>
        <p class="pass-form-item">
       
          <input type="checkbox"  class="pass-checkbox-input" value="1" id="remember" style="border:none;">
          
          <label class="" style="  height: auto;line-height: 18px;width: auto; border:none; background:none;">记住我的登录状态</label>
        </p>
        <p class="pass-form-item">
          <input name="submit" type="submit" value="登陆"   class="login_input"/><input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
          </p>
		 <p class="reg_pic"><a href="member.php?act=register&amp;"><img src="./resource/mstyle/index/images/reg.jpg" width="175" height="38"></a></p>
      </div>
  </form>
    <div class="yinying"><img src="./resource/mstyle/index/images/login_yiyin.png" width="320" height="4" /></div>
    <div class="news">
      <div class="gr"></div>
      <ul>
        <li style=" width:545px;">
          <div class="w545">
            <div class="inner"> <a href="javascript:void(0)" class="d1"></a> </div>
            <div class="products-desc">
              <h3 class="title">什么是微爱云</h3>
              <div class="desc"> 微爱云是一个集微信公众接口、微信达人、微信资讯， 
旨在通过方便实用的接口帮助吾爱传媒用户管理提高个人和团队效率的管理产品。 
通过"微信导航"为微信玩家们更好的找到自己喜欢的微信。 </div>
            </div>
          </div>
        </li>
        <li>
          <div class="w545">
            <div class="inner"> <a href="javascript:void(0)" class="d2"></a> </div>
            <div class="products-desc">
              <h3 class="title">新版上线</h3>
              <div class="desc">提供[微信喜帖],[微信宣传页],[3G相册],[微信图片墙],[微信留言板],[自定义订单]……</div>
            </div>
          </div>
        </li>
        <li>
          <div class="w545">
            <div class="inner"> <a href="javascript:void(0)" class="d3"></a> </div>
            <div class="products-desc">
              <div class="w545">
                <h3 class="title">我们能为您做什么？</h3>
                <div class="desc"> 提供微会员、微活动 <br>
                  微团购+刮刮乐+一战到底+微信喜帖+在线订餐+自定义订单+3G相册+优惠券+幸运大转盘的会员再营销，超炫微信3G网站，<br>
                  等您需要的各种吾爱传媒模块。</div>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div class="clear"></div>

</div>
<script type="text/javascript">
function onpost() {
	var pw = max.$("password");
	var idname = max.$("idname");
	if(idname.value == "") {
		alert("请输入用户名");
		return false;
	}
	if (pw.value == "" ){
		alert("请输入密码");
		return false;
	}	
	return true;
}
</script> 
<script>
$(function() {
    var $phone = $('.phone');
    var top = $phone.css('top');
    $phone.css({top: '-160px', display: 'inline'}).animate({top: top}, 2000, 'easeOutBounce') 
    $('.txt1').slideDown(2000);
	
	$(".d1").hover(
	 function(){
	   	 $(".d3").parent().parent().parent().animate({ width:'135px'},300)
		 $(".d2").parent().parent().parent().animate({ width:'135px'},300)
		 $(".d1").parent().parent().parent().animate({ width:'545px'},300)
	  }
	)
	$(".d2").hover(
	 function(){
	   	 $(".d1").parent().parent().parent().animate({ width:'135px'},300)
		 $(".d3").parent().parent().parent().animate({ width:'135px'},300)
		 $(".d2").parent().parent().parent().animate({ width:'545px'},300)
	  }
	)
	$(".d3").hover(
	 function(){
	   	 $(".d1").parent().parent().parent().animate({ width:'135px'},300)
		 $(".d2").parent().parent().parent().animate({ width:'135px'},300)
		 $(".d3").parent().parent().parent().animate({ width:'545px'},300)
	  }
	)
	
});
</script>

</body>
</html>