<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'backup') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('setting/database');?>">备份</a></li>
	<li<?php  if($do == 'restore') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('setting/database/restore');?>">还原</a></li>
	<li<?php  if($do == 'optimize') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('setting/database/optimize');?>">优化</a></li>
	<li<?php  if($do == 'run') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('setting/database/run');?>">运行SQL</a></li>
</ul>
<div class="main">
	<?php  if($do == 'backup') { ?>
	<form action="" method="post" class="form-horizontal form">
		<h4>备份数据库</h4>
		<table class="tb">
			<tr>
				<th>备份操作说明</th>
				<td>
					<div class="help-block">使用系统备份的备份数据, 只能使用系统来进行还原. 如果使用其他工具, 或者自行导入sql, 可能造成数据不完整或不正确.</div>
					<div class="help-block"><strong>请在站点访问量比较低的时间段(如:深夜)操作, 或操作之前关闭站点. 来防止可能出现的意外数据丢失. </strong></div>
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<button type="submit" class="btn btn-primary span3" name="submit" value="提交">开始备份</button>
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</td>
			</tr>
		</table>
	</form>
	<?php  } ?>
	<?php  if($do == 'restore') { ?>
	<form action="" method="post" class="form-horizontal form">
		<h4>还原数据备份</h4>
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="max-width:150px;">备份名称</th>
					<th style="width:60px;">备份时间</th>
					<th style="width:80px;">分卷数量</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
			<?php  if(is_array($ds)) { foreach($ds as $row) { ?>
			<tr>
				<td><?php  echo $row['bakdir'];?></td>
				<td><?php  echo date('Y-m-d H:i:s', $row['time']);?></td>
				<td><?php  echo $row['volume'];?></td>
				<td><a href="<?php  echo create_url('setting/database/restore', array('r' => $row['bakdir']));?>" onclick="return confirm('确认要恢复这条备份记录吗? 当前数据库的数据将会被覆盖.');">还原此备份</a> &nbsp; <a href="<?php  echo create_url('setting/database/restore', array('d' => $row['bakdir']));?>" onclick="return confirm('确认要删除这条备份记录吗? ');">删除</a></td>
			</tr>
			<?php  } } ?>
			</tbody>
		</table>
	<?php  } ?>
	<?php  if($do == 'optimize') { ?>
	<form action="" method="post" class="form-horizontal form">
		<h4>优化数据表</h4>
		<table class="tb">
			<tr>
				<th>数据优化说明</th>
				<td>
					<div class="help-block"><strong>数据表优化可以去除数据文件中的碎片, 使记录排列紧密, 提高读写速度.</strong></div>
				</td>
			</tr>
		</table>
		<?php  if(!empty($ds)) { ?>
		<table class="table table-hover">
			<thead class="navbar-inner">
				<tr>
					<th style="width:20px;">操作</th>
					<th style="max-width:150px;">表名</th>
					<th style="width:60px;">表类型</th>
					<th style="width:60px;">记录数</th>
					<th style="width:80px;">数据尺寸</th>
					<th style="width:80px;">索引尺寸</th>
					<th style="width:80px;">碎片尺寸</th>
				</tr>
			</thead>
			<tbody>
			<?php  if(is_array($ds)) { foreach($ds as $row) { ?>
			<tr>
				<td><input type="checkbox" name="select[]" checked="checked" value="<?php  echo $row['title'];?>"></td>
				<td><?php  echo $row['title'];?></td>
				<td><?php  echo $row['type'];?></td>
				<td><?php  echo $row['rows'];?></td>
				<td><?php  echo $row['data'];?></td>
				<td><?php  echo $row['index'];?></td>
				<td><?php  echo $row['free'];?></td>
			</tr>
			<?php  } } ?>
			</tbody>
		</table>
		<table class="tb">
			<tr>
				<td>
					<button type="submit" class="btn btn-primary span3" name="submit" value="提交">开始优化</button>
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</td>
			</tr>
		</table>
		<?php  } else { ?>
		<table class="tb">
			<tr>
				<th></th>
				<td>
					<div class="help-block"><strong>没有要优化的数据表</strong></div>
				</td>
			</tr>
		</table>
		<?php  } ?>
	<?php  } ?>
	<?php  if($do == 'run') { ?>
	<form action="" method="post" class="form-horizontal form" onsubmit="return confirm('请确保你已经了解这些语句的作用, 并自愿承担风险.');">
		<h4>运行SQL语句</h4>
		<table class="tb">
			<tr>
				<th>运行说明</th>
				<td>
					<div class="help-block">通过此功能可以直接在数据库中执行特定语句, 用于调试错误. 或者系统管理员特定排错. 注意, 这里运行的语句不会有任何返回结果. </div>
					<div class="help-block"><strong>注意: 此功能可能造成数据破坏, 请谨慎使用. 如果你不清楚他的功能, 请不要使用.</strong></div>
				</td>
			</tr>
			<tr>
				<th>SQL</th>
				<td>
					<textarea name="sql" class="span6" rows="8"></textarea>
					<div class="help-block">多条语句请使用 ; 隔开</div>
				</td>
			</tr>
			<tr>
				<th></th>
				<td>
					<button type="submit" class="btn btn-primary span3" name="submit" value="提交">运行SQL</button>
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</td>
			</tr>
		</table>
	<?php  } ?>
</div>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>
