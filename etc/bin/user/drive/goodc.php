<?php

class goodc {

    //添加账号
    function add($user) {
        $title = functions::request('title');
        $money = floatval(functions::request('money'));
        $max_money = floatval(functions::request('max_money'));
        $min_money = floatval(functions::request('min_money'));
        $category1 = functions::request('category1');
        $category2 = functions::request('category2');
        $category3 = functions::request('category3');
        $pictures_1 = functions::request('pictures_1');
        $pictures_2 = functions::request('pictures_2');
        $pictures_3 = functions::request('pictures_3');
        $content = functions::request('content');
        $status = intval(functions::request("status"));
        $mysql = functions::open_mysql();
        $insert = $mysql->insert('goods', array(
            'userid' => $user->sid,
            'title' => $title,
            'money' => $money,
            'max_money' => $max_money,
            'min_money' => $min_money,
            'category1' => $category1,
            'category2' => $category2,
            'category3' => $category3,
            'pictures_1' => $pictures_1,
            'pictures_2' => $pictures_2,
            'pictures_3' => $pictures_3,
            'content' => $content,
            'status' => $status,
            'create_time' => time()
        ));
        if ($insert > 0) {
            functions::json(200, '添加成功');
        } else {
            functions::json(6004, '系统错误!请联系管理员!');
        }
    }

    //修改账号
    function edit($user) {
        $id = intval(functions::request('id'));
        $title = functions::request('title');
        $money = floatval(functions::request('money'));
        $max_money = floatval(functions::request('max_money'));
        $min_money = floatval(functions::request('min_money'));
        $category1 = functions::request('category1');
        $category2 = functions::request('category2');
        $category3 = functions::request('category3');
        $pictures_1 = functions::request('pictures_1');
        $pictures_2 = functions::request('pictures_2');
        $pictures_3 = functions::request('pictures_3');
        $content = functions::request('content');
        $status = intval(functions::request("status"));
        $mysql = functions::open_mysql();
        $query = $mysql->query('goods', "id={$id} and userid={$user->sid}");

        if (!is_array($query[0]))
            functions::json(6003, '修改失败');
        $array = array('title' => $title, 'money' => $money, 'max_money' => $max_money, 'min_money' => $min_money, 'category1' => $category1, 'category2' => $category2, 'category3' => $category3, 'pictures_1' => $pictures_1, 'pictures_2' => $pictures_2, 'pictures_3' => $pictures_3, 'content' => $content, "status" => $status, "update_time" => time());
        $update = $mysql->update('goods', $array, "id={$id} and userid={$user->sid}");
        if ($update > 0) {
            functions::json(200, '修改成功');
        } else {
            functions::json(-1, '修改失败');
        }
    }

    //删除商品
    function del($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $query = $mysql->query('goods', "id={$id} and userid={$user->sid}");
        if (!is_array($query[0]))
            functions::json(-1, '系统错误,请联系管理员!');
        //删除账号库
        $mysql->delete('goods', "id={$id} and userid={$user->sid}");
        functions::json(200, '删除成功');
    }

    //查询登录状态
    function query_login($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $query = $mysql->query('land', "id={$id} and userid={$user->sid}", "id,userid,username,typec,login,image,ban");
        if (is_array($query[0])) {
            if ($query[0]['ban'] == 1) {
                functions::json(200, '查询成功', $query[0]);
            } else {
                functions::json(6003, '该收款账号已被禁止登录,如有疑问,请联系客服');
            }
        } else {
            functions::json(6004, '查询失败');
        }
    }

    //登录
    function login($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $query = $mysql->query('land', "id={$id} and userid={$user->sid}", "id,userid,username,typec,login,image,ban");
        if (is_array($query[0])) {
            if ($query[0]['ban'] != 1)
                functions::json(6002, '该收款账号已被禁止登录,如有疑问,请联系客服');
            $update = $mysql->update('land', array('login' => 1, 'image' => 0, 'timec' => time()), "id={$id} and userid={$user->sid}");
            if ($update > 0) {
                functions::json(200, '正在登录,请稍后');
            } else {
                functions::json(6003, '请求登录失败');
            }
        } else {
            functions::json(6004, '请求登录失败');
        }
    }

    //开启监听
    function listen($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $query = $mysql->query('land', "id={$id} and userid={$user->sid}", "id,userid,username,typec,login,image,ban");
        if (is_array($query[0])) {
            if ($query[0]['ban'] != 1)
                functions::json(6002, '该收款账号已被禁止操作,如有疑问,请联系客服');
            if ($query[0]['login'] != 3)
                functions::json(6003, '该收款账号还未登录,请登录后在开启监控');
            if ($user->balance <= 0)
                functions::json(6004, '您的账号余额为0.00元,请充值后在进行监控');
            $update = $mysql->update('land', array('onback' => 1), "id={$id} and userid={$user->sid}");
            if ($update > 0) {
                functions::json(200, '启动成功');
            } else {
                functions::json(6005, '启动失败');
            }
        } else {
            functions::json(6006, '启动有误');
        }
    }

    //停止监控
    function stop($user) {
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $query = $mysql->query('land', "id={$id} and userid={$user->sid}", "id,userid,username,typec,login,image,ban,onback");
        if (is_array($query[0])) {
            if ($query[0]['ban'] != 1)
                functions::json(6002, '该收款账号已被禁止操作,如有疑问,请联系客服');
            if ($query[0]['onback'] != 1)
                functions::json(6003, '该账号未启动监控,无需关闭');
            $update = $mysql->update('land', array('onback' => 2), "id={$id} and userid={$user->sid}");
            if ($update > 0) {
                functions::json(200, '关闭成功');
            } else {
                functions::json(6004, '关闭失败');
            }
        } else {
            functions::json(6005, '关闭有误');
        }
    }

    function updateStatus($user) {
        $id = intval(functions::request('id'));
        $status = intval(functions::request('status'));
        $mysql = functions::open_mysql();
        $query = $mysql->update('land', array('status' => $status, 'isClosed' => 0, 'overtimes' => 0), "id={$id}");
        if ($query > 0) {
            if ($status) {
                functions::json("200", '开启成功');
            } else {
                functions::json("200", '关闭成功');
            }
        } else {
            functions::json(-1, '系统错误，请联系管理员');
        }
    }

}
