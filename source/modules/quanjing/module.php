<?php
/**
 * 3D展示模块定义
 *
 * @author hy
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class QuanjingModule extends WeModule {
	public $tablename = '3d_reply';
	public function settingsDisplay($settings) {
		//点击模块设置时将调用此方法呈现模块设置页面，$settings 为模块设置参数, 结构为数组。这个参数系统针对不同公众账号独立保存。
		//在此呈现页面中自行处理post请求并保存设置参数（通过使用$this->saveSettings()来实现）
		if(checksubmit()) {
			//字段验证, 并获得正确的数据$dat
			$this->saveSettings($dat);
		}
		//这里来展示设置项表单
		include $this->template('setting');
	}
	public function fieldsFormDisplay($rid = 0) {
		global $_W;
      	if (!empty($rid)) {
			$reply = pdo_fetch("SELECT * FROM ".tablename($this->tablename)." WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));		
 		} 
		include $this->template('quanjing/form');
	}
  	public function fieldsFormValidate($rid = 0) {
		
        return true;
	}
  	public function fieldsFormSubmit($rid = 0) {
		global $_GPC, $_W;
 
        $id = intval($_GPC['reply_id']);
		$insert = array(
			'rid' => $rid,
			'title' => $_GPC['title'],
			'description' => $_GPC['description'],
			'thumb' => $_GPC['picture'],
		);
        //处理图片
      	if (!empty($_GPC['picture'])) {
			file_delete($_GPC['picture-old']);
		} else {
			unset($insert['thumb']);
		}
		if (empty($id)) {
			$id=pdo_insert($this->tablename, $insert);
		} else {
			pdo_update($this->tablename, $insert, array('id' => $id));
		}
      	return true;
	}
	//==================================================================================================
	//预览
	public function doview() {
		global $_GPC, $_W;
		$weid=$_GET["weid"];
		$sid=$_GET["sid"];
		$info = pdo_fetch("SELECT * FROM ".tablename('3d')." WHERE weid = ".$weid." and sid= ".$sid );
		$config = pdo_fetch("SELECT * FROM ".tablename('3d_set')." where weid = ".$weid );
		$config['picturl']=$_W['attachurl'].$config['picture'];
		include $this->template('quanjing/index');	

	}

	//编辑全景
	public function doedit() {
		global $_GPC, $_W;
		//表单提交路径
		$sid = $_GPC['sid'];
		$info = pdo_fetch("SELECT * FROM ".tablename('3d')." WHERE weid = ".$_W['weid']." and sid= ".$sid." limit 1" );
		if($_GPC["action"]=="create")
		{
				if($_GPC["sid"]=='')
				{
					$sid = time();
					//echo "sid为空";
				}else
				{
					$sid = $_GPC["sid"];	
					//echo "不为空";
				}
				
				//echo $info;
				//生成XML文档
				$dom = new DOMDocument('1.0', 'utf-8');

				$root = $dom->createElement("panorama"); 
				$dom->appendChild($root); 
				$dom->formatOutput=true; 
				// 节点创建区
				$item = $dom->createElement("view"); 
				$root->appendChild($item); 
				
				$start = $dom->createElement("start"); 
				$item->appendChild($start); 
				
				$min = $dom->createElement("min"); 
				$item->appendChild($min); 
				
				$max = $dom->createElement("max"); 
				$item->appendChild($max); 
				
				$autorotate = $dom->createElement("autorotate"); 
				$root->appendChild($autorotate); 
				
				$input = $dom->createElement("input"); 
				$root->appendChild($input); 
				
				$userdata = $dom->createElement("userdata"); 
				$root->appendChild($userdata); 
				
				$control = $dom->createElement("control"); 
				$root->appendChild($control); 
				
				$sounds = $dom->createElement("sounds"); 
				$root->appendChild($sounds); 
				
				// 节点属性区
				$price = $dom->createAttribute("fovmode"); 
				$item->appendChild($price); 
				//
				$priceValue = $dom->createTextNode("0"); 
				$price->appendChild($priceValue); 
				//
				$pan = $dom->createAttribute("pan"); 
				$start->appendChild($pan); 
				$panValue = $dom->createTextNode("0"); 
				$pan->appendChild($panValue); 
				
				$tilt = $dom->createAttribute("tilt"); 
				$start->appendChild($tilt); 
				$tiltValue = $dom->createTextNode("0"); 
				$tilt->appendChild($tiltValue); 
				
				$fov = $dom->createAttribute("fov"); 
				$start->appendChild($fov); 
				$fovValue = $dom->createTextNode("70"); 
				$fov->appendChild($fovValue); 
				
				//
				$pan = $dom->createAttribute("pan"); 
				$min->appendChild($pan); 
				$panValue = $dom->createTextNode("0"); 
				$pan->appendChild($panValue); 
				
				$tilt = $dom->createAttribute("tilt"); 
				$min->appendChild($tilt); 
				$tiltValue = $dom->createTextNode("-90"); 
				$tilt->appendChild($tiltValue); 
				
				$fov = $dom->createAttribute("fov"); 
				$min->appendChild($fov); 
				$fovValue = $dom->createTextNode("5"); 
				$fov->appendChild($fovValue); 
				//
				$pan = $dom->createAttribute("pan"); 
				$max->appendChild($pan); 
				$panValue = $dom->createTextNode("360"); 
				$pan->appendChild($panValue); 
				
				$tilt = $dom->createAttribute("tilt"); 
				$max->appendChild($tilt); 
				$tiltValue = $dom->createTextNode("90"); 
				$tilt->appendChild($tiltValue); 
				
				$fov = $dom->createAttribute("fov"); 
				$max->appendChild($fov); 
				$fovValue = $dom->createTextNode("120"); 
				$fov->appendChild($fovValue); 
				
				
				//
				$speed = $dom->createAttribute("speed"); 
				$autorotate->appendChild($speed); 
				$speedValue = $dom->createTextNode("0.100"); 
				$speed->appendChild($speedValue); 
				
				$delay = $dom->createAttribute("delay"); 
				$autorotate->appendChild($delay); 
				$delayValue = $dom->createTextNode("3.0"); 
				$delay->appendChild($delayValue); 
				
				$returntohorizon = $dom->createAttribute("returntohorizon"); 
				$autorotate->appendChild($returntohorizon); 
				$returntohorizonValue = $dom->createTextNode("0.000"); 
				$returntohorizon->appendChild($returntohorizonValue); 
				
				$startloaded = $dom->createAttribute("startloaded"); 
				$autorotate->appendChild($startloaded); 
				$startloadedValue = $dom->createTextNode("1"); 
				$startloaded->appendChild($startloadedValue); 
				
				// input 属性 
				$tilesize = $dom->createAttribute("tilesize"); 
				$input->appendChild($tilesize); 
				$tilesizeValue = $dom->createTextNode("500"); 
				$tilesize->appendChild($tilesizeValue); 
				
				$tilescale = $dom->createAttribute("tilescale"); 
				$input->appendChild($tilescale); 
				$tilescaleValue = $dom->createTextNode("1.01335"); 
				$tilescale->appendChild($tilescaleValue); 
				
				$tile0url = $dom->createAttribute("tile0url"); 
				$input->appendChild($tile0url); 
				$tile0urlValue = $dom->createTextNode($_W['attachurl'].$_GPC["p_1"]); 
				$tile0url->appendChild($tile0urlValue); 
				
				$tile1url = $dom->createAttribute("tile1url"); 
				$input->appendChild($tile1url); 
				$tile1urlValue = $dom->createTextNode($_W['attachurl'].$_GPC["p_2"]); 
				$tile1url->appendChild($tile1urlValue); 
				
				$tile2url = $dom->createAttribute("tile2url"); 
				$input->appendChild($tile2url); 
				$tile2urlValue = $dom->createTextNode($_W['attachurl'].$_GPC["p_3"]); 
				$tile2url->appendChild($tile2urlValue); 
				
				$tile3url = $dom->createAttribute("tile3url"); 
				$input->appendChild($tile3url); 
				$tile3urlValue = $dom->createTextNode($_W['attachurl'].$_GPC["p_4"]); 
				$tile3url->appendChild($tile3urlValue); 
				
				$tile4url = $dom->createAttribute("tile4url"); 
				$input->appendChild($tile4url); 
				$tile4urlValue = $dom->createTextNode($_W['attachurl'].$_GPC["p_5"]); 
				$tile4url->appendChild($tile4urlValue); 
				
				$tile5url = $dom->createAttribute("tile5url"); 
				$input->appendChild($tile5url); 
				$tile5urlValue = $dom->createTextNode($_W['attachurl'].$_GPC["p_6"]); 
				$tile5url->appendChild($tile5urlValue); 
				
				
				$title = $dom->createAttribute("title"); 
				$userdata->appendChild($title);
				
				$description = $dom->createAttribute("description"); 
				$userdata->appendChild($description);
				
				$author = $dom->createAttribute("author"); 
				$userdata->appendChild($author);
				
				$datetime = $dom->createAttribute("datetime"); 
				$userdata->appendChild($datetime);
				
				$copyright = $dom->createAttribute("copyright"); 
				$userdata->appendChild($copyright);
				
				$source = $dom->createAttribute("source"); 
				$userdata->appendChild($source);
				
				$info = $dom->createAttribute("info"); 
				$userdata->appendChild($info);
				
				$comment = $dom->createAttribute("comment"); 
				$userdata->appendChild($comment);
				
				// 
				$sensitifity = $dom->createAttribute("sensitifity"); 
				$control->appendChild($sensitifity);
				$sensitifityValue = $dom->createTextNode("8"); 
				$sensitifity->appendChild($sensitifityValue);
				
				$simulatemass = $dom->createAttribute("simulatemass"); 
				$control->appendChild($simulatemass);
				$simulatemassValue = $dom->createTextNode("1"); 
				$simulatemass->appendChild($simulatemassValue);
				
				$lockedmouse = $dom->createAttribute("lockedmouse"); 
				$control->appendChild($lockedmouse);
				$lockedmouseValue = $dom->createTextNode("0"); 
				$lockedmouse->appendChild($lockedmouseValue);
				
				$lockedkeyboard = $dom->createAttribute("lockedkeyboard"); 
				$control->appendChild($lockedkeyboard);
				$lockedkeyboardValue = $dom->createTextNode("0"); 
				$lockedkeyboard->appendChild($lockedkeyboardValue);
				
				$lockedwheel = $dom->createAttribute("lockedwheel"); 
				$control->appendChild($lockedwheel);
				$lockedwheelValue = $dom->createTextNode("0"); 
				$lockedwheel->appendChild($lockedwheelValue);
				
				$invertwheel = $dom->createAttribute("invertwheel"); 
				$control->appendChild($invertwheel);
				$invertwheelValue = $dom->createTextNode("0"); 
				$invertwheel->appendChild($invertwheelValue);
				
				$speedwheel = $dom->createAttribute("speedwheel"); 
				$control->appendChild($speedwheel);
				$speedwheelValue = $dom->createTextNode("1"); 
				$speedwheel->appendChild($speedwheelValue);
				
				$dblclickfullscreen = $dom->createAttribute("dblclickfullscreen"); 
				$control->appendChild($dblclickfullscreen);
				$dblclickfullscreenValue = $dom->createTextNode("0"); 
				$dblclickfullscreen->appendChild($dblclickfullscreenValue);
				
				$invertcontrol = $dom->createAttribute("invertcontrol"); 
				$control->appendChild($invertcontrol);
				$invertcontrolValue = $dom->createTextNode("1"); 
				$invertcontrol->appendChild($invertcontrolValue);
				$xml_url = "source/modules/quanjing/template/".$_W["weid"]."_".$sid.".xml";
				$dom->save($xml_url); 
//echo "xml可以写";
				//执行绑定
				$insert['p1']=$_GPC["p_1"];
				$insert['p2']=$_GPC["p_2"];
				$insert['p3']=$_GPC["p_3"];
				$insert['p4']=$_GPC["p_4"];
				$insert['p5']=$_GPC["p_5"];
				$insert['p6']=$_GPC["p_6"];
				$insert['xml_url']=$xml_url;
				$insert['weid']=$_W["weid"];
				$insert['sid']=$sid;
				$insert['title']=$_GPC["title"];
//echo "变量获取成功";
				$zhixing_=pdo_update('3d', $insert,array('sid' => $_GPC['sid']));
				$weid=$_W['weid'];
				$pindex = max(1, intval($_GPC['page']));
				$psize = 30;
				$list = pdo_fetchall("SELECT * FROM ".tablename('3d')." WHERE weid = ".$_W["weid"]." ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
				$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('3d')." WHERE weid = ".$_W["weid"]);
				$pager = pagination($total, $pindex, $psize);
				include $this->template('quanjing/list');	

		}
		$weid=$_W["weid"];
		$url_action = create_url('index/module', array('do' => 'edit', 'name' => 'quanjing', 'weid' => $_W['weid']));
		include $this->template('quanjing/create');	
	}
	//删除全景展示
	public function dodel() {
		global $_GPC, $_W;
		$weid=$_GET["weid"];
		$sid=$_GET["sid"];
		$info = pdo_fetch("SELECT p1,p2,p3,p4,p5,p6,xml_url FROM ".tablename('3d')." WHERE weid = ".$_W['weid']." and sid= ".$sid." limit 1" );
		foreach($info as $file)
		{
			if (file_exists($file))
			{
				unlink($file);
			}
		}
		$set = pdo_delete('3d', array('sid' => $sid));
		$pindex = max(1, intval($_GPC['page']));
		$psize = 30;
		
		$list = pdo_fetchall("SELECT * FROM ".tablename('3d')." WHERE weid = ".$weid." ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('3d')." WHERE weid = ".$weid);
		$pager = pagination($total, $pindex, $psize);
		include $this->template('quanjing/list');
	}
	
}


  