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
        if (empty($user['alipay_withdraw']) || $user['alipay_withdraw'] == 0) {
            $poundage['alipay_withdraw'] = $cost['alipay_withdraw'];
        } else {
            $poundage['alipay_withdraw'] = $user['alipay_withdraw'];
        }
        if (empty($user['wechat_withdraw']) || $user['wechat_withdraw'] == 0) {
            $poundage['wechat_withdraw'] = $cost['wechat_withdraw'];
        } else {
            $poundage['wechat_withdraw'] = $user['wechat_withdraw'];
        }
        return $poundage;
    }

    //订单请求(取消使用)
    function request_bak($mysql, $orderId, $payment, $user, $agent_payment) {
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
        $poundage = 0; //初始化手续费
        $agent_poundage = 0;
        //扣除手续费
        //判断订单手续费是否已经扣除
        //计算手续费咯~
        $poundage = $order[0]['money'] * $payment / 100;

        if (empty($order[0]['payment_state'])) {
            if ($payment != 0) {
                //开始从用户的账户中扣掉手续费
                $userMoney = $mysql->query("users", "id={$order[0]['userid']}");
                $tempMoney = $userMoney[0]['balance'] - $poundage;
                if ($tempMoney <= 0)
                    return false;
                $mysql->update("users", array("balance" => $tempMoney), "id={$order[0]['userid']}");
                $mysql->update("orders", array("payment_state" => 1), "id={$orderId}");
            }
            if ($agent_payment != 0) {
                $agent_poundage = $order[0]['money'] * ($payment - $agent_payment) / 100;
                //代理增加提成费用
                $agentMoney = $mysql->query("agent", "id={$user['agentid']}");
                $tempMoney = $agentMoney[0]['balance'] + $agent_poundage;
                if ($tempMoney <= 0)
                    return false;
                $mysql->update("agent", array("balance" => $tempMoney), "id={$user['agentid']}");
            }
            else {
                $agent_poundage = 0;
            }
        }

        //获取回掉地址
        $error_times = $order[0]['error_times'];
        $rebak = json_decode(functions::encode($land[0]['reback'], AUTH_PE, 2));
        $url = $takes[0]['notify_url']; //回掉接口地址
        $method = $rebak->method; //请求方式
        $data = $rebak->data; //请求数据
        $header = $rebak->header; //header头
        $cookie = $rebak->cookie; //cookies
        if ($takes[0]['money'] - $takes[0]['money_index'] >= 0 && $takes[0]['money'] - $takes[0]['money_index'] < 1) {
            if ($takes[0]['money'] != $takes[0]['money_index']) {
                $takes[0]['money'] = $takes[0]['money_index'];
            }
            $data = str_replace("[money]", $takes[0]['money'], $data); //金额替换
            $data = str_replace("[amount]", $takes[0]['money_index'], $data); //发起金额
            $data = str_replace("[order]", $takes[0]['num'], $data); //订单号
            $data = str_replace("[record]", $takes[0]['info'], $data); //附加信息
            $data = str_replace("[remark]", trim($order[0]['remark']), $data); //备注
            $data = str_replace('[attach]', $takes[0]['attach'], $data);
            //生成签名
            $sign_index = md5(Number_format($takes[0]['money_index'], 2, '.', '') . trim($takes[0]['info']) . $user['keyid']);
            $data = str_replace("[sign]", $sign_index, $data); //验证签名
            $header = explode(PHP_EOL, trim($header, PHP_EOL)); //header头 array数组
            if ($method == 'get') {
                $row = $this->curl($url . '?' . $data, null, null, $header, $cookie);
            }
            if ($method == 'post') {
                $row = $this->curl($url, $data, null, $header, $cookie);
            }
            //判断是否通知成功
            $request_state = functions::xss($row);
            if ($request_state == "ok") {
                $api_state = 2;
            } else {
                $api_state = 1;
            }
            //置订单已经请求api
            $mysql->update("orders", array(
                'http' => $request_state,
                'request_time' => time(),
                'api_state' => $api_state,
                'payment' => $poundage, //用户手续费
                'agent_payment' => $agent_poundage, //代理提成
                'profit' => $poundage - $agent_poundage, //平台利润由用户手续费减去代理提成
                'error_times' => $error_times
                    ), "id={$order[0]['id']}");
            return true;
        } else {
            return false;
        }
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
        $payment = $order[0]['money'] * ($poundage / 100);
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
                //开始从用户的账户中扣掉手续费
                $tempMoney = $user['balance'] - $payment;
                if ($tempMoney < 0)
                    return false;
                $mysql->update("users", array("balance" => $tempMoney), "id={$order[0]['userid']}");
                $mysql->update("orders", array("payment_state" => 1), "id={$orderId}");
            }
            if ($agent_payment != 0) {
                //代理增加提成费用
                $agentMoney = $mysql->query("agent", "id={$order[0]['agentid']}");
                $tempagentMoney = $agentMoney[0]['balance'] + $agent_payment;
                if ($tempMoney < 0)
                    return false;
                $mysql->update("agent", array("balance" => $tempagentMoney), "id={$order[0]['agentid']}");
            }
        }


        //获取回掉地址
        $rebak = json_decode(functions::encode($land[0]['reback'], AUTH_PE, 2));
        $url = $takes[0]['notify_url']; //回掉接口地址
        $method = $rebak->method; //请求方式
        $data = $rebak->data; //请求数据
        $header = $rebak->header; //header头
        $cookie = $rebak->cookie; //cookies

        if ($takes[0]['qr_type'] != 5) {
            if ($takes[0]['money'] - $takes[0]['money_index'] <= 0 && $takes[0]['money'] - $takes[0]['money_index'] > 1) {
                if ($takes[0]['money'] != $takes[0]['money_index']) {
                    $takes[0]['money'] = $takes[0]['money_index'];
                }
            } else {
                return false;
            }
        } else {
            if ($takes[0]['money_index'] - $takes[0]['money'] > 0.01 || $takes[0]['money_index'] - $takes[0]['money'] < 0.2) {
                return false;
            }
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

        $header = explode(PHP_EOL, trim($header, PHP_EOL)); //header头 array数组
        if ($method == 'get') {
            $row = $this->curl($url . '?' . $data, null, null, $header, $cookie);
        }
        if ($method == 'post') {
            $row = $this->curl($url, $data, null, $header, $cookie);
        }
        //判断是否通知成功
        $request_state = functions::xss($row);
        if ($request_state == "ok") {
            $api_state = 2;
        } else {
            $api_state = 1;
        }
        //置订单已经请求api
        $result = $mysql->update("orders", array(
            'http' => $request_state,
            'request_time' => time(),
            'api_state' => $api_state,
            'payment' => $payment, //用户手续费
            'agent_payment' => $agent_payment, //代理提成
            'profit' => $payment - $agent_payment, //平台利润由用户手续费减去代理提成
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
                header('Location:' . $refer);
            } else if ($take['state'] == 3) {
                functions::json(6003, '订单已超时');
            } else {
                functions::json(6001, '订单未支付');
            }
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
