<?php

class orderc {

    //批量删除
    function del($user) {
        $id = explode(",", functions::request('id'));
        if (empty($id))
            functions::urlx(functions::urlc('user', 'index', 'order'));
        $row = '';
        for ($i = 0; $i < count($id); $i++) {
            $row .= "id={$id[$i]} or ";
        }
        $row = trim(trim($row), 'or');
        $delete = functions::open_mysql()->delete('orders', "{$row} and userid={$user->sid}");
        functions::urlx(functions::urlc('user', 'index', 'order'));
    }

    //手动请求
    function request($user) {
        $mysql = functions::open_mysql();
        $orderId = intval(functions::request('id'));
        if ($user->parentid != 0) {
            $userid = $user->parentid;
        } else {
            $userid = $user->sid;
        }
        //判断是否是自己的订单
        $order = $mysql->query("orders", "id={$orderId} and userid={$userid}");
        //查询用户
        $users = $mysql->query("users", "id={$userid}");
        if (!is_array($order[0]))
            functions::json(7001, '账单错误');
        if ($order[0]['api_state'] == 2)
            functions::json(7003, '该订单已经请求过了');
//        if (!empty($order[0]['agentid'])) {
//            $poundage = functions::get_orderFee($order[0]['userid'], $order[0]['payc']);
//            $agent_poundage = functions::get_agentFee($order[0]['agentid'], $order[0]['payc']);
//        } else {
//            $poundage = functions::get_orderFee($order[0]['userid'], $order[0]['payc']);
//            $agent_poundage = 0;
//        }
//        $row = functions::api('reback')->request($mysql, $orderId, $poundage, $users[0],$agent_poundage);
        $row = functions::api('reback')->request($mysql, $orderId, $users[0]);
        if ($row) {
            functions::json(200, '请求成功');
        } else {
            functions::json(7002, '请求失败,请重试');
        }
    }

}
