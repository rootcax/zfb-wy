<?php
class orderc{
    //手动请求
    function request(){
        $mysql = functions::open_mysql();
        $orderId = intval(functions::request('id'));
        //判断是否是自己的订单
        $order = $mysql->query("orders","id={$orderId}");
        //查询用户
        $users = $mysql->query("users","id={$order[0]['userid']}");
        if (!is_array($order[0])) functions::json(7001, '账单错误');
        if ($order[0]['api_state'] == 2) functions::json(7003, '该订单已经请求过了');
        $row = functions::api('reback')->request($mysql,$orderId,$users[0]);
        if ($row) {
            functions::json(200, '请求成功');
        }else{
            functions::json(7002, '请求失败,请重试');
        }
    }
}