<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<?php  if($_W['isfounder']) { ?>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo create_url('index/welcome/' . $do);?>">概况</a></li>
	<li><a href="<?php  echo create_url('index/sysinfo/');?>">系统信息</a></li>
</ul>
<?php  } ?>
<div class="main">
	<div style="padding:15px 15px 0 15px;">
		<div class="welcome">
			<h4><i class="icon-user"></i> 公众号信息</h4>
			<div class="item">
				<img class="img-polaroid pull-left" style="margin-right:20px" src="<?php  echo $_W['attachurl'];?>/headimg_<?php  echo $_W['weid'];?>.jpg?weid=<?php  echo $_W['account']['weid'];?>" width="85" onerror="$(this).remove();" />
				<div class="pull-left">
					<p><b style="font-size:16px;"><?php  echo $_W['account']['name'];?></b></p>
					<p><span style="width:80px;display:inline-block;">接口地址：</span><?php  echo $_W['siteroot'];?>api.php?hash=<?php  echo $_W['account']['hash'];?>&weid=<?php  echo $_W['account']['weid'];?></p>
					<p><span style="width:80px;display:inline-block;">Token：</span><?php  echo $_W['account']['token'];?></p>
				</div>
			</div>
			<h4><i class="icon-plane"></i> 快捷操作</h4>
			<div class="item fast-set">
				<a href="<?php  echo create_url('site/module/switch', array('name' => 'userapi'))?>"><div class="fast-icon img-rounded"><i class="icon-cogs"></i></div><span>自定义接口</span></a>
				<?php  if(is_array($shortcuts)) { foreach($shortcuts as $shortcut) { ?>
				<a href="<?php  echo $shortcut['link'];?>"><img class="img-rounded" src="<?php  echo $shortcut['image'];?>"><span><?php  echo $shortcut['title'];?></span></a>
				<?php  } } ?>
			</div>
		</div>
		<table class="table">
			<tr><th colspan="2" class="alert alert-info">可用模块</th></tr>
			<?php  if(is_array($modules)) { foreach($modules as $module) { ?>
			<tr>
				<th style="width:250px;">
					<p><?php  echo $_W['modules'][$module['name']]['title'];?></p>
					<?php  if(!empty($_W['modules'][$module['name']]['isrulefields'])) { ?>
					<p><a href="<?php  echo create_url('rule/post', array('module' => $module['name']))?>">添加规则</a></p>
					<?php  } ?>
				</th>
				<td>
					<?php  if(empty($_W['modules'][$module['name']]['isrulefields'])) { ?>
					此模块无规则
					<?php  } else { ?>
					<p>规则数：<?php  echo $module['rule'];?></p>
					<p>当日触发数：<?php  echo $module['response']['today'];?></p>
					<p>当月触发数：<?php  echo $module['response']['month'];?></p>
					<?php  } ?>
				</td>
			</tr>
			<?php  } } ?>
		</table>
	 
	</div>
</div>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>
