<?php defined('IN_IA') or exit('Access Denied');?><ul class="nav nav-tabs">
	<li<?php  if($do == 'display') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('extension/service/display')?>">管理服务</a></li>
	<li<?php  if($do == 'post' && empty($rid)) { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('extension/service/post')?>"><i class="icon-plus"></i> 添加常用服务</a></li>
	<?php  if($do == 'post' && !empty($rid)) { ?><li class="active"><a href="<?php  echo create_url('extension/service/post', array('rid' => $rid))?>"><i class="icon-plus"></i> 编辑常用服务</a></li><?php  } ?>
</ul>
