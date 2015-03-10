<?php
/**
 * QQ群：304081212
 * 作者：晓楚, 547753994
 *
 * 网站：www.xuehuar.com
 */


defined('IN_IA') or exit('Access Denied');
define('CURRENT_VERSION', 0.1);
class StoreFrontEndModuleSite extends WeModuleSite {
  // 输入：modules目录
  // 输出：目录下所有模块的信息，包括版本号，模块名称
  private function scanModules() {
    $mroot = IA_ROOT . '/source/modules/';
    $mdir = '';
    if (is_dir($mroot)) {
      if ($handle = opendir($mroot)) {
        while(false !== ($mdir = readdir($handle))) {
          if ($mdir != '.' and $mdir != '..' and is_dir($mroot . $mdir)) {
            echo $mdir . "\n";
          }
        }
      }
    }
  }

  private function scanInstalledModule() {
    $list = pdo_fetchall("SELECT url, id FROM " . tablename('app_upgrade_log'), array(), 'id');
    return $list;
  }

  private function scanLocalModule() {
    $list = pdo_fetchall("SELECT name, version FROM " . tablename('modules'));
    return $list;
  }

  public function doWebStoreAdvice() {
    global $_W,$_GPC;
    include $this->template('store_advice');
  }

  public function doWebAppDetail() {
    global $_W,$_GPC;
    $appname = $_GPC['appname'];
    $appid = $_GPC['id'];
    if (!empty($appid)) {
      $url = "http://182.92.154.222/appstore/mobile.php?act=module&name=storebackend&do=AppDetail&id={$appid}&weid=12";
    } else if (!empty($appname)) {
      $url = "http://182.92.154.222/appstore/mobile.php?act=module&name=storebackend&do=AppDetail&appname={$appname}&weid=12";
    } else {
      message('非法参数', referer(), 'error');
    }
    $remote = ihttp_get($url);
    $content = json_decode($remote['content']);
    $item = $content->item;
    $logs = $content->logs;
    include $this->template('appdetail');
  }

  private function getUpdateInfo($remote) {
    $local = $this->scanInstalledModule();
    $list = array();
    foreach($remote as $r) {
      if ($r->url != $local[$r->id]['url'] and !empty($local[$r->id])) {
        $list[] = $r;
      }
    }
    return $list;
  }

  public function doWebMyApp() {
    global $_W, $_GPC;
    $notice = '';
    $url = "http://182.92.154.222/appstore/mobile.php?act=module&name=storebackend&do=category&weid=12";
    $remote = ihttp_get($url);
    $content = json_decode($remote['content']);
    $force_update = $content->force_update;
    $notice = $this->getNotice($content);
    $remote_list = $content->list;
    $list = $this->getUpdateInfo($remote_list);
    $my_update_count = count($list);
    $category = $content->category;
    include $this->template('index');
  }


  public function doWebApp() {
    global $_W, $_GPC;
    $notice = '';
    if (!empty($_GPC['tag'])) {
      $tag = $_GPC['tag'];
      $url = "http://182.92.154.222/appstore/mobile.php?act=module&name=storebackend&do=tag&tag={$tag}&weid=12";
    } else if (!empty($_GPC['author'])) {
      $author = $_GPC['author'];
      $url = "http://182.92.154.222/appstore/mobile.php?act=module&name=storebackend&do=author&author={$author}&weid=12";
    } else if (!empty($_GPC['id'])) {
      $active_category = intval($_GPC['id']);
      $url = "http://182.92.154.222/appstore/mobile.php?act=module&name=storebackend&do=category&id={$active_category}&weid=12";
    } else {
      $url = "http://182.92.154.222/appstore/mobile.php?act=module&name=storebackend&do=category&weid=12";
      $local = $this->scanInstalledModule();
    }
    $remote = ihttp_get($url);
    $content = json_decode($remote['content']);
    $force_update = $content->force_update;
    $notice = $this->getNotice($content);
    $list = $content->list;
    $my_update_count = count($this->getUpdateInfo($list));
    $category = $content->category;
    $active_category = $content->active_category;
    include $this->template('index');
  }

  private function getNotice($content) {
    $version = $content->version;
    $notice = $content->notice;
    if ($version > CURRENT_VERSION) {
      $force_update = $content->force_update;
      if ($force_update == true) {
        $notice .=  "<div style='padding:15px; background-color:#ec971f; color:#fff; border:1px solid #d58512; border-left:5px solid; border-left-color: #428bca;'>应用商店有重大更新, 必须更新后才能继续使用。此更新不会影响您的数据或现有模块。 <a style='color:blue;text-decoration:underline' href='" . $this->createWebUrl('AppDetail', array('appname'=>'应用商店')) . "'>请前往更新>></a></div>";
      } else {
        $notice .=  "<div style='padding:15px; border:1px solid #eee; border-left:5px solid; border-left-color: #428bca;'>应用商店有更新，当前版本号".CURRENT_VERSION."，最新版本号{$version}，<a style='color:blue;text-decoration:underline' href='" . $this->createWebUrl('AppDetail', array('appname'=>'应用商店')) . "'>请前往更新>></a></div>";
      }
    }
    return $notice;
  }

  public function doWebDownload() {
    global $_W, $_GPC;
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: -1");
    header("Pragma: no-cache");
    $url = base64_decode($_GPC['url']);
    $appid = intval($_GPC['appid']);
    if (strpos($url, 'http://') === FAlSE) message('invalid url');
    $url .= "&host={$_SERVER['HTTP_HOST']}&user={$this->module['config']['user']}&pass={$this->module['config']['pass']}&appid={$appid}";
    $tmpfilename = time() . rand() . '.zip';
    $tmpfilepath = IA_ROOT . "/data/update/";
    $tmpfilefullpath = "{$tmpfilepath}{$tmpfilename}";
    // build ./data/update/ dir
    $contents = file_get_contents($url);
    $magic = 'APPSTORE_MAGIC';
    if ($magic == substr($contents, 0, strlen($magic))) {
      $err = substr($contents, strlen($magic));
      message($err);
    }
    mkdirs(dirname($tmpfilefullpath));
    $ret = file_put_contents($tmpfilefullpath, $contents);
    if ($ret === false) {
      message('下载模块错误，请检查写入权限' . $tmpfilefullpath, '', 'error');
    } else if (10 > ($sz= filesize($tmpfilefullpath))) {
      message("文件大小{$sz}字节，非法文件", '', 'error');
    } else {
      header('Location:' . $_W['siteroot'] . '/data/update/' . $tmpfilename);
    }
  }

  public function doWebInstall() {
    global $_W, $_GPC;
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: -1");
    header("Pragma: no-cache");
    $encoded_url = $_GPC['url'];
    $app_id = $_GPC['appid'];
    $url = base64_decode($encoded_url);
    $appid = intval($_GPC['appid']);
    if (strpos($url, 'http://') === FAlSE) message('invalid url');
    $url .= "&host={$_SERVER['HTTP_HOST']}&user={$this->module['config']['user']}&pass={$this->module['config']['pass']}&appid={$appid}";
    $tmpfilename = time() . rand() . '.zip';
    $tmpfilepath = IA_ROOT . "/data/update/";
    $tmpfilefullpath = "{$tmpfilepath}{$tmpfilename}";
    // build ./data/update/ dir
    $contents = file_get_contents($url);
    $magic = 'APPSTORE_MAGIC';
    if (empty($contents)) {
      message('unknown error', '', 'error');
    } else if ($magic == substr($contents, 0, strlen($magic))) {
      $err = substr($contents, strlen($magic));
      message($err, '', 'error');
    }
    mkdirs(dirname($tmpfilefullpath));
    $ret = file_put_contents($tmpfilefullpath, $contents);
    if ($ret === false) {
      message('下载模块错误，请检查写入权限' . $tmpfilefullpath, '', 'error');
    } else if (10 > ($sz= filesize($tmpfilefullpath))) {
      message("文件大小{$sz}字节，非法文件");
    } else {
      require_once(IA_ROOT . '/source/modules/storefrontend/class/class-pclzip.php');
      require_once(IA_ROOT . '/source/modules/storefrontend/class/class-file.php');
      $zip = new PclZip($tmpfilefullpath);
      $ret = $zip->extract($tmpfilepath);
      if (!is_array($ret)) {
        message('非法压缩文件' . $tmpfilefullpath);
      }
      $d = $ret[0]['stored_filename'];
      if (!is_dir($tmpfilepath . $d)) {
        message('非法模块压缩文件' . $d);
      }
      $module_name = substr($d, 0, strlen($d) - 1);
      if (file_exists($tmpfilefullpath)) {
        @unlink($tmpfilefullpath);
        echo '删除临时文件成功';
      }
      $mdir = IA_ROOT . "/source/modules/" . $module_name;
      $backupdir = IA_ROOT . "/data/backup/" . $module_name;
      $updatedir = IA_ROOT . "/data/update/" . $module_name;
      if (file_exists($mdir)) {
        echo FileUtil::moveDir($mdir, $backupdir, true);
        echo FileUtil::copyDir($updatedir, $mdir, true);
      } else {
        echo FileUtil::copyDir($updatedir, $mdir, true);
      }
      $log_exist_in_db = pdo_fetch("SELECT * FROM " . tablename('app_upgrade_log') . " WHERE id={$app_id}");
      if (false !== $log_exist_in_db) {
        pdo_update('app_upgrade_log', array('url'=>$encoded_url, 'createtime'=>TIMESTAMP), array('logid'=>$log_exist_in_db['logid']));
      } else {
        pdo_insert('app_upgrade_log', array('url'=>$encoded_url, 'createtime'=>TIMESTAMP, 'id'=>$app_id));
      }

      $module_exist_in_db = pdo_fetch("SELECT * FROM " . tablename('modules') . " WHERE name='{$module_name}'");
      if (false !== $module_exist_in_db) {
        header("Location:" . create_url('extension/module/upgrade', array('id'=>$module_name)));
      } else {
        header("Location:" . create_url('extension/module/install', array('id'=>$module_name)));
      }
      //echo '模块下载成功<br>模块名称:'.$module_name.',请前往安装/更新.' . '<a href="extension.php?act=module&#'.$module_name.'">前往</a>';
    }
  }
}


