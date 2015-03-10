<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<?php  include template('extension/service-tabs', TEMPLATE_INCLUDEPATH);?>
<div class="main">
	<div class="search">
		<form action="" method="get">
		<input type="hidden" name="act" value="<?php  echo $_GPC['act'];?>" />
		<input type="hidden" name="do" value="<?php  echo $_GPC['do'];?>" />
		<input type="hidden" name="name" value="<?php  echo $_GPC['name'];?>" />
		<input type="hidden" name="foo" value="<?php  echo $_GPC['foo'];?>" />
		<table class="table table-bordered tb">
			<tbody>
				<tr>
					<th>状态</th>
					<td>
						<select name="status">
							<option value="1" <?php  if($_GPC['status'] == '1') { ?> selected<?php  } ?>>启用</option>
							<option value="0" <?php  if($_GPC['status'] == '0') { ?> selected<?php  } ?>>禁用</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>关键字</th>
					<td>
							<input class="span6" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>">
					</td>
				</tr>
				 <tr class="search-submit">
					<td colspan="2"><button class="btn pull-right span2"><i class="icon-search icon-large"></i> 搜索</button></td>
				 </tr>
			</tbody>
		</table>
		</form>
	</div>
	<div class="rule">
		<?php  if($import) { ?>
		<div class="alert alert-info">
			<a href="<?php  echo create_url('extension/service/import')?>">存在可用的服务, 点击这里快速导入</a>
		</div>
		<?php  } ?>
		<?php  if(is_array($ds)) { foreach($ds as $row) { ?>
		<table class="tb table table-bordered">
			<tr class="control-group">
				<td class="rule-content">
					<h4>
						<span class="pull-right">
							<a onclick="return confirm('删除规则将同时删除关键字与回复，确认吗？');return false;" href="<?php  echo create_url('extension/service/delete', array('rid' => $row['id']))?>">删除</a>
							<a href="<?php  echo create_url('extension/service/post', array('id' => $row['id']))?>">编辑</a>
						</span>
						<?php  echo $row['name'];?>
					</h4>
				</td>
			</tr>
			<tr class="control-group">
				<td class="rule-kw">
					<div>
						<span><?php  echo $row['description'];?></span>
					</div>
				</td>
			</tr>
		</table>
		<?php  } } ?>
	</div>
	<?php  echo $pager;?>
</div>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>
