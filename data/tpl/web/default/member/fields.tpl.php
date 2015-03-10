<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo create_url('member/fields');?>">字段管理</a></li>
</ul>
<?php  if($do == 'display') { ?>
<form action="" method="post">
<div class="rule">
	<table class="table table-hover">
		<thead class="navbar-inner">
			<tr>
				<th style="width:80px;"></th>
				<th style="width:150px;">字段名</th>
				<th style="width:150px;">标题</th>
				<th style="width:80px;">是否启用</th>
				<th style="width:80px;">注册页显示</th>
				<th style="width:80px;">是否必填</th>
				<th>操作</th>
			</tr>
		</thead>
		<tbody>
			<?php  if(is_array($fields)) { foreach($fields as $field) { ?>
			<tr>
				<td><input type="text" class="span1" placeholder="" name="displayorder[<?php  echo $field['id'];?>]" value="<?php  echo $field['displayorder'];?>"></td>
				<td><div><?php  echo $field['field'];?></div></td>
				<td><?php  echo $field['title'];?></td>
				<td><input type="checkbox" name="available[<?php  echo $field['id'];?>]" value="1" <?php  if($field['available']) { ?>checked<?php  } ?>/></td>
				<td><input type="checkbox" name="showinregister[<?php  echo $field['id'];?>]" value="1" <?php  if($field['showinregister']) { ?>checked<?php  } ?>/></td>
				<td><input type="checkbox" name="required[<?php  echo $field['id'];?>]" value="1" <?php  if($field['required']) { ?>checked<?php  } ?>/></td>
				<td><a href="<?php  echo create_url('member/fields/post', array('id' => $field['id']))?>" title="编辑" class="btn btn-small"><i class="icon-edit"></i></a></td>
			</tr>
			<?php  } } ?>
			<tr>
				<td colspan="7">
					<button type="submit" class="btn btn-primary" name="submit" value="提交">提交</button>
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</td>
			</tr>
		</tbody>
	</table>
</div>
</form>
<?php  } else if($do == 'post') { ?>
<div class="main">
	<form class="form-horizontal form" action="" method="post">
		<input type="hidden" name="id" value="<?php  echo $item['id'];?>">
		<h4>字段管理</h4>
		<table class="tb">
			<tr>
				<th><label for="">字段</label></th>
				<td>
					<input type="text" class="span6" value="<?php  echo $item['field'];?>" disabled>
				</td>
			</tr>
			<tr>
				<th><label for="">排序</label></th>
				<td>
					<input type="text" class="span1" placeholder="" name="displayorder" value="<?php  echo $item['displayorder'];?>">
				</td>
			</tr>
			<tr>
				<th><label for="">名称</label></th>
				<td>
					<input type="text" class="span6" placeholder="" name="title" value="<?php  echo $item['title'];?>">
					<input type="hidden" name="name_old" value="<?php  echo $item['Field'];?>">
				</td>
			</tr>
			<tr>
				<th><label for="">描述</label></th>
				<td>
					<textarea style="height:100px;" class="span6" name="description" cols="50"><?php  echo $item['description'];?></textarea>
				</td>
			</tr>
			<tr>
				<th><label for="">是否启用</label></th>
				<td>
					<label for="radio_1" class="radio inline"><input type="radio" name="available" id="radio_1" value="1" <?php  if(empty($item) || $item['available'] == 1) { ?> checked<?php  } ?> /> 是</label>
					<label for="radio_0" class="radio inline"><input type="radio" name="available" id="radio_0" value="0" <?php  if(!empty($item) && $item['available'] == 0) { ?> checked<?php  } ?> /> 否</label>
				</td>
			</tr>
			<tr>
				<th><label for="">是否必填</label></th>
				<td>
					<label for="radio_6" class="radio inline"><input type="radio" name="required" id="radio_6" value="1" <?php  if(empty($item) || $item['required'] == 1) { ?> checked<?php  } ?> /> 是</label>
					<label for="radio_7" class="radio inline"><input type="radio" name="required" id="radio_7" value="0" <?php  if(!empty($item) && $item['required'] == 0) { ?> checked<?php  } ?> /> 否</label>
				</td>
			</tr>
			<tr>
				<th><label for="">提交后不可修改</label></th>
				<td>
					<label for="radio_3" class="radio inline"><input type="radio" name="unchangeable" id="radio_3" value="1" <?php  if(empty($item) || $item['unchangeable'] == 1) { ?> checked<?php  } ?> /> 是</label>
					<label for="radio_2" class="radio inline"><input type="radio" name="unchangeable" id="radio_2" value="0" <?php  if(!empty($item) && $item['unchangeable'] == 0) { ?> checked<?php  } ?> /> 否</label>
				</td>
			</tr>
			<tr>
				<th><label for="">在注册页面显示</label></th>
				<td>
					<label for="radio_4" class="radio inline"><input type="radio" name="showinregister" id="radio_4" value="1" <?php  if(empty($item) || $item['showinregister'] == 1) { ?> checked<?php  } ?> /> 是</label>
					<label for="radio_5" class="radio inline"><input type="radio" name="showinregister" id="radio_5" value="0" <?php  if(!empty($item) && $item['showinregister'] == 0) { ?> checked<?php  } ?> /> 否</label>
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<button type="submit" class="btn btn-primary span3" name="submit" value="提交">提交</button>
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</td>
			</tr>
		</table>
	</form>
</div>
<?php  } ?>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>