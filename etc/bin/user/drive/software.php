<?php
class software{
    
    //windows版软件下载
    function windows($user){
        $land_id = intval(functions::request('land_id'));
        //电脑id
        $id = functions::xss(functions::request('id'));
        $mysql = functions::open_mysql();
        //查询收款账号是否正确
        $land = $mysql->query('land',"id={$land_id} and userid={$user->sid}");
        if (!is_array($land[0])) functions::json(-1, '发生错误,请刷新页面重新尝试');
        //写入电脑id
        $update_equipment = $mysql->update("land", array("equipment"=>$id,"login"=>2,"timec"=>0),"id={$land[0]['id']}");
        if ($land[0]['typec'] == 1) $url = functions::get_Config('webCog')['site'] . 'version/alipay.exe';
        if ($land[0]['typec'] == 2) $url = functions::get_Config('webCog')['site'] . 'version/wechat.exe';
        if ($land[0]['typec'] == 3) $url = functions::get_Config('webCog')['site'] . 'version/tenpay.exe';
        functions::json(200, '已准备就绪,请稍后..',array('downurl'=>$url));
    }
    
    //生成key
    function GeneratingKey($user){
        $keyid = substr(md5(mt_rand(10000,99999) . mt_rand(1000,9999)), 0,18);
        $mysql = functions::open_mysql();
        $update_Key = $mysql->update("users", array("keyid"=>$keyid),"id={$user->sid}");
        if ($update_Key > 0){
            functions::json(200, '已成功重新生成',array('key'=>$keyid));
        }else{
            functions::json(-1, '发生错误,生成失败');
        }
    }
    
}