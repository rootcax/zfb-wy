<?php


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

    static function bank_type($bank_type) {
        if ($bank_type == 1) {
            return 'PC';
        }
        if ($bank_type == 2) {
            return 'H5';
        }
        if ($bank_type == 3) {
            return '<span style="color:green;">PC/H5</span>';
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
            return '订单超时';
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

    //管理员用户组
    static function group_id($group) {
        if ($group == 1) {
            return '超级管理员';
        }
        if ($group == 2) {
            return '管理员';
        }
        if ($group == 3) {
            return '客服';
        }
    }

    static function agent_phone($agentid) {
        if (empty($agentid)) {
            return "暂无代理";
        } else {
            $mysql = functions::open_mysql();
            $agent = $mysql->query("agent", "id={$agentid}");
            return $agent[0]['phone'];
        }
    }

    static function user_phone($userid) {
        if (empty($userid)) {
            return "暂无上级";
        } else {
            $mysql = functions::open_mysql();
            $user = $mysql->query("users", "id={$userid}");
            return $user[0]['phone'];
        }
    }

}
