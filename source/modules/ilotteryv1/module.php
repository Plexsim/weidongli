<?php
/**
 * 幸运大抽奖
 * 作者:迷失卍国度
 * qq : 15595755
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');
define('CONTROLLER', 'ilotteryv1');
include 'plugin/phpexcelreader/reader.php';

class ilotteryv1Module extends WeModule
{
    public $name = 'ilotteryv1Module';
    public $title = '';
    public $ability = '';
    public $tablename = 'ilotteryv1_reply';

    public $action = 'style'; //方法
    public $modulename = 'ilotteryv1'; //模块标识
    public $actions_titles = array(
        'userlist' => '报名用户',
        'userform' => '添加用户'
    );

    public function fieldsFormSubmit($rid = 0)
    {
        global $_GPC, $_W;
        $id = intval($_GPC['reply_id']);
        $data = array(
            'rid' => $rid,
            'weid' => $_W['weid'],
            'title' => $_GPC['title'],
            'picture' => $_GPC['picture'],
            'description' => $_GPC['description'],
            'is_change' => $_GPC['is_change'],
            'only_default_user' => $_GPC['only_default_user'],
            'dateline' => TIMESTAMP
        );

        if (empty($id)) {
            pdo_insert($this->tablename, $data);
        } else {
            if (!empty($_GPC['picture'])) {
                file_delete($_GPC['picture-old']);
            } else {
                unset($data['picture']);
            }
            unset($data['dateline']);
            pdo_update($this->tablename, $data, array('id' => $id));
        }
    }

    public function fieldsFormDisplay($rid = 0)
    {
        global $_W;
        if (!empty($rid)) {
            $reply = pdo_fetch("SELECT * FROM " . tablename($this->tablename) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
            $starttime = intval($reply['starttime']) == 0 ? TIMESTAMP : $reply['starttime'];
            $endtime = intval($reply['endtime']) == 0 ? TIMESTAMP + 86400 * 10 : $reply['endtime'];
        } else {
            $starttime = TIMESTAMP;
            $endtime = TIMESTAMP + 86400 * 10;
        }
        include $this->template('ilotteryv1/form');
    }

    public function fieldsFormValidate($rid = 0)
    {
        return true;
    }

    //删除规则
    public function ruleDeleted($rid = 0)
    {
        global $_W;
        $replies = pdo_fetchall("SELECT id FROM " . tablename($this->tablename) . " WHERE rid = '$rid'");
        $deleteid = array();
        if (!empty($replies)) {
            foreach ($replies as $index => $row) {
                //file_delete($row['thumb']);
                $deleteid[] = $row['id'];
            }
        }
        pdo_delete($this->tablename, "id IN ('" . implode("','", $deleteid) . "')");
        return true;
    }

    //重置
    public function doReset()
    {
        $sql = "update ims_ilotteryv1_user set status=0";
        $query = pdo_query($sql);
        if ($query) {
            echo '1';
        }
    }

    public function doUserList()
    {
        global $_GPC, $_W;
        $weid = intval($_W['weid']);
        $rid = intval($_GPC['rid']);
        checklogin();
        $action = 'userlist';
        $title = $this->actions_titles[$action];
        $url = create_url('site/module', array('do' => $action, 'name' => $this->modulename));
        if ($rid == 0) {
            message('非法参数');
        }
        $pindex = max(1, intval($_GPC['page']));
        $psize = 15;
        $where = " WHERE weid = '{$_W['weid']}' AND rid = " . $rid;
        $userlist = pdo_fetchall("SELECT * FROM " . tablename('ilotteryv1_user') . " {$where} ORDER BY `id` DESC LIMIT " . ($pindex - 1) * $psize . ",{$psize}");

        if (!empty($userlist)) {
            $total = pdo_fetchcolumn("SELECT COUNT(1) FROM " . tablename('ilotteryv1_user') . " $where");
            $pager = pagination($total, $pindex, $psize);
        }
        include $this->template('ilotteryv1/userlist');
    }

    public function doUserForm()
    {
        global $_GPC, $_W;
        checklogin();
        $weid = intval($_W['weid']);
        $rid = intval($_GPC['rid']);
        if ($rid == 0) {
            message('非法参数');
        }
        $id = intval($_GPC['id']);
        $action = 'userform';
        $title = $this->actions_titles[$action];

        $url = create_url('site/module', array('do' => 'userlist', 'name' => $this->modulename, 'weid' => $weid, 'rid' => $rid));

        $username = $_GPC['username'];
        $mobile = $_GPC['mobile'];

        $reply = pdo_fetch("SELECT * FROM " . tablename('ilotteryv1_user') . " WHERE id = :id ORDER BY `id` DESC limit 1", array(':id' => $id));
        if (checksubmit('submit')) {
            $data = array(
                'rid' => $rid,
                'weid' => $weid,
                'username' => $username,
                'mobile' => $mobile,
                'status' => intval($_GPC['status']),
                'level' => intval($_GPC['level']),
                'default_user' => intval($_GPC['default_user']),
                'default_level' => intval($_GPC['default_level']),
                'dateline' => TIMESTAMP
            );
            if (empty($reply)) {
                pdo_insert('ilotteryv1_user', $data);
            } else {
                unset($data['dateline']);
                pdo_update('ilotteryv1_user', $data, array('id' => $id));
            }
            message('操作成功！', $url);
        }
        include $this->template('ilotteryv1/user_form');
    }

    public function doTest()
    {
        echo base64_encode(authcode('ojTGBjqFVmNHYOYIECobiqzTksRQ', 'ENCODE'));
    }

    public function doUserDelete()
    {
        global $_GPC, $_W;
        $weid = intval($_W['weid']);
        $rid = intval($_GPC['rid']);
        $id = intval($_GPC['id']);
        $url = create_url('site/module', array('do' => 'userlist', 'name' => $this->modulename, 'weid' => $weid, 'rid' => $rid));
        pdo_delete('ilotteryv1_user', array('id' => $id, 'weid' => $weid));
        message('操作成功', $url, 'success');
    }

    public function doCleanState()
    {
        global $_GPC, $_W;
        $weid = intval($_W['weid']);
        $rid = intval($_GPC['rid']);
        $url = create_url('site/module', array('do' => 'userlist', 'name' => $this->modulename, 'weid' => $weid, 'rid' => $rid));
        $data = array('status' => 0);
        pdo_update('ilotteryv1_user', $data, array('weid' => $weid, 'rid' => $rid));
        message('操作成功', $url, 'success');
    }


    //消费日志excel
    public function doUserExcel()
    {
        global $_GPC, $_W;
        checklogin();
        $weid = intval($_W['weid']);
        $rid = intval($_GPC["rid"]);
        $where = "WHERE weid={$weid} and rid={$rid}";
        $list = pdo_fetchall("SELECT * FROM " . tablename('ilotteryv1_user') . " {$where} order by id desc");

        $filename = 'download_' . date('YmdHis') . '.csv';
        $exceler = new Jason_Excel_Export();
        $exceler->charset('UTF-8');
        // 生成excel格式 这里根据后缀名不同而生成不同的格式。jason_excel.csv
        $exceler->setFileName($filename);
        // 设置excel标题行
        $excel_title = array('编号', '用户名', '联系电话', '状态', '时间');
        $exceler->setTitle($excel_title);
        // 设置excel内容
        $excel_data = array();
        foreach ($list as $key => $value) {
            $status = $value['status'] == 0 ? '未中奖' : '已中奖';
            $excel_data[] = array($value["id"], $value["username"], $value["mobile"], $status, date("Y-m-d H:i:s", $value["dateline"]));
        }
        $exceler->setContent($excel_data);

        // 生成excel
        $exceler->export();
    }

    public function doReadExcel()
    {
        global $_W, $_GPC;
        $rid = intval($_GPC["rid"]);
        $weid = intval($_W['weid']);
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('utf-8');

        $filepath = './source/modules/ilotteryv1/data_' . $weid . '.xls';
        $tmp = $_FILES['fileexcel']['tmp_name'];
        $filetype = $_FILES['fileexcel']['type'];

        if (empty ($tmp)) {
            message('请选择要导入的Excel文件！');
        } else {
            $flag = $this->checkUploadFileMIME($_FILES['fileexcel']);
            if ($flag == 0) {
                message('文件格式不对.');
            }
        }
        if (copy($tmp, $filepath)) {
            if (!file_exists($filepath)) {
                echo '文件路径不存在.';
                return;
            }
            if (!is_readable($filepath)) {
                echo ("文件为只读,请修改文件相关权限.");
                return;
            }
            $data->read($filepath);
            error_reporting(E_ALL ^ E_NOTICE);
            $count = 0;
            for ($i = 2; $i <= $data->sheets[0]['numRows']; $i++) { //$=2 第二行开始
                //以下注释的for循环打印excel表数据
                for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
                    //echo "\"".$data->sheets[0]['cells'][$i][$j]."\",";
                }
                $arr = array(
                    'rid' => $rid,
                    'weid' => $weid,
                    'username' => $data->sheets[0]['cells'][$i][1],
                    'mobile' => $data->sheets[0]['cells'][$i][2],
                    'dateline' => TIMESTAMP
                );
                //echo $data->sheets[0]['cells'][$i][1].'：'.$data->sheets[0]['cells'][$i][2].'<br/>';
                $ishave = pdo_fetch("SELECT * FROM " . tablename('ilotteryv1_user') . " WHERE username=:username AND mobile=:mobile", array(':username' => $arr['username'], ':mobile' => $arr['mobile']));
                if (empty($ishave)) {
                    $row = pdo_insert('ilotteryv1_user', $arr);
                    if (!empty($row)) {
                        $count++;
                    }
                }
            }
            if (!empty($row)) {
                message("成功导入{$count}条数据!", create_url('site/module', array('do' => 'userlist', 'name' => 'ilotteryv1', 'rid' => $rid)), 'success');
            } else {
                message("导入失败!");
            }
        }
    }

    private function checkUploadFileMIME($file)
    {
        // 1.through the file extension judgement 03 or 07
        $flag = 0;
        $file_array = explode(".", $file ["name"]);
        $file_extension = strtolower(array_pop($file_array));

        // 2.through the binary content to detect the file
        switch ($file_extension) {
            case "xls" :
                // 2003 excel
                $fh = fopen($file ["tmp_name"], "rb");
                $bin = fread($fh, 8);
                fclose($fh);
                $strinfo = @unpack("C8chars", $bin);
                $typecode = "";
                foreach ($strinfo as $num) {
                    $typecode .= dechex($num);
                }
                if ($typecode == "d0cf11e0a1b11ae1") {
                    $flag = 1;
                }
                break;
            case "xlsx" :
                // 2007 excel
                $fh = fopen($file ["tmp_name"], "rb");
                $bin = fread($fh, 4);
                fclose($fh);
                $strinfo = @unpack("C4chars", $bin);
                $typecode = "";
                foreach ($strinfo as $num) {
                    $typecode .= dechex($num);
                }
                echo $typecode . 'test';
                if ($typecode == "504b34") {
                    $flag = 1;
                }
                break;
        }

        // 3.return the flag
        return $flag;
    }

    /*
    ** 设置切换导航
    */
    public function set_tabbar($action, $rid)
    {
        $actions_titles = $this->actions_titles;
        $html = '<ul class="nav nav-tabs">';
        foreach ($actions_titles as $key => $value) {
            $url = 'site.php?act=module&do=' . $key . '&name=' . $this->modulename . '&rid=' . $rid;
            $html .= '<li class="' . ($key == $action ? 'active' : '') . '"><a href="' . $url . '">' . $value . '</a></li>';
        }
        $html .= '</ul>';
        return $html;
        /*
         1.在类前加
         public $action = 'style';//默认方法
         public $modulename = 'icard';
         public $actions_titles = array(
          'style' => '会员卡设置',
          'business' => '商家设置',
          'score' => '积分策略',
          'index' => '会员资料管理',
         );
         2.在调用的方法加
         $action = 'business';//方法名
         $title = $this -> actions_titles[$action];
        */
    }

    //添加入口
    public function doSetRule()
    {
        global $_W, $_GPC;
        //$rule = pdo_fetch("SELECT id FROM ".tablename('rule')." WHERE module = 'ilotteryv1' AND weid = '{$_W['weid']}' order by id desc");
        $rid = intval($_GPC['rid']);
        if (empty($rid)) {
            header('Location: ' . $_W['siteroot'] . create_url('rule/post', array('module' => 'ilotteryv1')));
            exit;
        } else {
            //header('Location: '.$_W['siteroot'] . create_url('rule/display', array('module' => 'ilotteryv1')));
            header('Location: ' . $_W['siteroot'] . create_url('rule/post', array('module' => 'ilotteryv1', 'id' => $rid)));
            exit;
        }
        //0.5
        /*
        $rulename = "{$_W['account']['name']}公众号的会员卡";
        $rule = pdo_fetch("SELECT id FROM ".tablename('rule')." WHERE name = '$rulename' AND weid = '{$_W['weid']}'");
        if (empty($rule)) {
            header('Location: '.$_W['siteroot'] . create_url('rule/post', array('module' => 'icard', 'name' => $rulename)));
            exit;
        } else {
            header('Location: '.$_W['siteroot'] . create_url('rule/post', array('module' => 'icard', 'id' => $rule['id'])));
            exit;
        }*/
    }
}

/**
 * 导出Excel
 *
 * @package:     Jason
 * @subpackage:  Excel
 * @version:     1.0
 */
class Jason_Excel_Export
{
    /**
     * Excel 标题
     *
     * @type: Array
     */
    private $_titles = array();

    /**
     * Excel 标题数目
     *
     * @type: int
     */
    private $_titles_count = 0;

    /**
     * Excel 内容
     *
     * @type:  Array
     */
    private $_contents = array();

    /**
     * Excel 内容数据
     *
     * @type:  Array
     */
    private $_contents_count = 0;

    /**
     * Excel 文件名
     *
     * @type: string
     */
    private $_fileName = '';
    private $_split = "\t";

    private $_charset = '';

    /**
     * 默认文件名
     *
     * @const :
     */
    const DEFAULT_FILE_NAME = 'jason_excel.xls';


    /**
     * 构造函数..
     *
     * @param    string  param
     * @return   mixed   return
     */
    function __construct($fileName = null)
    {
        if ($fileName !== null) {
            $this->_fileName = $fileName;
        } else {
            $this->setFileName();
        }
    }

    /**
     * 设置生成文件名
     *
     * @param    string  param
     * @return   mixed   Jason_Excel_Export
     */
    public function setFileName($fileName = self::DEFAULT_FILE_NAME)
    {
        $this->_fileName = $fileName;
        $this->setSplite();
        return $this;
    }

    private function _getType()
    {
        return substr($this->_fileName, strrpos($this->_fileName, '.') + 1);
    }

    public function setSplite($split = null)
    {
        if ($split === null) {
            switch ($this->_getType()) {
                case 'xls':
                    $this->_split = "\t";
                    break;
                case 'csv':
                    $this->_split = ",";
                    break;
            }
        } else
            $this->_split = $split;
    }

    /**
     * 设置Excel标题
     *
     * @param    string  param
     * @return   mixed   Jason_Excel_Export
     */
    public function setTitle(&$title = array())
    {
        $this->_titles = $title;
        $this->_titles_count = count($title);
        return $this;
    }

    /**
     * 设置Excel内容
     *
     * @param    string  param
     * @return   mixed   Jason_Excel_Export
     */
    public function setContent(&$content = array())
    {
        $this->_contents = $content;
        $this->_contents_count = count($content);
        return $this;
    }

    /**
     * 向excel中添加一行内容
     */
    public function addRow($row = array())
    {
        $this->_contents[] = $row;
        $this->_contents_count++;
        return $this;
    }

    /**
     * 向excel中添加多行内容
     */
    public function addRows($rows = array())
    {
        $this->_contents = array_merge($this->_contents, $rows);
        $this->_contents_count += count($rows);
        return $this;
    }

    /**
     * 数据编码转换
     */
    public function toCode($type = 'GB2312', $from = 'auto')
    {
        foreach ($this->_titles as $k => $title) {
            $this->_titles[$k] = mb_convert_encoding($title, $type, $from);
        }

        foreach ($this->_contents as $i => $contents) {
            $this->_contents[$i] = $this->_toCodeArr($contents);
        }

        return $this;
    }

    private function _toCodeArr(&$arr = array(), $type = 'GB2312', $from = 'auto')
    {
        foreach ($arr as $k => $val) {
            $arr[$k] = mb_convert_encoding($val, $type, $from);
        }

        return $arr;
    }

    public function charset($charset = '')
    {
        if ($charset == '')
            $this->_charset = '';
        else {
            $charset = strtoupper($charset);
            switch ($charset) {
                case 'UTF-8' :
                case 'UTF8' :
                    $this->_charset = ';charset=UTF-8';
                    break;

                default:
                    $this->_charset = ';charset=' . $charset;
            }
        }

        return $this;
    }


    /**
     * 导出Excel
     *
     * @param    string  param
     * @return   mixed   return
     */
    public function export()
    {
        $header = '';
        $data = array();

        $header = implode($this->_split, $this->_titles);

        for ($i = 0; $i < $this->_contents_count; $i++) {
            $line_arr = array();
            foreach ($this->_contents[$i] as $value) {
                if (!isset($value) || $value == "") {
                    $value = '""';
                } else {
                    $value = str_replace('"', '""', $value);
                    $value = '"' . $value . '"';
                }

                $line_arr[] = $value;
            }

            $data[] = implode($this->_split, $line_arr);
        }

        $data = implode("\n", $data);
        $data = str_replace("\r", "", $data);

        if ($data == "") {
            $data = "\n(0) Records Found!\n";
        }

        header("Content-type: application/vnd.ms-excel" . $this->_charset);
        header("Content-Disposition: attachment; filename=$this->_fileName");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "\xEF\xBB\xBF" . $header . "\n" . $data;
    }
}