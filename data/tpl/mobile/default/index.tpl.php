<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('header', TEMPLATE_INCLUDEPATH);?>
<style>
body{font:<?php  echo $_W['styles']['fontsize'];?> <?php  echo $_W['styles']['fontfamily'];?>; color:<?php  echo $_W['styles']['fontcolor'];?>;padding:0;margin:0;background-image:url('<?php  if(empty($_W['styles']['indexbgimg'])) { ?>./themes/mobile/default/images/bg_index.jpg<?php  } else { ?><?php  echo $_W['styles']['indexbgimg'];?><?php  } ?>');background-size:cover;<?php  if(!empty($_W['styles']['indexbgcolor'])) { ?> background-color:<?php  echo $_W['styles']['indexbgcolor'];?>;<?php  } ?><?php  echo $_W['styles']['indexbgextra'];?>}
a,a:hover{color:<?php  if(empty($_W['styles']['linkcolor'])) { ?>#FFF<?php  } else { ?><?php  echo $_W['styles']['linkcolor'];?><?php  } ?>; text-decoration:none;}
<?php  echo $_W['styles']['css'];?>
.box{width:58%;overflow:hidden;margin-top:10px;}
.box .box-item{float:left;text-align:center;display:block;text-decoration:none;outline:none;width:77px;height:92px;margin-left:8px;margin-bottom:8px;position:relative;background:rgba(0, 0, 0, 0.7);}
.box .box-item i{display:inline-block;width:45px;height:45px;margin-top:15px;font-size:35px;color:#CCC;overflow: hidden;}
.box .box-item span{color:<?php  echo $_W['styles']['fontnavcolor'];?>;display:block;font-size:14px;}
</style>
<div class="box">
	<?php  if(is_array($navs)) { foreach($navs as $nav) { ?>
	<a href="<?php  echo $nav['url'];?>" class="box-item">
		<?php  if(!empty($nav['icon'])) { ?>
		<i style="background:url(<?php  echo $_W['attachurl'];?><?php  echo $nav['icon'];?>) no-repeat;background-size:cover;"></i>
		<?php  } else { ?>
		<i class="<?php  echo $nav['css']['icon']['icon'];?>" style="<?php  echo $nav['css']['icon']['style'];?>"></i>
		<?php  } ?>
		<span style="<?php  echo $nav['css']['name'];?>"><?php  echo $nav['name'];?></span>
	</a>
	<?php  } } ?>
</div>
<?php  include template('footer', TEMPLATE_INCLUDEPATH);?>