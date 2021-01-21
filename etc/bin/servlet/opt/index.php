<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//云端
class index {

    //得到所有需要登录的账号
    function GetLogin() {
        ini_set('max_execution_time', '0');
        $phone = functions::request('phone');
        $key = functions::request('key');
        $sdk = functions::request('sdk');
        $typec = intval(functions::request('typec'));
        $user = $this->Check_Token($phone, $key);
        $mysql = functions::open_mysql();
        if ($typec == 1) {
            $data = $mysql->query('land', "login=1 and typec=1 and ban=1 and userid={$user['id']} and sdk = '{$sdk}'", 'id,userid,username,typec,login,image,onback,sdk');
            $mysql->update('land', array('login' => 2), "login=1 and ban=1 and userid={$user['id']} and sdk='{$sdk}'");
        } else {
            $data = $mysql->query('land', "login=1 and typec=2 and ban=1 and userid={$user['id']}", 'id,userid,username,typec,login,image,onback');
            $mysql->update('land', array('login' => 2), "login=1 and ban=1 and userid={$user['id']}");
        }
        functions::json(200, '读取完毕', array('num' => count($data), 'acc' => $data));
    }

    function CheckUser() {
        ini_set('max_execution_time', '0');
        $phone = functions::request('phone');
        $key = functions::request('key');

        $mysql = functions::open_mysql();
        $token = functions::encode(str_replace("@", "+", $key), SERVER_KEY, 2);
        $user = $mysql->query("users", "phone='{$phone}' and token='{$token}'");
        if (!is_array($user[0])) {
            functions::json_encode(-2, '用户不存在或通信KEY有误');
        } else {
            functions::json(200, '登录成功');
        }
    }

    //检查支付宝sdk
    function CheckAliUser() {
        ini_set('max_execution_time', '0');
        $phone = functions::request('phone');
        $key = functions::request('key');
        $sdk = functions::request('sdk');
        $mysql = functions::open_mysql();
        $token = functions::encode(str_replace("@", "+", $key), SERVER_KEY, 2);
        $user = $mysql->query("users", "phone='{$phone}' and token='{$token}'");
        if (!is_array($user[0])) {
            functions::json_encode(-2, '用户不存在或通信KEY有误');
        } else {
            $userid = $user[0]['id'];
            $land = $mysql->query("land", "userid='{$userid}' and sdk='{$sdk}'");
            if (!is_array($land[0])) {
                functions::json_encode(-1, '用户sdk不存在，请核对sdk后重新开启');
            } else {
                functions::json(200, '登录成功');
            }
        }
    }

    //获取已经失效的收款账户
    function GetInvalid() {
        ini_set('max_execution_time', '0');
        //$this->key();
        $phone = functions::request('phone');
        $token = functions::request('key');
        $sdk = functions::request('sdk');
        $typec = intval(functions::request('typec'));
        $user = $this->Check_Token($phone, $token);
        $mysql = functions::open_mysql();
        //1->请求登录  2->开始登录  3->正常在线  4->登录异常
        $qaqTime = time() - 120;
        if ($typec == 1) {
            $data = $mysql->query("land", "login<>0 and timec<{$qaqTime} and timec<>0 and userid={$user['id']} and sdk='{$sdk}' and typec=1", "id,userid,username,typec,login,image,onback,timec");
            //置未登录->开始登录的
            $mysql->update("land", array("login" => 0), "login=2 and timec<{$qaqTime} and userid={$user['id']} and sdk='{$sdk}' and typec=1");
            //账户异常发送短信通知
            $arp_query = $mysql->query("land", "login=3 and timec<{$qaqTime} and userid={$user['id']} and sdk='{$sdk}' and typec=1");
            if (is_array($arp_query[0])) {
                //置账户异常
                $arp = $mysql->update("land", array("login" => 4, "timec" => 0), "login=3 and timec<{$qaqTime} and userid={$user['id']} and sdk='{$sdk}' and typec=1");
                //发送异常短信
                foreach ($arp_query as $a) {
                    //查询该吊毛的手机号
                    $users = $mysql->query("users", "id={$a['userid']}");
                    //发送短信通知
                    if (is_array($users[0])) {
                        functions::api('sms')->send_abnormal($users[0]['phone'], $a['username']);
                    }
                }
            }
            //返回数据
            functions::json(200, '读取完毕', array('num' => count($data), 'acc' => $data));
        } else {
            $data = $mysql->query("land", "login<>0 and timec<{$qaqTime} and timec<>0 and userid={$user['id']} and typec=2", "id,userid,username,typec,login,image,onback,timec");
            //置未登录->开始登录的
            $mysql->update("land", array("login" => 0), "login=2 and timec<{$qaqTime} and userid={$user['id']} and typec=2");
            //账户异常发送短信通知
            $arp_query = $mysql->query("land", "login=3 and timec<{$qaqTime} and userid={$user['id']} and typec=2");
            if (is_array($arp_query[0])) {
                //置账户异常
                $arp = $mysql->update("land", array("login" => 4, "timec" => 0), "login=3 and timec<{$qaqTime} and userid={$user['id']} and typec=2");
                //发送异常短信
                foreach ($arp_query as $a) {
                    //查询该吊毛的手机号
                    $users = $mysql->query("users", "id={$a['userid']}");
                    //发送短信通知
                    if (is_array($users[0])) {
                        functions::api('sms')->send_abnormal($users[0]['phone'], $a['username']);
                    }
                }
            }
            //返回数据
            functions::json(200, '读取完毕', array('num' => count($data), 'acc' => $data));
        }
    }

    //账户心跳
    function Wander() {
        $this->key();
        //收款账号id
        $uid = intval(functions::request('uid'));
        //用户id
        $userid = intval(functions::request('userid'));
        $sdk = functions::request('sdk');
        $mysql = functions::open_mysql();
        if (empty($sdk)) {
            $mysql->update("land", array("timec" => time(), "login" => "3", "onback" => "1"), "id={$uid} and userid={$userid}");
        } else {
            $mysql->update("land", array("timec" => time(), "login" => "3", "onback" => "1"), "id={$uid} and userid={$userid} and sdk='{$sdk}'");
        }

        functions::json(200, '更新数据成功');
    }

    //将所有过期的订单置过期，以及置空闲二维码{私有}
    function Arranged() {
        ini_set('max_execution_time', '0');
        $this->key();
        //收款账号id
        $uid = intval(functions::request('uid'));
        //用户id
        $userid = intval(functions::request('userid'));
        //收款类型
        $payc = intval(functions::request('payc'));

        $mysql = functions::open_mysql();
        //先将所有过期订单列出来
        $timeTakes = intval(time() - 270);
        $mysql->update("takes", array("state" => 3), " create_time<{$timeTakes} and land_id={$uid} and userid={$userid} and state=1"); //将过期的订单pass
        //列出所有正在使用的二维码
        $qrcode = $mysql->query("qrcode_link", "userid={$userid} and money<>0 and land_id={$uid} and state=2 and typec={$payc}");
        foreach ($qrcode as $qr) {
            $mark = $qr['mark'];
            $push = $mysql->query("takes", "money={$qr['money']} and payc={$payc} and state=1 and land_id={$uid} and userid={$userid} and mark='{$mark}'");
            if (!is_array($push[0])) {
                //将二维码恢复正常
                $mysql->update("qrcode_link", array("state" => 1), "id={$qr['id']}");
            }
        }
        functions::json(200, '共处理:' . count($qrcode));
    }

    //自动回调
    function Reback() {
        ini_set('max_execution_time', '0');
        $this->key();
        $mysql = functions::open_mysql();
        //收款账号id
        $uid = intval(functions::request('uid'));
        //用户id
        $userid = intval(functions::request('userid'));
        //查询该账号是否开启了监控
        $land = $mysql->query("land", "id={$uid} and userid={$userid} and onback=1");
        if (!is_array($land[0]))
            functions::json(10, 'please open the monitor');
        //如果已经开启了监控，查询账户余额
        $user = $mysql->query("users", "id={$userid}");

        if ($user[0]['balance'] <= 0)
            functions::json(11, 'please recharge');
        //开始查询收款订单 { 得到请求列表 }
        $orders = $mysql->query("orders", "land_id={$uid} and userid={$userid} and api_state=1 and error_times<6");
        if (!is_array($orders[0]))
            functions::json(12, 'temporary no request');
        //查询到订单列表,得到请求接口的手续费
        //开始回调

        foreach ($orders as $order) {
//            if (!empty($order['agentid'])) {
//                $poundage = functions::get_orderFee($userid, $order['payc']);
//                $agent_poundage = functions::get_agentFee($order['agentid'], $order['payc']);
//            } else {
//                $poundage = functions::get_orderFee($userid, $order['payc']);
//                $agent_poundage = 0;
//            }
            //functions::api('reback')->request($mysql, $order['id'], $poundage, $user[0], $agent_poundage);
            functions::api('reback')->request($mysql, $order['id'], $user[0]);
        }
        functions::json(200, '全部回调完成');
    }

    //自动回调
    function autoReback() {
        ini_set('max_execution_time', '0');
        $key = functions::request('key');
        if ($key == "visa_admin") {
            //如果已经开启了监控，查询账户余额
            $start_time = strtotime(date('Y-m-d 00:00:00', time()));
            $end_time = strtotime(date('Y-m-d 23:59:59', time()));
            $mysql = functions::open_mysql();
            //开始查询收款订单 { 得到请求列表 }
            $orders = $mysql->query("orders", "api_state=1 and error_times<6 and order_time>={$start_time} and order_time<={$end_time}", "id,land_id,userid");
            if (!is_array($orders[0]))
                functions::json(12, '赞无未下发的订单');
            //查询到订单列表,得到请求接口的手续费
            //开始回调
            foreach ($orders as $order) {
                $users = $mysql->query("users", "id={$order['userid']}");
                functions::api('reback')->request($mysql, $order['id'], $users[0]);
            }
        }
    }

    //自动回调
    function Refer() {
        /*
        ini_set('max_execution_time', '0');
        $mysql = functions::open_mysql();
        //用户id
        $num = functions::request('num');
        //开始查询收款订单 { 得到请求列表 }
        $takes = $mysql->query("takes", "num={$num}");
        //获取用户信息
        $user = $mysql->query("users", "id={$takes[0]['userid']}");
        //开始回调
        functions::api('reback')->refer($mysql, $takes[0], $user[0]);
        */
    }

    //设置账号(2018-11-21修改)
    function SetLogin() {
        $this->key();
        $id = intval(functions::request('id')); //操作id
        $AliUserId = functions::request("Aliuid");
        $login = intval(functions::request("login"));
        $mysql = functions::open_mysql();
        $array['login'] = $login;
        if (!empty($AliUserId)) {
            $array['aliUserID'] = $AliUserId;
        }
        $up = $mysql->update('land', $array, "id={$id}");
        if ($up > 0) {
            functions::json(200, 'ID:' . $id . ',数据更新完成');
        } else {
            functions::json(-1, 'ID:' . $id . ',数据更新异常');
        }
    }

    //上传二维码图片
    function UploadLoginImage() {
        $this->key();
        $id = intval(functions::request('id')); //操作id
        $mysql = functions::open_mysql();
        $land = $mysql->query("land", "id={$id}");
        if (!is_array($land[0]))
            functions::json(-1, '数据错误');
        $dir = _public . 'upload/' . $land[0]['userid'] . '/images';
        $imagex = functions::api('upload')->run($_FILES['qrcode'], $dir, array('jpg', 'png', 'jpeg'), 5000, $land[0]['username'] . '.jpg');
        if (!is_array($imagex))
            functions::json(-1, '文件数据错误');
        $up = $mysql->update("land", array("image" => $land[0]['username'] . '.jpg'), "id={$id}");
        if ($up > 0) {
            functions::json(200, '更新数据成功');
        } else {
            functions::json(-1, '更新数据失败');
        }
    }

    //上传cookie
    function UploadCookie() {
        $this->key();
        $cookies = functions::request('ck');
        $uid = intval(functions::request('uid')); //操作id
        //用户id
        $userid = intval(functions::request('userid'));
        $timec = time();
        $typec = intval(functions::request('typec'));
        $mysql = functions::open_mysql();
        $land = $mysql->query("land", "id={$uid} and typec={$typec}");
        if (!is_array($land[0]))
            functions::json(-1, '数据错误');
        $land_c = $mysql->query("cookie", "land_id={$uid} and typec={$typec} and userid={$userid}");
        if (is_array($land_c[0])) {
            $id = $land_c[0]['id'];
            $up = $mysql->update("cookie", array("cookie" => $cookies, "timec" => $timec), "id={$id}");
        } else {
            $up = $mysql->insert("cookie", array("userid" => $userid, "land_id" => $uid, "cookie" => $cookies, "typec" => $typec, "timec" => $timec));
        }
        if ($up > 0) {
            functions::json(200, '更新数据成功');
        } else {
            functions::json(-2, '更新数据失败');
        }
    }

    //返回程序运行状态
    function ReRunState() {
        $x = intval(functions::request('x')); //
        if ($x > 1) {
            functions::json(-1, '程序已经运行，请勿重复运行！');
        }
    }

    //提交订单
    function RequestMent() {
        //$this->key();
        $mysql = functions::open_mysql();
        //得到提交的信息
        $uid = functions::request('uid'); //收款账号id
        //用户id
        $userid = functions::request('userid'); //用户id
        //金额 { 通过金额判断 }
        $money = functions::request('money'); //金额
        //支付类型 1、支付宝   2、微信   3、QQ
        $payc = intval(functions::request('payc'));
        //转账备注
        $remark = functions::request('remark');
        $orderNo = functions::request('orderNo');
        $sign = functions::request("sign");
        $user = $mysql->query('users', "id={$userid}");
        $check_sign = md5('money=' . $money . '&orderNo=' . $orderNo . '&payc=' . $payc . '&remark=' . $remark . '&uid=' . $uid . '&userid=' . $userid . '&key=' . str_replace("+", "@", functions::encode($user[0]['token'], SERVER_KEY, 1)));
        //验签
        if ($check_sign == $sign) {
            //判断是否存在有同一备注同一金额的订单以第三方订单号为准（微信以消息ID作为订单号）
            $time = strtotime(date('Y-m-d', time()));
            $order = $mysql->query("takes", "orderNo='{$orderNo}' and state=2 and create_time>{$time}");
            if (is_array($order[0])) {
                exit('ok');
            } else {
                if (strpos($remark, "商品") !== false || strpos($remark, "收款") !== false) {
                    $remark = "";
                    $where = " and money='{$money}'";
                } else if (strpos($remark, "=") !== false) {
                    $remark = strstr($remark, "=", TRUE);
                    $where = " and mark='{$remark}'";
                } else {
                    $where = " and mark='{$remark}'";
                }
                //检测是否属于订单的金额 :: 为了防止判断出错,所以这里加强判断,用户id关联收款id
                $query = $mysql->query('takes', "money={$money} and payc={$payc} and state=1 and land_id={$uid} and mark='{$remark}' and userid={$userid}", null, null, "desc", "1"); // and payc={$payc} and state=1 and land_id={$uid} and userid={$userid}
                //如果属于第三方订单平台，那么置已支付
                $paytime = time();
                //检测到无订单信息，次数不做任何操作直接返回未通知到
                if (!is_array($query[0]) && floatval($money) > 0) {
                    //创建订单
//                $mysql->insert('orders', array(
//                  'land_id'=>$uid,
//                  'userid'=>$userid,
//                  'num'=>date("YmdHis") . mt_rand(10000,99999),
//                  'money'=>$money,
//                  'remark'=>$remark,
//                  'payc'=>$payc,
//                  'order_time'=>$paytime,
//                  'api_state'=>2,
//                  'http'=>'无需请求',
//                  'request_time'=>0,
//                  'payment'=>0,
//                  'takes_id'=>0
//                  )); 
                    exit('通知失败，未找到对应订单信息!');
                } else {
                    //takes
                    $update = $mysql->update('takes', array('pay_time' => $paytime, 'state' => 2, 'orderNo' => $orderNo), "id={$query[0]['id']} and state=1 {$where}");
                    if ($update > 0) {
                        //创建订单
                        $insert = $mysql->insert('orders', array(
                            'land_id' => $uid,
                            'userid' => $userid,
                            'num' => $query[0]['num'],
                            'money' => $money,
                            'remark' => $remark,
                            'payc' => $payc,
                            'order_time' => $paytime,
                            'api_state' => 1,
                            'http' => '还未请求',
                            'request_time' => 0,
                            'payment' => 0,
                            'takes_id' => $query[0]['id'],
                            'orderNo' => $orderNo,
                            'type' => $query[0]['type'],
                            'agentid' => $query[0]['agentid']
                        ));
                        if ($insert > 0) {
                            //exit('订单处理成功');
                            //更新二维码状态为空闲状态
                            $mysql->update('qrcode_link', array('state' => 1), "land_id={$uid} and userid={$userid} and mark='{$remark}'");
                            //更新收款账号额度
                            $land = $mysql->query("land", "id={$uid} and userid={$userid}");
                            $land = $land[0];
                            if (floatval($land['requota']) != -1) {
                                $new_quota = floatval($land['quota']) + $money;
                                //如果额度用完，将收款账户状态更改为"2"（数据库任务将恢复该状态）
                                if ($new_quota < floatval($land['requota'])) {
                                    $mysql->update("land", array('quota' => $new_quota), "id={$land['id']} and userid={$land['userid']}");
                                } else {
                                    $mysql->update("land", array('quota' => $land['requota'], 'status' => 2), "id={$uid} and userid={$userid}");
                                }
                            }
                            exit('ok');
                        } else {
                            exit('系统错误,原因:写入订单失败!');
                        }
                    } else {
                        exit('系统错误,原因:更新订单数据失败');
                    }
                }
            }
        } else {
            exit("签名错误！");
        }
    }

    //验证key是否正确
    private function key() {
        $key = functions::request('key');
        $phone = functions::request('phone');
        $mysql = functions::open_mysql();
        $token = functions::encode(str_replace("@", "+", $key), SERVER_KEY, 2);
        $user = $mysql->query("users", "phone='{$phone}'");
        if ($token != $user[0]['token'])
            exit('通信KEY错误');
    }

    private function Check_Token($phone, $key) {
        $mysql = functions::open_mysql();
        $token = functions::encode(str_replace("@", "+", $key), SERVER_KEY, 2);
        $user = $mysql->query("users", "phone='{$phone}' and token='{$token}'");
        if (!is_array($user[0])) {
            return "";
        } else {
            return $user[0];
        }
    }

    //app登录
    //登录
    function applogin() {
        $phone = functions::request('phone');
        if (empty($phone)) {
            functions::json(5005, '账号不能为空');
        }
        $mysql = functions::open_mysql();
        $queryx = $mysql->query('sys_user', "account='{$phone}'");
        $user = $queryx[0];
        if (!is_array($user))
            functions::json(5003, '账号暂未注册');
        //验证密码
        $pwd = md5(functions::request('pwd'));
        $pwd = sha1($pwd . 'Signsduihfnsk&5sdHwifjpWF@#TUIsfzl_sqyzt');
        if ($user['password'] != $pwd)
            functions::json(5004, '密码不正确');
        //扩展功能接口
        $this->login_extend($user);
        //保存session
        $ip = functions::get_client_ip();
//        $userdata = str_replace("+", "@", functions::encode(json_encode(array(
        //                  "id" => $queryx[0]['id'],
        //                "phone" => $queryx[0]['phone'],
        //              "token" => $queryx[0]['token'],
        //            "ip" => $ip,
        //          'time' => time(),
        //        "balance" => $queryx[0]['balance']
        //          )), AUTH_KEY));
        $seed = md5(microtime() . 'uihfnsk&5sd' . mt_rand(100000, 999999));
        $token = substr($seed, 8, 16);

        $userdata = array(
            "id" => $user['id'],
            "phone" => $user['account'],
            "token" => $token,
            "ip" => $ip,
            'time' => time(),
            "balance" => $user['balance']);
        $mysql->update('sys_user', array("login_ip" => $ip, "token" => $token), "id={$user['id']}");
        functions::json(200, '登录成功', $userdata);
    }

    //登录扩展私有接口（比如验证账户其他的可以写到这里）
    private function login_extend($user) {
        
    }

    function GetLandInfo() {
        //$data = functions::encode(str_replace("@", "+", functions::request('data')), AUTH_KEY, 2);
        //$data = json_decode($data, true);
        $data = functions::request('data');
        $userid = functions::request('userid');
        $phone = functions::request('phone');
        $token = functions::request('token');
        //$userid = $data['id'];
        //$phone = $data['phone'];
        //$token = $data['token'];
        $mysql = functions::open_mysql();
        $land = $mysql->query('sk_ma', "uid={$userid} and status < 99", "id,uid as userid,ma_account as username,mtype_id as typec");
        //$encode_land = str_replace("+", "@", functions::encode(json_encode($land), AUTH_KEY, 1));
        functions::json(200, '获取成功', $land);
    }

    function getNewOrder() {
        $now_time = time();
        $ma_id = functions::request('id');
        $mysql = functions::open_mysql();
        $order = $mysql->select("select log.*,b.bank_name,b.bank_token,b.bank_code 
                        from sk_order log 
                        left join cnf_bank b on log.ma_bank_id=b.id 
                        left join sk_order_prev prev on log.order_sn=prev.order_sn 
                        where log.ma_id={$ma_id} and prev.state=0 and prev.create_qrcode_status=2 and prev.over_time>{$now_time}");
        if (empty($order)) {
            functions::json(-3, '暂时还没有订单');
        }
        $order = $order[0];
        $res = $mysql->update("sk_order_prev", ["state" => 1], "order_sn='{$order['order_sn']}'");
        if (!$res) {
            functions::json(-1, '获取订单状态失败');
        }

        $qrcode_array['state'] = 0;
        $qrcode_array['userid'] = $order['muid'];
        $qrcode_array['land_id'] = $order['ma_id'];
        $qrcode_array['money'] = floatval($order['money']);
        $qrcode_array['money_res'] = floatval($order['real_money']);
        $qrcode_array['qrcode'] = "";
        $qrcode_array['mark'] = $order['order_sn'];
        $qrcode_array['typec'] = $order['ptype'];
        $qrcode_array['info'] = $order['order_sn'];
        //$qrcode_array['device'] = $order['device'];
        $qrcode_array['bank'] = $order['bank_token'];
        $qrcode_array['bank_name'] = $order['bank_name'];
        $qrcode_array['create_time'] = $order['create_time'];
        $return_data = [
            'type' => 'qrcodec',
            'data' => $qrcode_array
        ];
        functions::json(200, '获取成功', $return_data);
    }

    function updateOrder() {
        $path = 'log/' . date('Ymd') . '/';
        $filename = 'test.log';
        if (!is_dir($path)) {
            $flag = mkdir($path, 0777, true);
        }

        $remark = functions::request('mark');
        $state = functions::request('state');
        $bank_token = functions::request('bank');
        $h5_link = functions::request('h5_link');
        $post_url = $_REQUEST['url'];//functions::request('url');
        $typec = functions::request('typec');
        $qrcode = $_REQUEST['qrcode'];
        $bank_name = $_REQUEST['bank_name'];

        $mysql = functions::open_mysql();
        $banks = $mysql->select("select * from cnf_bank where bank_token='{$bank_token}'");
        if (empty($banks)) {
            functions::json(-1, '更新失败, 银行信息查询失败');
        }
        $bank = $banks[0];
        //file_put_contents($path . $filename, date('Y-m-d H:i:s') . '----begin to update qrcode----' . $bank_name . '-----' . PHP_EOL, FILE_APPEND);
        //file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-h5_link-' . $h5_link . '-----' . PHP_EOL, FILE_APPEND);
        //file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-qrcode-' . substr($qrcode, 0, 20) . '-----' . PHP_EOL, FILE_APPEND);
        //file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-bank-' . $bank_token . '-----' . PHP_EOL, FILE_APPEND);
        //file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-state-' . $state . '-----' . PHP_EOL, FILE_APPEND);
        //file_put_contents($path . $filename, date('Y-m-d H:i:s') . '----end to update qrcode----' . $bank_name . '-----' . PHP_EOL. PHP_EOL. PHP_EOL, FILE_APPEND);

        if ($qrcode != "" && $qrcode != null) {
            $mysql = functions::open_mysql();
            $row_count = 0;
            if ($bank['bank_code'] == "CCB" || $bank['bank_code'] =="CMB") {
                if (!empty($h5_link)) {
                    $row_count = $mysql->update("sk_order_prev", ["qr_status" => $state, "h5_link" => $h5_link], "order_sn='{$remark}'");
                } else {
                    $row_count = $mysql->update("sk_order_prev", ["qrcode" => $qrcode, "post_url" => $post_url], "order_sn='{$remark}' and qr_status=0");
                }
            } else {
                $row_count = $mysql->update("sk_order_prev", ["qr_status" => $state, "qrcode" => $qrcode, "post_url" => $post_url], "order_sn='{$remark}' and qr_status=0");
            }

            if (empty($row_count)) {
                functions::json(-1, '更新失败');
            } else {
                functions::json(200, '更新成功');
            }
        } else {
            functions::json(-1, '更新失败, 参数不正确');
        }
    }

    function updateMask() {
        $id = functions::request('mask');
        $state = functions::request('status');

        $mysql = functions::open_mysql();
        $row_count = $mysql->update("sk_ma", ["status" => $state], "id={$id} and status<99");
        if (empty($row_count)) {
            functions::json(-1, '更新失败');
        } else {
            functions::json(200, '更新成功');
        }
    }

    //上传用户二维码链接，保存到数据库
    function UploadImage() {
        $dir = _public . 'upload/files';
        $imagex = functions::api('upload')->run($_FILES['file'], $dir, array('txt'), 5000, 'qrcode_link.txt');
        //$strJson = functions::encode(str_replace("@","+",functions::request('strJson')),AUTH_KEY,2);
        $strJson = file_get_contents($imagex['dir']);
        //$strJson = functions::request('strJson');
        $mysql = functions::open_mysql();
        if (strpos($strJson, '[') !== false && strpos($strJson, ']') !== false) {
            $qrcode_arr = json_decode($strJson, true);
            $count = count($qrcode_arr);
            for ($i = 0; $i < $count; $i++) {
                $userid = $qrcode_arr[$i]['userid'];
                $money = intval($qrcode_arr[$i]['money']); //二维码金额
                $qrcode = $qrcode_arr[$i]['qrcode'];
                $mark = $qrcode_arr[$i]['mark'];
                $landid = intval($qrcode_arr[$i]['landid']); //收款账号id
                $typec = $qrcode_arr[$i]['typec'];
                //if ($typec == 2) {
                //    $money = $money / 100;
                //}
                $un_qrcode = $mysql->query("qrcode_link", "money={$money} and typec={$typec} and state=0 and mark='{$mark}'", null, null, "desc", "1");
                if (is_array($un_qrcode[0])) {
                    $mysql->update('qrcode_link', array('qrcode' => $qrcode, 'state' => 1), "money={$money} and typec={$typec} and state=0 and id={$un_qrcode[0]['id']}");
                } else {
                    $add = $mysql->insert('qrcode_link', array(
                        'userid' => $userid,
                        'land_id' => $landid,
                        'money' => $money,
                        'money_res' => $money,
                        'qrcode' => $qrcode,
                        'state' => 1,
                        'typec' => $typec,
                        'mark' => $mark,
                        'create_time' => time()
                    ));
                }
            }
        } else {

            $qrcode_arr = json_decode(stripslashes($strJson), true);
            //$userid = $qrcode_arr['userid'];

            $money = $qrcode_arr['money']; //二维码金额
            $qrcode = $qrcode_arr['qrcode'];
            $mark = $qrcode_arr['mark'];
            $landid = intval($qrcode_arr['landid']); //收款账号id
            $typec = $qrcode_arr['typec'];

            if ($typec == "alipay") {
                $typec = 1;
            } else {
                $typec = 2;
            }
            $un_qrcode = $mysql->query("qrcode_link", "money={$money} and typec={$typec} and state=4 and mark='{$mark}'", null, null, "desc", "1");
            if (is_array($un_qrcode[0])) {
                $mysql->update('qrcode_link', array('qrcode' => $qrcode, 'state' => 3, 'create_time' => time()), "money={$money} and typec={$typec} and state=4 and id={$un_qrcode[0]['id']}");
            } else {
                $add = $mysql->insert('qrcode_link', array(
                    'userid' => $un_qrcode[0]['userid'],
                    'land_id' => $landid,
                    'money' => $money,
                    'money_res' => $money,
                    'qrcode' => $qrcode,
                    'state' => 3,
                    'typec' => $typec,
                    'mark' => $mark,
                    'create_time' => time()
                ));
            }
        }
        //functions::json(200, '添加成功');
    }

    function errmessage() {
        file_put_contents("logerr.txt", json_encode($_REQUEST) . "\n", FILE_APPEND);
    }

    function GetQrcodeLink() {
        $userid = functions::request("userid");
        $landid = functions::request("landid");
        $mysql = functions::open_mysql();
        $qrcode = $mysql->query("qrcode_link", "userid={$userid} and land_id={$landid} and state=0", null, null, "desc", "1");
        $qrcode = $qrcode[0];
        if (is_array($qrcode)) {
            $mysql->update('qrcode_link', array('state' => 4), "id={$qrcode['id']}");
            functions::json("200", "准备生成二维码", $qrcode);
        } else {
            functions::json("201", "未获取到");
        }
    }

    //订单类型为json时，通过该方法获取返回的二维码链接
    function AjaxGetQrcodeLink() {
        $info = trim(functions::xss(functions::request('record')));
        $sdk = trim(functions::xss(functions::request('sdk')));
        $money = floatval(functions::request('money'));
        $mysql = functions::open_mysql();
        $land = $mysql->query("land", "sdk='{$sdk}'");
        if (!is_array($land[0])) {
            functions::json("202", "提交参数有误");
        }
        $qrcode = $mysql->query("qrcode_link", "info='{$info}' and state=3 and money={$money} and userid={$land['userid']}", null, null, "desc", "1");
        $qrcode = $qrcode[0];
        if (is_array($qrcode)) {
            $link = functions::get_Config('webCog')['site'] . "?a=servlet&b=index&c=qrcode&text=" . $qrcode['qrcode'];
            functions::json("200", "获取成功", $link);
        } else {
            functions::json("201", "未获取到二维码");
        }
    }

    //订单APP回调
    function APPRequestMent() {
        //$this->key();
        $mysql = functions::open_mysql();

        //得到提交的信息
        //$uid = intval(functions::request('landid')); //收款账号id
        //用户id
        //$userid = intval(functions::request('uid')); //用户id
        //支付类型 1、支付宝   2、微信   3、QQ
        $payc = intval(functions::request('payTypec'));
        if (empty($payc)) {
            $payc = 1;
        }
        $tradeAmount = floatval(functions::request('tradeAmount'));
        //if ($payc == 2) {
        //    $tradeAmount = $tradeAmount / 100;
        //}
        $tradeNo = functions::request('tradeNo');
        $tradeRemark = functions::request('tradeRemark');
        $tradeTime = functions::request('tradeTime');
        $payTime = functions::request('payTime');

        //通过返回的订单号以及备注确定数据库是否已经存在订单信息
        //如果存在则说明订单已经通知成功
        $query = $mysql->query("takes", "money='{$tradeAmount}' and payc={$payc} and mark='{$tradeRemark}' and orderNo='{$tradeNo}' order by id desc limit 1");

        //如果属于第三方订单平台，那么置已支付
        //检测到无订单信息，不做任何操作直接返回未通知到
        //errot_type 1:正常订单，但未能正常更新订单状态  2：订单已超时导致不能更新订单状态
        //ab_orders表为存储因备注信息不正确导致的无法查找到系统内存在的订单信息
        if (!is_array($query[0])) {
            //不存在
            $row = $mysql->query("takes", "money='{$tradeAmount}' and payc={$payc} and state=1 and mark='{$tradeRemark}' order by id desc limit 1");
            //存在对应订单信息
            if (count($row)) {
                $update = $mysql->update('takes', array('pay_time' => $payTime, 'state' => 2, 'orderNo' => $tradeNo), "id={$row[0]['id']} and mark='{$tradeRemark}'");
                if ($update > 0) {

                    //创建订单
                    $insert = $mysql->insert('orders', array(
                        'land_id' => $row[0]['land_id'],
                        'userid' => $row[0]['userid'],
                        'num' => $row[0]['num'],
                        'money' => $row[0]['money'],
                        'remark' => $row[0]['mark'],
                        'payc' => $row[0]['payc'],
                        'order_time' => $payTime,
                        'api_state' => 1,
                        'http' => '还未请求',
                        'request_time' => 0,
                        'payment' => 0,
                        'takes_id' => $row[0]['id'],
                        'orderNo' => $tradeNo,
                        'type' => $row[0]['type'],
                        'agentid' => $row[0]['agentid']
                    ));

                    if ($insert > 0) {
                        //exit('订单处理成功');
                        //更新二维码状态为空闲状态
                        if ($row['qr_type'] != 2 && $row['qr_type'] != 3) {
                            $mysql->update('qrcode_link', array('state' => 1), "mark='{$tradeRemark}'");
                        }
                        //更新收款账号额度
                        $land = $mysql->query("land", "id={$row[0]['land_id']}");
                        $land = $land[0];
                        if (floatval($land['requota']) != -1) {
                            $new_quota = floatval($land['quota']) + $tradeAmount;
                            //如果额度用完，将收款账户状态更改为"2"（数据库任务将恢复该状态）
                            if ($new_quota < floatval($land['requota'])) {
                                $mysql->update("land", array('quota' => $new_quota), "id={$land['id']}");
                            } else {
                                $mysql->update("land", array('quota' => $land['requota'], 'status' => 2), "id={$land['id']}");
                            }
                        }
                        $user = $mysql->query("users", "id={$row[0]['userid']}");
                        if ($user[0]['balance'] <= 0)
                            functions::json(11, 'please recharge');
                        //开始查询收款订单 { 得到请求列表 }
                        $order = $mysql->query("orders", "num='{$row[0]['num']}'");
                        if (!is_array($order[0]))
                            functions::json(12, 'temporary no request');
                        functions::api('reback')->request($mysql, $order[0]['id'], $user[0]);
                        //$this->APP_Reback($row[0]['land_id'], $row[0]['userid'], $row[0]['num'], $row[0]['mark']);
                        //$this->App_Arranged($row[0]['land_id'], $row[0]['userid'], $payc);
                        exit('ok');
                    } else {
                        exit('ok');
                    }
                } else {
                    //未能正常更新订单状态且未通知
                    $abnormal_orders = $mysql->query('abnormal_orders', "num='{$row[0]['num']}'");
                    if (count($abnormal_orders) == 0) {
                        $insert = $mysql->insert('abnormal_orders', array(
                            'land_id' => $row[0]['land_id'],
                            'userid' => $row[0]['userid'],
                            'num' => $row[0]['num'],
                            'money' => $row[0]['money'],
                            'remark' => $row[0]['mark'],
                            'payc' => $row[0]['payc'],
                            'order_time' => $payTime,
                            'takes_id' => $row[0]['id'],
                            'orderNo' => $tradeNo,
                            'error_type' => 1,
                            'state' => 0
                        ));
                        if ($insert > 0) {
                            exit('err_1');
                        }
                    } else {
                        exit('err_1');
                    }
                }
            } else {
                $row_3 = $mysql->query('takes', "money='{$tradeAmount}' and payc={$payc} and state=3 and mark='{$tradeRemark}'", null, null, "desc", "1");
                //查询状态已被更新为超时订单
                if (count($row_3)) {
                    $row_3 = $row_3[0];
                    $abnormal_num = $row['num'];
                    $abnormal_orders = $mysql->query('abnormal_orders', "num='{$abnormal_num}'");
                    if (count($abnormal_orders) == 0) {
                        $insert = $mysql->insert('abnormal_orders', array(
                            'land_id' => $row_3['land_id'],
                            'userid' => $row_3['userid'],
                            'num' => $row_3['num'],
                            'money' => $row_3['money'],
                            'remark' => $row_3['mark'],
                            'payc' => $row_3['payc'],
                            'order_time' => $payTime,
                            'takes_id' => $row_3['id'],
                            'orderNo' => $tradeNo,
                            'error_type' => 3,
                            'state' => 0
                        ));
                        if ($insert > 0) {
                            exit('err_2');
                        }
                    } else {
                        exit('err_2');
                    }
                } else {
                    //储存备注被更改的订单
                    $insert = $mysql->insert('ab_orders', array(
                        'money' => $tradeAmount,
                        'remark' => $tradeRemark,
                        'payc' => $payc,
                        'order_time' => $payTime,
                        'orderNo' => $tradeNo
                    ));
                    exit('ab_err');
                }
            }
        } else {
            //存在
            exit('ok');
        }

//        } else {
//            //金额 { 通过金额判断 }
//            $money = functions::request('money'); //金额
//            //转账备注
//            $remark = functions::request('remark');
//            $orderNo = functions::request('orderNo');
//
//            $query = $mysql->query('takes', "money={$money} and payc={$payc} and state=1 and userid={$userid} and mark='{$remark}'", null, null, "desc", "1"); // and payc={$payc} and state=1 and land_id={$uid} and userid={$userid}
//            //如果属于第三方订单平台，那么置已支付
//            $paytime = time();
//            //检测到无订单信息，次数不做任何操作直接返回未通知到
//            if (!is_array($query[0]) && floatval($money) > 0) {
//                functions::json(304, "通知失败，未找到对应订单信息!");
//            } else {
//                //takes
//                $update = $mysql->update('takes', array('pay_time' => $paytime, 'state' => 2, 'orderNo' => $orderNo), "id={$query[0]['id']} and mark='{$remark}'");
//                if ($update > 0) {
//                    //创建订单
//                    $insert = $mysql->insert('orders', array(
//                        'land_id' => $uid,
//                        'userid' => $userid,
//                        'num' => $query[0]['num'],
//                        'money' => $money,
//                        'remark' => $remark,
//                        'payc' => $payc,
//                        'order_time' => $paytime,
//                        'api_state' => 1,
//                        'http' => '还未请求',
//                        'request_time' => 0,
//                        'payment' => 0,
//                        'takes_id' => $query[0]['id'],
//                        'orderNo' => $orderNo,
//                        'type' => $query[0]['type'],
//                        'agentid' => $query[0]['agentid']
//                    ));
//                    if ($insert > 0) {
//                        //exit('订单处理成功');
//                        //更新二维码状态为空闲状态
//                        $mysql->update('qrcode_link', array('state' => 1), "land_id={$uid} and userid={$userid} and mark='{$remark}'");
//                        //更新收款账号额度
//                        $land = $mysql->query("land", "id={$uid} and userid={$userid}");
//                        $land = $land[0];
//                        if (floatval($land['requota']) != -1) {
//                            $new_quota = floatval($land['quota']) + $money;
//                            //如果额度用完，将收款账户状态更改为"2"（数据库任务将恢复该状态）
//                            if ($new_quota < floatval($land['requota'])) {
//                                $mysql->update("land", array('quota' => $new_quota), "id={$land['id']} and userid={$land['userid']}");
//                            } else {
//                                $mysql->update("land", array('quota' => $land['requota'], 'status' => 2), "id={$uid} and userid={$userid}");
//                            }
//                        }
//                        $this->APP_Reback($uid, $userid, $query[0]['num'], $remark);
//                        $this->App_Arranged($uid, $userid, $payc);
//                        exit('ok');
//                    } else {
//                        exit('系统错误,原因:写入订单失败!');
//                    }
//                } else {
//                    exit('系统错误,原因:更新订单数据失败');
//                }
//            }
//        }
    }

    function qrcode() {
        $text = functions::request('text');
        if (!empty($text)) {
            ob_clean();
            QRcode::png($text, false, "L", 5);
        }
    }

    function app_monitor() {
        $status = functions::request('status');
        $landid = intval(functions::request('landid'));
        $app_time = functions::request('time');
        $mysql = functions::open_mysql();
        $land = $mysql->query('land', "id={$landid}");
        if (!is_array($land[0])) {
            functions::json(301, "开启监控失败，不存在该收款账户");
        }
        $mysql->update("land", array('app_status' => $status, 'app_timec' => $app_time), "id={$landid}");
        //检查是否存在待回收二维码
        $userid = $land[0]['userid'];
        $typec = $land[0]['typec'];
        $timeTakes = intval(time() - 270);
        $mysql->update("takes", array("state" => 3), "userid={$userid} and create_time<{$timeTakes} and land_id={$landid} and state=1"); //将过期的订单pass
        //列出所有正在使用的二维码
        $qrcode = $mysql->query("qrcode_link", "userid={$userid} and money<>0 and land_id={$landid} and state=2 and typec={$typec}");
        foreach ($qrcode as $qr) {
            $mark = $qr['mark'];
            $push = $mysql->query("takes", "money={$qr['money']} and payc={$typec} and state=1 and land_id={$landid} and userid={$userid} and mark='{$mark}'");
            if (!is_array($push[0])) {
                //将二维码恢复正常
                $mysql->update("qrcode_link", array("state" => 1), "id={$qr['id']}");
            }
        }
        if (empty($status)) {
            functions::json(201, '监听关闭成功');
        } else {
            functions::json(200, '监听开启成功');
        }
    }

    function recovery_qrcode() {
        $key = functions::request('key');
        if ($key == "visa_admin") {
            $mysql = functions::open_mysql();
            //检查是否存在待回收二维码
            $time = intval(time() - 600);
            //将过期的订单pass
            $max_orverdue = $mysql->select("select id,mark,land_id,payc,qr_type,create_time from mi_takes where create_time<{$time} and state=1 and payc=303");
            if (count($max_orverdue) > 0) {
                foreach ($max_orverdue as $var) {
                    $mysql->update("takes", array("state" => 3, 'overtime' => time()), "id={$var['id']}"); //将过期的订单pass
                    $over_succ = $mysql->select("select id from mi_takes where create_time>{$var['create_time']} and land_id={$var['land_id']} and state=2 limit 1");
                    if (count($over_succ) == 0) {
                        $land = $mysql->query("land", "id={$var['land_id']}");
                        if (!empty($land[0]['otorder_limit'])) {
                            $overtimes = $land[0]['overtimes'] + 1;
                            //如果连续超时达到设定笔数，关闭该收款账号
                            if ($overtimes >= $land[0]['otorder_limit']) {
                                $mysql->update("land", array("status" => '0', 'isClosed' => '1'), "id={$var['land_id']}"); //更新收款账户状态
                            }
                            $mysql->update("land", array("overtimes" => $overtimes), "id={$var['land_id']}"); //更新过期订单笔数
                        }
                    }
                }
            }
        }
    }

    function app_monitor_state() {
        file_put_contents("app_monitor_state.txt", json_encode($_REQUEST));
        $status = functions::request('status');
        $landid = functions::request('landid');
        $app_time = functions::request('time');
        $mysql = functions::open_mysql();
        $land = $mysql->query('land', "id={$landid}");
        $land = $land[0];
        if (!is_array($land)) {
            functions::json(201, "不存在该收款账户");
        } else {
            if ($land['app_status'] == 1) {
                $mysql->update("land", array('app_timec' => $app_time), "id={$landid} and app_status=1");
                functions::json(200, '同步成功');
            } else {
                functions::json(202, "APP监控已关闭");
            }
        }
    }

    //支付宝当面付回调
    function precreate() {
        header('Content-type:text/html; Charset=utf-8');
        //$this->key();
        $trade_status = functions::request('trade_status');
        if ($trade_status == "TRADE_SUCCESS") {
            $out_trade_no = functions::request('out_trade_no');
            $trade_no = functions::request('trade_no');
            $buyer_pay_amount = functions::request('buyer_pay_amount'); //用户实际支付金额
            $notify_time = strtotime(functions::request('notify_time')); //支付宝通知时间
            $mysql = functions::open_mysql();
            $query = $mysql->query("takes", "num='{$out_trade_no}'");
            $query = $query[0];
            if (is_array($query)) {
                if ($query['state'] == 2) {
                    exit('success');
                } else {
                    $land_id = $query['land_id'];
                    $land = $mysql->query("land", "id='{$land_id}'");
                    if (is_array($land[0])) {
                        $alipayPublicKey = $land[0]['alipayPublicKey'];
                        $aliPay = new AlipayService($alipayPublicKey);
                        //验证签名
                        $result = $aliPay->rsaCheck($_REQUEST, $_REQUEST['sign_type']);
                        if ($result === true) {
                            //处理你的逻辑，例如获取订单号$_POST['out_trade_no']，订单金额$_POST['total_amount']等
                            //程序执行完后必须打印输出“success”（不包含引号）。如果商户反馈给支付宝的字符不是success这7个字符，支付宝服务器会不断重发通知，直到超过24小时22分钟。一般情况下，25小时以内完成8次通知（通知的间隔频率一般是：4m,10m,10m,1h,2h,6h,15h）；
                            $update = $mysql->update('takes', array('pay_time' => $notify_time, 'state' => 2, 'orderNo' => $trade_no), "id={$query['id']}");   //更新订单状态（无论订单是否为超时）
                            //更新成功以后写入orders订单表
                            if ($update > 0) {
                                //创建订单
                                $insert = $mysql->insert('orders', array(
                                    'land_id' => $query['land_id'],
                                    'userid' => $query['userid'],
                                    'num' => $out_trade_no,
                                    'money' => $buyer_pay_amount,
                                    'remark' => $query['mark'],
                                    'payc' => $query['payc'],
                                    'order_time' => $notify_time,
                                    'api_state' => 1,
                                    'http' => '还未请求',
                                    'request_time' => 0,
                                    'payment' => 0,
                                    'takes_id' => $query['id'],
                                    'orderNo' => $trade_no,
                                    'type' => $query['type'],
                                    'agentid' => $query['agentid'],
                                    'version' => $query['version']
                                ));

                                if ($insert > 0) {
                                    //更新收款账号额度
                                    if (floatval($land[0]['requota']) != -1) {
                                        $new_quota = floatval($land[0]['quota']) + $buyer_pay_amount;
                                        //如果额度用完，将收款账户状态更改为"2"（数据库任务将恢复该状态）
                                        if ($new_quota < floatval($land[0]['requota'])) {
                                            $mysql->update("land", array('quota' => $new_quota), "id={$land[0]['id']}");
                                        } else {
                                            $mysql->update("land", array('quota' => $land[0]['requota'], 'status' => 2), "id={$land[0]['id']}");
                                        }
                                    }
                                    $user = $mysql->query("users", "id={$query['userid']}");
                                    if ($user[0]['balance'] <= 0)
                                        functions::json(11, 'please recharge');
                                    //开始查询收款订单 { 得到请求列表 }
                                    $order = $mysql->query("orders", "num='{$query['num']}'");
                                    if (!is_array($order[0]))
                                        functions::json(12, 'temporary no request');
                                    functions::api('reback')->request($mysql, $order[0]['id'], $user[0]);
                                    echo 'success';
                                    exit();
                                } else {
                                    exit('success');
                                }
                            } else {
                                exit('fail');
                            }
                        } else {
                            exit('fail');
                        }
                    } else {
                        exit('fail');
                    }
                }
            } else {
                exit('fail');
            }
        } else {
            exit('fail');
        }
    }

}
