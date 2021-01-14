<?php
class loginc{
    public function AI($type,$login_url){
        $userfrom = json_decode(functions::encode($_SESSION['user'], AUTH_KEY,2));
        if ($type == 'user'){
            //检测状态
            if (count($userfrom) == 1){
                //检测cookies是否过期
                if (time()-intval($userfrom->time) > 1800) $this->out($login_url);
                $mysql = functions::open_mysql();
                $service = $mysql->query("users","id={$userfrom->sid}");
                if (!is_array($service[0])) functions::urlx($login_url);
                //检测token是否正常
                if ($userfrom->token != $service[0]['token']) $this->out($login_url);
                //检测手机号是否正常
                if ($userfrom->phone != $service[0]['phone']) $this->out($login_url);
                //检测ip是否异常
                if ($userfrom->ip != $service[0]['ip']) $this->out($login_url);
                //全部正常后更新数据
                $userArray = $this->set($service[0]);
                return json_decode(functions::encode($userArray, AUTH_KEY,2));
            }else{
                //没有登录
                $this->out($login_url);
            }
        }
        if ($type == 'login'){
            if (count($userfrom) == 1){
                functions::urlx($login_url);
            }
        }
    }
    public function out($login_url = NULL){
        unset($_SESSION['user']);
        if ($login_url != NULL){
            functions::urlx($login_url);
        }
    }
    public function set($user){
        $_SESSION['user'] = functions::encode(json_encode(array(
            "sid"=>$user['id'],
            "phone"=>$user['phone'],
            "token"=>$user['token'],
            "ip"=>$user['ip'],
            'time'=>time(),
            "balance"=>$user['balance'],
            "avatar"=>$user['avatar'],
            'keyid'=>$user['keyid']
        )), AUTH_KEY,1);
        return $_SESSION['user'];
    }
}