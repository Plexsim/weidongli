<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('header', TEMPLATE_INCLUDEPATH);?>
<?php  include template('member', TEMPLATE_INCLUDEPATH);?>
<style>
/*风格设置*/
body {background-image:url(<?php  echo $_W['styles']['homebgimg'];?>); background-color:<?php  if(empty($_W['styles']['homebgcolor'])) { ?>#F9F9F9<?php  } else { ?><?php  echo $_W['styles']['homebgcolor'];?><?php  } ?>; <?php  echo $_W['styles']['homebgextra'];?>;}
.nav-tabs .active a, .nav-tabs .active a:hover{background:#F9F9F9;}
.card{position:relative; margin:10px 8%; max-width:534px; max-height:318px; overflow:hidden;}
.cardsn{position:absolute; color:<?php  echo $card['color']['number'];?>; right:20px; bottom:10px; text-shadow:0 -1px 0 rgba(255, 255, 255, 0.5); font-size:16px;}
.cardtitle{position:absolute; right:20px; top:10px; color:<?php  echo $card['color']['title'];?>; font-size:16px; text-shadow:0 -1px 0 rgba(255, 255, 255, 0.5);}
.cardlogo{position:absolute; top:10px; left:20px;}
</style>
<div class="tabbable">
	<div class="tab-content">
		<div class="tab-pane active" id="tab1">
			<?php  if(is_array($menus)) { foreach($menus as $name => $menu) { ?>
			<div class="mobile-div img-rounded">
				<div class="mobile-hd" style="border-bottom:0;"><?php  if($name == 'other') { ?>通用<?php  } else { ?><?php  echo $_W['account']['modules'][$name]['title'];?><?php  } ?></div>
				<?php  if(is_array($menu)) { foreach($menu as $module) { ?>
					<?php  if($name == 'other') { ?>
					<?php  if(is_array($module)) { foreach($module as $nav) { ?>
					<a href="<?php  echo $nav['url'];?>#qq.com#wechat_redirect" class="mobile-li">
						<i class="icon-hand-up pull-right"></i>
						<?php  echo $nav['name'];?>
					</a>
					<?php  } } ?>
					<?php  } else { ?>
					<?php  $nav = $module;?>
					<a href="<?php  echo $nav['url'];?>#qq.com#wechat_redirect" class="mobile-li">
						<i class="icon-hand-up pull-right"></i>
						<?php  echo $nav['name'];?>
					</a>
					<?php  } ?>
				<?php  } } ?>
			</div>
			<?php  } } ?>
		</div>
	</div>
</div>
<?php  include template('footer', TEMPLATE_INCLUDEPATH);?>