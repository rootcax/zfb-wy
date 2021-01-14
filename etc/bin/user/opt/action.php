<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//action方法
//该类不支持界面和ajax请求，该界面是用户操作
class action {

    //充值回调接口
    function recharge() {
        $mysql = functions::open_mysql();
        $amount = Number_format(functions::request('amount'), 2, '.', '');
        $record = trim(functions::request('record'));
        $num = trim(functions::request('order'));
        $attach = trim(functions::request('attach'));
        $order = $mysql->query("orders", "num='{$num}'");
        $sign = functions::request('sign');
        if (!is_array($order[0]))
            exit("订单不存在");
        $user = $mysql->query("users", "id={$order[0]['userid']}");
        if (!is_array($user[0]))
            exit("收款用户不存在");
        $user = $user[0];
        if ($user['status'] == 0) {
            exit("用户未通过审核");
        }
        if ($user['status'] == 2) {
            exit("用户已被冻结");
        }
        $recharge_user = $mysql->query("users", "phone='{$attach}'");
        if (!is_array($recharge_user[0]))
            exit("预充值用户不存在");
        $recharge_user = $recharge_user[0];
        $resign = md5($amount . $record . $user['keyid']);
        if ($resign == $sign) {
            //判断订单是否已经增加到用户余额
            $recharge_record = $mysql->query("recharge_record", "num='{$num}'");
            if (!is_array($recharge_record[0])) {
                $temp_money = $recharge_user['balance'] + $amount;
                $in = $mysql->insert("recharge_record", array(
                    'userid' => $recharge_user['id'],
                    'num' => $num,
                    'old_money' => $recharge_user['balance'],
                    'money' => $amount,
                    'new_money' => $temp_money,
                    'pay_time' => $order[0]['order_time'],
                    'remark' => $order[0]['remark']
                ));
                if ($in > 0) {
                    $mysql->update("users", array("balance" => $temp_money), "id={$recharge_user['id']}");
                    echo 'ok';
                } else {
                    echo '充值失败!';
                }
            } else {
                echo 'ok';
            }
        }


        //}else{
        //    exit('密钥错误');
        // }
    }

    //上传二维码
    function qrcode_add() {
        //设置脚本处理时间，不限制时间
        ini_set('max_execution_time', '0');
        $user = $this->AI();
        functions::drive('qrcodec')->add($user);
    }

    //更改二维码
    function qrcode_edit() {
        //设置脚本处理时间，不限制时间
        ini_set('max_execution_time', '0');
        $user = $this->AI();
        functions::drive('qrcodec')->edit($user);
    }

    function qrcode_batch() {
        $user = $this->AI();
        functions::drive('qrcodec')->batch($user);
    }

    //删除二维码
    function qrcode_del() {
        $user = $this->AI();
        functions::drive('qrcodec')->del($user);
    }

    //删除二维码链接
    function qrcode_link_del() {
        $user = $this->AI();
        functions::drive('qrcodec')->del_link($user);
    }

    //删除多选二维码链接
    function qrcode_link_ch_del() {
        $user = $this->AI();
        functions::drive('qrcodec')->del_ch_link($user);
    }

    //批量删除支付订单
    function takes_del() {
        $user = $this->AI();
        functions::drive('takec')->del($user);
    }

    //批量删除收款订单
    function order_del() {
        $user = $this->AI();
        functions::drive('orderc')->del($user);
    }

    //添加用户
    function customer_add() {
        $user = $this->AI();
        if ($user->parentid != 0)
            exit('用户无权限');
        $mysql = functions::open_mysql();
        $phone = functions::request('phone');
        //$code = intval(functions::request('code'));
        if (!functions::isphone($phone))
            functions::json(2002, '手机号码不正确');

        $pwd = functions::request('pwd');
        if (!functions::ispwd($pwd))
            functions::json(2005, '密码输入错误,请输入6-26位的字符密码');
        //再次检测一次手机是否注册过了
        $queryx = $mysql->query('users', "phone={$phone}");
        if (is_array($queryx[0]))
            functions::json(2007, '该手机已被注册');
        //如果没被注册那么接着注册
        //生成token
        $token = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 12);
        //生成key
        $keyid = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 18);
        //生成密码
        $pwd = md5($pwd . $token);
        $in = $mysql->insert('users', array(
            "phone" => $phone,
            "pwd" => $pwd,
            "token" => $token,
            "ip" => functions::get_client_ip(),
            "regc" => time(),
            'loginc' => 0,
            'balance' => 0,
            'avatar' => '',
            'keyid' => $keyid,
            'status' => 0,
            'parentid' => $user->sid,
            'agentid' => 0,
            'bank2alipay_withdraw' => 0
        ));
        if ($in) {
            functions::json(200, '添加用户成功');
        } else {
            functions::json(2008, '添加用户失败,请重试');
        }
    }

    //修改用户
    function customer_edit() {
        $user = $this->AI();
        if ($user->parentid != 0)
            exit('用户无权限');
        $id = intval(functions::request('id'));
        $pwd = functions::request('pwd');
        $mysql = functions::open_mysql();
        //先查询该用户
        $query = $mysql->query("users", "id={$id}");
        if (!is_array($query[0]))
            functions::json(-1, '修改失败,用户不存在');
        $array = array('parentid' => $user->sid);
        if (!empty($pwd)) {
            //修改密码和金额
            $pwd = md5($pwd . $query[0]['token']);
            $array['pwd'] = $pwd;
        }
        $mysql->update("users", $array, "id={$id}");
        functions::json(200, '用户更新成功');
    }

    //删除用户
    function customer_del() {
        $user = $this->AI();
        if ($user->parentid != 0)
            exit('用户无权限');
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $mysql->delete("users", "id={$id}"); //删除用户组
        functions::urlx(functions::get_Config('webCog')['site'] . 'index.php?b=index&c=customer');
    }

    function applyWithdraw() {
        $user = $this->AI();
        if ($user->parentid == 0) {
            functions::json(-1, "当前商户不允许提现");
        }
        functions::drive('users')->applyWithdraw($user);
    }

    //修改密码
    function edit() {
        $user = $this->AI();
        functions::drive('users')->edit($user);
    }

    //头像
    function avatar() {
        $user = $this->AI();
        functions::drive('users')->avatar($user);
    }

    //注销登录
    function out() {
        unset($_SESSION['user']);
        functions::urlx(functions::urlc('user', 'index', 'login'));
    }

    //AI登录验证
    private function AI() {
        return functions::api('loginc')->AI('user', functions::urlc('user', 'index', 'login'));
    }

}
