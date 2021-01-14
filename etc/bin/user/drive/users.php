<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
class users {

    //登录
    function login() {
        $phone = functions::request('phone');
        //$code = functions::request('code');
        //if (!functions::api('secoder')->check($code, 'login'))
        //    functions::json(5002, '验证码错误');
        if (!functions::isphone($phone))
            functions::json(5001, '手机不正确');
        $mysql = functions::open_mysql();
        $queryx = $mysql->query('users', "phone={$phone}");
        if (!is_array($queryx[0]))
            functions::json(5003, '该手机暂未注册');
        //检测用户状态
        if ($queryx[0]['status'] != 1) {
            if ($queryx[0]['status'] == 0)
                functions::json(5005, '该用户暂未审核，请联系管理员审核');
            if ($queryx[0]['status'] == 2)
                functions::json(5006, '该用户已被冻结！');
        }

        //验证密码
        $pwd = md5(functions::request('pwd') . $queryx[0]['token']);
        if ($queryx[0]['pwd'] != $pwd)
            functions::json(5004, '密码不正确');
        //扩展功能接口
        $this->login_extend($queryx[0]);
        //保存session
        $ip = functions::get_client_ip();
        $_SESSION['user'] = functions::encode(json_encode(array(
                    "sid" => $queryx[0]['id'],
                    "phone" => $queryx[0]['phone'],
                    "token" => $queryx[0]['token'],
                    "ip" => $ip,
                    'time' => time(),
                    "balance" => $queryx[0]['balance'],
                    "avatar" => $queryx[0]['avatar'],
                    "keyid" => $queryx[0]['keyid']
                        )), AUTH_KEY);
        $session_id = session_id();
        setcookie('PHPSESSID', $session_id, time() + 15 * 24 * 3600);
        $mysql->update('users', array("ip" => $ip, "loginc" => time()), "id={$queryx[0]['id']}");
        functions::json(200, '登录成功');
    }

    //登录扩展私有接口（比如验证账户其他的可以写到这里）
    private function login_extend($user) {
        
    }

    //注册
    function register() {
        $phone = functions::request('phone');

        if (!functions::isphone($phone))
            functions::json(2002, '手机不正确');
        $mysql = functions::open_mysql();
        $sms_config = functions::get_Config("smsCog");
        if ($sms_config['register_sms']) {
            $code = intval(functions::request('code'));
            $code_query = $mysql->query('codes', "code={$code} and phone={$phone} and typec=1");
            if (!is_array($code_query[0]))
                functions::json(2003, '验证码错误');
            if (time() - $code_query[0]['ctime'] > $code_query[0]['survival'])
                functions::json(2004, '验证码已过期');
        }
        $pwd = functions::request('pwd');
        $repwd = functions::request('repwd');
        if (!functions::ispwd($pwd))
            functions::json(2005, '密码输入错误,请输入6-26位的字符密码');
        if ($pwd != $repwd)
            functions::json(2006, '两次密码输入不一样');
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
        $config = functions::get_Config('registerCog');
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
            'bank2alipay_withdraw' => $config['bank2alipay_withdraw']
        ));
        if ($in) {
            $this->cdie($mysql, $phone, 1);
            functions::json(200, '注册成功,请联系管理员审核');
        } else {
            functions::json(2008, '注册失败,请重试');
        }
    }

    //找回密码
    function forget() {
        $phone = functions::request('phone');
        $code = intval(functions::request('code'));
        if (!functions::isphone($phone))
            functions::json(3002, '手机不正确');
        $mysql = functions::open_mysql();
        $code_query = $mysql->query('codes', "code={$code} and phone={$phone} and typec=2");
        if (!is_array($code_query[0]))
            functions::json(3003, '验证码错误');
        if (time() - $code_query[0]['ctime'] > $code_query[0]['survival'])
            functions::json(3004, '验证码已过期');
        $pwd = functions::request('pwd');
        $repwd = functions::request('repwd');
        if (!functions::ispwd($pwd))
            functions::json(3005, '密码输入错误,请输入6-26位的字符密码');
        if ($pwd != $repwd)
            functions::json(3006, '两次密码输入不一样');
        $queryx = $mysql->query('users', "phone={$phone}");
        if (!is_array($queryx[0]))
            functions::json(3007, '该手机暂未注册');
        //生成token
        $token = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 12);
        //生成密码
        $pwd = md5($pwd . $token);
        $in = $mysql->update('users', array(
            "pwd" => $pwd,
            "token" => $token
                ), "id={$queryx[0]['id']}");
        if ($in) {
            $this->cdie($mysql, $phone, 1);
            functions::json(200, '修改成功');
        } else {
            functions::json(3008, '修改失败,请重试');
        }
    }

    //修改密码
    function edit($user) {
        $phone = $user->phone;
        $mysql = functions::open_mysql();
        //$code = intval(functions::request('code'));
        //$code_query = $mysql->query('codes', "code={$code} and phone={$phone} and typec=3");
        //if (!is_array($code_query[0])) functions::msg('验证码错误',functions::urlc('user', 'index', 'my',array('d'=>'edit')));
        //if (!is_array($code_query[0]))
        //   functions::json('-1', '验证码错误');
        //if (time()-$code_query[0]['ctime'] > $code_query[0]['survival']) functions::msg('验证码已过期',functions::urlc('user', 'index', 'my',array('d'=>'edit')));
        //if (time() - $code_query[0]['ctime'] > $code_query[0]['survival'])
        //    functions::json('-1', '验证码已过期');

        $pwd = functions::request('pwd');
        //生成密码
        if (!empty($pwd)) {
            if (!functions::ispwd($pwd))
                functions::json('-1', '密码不够安全,请重新输入');
            $edit['pwd'] = md5($pwd . $user->token);
        }
        if ($user->parentid != 0) {
            $bank_type = intval(functions::request('bank_type'));
            if ($bank_type == 1) {
                //支付宝
                $alipay_name = functions::request('alipay_name');
                //账号
                $alipay_content = functions::request('alipay_content');
                if (empty($alipay_name) || empty($alipay_content))
                    functions::json(-1, '支付宝姓名或账号不能为空!');
                //写入
                $edit['bank'] = json_encode(['type' => 1, 'name' => $alipay_name, 'card' => $alipay_content], JSON_UNESCAPED_UNICODE);
            }
            if ($bank_type == 2) {
                //姓名
                $bank_name = functions::request('bank_name');
                //银行名称
                $bank = functions::request('bank');
                //账号
                $card = functions::request('card');
                if (empty($bank_name) || empty($bank) || empty($card))
                    functions::json(-1, '银行卡信息有误,请填写正确!');
                $edit['bank'] = json_encode(['type' => 2, 'name' => $bank_name, 'card' => $card, 'bank' => $bank], JSON_UNESCAPED_UNICODE);
            }
            
        }
        if (!is_array($edit))
                functions::json(-3, '您没有做任何修改哟!');

            $up = $mysql->update('users', $edit, "id={$user->sid}");
            if ($up) {
                functions::drive('users')->cdie($mysql, $phone, 3);
                functions::json(200, '您的资料已经修改成功啦');
            } else {
                functions::json('-2', '您的资料修改失败啦');
            }
    }

    //短信
    function sms() {
        $typec = intval(functions::request('typec'));
        $phone = functions::request('phone');
        $ip = functions::get_client_ip(); //ip地址
        //手机号 phone
        //注册验证码
        if (!in_array($typec, array(1, 2, 3)))
            functions::json(1001, '验证码类型不正确');
        if (!functions::isphone($phone))
            functions::json(1002, '手机不正确');
        $mysql = functions::open_mysql();
        //6位验证码
        $code = mt_rand(100, 999) . mt_rand(100, 999);
        //检测一个ip发送短信次数是否超过3次
        $rows = $mysql->query("codes", "ip='{$ip}'");
        if (count($rows) >= 3) {
            $imx = 0;
            foreach ($rows as $im) {
                //这里循环加入已存活的验证码次数
                $surd = $im['ctime'] + $im['survival'];
                if (time() < $surd) {
                    //未过期的验证码
                    $imx++;
                }
            }
            //如果验证码存活大于3则放弃
            if ($imx >= 3)
                functions::json(1003, '验证码发送频繁,请稍后再试');
        }
        $queryx = $mysql->query('users', "phone={$phone}"); //查询手机
        //手机注册验证码
        if ($typec === 1) {
            //查看是否已经有该手机存在了
            if (is_array($queryx[0]))
                functions::json(1005, '该手机已被注册');
        }

        //找回密码
        if ($typec === 2) {
            //查看手机是否存在
            if (!is_array($queryx[0]))
                functions::json(1006, '该手机暂未注册');
        }
        //修改密码
        if ($typec === 3) {
            //查看手机是否存在
            if (!is_array($queryx[0]))
                functions::json(1006, '该手机暂未注册');
        }
        //进入数据库查是否有数据
        $dtime = time();
        //查询条件：手机号  类型  过期
        $row = $mysql->query("codes", "phone={$phone} and typec={$typec} and overdue>{$dtime}");
        $overtime = $row[0]['overdue'] - time();
        if (is_array($row[0]))
            functions::json(1004, "请过{$overtime}秒后再尝试!", array('ms' => $overtime, 'over' => date('Y/m/d H:i:s', $row[0]['overdue'])));
        //发送验证码
        $in = $mysql->insert('codes', array(
            "typec" => $typec,
            "code" => $code,
            "phone" => $phone,
            "overdue" => (time() + 90),
            "ip" => $ip,
            "ctime" => time(),
            "survival" => 300
        ));
        if ($in) {
            //发送验证码
            functions::api('sms')->send($phone, $code);
            functions::json(200, '发送成功', array('date' => date('Y/m/d H:i:s', time())));
        }
    }

    //头像
    function avatar($user) {
        //上传文件到自己的空间
        $path = _public . 'upload/' . $user->sid . '/images';
        //ok
        $upload = functions::api('upload')->run($_FILES['avatar'], $path, array('jpg', 'png'), 1000);
        //if (!is_array($upload)) functions::msg('头像上传错误,请选择一张小于1M的图片!',functions::urlc('user', 'index', 'my',array('d'=>'avatar')));
        if (!is_array($upload))
            functions::json(-1, '头像更换失败,请选择一张小于1M的图片!');
        //上传成功保存到数据库
        $mysql = functions::open_mysql();
        $up = $mysql->update('users', array('avatar' => $upload['new']), "id={$user->sid}");
        if ($up > 0) {
            //functions::msg('上传成功',functions::urlc('user', 'index', 'my',array('d'=>'avatar')));
            functions::json(200, '头像更换成功');
        } else {
            //functions::msg('上传失败',functions::urlc('user', 'index', 'my',array('d'=>'avatar')));
            functions::json(-1, '头像更换失败');
        }
    }

    //清理所有关于自己类型的验证码
    function cdie($mysql, $phone, $typec) {
        $mysql->delete("codes", "typec={$typec} and phone={$phone}");
    }

    function updatePollMode($user) {
        $typec = intval(functions::request('typec'));
        $mode = intval(functions::request('mode'));

        $mysql = functions::open_mysql();
        if ($typec == 26) {
            $query = $mysql->update('users', array('bank2alipay_polling' => $mode), "id={$user->sid}");
            $msg = "支付宝轮询模式更新成功";
        }

        if ($query > 0) {
            functions::json("200", $msg);
        } else {
            functions::json(-1, '系统错误，请联系管理员');
        }
    }

    //申请提现
    function applyWithdraw($user) {
        $phone = $user->phone;
        $mysql = functions::open_mysql();
        //$code = intval(functions::request('code'));
        //$code_query = $mysql->query('codes', "code={$code} and phone={$phone} and typec=3");
        //if (!is_array($code_query[0]))
        //    functions::json('-1', '验证码错误');
        //if (time() - $code_query[0]['ctime'] > $code_query[0]['survival'])
        //    functions::json('-2', '验证码已过期');
        //$settle_money = functions::get_settleMoney($user);
        //if(empty($settle_money))
        //   functions::json ('-7', '可提现金额为0');
        $money = floatval(functions::request('money'));
        $parent = $mysql->query("users", "id={$user->parentid}");
        if ($parent[0]['balance'] < $money)
            functions::json('-3', '余额不足，无法提现');
        //if($money!=$settle_money)
        //    $money = $settle_money;
        $withdrawCog = functions::get_Config('withdrawCog');
        $drawCount = $withdrawCog['drawcount'];
        $min_payment = $withdrawCog['min_payment'];
        $max_payment = $withdrawCog['max_payment'];
        $drwaFee = $withdrawCog['drwaFee'];
        //$cycle = $withdrawCog['cycle'];
        $time = strtotime(date('Y-m-d', time()));
        $drawdata = $mysql->query('withdraw', "user_id={$user->sid} and apply_time>={$time}", 'count(id) as apply_count');
        if ($drawdata[0]['apply_count'] > $drawCount)
            functions::json('-4', '超出最大提现次数');
        //if($money>$max_payment)
        //    functions::json ('-5','超出最大提现金额');
        if ($money < $min_payment)
            functions::json('-6', '超出最小提现金额');
        //手续费
        //$fees = floatval($drwaFee) * $money;
        $fees = floatval($drwaFee);
        //计算减掉的金额
        $user_amount = $parent[0]['balance'] - $money;
        //更新用户账户信息
        if ($mysql->update("users", ['balance' => $user_amount], "id={$user->parentid}") > 0) {
            $in = $mysql->insert("withdraw", [
                'user_id' => $user->sid,
                'old_amount' => $parent[0]['balance'],
                'amount' => $money,
                'new_amount' => $user_amount,
                'types' => 1,
                'content' => '提现到账时间为2小时-24小时内到账',
                'apply_time' => time(),
                'deal_time' => 0,
                'flow_no' => date("YmdHis") . mt_rand(100000, 999999),
                'fees' => $fees
            ]);
            $this->cdie($mysql, $user->phone, 3);
            functions::json(200, '您的提现已经提交成功!');
        } else {
            functions::json(-1, '系统正在维修,请稍后再提现!');
        }
    }

    //修改账号
    function key_edit($user) {
        $id = intval($user->sid);
        $mysql = functions::open_mysql();
        $publicKey = trim(functions::request('publicKey'));
        $update = $mysql->update('users', array('publicKey' => $publicKey), "id={$id}");
        if ($update) {
            functions::json(200, '提交成功');
        } else {
            functions::json(-1, '提交失败');
        }
    }

}
