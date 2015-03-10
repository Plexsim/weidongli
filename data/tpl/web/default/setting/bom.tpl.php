<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<ul class="nav nav-tabs">
	<li<?php  if($do == 'bom') { ?> class="active"<?php  } ?>><a href="<?php  echo create_url('setting/tools');?>">BOM检测</a></li>
</ul>
<div class="main">
	<?php  if($do == 'bom') { ?>
	<form action="" method="post" class="form-horizontal form">
		<h4>检测系统BOM</h4>
		<table class="tb">
			<tr>
				<th>操作说明</th>
				<td>
					<div class="help-block">系统使用utf-8无bom格式的文件编码方式, 如果使用编辑器修改配置或者查看文件时没有注意编辑器设置将可能在被编辑的文件上附加BOM头, 从而造成系统功能异常. </div>
					<div class="help-block"><strong>注意: 在公众平台添加API地址时重复错误时, 请尝试检测BOM异常. </strong></div>
					<div class="help-block"><strong>注意: 使用云平台功能时重复出现错误提示时, 请尝试检测BOM异常. </strong></div>
					<div class="help-block"><strong>注意: 使用 Windows 系统自带的记事本编辑吾爱传媒源码可能会造成这样的问题. </strong></div>
				</td>
			</tr>
			<tr>
				<th>处理说明</th>
				<td>
					<div class="help-block">为保证系统正常运行, 系统不会尝试修复检测出来的错误文件, 检测完成后请自行使用编辑器修改文件编码方式</div>
				</td>
			</tr>
			<?php  if(isset($ds)) { ?>
			<tr>
				<th>检测结果</th>
				<td>
					<?php  if(empty($ds)) { ?>
						<div class="help-block"><strong>没有检测到存在BOM的异常文件</strong></div>
					<?php  } else { ?>
						<div class="alert alert-info" style="line-height:20px;">
						<?php  if(is_array($ds)) { foreach($ds as $line) { ?>
						<div><?php  echo $line;?></div>
						<?php  } } ?>
						</div>
					<?php  } ?>
				</td>
			</tr>
			<?php  } ?>
			<tr>
				<th></th>
				<td>
					<button type="submit" class="btn btn-primary span3" name="submit" value="提交">检测BOM异常</button>
					<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
				</td>
			</tr>
		</table>
	</form>
	<?php  } ?>
</div>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>
