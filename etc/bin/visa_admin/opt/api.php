<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//本页面是整个用户的api请求接口，不存在视窗
class api {

    //手动请求api
    function order_api() {
        $this->AI();
        functions::drive('orderc')->request();
    }

    //设置成功订单api
    function take_api() {
        $this->AI();
        functions::drive('takec')->request();
    }

    //初始化模块代码：5
    //登录
    function login() {
        $this->csrf();
        functions::drive('admin')->login();
    }

    //csrf拦截
    private function csrf() {
        $csrf = functions::request('csrf');
        if (!empty($csrf) && $csrf != $_SESSION['csrf'])
            functions::json(-1, '页面失效,请刷新后再尝试');
    }

    //AI登录验证
    private function AI() {
        return functions::api('loginc')->AI('visa_admin', functions::urlc('visa_admin', 'index', 'login'));
    }

    function updateWithdraw() {
        $this->AI();
        functions::drive('admin')->updateWithdraw();
    }

    function updateOrder() {
        $manager = $this->AI();
        functions::drive('admin')->updateOrder($manager);
    }

    //删除提现
    function deleteWithdraw() {
        $this->AI();
        functions::drive('admin')->updateWithdraw();
    }

}
