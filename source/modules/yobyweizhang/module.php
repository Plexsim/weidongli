<?php
/**
 * 违章查询模块定义
 *
 * @author Yoby
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class YobyweizhangModule extends WeModule {

	public function settingsDisplay($settings) {
		global $_GPC, $_W;
		$site1 = "<a href=mobile.php?act=module&weid=".$_W['weid']."&name=yobyweizhang&do=detail'>违章查询</a>";
		$site2 ="mobile.php?act=module&weid=".$_W['weid']."&name=yobyweizhang&do=detail";
  					if(!isset($settings['city'])) {

				$settings['city'] = '西安';

			}
			$file = fopen(IA_ROOT.'/source/modules/yobyweizhang/pro.csv','r'); 
while ($data = fgetcsv($file)) {
$arr[] =$data;
 }
 fclose($file);
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
				$dat = array(
			'city' => $_GPC['city'],
			'kkk' => $_GPC['kkk'],	
			);
			$this->saveSettings($dat);
			message('保存成功', 'refresh');
		}
		
		if($_GPC['ajax']==1){
		$file1 = fopen(IA_ROOT.'/source/modules/yobyweizhang/citys.csv','r'); 
while ($data1 = fgetcsv($file1)) {
$arr1[] =$data1;
 }
 fclose($file1);
 foreach($arr1 as $v){
    if($_GPC['pro']==$v[6]){
    $q[] = array("text"=>$v[0],'cid'=>$v[1]);
    }
 }
 echo json_encode($q);
		}else{
		include $this->template('settings');}
	}

}