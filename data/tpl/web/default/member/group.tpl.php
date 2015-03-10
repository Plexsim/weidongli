<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<ul class="nav nav-tabs">
	<li <?php  if($do == 'post') { ?>class="active"<?php  } ?>><a href="<?php  echo create_url('member/group/post');?>"><?php  if($id) { ?>编辑<?php  } else { ?>添加<?php  } ?>用户组</a></li>
	<li <?php  if($do == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo create_url('member/group/display');?>">用户组列表</a></li>
</ul>
<?php  if($do == 'post') { ?>
<form class="form-horizontal form" action="" method="post" enctype="multipart/form-data">
<div class="main">
	<input type="hidden" name="id" value="<?php  echo $id;?>" />
	<input type="hidden" name="templateid" value="<?php  echo $template['id'];?>">
	<h4>用户组管理</h4>
	<table class="tb">
		<tr>
			<th><label for="">名称</label></th>
			<td>
				<input type="text" class="span4" name="name" id="name" value="<?php  echo $item['name'];?>" />
			</td>
		</tr>
		<tr>
			<th><label for="">公众号数量</label></th>
			<td>
				<input type="text" class="span4" name="maxaccount" value="<?php  echo $item['maxaccount'];?>" />
				<span class="help-block">限制公众号的数量，为0则不限制。</span>
			</td>
		</tr>
		<tr>
			<th><label for="">子公众号数量</label></th>
			<td>
				<input type="text" class="span4" name="maxsubaccount" value="<?php  echo $item['maxsubaccount'];?>" />
				<span class="help-block">限制子公众号的数量，为0则不允许添加。</span>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="sub-item" id="table-list">
					<h4 class="sub-title">设置当前用户允许访问的功能</h4>
					<div class="sub-content">
						<table class="table table-hover">
							<thead class="navbar-inner">
								<tr>
									<th style="width:40px;" class="row-first">选择</th>
									<th>模块名称</th>
									
									<th></th>
								</tr>
							</thead>
							<tbody>
								<?php  if(is_array($modules)) { foreach($modules as $module) { ?>
								<tr>
									<td class="row-first"><?php  if(!$module['issystem']) { ?><input class="modules" type="checkbox" value="<?php  echo $module['mid'];?>" name="modules[]" <?php  if(!empty($item['modules']) && in_array($module['mid'], $item['modules'])) { ?>checked<?php  } ?> /><?php  } else { ?><input class="modules" type="checkbox" value="<?php  echo $module['mid'];?>" name="modules[]" disabled checked /><?php  } ?></td>
									<td><?php  echo $module['title'];?></td>
									
									
								</tr>
								<?php  } } ?>
							</tbody>
						</table>
					</div>
				</div>
			</td>
		</tr>
		<tr>
			
				
		<tr>
			<th></th>
			<td>
				<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
				<input type="submit" class="btn btn-primary" name="submit" value="提交" />
			</td>
		</tr>
	</table>
</div>
</form>
<?php  } else if($do == 'display') { ?>
<form action="" method="post">
<div class="main">
	<div style="padding:15px;">
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:30px;">删？</th>
					<th style="width:150px;">名称</th>
					<th>公众号数量</th>
					<th>子公众号数量</th>
					<th style="min-width:60px;">操作</th>
				</tr>
			</thead>
			<tbody>
				<?php  if(is_array($list)) { foreach($list as $item) { ?>
				<tr>
					<td><input type="checkbox" name="delete[]" value="<?php  echo $item['id'];?>" /></td>
					<td><?php  echo $item['name'];?></td>
					<td><?php  if(empty($item['maxaccount'])) { ?>不限<?php  } else { ?><?php  echo $item['maxaccount'];?><?php  } ?></td>
					<td><?php  if(empty($item['maxsubaccount'])) { ?>无权限<?php  } else { ?><?php  echo $item['maxsubaccount'];?><?php  } ?></td>
					<td><span><a href="<?php  echo create_url('member/group/post', array('id' => $item['id']))?>">编辑</a></span></td>
				</tr>
				<?php  } } ?>
			</tbody>
			<tr>
				<th></th>
				<td>
					<input name="token" type="hidden" value="<?php  echo $_W['token'];?>" />
					<input type="submit" class="btn btn-primary" name="submit" value="提交" />
				</td>
			</tr>
		</table>
	</div>
</div>
</form>
<?php  } ?>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>