<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
class index {

    //首页
    function start() {
        //functions::api('loginc')->AI('index',functions::urlc('index'));
        functions::import('index');
    }

    //登录界面
    function login() {
        functions::api('loginc')->AI('login', functions::urlc('user', 'index', 'home'));
        functions::import('login');
    }

    //default
    function inlet() {
        $user = $this->AI();
        functions::import('index', array("user" => $user));
    }

    //注册界面
    function register() {
        functions::api('loginc')->AI('login', functions::urlc('user', 'index', 'home'));
        functions::import('register');
    }

    //找回密码界面
    function forget() {
        functions::api('loginc')->AI('login', functions::urlc('user', 'index', 'home'));
        functions::import('forget');
    }

    //home界面
    function home() {
        $user = $this->AI();
        if ($user->parentid != 0) {
            $mysql = functions::open_mysql();
            $parent = $mysql->query("users", "id={$user->parentid}");
            functions::import('home', array("user" => $user, "parent" => $parent[0]));
        } else {
            functions::import('home', array("user" => $user));
        }
    }

    //个人设置
    function my() {
        $user = $this->AI();
        if ($user->parentid == 0) {
            $temp = "my";
        } else {
            $temp = "my_bank";
        }
        functions::import($temp, array("user" => $user));
    }

    //key
    function key() {
        $user = $this->AI();
        $data = functions::open_mysql()->select("select * from mi_users where id={$user->sid}");
        functions::import('key', array("user" => $user, "data" => $data[0]));
    }

    function customer() {
        $user = $this->AI();
        if ($user->parentid != 0)
            exit('用户无权限');
        $id = intval(functions::request("id"));
        $phone = functions::request("phone");
        $ip = functions::request("ip");
        $state = intval(functions::request("status"));
        $where = "parentid={$user->sid}";
        if (!empty($id)) {
            $where = $where . " and id={$id}";
        }
        if ($phone != "" && $phone != null) {
            $where = $where . " and phone={$phone}";
        }
        if ($ip != "" && $ip != null) {
            $where = $where . " and ip='{$ip}'";
        }
        if (!empty($state)) {
            $where = $where . " and state={$state}";
        }
        functions::import('customer', array('user' => $user, 'data' => functions::drive('query')->column('users', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    //添加收款账号
    function customer_add() {
        $user = $this->AI();
        if ($user->parentid != 0)
            exit('用户无权限');
        functions::import('customer_add', array('user' => $user));
    }

    //修改收款账号
    function customer_edit() {
        $user = $this->AI();
        if ($user->parentid != 0)
            exit('用户无权限');
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('users', "id={$id} and parentid={$user->sid}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import('customer_edit', array('user' => $user, 'data' => $query[0]));
    }

    //我的收款账号
    function land() {
        $user = $this->AI();
        $today_start_time = strtotime(date('Y-m-d', time()));
        $today_end_time = strtotime(date('Y-m-d 23:59:59', time()));
        $yesterday_start_time = strtotime(date("Y-m-d")) - 86400;
        $yesterday_end_time = strtotime(date('Y-m-d 23:59:59', time())) - 86400;
        //$sql = "select a.*, sum(IF(b.state = 2 and b.create_time >= {$today_start_time} and b.create_time<={$today_end_time}, money, 0)) as today_all_money, count((b.state = 2 and b.create_time >= {$today_start_time} and b.create_time<={$today_end_time}) or null) as today_total_success_order, count((b.create_time >= {$today_start_time} and b.create_time<={$today_end_time}) or null) as today_total_order, sum(IF(b.state = 2 and b.create_time >= {$yesterday_start_time} and b.create_time<={$yesterday_end_time}, money, 0)) as yesterday_all_money, count((b.create_time >= {$yesterday_start_time} and b.create_time<={$yesterday_end_time}) or null) as yesterday_total_order, count((state = 2 and b.create_time >= {$yesterday_start_time} and b.create_time<={$yesterday_end_time}) or null) as yesterday_total_success_order from mi_land as a left JOIN mi_takes as b on a.id = b.land_id where a.userid={$user->sid} GROUP BY a.id ORDER BY a.app_status desc,a.id desc";
        $sql = "select a.*, sum(IF(b.state = 2 and b.create_time >= {$today_start_time} and b.create_time<={$today_end_time}, money, 0)) as today_all_money, count((b.state = 2 and b.create_time >= {$today_start_time} and b.create_time<={$today_end_time}) or null) as today_total_success_order, count((b.create_time >= {$today_start_time} and b.create_time<={$today_end_time}) or null) as today_total_order, sum(IF(b.state = 2 and b.create_time >= {$yesterday_start_time} and b.create_time<={$yesterday_end_time}, money, 0)) as yesterday_all_money, count((b.create_time >= {$yesterday_start_time} and b.create_time<={$yesterday_end_time}) or null) as yesterday_total_order, count((state = 2 and b.create_time >= {$yesterday_start_time} and b.create_time<={$yesterday_end_time}) or null) as yesterday_total_success_order from mi_land as a left JOIN (SELECT state,create_time,money,land_id from mi_takes where userid={$user->sid} and create_time>={$yesterday_start_time} and create_time<={$today_end_time}) as b on a.id = b.land_id where a.userid={$user->sid} GROUP BY a.id ORDER BY a.status desc,CONVERT(a.username USING gbk)";
        $count_land_sql = "select count(*) as count from mi_land where userid={$user->sid}";
        functions::import('land', array('user' => $user, 'data' => functions::drive('query')->column_sql($sql, $count_land_sql, array('num' => intval(functions::request('page')), 'all' => 20))));
        //functions::import('land', array('user' => $user, 'data' => $query));
    }

    //添加收款账号
    function land_add() {
        $user = $this->AI();
        $Cost = functions::api('reback')->getCost($user->sid);

        functions::import('land_add', array('user' => $user, "cost" => $Cost));
    }

    //修改收款账号
    function land_edit() {
        $user = $this->AI();
        $Cost = functions::api('reback')->getCost($user->sid);
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('land', "id={$id} and userid={$user->sid}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import('land_edit', array('user' => $user, 'data' => $query[0], "cost" => $Cost));
    }

    //修改收款账号
    function land_del() {
        $user = $this->AI();
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('land', "id={$id} and userid={$user->sid}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import('land_del', array('user' => $user, 'data' => $query[0]));
    }

    //我的通用二维码
    function qrcode() {
        $user = $this->AI();
        $payc = intval(functions::request('payc'));
        if (!empty($payc))
            $where = 'and typec=' . $payc;
        functions::import('qrcode', array('user' => $user, 'data' => functions::drive('query')->column('qrcode', "userid={$user->sid} {$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    //我的二维码
    function qrcode_link() {
        $user = $this->AI();
        //$payc = intval(functions::request('payc'));
        //if (!empty($payc)) $where = 'and typec=' . $payc;
        //functions::import('qrcode',array('user'=>$user,'data'=>functions::drive('query')->column('qrcode',"userid={$user->sid} {$where}",array('num'=>intval(functions::request('page')),'all'=>20),null,'id','desc')));
        //全新修改
        $landid = intval(functions::request('id'));
        $typec = intval(functions::request('typec'));
        $page = intval(functions::request('page'));
        //查找索引以money_res为准
        $mysql = functions::open_mysql();
        $index = functions::drive('query')->column_qrcode("qrcode_link", "land_id='{$landid}' and typec={$typec} group by money_res", array('num' => $page, 'all' => 20), "money_res", "money_res", "asc");

        //$index = $mysql->query("qrcode_link","land_id='{$landid}' and typec={$typec} group by money_res","money_res","money_res","asc");
        //索引总数量
        $index_count = count($index['query']);
        for ($i = 0; $i < $index_count; $i++) {
            $money_res = $index['query'][$i]['money_res'];
            //单个金额二维码总数
            $money = $mysql->query("qrcode_link", "money_res='{$money_res}' and land_id={$landid} and typec={$typec}", "count(money_res) as money_index_count");
            //单个金额二维码已使用总数
            $money_used = $mysql->query("qrcode_link", "money_res='{$money_res}' and land_id={$landid} and state=2 and typec={$typec}", "count(money_res) as money_used_count");
            $qrcode[$i]['money_res'] = $money_res;
            $qrcode[$i]['count'] = $money[0]['money_index_count'];
            $qrcode[$i]['used'] = $money_used[0]['money_used_count'];
            //echo json_encode($money[0]['money_index_count']);
        }
        $data = $index['info'];
        functions::import("qrcode_list", array('qrcode' => $qrcode, 'data' => $data));
    }

    //添加二维码
    function qrcode_add() {
        $user = $this->AI();
        $mysql = functions::open_mysql();
        //查询收款账号数据库
        $land = $mysql->select("select a.id,a.username,a.typec from mi_land as a left join mi_qrcode as b on a.id = b.land_id  where a.userid={$user->sid} and a.qr_typec =2 and b.id is null");
        functions::import('qrcode_add', array('user' => $user, 'land' => $land));
    }

    //修改二维码
    function qrcode_edit() {
        $user = $this->AI();
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('qrcode', "id={$id} and userid={$user->sid}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import('qrcode_edit', array('user' => $user, 'data' => $query[0]));
    }

    //批量上传二维码
    function qrcode_batch() {
        $user = $this->AI();
        functions::import('qrcode_batch', array('user' => $user));
    }

    //单通道测试
    function singleTest() {
        $user = $this->AI();
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('land', "id={$id}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import('singleTest', array('user' => $user, 'data' => $query[0]));
    }

    //二级密码校验
    function api_request() {
        $user = $this->AI();
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('takes', "id={$id}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import('api_request', array('user' => $user, 'data' => $query[0]));
    }

    //支付订单 takes
    function takes() {
        $user = $this->AI();
        $payc = intval(functions::request('payc'));
        $mark = trim(functions::request('mark'));
        $state = intval(functions::request('state'));
        $info = trim(functions::request('info'));
        $landname = trim(functions::request('landname'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if ($user->parentid != 0) {
            $userid = $user->parentid;
        } else {
            $userid = $user->sid;
        }
        $where = "a.userid={$userid}";
        if (!empty($payc))
            $where = $where . ' and a.payc=' . $payc;
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where = $where . " and a.create_time>=" . $start_time . " and a.create_time<=" . $end_time;
        $num = trim(functions::request('num'));
        if (!empty($num))
            $where = $where . " and a.num='" . $num . "'";
        $orderNo = trim(functions::request('orderNo'));
        if (!empty($orderNo))
            $where = $where . " and a.reorderNo='" . $orderNo . "'";
        if (!empty($mark))
            $where = $where . " and a.mark='" . $mark . "'";
        if (!empty($state))
            $where = $where . ' and a.state=' . $state;
        if (!empty($landname)) {
            $where = $where . ' and b.username="' . $landname . '"';
        }
        if (!empty($info))
            $where = $where . ' and a.info="' . $info . '"';
        $is_export = trim(functions::request('is_export'));
        $export_all = trim(functions::request('export_all'));
        $sql = "select a.id,a.num,a.info,a.mark,a.orderNo,a.reorderNo,a.create_time,a.pay_time,a.money,a.payc,a.state,a.sending_times,a.bank_code,b.username,c.bank_name,b.username from mi_takes as a left join mi_land as b on a.land_id = b.id left join mi_bank as c on a.bank_code=c.bank_code where {$where} order by a.id desc";
        $count = "select count(*) as count from mi_takes as a left join mi_land as b on a.land_id = b.id where {$where}";
        $mysql = functions::open_mysql();
        if ($is_export) {
            if ($export_all) {
                $res['query'] = $mysql->query($sql);
            } else {
                $res = functions::drive('query')->column_sql($sql, $count, array('num' => intval(functions::request('page')), 'all' => 20));
            }

            $title = ['ID', '收款账号', '订单号', '商户订单号', '金额', '支付时间', '创建时间', '状态'];
            $state_map = ['1' => '未支付', '2' => '已支付', '3' => '订单超时'];
            $payc_map = ['26' => '支付宝'];
            $data = [];
            foreach ($res['query'] as $x) {
                $data[] = [
                    $x['id'],
                    $x['username'] . '(' . $payc_map[$x['payc']] . ')',
                    $x['num'],
                    $x['info'],
                    $x['money'],
                    date('Y/m/d H:i:s', $x['create_time']),
                    $x['pay_time'] != 0 ? date('Y/m/d H:i:s', $x['pay_time']) : '暂无数据',
                    $state_map[$x['state']],
                ];
            }

            return functions::exportExcel($title, $data);
        }

        functions::import('takes', array('user' => $user, 'data' => functions::drive('query')->column_sql($sql, $count, array('num' => intval(functions::request('page')), 'all' => 20))));
    }

    //收款订单 order
    function order() {
        $user = $this->AI();
        $payc = intval(functions::request('payc'));
        $mark = trim(functions::request('mark'));
        $landname = trim(functions::request('landname'));
        $num = trim(functions::request('num'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if ($user->parentid != 0) {
            $userid = $user->parentid;
        } else {
            $userid = $user->sid;
        }
        $where = "a.userid={$userid}";
        if (!empty($payc))
            $where = $where . ' and a.payc=' . $payc;
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where = $where . " and a.order_time>=" . $start_time . " and a.order_time<=" . $end_time;
        if (!empty($num))
            $where = $where . " and a.num='" . $num . "'";
        if (!empty($mark))
            $where = $where . " and a.remark='" . $mark . "'";
        if (!empty($landname)) {
            $land = functions::open_mysql()->query("land", "userid={$userid} and username='{$landname}'");
            if (is_array($land[0])) {
                $where = $where . ' and a.land_id=' . $land[0]['id'];
            }
        }
        $sql = "select a.id,a.num,a.remark,a.order_time,a.api_state,a.money,a.payc,a.http,a.request_time,a.payment,a.sending_times,b.username from mi_orders as a left join mi_land as b on a.land_id = b.id where {$where} order by a.id desc";
        $count = "select count(*) as count from mi_orders as a where {$where}";
        functions::import('order', array('user' => $user, 'data' => functions::drive('query')->column_sql($sql, $count, array('num' => intval(functions::request('page')), 'all' => 20))));
        //functions::import('order', array('user' => $user, 'data' => functions::drive('query')->column('orders', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    //异常订单
    function ab_order() {
        $user = $this->AI();
        $payc = intval(functions::request('payc'));
        $remark = trim(functions::request('remark'));
        $landname = trim(functions::request('landname'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        $orderNo = trim(functions::request("orderNo"));
        if (!empty($payc))
            $where = ' and payc=' . $payc;
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where = $where . " and order_time>=" . $start_time . " and order_time<=" . $end_time;
        if (!empty($orderNo))
            $where = $where . " and orderNo='" . $orderNo . "'";
        if (!empty($mark))
            $where = $where . " and remark='" . $remark . "'";
        if (!empty($landname)) {
            $land = functions::open_mysql()->query("land", "username='{$landname}' and userid={$user->sid}");
            if (is_array($land[0])) {
                $where = $where . ' and land_id=' . $land[0]['id'];
            }
        }
        functions::import('ab_order', array('user' => $user, 'data' => functions::drive('query')->column('ab_orders', "userid={$user->sid} {$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    //异常订单2
    function abnormal_order() {
        $user = $this->AI();
        $payc = intval(functions::request('payc'));
        $remark = trim(functions::request('remark'));
        $state = intval(functions::request('state'));
        $landname = trim(functions::request('landname'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if (!empty($payc))
            $where = ' and payc=' . $payc;
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where = $where . " and order_time>=" . $start_time . " and order_time<=" . $end_time;
        $num = trim(functions::request('num'));
        if (!empty($num))
            $where = $where . " and num='" . $num . "'";
        if (!empty($mark))
            $where = $where . " and remark='" . $remark . "'";
        if (!empty($state))
            $where = $where . ' and state=' . $state;
        if (!empty($landname)) {
            $land = functions::open_mysql()->query("land", "username='{$landname}' and userid={$user->sid}");
            if (is_array($land[0])) {
                $where = $where . ' and land_id=' . $land[0]['id'];
            }
        }
        functions::import('abnormal_order', array('user' => $user, 'data' => functions::drive('query')->column('abnormal_orders', "userid={$user->sid} {$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    //用户充值
    function recharge() {
        $user = $this->AI();
        functions::import('recharge', array("user" => $user));
    }

    //我的收款账号
    function news() {
        $user = $this->AI();
        $query = functions::open_mysql()->query('news');
        functions::import('news', array('data' => $query));
    }

    //我的收款账号
    function news_view() {
        $user = $this->AI();
        $id = functions::request("id");
        $query = functions::open_mysql()->query('news', "id={$id}");
        functions::import('news_view', array('data' => $query[0]));
    }

    function withdraw() {
        $user = $this->AI();
        $where = "user_id=" . $user->sid;
        $types = intval(functions::request('types'));
        $num = trim(functions::request('num'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where .= " and apply_time between " . $start_time . " and " . $end_time;
        if (!empty($types))
            $where = $where . " and types=" . $types;
        if (!empty($num))
            $where = $where . " and flow_no='" . $num . "'";
        $mysql = functions::open_mysql();
        $is_export = trim(functions::request('is_export'));
        $export_all = trim(functions::request('export_all'));
        if ($is_export) {
            if ($export_all) {
                $res['query'] = $mysql->query('withdraw', "{$where}", null, 'id', 'desc');
            } else {
                $res = functions::drive('query')->column('withdraw', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc');
            }

            $title = ['ID', '流水单号', '提现金额', '手续费用', '实际打款', '提现前余额', '提现后余额', '提交时间', '处理时间', '状态'];
            $state_map = ['1' => '等待管理员处理', '2' => '已经处理', '3' => '已驳回该提现'];
            $data = [];
            foreach ($res['query'] as $x) {
                $data[] = [
                    $x['id'],
                    $x['flow_no'],
                    $x['amount'],
                    $x['fees'],
                    $x['amount'] - $x['fees'],
                    $x['old_amount'],
                    $x['new_amount'],
                    date('Y/m/d H:i:s', $x['apply_time']),
                    $x['deal_time'] != 0 ? date('Y/m/d H:i:s', $x['deal_time']) : '暂无数据',
                    $state_map[$x['types']],
                ];
            }

            return functions::exportExcel($title, $data);
        }
        //$where = "user_id=".$agent->sid." and apply_time>=".$start_time." and apply_time<=".$end_time ."and status=".$status;
        functions::import('withdraw', array('data' => functions::drive('query')->column('withdraw', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc'), "user" => $user));
    }

    function applyWithdraw() {
        $user = $this->AI();
        if ($user->parentid == 0) {
            functions::json(-1, "当前商户不允许提现");
        }
        if (!in_array($user->bank->type, [1, 2]))
            exit('<span style="color:red;">您当前没有填写收款方式,请在个人设置里面添加银行卡或支付宝!</span>');
        $mysql = functions::open_mysql();
        $parent = $mysql->query("users", "id={$user->parentid}");
        //查询商户总利润
        $amount = $mysql->select("select sum(payment) as money from mi_orders where userid={$user->parentid}");
        //查询提现订单
        $order = $mysql->select("select sum(amount) as money,count(id) as count from mi_withdraw where user_id={$user->sid} and types!=3");
        //计算商户可提现金额
        $L_amount = floatval($amount[0]['money']) - floatval($order[0]['money']);
        if ($L_amount == $parent[0]['balance']) {
            $balance = $parent[0]['balance'];
        } else {
            $balance = $L_amount;
        }
        functions::import('apply_withdraw', array("user" => $user, "balance" => $balance));
    }

    //AI登录验证
    private function AI() {
        return functions::api('loginc')->AI('user', functions::urlc('user', 'index', 'login'));
    }

}
