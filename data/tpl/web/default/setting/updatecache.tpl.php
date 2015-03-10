<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
	<div class="main">
		<form action="" method="post" class="form-horizontal form">
			<h4>更新缓存</h4>
			<table class="tb">
				<tr>
					<th><label for="">缓存类型</label></th>
					<td>
						<label for="type_data" class="checkbox inline"><input type="checkbox" name="type[]" value="data" id="type_data" checked="checked" /> 数据缓存</label><label for="type_template" class="checkbox inline"><input type="checkbox" name="type[]" value="template" id="type_template" checked="checked" /> 模板缓存</label>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input name="submit" type="submit" value="提交" class="btn btn-primary span3" />
						<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
					</td>
				</tr>
			</table>
		</form>
	</div>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>