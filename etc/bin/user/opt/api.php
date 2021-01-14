<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//本页面是整个用户的api请求接口，不存在视窗
class api {

    //初始化模块代码：2
    //返回代码大全
    //2002 ： 手机不正确
    //2003 ： 验证码错误
    //2004 : 验证码已过期
    //注册账号api请求，接受POST,GET值
    function register() {
        $this->csrf();
        functions::drive('users')->register();
    }

    //初始化模块代码：1
    //短信验证码
    //返回代码大全
    //200 ： 成功
    //1001 ：验证码类型不正确
    //1002 : 手机不正确
    //1003 : 验证码发送频繁,请稍后再试
    //1004 : 验证码发送没有达到90秒
    function sms() {
        //短信类型 typec ： 1 为 注册验证码  2为找回密码验证码  3为 修改密码验证码
        $this->csrf();
        functions::drive('users')->sms();
    }

    //初始化模块代码：3
    function forget() {
        $this->csrf();
        functions::drive('users')->forget();
    }

    //初始化模块代码：4
    //验证码
    function imagec() {
        $this->csrf();
        $typec_array = array('login');
        $typec = functions::request('typec');
        if (!in_array($typec, $typec_array))
            functions::json(4001, '类型错误');
        functions::api('secoder')->entry($typec);
    }

    //初始化模块代码：5
    //登录
    function login() {
        $this->csrf();
        functions::drive('users')->login();
    }

    function key_edit() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('users')->key_edit($user);
    }

    //初始化模块代码：6
    //添加收款账号
    function land_add() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('landc')->add($user);
    }

    //修改收款账号
    function land_edit() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('landc')->edit($user);
    }

    //删除收款账号
    function land_del() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('landc')->del($user);
    }

    //添加商品
    function good_add() {
        //$this->csrf();
        $user = $this->AI();
        functions::drive('goodc')->add($user);
    }

    //修改商品
    function good_edit() {
        //$this->csrf();
        $user = $this->AI();
        functions::drive('goodc')->edit($user);
    }

    //删除商品
    function good_del() {
        //this->csrf();
        $user = $this->AI();
        functions::drive('goodc')->del($user);
    }

    //查询登录状态
    function land_login() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('landc')->query_login($user);
    }

    //置登录状态
    function land_login_typec() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('landc')->login($user);
    }

    //开启监控
    function start_listen() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('landc')->listen($user);
    }

    //停止监控
    function stop_listen() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('landc')->stop($user);
    }

    //手动请求api
    function order_api() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('orderc')->request($user);
    }

    //设置成功订单api
    function take_api() {
        $this->csrf();
        $user = $this->AI();
        if ($user->parentid != 0) {
            functions::json(-1, "当前用户不允许操作");
        }
        functions::drive('takec')->request($user);
    }

    //设置成功订单api
    function ab_orders_api() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('ab_orderc')->request($user);
    }

    //更改收款账户状态
    function updateStatus() {
        $user = $this->AI();
        functions::drive('landc')->updateStatus($user);
    }

    //更改支付宝收款模式
    function updateTransfer() {
        $user = $this->AI();
        functions::drive('landc')->updateTransfer($user);
    }

    //更改收款账户轮询模式
    function updatePollMode() {
        $user = $this->AI();
        functions::drive('users')->updatePollMode($user);
    }

    //生成key
    function GeneratingKey() {
        $this->csrf();
        $user = $this->AI();
        functions::drive('software')->GeneratingKey($user);
    }

    //csrf拦截
    private function csrf() {

        $csrf = functions::request('csrf');
        if (!empty($csrf) && $csrf != $_SESSION['csrf'])
            functions::json(-1, '页面失效,请刷新后再尝试');
    }

    //AI登录验证
    private function AI() {
        return functions::api('loginc')->AI('user', functions::urlc('user', 'index', 'login'));
    }

}
