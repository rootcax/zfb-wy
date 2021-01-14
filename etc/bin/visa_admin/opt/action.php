<?php

class action {

    //修改密码
    function edit() {
        $user = $this->AI();
        functions::drive('admin')->edit($user);
    }

    //删除收款订单
    function order_del() {
        $this->AI();
        $id = intval(functions::request('id'));
        functions::open_mysql()->delete("orders", "id={$id}");
        functions::urlx(functions::get_Config('webCog')['site'] . 'visa_admin.php?b=index&c=home');
    }

    //删除支付订单
    function takes_del() {
        $this->AI();
        $id = intval(functions::request('id'));
        if ($id == 1) {
            functions::json(-1, '当前用户不允许删除');
        }
        functions::open_mysql()->delete("takes", "id={$id}");
        functions::urlx(functions::get_Config('webCog')['site'] . 'visa_admin.php?b=index&c=takes');
    }

    //删除用户
    function manager_del() {
        $this->AI();
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $manager = $mysql->query("admin", "id={$id}");
        if (!is_array($manager[0]))
            functions::msg("当前账号不存在");
        if ($manager[0]['disdel'])
            functions::msg("当前账号不允许删除");
        $mysql->delete("admin", "id={$id}"); //删除用户组
        functions::urlx(functions::get_Config('webCog')['site'] . 'visa_admin.php?b=index&c=manager');
    }

    //添加管理员
    function manager_add() {
        $manager = $this->AI();
        $username = functions::request('username');
        $phone = functions::request('phone');
        $status = intval(functions::request('status'));
        $groupid = intval(functions::request('group_id'));
        if ($groupid == 1) {
            functions::json(2007, '权限不足');
        }
        $mysql = functions::open_mysql();
        $pwd = functions::request('pwd');
        if (!functions::ispwd($pwd))
            functions::json(2005, '密码输入错误,请输入6-26位的字符密码');
        //再次检测一次手机是否注册过了
        $queryx = $mysql->query('admin', "username='{$username}'");
        if (is_array($queryx[0]))
            functions::json(2007, '该管理员已经存在');
        //如果没被注册那么接着注册
        //生成token
        $token = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 12);
        //生成密码
        $pwd = md5($pwd . $token);

        $in = $mysql->insert('admin', array(
            "username" => $username,
            "pwd" => $pwd,
            "token" => $token,
            'phone' => $phone,
            'group_id' => $groupid,
            "ip" => functions::get_client_ip(),
            'loginc' => 0,
            'avatar' => '',
            'status' => $status
        ));
        if ($in) {
            functions::json(200, '添加管理员成功');
        } else {
            functions::json(2008, '添加管理员失败,请重试');
        }
    }

    //修改管理员
    function manager_edit() {
        $manager = $this->AI();
        $id = intval(functions::request('id'));
        $pwd = functions::request('pwd');
        $group_id = functions::request('group_id');
        $status = functions::request('status');
        $phone = functions::request('phone');
        if ($groupid == 1) {
            functions::json(2007, '权限不足');
        }
        $mysql = functions::open_mysql();
        //先查询该用户
        $query = $mysql->query("admin", "id={$id}");
        if (!is_array($query[0]))
            functions::json(-1, '修改失败,用户错误');
        //判断当前登录用户权限是否大于修改用户权限
        if ($manager->sid != $id) {
            if ($manager->groupid >= $query[0]['group_id']) {
                functions::json(-2, '当前用户权限不足');
            }
        }

        //if($manager['groupid']==2)
        //{
        //    functions::json(-2, '当前用户权限不足');
        //}
        if ($manager->groupid == 1) {
            $array = array("group_id" => $group_id, 'status' => $status, 'phone' => $phone);
        } else {
            if ($manager->sid != $id) {
                $array = array("group_id" => $group_id, 'phone' => $phone);
            } else {
                $array = array('phone' => $phone);
            }
        }
        if ($pwd != "" && $pwd != null) {
            //生成token
            $token = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 12);
            $pwd = md5($pwd . $token);
            $array['pwd'] = $pwd;
            $array['token'] = $token;
        }
        $up = $mysql->update("admin", $array, "id={$id}");
        functions::json(200, '用户更新成功');
    }

    //删除结算记录
    function withdraw_del() {
        $this->AI();
        $id = functions::request('id');
        $withdraw = functions::open_mysql()->query('withdraw', "id={$id}");
        if (!is_array($withdraw[0]))
            functions::json(-2, '当前记录不存在');
        $delete = functions::open_mysql()->delete('withdraw', "id ={$id}");
        functions::json(200, '操作完成,您已经将记录成功移除!');
    }

    //添加用户
    function user_add() {
    	$manager = $this->AI();
        $phone = functions::request('phone');
        $bank2alipay_withdraw = floatval(functions::request('bank2alipay_withdraw'));
        //$code = intval(functions::request('code'));
        if (!functions::isphone($phone))
            functions::json(2002, '手机号码不正确');
        $mysql = functions::open_mysql();
        //临时取消短信验证
        //$code_query = $mysql->query('codes',"code={$code} and phone={$phone} and typec=1");
        //if (!is_array($code_query[0]))  functions::json(2003, '验证码错误');
        //if (time()-$code_query[0]['ctime'] > $code_query[0]['survival']) functions::json(2004, '验证码已过期');
        $pwd = functions::request('pwd');
        $agent = functions::request('agent');
        $status = intval(functions::request('status'));
        if (!empty($agent) && !functions::isphone($agent))
            functions::json(-3, '代理手机号不正确');
        if (!functions::ispwd($pwd))
            functions::json(2005, '密码输入错误,请输入6-26位的字符密码');
        //再次检测一次手机是否注册过了
        $queryx = $mysql->query('users', "phone={$phone}");
        if (is_array($queryx[0]))
            functions::json(2007, '该手机已被注册');
        if (!empty($agent)) {
            $agent_query = $mysql->query("agent", "phone='{$agent}'");
            if (!is_array($agent_query[0]))
                functions::json(-2, '代理用户不存在');
            $agent_id = $agent_query[0]['id'];
        }
        else {
            $agent_id = 0;
        }
        //如果没被注册那么接着注册
        //生成token
        $token = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 12);
        //生成key
        $keyid = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 18);
        //生成密码
        $pwd = md5($pwd . $token);
        $config = functions::get_Config('registerCog');
        if (empty($bank2alipay_withdraw)) {
            $bank2alipay_withdraw = $config['bank2alipay_withdraw'];
        }
        if (!empty($agent)) {
            if ($bank2alipay_withdraw < $agent_query[0]['bank2alipay_withdraw']) {
                functions::json(2008, '商户费率需大于所属代理费率');
            }
        }
        $in = $mysql->insert('users', array(
            "phone" => $phone,
            "pwd" => $pwd,
            "token" => $token,
            "ip" => functions::get_client_ip(),
            "regc" => time(),
            'loginc' => 0,
            'balance' => 0,
            'avatar' => '',
            'keyid' => $keyid,
            'status' => $status,
            'agentid' => $agent_id,
            'bank2alipay_withdraw' => $bank2alipay_withdraw
        ));
        if ($in) {
            functions::json(200, '添加用户成功');
        } else {
            functions::json(2008, '添加用户失败,请重试');
        }
    }

    //修改用户
    function user_edit() {
        $manager = $this->AI();
        $id = intval(functions::request('id'));
        $pwd = functions::request('pwd');
        $money = functions::request('money');
        $status = functions::request('status');
        $agent = functions::request('agent');
        $bank2alipay_withdraw = floatval(functions::request('bank2alipay_withdraw'));
        if (!empty($agent) && !functions::isphone($agent))
            functions::json(-3, '代理手机号码不正确');
        $mysql = functions::open_mysql();
        //先查询该用户
        $query = $mysql->query("users", "id={$id}");
        if (!is_array($query[0]))
            functions::json(-1, '修改失败,用户错误');
        $group = intval($query[0]['group_id']);
        if (!empty($agent)) {
            //查询是否存在代理用户信息
            $agent_query = $mysql->query("agent", "phone='{$agent}'");
            if (!is_array($agent_query[0]))
                functions::json(-2, '代理用户不存在');
        }
        if (empty($bank2alipay_withdraw)) {
            $bank2alipay_withdraw = $query[0]['bank2alipay_withdraw'];
        }
        if (!empty($agent)) {
            if ($bank2alipay_withdraw < $agent_query[0]['bank2alipay_withdraw']) {
                functions::json(2008, '商户费率需大于所属代理费率');
            }
        }
        $array = array('status' => $status,
            'bank2alipay_withdraw' => $bank2alipay_withdraw
        );
        if ($manager->groupid == "1") {
            $array['balance'] = $money;
        }


        if (!empty($pwd)) {
            //修改密码和金额
            $pwd = md5($pwd . $query[0]['token']);
            $array['pwd'] = $pwd;
        }
        if (!empty($agent)) {
            $array['agentid'] = $agent_query[0]['id'];
        } else {
            $array['agentid'] = 0;
        }
        $mysql->update("users", $array, "id={$id}");
        functions::json(200, '用户更新成功');
    }

    //修改用户
    function user_customer_edit() {
        $manager = $this->AI();
        $id = intval(functions::request('id'));
        $pwd = functions::request('pwd');
        $status = functions::request('status');
        $mysql = functions::open_mysql();
        //先查询该用户
        $query = $mysql->query("users", "id={$id}");
        if (!is_array($query[0]))
            functions::json(-1, '修改失败,用户错误');
        $array = array('status' => $status);
        if (!empty($pwd)) {
            //修改密码和金额
            $pwd = md5($pwd . $query[0]['token']);
            $array['pwd'] = $pwd;
        }
        $mysql->update("users", $array, "id={$id}");
        functions::json(200, '用户更新成功');
    }

    //删除用户
    function user_del() {
        $this->AI();
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $mysql->delete("users", "id={$id}"); //删除用户组
        $mysql->delete('takes', "userid={$id}");
        $mysql->delete('orders', "userid={$id}");
        $mysql->delete('land', "userid={$id}");
        functions::urlx(functions::get_Config('webCog')['site'] . 'visa_admin.php?b=index&c=user');
    }

    //添加代理
    function agent_add() {
    	$manager = $this->AI();
        $phone = functions::request('phone');
        //$code = intval(functions::request('code'));
        if (!functions::isphone($phone))
            functions::json(2002, '手机不正确');
        $status = intval(functions::request('status'));
        $bank2alipay_withdraw = floatval(functions::request('bank2alipay_withdraw'));
        $mysql = functions::open_mysql();
        //临时取消短信验证
        //$code_query = $mysql->query('codes',"code={$code} and phone={$phone} and typec=1");
        //if (!is_array($code_query[0]))  functions::json(2003, '验证码错误');
        //if (time()-$code_query[0]['ctime'] > $code_query[0]['survival']) functions::json(2004, '验证码已过期');
        $pwd = functions::request('pwd');
        if (!functions::ispwd($pwd))
            functions::json(2005, '密码输入错误,请输入6-26位的字符密码');
        //再次检测一次手机是否注册过了
        $queryx = $mysql->query('agent', "phone={$phone}");
        if (is_array($queryx[0]))
            functions::json(2007, '该用户已存在');
        //如果没被注册那么接着注册
        //生成token
        $token = substr(md5(mt_rand(10000, 99999) . mt_rand(1000, 9999)), 0, 12);
        //生成密码
        $pwd = md5($pwd . $token);
        $config = functions::get_Config('agentCog');
        if (empty($bank2alipay_withdraw)) {
            $bank2alipay_withdraw = $config['bank2alipay_withdraw'];
        }
        $in = $mysql->insert('agent', array(
            "phone" => $phone,
            "pwd" => $pwd,
            "token" => $token,
            "ip" => functions::get_client_ip(),
            "regc" => time(),
            'loginc' => 0,
            'balance' => 0,
            'avatar' => '',
            'status' => $status,
            'bank2alipay_withdraw' => $bank2alipay_withdraw
        ));
        if ($in) {
            functions::json(200, '添加代理成功');
        } else {
            functions::json(2008, '添加代理失败,请重试');
        }
    }

    //修改代理
    function agent_edit() {
        $manager = $this->AI();
        $id = intval(functions::request('id'));
        $pwd = functions::request('pwd');
        $status = intval(functions::request('status'));
        $bank2alipay_withdraw = floatval(functions::request('bank2alipay_withdraw'));
        $mysql = functions::open_mysql();
        //先查询该用户
        $query = $mysql->query("agent", "id={$id}");
        if (!is_array($query[0]))
            functions::json(-1, '修改失败,用户错误');
        if (empty($bank2alipay_withdraw)) {
            $bank2alipay_withdraw = $query[0]['bank2alipay_withdraw'];
        }
        $array = array('status' => $status,
            'bank2alipay_withdraw' => $bank2alipay_withdraw
        );
        if ($manager->groupid == "1") {
            $money = functions::request('money');
            $array['balance'] = $money;
        }
        if (!empty($pwd)) {
            //修改密码和金额
            $pwd = md5($pwd . $query[0]['token']);
            $array['pwd'] = $pwd;
        }
        $bank_type = intval(functions::request('bank_type'));
        if ($bank_type == 1) {
            //支付宝
            $alipay_name = functions::request('alipay_name');
            //账号
            $alipay_content = functions::request('alipay_content');
            if (empty($alipay_name) || empty($alipay_content))
                functions::json(-1, '支付宝姓名或账号不能为空!');
            //写入
            $array['bank'] = json_encode(['type' => 1, 'name' => $alipay_name, 'card' => $alipay_content], JSON_UNESCAPED_UNICODE);
        }
        if ($bank_type == 2) {
            //姓名
            $bank_name = functions::request('bank_name');
            //银行名称
            $bank = functions::request('bank');
            //账号
            $card = functions::request('card');
            if (empty($bank_name) || empty($bank) || empty($card))
                functions::json(-1, '银行卡信息有误,请填写正确!');
            $array['bank'] = json_encode(['type' => 2, 'name' => $bank_name, 'card' => $card, 'bank' => $bank], JSON_UNESCAPED_UNICODE);
        }
        $mysql->update("agent", $array, "id={$id}");
        functions::json(200, '用户更新成功');
    }

    //删除代理
    function agent_del() {
        $this->AI();
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $data = $mysql->query("users", "agentid={$id}");
        if (is_array($data[0])) {
            $mysql->update('users', array('agentid' => 0), "agentid={$id}");
        }
        $mysql->delete("agent", "id={$id}"); //删除用户组
        functions::urlx(functions::get_Config('webCog')['site'] . 'visa_admin.php?b=index&c=agent');
    }

    //添加新闻
    function news_add() {
    	$manager = $this->AI();
        $content = functions::request('content');
        $title = functions::request('title');
        $timec = time();
        $mysql = functions::open_mysql();
        $in = $mysql->insert('news', array(
            "title" => $title,
            "contents" => $content,
            "timec" => $timec
        ));
        if ($in) {
            functions::json(200, '添加成功');
        } else {
            functions::json(2008, '添加失败,请重试');
        }
    }

    //修改新闻
    function news_edit() {
    	$manager = $this->AI();
        $id = intval(functions::request('id'));
        $content = functions::request('content');
        $title = functions::request('title');
        $timec = time();
        $mysql = functions::open_mysql();
        $in = $mysql->update('news', array(
            "title" => $title,
            "contents" => $content,
            "timec" => $timec
                ), "id={$id}");
        if ($in) {
            functions::json(200, '修改成功');
        } else {
            functions::json(2008, '修改失败,请重试');
        }
    }

    //删除新闻
    function news_del() {
        $this->AI();
        $id = intval(functions::request('id'));
        $mysql = functions::open_mysql();
        $mysql->delete("news", "id={$id}"); //删除用户组
        functions::urlx(functions::get_Config('webCog')['site'] . 'visa_admin.php?b=index&c=news');
    }

    function bank_memo_add() {
        $manager = $this->AI();
        $bank_id = intval(functions::request('bank_id'));
        $singleQuota = trim(functions::request("singleQuota"));
        $dailyQuota = trim(functions::request("dailyQuota"));
        $memo = trim(functions::request("memo"));
        $sortId = intval(functions::request("sortId"));
        if (empty($bank_id)) {
            functions::json(-1, "银行信息有误");
        }
        $inData = [
            "bank_id" => $bank_id,
            "singleQuota" => $singleQuota,
            "dailyQuota" => $dailyQuota,
            "memo" => $memo,
            "sortId" => $sortId,
            "create_time" => time()
        ];
        $mysql = functions::open_mysql();
        $in = $mysql->insert("bank_memo", $inData);
        if ($in) {
            functions::json(200, "添加成功");
        }
    }

    //修改环境配置
    function withdraw_config() {
        $this->AI();
        //写入配置
        $drawcount = intval(functions::request('drawcount'));
        $min_payment = floatval(functions::request('min_payment'));
        $max_payment = floatval(functions::request('max_payment'));
        $drwaFee = floatval(functions::request('drwaFee'));
        $cycle = intval(functions::request('cycle'));
        $withdrawCog = array(
            'drawcount' => $drawcount,
            'min_payment' => $min_payment,
            'max_payment' => $max_payment,
            'drwaFee' => $drwaFee,
            'cycle' => $cycle
        );
        $mysql = functions::open_mysql();
        $Cogc = $mysql->query('config', "name='withdrawCog'");
        if (is_array($Cogc[0])) {
            //更改
            $mysql->update("config", array('value' => json_encode($withdrawCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), "id={$Cogc[0]['id']}");
        } else {
            //插入
            $mysql->insert("config", array('name' => 'withdrawCog', 'value' => json_encode($withdrawCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        }
        functions::json(200, '环境更新成功');
    }

    //修改代付配置
    function df_config() {
        $this->AI();
        //写入配置
        $min_payment = floatval(functions::request('min_payment'));
        $max_payment = floatval(functions::request('max_payment'));
        $drwaFee = floatval(functions::request('drwaFee'));
        $fee_mode = intval(functions::request('fee_mode'));
        $dfCog = array(
            'min_payment' => $min_payment,
            'max_payment' => $max_payment,
            'drwaFee' => $drwaFee,
            'fee_mode' => $fee_mode
        );
        $mysql = functions::open_mysql();
        $Cogc = $mysql->query('config', "name='dfCog'");
        if (is_array($Cogc[0])) {
            //更改
            $mysql->update("config", array('value' => json_encode($dfCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), "id={$Cogc[0]['id']}");
        } else {
            //插入
            $mysql->insert("config", array('name' => 'dfCog', 'value' => json_encode($dfCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        }
        functions::json(200, '环境更新成功');
    }

    //修改环境配置
    function web_config() {
        $this->AI();
        //写入配置
        $title = functions::xss(functions::request('title'));
        $site = functions::xss(functions::request('site'));
        $keywords = functions::xss(functions::request('keywords'));
        $description = functions::xss(functions::request('description'));
        $theme = functions::xss(functions::request('theme'));
        $max_money = floatval(functions::request('max_money'));
        $min_money = floatval(functions::request('min_money'));
        $webCog = array(
            'site' => $site,
            'title' => $title,
            'keywords' => $keywords,
            'description' => $description,
            'theme' => $theme,
            'max_money' => $max_money,
            'min_money' => $min_money
        );
        $mysql = functions::open_mysql();
        $Cogc = $mysql->query('config', "name='webCog'");
        if (is_array($Cogc[0])) {
            //更改
            $mysql->update("config", array('value' => json_encode($webCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), "id={$Cogc[0]['id']}");
        } else {
            //插入
            $mysql->insert("config", array('name' => 'webCog', 'value' => json_encode($webCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        }
        functions::json(200, '环境更新成功');
    }

    //修改环境配置
    function sms_config() {
        $this->AI();
        //写入配置
        $accessKeyId = functions::request('accessKeyId');
        $accessKeySecret = functions::request('accessKeySecret');
        $SignName = functions::request('SignName');
        $TemplateCode = functions::request('TemplateCode');
        $Abnormal = functions::request('Abnormal');
        $register_sms = intval(functions::request('register_sms'));
        //$login_sms = intval(functions::request('login_sms'));
        $landadd_sms = intval(functions::request('landadd_sms'));
        $landedit_sms = intval(functions::request('landedit_sms'));
        $landdel_sms = intval(functions::request('landdel_sms'));
        $withdraw_sms = intval(functions::request('withdraw_sms'));
        $smsCog = array(
            'accessKeyId' => $accessKeyId,
            'accessKeySecret' => $accessKeySecret,
            'SignName' => $SignName,
            'TemplateCode' => $TemplateCode,
            'Abnormal' => $Abnormal,
            'register_sms' => $register_sms,
            //'login_sms'=>$login_sms,
            'landadd_sms' => $landadd_sms,
            'landedit_sms' => $landedit_sms,
            'landdel_sms' => $landdel_sms,
            'withdraw_sms' => $withdraw_sms
        );
        $mysql = functions::open_mysql();
        $Cogc = $mysql->query('config', "name='smsCog'");
        if (is_array($Cogc[0])) {
            //更改
            $mysql->update("config", array('value' => json_encode($smsCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), "id={$Cogc[0]['id']}");
        } else {
            //插入
            $mysql->insert("config", array('name' => 'smsCog', 'value' => json_encode($smsCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        }
        functions::json(200, '环境更新成功');
    }

    //修改环境配置
    function reg_config() {
        $this->AI();
        //写入配置
        $balance = functions::request('balance');
        $bank2alipay_withdraw = floatval(functions::request('bank2alipay_withdraw'));
        $registerCog = array(
            'balance' => $balance,
            'bank2alipay_withdraw' => $bank2alipay_withdraw
        );
        $mysql = functions::open_mysql();
        $Cogc = $mysql->query('config', "name='registerCog'");
        if (is_array($Cogc[0])) {
            //更改
            $mysql->update("config", array('value' => json_encode($registerCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), "id={$Cogc[0]['id']}");
        } else {
            //插入
            $mysql->insert("config", array('name' => 'registerCog', 'value' => json_encode($registerCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        }
        functions::json(200, '环境更新成功');
    }

    //修改环境配置
    function agent_config() {
        $this->AI();
        //写入配置
        $bank2alipay_withdraw = floatval(functions::request('bank2alipay_withdraw'));
        $agentCog = array(
            'bank2alipay_withdraw' => $bank2alipay_withdraw
        );
        $mysql = functions::open_mysql();
        $Cogc = $mysql->query('config', "name='agentCog'");
        if (is_array($Cogc[0])) {
            //更改
            $mysql->update("config", array('value' => json_encode($agentCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), "id={$Cogc[0]['id']}");
        } else {
            //插入
            $mysql->insert("config", array('name' => 'agentCog', 'value' => json_encode($agentCog, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)));
        }
        functions::json(200, '环境更新成功');
    }

    //更改收款账户状态
    function updateSwitch() {
        $manager = $this->AI();
        $id = intval(functions::request('id'));
        $status = intval(functions::request('status'));
        $mysql = functions::open_mysql();
        $user = $mysql->select("select * from mi_users where id={$id}");
        if (empty($user)) {
            functions::json(-1, "用户不存在");
        }
        if ($status == 2) {
            if (empty($user[0]['memberCode'])) {
                $memberCode = "100" . date("ymd") . mt_rand(1000, 9999);
                $upData['memberCode'] = $memberCode;
                $upData['df_switch'] = $status;
            } else {
                $upData['df_switch'] = $status;
            }
        } else {
            $upData['df_switch'] = $status;
        }

        $query = $mysql->update('users', $upData, "id={$id}");
        if ($query > 0) {
            if ($status == 2) {
                functions::json("200", '开启成功');
            } else {
                functions::json("200", '关闭成功');
            }
        } else {
            functions::json(-1, '系统错误，请联系管理员');
        }
    }

    //修改用户余额
    function user_balance_edit() {
        $manager = $this->AI();
        if ($manager->groupid != "1")
            functions::json(-1, '对不起，权限不足');
        $user_id = intval(functions::request('user_id'));
        $mode = intval(functions::request("mode"));
        $amount = intval(functions::request("amount"));
        if (empty($amount)) {
            functions::json(-3, '请输入要调整的金额');
        }
        $mysql = functions::open_mysql();
        $user = $mysql->query("users", "id={$user_id}");
        if (empty($user)) {
            functions::json(-2, '用户不存在');
        }
        $user = $user[0];
        $before = $user['balance'];
        if ($mode == 1) {
            $after = ($before * 1000 + $amount * 1000) / 1000;
        } else {
            if ($amount > $before) {
                functions::json("-5", "该账号余额" . $before . "，扣减金额要小于" . $before);
            }
            $after = ($before * 1000 - $amount * 1000) / 1000;
        }
        $userRes = $mysql->update("users", array("balance" => $after, 'update_time' => time()), "id={$user_id}");
        if ($userRes === false) {
            functions::json(-4, '更新用户余额失败');
        }

//        if ($mode == 1) {
//            $operate = "增加";
//        } else {
//            $operate = "减少";
//        }
//        $descript = "管理员" . $manager->name . $operate . $amount;
//        //商户余额记录表
//        $addData = [
//            'type' => $mode, // 类型 1充值 2扣除
//            'scene' => 2, // 场景 1交易订单 2管理员操作 3商户操作 4提现
//            'user_id' => $user_id,
//            'before_money' => $before,
//            'money' => $amount,
//            'after_money' => $after,
//            'source_table' => 'users',
//            'data_id' => $user_id,
//            'descript' => $descript,
//            'create_time' => time()
//        ];
//        $userRecRes = $mysql->insert("users_balance_records", $addData);
//        if (!$userRecRes) {
//            functions::json(-6, '用户流水录入失败');
//        }
        //增加管理员日志
        functions::json(200, '操作成功');
    }

    //cookies验证
    private function AI() {
        //检测是否管理员
        return functions::api('loginc')->AI('visa_admin', functions::urlc('visa_admin', 'index', 'login'));
    }

    //注销登录
    function out() {
        unset($_SESSION['user_admin']);
        functions::urlx(functions::urlc('visa_admin', 'index', 'login'));
    }

}
