<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li<?php  if($do == 'installed') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('extension/module/installed');?>">已安装的功能</a></li>
	<li<?php  if($do == 'prepared') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('extension/module/prepared');?>">安装功能</a></li>
	<?php  if($do == 'permission') { ?><li class="active"><a href="<?php  echo create_url('extension/module/permission', array('id' => $id))?>">当前模块</a></li><?php  } ?>
</ul>
