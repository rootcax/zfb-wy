<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00

class landc {

    //添加账号
    function add($user) {
        $username = functions::request('username');
        $mysql = functions::open_mysql();
        $sms_config = functions::get_Config("smsCog");
        if ($sms_config['landadd_sms']) {
            $code = intval(functions::request('code'));
            $code_query = $mysql->query('codes', "code={$code} and phone={$user->phone} and typec=3");
            if (!is_array($code_query[0]))
                functions::json('-1', '验证码错误');
            if (time() - $code_query[0]['ctime'] > $code_query[0]['survival'])
                functions::json('-2', '验证码已过期');
        }

        $typec = intval(functions::request('typec'));
        if (!functions::islang_str($username, 4, 64))
            functions::json(6001, '收款账号有误');
        $pattern = intval(functions::request('pattern'));
        $polling = intval(functions::request('polling'));
        //二维码类型
        $qr_typec = intval(functions::request('qr_typec'));
        $appid = intval(functions::request('appid'));
        $rsaPrivateKey = functions::request('rsaPrivateKey');
        $alipayPublicKey = functions::request('alipayPublicKey');
        $ToaliUserId = functions::request('ToaliUserId');
        $ToaliUserAccount = functions::request('ToaliUserAccount');
        $aliname = functions::request("aliname");
        $cardno = functions::request("cardno");
        $bankno = functions::request("bankno");
        $cardid = functions::request("cardid");
        $transfer = intval(functions::request("transfer"));
        $limit_time = intval(functions::request("limit_time"));
        //2019-03-14新增最大金额、最小金额
        $max_amount = floatval(functions::request("max_amount"));
        $min_amount = floatval(functions::request("min_amount"));
        $otorder_limit = intval(functions::request("otorder_limit"));
        if (!empty($min_amount) && !empty($max_amount)) {
            if ($max_amount < $min_amount) {
                functions::msg('单笔最大金额不能小于最小金额，请修改后重新提交', functions::urlc("visa_admin", "index", "land_add"));
            }
            if ($max_amount < 0) {
                $max_amount = 0;
            }
            if ($min_amount < 0) {
                $min_amount = 0;
            }
        }
        //店长姓名
        $supervisor = functions::request("supervisor");
        //开始添加账号
        //提交类型
        $method = functions::request('send_type');
        //检测类型
        $method_array = array('get', 'post');
        if (!in_array($method, $method_array))
            functions::json(6005, '回掉类型有误');
        //为了安全起见，将接口以及所有数据加密，防止泄露
        $json = functions::encode(json_encode(array(
                    'method' => $method,
                    'data' => 'money=[money]&amount=[amount]&order=[order]&record=[record]&remark=[remark]&attach=[attach]&sign=[sign]',
                    'header' => '',
                    'cookie' => ''
                        )), AUTH_PE);
        $query = $mysql->query('land', "username='{$username}'");
        if (is_array($query[0]))
            functions::json(6003, '该收款账号已存在,请勿重复添加!');
        //生成sdk
        $sdk = substr(md5(mt_rand(100000, 999999) . time() . functions::encode(mt_rand(10000, 99999), AUTH_PE)), 0, 26);
        $status = 1;
        $requota = floatval(functions::request('requota'));
        if ($requota < -1)
            functions::json(6004, '该收款账号额度不允许为负数');
        $insert = $mysql->insert('land', array(
            'userid' => $user->sid,
            'username' => $username,
            'typec' => $typec,
            'login' => 0,
            'image' => 0,
            'reback' => $json,
            'onback' => 2,
            'ban' => 1,
            'sdk' => $sdk,
            'limit_time' => $limit_time,
            'min_amount' => $min_amount, //2019-03-14新增最大金额、最小金额
            'max_amount' => $max_amount, //2019-03-14新增最大金额、最小金额
            'otorder_limit' => $otorder_limit,
            'qr_typec' => $qr_typec,
            'supervisor' => $supervisor,
            'appid' => $appid,
            'rsaPrivateKey' => $rsaPrivateKey,
            'alipayPublicKey' => $alipayPublicKey,
            'ToaliUserId' => $ToaliUserId,
            'ToaliUserAccount' => $ToaliUserAccount,
            'aliname' => $aliname,
            'cardno' => $cardno,
            'bankno' => $bankno,
            'cardid' => $cardid,
            'transfer' => $transfer,
            'timec' => 0,
            'pattern' => $pattern,
            'polling' => $polling,
            'requota' => $requota,
            'quota' => 0,
            'status' => $status
        ));
        if ($insert > 0) {
            functions::drive('users')->cdie($mysql, $user->phone, 3);
            functions::json(200, '添加成功');
        } else {
            functions::json(6004, '系统错误!请联系管理员!');
        }
    }

    //修改账号
    function edit($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $sms_config = functions::get_Config("smsCog");
        if ($sms_config['landedit_sms']) {
            $code = intval(functions::request('code'));
            $code_query = $mysql->query('codes', "code={$code} and phone={$user->phone} and typec=3");
            if (!is_array($code_query[0]))
                functions::json(6004, '验证码错误');
            if (time() - $code_query[0]['ctime'] > $code_query[0]['survival'])
                functions::json(6006, '验证码已过期');
        }
        $username = functions::request('username');
        if (!functions::islang_str($username, 4, 64))
            functions::json(6001, '收款账号有误');
        //提交类型
        $method = functions::request('send_type');
        $pattern = intval(functions::request('pattern'));
        $polling = intval(functions::request('polling'));
        //二维码类型
        $qr_typec = intval(functions::request('qr_typec'));
        //店长姓名
        $supervisor = functions::request("supervisor");
        $appid = intval(functions::request('appid'));
        $rsaPrivateKey = functions::request('rsaPrivateKey');
        $alipayPublicKey = functions::request('alipayPublicKey');
        $ToaliUserId = functions::request('ToaliUserId');
        $ToaliUserAccount = functions::request('ToaliUserAccount');
        $aliname = functions::request("aliname");
        $cardno = functions::request("cardno");
        $bankno = functions::request("bankno");
        $cardid = functions::request("cardid");
        $transfer = intval(functions::request("transfer"));
        $limit_time = intval(functions::request("limit_time"));
        //2019-3-03-14新增收款账户
        $max_amount = floatval(functions::request("max_amount"));
        $min_amount = floatval(functions::request("min_amount"));
        $otorder_limit = intval(functions::request("otorder_limit"));
        //检测类型
        $method_array = array('get', 'post');
        if (!in_array($method, $method_array))
            functions::json(6005, '回掉类型有误');
        //为了安全起见，将接口以及所有数据加密，防止泄露
        $json = functions::encode(json_encode(array(
                    'method' => $method,
                    'data' => 'money=[money]&amount=[amount]&order=[order]&record=[record]&remark=[remark]&attach=[attach]&sign=[sign]',
                    'header' => '',
                    'cookie' => ''
                        )), AUTH_PE);

        $query = $mysql->query('land', "id={$id} and userid={$user->sid}");

        if (!is_array($query[0]))
            functions::json(6003, '修改失败');
        $array = array('username' => $username, 'reback' => $json, 'pattern' => $pattern, 'polling' => $polling, 'qr_typec' => $qr_typec, 'otorder_limit' => $otorder_limit);
        if ($qr_typec == 3 || $qr_typec == 4) {
            $array['appid'] = $appid;
            $array['rsaPrivateKey'] = $rsaPrivateKey;
            $array['alipayPublicKey'] = $alipayPublicKey;
        }
        if ($qr_typec == 4) {
            $array['ToaliUserId'] = $ToaliUserId;
            $array['ToaliUserAccount'] = $ToaliUserAccount;
        }

        $array['aliname'] = $aliname;
        $array['cardno'] = $cardno;
        $array['bankno'] = $bankno;
        if ($qr_typec == 1) {
            $array['cardid'] = $cardid;
            $array['transfer'] = $transfer;
        }


        if ($limit_time >= 0) {
            $array['limit_time'] = $limit_time;
        }
        if (!empty($min_amount) && !empty($max_amount)) {
            if ($max_amount < $min_amount) {
                functions::msg('单笔最大金额不能小于最小金额，请修改后重新提交', functions::urlc("visa_admin", "index", "land_edit") . "&id=" . $id);
            }
            if ($max_amount < 0) {
                $max_amount = 0;
            }
            if ($min_amount < 0) {
                $min_amount = 0;
            }
        }
        $array['min_amount'] = $min_amount;
        $array['max_amount'] = $max_amount;
        if ($supervisor != "" || $supervisor != null) {
            $array['supervisor'] = $supervisor;
        }
        $requota = floatval(functions::request('requota'));
        if (empty($requota))
            functions::json(-1, "额度不允许调整为0");
        if ($requota < -1)
            functions::json(6004, '额度不能为负数');
        if ($requota == -1) {
            //if ($query[0]['status'] != 1)
            //    $array['status'] = 1;
            $array['requota'] = $requota;
        } else {
            if (!empty($requota)) {
                //变更金额对比
                $tempquota = floatval($requota) - floatval($query[0]['requota']);
                if ($tempquota < 0) {
                    //每日额度被减少
                    if (floatval($land[0]['quota']) >= $requota)
                        functions::json(-1, "当前已使用额度已超出欲调整的额度");
                    $array['requota'] = $requota;
                } else if ($tempquota = 0) {
                    //金额未作改变
                    //$quota = floatval($query[0]['quota']);
                    //$status = intval($query[0]['status']);
                } else {
                    //增加额度以后将自动开启
                    if ($query[0]['status'] != 1)
                        $array['status'] = 1;
                    $array['requota'] = $requota;
                }
            }
        }

        $update = $mysql->update('land', $array, "id={$id} and userid={$user->sid}");

        functions::drive('users')->cdie($mysql, $user->phone, 3);
        functions::json(200, '修改成功');
    }

    //删除账号
    function del($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $sms_config = functions::get_Config("smsCog");
        if ($sms_config['landdel_sms']) {
            $code = intval(functions::request('code'));
            $code_query = $mysql->query('codes', "code={$code} and phone={$user->phone} and typec=3");
            if (!is_array($code_query[0]))
                functions::json('-1', '验证码错误');
            if (time() - $code_query[0]['ctime'] > $code_query[0]['survival'])
                functions::json('-2', '验证码已过期');
        }
        //删除账号库
        $mysql->delete('land', "id={$id} and userid={$user->sid}");
        //删除生成的二维码库
        $mysql->delete('qrcode_link', "land_id={$id} and userid={$user->sid}");
        //删除通用二维码库
        $mysql->delete('qrcode', "land_id={$id} and userid={$user->sid}");
        //删除订单（该功能取消）
        //$mysql->delete('takes', "land_id={$id} and userid={$user->sid}");
        //删除账单（该功能取消）
        //$mysql->delete('orders', "land_id={$id} and userid={$user->sid}");
        if ($sms_config['landedit_sms']) {
            functions::drive('users')->cdie($mysql, $user->phone, 3);
            functions::json('200', '删除成功');
        } else {
            functions::urlx(functions::urlc('user', 'index', 'land'));
        }
    }

    //查询登录状态
    function query_login($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $query = $mysql->query('land', "id={$id} and userid={$user->sid}", "id,userid,username,typec,login,image,ban");
        if (is_array($query[0])) {
            if ($query[0]['ban'] == 1) {
                functions::json(200, '查询成功', $query[0]);
            } else {
                functions::json(6003, '该收款账号已被禁止登录,如有疑问,请联系客服');
            }
        } else {
            functions::json(6004, '查询失败');
        }
    }

    //登录
    function login($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $query = $mysql->query('land', "id={$id} and userid={$user->sid}", "id,userid,username,typec,login,image,ban");
        if (is_array($query[0])) {
            if ($query[0]['ban'] != 1)
                functions::json(6002, '该收款账号已被禁止登录,如有疑问,请联系客服');
            $update = $mysql->update('land', array('login' => 1, 'image' => 0, 'timec' => time()), "id={$id} and userid={$user->sid}");
            if ($update > 0) {
                functions::json(200, '正在登录,请稍后');
            } else {
                functions::json(6003, '请求登录失败');
            }
        } else {
            functions::json(6004, '请求登录失败');
        }
    }

    //开启监听
    function listen($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $query = $mysql->query('land', "id={$id} and userid={$user->sid}", "id,userid,username,typec,login,image,ban");
        if (is_array($query[0])) {
            if ($query[0]['ban'] != 1)
                functions::json(6002, '该收款账号已被禁止操作,如有疑问,请联系客服');
            if ($query[0]['login'] != 3)
                functions::json(6003, '该收款账号还未登录,请登录后在开启监控');
            if ($user->balance <= 0)
                functions::json(6004, '您的账号余额为0.00元,请充值后在进行监控');
            $update = $mysql->update('land', array('onback' => 1), "id={$id} and userid={$user->sid}");
            if ($update > 0) {
                functions::json(200, '启动成功');
            } else {
                functions::json(6005, '启动失败');
            }
        } else {
            functions::json(6006, '启动有误');
        }
    }

    //停止监控
    function stop($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $query = $mysql->query('land', "id={$id} and userid={$user->sid}", "id,userid,username,typec,login,image,ban,onback");
        if (is_array($query[0])) {
            if ($query[0]['ban'] != 1)
                functions::json(6002, '该收款账号已被禁止操作,如有疑问,请联系客服');
            if ($query[0]['onback'] != 1)
                functions::json(6003, '该账号未启动监控,无需关闭');
            $update = $mysql->update('land', array('onback' => 2), "id={$id} and userid={$user->sid}");
            if ($update > 0) {
                functions::json(200, '关闭成功');
            } else {
                functions::json(6004, '关闭失败');
            }
        } else {
            functions::json(6005, '关闭有误');
        }
    }

    function updateStatus($user) {
        $id = intval(functions::request('id'));
        $status = intval(functions::request('status'));
        $mysql = functions::open_mysql();
        $query = $mysql->update('land', array('status' => $status, 'isClosed' => 0, 'overtimes' => 0), "id={$id}");
        if ($query > 0) {
            if ($status) {
                functions::json("200", '开启成功');
            } else {
                functions::json("200", '关闭成功');
            }
        } else {
            functions::json(-1, '系统错误，请联系管理员');
        }
    }

}
