<?php

class action {

    //删除收款订单
    function order_del() {
        $this->AI();
        $id = intval(functions::request('id'));
        functions::open_mysql()->delete("orders", "id={$id}");
        functions::urlx(functions::get_Config('webCog')['site'] . 'agent.php?b=index&c=home');
    }

    //删除支付订单
    function takes_del() {
        $this->AI();
        $id = intval(functions::request('id'));
        if ($id == 1) {
            functions::json(-1, '当前用户不允许删除');
        }
        functions::open_mysql()->delete("takes", "id={$id}");
        functions::urlx(functions::get_Config('webCog')['site'] . 'agent.php?b=index&c=takes');
    }

    //删除用户
    function user_del() {
        $this->AI();
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $mysql->delete("users", "id={$id}"); //删除用户组
        //$mysql->delete('takes', "userid={$id}");
        //$mysql->delete('orders', "userid={$id}");
        $mysql->delete('land', "userid={$id}");
        functions::urlx(functions::get_Config('webCog')['site'] . 'agent.php?b=index&c=user');
    }

    //修改用户
    function user_edit() {
        $user = $this->AI();
        $id = intval(functions::request('id'));
        $pwd = functions::request('pwd');
        $status = functions::request('status');
        $bank2alipay_withdraw = floatval(functions::request('bank2alipay_withdraw'));
        if ($bank2alipay_withdraw < $user->bank2alipay_withdraw) {
            functions::json(2008, '商户费率不允许超过自身费率');
        }
        $mysql = functions::open_mysql();
        //先查询该用户
        $query = $mysql->query("users", "id={$id} and agentid={$user->sid}");
        if (!is_array($query[0]))
            functions::json(-1, '修改失败,用户错误');
        if (empty($bank2alipay_withdraw)) {
            $bank2alipay_withdraw = $query[0]['bank2alipay_withdraw'];
        }
        $array = array('status' => $query[0]['status'], 'bank2alipay_withdraw' => $bank2alipay_withdraw);
        if (!empty($pwd)) {
            //修改密码和金额
            $pwd = md5($pwd . $query[0]['token']);
            $array['pwd'] = $pwd;
        }
        $mysql->update("users", $array, "id={$id}");
        functions::json(200, '用户更新成功');
    }

    //添加用户
    function user_add() {
        $user = $this->AI();
        $phone = functions::request('phone');
        if (!functions::isphone($phone))
            functions::json(2002, '手机不正确');
        $mysql = functions::open_mysql();
        //$sms_config = functions::get_Config('smsCog');
        //if ($sms_config['withdraw_sms']) {
        //$code = intval(functions::request('code'));
        //$code_query = $mysql->query('codes', "code={$code} and phone={$phone} and typec=1");
        //if (!is_array($code_query[0]))
        //    functions::json(2003, '验证码错误');
        //if (time() - $code_query[0]['ctime'] > $code_query[0]['survival'])
        //    functions::json(2004, '验证码已过期');
        //}
        $pwd = functions::request('pwd');
        if (!functions::ispwd($pwd))
            functions::json(2005, '密码输入错误,请输入6-26位的字符密码');
        //再次检测一次手机是否注册过了
        $queryx = $mysql->query('users', "phone={$phone}");
        if (is_array($queryx[0]))
            functions::json(2007, '该手机已被注册');
        //如果没被注册那么接着注册
        //生成token
        $bank2alipay_withdraw = floatval(functions::request('bank2alipay_withdraw'));
        $token = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 12);
        //生成key
        $keyid = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 18);
        //生成密码
        $pwd = md5($pwd . $token);
        $config = functions::get_Config('registerCog');
        if (empty($bank2alipay_withdraw)) {
            $bank2alipay_withdraw = $config['bank2alipay_withdraw'];
        }
        if ($bank2alipay_withdraw < $user->bank2alipay_withdraw) {
            functions::json(2008, '商户费率不允许超过自身费率');
        }
        $in = $mysql->insert('users', array(
            "phone" => $phone,
            "pwd" => $pwd,
            "token" => $token,
            "ip" => functions::get_client_ip(),
            "regc" => time(),
            'loginc' => 0,
            'balance' => $config['balance'],
            'avatar' => '',
            'keyid' => $keyid,
            'status' => 0,
            'agentid' => $user->sid,
            'bank2alipay_withdraw' => $bank2alipay_withdraw
        ));
        if ($in) {
            functions::json(200, '添加用户成功');
        } else {
            functions::json(2008, '添加用户失败,请重试');
        }
    }

    //注销登录
    function out() {
        unset($_SESSION['agent']);
        functions::urlx(functions::urlc('agent', 'index', 'login'));
    }

    //修改密码
    function edit() {
        $user = $this->AI();
        functions::drive('agent')->edit($user);
    }

    //cookies验证
    private function AI() {
        //检测是否管理员
        return functions::api('loginc')->AI('agent', functions::urlc('agent', 'index', 'login'));
    }

    function applyWithdraw() {
        $agent = $this->AI();
        functions::drive('agent')->applyWithdraw($agent);
    }

}
