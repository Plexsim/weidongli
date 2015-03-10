<?php defined('IN_IA') or exit('Access Denied');?>﻿<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
         <link rel="stylesheet" type="text/css" href="./resource/mstyle/css/bootstrap_min.css?v=2014-05-21" media="all" />
<link rel="stylesheet" type="text/css" href="./resource/mstyle/css/bootstrap_responsive_min.css?v=2014-05-21" media="all" />
<link rel="stylesheet" type="text/css" href="./resource/mstyle/css/style.css?v=2014-05-21" media="all" />
<link rel="stylesheet" type="text/css" href="./resource/mstyle/css/todc_bootstrap.css?v=2014-05-21" media="all" />
<link rel="stylesheet" type="text/css" href="./resource/mstyle/css/themes.css?v=2014-05-21" media="all" />
<link rel="stylesheet" type="text/css" href="./resource/mstyle/css/inside.css?v=2014-05-21" media="all" />
<script type="text/javascript" src="./resource/mstyle/src/jQuery.js?v=2014-05-21"></script>
<script type="text/javascript" src="./resource/mstyle/src/bootstrap_min.js?v=2014-05-21"></script>
<script type="text/javascript" src="./resource/mstyle/src/inside.js?v=2014-05-21"></script>
<title><?php  if(empty($_W['setting']['copyright']['sitename'])) { ?>微爱云营销平台<?php  } else { ?><?php  echo $_W['setting']['copyright']['sitename'];?><?php  } ?></title>
        <!--[if IE 7]>
			<link rel="stylesheet" href="./resource/style/font-awesome-ie7.min.css">
        <![endif]-->
        <!--[if lte IE 8]>
            <script src="./resource/mstyle/js/excanvas_min.js?v=2014-05-21"></script>
        <![endif]-->
        <!--[if lte IE 9]>
            <script src="./resource/mstyle/js/watermark.js?v=2014-05-21"></script>
        <![endif]-->
    </head>
	<body>
    <style>
#fallr-button-button1,#fallr-button-button2 {
    cursor:pointer !important;
}
</style>
<div id="main">
	<div class="container-fluid">

		<div class="row-fluid">
			<div class="span12">

				<div class="box">
					<div class="box-title">
						<div class="span8">
							<h3><i class="icon-table"></i>管理微信公众帐号</h3>
						</div>
						<div class="span4">
                                <div class="form-horizontal">
                                    <input type="text" id="keyword-input" class="input-z" placeholder="请输入公共账号">
                                    <button class="btn" id="btn_search">查询</button>
                                </div>
                            </div>

					</div>
					<div class="box-content nozypadding">
						<div class="row-fluid">
							<div class="span4 control-group">
								<a class="btn" href="<?php  echo create_url('account/post')?>"><i class="icon-plus"></i>添加公众帐号</a>
								<!--a class="btn" href="javascript:alert('配额不足，请升级！');"><i class="icon-plus"></i>添加公众帐号</a->
								<!--a href="#" target="_blank" class="btn btn-warning" style="cursor:pointer">微助手</a-->
							</div>

						</div>
						<div class="row-fluid dataTables_wrapper">
							<div class="alert">
								<!--<strong>温馨提示</strong>：您还有0个微信公众号配额，请珍惜使用名额！ <span class="line hide">  </span>-->
								<i class="icon-lightbulb"></i> 欢迎使用微爱云场景应用。如果不需要绑定公众账号，请随意填写公众账号名称即可。<br>
								<i class="icon-lightbulb"></i> 点击公众账号名称右下角【管理账号】后开始使用。<br>
								<i class="icon-lightbulb"></i> 微爱云严厉打击倒卖盗卖行为，一经发现恕不更新。
							</div>
							<form method="post" action="" id="listForm">
								<table id="listTable" class="table table-hover table-nomargin table-bordered usertable dataTable">
									<thead>
										<tr>
											<th>公众号logo</th>
											<th>公众号名称</th>											
 											<th>所属用户</th>

											<th>接口地址/Token</th>
 											<!--th>请求数</th>
											<th>剩余请求数</th>
											<th>增值服务</th-->
 											<th>操作</th>
										</tr>
									</thead>
									<tbody>
									<?php  if(is_array($list)) { foreach($list as $row) { ?>
										<tr>
											<td style="text-align:center;">
												<p>
													<a href="javascript:void(0)" onclick="parent.location.href='<?php  echo create_url('account/switch', array('id' => $row['weid']))?>'" title="点击进入功能管理">
													<?php  if(file_exists(IA_ROOT . '/resource/attachment/headimg_'.$row['weid'].'.jpg')) { ?><img src="<?php  echo $_W['attachurl'];?>/headimg_<?php  echo $row['weid'];?>.jpg?time=<?php  echo time()?>" width="85" />
													<?php  } else { ?>
													<img src="./resource/mstyle/img/headimg.jpg" style="width:88px;height:88px;">
													<?php  } ?>
													</a>
												</p>
												
											</td>
											<Td><?php  echo $row['name'];?></td>
											 
											<td>
											<?php  if(in_array($row['uid'], $founder)) { ?>创始人<?php  } else { ?><?php  echo $users[$row['uid']]['username'];?><?php  } ?>
											</td>
											<td style="max-width:150px" class="account">
												<dd>小提示:<em>鼠标左键点击链接或者Token即可自动复制</em></span>
												<dd copy-status=0><b>接口地址:</b><?php  echo $_W['siteroot'];?>api.php?hash=<?php  echo $row['hash'];?></dd>
												<dd copy-status=0><b>Token:</b><?php  echo $row['token'];?></dd>
 											</td>
											 
											<!--td>
												<p>总请求数:</p>
												<p>本月请求数:</p>
											</td>
											<td>
												<p>请求数剩余：</p>
											</td>
											<td>
												<p>短信：条</p>
											</td-->
											 
											<td>
												<a href="<?php  echo create_url('account/post', array('id' => $row['weid']))?>" class="btn" rel="tooltip" title="编辑"><i class="icon-edit"></i>编辑</a>
												<a href="javascript:G.ui.tips.confirm('您确定要删除此公众帐号吗?', '<?php  echo create_url('account/delete', array('id' => $row['weid']))?>');" class="btn" rel="tooltip" title="删除"><i class="icon-remove"></i>删除</a>
												<a  href="javascript:void(0)" onclick="parent.location.href='<?php  echo create_url('account/switch', array('id' => $row['weid']))?>'" class="btn" rel="tooltip" title="管理"><i class="icon-cog"></i>切换账号</a>
											</td>
										</tr>
									</tbody>
								<?php  } } ?>
								</table>
						</div>
						<?php  echo $pager;?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
 
	<div style="width:100%;text-align:center;font-size:14px;color:#ff0000;display:none;">微爱云 -  2.0</div>
	</body>
	<script>
	    $("#btn_search").click(function(){
			var keywords = $.trim($('#keyword-input').val());
			if (keywords.length <= 0) {
				alert('请输入公众号名称.');
				$('#keyword-input').focus();
				return false;
			}
			window.location.href = "<?php  echo create_url('account')?>&wechat=" + keywords;

		});

		window.document.onkeydown = function(e) {
			if ('BODY' == event.target.tagName.toUpperCase()) {
				var e=e || event;
　 				var currKey=e.keyCode || e.which || e.charCode;
				if (8 == currKey) {
					return false;
				}
			}
		};
	 
		 

	</script> 
</html>