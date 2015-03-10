<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<ul class="nav nav-tabs">
	<li><a href="<?php  echo create_url('index/welcome/' . $do);?>">概况</a></li>
	<li class="active"><a href="<?php  echo create_url('index/sysinfo/' . $do);?>">系统信息</a></li>
</ul>
<div class="main">
	<div style="padding:15px 15px 0 15px;">
		<table class="table">
			<tr><th colspan="2" class="alert alert-info">用户信息</th></tr>
			<tr>
				<th style="width:250px;">用户ID</th>
				<td><?php  echo $info['uid'];?></td>
			</tr>
			<tr>
				<th style="width:250px;">当前公众号</th>
				<td><?php  echo $info['account'];?></td>
			</tr>
			<tr><th colspan="2" class="alert alert-info">系统信息</th></tr>
			<tr>
				<th>服务器系统</th>
				<td><?php  echo $info['os'];?></td>
			</tr>
			<tr>
				<th>PHP版本 </th>
				<td>PHP Version <?php  echo $info['php'];?></td>
			</tr>
			<tr>
				<th>服务器软件</th>
				<td><?php  echo $info['sapi'];?></td>
			</tr>
			<tr>
				<th>服务器 MySQL 版本</th>
				<td><?php  echo $info['mysql']['version'];?></td>
			</tr>
			<tr>
				<th>上传许可</th>
				<td><?php  echo $info['limit'];?></td>
			</tr>
			<tr>
				<th>当前数据库尺寸</th>
				<td><?php  echo $info['mysql']['size'];?></td>
			</tr>
			<tr>
				<th>当前附件根目录</th>
				<td><?php  echo $info['attach']['url'];?></td>
			</tr>
			<tr>
				<th>当前附件尺寸</th>
				<td><?php  echo $info['attach']['size'];?></td>
			</tr>
		</table>
		 
	</div>
</div>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>
