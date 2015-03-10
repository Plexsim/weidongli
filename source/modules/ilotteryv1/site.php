<?php
/**
 * 幸运大抽奖
 * 作者:迷失卍国度
 * qq : 15595755
 * [WeEngine System] Copyright (c) 2013 WE7.CC
 */
defined('IN_IA') or exit('Access Denied');

class ilotteryv1ModuleSite extends WeModuleSite
{
    public $tablename = 'ilotteryv1_reply';

    public function doMobileIndex()
    {
        global $_W, $_GPC;
        $rid = intval($_GPC['rid']);
        $weid = intval($_GPC['weid']);

        $reply = pdo_fetch("SELECT * FROM ".tablename('ilotteryv1_reply')." WHERE rid=:rid", array(':rid' => $rid));
        $userlist1 = pdo_fetchall('select * from ' . tablename('ilotteryv1_user') . ' where status=1 and level=1 and rid=:rid order by id desc', array(':rid' => $rid));
        $userlist2 = pdo_fetchall('select * from ' . tablename('ilotteryv1_user') . ' where status=1 and level=2 and rid=:rid order by id desc', array(':rid' => $rid));
        $userlist3 = pdo_fetchall('select * from ' . tablename('ilotteryv1_user') . ' where status=1 and level=3 and rid=:rid order by id desc', array(':rid' => $rid));
        $usercount = pdo_fetchcolumn('select count(1) from ' . tablename('ilotteryv1_user') . ' where rid=:rid', array(':rid' => $rid));
        $lotterycount = pdo_fetchcolumn('select count(1) from ' . tablename('ilotteryv1_user') . ' where status=1 and rid=:rid', array(':rid' => $rid));
        include $this->template('index');
    }

    //用户信息
    public function doMobileUserinfo()
    {
        global $_GPC;
        $weid = intval($_GET['weid']);
        $result['state'] = 0;
        $rid = intval($_GPC['rid']);
        $page_from_user = $_GPC['from_user'];
        $from_user = authcode(base64_decode($_GPC['from_user']), 'DECODE');

        $reply = pdo_fetch("SELECT * FROM " . tablename($this->tablename) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));

        $user = pdo_fetch("SELECT * FROM " . tablename('ilotteryv1_user') . " WHERE weid = :weid and from_user=:from_user and rid=:rid ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user, ':rid' => $rid));
        include $this->template('wap_userinfo');
    }

    //抽奖
    public function doMobileLottery()
    {
        global $_W, $_GPC;
        $action = $_GPC['action'];
        $rid = intval($_GPC['rid']);

        if ($action == "") { //读取数据，返回json

            $users = pdo_fetchall('select * from ' . tablename('ilotteryv1_user') . ' where status=0 and rid=:rid', array(':rid' => $rid));
            foreach ($users as $key => $value) {
                $arr[] = array(
                    'id' => $value['id'],
                    'username' => $value['username'],
                    'mobile' => substr($value['mobile'], 0, 3) . "****" . substr($value['mobile'], -4, 4)
                );
            }
            echo json_encode($arr);
        } else { //标识中奖号码
            $default_level = intval($_GPC['award']);
            $user = pdo_fetch("SELECT * FROM ".tablename('ilotteryv1_user')." WHERE status=0 AND default_user=1 AND default_level=:default_level", array(':default_level' => $default_level));
            if (!empty($user)) { //是内定人员的时候
                pdo_query("update ".tablename('ilotteryv1_user')." set status=1,level=" . $default_level . " where id=:id", array(':id' => $user['id']));
                $return_data = array(
                    'success' => 1,
                    'username' => $user['username'],
                    'mobile' => substr($user['mobile'], 0, 3) . "****" . substr($user['mobile'], -4, 4)
                );
            } else {
                $reply = pdo_fetch("SELECT * FROM ".tablename('ilotteryv1_reply')." WHERE rid=:rid", array(':rid' => $rid));
                $return_data = array(
                    'success' => 0,
                );

                if ($reply['only_default_user'] != 1) {
                    $userid = $this->getLotteryUser($rid);
                    if ($userid != 0) {
                        $user = pdo_fetch("SELECT * FROM ".tablename('ilotteryv1_user')." WHERE id=:id AND default_user=0", array(':id' => $userid));
                        if (!empty($user)) {
                            pdo_query("update ".tablename('ilotteryv1_user')." set status=1,level=" . $default_level . " where id=:id", array(':id' => $userid));
                            $return_data = array(
                                'success' => 1,
                                'username' => $user['username'],
                                'mobile' => substr($user['mobile'],0,3)."****".substr($user['mobile'],-4,4)
                            );
                        }
                    }
                }
            }
            echo json_encode($return_data);
        }
    }

    //抽取人员
    public function getLotteryUser($rid = 0)
    {
        if ($rid == 0) {
            return 0;
        }

        $users = pdo_fetchall("SELECT id FROM " . tablename('ilotteryv1_user') . " WHERE rid = '$rid' and status=0 ");
        $arr_index = array_rand($users, 1);
        return $users[$arr_index]['id'];
    }

    public function doMobileGetUserCount()
    {
        global $_GPC;
        $rid = intval($_GPC['rid']);
        $usercount = pdo_fetchcolumn('select count(1) from ' . tablename('ilotteryv1_user') . ' where rid=:rid', array(':rid' => $rid));
        $lotterycount = pdo_fetchcolumn('select count(1) from ' . tablename('ilotteryv1_user') . ' where status=1 and rid=:rid', array(':rid' => $rid));
        $arr = array('usercount' => $usercount, 'lotterycount' => $lotterycount);
        echo json_encode($arr);
    }

    //保存用户数据
    public function doMobileSaveUserinfo()
    {
        global $_GPC;
        $weid = intval($_GET['weid']);
        $rid = intval($_GPC['rid']);
        $result['state'] = 0;
        $page_from_user = $_GPC['from_user'];
        $from_user = authcode(base64_decode($_GPC['from_user']), 'DECODE');
        if (empty($rid)) {
            $result['msg'] = '非法参数!';
            message($result, '', 'ajax');
        }
        if (empty($from_user)) {
            $result['msg'] = '非法参数!';
            message($result, '', 'ajax');
        }
        $username = trim($_GPC['username']);
        $mobile = trim($_GPC['mobile']);
        if ($rid == 0) { //rid=141
            $result['msg'] = '非法参数!';
            message($result, '', 'ajax');
        }

        $data = array(
            'weid' => $weid,
            'rid' => $rid,
            'from_user' => $from_user,
            'username' => $username,
            'mobile' => $mobile,
            'dateline' => TIMESTAMP
        );
        if (strlen($data['username']) == 0) {
            $result['msg'] = '用户名不能为空!';
            message($result, '', 'ajax');
        }
        if (strlen($data['mobile']) == 0 || strlen($data['mobile']) != 11) {
            $result['msg'] = '手机号码错误请重新填写!';
            message($result, '', 'ajax');
        }


        $user = pdo_fetch("SELECT * FROM " . tablename('ilotteryv1_user') . " WHERE weid = :weid and from_user=:from_user and rid=:rid ORDER BY `id` DESC limit 1", array(':weid' => $weid, ':from_user' => $from_user, ':rid' => $rid));

        if (empty($user)) {
            pdo_insert('ilotteryv1_user', $data);
            $result['msg'] = '幸运大抽奖报名成功.';
        } else {
            $reply = pdo_fetch("SELECT * FROM " . tablename($this->tablename) . " WHERE rid = :rid ORDER BY `id` DESC", array(':rid' => $rid));
            if ($reply['is_change'] == 0) {
                $result['msg'] = '本次活动报名后不允许修改资料.';
            } else {
                pdo_update('ilotteryv1_user', $data, array('from_user' => $from_user, 'weid' => $weid, 'rid' => $rid));
                $result['msg'] = '保存成功.';
            }
        }
        $result['state'] = 1;
        message($result, '', 'ajax');
    }
    //327 626 361
}