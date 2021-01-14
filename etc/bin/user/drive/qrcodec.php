<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00

class qrcodec {

    //添加二维码
    function add($user) {
        $land_id = intval(functions::request('land_id')); //收款账号id
        $mysql = functions::open_mysql();
        //检测收款账号是否存在
        $query = $mysql->query('land', "id={$land_id} and userid={$user->sid}");
        if (!is_array($query[0]))
            functions::msg('收款账号有误,请重新选择', functions::urlc('user', 'index', 'qrcode_add'));
        //$money = floatval(functions::request('money'));//二维码金额
        $money = 0;
        $money_res = 0;
        //检测金额是否与数据库中重复
        $money_query = $mysql->query('qrcode', "userid={$user->sid} and land_id={$land_id} and money={$money}");
        if (is_array($money_query[0]))
            functions::msg('该二维码和金额已存在,请重新上传其他金额的二维码', functions::urlc('user', 'index', 'qrcode_add'));
        //设置图片上传路径
        $dir = _public . 'cache/images/';
        if (!file_exists($dir))
            mkdir($dir); //创建目录

//上传api
        $upload = functions::api('upload')->run($_FILES['image'], $dir, array('jpg', 'png', 'jpeg'), 1024);
        $qrcode = $upload['new']; //二维码图片名称
        if (!is_array($upload))
            functions::msg('请选择二维码在上传!', functions::urlc('user', 'index', 'qrcode_add'));
        $link = functions::get_Config('webCog')['site'] . "public/cache/images/" . $qrcode;
        $qrcode_link = functions::qrcode($link);
        if ($qrcode_link == false || $qrcode_link == "" || $qrcode_link == null) {
            functions::msg('添加失败', functions::urlc('user', 'index', 'qrcode_add'));
        } else {
            if (strpos($qrcode_link, 'qr.alipay.com') === false && strpos($qrcode_link, 'QR.ALIPAY.COM') === false && strpos($qrcode_link, 'wxp://') === false && strpos($qrcode_link, 'WXP://') === false && strpos($qrcode_link, 'qr.95516.com') === false && strpos($qrcode_link, 'QR.95516.COM') === false && strpos($qrcode_link, 'gateway.starpos.com.cn') === false && strpos($qrcode_link, 'GATEWAY.STARPOS.COM.CN') === false && strpos($qrcode_link, 'qr.shouqianba.com') === false) {
                functions::msg('添加失败', functions::urlc('user', 'index', 'qrcode_add'));
            } else {
                if ($query[0]['typec'] == 17) {
                    $qrcode_sid = substr($qrcode_link, 30);
                }
                //写入数据库
                $add = $mysql->insert('qrcode', array(
                    'userid' => $user->sid,
                    'land_id' => $land_id,
                    'money' => $money,
                    'money_res' => $money_res,
                    'qrcode' => $qrcode,
                    'qrcode_link' => $qrcode_link,
                    'qrcode_sid' => $qrcode_sid,
                    'state' => 1,
                    'typec' => $query[0]['typec']
                ));
                if ($add > 0) {
                    functions::msg('添加成功', functions::urlc('user', 'index', 'qrcode_add'));
                } else {
                    functions::msg('添加失败', functions::urlc('user', 'index', 'qrcode_add'));
                }
            }
        }
    }

    //批量上传二维码
    function batch($user) {
        $land_id = intval(functions::request('land_id')); //收款账号id
        $mysql = functions::open_mysql();
        //检测收款账号是否存在
        $query = $mysql->query('land', "id={$land_id} and userid={$user->sid}");
        if (!is_array($query[0]))
            exit('收款账号不存在,请重新打开页面在进行上传');
        $money = floatval($_FILES['file']['name']); //二维码金额
        //检测金额是否与数据库中重复
        $money_query = $mysql->query('qrcode', "userid={$user->sid} and land_id={$land_id} and money={$money}");
        if (is_array($money_query[0]))
            exit('该二维码已经被上传过了,该二维码已中断上传');
        //设置图片上传路径
        $dir = _public . 'cache/images/';
        if (!file_exists($dir))
            mkdir($dir); //创建目录





            
//上传api
        $upload = functions::api('upload')->run($_FILES['file'], $dir, array('jpg', 'png', 'jpeg'), 1024);
        $qrcode = $upload['new']; //二维码图片名称
        if (!is_array($upload))
            exit('二维码数据异常,请检查后缀是否为图片');
        //写入数据库
        $add = $mysql->insert('qrcode', array(
            'userid' => $user->sid,
            'land_id' => $land_id,
            'money' => $money,
            'money_res' => intval($money),
            'qrcode' => $qrcode,
            'state' => 1,
            'typec' => $query[0]['typec']
        ));
        if ($add > 0) {
            exit('二维码图片上传成功，识别金额：' . $money . '，收款账号：' . $query[0]['username'] . '，绑定金额：' . intval($money));
        } else {
            exit('二维码图片上传失败,原因：写入数据库时出错');
        }
    }

    //更新二维码
    function edit($user) {
        $id = intval(functions::request('id')); //修改的id
        $mysql = functions::open_mysql();
        //$money = floatval(functions::request('money'));//二维码金额
        $money = 0;
        $money_res = 0;
        //得到id的数据
        $qrcode_query = $mysql->query('qrcode', "id={$id} and userid={$user->sid}");
        if (!is_array($qrcode_query[0]))
            functions::msg('信息有误,请重新操作', functions::urlc('user', 'index', 'qrcode_edit', array('id' => $id)));
        //检测金额是否与数据库中重复
        $money_query = $mysql->query('qrcode', "userid={$user->sid} and money={$money} and land_id={$qrcode_query[0]['land_id']}");

        if (is_array($money_query[0])) {
            if ($id != $money_query[0]['id']) {
                functions::msg('该二维码和金额已存在,请重新上传其他金额的二维码', functions::urlc('user', 'index', 'qrcode_edit', array('id' => $id)));
            }
        }

        //检测file是否为空
        $qrcode = null;
        if (!empty($_FILES['image']['name'])) {
            //设置图片上传路径
            $dir = _public . 'cache/images/';
            if (!file_exists($dir))
                mkdir($dir); //创建目录





                
//上传api
            $upload = functions::api('upload')->run($_FILES['image'], $dir, array('jpg', 'png', 'jpeg'), 1024);
            $qrcode = $upload['new']; //二维码图片名称
            if (!is_array($upload))
                functions::msg('二维码有误,请重新上传', functions::urlc('user', 'index', 'qrcode_edit', array('id' => $id)));
        }
        if ($qrcode != null) {
            $link = functions::get_Config('webCog')['site'] . "public/cache/images/" . $qrcode;
            $qrcode_link = functions::qrcode($link);
            //写入数据库
            $edit = $mysql->update('qrcode', array(
                'money' => $money,
                'money_res' => $money_res,
                'qrcode' => $qrcode,
                'qrcode_link' => $qrcode_link
                    ), "id={$id} and userid={$user->sid}");
        } else {
            //写入数据库
            $edit = $mysql->update('qrcode', array(
                'money' => $money,
                'money_res' => floatval(functions::request('money_res'))
                    ), "id={$id} and userid={$user->sid}");
        }
        functions::msg('修改成功', functions::urlc('user', 'index', 'qrcode_edit', array('id' => $id)));
    }

    //删除
    function del($user) {
        $id = intval(functions::request('id'));
        $delete = functions::open_mysql()->delete('qrcode', "id={$id} and userid={$user->sid}");
        functions::urlx(functions::urlc('user', 'index', 'qrcode'));
    }

    //删除链接
    function del_link($user) {
        $id = functions::request('id');
        $landid = functions::request('landid');
        $land = functions::open_mysql()->query('land', "id={$landid}");
        $delete = functions::open_mysql()->delete('qrcode_link', "money_res='{$id}' and land_id='{$landid}' and userid={$user->sid}");
        functions::urlx(functions::urlc('user', 'index', 'qrcode_link', array('id' => $landid, 'typec' => $land[0]['typec'])));
    }

    //删除链接
    function del_ch_link($user) {
        $id = functions::request('id');
        $landid = functions::request('landid');
        $land = functions::open_mysql()->query('land', "id={$landid}");
        if (empty($id))
            functions::urlx(functions::urlc('user', 'index', 'qrcode_link', array('id' => $landid, 'typec' => $land[0]['typec'])));
        //$row = '';
        //for ($i=0;$i<count($id);$i++){
        //    $row .= "money_res={$id[$i]} or ";
        //}
        //$row = trim(trim($row),'or');
        $delete = functions::open_mysql()->delete('qrcode_link', "money_res in ({$id}) and land_id='{$landid}' and userid={$user->sid}");
        functions::urlx(functions::urlc('user', 'index', 'qrcode_link', array('id' => $landid, 'typec' => $land[0]['typec'])));
    }

}
