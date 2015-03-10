<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<div class="message alert alert-<?php  echo $type;?>">
	<div class="icon pull-left"><i class="<?php  if($type=='success') { ?>icon-ok<?php  } else if($type=='error') { ?>icon-remove<?php  } else if($type=='tips') { ?>icon-exclamation-sign<?php  } else if($type=='sql') { ?>icon-warning-sign<?php  } ?>"></i></div>
	<div class="content">
		<?php  if($type == 'sql') { ?>
			<h4>MYSQL 错误：</h4>
			<p><?php  echo cutstr($msg['sql'], 300, 1);?></p>
			<p><b><?php  echo $msg['error']['0'];?> <?php  echo $msg['error']['1'];?>：</b><?php  echo $msg['error']['2'];?></p>
		<?php  } else { ?>
		<h4><?php  echo $msg;?></h4>
		<?php  if($redirect) { ?>
		<p><a href="<?php  echo $redirect;?>">如果你的浏览器没有自动跳转，请点击此链接</a></p>
		<script type="text/javascript">
			setTimeout(function () {
				location.href = "<?php  echo $redirect;?>";
			}, 3000);
		</script>
		<?php  } else { ?>
		<p>[<a href="javascript:history.go(-1);">点击这里返回上一页</a>] &nbsp; </p>
		<?php  } ?>
		<?php  } ?>
	</div>
</div>

<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>
