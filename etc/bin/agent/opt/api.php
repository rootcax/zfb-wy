<?php
//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//本页面是整个用户的api请求接口，不存在视窗
class api{
    
    
    //手动请求api
    function order_api(){
        functions::drive('orderc')->request();
    }
    
    //设置成功订单api
    function take_api(){
        functions::drive('takec')->request();
    }
    
    //初始化模块代码：5
    //登录
    function login(){
        $this->csrf();
        functions::drive('agent')->login();
    }
    
     //初始化模块代码：1
    //短信验证码
    //返回代码大全
    //200 ： 成功
    //1001 ：验证码类型不正确
    //1002 : 手机不正确
    //1003 : 验证码发送频繁,请稍后再试
    //1004 : 验证码发送没有达到90秒
    function sms(){
        //短信类型 typec ： 1 为 注册验证码  2为找回密码验证码  3为 修改密码验证码
        $this->csrf();
        functions::drive('agent')->sms();
    }
    
    
    //csrf拦截
    private function csrf(){
        $csrf = functions::request('csrf');
        if (!empty($csrf) && $csrf != $_SESSION['csrf']) functions::json(-1, '页面失效,请刷新后再尝试');
    }
    
    //AI登录验证
    private function AI(){
        return functions::api('loginc')->AI('agent',functions::urlc('agent', 'index', 'login'));
    }
}

