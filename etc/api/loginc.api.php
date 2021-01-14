<?php

class loginc {

    public function AI($type, $login_url) {

        //用户数据
        $userfrom = json_decode(functions::encode($_SESSION['user'], AUTH_KEY, 2));
        //代理数据
        $agentfrom = json_decode(functions::encode($_SESSION['agent'], AUTH_KEY, 2));
        //管理员数据
        $adminfrom = json_decode(functions::encode($_SESSION['user_admin'], AUTH_KEY, 2));
        $mysql = functions::open_mysql();
        if ($type == 'user') {
            //检测状态
            if (count($userfrom) == 1) {
                //检测cookies是否过期
                if (time() - intval($userfrom->time) > 1800)
                    $this->out_user($login_url);
                $service = $mysql->query("users", "id={$userfrom->sid}");
                if (!is_array($service[0]))
                    functions::urlx($login_url);
                //检测token是否正常
                if ($userfrom->token != $service[0]['token'])
                    $this->out_user($login_url);
                //检测手机号是否正常
                if ($userfrom->phone != $service[0]['phone'])
                    $this->out_user($login_url);
                //检测ip是否异常
                //if ($userfrom->ip != $service[0]['ip'])
                //    $this->out_user($login_url);
                //全部正常后更新数据
                $userArray = $this->set($service[0]);
                return json_decode(functions::encode($userArray, AUTH_KEY, 2));
            }else {
                //没有登录
                $this->out_user($login_url);
            }
        }

        if ($type == 'agent') {
            //检测状态
            if (count($agentfrom) == 1) {
                //检测cookies是否过期
                if (time() - intval($agentfrom->time) > 1800)
                    $this->out_agent($login_url);
                $service = $mysql->query("agent", "id={$agentfrom->sid}");
                if (!is_array($service[0]))
                    functions::urlx($login_url);
                //检测token是否正常
                if ($agentfrom->token != $service[0]['token'])
                    $this->out_agent($login_url);
                //检测手机号是否正常
                if ($agentfrom->phone != $service[0]['phone'])
                    $this->out_agent($login_url);
                //检测ip是否异常
                if ($agentfrom->ip != $service[0]['ip'])
                    $this->out_agent($login_url);
                //全部正常后更新数据
                $agentArray = $this->set_agent($service[0]);
                return json_decode(functions::encode($agentArray, AUTH_KEY, 2));
            }else {
                //没有登录
                $this->out_agent($login_url);
            }
        }

        if ($type == 'visa_admin') {
            //检测状态
            if (count($adminfrom) == 1) {
                //检测cookies是否过期
                if (time() - intval($adminfrom->time) > 1800)
                    $this->out_admin($login_url);
                $service = $mysql->query("admin", "id={$adminfrom->sid}");
                if (!is_array($service[0]))
                    functions::urlx($login_url);
                //检测token是否正常
                if ($adminfrom->token != $service[0]['token'])
                    $this->out_admin($login_url);
                //检测手机号是否正常
                if ($adminfrom->username != $service[0]['username'])
                    $this->out_admin($login_url);
                //检测ip是否异常
                if ($adminfrom->ip != $service[0]['ip'])
                    $this->out_admin($login_url);
                //全部正常后更新数据

                $adminArray = $this->set_admin($service[0]);
                return json_decode(functions::encode($adminArray, AUTH_KEY, 2));
            }else {
                //没有登录
                $this->out_admin($login_url);
            }
        }

        if ($type == 'login') {
            if (count($userfrom) == 1) {

                functions::urlx($login_url);
            }
        }

        if ($type == 'login_agent') {
            if (count($agentfrom) == 1) {
                functions::urlx($login_url);
            }
        }


        if ($type == 'login_admin') {
            if (count($adminfrom) == 1) {
                functions::urlx($login_url);
            }
        }
    }

    public function out_user($login_url = NULL) {
        unset($_SESSION['user']);
        if ($login_url != NULL) {
            functions::urlx($login_url);
        }
    }

    public function out_agent($login_url = NULL) {
        unset($_SESSION['agent']);
        if ($login_url != NULL) {
            functions::urlx($login_url);
        }
    }

    public function out_admin($login_url = NULL) {
        unset($_SESSION['user_admin']);
        if ($login_url != NULL) {
            functions::urlx($login_url);
        }
    }

    public function set($user) {
        $_SESSION['user'] = functions::encode(json_encode(array(
                    "sid" => $user['id'],
                    "phone" => $user['phone'],
                    "token" => $user['token'],
                    "ip" => $user['ip'],
                    'time' => time(),
                    "balance" => $user['balance'],
                    "df_switch" => $user['df_switch'],
                    "memberCode" => $user['memberCode'],
                    "avatar" => $user['avatar'],
                    'keyid' => $user['keyid'],
                    'bank2alipay_withdraw' => $user['bank2alipay_withdraw'],
                    'bank' => json_decode($user['bank'], true),
                    'status' => $user['status'],
                    'agentid' => $user['agentid'],
                    'group_id' => $user['group_id'],
                    'parentid' => $user['parentid'],
                    'device_key' => str_replace("+", "@", functions::encode($user['token'], SERVER_KEY, 1))
                        )), AUTH_KEY, 1);
        return $_SESSION['user'];
    }

    public function set_agent($agent) {
        $_SESSION['agent'] = functions::encode(json_encode(array(
                    "sid" => $agent['id'],
                    "phone" => $agent['phone'],
                    "token" => $agent['token'],
                    "ip" => $agent['ip'],
                    'time' => time(),
                    'balance' => $agent['balance'],
                    "avatar" => $agent['avatar'],
                    'bank2alipay_withdraw' => $agent['bank2alipay_withdraw'],
                    'bank' => json_decode($agent['bank'], true),
                    'status' => $agent['status']
                        )), AUTH_KEY, 1);
        return $_SESSION['agent'];
    }

    public function set_admin($user) {
        $_SESSION['user_admin'] = functions::encode(json_encode(array(
                    "sid" => $user['id'],
                    "username" => $user['username'],
                    "token" => $user['token'],
                    "ip" => $user['ip'],
                    'time' => time(),
                    "avatar" => $user['avatar'],
                    "groupid" => $user['group_id'],
                    'status' => $user['status']
                        )), AUTH_KEY, 1);
        return $_SESSION['user_admin'];
    }

}
