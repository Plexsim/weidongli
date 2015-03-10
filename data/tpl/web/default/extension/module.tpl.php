<?php defined('IN_IA') or exit('Access Denied');?><?php  include template('common/header', TEMPLATE_INCLUDEPATH);?>
<script type="text/javascript">
$(function() {
	$('.module').delegate('.media-description-button', 'click', function(){ //控制模块详细信息//零壹贰科技
		$(this).parents('.item').find('.media-description').toggle();
		return false;
	});
});
</script>
<style type="text/css">
small a{color:#999;}
.module{padding:15px;}
.form h4{margin-bottom:0;}
</style>
<?php  include template('extension/module-tabs', TEMPLATE_INCLUDEPATH);?>
<div class="main">
	<?php  if($do == 'installed') { ?>
	<div class="form">
		<h4>已安装的功能</h4>
	</div>
	<div class="module form-horizontal">
		<?php  if(is_array($modules)) { foreach($modules as $row) { ?>
		<div class="item">
			<div class="media">
				<div class="pull-right" style="width:230px;">
					<div class="input-prepend">
					</div>
					<div class="module-set">
						<?php  if($row['version_error']) { ?>
						版本不兼容 <a href="<?php  echo create_url('extension/module/convert', array('id' => strtolower($row['name'])))?>" style="color:red;">转换版本</a>
						<?php  } else { ?>
						<?php  if(!$row['issystem']) { ?>
						<a onclick="return confirm('卸载模块会删其相关数据，确定吗？'); return false;" href="<?php  echo create_url('extension/module/uninstall', array('id' => $row['name']))?>">卸载</a>
						<?php  } ?>
						<?php  } ?>
						<span class="upgrade-label" module="<?php  echo $row['name'];?>" version="<?php  echo $row['version'];?>"><?php  if($row['upgrade']) { ?><a href="<?php  echo create_url('extension/module/upgrade', array('id' => $row['name']));?>" style="color:red;" title="来自本地文件更新">更新</a><?php  } ?></span>
						<a href="<?php  echo create_url('extension/module/permission', array('id' => $row['name']))?>">访问权限</a>
						&nbsp;
					</div>
				</div>
				<img class="media-object img-rounded" src="./source/modules/<?php  echo $row['name'];?>/icon.jpg" onerror="this.src='./resource/image/module-nopic-small.jpg'">
				<div class="media-body">
					<h4 class="media-heading"><?php  echo $row['title'];?><small></small> <em class="upgrade-label-tips" module="<?php  echo $row['name'];?>" style="color:red;display:none;">New</em></h4>
					<span><?php  echo $row['ability'];?>&nbsp;<a href="#" class="media-description-button">详细介绍</a></span >
				</div>
			</div>
			<div class="media-description">
				<b>功能介绍：</b>
				<span><?php  echo $row['description'];?></span>
			</div>
		</div>
		<?php  } } ?>
	</div>
	<script type="text/javascript">
		$(function(){
			$.post('<?php  echo create_url('extension/module/check');?>', {foo: 'upgrade'},function(dat){
				try {
					var ret = $.parseJSON(dat);
					$('.upgrade-label').each(function(){
						var n = $(this).attr('module');
						var v = $(this).attr('version');
						if(ret[n] && ret[n].version > v) {
							var tips = '';
							if(ret[n].from == 'local') {
								tips = '来自本地文件更新';
							} else {
								tips = '来自吾爱传媒云服务更新';
							}
							$(this).html('<a href="<?php  echo create_url('extension/module/upgrade')?>&id=' + n + '" style="color:red;" title="' + tips + '">更新</a>');
							$('.upgrade-label-tips[module=' + n + ']').show();
						}
					});
				} catch(err) {}
			});
		});
	</script>
	<?php  } ?>
	<?php  if($do == 'prepared') { ?>
	
	<div class="form">
		<h4>可安装的功能</h4>
	</div>
	<div class="module form-horizontal">
		<?php  if(is_array($localUninstallModules)) { foreach($localUninstallModules as $row) { ?>
		<div class="item" module-name="<?php  echo $row['name'];?>">
			<div class="media">
				<div class="pull-right" style="width:230px;">
					<div class="module-set">
						<?php  if($row['version_error']) { ?>
						版本不兼容 <a href="<?php  echo create_url('extension/module/convert', array('id' => strtolower($row['name'])))?>" style="color:red;">转换版本</a>
						<?php  } else { ?>
						<a href="<?php  echo create_url('extension/module/install', array('id' => strtolower($row['name'])))?>">安装</a>
						<?php  } ?>
					
						&nbsp;
					</div>
				</div>
				<img class="media-object img-rounded gray" src="./source/modules/<?php  echo strtolower($row['name']);?>/icon.jpg" onerror="this.src='./resource/image/module-nopic-small.jpg'">
				<div class="media-body">
					<h4 class="media-heading"><?php  echo $row['title'];?><small></small></h4>
					
				</div>
			</div>
			<div class="media-description">
				<b>功能介绍：</b>
				<span>
					<?php  echo $row['description'];?>
				</span>
			</div>
		</div>
		<?php  } } ?>
	</div>
	<script type="text/javascript">
		function listInstallModules($scope, $http) {
			$.post('<?php  echo create_url('extension/module/check');?>', {foo: 'install'},function(dat){
				try {
					var ret = $.parseJSON(dat);
					if(!$.isArray(ret)) {
						return;
					}
					$.each(ret, function(){
						$('div.item[module-name=' + this.name + ']').remove();
					});
					$scope.$apply(function(){
						$scope.modules = ret;
					});
				} catch(err) {}
			});
		}
	</script>
	<?php  } ?>
</div>
<?php  include template('common/footer', TEMPLATE_INCLUDEPATH);?>
