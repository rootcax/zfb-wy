<?php
//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//服务端（暂未启动）
class personal{
    
    //连接账户
    function Connect(){
        $mysql = functions::open_mysql();
        $land = $this->Login($mysql);
        functions::json(200, $land[0]['username']);
    }
    
    //账户心跳
    function Wander(){
        $mysql = functions::open_mysql();
        $land = $this->Login($mysql);
        $mysql->update("land", array("timec"=>time()), "id={$land[0]['id']}");
        functions::json(200, '防掉线更新成功');
    }
    
    //将所有过期的订单置过期，以及置空闲二维码{私有}
    function Arranged(){
        ini_set('max_execution_time','0');
        $mysql = functions::open_mysql();
        $land = $this->Login($mysql);
        
        //先将所有过期订单列出来
        $timeTakes = intval(time()-270);
        $mysql->update("takes", array("state"=>3)," create_time<{$timeTakes} and land_id={$land[0]['id']} and userid={$land[0]['userid']} and state=1");//将过期的订单pass
        //列出所有正在使用的二维码
        $qrcode = $mysql->query("qrcode","userid={$land[0]['userid']} and money<>0 and land_id={$land[0]['id']} and state=2 and typec={$land[0]['typec']}");
        foreach ($qrcode as $qr){
            $push = $mysql->query("takes","money={$qr['money']} and payc={$land[0]['typec']} and state=1");
            if (!is_array($push[0])){
                //将二维码恢复正常
                $mysql->update("qrcode", array("state"=>1),"id={$qr['id']}");
                
            }
        }
        functions::json(200, '任务处理完成,共计:' . count($qrcode));
    }
    
    //自动回调
    function Reback(){
        ini_set('max_execution_time','0');
        $mysql = functions::open_mysql();
        $land = $this->Login($mysql);
        //查询该账号是否开启了监控
        if ($land[0]['onback'] != 1) functions::json(10, '回调错误:没有启动监控,请到网站后台启动监控');
        //如果已经开启了监控，查询账户余额
        $user = $mysql->query("users","id={$land[0]['userid']}");
        if ($user[0]['balance'] <= 0)  functions::json(11, '回调错误:账户余额为0.00元,请及时充值,否则无法及时回调');
        //开始查询收款订单 { 得到请求列表 }
        $orders = $mysql->query("orders","land_id={$land[0]['id']} and userid={$land[0]['userid']} and api_state=1");
        if (!is_array($orders[0])) functions::json(12, '回调提醒:暂时没有需要回调的订单');
        //查询到订单列表,得到请求接口的手续费
        $cost = explode(",", functions::getc('Poundage'));
        $cost_default = explode("=>", $cost[0]);
        //初始化默认手续费
        $poundage = $cost_default[1];
        //开始列出手续费规则
        for ($i=1;$i<count($cost);$i++){
            $cost_list = explode("=>", $cost[$i]);
            if ($cost_list[0] == intval($land[0]['userid'])){
                //如果查找到该ID的规则，立即重置手续费
                $poundage = $cost_list[1];
            }
        }
        //开始回调
        foreach ($orders as $order){
            functions::api('reback')->request($mysql,$order['id'],$poundage,$user[0]);
        }
        functions::json(200, '回调提醒:已检测到有订单,全部回调完成');
    }
    
    //设置账号
    function SetLogin(){
        $mysql = functions::open_mysql();
        $land = $this->Login($mysql);
        $json = json_decode(stripslashes(functions::request('data')),true);
        $ext_mysql = array();
        foreach ($json as $ext){
            $ext_mysql[$ext['key']] = $ext['value'];
        }
        $up = $mysql->update('land', $ext_mysql,"id={$land[0]['id']}");
        functions::json(200, 'login:更新完成');
    }
    
    //提交订单
    function RequestMent(){
        $mysql = functions::open_mysql();
        $land = $this->Login($mysql);
        //得到提交的信息
        $uid = $land[0]['id'];//收款账号id
        //用户id
        $userid = $land[0]['userid'];//用户id
        //金额 { 通过金额判断 }
        $money = floatval(functions::request('money'));//金额
        //支付类型
        $payc = $land[0]['typec'];
        //转账备注
        $remark = functions::xss(functions::request('remark'));
        //检测是否属于订单的金额 :: 为了防止判断出错,所以这里加强判断,用户id关联收款id
        $query = $mysql->query('takes',"money={$money} and payc={$payc} and state=1 and land_id={$uid} and userid={$userid}");
        if (!is_array($query[0])) functions::json(-1, '订单提交:当前交易不属于第三方,已忽略该订单');
        //如果属于第三方订单平台，那么置已支付
        $paytime = time();
        //takes
        $update = $mysql->update('takes', array('pay_time'=>$paytime,'state'=>2),"id={$query[0]['id']}");
        if ($update > 0){
            //创建订单
            $insert = $mysql->insert('orders', array(
                'land_id'=>$uid,
                'userid'=>$userid,
                'num'=>$query[0]['num'],
                'money'=>$money,
                'remark'=>$remark,
                'payc'=>$payc,
                'order_time'=>$paytime,
                'api_state'=>1,
                'http'=>'还未请求',
                'request_time'=>0,
                'payment'=>0,
                'takes_id'=>$query[0]['id']
            ));
            if ($insert > 0){
                functions::json(200, '订单提交:订单处理成功,订单号:' . $query[0]['num']);
            }else{
                functions::json(-1, '订单提交:系统错误,订单状态更新失败');
            }
        }else{
            functions::json(200, '订单提交:系统错误,订单数据获取失败');
        }
    }
    
    //验证
    private function Login($mysql){
        //收款账号sdk
        $sdk = functions::xss(functions::request('sdk'));
        //电脑id
        $WID = functions::xss(functions::request('wid'));
        if (empty($sdk) || empty($WID)) functions::json(-1, '请输入sdk');
        $land = $mysql->query("land","sdk='{$sdk}' and equipment='{$WID}'");
        if (!is_array($land[0])) functions::json(-1, '连接时发生错误,电脑ID不符合,如过更换电脑或服务器请重新下载挂机软件');
        //检测类型
        $type = intval(functions::request('type'));
        if ($type != $land[0]['typec']) functions::json(-1, '该SDK与当前软件类型不符合,无法初始化');
        return $land;
    }
    
    
}