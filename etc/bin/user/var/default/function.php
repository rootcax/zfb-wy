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
    
    //订单状态
    static function dforder_status($s) {
        if ($s == 110) {
            return '<span style="color:#039be5;">等待管理员处理..</span>';
        }
        if ($s == 111) {
            return '<span style="color:green;">已经处理</span>';
        }
        if ($s == 112) {
            return '<span style="color:#bdbdbd;">失败</span>';
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

    static function succ_rate($all_money, $total_success_order, $total_order) {
        $per = functions::_getFloat($total_success_order, $total_order, 1);
        return "<span style='color:green;'>" . $all_money . "元</span>/<span style='color:black;'>" . $total_success_order . "笔</span>/成功率<span style='color:red;'>" . $per . "</span>";
    }

    static function get_pollingmode($userid, $type) {
        $mysql = functions::open_mysql();
        $user = $mysql->query("users", "id={$userid}");
        $user = $user[0];
        if ($type == 26) {
            return intval($user['bank2alipay_polling']);
        }
    }

}
