<?php

defined('IN_IA') or exit('Access Denied');
class ShowimageModuleSite extends WeModuleSite {

    public function doWebDemo() {
        global $_W,$_GPC;
        $typeId = $_GPC['type_id'];
        if(empty($typeId)){
            echo "<script>alert('获取数据失败！')</script>";exit;
        }
        $types = pdo_fetch("SELECT * FROM ".tablename('showimage_type')." WHERE id=".$typeId);
        $operation = empty($_GPC['op'])?'manage':$_GPC['op'];
        /*上调*/
        $order_id = $_GPC['order_id'];
        $tDemo = pdo_fetch('SELECT * FROM ' . tablename('showimage_demo').' WHERE wid='.$_W['weid']." AND type=".$typeId);
        if($operation == "order_up"){
            $oDemo = pdo_fetch("SELECT * FROM ".tablename('showimage_picture')." WHERE id='".$order_id."'");
            if($oDemo['sindex']>1){
                pdo_query("UPDATE ".tablename('showimage_picture')." SET sindex=".($oDemo['sindex'])." WHERE sid='".$tDemo['id']."' AND sindex='".($oDemo['sindex']-1)."'");
                pdo_query("UPDATE ".tablename('showimage_picture')." SET sindex=".($oDemo['sindex']-1)." WHERE id='".$order_id."'");
            }
            $operation = "manage";
        }
        /*下调*/
        if($operation == "order_down"){
            $oDemo = pdo_fetch("SELECT * FROM ".tablename('showimage_picture')." WHERE id='".$order_id."'");
            $lastDemo = pdo_fetch("SELECT * FROM ".tablename('showimage_picture')." WHERE sid=".$tDemo['id']." ORDER BY sindex DESC LIMIT 1");
            $sindex2 = intval($oDemo['sindex']+1);
            if($oDemo['sindex'] < $lastDemo['sindex']){
                pdo_query("UPDATE ".tablename('showimage_picture')." SET sindex=".($oDemo['sindex'])." WHERE sid='".$tDemo['id']."' AND sindex='".($oDemo['sindex']+1)."'");
                pdo_query("UPDATE ".tablename('showimage_picture')." SET sindex=".$sindex2." WHERE id=".$order_id);
            }
            $operation = "manage";
        }
        /*最上*/
        if($operation == 'order_maxup'){
            $oDemo = pdo_fetch("SELECT * FROM ".tablename('showimage_picture')." WHERE id='".$order_id."'");
            for($i = ($oDemo['sindex']-1);$i>=1;$i--){
                pdo_query("UPDATE ".tablename('showimage_picture')." SET sindex=".($i+1)." WHERE sid='".$tDemo['id']."' AND sindex='".$i."'");
            }
            pdo_query("UPDATE ".tablename('showimage_picture')." SET sindex=1 WHERE id=".$order_id);
            $operation = "manage";
        }
        if($operation == 'order_maxdown'){
            $oDemo = pdo_fetch("SELECT * FROM ".tablename('showimage_picture')." WHERE id='".$order_id."'");
            $lastDemo = pdo_fetch("SELECT * FROM ".tablename('showimage_picture')." WHERE sid=".$tDemo['id']." ORDER BY sindex DESC LIMIT 1");
            for($i = ($oDemo['sindex']+1);$i <= $lastDemo['sindex'];$i++){
                pdo_query("UPDATE ".tablename('showimage_picture')." SET sindex=".($i-1)." WHERE sid=".$tDemo['id']." AND sindex='".$i."'");
            }
            pdo_query("UPDATE ".tablename('showimage_picture')." SET sindex=".$lastDemo['sindex']." WHERE id=".$order_id);
            $operation = "manage";
        }
        if($operation == 'manage'){     //菜单分类列表
            if(!empty($_GPC['alldelete'])){
                $ddemo = pdo_fetch('SELECT * FROM ' . tablename('showimage_demo').' WHERE wid='.$_W['weid']." AND type=".$typeId);
                pdo_delete('showimage_demo',array("wid"=>$_W['weid'],'type'=>$typeId));
                pdo_delete('showimage_picture',array("sid"=>$ddemo['id']));
            }
            $demo = pdo_fetch('SELECT * FROM ' . tablename('showimage_demo').' WHERE wid='.$_W['weid']." AND type=".$typeId);
            $list = pdo_fetchall("SELECT * FROM ".tablename('showimage_picture')." WHERE sid='".$demo['id']."' ORDER BY sindex");
            include $this->template('demo');
        }
        /*分享*/
        if($operation == 'share'){
            $item = pdo_fetch("SELECT * FROM ".tablename('showimage_demo')." WHERE wid='".$_W['weid']."' AND type=".$typeId);
            if(checksubmit('submit')){
                $insert = array(
                    "wid"=>$_W['weid'],
                    'type'=>$typeId,
                    'share'=>$_GPC['share'],
                    'share_picture'=>$_GPC['share_picture'],
                );
                if(empty($item)){
                    pdo_insert('showimage_demo',$insert);
                }else{
                    pdo_update("showimage_demo",$insert,array("wid"=>$_W['weid'],'type'=>$typeId));
                }
                message("操作成功！",'','success');
            }
            include $this->template('demo_share');
        }
        if($operation == 'audio_add'){
            $delete_id = $_GPC['delete_id'];
            if(!empty($delete_id)){
                $item1 = pdo_fetch("SELECT * FROM ".tablename('showimage_demo')." WHERE id='".$delete_id."'");
                unlink($_W['attachurl'].$item1['music']);
                pdo_update("showimage_demo",array("music"=>'','title'=>''),array("id"=>$delete_id));
                message("操作成功！",referer(),'success');
            }
            if(checksubmit('submit')){
                $path = IA_ROOT."/resource/attachment/audio/";
                if (true)
                {
                    if ($_FILES["audio"]["error"] > 0)
                    {
                        message('添加失败','','tips');
                    }
                    else
                    {
                        $fileName = trim($_FILES['audio']['name']);
                        $titleArr = explode('.',$fileName);
                        $vtitle = date('md',time()).random(8).".".$titleArr[1];
                        $r = move_uploaded_file($_FILES["audio"]["tmp_name"],
                            $path . $vtitle);
                        if($r){
                            $isHas = pdo_fetch("SELECT * FROM ".tablename('showimage_demo')." WHERE type=".$typeId);
                            $insert = array(
                                'wid'=>$_W['weid'],
                                'music'=>'/audio/'.$vtitle,
                                'title'=>$fileName,
                                'type'=>$typeId
                            );
                            if(empty($isHas)){
                                pdo_insert('showimage_demo',$insert);
                            }else{
                                pdo_update('showimage_demo',$insert,array("id"=>$isHas['id']));
                            }
                            message("操作成功！",'','success');
                        }else{
                            message('上传失败！','','tips');
                        }
                    }
                }
            }
            $item = pdo_fetch("SELECT * FROM ".tablename('showimage_demo')." WHERE wid='".$_W['weid']."' AND type=".$typeId);
            include $this->template('demo_audio_add');
        }
        /*添加首页静态图*/
        if($operation == "start_add"){
            $delete_id = $_GPC['delete_id'];
            if(!empty($delete_id)){
                $item1 = pdo_fetch("SELECT * FROM ".tablename('showimage_demo')." WHERE id='".$delete_id."'");
                unlink($_W['attachurl'].$item1['start_picture']);
                pdo_update("showimage_demo",array("start_picture"=>''),array("id"=>$delete_id));
                message("操作成功！",referer(),'success');
            }
            $demo = pdo_fetch('SELECT * FROM ' . tablename('showimage_demo').' WHERE wid='.$_W['weid']." AND type=".$typeId);

            if(checksubmit('submit')){
                $start_picture = $_GPC['start_picture'];
                $insert = array(
                    "wid"=>$_W['weid'],
                    'type'=>$typeId,
                    'start_picture'=>$start_picture
                );
                if(empty($demo)){
                    pdo_insert('showimage_demo',$insert);
                }else{
                    pdo_update("showimage_demo",$insert,array("wid"=>$_W['weid'],'type'=>$typeId));
                }
                message("操作成功！",'','success');
            }
            include $this->template('demo_start_add');

        }
        if($operation == 'picture_add'){    //添加、修改菜单分类
            $demo = pdo_fetch('SELECT * FROM ' . tablename('showimage_demo').' WHERE wid='.$_W['weid']." AND type=".$typeId);
            if(empty($demo)){
                pdo_insert('showimage_demo',array("wid"=>$_W['weid'],'type'=>$typeId));
            }
            $demo = array();
            $demo = pdo_fetch('SELECT * FROM ' . tablename('showimage_demo').' WHERE wid='.$_W['weid']." AND type=".$typeId);
            $picture = pdo_fetch("SELECT * FROM ".tablename('showimage_picture')." WHERE sid='".$demo['id']."'  ORDER BY sindex DESC LIMIT 1");
            if(checksubmit('submit')){
                if(empty($_GPC['add_id'])){
                    if(empty($picture)){
                        $index = 1;
                    }else{
                        $index = ($picture['sindex']+1);
                    }

                        $insert = array(
                            'wid'=>$_W['weid'],
                            'sid'=>$demo['id'],
                            'picture1'=>$_GPC['picture1'],
                            'picture2'=>$_GPC['picture2'],
                            'picture3'=>$_GPC['picture3'],
                            'time'=>time(),
                            'sindex'=>$index,
                        );
                        if(!empty($_GPC['picture1'])){
                            pdo_insert('showimage_picture',$insert);
                        }
                }else{
                    $insert = array(
                        'picture1'=>$_GPC['picture1'],
                        'picture2'=>$_GPC['picture2'],
                        'picture3'=>$_GPC['picture3'],
                    );
                    pdo_update('showimage_picture',$insert,array("id"=>$_GPC['add_id']));
                }
                message('操作成功！', create_url('site/module/demo', array('name' => 'showimage', 'op' => 'manage','type_id'=>$typeId)), 'success');
            }
           if(!empty($_GPC['add_id'])){
                $item = pdo_fetch("SELECT * FROM ".tablename('showimage_picture')." WHERE id=".$_GPC['add_id']);
            }
            include $this->template('demo_picture_add');
        }
        /*设置播放顺序*/
        if($operation == 'play_order'){
            $demo = pdo_fetch('SELECT * FROM ' . tablename('showimage_demo').' WHERE wid='.$_W['weid']." AND type=".$typeId);
            if(checksubmit('submit')){
                $insert = array(
                    'wid'=>$_W['weid'],
                    'type'=>$typeId,
                    'play_order'=>$_GPC['play_order']
                );
                if(empty($demo)){
                    pdo_insert('showimage_demo',$insert);
                }else{
                    pdo_update('showimage_demo',$insert,array("id"=>$demo['id']));
                }
                message('操作成功！', create_url('site/module/demo', array('name' => 'showimage', 'op' => 'manage','type_id'=>$typeId)), 'success');
            }
            include $this->template('demo_play_order');
        }
        if($operation == 'delete'){ //删除
            if(!empty($_GPC['delete_id'])){
                $dDemo = pdo_fetch("SELECT * FROM ".tablename('showimage_picture')." WHERE id='".$_GPC['delete_id']."'");
                $demo = pdo_fetch('SELECT * FROM ' . tablename('showimage_demo').' WHERE wid='.$_W['weid']." AND type=".$typeId);
                $lastDemo = pdo_fetch("SELECT * FROM ".tablename('showimage_picture')." WHERE sid='".$demo['id']."' ORDER BY sindex DESC");
                for($i = $dDemo['sindex'];$i < $lastDemo['sindex'];$i++){
                    pdo_query("UPDATE ".tablename('showimage_picture')." SET sindex=".$i." WHERE sid=".$demo['id']." AND sindex='".($i+1)."'");
                }
               pdo_delete('showimage_picture',array('id'=>$_GPC['delete_id']));
                message('删除成功！', create_url('site/module/demo', array('name' => 'showimage', 'op' => 'manage','type_id'=>$typeId)), 'success');
            }else{
                message('删除失败！', create_url('site/module/demo', array('name' => 'showimage', 'op' => 'manage','type_id'=>$typeId)), 'success');
            }
        }
    }

    public function doWebType(){
        global $_W,$_GPC;
        $operation = empty($_GPC['op'])?'manage':$_GPC['op'];
        if($operation == 'manage'){
            $list = pdo_fetchall("SELECT * FROM ".tablename('showimage_type')." WHERE wid='".$_W['weid']."'");
            include $this->template('type');
        }
        if($operation == 'add'){
            if(checksubmit('submit')){
                $insert = array(
                    'wid'=>$_W['weid'],
                    'model'=>$_GPC['model'],
                    'title'=>$_GPC['title'],
                );
                pdo_insert('showimage_type',$insert);
                message('操作成功！', create_url('site/module/type', array('name' => 'showimage', 'op' => 'manage')), 'success');
            }
            include $this->template('type_add');
        }
        if($operation == 'delete'){
            $deleteId = $_GPC['delete_id'];
            if(!empty($deleteId)){
                pdo_delete('showimage_type',array("id"=>$deleteId));
                $demo = pdo_fetch("SELECT * FROM ".tablename('showimage_demo')." WHERE type='".$deleteId."'");
                pdo_delete('showimage_demo',array('type'=>$deleteId));
                pdo_delete('showimage_picture',array("sid"=>$demo['id']));
                message('删除成功！', create_url('site/module/type', array('name' => 'showimage', 'op' => 'manage')), 'success');
            }
        }
    }

    public function doMobileDemo(){
        global $_W,$_GPC;
        $typeId = $_GPC['typeid'];
        if(empty($typeId)){
            echo "<script>alert('数据获取失败！')</script>";exit;
        }
        $types = pdo_fetch("SELECT * FROM ".tablename('showimage_type')." WHERE id=".$typeId);
        $demo = pdo_fetch('SELECT * FROM '.tablename('showimage_demo').' WHERE wid='.$_W['weid']." AND type=".$typeId);
        $list = pdo_fetchall("SELECT * FROM ".tablename('showimage_picture')." WHERE  sid='".$demo['id']."' ORDER BY sindex");
        if($types['model'] == 1){
            include $this->template('demo1');
        }else if($types['model'] == 2){
            include $this->template('demo2');
        }

    }

}