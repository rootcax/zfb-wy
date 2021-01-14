<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//用户sdk创建订单以及获取订单
class index {

    //初始化api接口
    function run() {
        //echo functions::api('sms')->send_abnormal(13824324946,"prometed");exit;
        $getc = file_get_contents(_etc . 'dynamic_config.php');
        $getcx = json_decode(functions::encode($getc, AUTH_PE, 2), true);
        $getcx['url'] = 'http://3w.mqzf.top/';
        echo functions::encode(json_encode($getcx), AUTH_PE);
        exit;

        echo 'api run ok!';
    }

    //创建订单
    function payment() {
        $typeJson = trim(functions::request('type'));
        //附加信息
        $info = trim(functions::xss(functions::request('record')));
        if (empty($info))
            $this->msgJson($typeJson, 1000, 'record参数错误');
        //充值金额
        $money_index = floatval(functions::request('money'));
        if ($money_index <= 0)
            $this->msgJson($typeJson, 1001, 'money参数错误');
        //sdk
        $sdk = functions::xss(functions::request('sdk'));
        if (empty($sdk))
            $this->msgJson($typeJson, 1002, 'sdk参数有误');
        //refer来源
        $refer = urlencode(functions::request('refer'));
        if (empty($refer))
            $this->msgJson($typeJson, 1003, '订单错误,来源不明');
        $sign = trim(functions::request('sign'));
        //验证签名
        $sign_index = md5(floatval($money_index) . trim($info) . $sdk);
        if ($sign != $sign_index)
            $this->msgJson($typeJson, 1004, '签名错误');
        //notify_url 异步通知地址
        $notify_url = functions::xss(functions::request('notify_url'));
        if (empty($notify_url))
            $this->msgJson($typeJson, 1009, '异步通知地址错误');
        //查询sdk
        $mysql = functions::open_mysql();
        $sdk_query = $mysql->query("land", "sdk='{$sdk}'");
        $sdk_query = $sdk_query[0]; //数据转换一下，免得写0
        if (!is_array($sdk_query))
            $this->msgJson($typeJson, 1005, 'sdk连接失败');
        //开始分析订单和支付类型
        $msgInfo = null; //通道提示信息
        //检测是否有足够的二维码能够用来创建订单
        $qrcode_query = $mysql->query("qrcode", "land_id={$sdk_query['id']} and state=1 and money_res={$money_index}");
        $qrcode_query = $qrcode_query[0]; //数据转换
        //这里是进入到通用二维码通道
        if (!is_array($qrcode_query)) {
            //如果没有可用对应金额的二维码，则查找通用二维码
            $qrcode_query = $mysql->query("qrcode", "land_id={$sdk_query['id']} and state=1 and money=0");
            $qrcode_query = $qrcode_query[0]; //数据转换
            if (!is_array($qrcode_query))
                $this->msgJson($typeJson, 1006, '当前支付通道繁忙,请稍后再试');
            //如果找到通用二维码则开始随机创建0.01 - 0.99之间的随机数字,并且不与已存在的二维码互相排斥
            $money = functions::drive('money')->garden($mysql, $sdk_query['id'], $money);
            if (!$money)
                exit('<script>location.href="";</script>'); //自动刷新并执行
            $msgInfo = '您好,本次交易请按照二维码指定的金额转账,金额与二维码不符会充值失败哦~';
        }else {
            $money = $qrcode_query['money'];
        }

        //二维码拉取成功，开始创建订单
        $order_num = date("YmdHis") . time() . mt_rand(10000, 99999); //订单号 29 位
        //订单创建时间
        $order_time = time();
        //创建订单到数据库
        $insert_order = $mysql->insert('takes', array(
            'num' => $order_num,
            'info' => $info,
            'create_time' => $order_time,
            'pay_time' => 0,
            'money' => $money,
            'money_index' => $money_index,
            'payc' => $sdk_query['typec'],
            'state' => 1,
            'land_id' => $sdk_query['id'],
            'userid' => $sdk_query['userid'],
            'notify_url' => $notify_url
        ));
        //创建失败
        if (!$insert_order)
            $this->msgJson($typeJson, 1007, '订单创建失败,请重试');
        //更改二维码状态
        if ($qrcode_query['money'] != 0) {
            $update_qrcode = $mysql->update('qrcode', array('state' => 2), "id={$qrcode_query['id']}");
            //二维码状态更新成功
            if (!$update_qrcode)
                $this->msgJson($typeJson, 1008, '订单创建出错,请重试');
        }

        $wap = '';
        if (functions::isMobile()) {
            $wap = 'wap_';
        }
        //分析支付宝/微信/QQ通道
        if ($sdk_query['typec'] == 1)
            $temp = $wap . 'alipay';
        if ($sdk_query['typec'] == 2)
            $temp = $wap . 'wechat';
        if ($sdk_query['typec'] == 3)
            $temp = $wap . 'tenpay';

        if ($typeJson != 'json') {
            //拉取二维码,渲染界面
            functions::import_var($temp, array(
                'username' => $sdk_query['username'],
                'money' => $money,
                'order_num' => $order_num,
                'order_time' => $order_time,
                'image' => _pub . 'cache/images/' . $qrcode_query['qrcode'],
                'refer' => urldecode($refer),
                'msgInfo' => $msgInfo
            ));
        } else {
            //返回json
            functions::json(200, 'success', array(
                'sdk_name' => $sdk_query['username'], //商家帐户名
                'money' => $money, //交易金额
                'amount' => $money_index,
                'record' => $info, //提交信息
                'order_num' => $order_num, //订单号
                'order_time' => $order_time, //订单号创建时间
                'image' => _pub . 'cache/images/' . $qrcode_query['qrcode'], //二维码地址
                'refer' => urldecode($refer), //成功跳转地址
                'msgInfo' => $msgInfo //支付提示信息
            ));
        }
    }

    //拉取订单处理信息
    function get() {
        $num = functions::request('num'); //订单号
        if (empty($num))
            functions::json(-1, '订单号错误');
        $mysql = functions::open_mysql();
        $order = $mysql->select("SELECT * FROM mi_takes where num='{$num}'");
        $order = $order[0];
        if (!is_array($order)) {
            functions::json(1001, '订单已被销毁');
            //mysql->update('qrcode',array('state'=>1,"land_id={}"))
        }

        //检测订单是否超时
        if($order['qr_type']==6)
        {
            $time = 600;
        }
        else
        {
            $time = 270;
        }
//        if (intval($order['create_time']) < (time() - $time)) {
//            //更新订单状态
//            $mysql->update('takes', array('state' => 3), "id={$order['id']}");
//            $mark = $order['mark'];
//            $qrcode = $mysql->query("qrcode_link","mark='{$mark}'");
//            if (is_array($qrcode[0])) {
//                if ($order['payc'] != "3") {
//                    $mysql->update('qrcode_link', array('state' => 1), "mark='{$mark}'");
//                } else {
//                    $mysql->delete('qrcode_link', "mark='{$mark}'");
//                }
//            }
//            functions::json(1002, '订单已经超时',$order['num']);
//        }
        if ($order['state'] == 3)
            functions::json(1002, '订单已经超时',$order['num']);
        if ($order['state'] == 2)
            functions::json(200, '支付成功',$order['num']);
        if ($order['state'] == 1)
            functions::json(1003, '订单未支付',$order['num']);
    }

    function getQrcode() {
        $num = functions::request('num'); //订单号
        if (empty($num))
            functions::json(-1, '订单号错误');
        $mysql = functions::open_mysql();
        $order = $mysql->query('takes', "num='{$num}'");
        $order = $order[0];
        if (!is_array($order))
            functions::json(1001, '订单已被销毁');
        //检测订单是否超时
        $money = $order['money'];
        $mark = $order['mark'];

        $qrcode = $mysql->query('qrcode_link', "userid={$order['userid']} and land_id={$order['land_id']} and money='{$money}' and mark='{$mark}' and state=3");
        $qrcode = $qrcode[0];
        if (!is_array($qrcode)) {
            //functions::json(1001, '获取二维码失败！');
        } else {
            if ($qrcode['qrcode'] != "") {
                $mysql->update('qrcode_link', array("state" => "2"), "userid={$order['userid']} and land_id={$order['land_id']} and money='{$money}' and state=3 and id={$qrcode['id']} and mark='{$mark}'");
                functions::json(200, '获取成功', $qrcode);
            }
        }
    }

    //下载二维码
    function qrcode_down() {
        functions::downloadFile(functions::request("image"));
    }

    //json转普通
    private function msgJson($type, $code, $msg) {
        if ($type == 'json') {
            functions::json($code, $msg);
        } else {
            exit($msg);
        }
    }

}
