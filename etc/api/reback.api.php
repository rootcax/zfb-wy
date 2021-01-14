<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//订单请求
class reback {

    //获取自己的手续费
    function getCost($userId) {
        //查询到订单列表,得到请求接口的手续费
        $cost = functions::get_Config('registerCog');
        $mysql = functions::open_mysql();

        $user = $mysql->query('users', "id={$userId}");
        if (!is_array($user[0]))
            return false;
        $user = $user[0];
        if (empty($user['bank2alipay_withdraw']) || $user['bank2alipay_withdraw'] == 0) {
            $poundage['bank2alipay_withdraw'] = floatval($cost['bank2alipay_withdraw']);
        } else {
            $poundage['bank2alipay_withdraw'] = floatval($user['bank2alipay_withdraw']);
        }
        return $poundage;
    }

    //订单请求
    function request($mysql, $orderId, $user) {
        $order = $mysql->query("orders", "id={$orderId}");
        if (!is_array($order[0]))
            return false;
        //查询收款账号
        $land = $mysql->query("land", "id={$order[0]['land_id']}");
        if (!is_array($land[0]))
            return false;
        //查询支付订单
        $takes = $mysql->query("takes", "id={$order[0]['takes_id']}");
        if (!is_array($takes[0]))
            return false;

        //扣除手续费
        //判断订单手续费是否已经扣除
        //计算手续费咯~
        //$poundage = $order[0]['money'] * $payment / 100;
        $poundage = functions::get_orderFee($order[0]['userid'], $order[0]['payc']);
        $fee = $order[0]['money'] * ($poundage / 100);      //手续费
        $payment = $order[0]['money'] - $fee;               //商户应得
        if (!empty($order[0]['agentid'])) {
            $agent_poundage = functions::get_agentFee($order[0]['agentid'], $order[0]['payc']);
            $agent_payment = $order[0]['money'] * (($poundage - $agent_poundage) / 100);
        } else {
            $agent_poundage = 0;
            $agent_payment = 0;
        }
        if (empty($order[0]['payment_state'])) {
            //取用户费率
            if ($payment != 0) {
                //开始为用户的账户中增加手续费
                $tempMoney = $user['balance'] + $payment;
                $up_userBalance = $mysql->select("update mi_users set balance = @newbalance:=balance+{$payment} where id={$order[0]['userid']}");
                $mysql->update("orders", array("payment_state" => 1), "id={$orderId}");
            }
            if ($agent_payment != 0) {
                $mysql->select("update mi_agent set balance = balance+{$agent_payment} where id={$order[0]['agentid']}");
            }
        }


        //获取回掉地址
        $rebak = json_decode(functions::encode($land[0]['reback'], AUTH_PE, 2));
        $url = $takes[0]['notify_url']; //回掉接口地址
        $method = $rebak->method; //请求方式
        $data = $rebak->data; //请求数据
        $header = $rebak->header; //header头
        $cookie = $rebak->cookie; //cookies

        if ($takes[0]['version'] == 1) {
            $array['accFlag'] = "1";
            $array['accName'] = $land[0]['username'];
            $array['money'] = $takes[0]['money_index']; //实际提交金额
            $array['amount'] = $takes[0]['money'];  //实际支付金额
            $array['createTime'] = date("yyyyMMddHHmmss", $takes[0]['create_time']);
            $array['currentTime'] = date("yyyyMMddHHmmss", time());
            $array['merchant'] = $takes[0]['userid'];
            $array['orderNo'] = $takes[0]['info'];
            $array['payFlag'] = "2";
            $array['payTime'] = $takes[0]['pay_time'];
            if ($takes[0]['payc'] == 26) {
                $array['payType'] = "bank2alipay";
            }
            $mySign = "accFlag=" . $array['accFlag'] . "&accName=" . $array['accName'] . "&amount=" . $array['amount'] . "&createTime=" . $array['createTime'] . "&currentTime=" . $array['currentTime'] . "&merchant=" . $array['merchant'] . "&orderNo=" . $array['orderNo'] . "&payFlag=" . $array['payFlag'] . "&payTime=" . $array['payTime'] . "&payType=" . $array['payType'];
            if ($takes[0]['attach'] != "") {
                $array['remark'] = $takes[0]['attach'];
                $mySign = $mySign . "&remark=" . $array['remark'];
            }
            $array['systemNo'] = $takes[0]['num'];
            $mySign = $mySign . "&systemNo=" . $array['systemNo'];
            $mySign = $mySign . "#" . $user['keyid'];
            $array['sign'] = md5($mySign);
            $data = http_build_query($array);
        } else {
            if (bccomp(abs(floatval($takes[0]['money_index'] - $takes[0]['money'])), 1) > 0) {
                return false;
            }
            $data = str_replace("[money]", $takes[0]['money'], $data); //金额替换
            $data = str_replace("[amount]", $takes[0]['money_index'], $data); //发起金额
            $data = str_replace("[order]", $takes[0]['num'], $data); //订单号
            $data = str_replace("[record]", $takes[0]['info'], $data); //附加信息
            $data = str_replace("[remark]", trim($order[0]['remark']), $data); //备注
            $data = str_replace('[attach]', $takes[0]['attach'], $data);
            //生成签名
            $sign_index = md5(Number_format($takes[0]['money'], 2, '.', '') . trim($takes[0]['info']) . $user['keyid']);
            $data = str_replace("[sign]", $sign_index, $data); //验证签名
        }

        $header = explode(PHP_EOL, trim($header, PHP_EOL)); //header头 array数组
        if ($method == 'get') {
            $row = $this->curl($url . '?' . $data, null, null, $header, $cookie);
        }
        if ($method == 'post') {
            $row = $this->curl($url, $data, null, $header, $cookie);
        }
        //判断是否通知成功
        $request_state = functions::xss($row);
        if ($request_state == "ok" || $request_state == "success") {
            $api_state = 2;
            $error_times = 0;
        } else {
            $api_state = 1;
            $error_times = $order[0]['error_times'] + 1;
        }
        file_put_contents('requestResult_' . date('Ymd') . '.log', date('Y-m-d H:i:s') . '-订单号：' . $takes[0]['num'] . ' 下发链接地址：' . $url . '?' . $data . ' 下发返回内容：' . $request_state . ' -----' . PHP_EOL, FILE_APPEND);
        //置订单已经请求api
        $result = $mysql->update("orders", array(
            'http' => $request_state,
            'request_time' => time(),
            'api_state' => $api_state,
            'error_times' => $error_times, //通知错误次数
            'payment' => $payment, //用户所得
            'agent_payment' => $agent_payment, //代理提成
            'profit' => $fee - $agent_payment, //平台利润由用户手续费减去代理提成
                ), "id={$order[0]['id']}");
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    //订单前台跳转
    function refer($mysql, $take, $user) {
        $orderinfo = $mysql->query("orders", "num={$take['num']}");
        if (!is_array($orderinfo[0]))
            functions::json(3001, '订单信息有误');
        //查询收款账号
        $land = $mysql->query("land", "id={$orderinfo[0]['land_id']}");
        if (!is_array($land[0]))
            functions::json(3000, '订单信息有误');
        $url = $take['refer']; //回掉接口地址
        if (!empty($url)) {
            if ($take['state'] == 2) {
                if ($take['version'] == 1) {
                    $array['accFlag'] = "1";
                    $array['accName'] = $land[0]['username'];
                    $array['money'] = $take['money_index']; //实际提交金额
                    $array['amount'] = $take['money'];  //实际支付金额
                    $array['createTime'] = date("yyyyMMddHHmmss", $take['create_time']);
                    $array['currentTime'] = date("yyyyMMddHHmmss", time());
                    $array['merchant'] = $take['userid'];
                    $array['orderNo'] = $take['info'];
                    $array['payFlag'] = "2";
                    $array['payTime'] = $take['pay_time'];
                    if ($take['payc'] == 26) {
                        $array['payType'] = "bank2alipay";
                    }
                    $mySign = "accFlag=" . $array['accFlag'] . "&accName=" . $array['accName'] . "&amount=" . $array['amount'] . "&createTime=" . $array['createTime'] . "&currentTime=" . $array['currentTime'] . "&merchant=" . $array['merchant'] . "&orderNo=" . $array['orderNo'] . "&payFlag=" . $array['payFlag'] . "&payTime=" . $array['payTime'] . "&payType=" . $array['payType'];
                    if ($take['attach'] != "") {
                        $array['remark'] = $take['attach'];
                        $mySign = $mySign . "&remark=" . $array['remark'];
                    }
                    $array['systemNo'] = $take['num'];
                    $mySign = $mySign . "&systemNo=" . $array['systemNo'];
                    $Parma = $mySign;
                    $mySign = $mySign . "#" . $user['keyid'];
                    $array['sign'] = md5($mySign);
                    $Parma = $Parma . "&sign=" . $array['sign'];
                    //$data = http_build_query($array);
                    $refer = $url . '?' . $Parma;
                } else {
                    $money = Number_format($take['money'], 2, '.', ''); //金额
                    $amount = Number_format($take['money_index'], 2, '.', ''); //发起金额
                    $key = $user['keyid']; //keyid
                    $order = $take['num']; //订单号
                    $record = $take['info']; //附加信息
                    $remark = $take['remark']; //备注
                    $attach = $take['attach']; //自定义内容
                    $sign = md5($amount . trim($record) . $key); //验证签名
                    $Parma = "money=" . $money . "&amount=" . $amount . "&order=" . $order . "&record=" . $record . "&remark=" . $remark . "&attach=" . $attach . "&sign=" . $sign;
                    $refer = $url . '?' . $Parma;
                }

                header('Location:' . $refer);
            } else if ($take['state'] == 3) {
                functions::json(6003, '订单已超时');
            } else {
                functions::json(6001, '订单未支付');
            }
        }
    }

    //订单请求
    function df_request($mysql, $order) {
        if (empty($order)) {
            return false;
        }
        //获取回掉地址
        $url = $order['notify_url']; //回调接口地址
        $PublicKey = $order['publicKey'];
        $array['version'] = "1.0";
        $array['memberCode'] = $order['memberCode'];
        $array['orderId'] = $order['orderId']; //商户订单号
        $array['amount'] = $order['amount'] * 100;  //支付金额（分）
        $array['apply_time'] = $order['create_time'];  //请求时间
        $array['deal_time'] = $order['update_time'];
        $array['order_seq_id'] = $order['order_seq_id']; //平台订单号
        $array['fee'] = $order['fee'];
        $array['status'] = $order['status'];
        $rsaService = new SignService($PublicKey);
        //验证签名
        $sign = $rsaService->generateSign($array, "RSA2");
        $array['sign'] = $sign;
        $data = http_build_query($array);

        $header = explode(PHP_EOL, trim($header, PHP_EOL)); //header头 array数组
        $request_state = $this->curl($url, $data, null);
        //判断是否通知成功
        $request_state = functions::xss($request_state);
        if ($request_state == "ok" || $request_state == "success") {
            $callback_status = 3;
            $callback_error_times = 0;
        } else {
            $callback_status = 2;
            $callback_error_times = $order['callback_error_times'] + 1;
        }
        file_put_contents('DfOrderrequestResult_' . date('Ymd') . '.log', date('Y-m-d H:i:s') . '-订单号：' . $order['order_seq_id'] . ' 下发链接地址：' . $url . '?' . $data . ' 下发返回内容：' . $request_state . ' -----' . PHP_EOL, FILE_APPEND);
        //置订单已经请求api
        $result = $mysql->update("dforders", array(
            'callback_contents' => $request_state,
            'request_time' => time(),
            'callback_status' => $callback_status,
            'callback_error_times' => $callback_error_times, //通知错误次数
                ), "id={$order[0]['id']}");
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    //curl
    function curl($url, $data = null, $referer = null, $header = array(), $cookie = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

}
