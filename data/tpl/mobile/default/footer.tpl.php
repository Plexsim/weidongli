<?php defined('IN_IA') or exit('Access Denied');?>	<?php  if(empty($footer_off)) { ?><div id="footer"><?php  if($_W['account']['siteinfo']['footer']) { ?><?php  echo htmlspecialchars_decode($_W['account']['siteinfo']['footer'])?><?php  } else { ?>&copy;<?php  if(empty($_W['account']['name'])) { ?>微动力团队<?php  } else { ?><?php  echo $_W['account']['name'];?><?php  } ?>&nbsp;&nbsp;<?php  } ?><?php  echo $_W['setting']['copyright']['statcode'];?></div><?php  } ?>
</div>
<?php  if($_W['quickmenu']['menus']) { ?>
	<?php   include_once template($_W['quickmenu']['template'], TEMPLATE_INCLUDEPATH);?>
<?php  } ?>
<script type="text/javascript">
$(function() {
	$(".user-box .box-item").each(function(i) {
		i = i +1;
		if(i%3 == 0) $(this).css("border-right", "0");
	});
	$(window).scroll(function(){
		$(".menu-button").find("i").removeClass("icon-minus-sign").addClass("icon-plus-sign");
		$(".menu-main").hide();
	});
	$(".menu-main a").click(function(){ $(".menu-main").hide(); });

	//控制tab宽度
	var profile_tab = $(".nav-tabs li");
	profile_tab.css({"width": 100/profile_tab.length+"%", "text-align": "center"});

	//手机表单处理
	$(".form-table").delegate(".checkbox input[type='checkbox']", "click", function(){
		$(this).parent().toggleClass("btn-inverse");
	});
	$(".form-table").delegate(".file input[type='file']", "change", function(){
		var a = $(this).next("button");
		a.html(a.html() +' '+  $(this).val());
	});

	//处理固定横向导航条
	var navbarFixedTop = false, navbarFixedBottom = false;
	navbarFixedTop = $(".navbar").hasClass("navbar-fixed-top");
	navbarFixedBottom = $(".navbar").hasClass("navbar-fixed-bottom");
	if(navbarFixedTop) $("body").css("padding-top", "41px");
	if(navbarFixedBottom) $("body").css("padding-bottom", "41px");
});

//对分享时的数据处理
function _removeHTMLTag(str) {
	str = str.replace(/<script[^>]*?>[\s\S]*?<\/script>/g,'');
	str = str.replace(/<style[^>]*?>[\s\S]*?<\/style>/g,'');
    str = str.replace(/<\/?[^>]*>/g,'');
    str = str.replace(/\s+/g,'');
    str = str.replace(/&nbsp;/ig,'');
    return str;
}
document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {
	<?php
		$_share = array();
		$_share['title'] = (empty($title)) ? $_W['account']['name'] : $title;
		$_share['link'] = 'http://'.$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&wxref=mp.weixin.qq.com';
		$_share['img'] = $_W['siteroot'] . 'source/modules/' . $_GPC['name'] . '/icon.jpg';
		$_share['content'] = $_share_content;
	?>
	<?php  if(!empty($_share_img)) { ?>
	var _share_img = "<?php  echo $_share_img;?>";
	<?php  } else { ?>
	var _share_img = $('body img:eq(0)').attr("src");
	if(typeof(_share_img) == "undefined") _share_img = "<?php  echo $_share['img'];?>";
	if(_share_img.indexOf("http://") == -1 || _share_img.indexOf("https://") == -1 ) _share_img = "<?php  echo $_W['siteroot'];?>" + _share_img;
	<?php  } ?>
	<?php  if(empty($_share['content'])) { ?>
	var _share_content = _removeHTMLTag($('body').html()).replace("<?php  echo $_share['title'];?>",'');
	<?php  } else { ?>
	var _share_content = "<?php  echo preg_replace('/\s/i', '', str_replace('	', '', cutstr(str_replace('&nbsp;', '', ihtmlspecialchars(strip_tags($_share['content']))), 60)));?>";
	<?php  } ?>
	window.shareData = {
		"imgUrl": _share_img,
		"timeLineLink": "<?php  echo $_share['link'];?>",
		"sendFriendLink": "<?php  echo $_share['link'];?>",
		"weiboLink": "<?php  echo $_share['link'];?>",
		"tTitle": "<?php  echo $_share['title'];?>",
		"tContent":  _share_content,
		"fTitle": "<?php  echo $_share['title'];?>",
		"fContent":  _share_content,
		"wContent":  "<?php  echo $_share['title'];?>"
	};

	// 发送给好友
	WeixinJSBridge.on('menu:share:appmessage', function (argv) {
		WeixinJSBridge.invoke('sendAppMessage', {
			"img_url": window.shareData.imgUrl,
			"img_width": "640",
			"img_height": "640",
			"link": window.shareData.sendFriendLink,
			"desc": window.shareData.fContent,
			"title": window.shareData.fTitle
		}, function (res) {
			_report('send_msg', res.err_msg);
		})
	});

	// 分享到朋友圈
	WeixinJSBridge.on('menu:share:timeline', function (argv) {
		WeixinJSBridge.invoke('shareTimeline', {
			"img_url": window.shareData.imgUrl,
			"img_width": "640",
			"img_height": "640",
			"link": window.shareData.timeLineLink,
			"desc": window.shareData.tContent,
			"title": window.shareData.tTitle
		}, function (res) {
			_report('timeline', res.err_msg);
		});
	});

	// 分享到微博
	WeixinJSBridge.on('menu:share:weibo', function (argv) {
		WeixinJSBridge.invoke('shareWeibo', {
			"content": window.shareData.wContent,
			"url": window.shareData.weiboLink
		}, function (res) {
			_report('weibo', res.err_msg);
		});
	});
}, false);
</script>
<?php  if(empty($main_off)) { ?></div><?php  } ?>
</body>
</html>
