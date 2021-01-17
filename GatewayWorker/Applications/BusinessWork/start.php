<?php

use \GatewayWorker\Lib\Gateway;
use Workerman\Worker;
use \GatewayWorker\Lib\Db;

//require_once __DIR__ . '/Workerman/Autoloader.php';
require_once __DIR__ . '/../../vendor/autoload.php';
// task worker，使用Text协议
$task_worker = new Worker('Text://0.0.0.0:13000');
// task进程数可以根据需要多开一些
$task_worker->count = 50;
$task_worker->name = 'TaskWorker';
$task_worker->onMessage = function($connection, $task_data) {
    $path = 'log/' . date('Ymd') . '/';
    $filename = 'AppReback.log';
    if (!is_dir($path)) {
        $flag = mkdir($path, 0777, true);
    }
    // 假设发来的是json数据
    $task_data = json_decode($task_data, true);
    // 根据task_data处理相应的任务逻辑.... 得到结果，这里省略....
    if ($task_data['function'] == "send_orders") {
        $payc = $task_data['data']['payTypec'];
        if ($payc == "303") {
            $tradeNo = $task_data['data']['tradeNo'];
            $tradeAmount = $task_data['data']['tradeAmount'];
            $tradeRemark = $task_data['data']['tradeRemark'];
            if (strpos($tradeRemark, "=") !== false) {
                $tradeRemark = strstr($tradeRemark, "=", TRUE);
            }
            $landid = $task_data['data']['landid'];
            $payTime = $task_data['data']['payTime'];
            file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-接收到APP返回内容，准备查询系统订单-' . $tradeNo . '-----' . $tradeAmount . '-----'. $tradeRemark . PHP_EOL, FILE_APPEND);
            $state = send_orders($payc, $tradeNo, $tradeAmount, $tradeRemark, $payTime, $landid);
            $task_result = [];
            if ($state['state'] == "ok") {
                $task_result['state'] = "ok";
            } else if ($state['state'] == "abnormal") {
                $task_result['state'] = "abnormal";
            } else {
                $task_result['state'] = "error";
            }
            $task_result['payc'] = $payc;
            $task_result['tradeNo'] = $tradeNo;
            $task_result['tradeAmount'] = $tradeAmount;
            $task_result['tradeRemark'] = $tradeRemark;
            $task_result['userid'] = $state['userid'];
            $connection->send(json_encode($task_result));
        }
    }
};

function send_orders($payc, $tradeNo, $tradeAmount, $tradeRemark, $payTime, $landid) {
    $path = 'log/' . date('Ymd') . '/';
    $filename = 'AppReback.log';
    if (!is_dir($path)) {
        $flag = mkdir($path, 0777, true);
    }
    $db = Db::instance('db');
    //如果属于第三方订单平台，那么置已支付
    //检测到无订单信息，不做任何操作直接返回未通知到
    //errot_type 1:正常订单，但未能正常更新订单状态  2：订单已超时导致不能更新订单状态
    //ab_orders表为存储因备注信息不正确导致的无法查找到系统内存在的订单信息
    $query = $db->query("select * from sk_order where remark='{$tradeNo}' order by id desc limit 1");
    if (empty(count($query))) {
        if ($payc == "303") {
            $row = $db->query("select * from sk_order where money={$tradeAmount} and ptype={$payc} and pay_status<3 and ma_id={$landid} order by id desc limit 1");
        }
        file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-第三方订单号：' . $tradeNo . ' 查询结果-' . json_encode($row) . '-----' . PHP_EOL, FILE_APPEND);
        if (count($row)) {
            $row = $row[0];

            if (1) {
                $update = $db->query("UPDATE sk_order set pay_status=9,pay_time='{$payTime}',remark='{$tradeNo}' WHERE id = {$row['id']}");   //修改订单状态
                file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-第三方订单号： ' . $tradeNo . ' 系统订单号： ' . $row['num'] . ' 订单金额：' . $row['money'] . ' 写入数据库成功-----' . PHP_EOL, FILE_APPEND);
                if ($update > 0 && $tradeNo != "" && $tradeNo != null) {
                    $file = 'AppUserChargeHistory.log';
                    file_put_contents($path . $file, date('Y-m-d H:i:s') . '-系统订单号：' . $tradeNo . ' 订单金额：' . $row['money'] .  '-----' . PHP_EOL, FILE_APPEND);
                    //创建订单
//                    if ($row['payc'] == 26) {
//                        $qrcode_info = $row['info'];
//                        $row_count = $db->query("delete from `mi_qrcode_link` WHERE info='{$qrcode_info}'");
//                    }
                    //更新收款账号额度
                    $land = $db->select('*')->from("mi_land")->where("id={$row['land_id']}")->query();
                    $land = $land[0];
                    if (floatval($land['requota']) != -1) {
                        $new_quota = floatval($land['quota']) + $tradeAmount;
                        //如果额度用完，将收款账户状态更改为"2"（数据库任务将恢复该状态）
                        if ($new_quota < floatval($land['requota'])) {
                            $db->query("update mi_land set quota = quota + {$tradeAmount} where id={$land['id']}");
                        } else {
                            $db->query("update mi_land set quota = quota + {$tradeAmount}, status = 2 where id={$land['id']}");
                        }
                    }
                    if (!empty($land['otorder_limit'])) {
                        $db->query("UPDATE `mi_land` SET overtimes=0  WHERE id={$land['id']}");
                    }
                    //开始从用户的账户中扣掉手续费
                    $user = $db->select('*')->from("mi_users")->where("id={$row['userid']}")->query();
                    if ($user[0]['balance'] <= 0) {
                        return false;
                    } else {
                        $tempMoney = $user[0]['balance'] - $payment;
                        if ($tempMoney < 0)
                            return false;
                        $up_userBalance = $db->query("update mi_users set balance = @newbalance:=balance+{$payment} where id={$row['userid']}");
                        $new_balance = $db->query("select @newbalance as newbalance");
                        $tempMoney = $new_balance[0]['newbalance'];
                        //$db->update("mi_users")->cols(array("balance" => $tempMoney))->where("id={$row['userid']}")->query();                     //修改用户余额
                        if ($up_userBalance > 0) {
                            file_put_contents($path . $file, date('Y-m-d H:i:s') . '-系统订单：' . $tradeNo . ' 订单金额：' . $row['money'] . ' 支付成功，系统将用户：' . $user[0]['phone'] . ' -余额：' . $user[0]['balance'] . ' 变动为：' . $tempMoney . '-----' . PHP_EOL, FILE_APPEND);
                            if ($agent_payment != 0) {
                                //代理增加提成费用
                                $agent = $db->select('*')->from("mi_agent")->where("id={$row['agentid']}")->query();
                                //$tempagentMoney = $agent[0]['balance'] + $agent_payment;
                                //如果用户余额小于0
                                if ($tempMoney < 0)
                                    return false;
                                //$db->update("mi_agent")->cols(array("balance" => $tempagentMoney))->where("id={$agent[0]['id']}")->query();         //修改代理余额
                                $db->query("update mi_agent set balance = @newbalance:=balance+{$agent_payment} where id={$agent[0]['id']}");
                                $new_balance = $db->query("select @newbalance as newbalance");
                                $tempagentMoney = $new_balance[0]['newbalance'];
                                file_put_contents($path . $file, date('Y-m-d H:i:s') . '-系统订单：' . $tradeNo . ' 订单金额：' . $row['money'] . ' 支付成功，系统将代理：' . $agent[0]['phone'] . ' -余额：' . $agent[0]['balance'] . ' 变动为：' . $tempagentMoney . '-----' . PHP_EOL, FILE_APPEND);
                            }
                            $db->update("mi_orders")->cols(array("payment_state" => 1))->where("takes_id={$row['id']}")->query();                     //修改成功订单是否计算费率
                            //开始查询收款订单 { 得到请求列表 }
                            $order = $db->select('*')->from("mi_orders")->where("land_id={$row['land_id']} and userid={$row['userid']} and api_state=1 and num='{$row['num']}' and remark='{$row['mark']}'")->query();
                            if (!is_array($order[0])) {
                                echo ('temporary no request');
                            } else {
                                $reback = app_request_order($order[0]['id'], $user[0], $db);
                                if ($reback) {
                                    $order_request['state'] = "ok";
                                    $order_request['tradeNo'] = $tradeNo;
                                    $order_request['payc'] = $payc;
                                    $order_request['userid'] = $row['userid'];
                                    Db::close("db");
                                    return $order_request;
                                }
                            }
                        }
                    }
                } //else {
                // exit('系统错误,原因:写入订单失败!');
                //}
            }
        } else {
            //订单通知延迟导致的异常订单
            $row = $db->query("select * from mi_takes where money='{$tradeAmount}' and payc={$payc} and state=3 and mark='{$tradeRemark}' and land_id={$landid} order by id desc limit 1");
            if (count($row)) {
                $row = $row[0];
                $num = $row['num'];
                $abnormal_orders = $db->query("select * from mi_abnormal_orders where num='{$num}'");
                if (count($abnormal_orders) == 0) {
                    $insert = $db->insert('mi_abnormal_orders')->cols(array(
                                'land_id' => $row['land_id'],
                                'userid' => $row['userid'],
                                'num' => $num,
                                'money' => $row['money'],
                                'remark' => $row['mark'],
                                'payc' => $row['payc'],
                                'order_time' => $payTime,
                                'takes_id' => $row['id'],
                                'orderNo' => $tradeNo,
                                'error_type' => 1,
                                'state' => 0))->query();
                }
                $order_request['state'] = "abnormal";
                $order_request['tradeNo'] = $tradeNo;
                $order_request['payc'] = $payc;
                $order_request['userid'] = $row['userid'];
            } else {
                $aorders = $db->query("select * from mi_ab_orders where orderNo='{$tradeNo}'");
                if (empty($aorders)) {
                    //储存备注被更改的订单
                    $insert = $db->insert('mi_ab_orders')->cols(array(
                                'money' => $tradeAmount,
                                'remark' => $tradeRemark,
                                'payc' => $payc,
                                'order_time' => $payTime,
                                'orderNo' => $tradeNo
                            ))->query();
                    $order_request['state'] = "ab_normal";
                    $order_request['tradeNo'] = $tradeNo;
                    $order_request['payc'] = $payc;
                    $order_request['userid'] = "";
                }
            }
            Db::close("db");
            return $order_request;
        }
    }
    file_put_contents($path . $filename, date('Y-m-d H:i:s') . '====================================系统订单：' . $tradeNo . ' 请求结束========================================================================' . PHP_EOL, FILE_APPEND);
}

function APP_Reback($landid, $userid, $num, $remark, $db) {
    ini_set('max_execution_time', '0');
    //查询该账号是否开启了监控（临时检测监控开启）
    //$land = $db->select('*')->from("mi_land")->where("id={$landid} and app_status=1")->query();
    //if (!is_array($land[0]))
    //    echo ('please open the monitor');
    //如果已经开启了监控，查询账户余额
    $user = $db->select('*')->from("sys_user")->where("id={$userid}")->query();
    if ($user[0]['balance'] <= 0) {
        echo ('please recharge');
    } else {
        //开始查询收款订单 { 得到请求列表 }
        $order = $db->select('*')->from("sk_order")->where("ma_id={$landid} and uid={$userid} and order_sn='{$num}' and remark='{$remark}'")->query();
        if (!is_array($order[0])) {
            echo ('temporary no request');
        } else {
            $requerst_order_state = app_request_order($order[0]['id'], $user[0], $db);
            if ($requerst_order_state) {
                return true;
            } else {
                return false;
            }
        }
    }

    //Db::close('db');
    //functions::json(200, '回调完成');
}

//订单请求
function app_request_order($orderId, $user, $db) {
    $path = 'log/' . date('Ymd') . '/';
    $filename = 'AppRequestOrder.log';
    if (!is_dir($path)) {
        $flag = mkdir($path, 0777, true);
    }
    $order = $db->select('*')->from("mi_orders")->where("id={$orderId} and userid={$user['id']}")->query();
    if (!is_array($order[0]))
        return false;
    //查询收款账号
    $land = $db->select('*')->from("mi_land")->where("id={$order[0]['land_id']} and userid={$user['id']}")->query();
    if (!is_array($land[0]))
        return false;
    //查询支付订单
    $takes = $db->select('*')->from("mi_takes")->where("id={$order[0]['takes_id']} and userid={$user['id']}")->query();
    if (!is_array($takes[0]))
        return false;
    //订单费率已经计算以及用户余额、代理余额已经修改
    if ($order[0]['payment_state'] == 1) {
        //获取回掉地址
        $reback = json_decode(encode($land[0]['reback'], "f5624bac9df1db7b9d6c8fabdb77706d", 2));
        $url = $takes[0]['notify_url']; //回掉接口地址
        $method = $reback->method; //请求方式
        $data = $reback->data; //请求数据
        $header = $reback->header; //header头
        $cookie = $reback->cookie; //cookies
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
        file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-系统订单号：' . $takes[0]['num'] . ' 商户订单号：' . $takes[0]['info'] . ' 请求链接-' . $url . '?' . $data . '-----' . PHP_EOL, FILE_APPEND);
        if ($method == 'get') {
            $row = curl($url . '?' . $data, null, null, $header, $cookie);
        }
        if ($method == 'post') {
            $row = curl($url, $data, null, $header, $cookie);
        }
        file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-系统订单号：' . $takes[0]['num'] . ' 商户订单号：' . $takes[0]['info'] . ' 请求结果-' . $row . '-----' . PHP_EOL, FILE_APPEND);
        //判断是否通知成功
        $request_state = xss($row);
        if ($request_state == "ok" || $request_state == "success") {
            $api_state = 2;
        } else {
            $api_state = 1;
        }
        //置订单已经请求api
        $result = $db->update("mi_orders")->cols(array(
                    'http' => $request_state,
                    'request_time' => time(),
                    'api_state' => $api_state,
                ))->where("id={$order[0]['id']}")->query();
        if ($result) {
            return true;
        } else {
            return false;
        }
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

//xss攻击过滤
function xss($val) {
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); $i++) {
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); //
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // 
    }

    $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
    $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
    $ra = array_merge($ra1, $ra2);

    $found = true; // 
    while ($found == true) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); $i++) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); $j++) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // 
            $val = preg_replace($pattern, $replacement, $val); // 
            if ($val_before == $val) {
                $found = false;
            }
        }
    }
    return $val;
}

//RC4加密
function RC4($pwd, $data) {
    $key[] = "";
    $box[] = "";
    $cipher = "";
    $pwd_length = strlen($pwd);
    $data_length = strlen($data);

    for ($i = 0; $i < 256; $i++) {
        $key[$i] = ord($pwd[$i % $pwd_length]);
        $box[$i] = $i;
    }

    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $key[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }

    for ($a = $j = $i = 0; $i < $data_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;

        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;

        $k = $box[(($box[$a] + $box[$j]) % 256)];
        $cipher .= chr(ord($data[$i]) ^ $k);
    }

    return $cipher;
}

function get_orderFee($userid, $typec, $db) {
    $cost = get_Config('registerCog', $db);

    $user = $db->select('*')->from('mi_users')->where("id={$userid}")->query();
    if (!is_array($user[0]))
        return false;
    $user = $user[0];
    if ($typec == 26) {
        if (empty($user["bank2alipay_withdraw"]) || $user['bank2alipay_withdraw'] == 0) {
            $poundage = $cost['bank2alipay_withdraw'];
        } else {
            $poundage = $user['bank2alipay_withdraw'];
        }
    }
    return $poundage;
}

function get_agentFee($agentid, $typec, $db) {
    $cost = get_Config('agentCog', $db);

    $agent = $db->select('*')->from("mi_agent")->where("id='{$agentid}'")->query();
    if (!is_array($agent[0]))
        return false;
    $agent = $agent[0];
    if ($typec == 26) {
        if (empty($agent["bank2alipay_withdraw"]) || $agent['bank2alipay_withdraw'] == 0) {
            $poundage = $cost['bank2alipay_withdraw'];
        } else {
            $poundage = $agent['bank2alipay_withdraw'];
        }
    }
    return $poundage;
}

function get_Config($name, $db) {
    $query = $db->select('*')->from("mi_config")->where("name='{$name}'")->query();
    if (is_array($query[0])) {
        $data = json_decode($query[0]['value'], true);
        return $data;
    } else {
        return false;
    }
}

// RC4加密和解密  action 1加密 2解密
function encode($data, $pwd, $action = 1) {
    if ($action == 1) {
        return base64_encode(RC4($pwd, $data));
    }
    if ($action == 2) {
        return iconv("UTF-8", "GB2312//IGNORE", RC4($pwd, base64_decode($data)));
    }
}
