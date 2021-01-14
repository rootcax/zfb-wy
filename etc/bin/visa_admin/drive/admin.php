<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
class admin {

    //登录
    function login() {
        $username = functions::request('username');
        //$code = functions::request('code');
        //if (!functions::api('secoder')->check($code, 'login'))
        //    functions::json(5002, '验证码错误');
        $mysql = functions::open_mysql();
        $queryx = $mysql->query('admin', "username='{$username}'");
        if (!is_array($queryx[0]))
            functions::json(5001, '用户名不存在');

        //验证密码
        $pwd = md5(functions::request('pwd') . $queryx[0]['token']);
        if ($queryx[0]['pwd'] != $pwd)
            functions::json(5004, '密码不正确');
        //保存session
        $ip = functions::get_client_ip();functions::verify($ip);
        $_SESSION['user_admin'] = functions::encode(json_encode(array(
                    "sid" => $queryx[0]['id'],
                    "username" => $queryx[0]['username'],
                    "token" => $queryx[0]['token'],
                    "ip" => $ip,
                    "groupid" => $queryx[0]['groupid'],
                    'time' => time(),
                    "avatar" => $queryx[0]['avatar']
                        )), AUTH_KEY);
        $mysql->update('admin', array("ip" => $ip, "loginc" => time()), "id={$queryx[0]['id']}");
        functions::json(200, '登录成功');
    }

    //修改密码
    function edit($user) {
        $username = $user->username;
        $phone = functions::request('phone');
        if (!functions::isphone($phone))
            functions::json(5001, '手机不正确');
        $mysql = functions::open_mysql();
        //$code = intval(functions::request('code'));
        //$code_query = $mysql->query('codes', "code={$code} and phone={$phone} and typec=3");
        //if (!is_array($code_query[0])) functions::msg('验证码错误',functions::urlc('user', 'index', 'my',array('d'=>'edit')));
        //if (!is_array($code_query[0]))
        //    functions::json('-1', '验证码错误');
        //if (time()-$code_query[0]['ctime'] > $code_query[0]['survival']) functions::msg('验证码已过期',functions::urlc('user', 'index', 'my',array('d'=>'edit')));
        //if (time() - $code_query[0]['ctime'] > $code_query[0]['survival'])
        //    functions::json('-1', '验证码已过期');

        $pwd = functions::request('pwd');
        //if (!functions::ispwd($pwd)) functions::msg('密码不够安全,请重新输入',functions::urlc('user', 'index', 'my',array('d'=>'edit')));
        if (!functions::ispwd($pwd))
            functions::json('-1', '密码不够安全,请重新输入');

        //生成token
        $token = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 12);
        //生成密码
        $pwd = md5($pwd . $token);
        $up = $mysql->update('admin', array('pwd' => $pwd, 'phone' => $phone, 'token' => $token), "id={$user->sid}");
        if ($up) {
            functions::drive('users')->cdie($mysql, $phone, 3);
            //functions::msg('修改成功,请重新登陆',functions::urlc('user', 'index', 'my',array('d'=>'edit')));
            functions::json(200, '资料修改成功');
        } else {
            //functions::msg('修改失败',functions::urlc('user', 'index', 'my',array('d'=>'edit')));
            functions::json('-1', '资料修改失败');
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

    function updateWithdraw() {

        $id = intval(functions::request('id'));
        $type = functions::request('type');
        $type_arr = [2, 3, 4];
        if (!in_array($type, $type_arr))
            functions::json(-1, '当前更新的状态有误!');
        $msg = $type == 2 ? '提现已到账' : functions::request('msg');
        $mysql = functions::open_mysql();
        $result = $mysql->query("withdraw", "id={$id}");
        if (!is_array($result[0]))
            functions::json(-2, '当前订单不存在');
        $result = $result[0];
        $mysql->update("withdraw", [
            'types' => $type,
            'content' => $msg,
            'deal_time' => time()
                ], "id={$id}");
        //钱款驳回
        if ($type == 3) {
            //将钱款退款给用户
            if ($result['user_id'] != 0) {
                $user = $mysql->query("users", "id={$result['user_id']}")[0];
                $find_user = $mysql->query("users", "id={$user['parentid']}")[0];
                $table = "users";
            } else {
                $find_user = $mysql->query("agent", "id={$result['user_id']}")[0];
                $table = "agent";
            }

            if (is_array($find_user)) {
                $mysql->update($table, ['balance' => $find_user['balance'] + $result['amount']], "id={$find_user['id']}");
            }
        }
        functions::json(200, '处理成功');
    }

    //删除提现
    function deleteWithdraw() {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        //查询当前用户组是否存在
        $result = $mysql->query("withdraw", "id={$id}")[0];
        if (!is_array($result))
            functions::json(-2, '当前记录不存在');
        //删除
        $mysql->delete("withdraw", "id={$id}");
        functions::json(200, '操作完成,您已经将记录成功移除!');
    }

    //处理代付
    function updateOrder($manager) {
        $manager_id = $manager->sid;
        $id = intval(functions::request('id'));
        $type = functions::request('type');
        $msg = trim(functions::request('msg'));
        $type_arr = [2, 3];
        if (!in_array($type, $type_arr))
            functions::json(-1, '当前更新状态有误!');
        $msg = $type == 2 ? '提现已到账' : $msg;
        $mysql = functions::open_mysql();
        $result = $mysql->select("select a.*,b.id as user_id,b.parentid,b.status as user_status,b.publicKey from mi_dforders as a left join mi_users as b on a.memberCode=b.memberCode where a.id={$id}");
        if (!is_array($result[0]))
            functions::json(-2, '当前订单不存在');
        $result = $result[0];
        if ($result[0]['user_status']) {
            functions::json(-3, '当前用户已被禁用');
        }
        if ($type == 2) {
            $status = 111;
        } else {
            $status = 112;
        }
        $up = $mysql->update("dforders", [
            'status' => $status,
            'deal_content' => $msg,
            'dealer_id' => $manager_id,
            'update_time' => time()
                ], "id={$id}");
        if ($up) {
            //钱款驳回
            if ($type == 3) {
                //将钱款退款给用户
                $up_balance = $mysql->update("users", ['balance' => $result['balance'] + $result['amount']], "id={$result['user_id']}");
            }
            if ($type == 2) {
                if (!empty($result['notify_url'])) {
                    functions::api('reback')->df_request($mysql, $result);
                }
            }
            functions::json(200, '处理成功');
        } else {
            functions::json(200, '处理失败');
        }
    }

}
