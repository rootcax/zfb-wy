<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//扩展库
class M {

    //检测支付类型
    static function payc($type) {
        if ($type == 26) {
            return '支付宝';
        }
    }

    //检测状态
    static function loginc($login) {
        if ($login == 0) {
            return '未登录';
        }
        if ($login == 1) {
            return '请求登录';
        }
        if ($login == 2) {
            return '正在登录';
        }
        if ($login == 3) {
            return '<span style="color:green;">在线</span>';
        }
        if ($login == 4) {
            return '<span style="color:red;">账号异常</span>';
        }
    }

    //订单状态
    static function takes_state($s) {
        if ($s == 1) {
            return '<span style="color:red;">未支付</span>';
        }
        if ($s == 2) {
            return '<span style="color:green;">已支付</span>';
        }
        if ($s == 3) {
            return '<span style="color:red;">订单超时</span>';
        }
    }

    static function quotac($requota) {
        if ($requota == -1) {
            return '<span style="color:green;">不限额</span>';
        }
        if (empty($requota)) {
            return '<span style="color:red;">0</span>';
        } else {
            return floatval($requota);
        }
    }

    static function used_quotac($requota, $quota) {
        if ($requota == -1) {
            return '<span style="color:green;">不限额</span>';
        }
        $used_quota = floatval($requota) - floatval($quota);
        if (empty($used_quota)) {

            return '<span style="color:red;">' . floatval($requota) . '</span>';
        } else {
            return floatval($quota);
        }
    }

    static function day_withdraw($landid) {
        $start_time = strtotime(date('Y-m-d', time()));
        $end_time = strtotime(date('Y-m-d 23:59:59', time()));
        $where = "create_time>=" . $start_time . " and create_time<=" . $end_time;
        $mysql = functions::open_mysql();
        //查询今日所有订单，以及总订单数量
        $order = $mysql->query("takes", "land_id={$landid} and {$where}", "count(1) as total,sum(money) as total_amount");
        $sum = intval($order[0]['total']);
        $sum_amount = intval($order[0]['total_amount']);
        $takes = $mysql->query("takes", "land_id={$landid} and state=2 and {$where}", "count(1) as total,sum(money) as total_amount");
        $suc_sum = intval($takes[0]['total']);
        $suc_sum_amount = intval($takes[0]['total_amount']);
        $per = functions::_getFloat($suc_sum, $sum, 1);
        return "<span style='color:green;'>" . $suc_sum_amount . "元</span>/<span style='color:black;'>" . $suc_sum . "笔</span>/成功率<span style='color:red;'>" . $per . "</span>";
    }

    static function yesterday_withdraw($landid) {
        $start_time = strtotime(date("Y-m-d")) - 86400;
        $end_time = strtotime(date('Y-m-d 23:59:59', time())) - 86400;
        $where = "create_time>=" . $start_time . " and create_time<=" . $end_time;
        $mysql = functions::open_mysql();
        //查询今日所有订单，以及总订单数量
        $order = $mysql->query("takes", "land_id={$landid} and {$where}", "count(1) as total,sum(money) as total_amount");
        $sum = intval($order[0]['total']);
        $sum_amount = intval($order[0]['total_amount']);
        $takes = $mysql->query("takes", "land_id={$landid} and state=2 and {$where}", "count(1) as total,sum(money) as total_amount");
        $suc_sum = intval($takes[0]['total']);
        $suc_sum_amount = intval($takes[0]['total_amount']);
        $per = functions::_getFloat($suc_sum, $sum, 1);
        return "<span style='color:green;'>" . $suc_sum_amount . "元</span>/<span style='color:black;'>" . $suc_sum . "笔</span>/成功率<span style='color:red;'>" . $per . "</span>";
    }

}
