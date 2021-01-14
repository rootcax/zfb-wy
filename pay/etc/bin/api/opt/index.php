<?php

//用户sdk创建订单以及获取订单
class index {

    //初始化api接口
    function run() {
        //echo functions::api('sms')->send_abnormal(13824324946,"prometed");exit;
        $getc = file_get_contents(_etc . 'dynamic_config.php');
        $getcx = json_decode(functions::encode($getc, AUTH_PE, 2), true);
        $getcx['url'] = $webCog['site'];
        //echo functions::encode(json_encode($getcx), AUTH_PE);
        //exit;
        echo 'api run ok!';
    }

    //创建订单
    function payment() {
        $path = 'log/' . date('Ymd') . '/';
        $filename = 'payRequest.log';
        if (!is_dir($path)) {
            $flag = mkdir($path, 0777, true);
        }
        file_put_contents($path . $filename, date('Y-m-d H:i:s') . '-请求参数-' . json_encode($_REQUEST) . '-----' . PHP_EOL, FILE_APPEND);
        $version = functions::request("version");
        if ($version == "" || $version == null) {
            $version = 0;
        }
        $typeJson = trim(functions::request('type'));
        $webCog = functions::get_Config('webCog');
        $max_money = floatval($webCog['max_money']);
        $min_money = floatval($webCog['min_money']);
        //sdk version=1
        if ($version == 1) {
            $merchant = trim(functions::xss(functions::request('merchant')));
            if (empty($merchant))
                $this->msgJson($typeJson, 1000, '商户号有误');
            $payType = trim(functions::xss(functions::request('payType')));
            if ($payType == "" || $payType == null)
                $this->msgJson($typeJson, 1000, '订单类型有误');
            $mysql = functions::open_mysql();
            //根据用户ID判断用户是否存在，并获取用户key
            $user = $mysql->query("users", "id='{$merchant}'");
            if (!is_array($user[0])) {
                $this->msgJson($typeJson, 1013, '用户不存在！');
            }
            $user = $user[0];
            $key = $user['keyid'];
            $sign = functions::xss(functions::request('sign'));
            $ip = functions::get_client_ip();
            $currentTime = functions::xss(functions::request('currentTime'));
            $info = trim(functions::xss(functions::request('orderNo')));
            $bank = trim(functions::xss(functions::request('bank')));
            //检查订单是否有重复
            $takes = $mysql->query("takes", "info='{$info}'");
            if (is_array($takes[0])) {
                $this->msgJson($typeJson, 1013, '订单号重复');
            }
            $addData['money_index'] = number_format(floatval(functions::request('amount')), 2, '.', '');
            if (!empty($max_money)) {
                if ($addData['money_index'] > $max_money)
                    $this->msgJson($typeJson, 1015, '单笔订单金额不允许超出' . $max_money);
            }
            if (!empty($min_money)) {
                if ($addData['money_index'] < $min_money)
                    $this->msgJson($typeJson, 1016, '单笔订单金额不允许低于' . $min_money);
            }
            $refer = functions::xss(functions::request('returnUrl'));
            $notify_url = functions::xss(functions::request('notifyUrl'));
            $attach = functions::xss(functions::request('remark'));
            $sign_index = "amount=" . $addData['money_index'] . "&bank=" . $bank . "&currentTime=" . $currentTime . "&merchant=" . $merchant . "&notifyUrl=" . $notify_url . "&orderNo=" . $info . "&payType=" . $payType;
            if ($attach != "") {
                $sign_index = $sign_index . "&remark=" . $attach;
            }
            if ($refer != "") {
                $sign_index = $sign_index . "&returnUrl=" . $refer;
            }
            $sign_index = $sign_index . "#" . $key;
            if ($sign != md5($sign_index))
                $this->msgJson($typeJson, 1004, '签名错误');
            $order_time = strtotime($currentTime);
            if ($payType == "bank2alipay") {
                $typec = 26;
            }
            $sdk = trim(functions::request('sdk'));
            if ($sdk == null || $sdk == "") {
                $sdk_query = $mysql->query("land", "typec={$typec} and userid={$merchant} and status=1");
            } else {
                $sdk_query = $mysql->query("land", "sdk='{$sdk}'");
            }
            //取收款账户
            $count_land = count($sdk_query);
            if ($count_land > 0) {
                $id = mt_rand(0, $count_land - 1);
                $sdk_query = $sdk_query[$id]; //先取出一个收款账户
            } else {
                $this->msgJson($typeJson, 2011, '当前渠道暂无可用通道，请联系管理后重新提交订单');
            }
        } else {
            //订单类型 0：正常订单 1：用户余额充值订单
            $type = intval(functions::request('order_type'));
            if (empty($type)) {
                $type = 0;
            }
            //附加信息
            $info = trim(functions::xss(functions::request('record')));
            if (empty($info))
                $this->msgJson($typeJson, 1000, 'record参数错误');

            $mysql = functions::open_mysql();
            if (!$type) {
                //检查订单是否有重复
                $takes = $mysql->query("takes", "info='{$info}'");
                if (is_array($takes[0])) {
                    $this->msgJson($typeJson, 1013, '订单号重复');
                }
            }

            //充值金额
            $addData['money_index'] = floatval(functions::request('money'));
            if ($addData['money_index'] <= 0)
                $this->msgJson($typeJson, 1001, 'money参数错误');
            //判断提交金额是否超出平台设置限额

            if (!empty($max_money)) {
                if ($addData['money_index'] > $max_money)
                    $this->msgJson($typeJson, 1015, '单笔订单金额不允许超出' . $max_money);
            }
            if (!empty($min_money)) {
                if ($addData['money_index'] < $min_money)
                    $this->msgJson($typeJson, 1016, '单笔订单金额不允许低于' . $min_money);
            }
            //sdk
            $sdk = functions::xss(functions::request('sdk'));
            if (empty($sdk))
                $this->msgJson($typeJson, 1002, 'sdk参数有误');
            //refer来源
            $refer = functions::xss(functions::request('refer'));
            if (empty($refer))
                $this->msgJson($typeJson, 1003, '订单错误,来源不明');
            $sign = trim(functions::request('sign'));
            //验证签名
            $sign_index = md5(Number_format($addData['money_index'], 2, '.', '') . trim($info) . $sdk);
            if ($sign != $sign_index)
                $this->msgJson($typeJson, 1004, '签名错误');
            //notify_url 异步通知地址
            $notify_url = functions::xss(functions::request('notify_url'));
            $attach = functions::xss(functions::request('attach'));
            if (empty($notify_url))
                $this->msgJson($typeJson, 1009, '异步通知地址错误');
            //查询sdk
            $sdk_query = $mysql->query("land", "sdk='{$sdk}'");
            if (!is_array($sdk_query[0]))
                $this->msgJson($typeJson, 1005, 'sdk连接失败');
            $sdk_query = $sdk_query[0]; //数据转换一下，免得写0
            $typec = intval($sdk_query['typec']);
            $user = $mysql->query("users", "id={$sdk_query['userid']}");
            $user = $user[0];
        }
        if ($typec == 26) {
            $pollmode = $user['bank2alipay_polling'];
        }
        $addData['money_index'] = floatval($addData['money_index']);
        $start_time = strtotime(date('Y-m-d 00:00:00', time()));
        $end_time = strtotime(date('Y-m-d 23:59:59', time()));
        if (functions::isMobile()) {
            $addData['device'] = "mobile";
        } else {
            $addData['device'] = "PC";
        }

        //开始分析订单和支付类型
        $msgInfo = null; //通道提示信息
        //判断当前收款账户是否开启轮询
        //$mark = date("md") . functions::generateRandomNum(8);

        $mark = functions::generateRandomString(6);
        if ($sdk_query['polling'] == "0" && ($sdk_query['status'] == "0" || $sdk_query['status'] == "2")) {
            $this->msgJson($typeJson, 2010, '当前通道已经关闭，请联系管理后重新提交订单');
        }
        $addData['num'] = date("YmdHis") . time() . mt_rand(10000, 99999); //订单号 29 位
        //固码开启轮询
        if ($sdk_query['polling'] == "1") {
            if ($pollmode == "1") {
                //判断用户账户下所有开启启轮询且状态为开启状态的的收款账户
                $all_polling = $mysql->select("select a.*,b.create_time,b.pay_time,b.overtime,b.state,c.total from mi_land as a left join (select sum(IF(state<>3,money,0)) as total,land_id from mi_takes where userid={$user['id']} and create_time>={$start_time} and create_time<={$end_time} GROUP by land_id) as c on a.id=c.land_id left join (SELECT T.* FROM (select * from `mi_takes` where money={$addData['money_index']} and userid={$user['id']} and create_time>={$start_time} and create_time<={$end_time} order by id desc limit 999999) T group by T.land_id ORDER BY T.id desc) as b on a.id = b.land_id where a.typec={$typec} and a.status=1 and a.userid={$user['id']} and a.qr_typec=1 and a.polling=1 and a.app_status=1 GROUP BY a.id");
                $count_land = count($all_polling);
                //存在其他轮询账户并且为开启状态
                if ($count_land > 0) {
                    //取所有符合条件的收款账户
                    for ($i = 0; $i < $count_land; $i++) {

                        if ($all_polling[$i]['limit_time'] != "0") {
                            if ($all_polling[$i]['state'] == 1) {
                                unset($all_polling[$i]);
                                continue;
                            }
                            if ($all_polling[$i]['state'] == 2) {
                                if ($all_polling[$i]['pay_time'] != null && $all_polling[$i]['pay_time'] != "") {
                                    if (time() - $all_polling[$i]['pay_time'] < $all_polling[$i]['limit_time']) {
                                        unset($all_polling[$i]);
                                        continue;
                                    }
                                }
                            } else {
                                if ($all_polling[$i]['overtime'] != null && $all_polling[$i]['overtime'] != "") {
                                    if (time() - $all_polling[$i]['overtime'] < $all_polling[$i]['limit_time']) {
                                        unset($all_polling[$i]);
                                        continue;
                                    }
                                }
                            }
                        }
                        //判断提交金额是否在收款账户限制金额范围内
                        if ($all_polling[$i]['min_amount'] != 0) {
                            if ($addData['money_index'] < floatval($all_polling[$i]['min_amount'])) {
                                unset($all_polling[$i]);
                                continue;
                            }
                        }
                        if ($all_polling[$i]['max_amount'] != 0) {
                            if ($addData['money_index'] > $all_polling[$i]['max_amount']) {
                                unset($all_polling[$i]);
                                continue;
                            }
                        }

                        //查询数据库中包含的未支付的订单，确保额度计算更准确
                        if ($all_polling[$i]['requota'] != -1) {
                            //查询数据库中包含的未支付的订单，确保额度计算更准确
                            if ($all_polling[$i]['total'] != null && $all_polling[$i]['total'] != "" && $all_polling[$i]['total'] != 0) {
                                if ($addData['money_index'] + floatval($all_polling[$i]['total']) > floatval($all_polling[$i]['requota'])) {
                                    unset($all_polling[$i]);
                                    continue;
                                }
                            } else {
                                if ($addData['money_index'] > floatval($all_polling[$i]['requota'])) {
                                    unset($all_polling[$i]);
                                    continue;
                                }
                            }
                        }
                    }
                    $count = count($all_polling);
                    if ($count > 0) {
                        $all_polling = array_values($all_polling);
                        $rand = mt_rand(0, $count - 1);
                        $find_land = $all_polling[$rand];
                        unset($all_polling);
                        $qrcode_query = [];
                    } else {
                        $this->msgJson($typeJson, 2011, '当前通道暂无可用收款账户可用，请稍后再提交订单');
                    }
                } else {
                    $this->msgJson($typeJson, 2011, '当前通道已经关闭，且不存在其他通道可用，请联系管理后重新提交订单');
                }
            } else if ($pollmode == "2") {
                $polling_land = $mysql->query("land", "userid={$user['id']} and typec={$typec} and polling=1 and qr_typec=1 and status=1", null, "polltimec", "ASC", "1");
                $polling_land = $polling_land[0];
                if (is_array($polling_land)) {
                    $mysql->update("land", array('polltimec' => time()), "id={$polling_land['id']}");
                    //判断是否有未支付的相同金额订单
                    if ($polling_land['limit_time'] != "0") {
                        $order = $mysql->query("takes", "state=1 and land_id={$polling_land['id']} and money={$addData['money_index']}", null, "id", "desc", "1");
                        if (is_array($order[0])) {
                            unset($order);
                            $this->msgJson($typeJson, 3001, '该金额暂时无法在该渠道提交，请稍后再试！');
                        }

                        $checkorder = $mysql->query("takes", "state<>1 and land_id={$polling_land['id']} and money={$addData['money_index']}", null, "id", "desc", "1");
                        if (is_array($checkorder[0])) {
                            if ($checkorder[0]['state'] == 2) {
                                if (time() - $checkorder[0]['pay_time'] < $polling_land['limit_time']) {
                                    unset($checkorder);
                                    $this->msgJson($typeJson, 3002, '暂时无法提交，请稍后重试');
                                }
                            } else {
                                if (time() - $checkorder[0]['overtime'] < $polling_land['limit_time']) {
                                    unset($checkorder);
                                    $this->msgJson($typeJson, 3002, '暂时无法提交，请稍后重试');
                                }
                            }
                        }
                    }
                    //判断提交金额是否在收款账户限制金额范围内
                    if ($polling_land['min_amount'] != 0) {
                        if ($addData['money_index'] < floatval($polling_land['min_amount'])) {
                            $this->msgJson($typeJson, 2011, '提交金额不能小于 ' . $polling_land['min_amount'] . " 元");
                            //continue;
                        }
                    }

                    if ($polling_land['max_amount'] != 0) {
                        if ($addData['money_index'] > $polling_land['max_amount']) {
                            $this->msgJson($typeJson, 2011, '提交金额不能大于 ' . $polling_land['max_amount'] . " 元");
                            //continue;
                        }
                    }
                    if (floatval($polling_land['requota']) != -1) {
                        $orders_money = $mysql->query("takes", "land_id={$polling_land['id']} and userid={$user['id']} and state=1", "sum(money) as total");
                        if (is_array($orders_money[0])) {
                            $orders_money = $orders_money[0]['total'];
                        } else {
                            $orders_money = 0;
                        }
                        if (floatval($polling_land['quota']) + $addData['money_index'] + $orders_money > floatval($polling_land['requota'])) {
                            //特殊情况，返回错误(当前渠道额度已经不足够订单使用)
                            $this->msgJson($typeJson, 2011, '当前通道额度不足，请联系管理后重新提交订单');
                        }
                    }
                    $find_land = $polling_land;
                } else {
                    $this->msgJson($typeJson, 2016, '通道配置错误');
                }
            }

            //计算总的二维码数量
            $qr_count = count($qrcode_query);
            if (is_array($find_land)) {
                if ($find_land['pattern'] == "3" && $find_land['app_status'] == 1) {
                    $model = 2;          //APP实时生码收款账户
                }
            }

            //不存在已生成的二维码
            if (empty($qr_count)) {
                //如果账户下不存在通用码，判断是否存在实时生成渠道
                if ($model == 2) {
                    if ($typec == 26) {
                        $dir_path = $_SERVER['DOCUMENT_ROOT'] . "/temp";
                        if (!is_dir($dir_path)) {
                            mkdir($dir_path, 0777, true);
                        }
                        if (!file_exists($dir_path . "/tempP_" . $find_land['id'] . "_" . intval($addData['money_index']) . ".txt")) {
                            $tempMoney = 99;
                            file_put_contents($dir_path . "/tempP_" . $find_land['id'] . "_" . intval($addData['money_index']) . ".txt", $tempMoney);
                        } else {
                            $tempMoney = file_get_contents($dir_path . "/tempP_" . $find_land['id'] . "_" . intval($addData['money_index']) . ".txt");
                            $tempMoney -= 1;
                        }
                        $addData['money'] = functions::drive('money')->garden_real($mysql, $find_land['id'], $addData['money_index'], $tempMoney, 1);
                        if ($addData['money'] === false || empty($addData['money'])) {
                            $this->msgJson($typeJson, 2010, '金额不正确，请重新提交');
                        }
                    }
                    $addData['money'] = empty($addData['money']) ? $addData['money_index'] : $addData['money'];
                    //如果没有可用对应金额的二维码，则查找通用二维码
                    $qrcode_array['state'] = 0;
                    $qrcode_array['userid'] = $sdk_query['userid'];
                    $qrcode_array['land_id'] = $find_land['id'];
                    $qrcode_array['money'] = floatval($addData['money']);
                    $qrcode_array['money_res'] = $addData['money_index'];
                    $qrcode_array['qrcode'] = "";
                    $qrcode_array['mark'] = $mark;
                    $qrcode_array['typec'] = $typec;
                    $qrcode_array['info'] = $info;
                    $qrcode_array['create_time'] = time();
                    $qrcode_array['bank'] = $bank;
                    $qrcode_array['device'] = $addData['device'];
                    $insert_qrcode = $mysql->insert("qrcode_link", $qrcode_array);
                    if ($insert_qrcode) {
                        $qrcode_query[] = $qrcode_array;
                        $real = 1;
                        $addData['qrcode_id'] = $insert_qrcode;
                    } else {
                        $this->msgJson($typeJson, 3015, '插入数据失败');
                    }
                } else {
                    $real = 0;
                }


                //所有通道均不可用
                if ($real == 0) {
                    $this->msgJson($typeJson, 1006, '当前支付通道繁忙,请稍后再试');
                }
                if (count($qrcode_query) > 1) {
                    $a = rand(0, count($qrcode_query) - 1);
                } else if (count($qrcode_query) == 1) {
                    $a = 0;
                } else {
                    $this->msgJson($typeJson, 1007, '当前通道繁忙,请稍后再试!');
                }
            } else {
                //存在已生成的二维码
                if ($qr_count > 1) {
                    $a = rand(0, $qr_count - 1);
                } else if ($qr_count == 1) {
                    $a = 0;
                }
            }
            $qrcode_query = $qrcode_query[$a]; //数据转换
            $addData['money'] = $qrcode_query['money'];
        } else {
            if (empty($sdk_query['status'])) {
                $this->msgJson($typeJson, 3010, '当前通道已经关闭，请联系管理后重新提交订单');
            }
            if ($sdk_query['status'] == "2") {
                $this->msgJson($typeJson, 3010, '当前通道额度已用完，请联系管理后重新提交订单');
            }

            //判断是否有未支付的相同金额订单
            if ($sdk_query['limit_time'] != "0") {
                $order = $mysql->query("takes", "state=1 and land_id={$sdk_query['id']} and money={$addData['money_index']} and create_time>={$start_time} and create_time<={$end_time}", "id", "id", "desc", "1");
                if (is_array($order[0])) {
                    unset($order);
                    $this->msgJson($typeJson, 3001, '该金额暂时无法在该渠道提交，请稍后再试！');
                }
                $checkorder = $mysql->query("takes", "state<>1 and land_id={$sdk_query['id']} and money={$addData['money_index']} and create_time>={$start_time} and create_time<={$end_time}", "state,pay_time,overtime", "id", "desc", "1");
                if (is_array($checkorder[0])) {
                    if ($checkorder[0]['state'] == 2) {
                        if (time() - $checkorder[0]['pay_time'] < $sdk_query['limit_time']) {
                            unset($checkorder);
                            $this->msgJson($typeJson, 3002, '暂时无法提交，请稍后重试');
                        }
                    } else {
                        if (time() - $checkorder[0]['overtime'] < $sdk_query['limit_time']) {
                            unset($checkorder);
                            $this->msgJson($typeJson, 3002, '暂时无法提交，请稍后重试');
                        }
                    }
                }
            }
            //判断提交金额是否在收款账户限制金额范围内
            if ($sdk_query['min_amount'] != 0) {
                if ($addData['money_index'] < floatval($sdk_query['min_amount'])) {
                    $this->msgJson($typeJson, 2011, '提交金额不能小于 ' . $sdk_query['min_amount'] . " 元");
                    //continue;
                }
            }

            if ($sdk_query['max_amount'] != 0) {
                if ($addData['money_index'] > $sdk_query['max_amount']) {
                    $this->msgJson($typeJson, 2011, '提交金额不能大于 ' . $sdk_query['max_amount'] . " 元");
                    //continue;
                }
            }
            if (floatval($sdk_query['requota']) != -1) {
                $orders_money = $mysql->query("takes", "land_id={$sdk_query['id']} and userid={$sdk_query['userid']} and state=1 and create_time>={$start_time} and create_time<={$end_time}", "sum(money) as total");
                $orders_money = $orders_money[0];
                if (floatval($sdk_query['quota']) + $addData['money_index'] + $orders_money['total'] > floatval($sdk_query['requota'])) {
                    //特殊情况，返回错误(当前渠道额度已经不足够订单使用)
                    $this->msgJson($typeJson, 2011, '当前通道额度不足，请联系管理后重新提交订单');
                }
            }

            $qr_count = count($qrcode_query);
            if (empty($qr_count)) {
                if ($sdk_query['pattern'] == "3" && $sdk_query['app_status'] == 1) {
                    if ($typec == 26) {
                        $dir_path = $_SERVER['DOCUMENT_ROOT'] . "/temp";
                        if (!is_dir($dir_path)) {
                            mkdir($dir_path, 0777, true);
                        }
                        if (!file_exists($dir_path . "/tempP_" . $sdk_query['id'] . "_" . intval($addData['money_index']) . ".txt")) {
                            $tempMoney = 99;
                            file_put_contents($dir_path . "/tempP_" . $sdk_query['id'] . "_" . intval($addData['money_index']) . ".txt", $tempMoney);
                        } else {
                            $tempMoney = file_get_contents($dir_path . "/tempP_" . $sdk_query['id'] . "_" . intval($addData['money_index']) . ".txt");
                            $tempMoney -= 1;
                        }
                        $addData['money'] = functions::drive('money')->garden_real($mysql, $sdk_query['id'], $addData['money_index'], $tempMoney, 1);
                        if ($addData['money'] === false || empty($addData['money'])) {
                            $this->msgJson($typeJson, 2010, '金额不正确，请重新提交');
                        }
                    }
                    $addData['money'] = empty($addData['money']) ? $addData['money_index'] : $addData['money'];
                    $addData['money'] = floatval($addData['money']);
                    if (floatval($sdk_query['requota']) != -1 && ((floatval($sdk_query['requota']) - floatval($sdk_query['quota']) - floatval($orders_money['total'])) < $addData['money_index'])) {
                        $this->msgJson($typeJson, 3011, '当前通道额度不足，请联系管理后重新提交订单');
                    }
                    $qrcode_array['state'] = 0;
                    $qrcode_array['userid'] = $sdk_query['userid'];
                    $qrcode_array['land_id'] = $sdk_query['id'];
                    $qrcode_array['money'] = $addData['money'];
                    $qrcode_array['money_res'] = $addData['money_index'];
                    $qrcode_array['qrcode'] = "";
                    $qrcode_array['mark'] = $mark;
                    $qrcode_array['typec'] = $typec;
                    $qrcode_array['info'] = $info;
                    $qrcode_array['create_time'] = time();
                    $qrcode_array['bank'] = $bank;
                    $qrcode_array['device'] = $addData['device'];
                    $insert_qrcode = $mysql->insert("qrcode_link", $qrcode_array);
                    if ($insert_qrcode) {
                        $qrcode_query = $qrcode_array;
                        $real = 1;
                        $addData['qrcode_id'] = $insert_qrcode;
                    } else {
                        $this->msgJson($typeJson, 3015, '插入数据失败');
                    }
                } else {
                    $this->msgJson($typeJson, 3012, '当前通道繁忙');
                }
            } else {
                if ($qr_count > 1) {
                    $b = rand(0, $qr_count - 1);
                } else {
                    $b = 0;
                }
                $qrcode_query = $qrcode_query[$b]; //数据转换
                $addData['money'] = $qrcode_query['money'];
            }
        }


        if ($qrcode_query['land_id'] != $sdk_query['id']) {
            $land_query = $mysql->query("land", "id={$qrcode_query['land_id']}");
            $landname = $land_query[0]['username'];
        } else {
            $landname = $sdk_query['username'];
        }
        //订单创建时间
        $order_time = time();
        //二维码是否为实时生成
        if ($real == 1) {
            $qr_type = 1;
        } else {
            $qr_type = 0;
        }

        if ($sdk_query['qr_typec'] != "2") {
            if ($qrcode_query['mark'] == "") {
                if ($qr_type == 7 || $qr_type == 8) {
                    $qrcode_query['mark'] = date("md") . functions::generateRandomNum(16);
                } else if ($qr_type == 14) {
                    $qrcode_query['mark'] = date("md") . functions::generateRandomNum(8);
                } else {
                    $qrcode_query['mark'] = $addData['num'];
                }
            }
        }
        //再次判断是否有未支付的相同金额订单
        if ($qrcode_query['limit_time'] != 0) {
            $order = $mysql->query("takes", "land_id={$qrcode_query['id']} and money={$addData['money_index']}", "id,state,create_time,pay_time,overtime", "id", "desc", "1");
            if (is_array($order[0])) {
                $order = $order[0];
                if ($order['state'] == 1) {
                    unset($order);
                    $this->msgJson($typeJson, 3001, '该金额暂时无法在该渠道提交，请稍后再试！');
                } else if ($order['state'] == 2) {
                    $temp_pay_time = time() - $order['pay_time'];
                    if ($temp_pay_time < $qrcode_query['limit_time']) {
                        unset($order);
                        $this->msgJson($typeJson, 3002, '暂时无法提交，请稍后重试');
                    }
                } else {
                    $temp_overtime = time() - $order['overtime'];
                    if ($temp_overtime < $qrcode_query['limit_time']) {
                        unset($order);
                        $this->msgJson($typeJson, 3002, '暂时无法提交，请稍后重试');
                    }
                }
            }
        }

        $addData['info'] = $info;
        $addData['create_time'] = time();
        $addData['payc'] = $typec;
        $addData['state'] = 1;
        $addData['land_id'] = $qrcode_query['land_id'];
        $addData['userid'] = $sdk_query['userid'];
        $addData['notify_url'] = $notify_url;
        $addData['refer'] = $refer;
        $addData['mark'] = $qrcode_query['mark'];
        $addData['qr_type'] = $qr_type;
        $addData['agentid'] = $user['agentid'];
        $addData['version'] = $version;
        $addData['bank_code'] = $bank;
        //创建订单到数据库
        $insert_order = $mysql->insert('takes', $addData);
        //创建失败
        if (!$insert_order)
            $this->msgJson($typeJson, 1007, '订单创建失败,请重试');
        if ($real != 2) {
            //更改二维码状态
            if ($qrcode_query['money'] != 0) {
                $update_qrcode = $mysql->update('qrcode_link', array('state' => 2), "id={$qrcode_query['id']}");
                //二维码状态更新成功
                if (!$update_qrcode)
                    $this->msgJson($typeJson, 1008, '订单创建出错,请重试');
            }
        }
        if (empty($bank)) {
            if ($real == 1) {
                $link = functions::getdomain() . '?a=servlet&b=index&c=qrcode&text=' . urlencode(functions::getdomain() . "pay/qrcode.php?order_no=" . $addData['num'] . "&step=1");
                $qrcode = $qrcode_query['qrcode'];
            }
            if ($typeJson != 'json') {
                header("location:" . functions::getdomain() . "pay/api.php?c=cashier&order_no=" . $addData['num']);
            } else {
                //返回json
                functions::json(200, 'success', array(
                    'sdk_name' => $landname, //商家帐户名
                    'money' => $addData['money'], //交易金额
                    'amount' => $addData['money_index'],
                    'record' => $info, //提交信息
                    'order_num' => $addData['num'], //订单号
                    'order_time' => $order_time, //订单号创建时间
                    'image' => $link, //二维码地址
                    'refer' => urldecode($refer), //成功跳转地址
                    'msgInfo' => $msgInfo, //支付提示信息
                    'real' => $real, //是否实时生成二维码
                    'mark' => $mark,
                    'qrcode' => $qrcode,
                    'typec' => $typec,
                    'qr_typec' => $sdk_query['qr_typec'] // 二维码类型： 通码  固码
                ));
            }
        } else {
            header("location:" . functions::getdomain() . "pay/qrcode.php?order_no=" . $addData['num'] . "&step=1");
        }
    }

    //拉取订单处理信息
    function get() {
        // 指定允许其他域名访问
        //header('Access-Control-Allow-Origin:*');
        // 响应类型
        //header('Access-Control-Allow-Methods:POST');
        // 响应头设置
        //header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $num = functions::request('num'); //订单号
        $tradeno = functions::request("tradeno");
        if (empty($num) && empty($tradeno)) {
            functions::json(1001, '订单不存在');
        }
        if ($num != '' && $num != null) {
            $where = "num='{$num}'";
            if ($tradeno != '' && $tradeno != null) {
                $where = $where . " and info='{$tradeno}'";
            }
        } else {
            if ($tradeno != '' && $tradeno != null) {
                $where = "info='{$tradeno}'";
            }
        }

        $mysql = functions::open_mysql();
        $order = $mysql->select("SELECT state FROM mi_takes where {$where}");
        $order = $order[0];
        if (!is_array($order)) {
            functions::json(1001, '订单已被销毁');
            //mysql->update('qrcode',array('state'=>1,"land_id={}"))
        }

        //检测订单是否超时
        if ($order['payc'] == 5 || $order['payc'] == 6) {
            $time = 120;
        } else {
            if ($order['qr_type'] == 6) {
                $time = 600;
            } else {
                $time = 270;
            }
        }

        if ($order['state'] == 3)
            functions::json(1002, '订单已经超时', $order['num']);
        if ($order['state'] == 2)
            functions::json(200, '支付成功', $order['num']);
        if ($order['state'] == 1)
            functions::json(1003, '订单未支付', $order['num']);
    }

    function cashier() {
        $num = functions::request('order_no'); //订单号
        if (empty($num))
            functions::json(-1, '订单号错误');
        $mysql = functions::open_mysql();
        $order = $mysql->select("select A.*,B.qrcode,B.bank,B.post_url from mi_takes as A INNER JOIN mi_qrcode_link as B on A.qrcode_id = B.id left join mi_land as C on A.payc=C.id where A.num='{$num}' and B.state=0 limit 1");
        if (empty($order))
            functions::json(1001, '订单已被销毁');
        $order = $order[0];
        $wap = '';
        if (functions::isMobile()) {
            $wap = 'wap_';
            $bank_type = 1;
        } else {
            $bank_type = 2;
        }
        $bank_sql = "select * from mi_bank where bank_type<>{$bank_type} and status=1";
        $banks = $mysql->select($bank_sql);
        //分析支付宝/微信/QQ通道
        if ($order['payc'] == 26)
            $temp = $wap . 'bank2alipay';

        //拉取二维码,渲染界面
        functions::import_var($temp, array("order" => $order, "banks" => $banks));
    }

    function bankMemoList() {
        $bank_id = intval(functions::request("bankId"));
        $mysql = functions::open_mysql();
        $bank_memo = $mysql->select("select * from mi_bank_memo where bank_id={$bank_id} order by sortId asc");
        if (!empty($bank_memo)) {
            functions::json(200, "获取成功", $bank_memo);
        } else {
            functions::json(-1, "暂无数据");
        }
    }

    function create() {
        $order_no = trim(functions::request("order_no"));
        $bank_id = trim(functions::request("bank_id"));
        if (empty($order_no)) {
            functions::json(-1, "订单信息有误");
        }
        if (empty($bank_id)) {
            functions::json(-1, "银行代码为空");
        }
        $mysql = functions::open_mysql();
        $bank = $mysql->query("bank", "bank_id={$bank_id}");
        if (empty($bank)) {
            functions::json(-1, "银行信息有误");
        }
        $order = $mysql->query("takes", "num='{$order_no}'");
        if (empty($order)) {
            functions::json(-1, "订单错误");
        }
        if ($order[0]['state'] == 3) {
            functions::json(-1, "订单超时");
        }
        $mysql->update("takes", array("bank_code" => $bank[0]['bank_code']), "num='{$order_no}' and state=1");
        $mysql->update("qrcode_link", array("bank" => $bank[0]['bank_code']), "id='{$order['qrcode_id']}' and state=0");
        $payUrl = functions::getdomain() . "pay/qrcode.php?order_no=" . $order_no . "&step=1";
        functions::json(200, "获取成功", $payUrl);
    }

    function getQrcode() {
        // 指定允许其他域名访问
        header('Access-Control-Allow-Origin:*');
// 响应类型
        header('Access-Control-Allow-Methods:POST');
// 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $num = functions::request('num'); //订单号
        if (empty($num))
            functions::json(-1, '订单号错误');
        $mysql = functions::open_mysql();
        $qrcode_info = $mysql->select("select a.money,a.mark,a.userid,a.land_id,a.qr_type,a.payc,b.id,b.qrcode,b.state from mi_takes as a inner join mi_qrcode_link as b on a.mark=b.mark where a.num={$num}");
        //$order = $mysql->query('takes', "num='{$num}'");
        $qrcode_info = $qrcode_info[0];
        if (!is_array($qrcode_info))
            functions::json(1001, '订单已被销毁');
        if ($qrcode_info['qrcode'] != "") {
            if ($qrcode_info['payc'] == 1 || $qrcode_info['payc'] == 2) {
                $mysql->update('qrcode_link', array("state" => "2"), "id={$qrcode_info['id']}");
            } else {
                //$mysql->delete('qrcode_link', "id={$qrcode_info['id']}");
            }
            functions::json(200, '获取成功', $qrcode_info);
        }
    }
    
     public function orderQuery() {
        $data['partnerid'] = intval(functions::request("partnerid"));
        $data['out_trade_no'] = trim(functions::request("out_trade_no"));
        $data['sign'] = trim(functions::request("sign"));
        if (empty($data['partnerid']))
            functions::json(1001, "用户不存在");
        if (empty($data['out_trade_no']))
            functions::json(1002, "订单号不正确");
        $mysql = functions::open_mysql();
        $user = $mysql->query("users", "id={$data['partnerid']}");
        if (empty($user)) {
            functions::json(-1, "商户不存在");
        }
        $key = $user[0]['keyid'];
        $sign_index = "out_trade_no=" . $data['out_trade_no'] . "&partnerid=" . $data['partnerid'];
        $sign_index = $sign_index . "#" . $key;
        if ($data['sign'] != md5($sign_index))
            functions::json(1003, "签名错误");
        $order = $mysql->query("takes", "userid={$data['partnerid']} and info='{$data['out_trade_no']}'", "id,userid,info,money,money_index,num,state,create_time,pay_time,overtime");
        // 订单不存在
        if (empty($order))
            functions::json(1004, "订单不存在");
        $order = $order[0];
        if ($order['state'] == 2) {
            functions::json(200, " 支付成功", $order);
        } else if ($order['state'] == 3) {
            functions::json(1005, "订单支付已超时 ");
        } else {
            functions::json(1006, "订单等待支付中 ");
        }
    }

    function checkalikey() {
        $num = functions::request('num'); //订单号
        $psw = functions::request('psw');
        $repsw = functions::request('repsw');
        if (empty($num))
            functions::json(-1, '订单号错误');
        //if ($psw != $repsw) {
        //    functions::json(-2, '两次口令输入不同');
        //}
        $mysql = functions::open_mysql();
        $takes = $mysql->select("select id,userid,land_id,payc,aliredkey from mi_takes where aliredkey={$psw} limit 1");
        if (!is_array($takes[0])) {
            $order = $mysql->select("select id,userid,land_id,payc,aliredkey from mi_takes where num='{$num}' limit 1");
            if (is_array($order[0])) {
                if ($order[0]['aliredkey'] == 0) {
                    $mysql->update("takes", array('aliredkey' => $psw), "num='{$num}'");
                    $key['userid'] = $order[0]['userid'];
                    $key['land_id'] = $order[0]['land_id'];
                    $key['typec'] = $order[0]['payc'];
                    $key['PasswordKey'] = $psw;
                    $client = stream_socket_client('tcp://127.0.0.1:8806', $errno, $errmsg, 1);
                    if (!$client)
                        return "can not connect";
                    // 推送的数据，包含uid字段，表示是给这个uid推送
                    $data['data'] = $key;
                    $data['type'] = "PasswordGathering";
                    // 发送数据，注意8991端口是Text协议的端口，Text协议需要在数据末尾加上换行符
                    fwrite($client, json_encode($data) . "\n");
                    functions::json(200, '提交成功');
                } else {
                    functions::json(-5, '订单已存在口令');
                }
            } else {
                functions::json(-3, '订单号不存在');
            }
        } else {
            functions::json(-4, '口令已被使用');
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
