<?php
/**
 * 照片墙模块微站定义
 *
 * @author 珊瑚海
 * @url http://www.vfanm.com/
 */
defined('IN_IA') or exit('Access Denied');

class PhotosModuleSite extends WeModuleSite {

	public function doWebDisplay() {
		global $_GPC, $_W;
		if (!empty($_W['ispost'])) {
			$ret = $_GPC['ret'] == 'true';
			$set = @json_decode(base64_decode($_GPC['dat']), true);
			$ree = pdo_fetch("SELECT status FROM ".tablename('photos_data')." WHERE id = '{$set}'");
			if ($ree['status'] == '0') {
				$re = '1';
			}else{
				$re = '0';
			}
			if (pdo_update('photos_data',array('status' => $re, ),array('id'=> $set))) {
				exit('success');
			}
		}
		$pindex = max(1, intval($_GPC['page']));
		$psize = 50;
		$list = pdo_fetchall("SELECT * FROM ".tablename('photos_data')." WHERE weid = '{$_W['weid']}' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize);
		$total = pdo_fetchcolumn("SELECT COUNT(*) FROM ".tablename('photos_data')." WHERE weid = '{$_W['weid']}'");
		$pager = pagination($total, $pindex, $psize);
		include $this->template('display');
	}

	public function doMobileDisplay(){
		global $_GPC, $_W;
		$config = $this->module['config'];
		$topinfo = isset($config['topinfo']) ? $config['topinfo'] : '点击+选取照片，点击发送后输入对应文字描述提交照片';
		include $this->template('display');
	}

	public function doMobileDetail() {
		global $_W, $_GPC;
		$id = intval($_GPC['id']);
		$photo = pdo_fetch("SELECT * FROM ".tablename('photos_data')." WHERE id = :id", array(':id' => $id));
		if (empty($photo)) {
			message('照片不存在或是已经被删除！');
		}
		include $this->template('detail');
	}

	public function doMobilegetData(){
    	global $_W,$_GPC;
    	$psize = $_GPC['pagesize'];
    	$pindex = max(1, intval($_GPC['pageindex']));
		$pic_log_list = pdo_fetchall("SELECT * FROM ".tablename('photos_data')." WHERE weid = '{$_W['weid']}' AND status = '1' ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize .',' .$psize);
		$pic_total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('photos_data') . " WHERE status = '1'");
		//$result['pager'] = pagination($total, $pindex, $psize);
		//print_r($pic_log_list);
        $jsonstr = 'picResult(';
        $jsonstr .= '          {"msg":"ok",';
        $jsonstr .= '           "picture":[';
        foreach ( $pic_log_list as $k => $row ) {
           
           if($k !== 0){
           	$jsonstr .= ',';
           }
           $jsonstr .= '                    {';
           $jsonstr .= '                      "createts":'.$row['time'].',';
	       $jsonstr .= '                      "height":0,';
	       $jsonstr .= '                      "id":'.$row['id'].',';
	       $jsonstr .= '                      "nickname":"'.$row['from_user'].'",';
	       $jsonstr .= '                      "thumbnailurl":"",';
	       $jsonstr .= '                      "url":"'.$_W['attachurl'].$row['url'].'",';
	       $jsonstr .= '                      "width":0';
           $jsonstr .= '                    }';
        }
        $jsonstr .= '                    ],';
        $jsonstr .= '           "ret":0,';
        $jsonstr .= '           "total":'.$pic_total.'})';
        die($jsonstr);
    }

}