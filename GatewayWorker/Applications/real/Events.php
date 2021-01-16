<?php

/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;
use Workerman\Connection\AsyncTcpConnection;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events {

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id) {
        sleep(1);
        //var_export($client_id);
        //echo "\n";
        $session[$client_id] = Gateway::getSession($client_id);
        if (!empty($session[$client_id])) {
            //if (count(Gateway::getClientIdByUid($session['userid'])) > 0) {
            //    Gateway::closeClient(Gateway::getClientIdByUid($session['userid'])[0]);
            //}
            Gateway::bindUid($client_id, $session[$client_id]['userid']);
            //var_export(Gateway::getClientIdByUid($session['userid']));
        }

        //Gateway::sendToAll($session['userid']." 进入");
        // 向当前client_id发送数据 
        //Gateway::sendToClient($client_id, "Hello $client_id\r\n");
        // 向所有人发送
        //Gateway::sendToAll("$client_id login\r\n");
    }

    /**
     * 当客户端发来消息时触发
     * @param int $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($client_id, $message) {
        $db = Db::instance('db');
        // debug
        //echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']} client_id:$client_id session:" . json_encode($_SESSION) . " onMessage:" . $message . "\n";
        $path = 'log/' . date('Ymd') . '/';
        $filename = 'AppMessage.log';
        if (!is_dir($path)) {
            $flag = mkdir($path, 0777, true);
        }
        file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-请求参数-' . "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']} client_id:$client_id session:" . json_encode($_SESSION) . " onMessage:" . $message . '-----' . PHP_EOL, FILE_APPEND);
        if (empty($_SESSION['userid'])) {
            Gateway::closeClient($client_id);
        }
        //var_export(Gateway::getAllClientSessions());echo "\n";
        //var_export(Gateway::isOnline($client_id));echo "\n";
        $message_data = json_decode($message, true);
        var_export($message);
        echo "\n";
        //var_dump(Gateway::getAllClientInfo());
        if (strpos($message, 'landId') !== false) {
            //$aliUserID = $message_data['userId'];
            //$_SESSION['aliUserID'] = $aliUserID;
            $type = $message_data['type'];
            $landid = $message_data['landId'];

            if (!empty($landid)) {
                if ($type == "303") {
                    if (empty($_SESSION['WY_landid']))
                        $_SESSION['WY_landid'] = $landid;
                }
                $_SESSION['type'] = $type;
            }
        } else {
            switch ($message_data['type']) {

                // 客户端回应服务端的心跳
                //case 'pong':
                //    return;
                // 客户端初始化 message格式: {type:init, task_id:xx}
                //case 'init':
                //	$task_id = $message_data['task_id'];
                //Gateway::bindUid($client_id,$task_id);
                //Gateway::sendToUid($task_id,'{"type":"login_success"}');
                //return;
                case 'qrcodec':

                    //if(empty($groupid))
                    //{
                    if (empty($message_data['data']['state'])) {
                        $task_id = $message_data['data']['userid'];
                        $qr_landid = $message_data['data']['land_id'];
                        $typec = $message_data['data']['typec'];

                        $qrcode = $message_data['data']['qrcode'];
                        $client_id = Gateway::getClientIdByUid($task_id);

                        for ($i = 0; $i < count($client_id); $i++) {
                            $s_client = Gateway::getSession($client_id[$i]);
                            if ($s_client['type'] == $typec) {
                                if ($typec == "303") {
                                    if ($s_client['WY_landid'] == $qr_landid) {
                                        Gateway::sendToClient($client_id[$i], $message);
                                        break;
                                    }
                                }
                            }
                        }
                    } else {
                        $qrcode_array = $message_data['data'];
                        $remark = $qrcode_array['mark'];
                        $state = $qrcode_array['state'];
                        $qrcode = $qrcode_array['qrcode'];
                        $bank = $qrcode_array['bank'];
                        $h5_link = $qrcode_array['h5_link'];
                        $post_url = $qrcode_array['url'];
                        $typec = $qrcode_array['typec'];
                        if ($qrcode != "" && $qrcode != null) {
                            $bank_name = $qrcode_array['bank_name'];
                            if ($bank == "CMB" || $bank == "CCB") {
                                if (!empty($h5_link)) {
                                    $row_count = $db->query("UPDATE `sk_order` SET ma_qrcode_status = '{$state}',`h5_link` = '{$h5_link}' WHERE remark= '{$remark}'");
                                } else {
                                    $row_count = $db->query("UPDATE `sk_order` SET qrcode='{$qrcode}',post_url='{$post_url}' WHERE remark= '{$remark}' and ma_qrcode_status=0");
                                }
                            } else {
                                $row_count = $db->query("UPDATE `sk_order` SET ma_qrcode_status= '{$state}',qrcode='{$qrcode}',post_url='{$post_url}' WHERE remark= '{$remark}' and state=0");
                            }

                            if ($row_count) {
                                echo "update success!\n";
                            } else {
                                echo "no qrcode link to update\n";
                            }
                        }
                    }
                    break;
                case 'YeOrderList':
                    $order = json_decode($message_data['Json'], TRUE);
                    if ($order['tradeNo'] == "" || $order['tradeNo'] == null) {
                        continue;
                    }
                    $ye['tradeAmount'] = floatval(str_replace(',', '', $order['tradeAmount']));
                    $ye['tradeNo'] = $order['tradeNo'];
                    $ye['tradeRemark'] = "充值";
                    $ye['tradeTime'] = $order['tradeTime'] / 1000;
                    $ye['landid'] = $order['landid'];
                    $ye['payTime'] = time();
                    $ye['payTypec'] = 26;
                    if ($ye['payTime'] - $ye['tradeTime'] > 600) {
                        continue;
                    }
                    // 与远程task服务建立异步链接，ip为远程task服务的ip，如果是本机就是127.0.0.1，如果是集群就是lvs的ip
                    $task_connection = new AsyncTcpConnection('Text://127.0.0.1:13000');
                    // 任务及参数数据
                    $task_data = array(
                        'function' => 'send_orders',
                        'data' => $ye,
                    );
                    // 发送数据
                    $task_connection->send(json_encode($task_data));
                    // 异步获得结果
                    $task_connection->onMessage = function($task_connection, $task_result)use($client_id) {
                        // 结果
                        //echo($task_result)."\n";
                        $notify = json_decode($task_result, true);
                        if ($notify['state'] != "error") {
                            $AlipayOrder['tradeNo'] = $notify['tradeNo'];
                            $AlipayOrder['type'] = "orderList";
                            //Gateway::sendToUid($notify['userid'], json_encode($AlipayOrder));
                            echo $notify['tradeNo'] . " orders sendToUid message\n";
                            file_put_contents("bank2alipaySuccessOrderList.txt", json_encode($AlipayOrder['tradeNo']) . "\n", FILE_APPEND);
                            Gateway::sendToUid($_SESSION['userid'], json_encode($AlipayOrder));
                        }
                        // 获得结果后记得关闭异步链接
                        $task_connection->close();
                        // 通知对应的websocket客户端任务完成
                        //$client_id->send('task complete');
                    };
                    // 执行异步链接
                    $task_connection->connect();

                    $success_orders['tradeNo'] = $ye['tradeNo'];
                    $success_orders['type'] = "orderList";
                    Gateway::sendToUid($_SESSION['userid'], json_encode($success_orders));

                    file_put_contents("bank2alipayOrderList.txt", json_encode($message_data) . "\n", FILE_APPEND);
                    break;

                case 'sync_bank';
                    break;
            }
        }
    }

    /**
     * 当用户断开连接时触发
     * @param int $client_id 连接id
     */
    public static function onClose($client_id) {
        // 向所有人发送 
        var_export($client_id . " logout");
        echo "\n";
        //GateWay::sendToAll("$client_id logout\r\n");
    }

    static function get_between($input, $start, $end) {
        $substr = substr($input, strlen($start) + strpos($input, $start), (strlen($input) - strpos($input, $end)) * (-1));
        return $substr;
    }

    /**
     * 取毫秒级时间戳，默认返回普通秒级时间戳 time() 及 3 位长度毫秒字符串
     *
     * @param int  $msec_length 毫秒长度，默认 3
     * @param int  $random_length 添加随机数长度，默认 0
     * @param bool $dot 随机是否存入小数点，默认 false
     * @param int  $delay 是否延迟，传入延迟秒数，默认 0
     * @return string
     */
    static function msectime($msec_length = 3, $random_length = 0, $dot = false, $delay = 0) {
        list($msec, $sec) = explode(' ', microtime());
        $rand = $random_length > 0 ?
                number_format(
                        mt_rand(1, (int) str_repeat('9', $random_length)) * (float) ('0.' . str_repeat('0', $random_length - 1) . '1'), $random_length, '.', '') : '';
        $msectime = sprintf('%.0f', (floatval($msec) + floatval($sec) + $delay) * pow(10, $msec_length));
        return $dot ? $msectime . '.' . substr($rand, 2) : $msectime . substr($rand, 2);
    }

    static function curlhttp($url, $headers, $data = '', $method) {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            if ($data != '') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            }
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        //curl_close($curl); // 关闭CURL会话
        $tmpInfo = self::curl($curl, "https://pceuser.netpay.cmbchina.com/pc-card-epcc/PrePayC.do", array(
                    'User-Agent: Mozilla/5.0 (Linux; Android 5.0; SM-G900P Build/LRX21T) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Mobile Safari/537.36',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
                    'Accept-Language: zh-CN,zh;q=0.9',
                    'Accept-Encoding: gzip, deflate, br',
                    'Referer:https://netpay.cmbchina.com/netpayment/PC_EpccPay.do',
                    'Origin: https://netpay.cmbchina.com',
                    'Host: pceuser.netpay.cmbchina.com',
                    'Cookie:Cookie_NetPay_Main_CMB=CallerId:CardPwd_Pay_Entry',
                    'Content-Type:application/x-www-form-urlencoded'
                        ), $data, "POST");
        $tmpInfo = mb_convert_encoding($tmpInfo, 'utf-8', 'GBK,UTF-8,ASCII');
        return $tmpInfo; // 返回数据
    }

    static function curl($curl, $url, $headers, $data = '', $method) {
        //$curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            if ($data != '') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            }
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip, deflate, br');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据
    }

}
