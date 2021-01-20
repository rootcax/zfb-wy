<?php

class index {

    //加载生码页面
    function create_qrcode() {
        $path = 'log/' . date('Ymd') . '/';
        $filename = 'test.log';
        if (!is_dir($path)) {
            $flag = mkdir($path, 0777, true);
        }
        //file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-请求参数-' . json_encode($_REQUEST) . '-----' . PHP_EOL, FILE_APPEND);
        $sn = functions::request('order_no'); //订单号
        $step = intval(functions::request("step"));
        $mysql = functions::open_mysql();
        $state = 5;
        if ($step == 1) {
            $state = 0;
        }
        //$order = $mysql->select("select A.*,B.qrcode,B.bank,B.post_url,B.h5_link,C.bank_token,C.bank_name from mi_takes as A INNER JOIN mi_qrcode_link as B on A.qrcode_id = B.id left join mi_bank as C on A.bank_code=C.bank_code where A.num='{$num}' and B.state={$state} limit 1");
        $order = $mysql->select("select log.*,b.bank_name,b.bank_token,b.bank_code from sk_order log left join cnf_bank b on log.ma_bank_id=b.id where order_sn='{$sn}' and ma_qrcode_status={$state}");
        if (empty($order)) {
            functions::json(1001, '订单已被销毁');
        }
        $order = $order[0];
        if ($step == 1) {
            if ($order['ma_qrcode_status'] == 0) {
                /*
                $qrcode_array['state'] = 0;
                $qrcode_array['userid'] = $order['muid'];
                $qrcode_array['land_id'] = $order['ma_id'];
                $qrcode_array['money'] = floatval($order['money']);
                $qrcode_array['money_res'] = $order['real_money'];
                $qrcode_array['qrcode'] = "";
                $qrcode_array['mark'] = $sn;
                $qrcode_array['typec'] = $order['ptype'];
                $qrcode_array['info'] = $sn;
                $qrcode_array['device'] = $order['device'];
                $qrcode_array['bank'] = $order['bank_token'];
                $qrcode_array['bank_name'] = $order['bank_name'];
                $qrcode_array['create_time'] = $order['create_time'];
                $client = stream_socket_client('tcp://127.0.0.1:8806', $errno, $errmsg, 1);
                if (!$client)
                    functions::json(-1, "发送失败");
                // 推送的数据，包含uid字段，表示是给这个uid推送
                $data['data'] = $qrcode_array;
                $data['type'] = "qrcodec";
                // 发送数据，注意8991端口是Text协议的端口，Text协议需要在数据末尾加上换行符
                fwrite($client, json_encode($data) . "\n");
                $mysql->update("sk_order", ["ma_qrcode_status" => 1], "id={$order['id']}");
                */
            }else if ($order['ma_qrcode_status'] == 1) {
                functions::json(1001, '请勿重复提交');
            }
            functions::import_var('create_qrcode', ['data' => $order]);

        } else if ($step == 2) {
            if ($order['ma_qrcode'] != "" && $order['post_url'] != "") {
                if ($order['bank_code'] == "CCB" && functions::isMobile()) {
                    functions::import_var('goCCBPay', array('data' => $order));
                } else {
                    functions::import_var('goPay', array('data' => $order));
                }
            } else {
                functions::json(1001, '获取二维码信息失败');
            }
        }
    }

    function getPayOrder() {
        // 指定允许其他域名访问
        header('Access-Control-Allow-Origin:*');
// 响应类型
        header('Access-Control-Allow-Methods:POST');
// 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $num = functions::request('orderId'); //订单号
        if (empty($num))
            functions::json(-1, '订单号错误');
        $mysql = functions::open_mysql();
        $order = $mysql->query('sk_order', "order_sn='{$num}'");
        $order = $order[0];
        $money = floatval($order['money']);
        if (!is_array($order))
            functions::json(1001, '订单已被销毁');
        if (empty($order['id'])) {
            functions::json(-1, '获取失败');
        }
        $mysql->update('sk_order', array("ma_qrcode_status" => "5"), "id={$order['id']} and ma_qrcode_status=3");
        functions::json(200, '获取成功', $order);
        /*
        $qrcode = $mysql->query('sk_order', "id={$order['id']} and money={$money} and state=3");
        $qrcode = $qrcode[0];
        if (!is_array($qrcode)) {
            //functions::json(1001, '获取二维码失败！');
        } else {
            $mysql->update('sk_order', array("state" => "5"), "id={$order['id']} and state=3");
            functions::json(200, '获取成功', $order);
        }*/
    }

    /**
     * 判断是否微信内置浏览器访问
     * @return bool
     */
    private function isWxClient() {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }

    /**
     * 判断是否支付宝内置浏览器访问
     * @return bool
     */
    private function isAliClient() {
        $isAli = strpos($_SERVER['HTTP_USER_AGENT'], 'Alipay') !== false;
        //$isAli_1 = empty($_SERVER['HTTP_SPDY_H5_UUID']) !== true;
        $result = $isAli; // && $isAli_1;
        return $result;
    }

    private function isAndroid() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'Adr') !== false) {
            return true;
        }
        return false;
    }

    private function isIOS() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false) {
            return true;
        }
        return false;
    }

}
