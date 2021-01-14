<?php

class index {

    //登录
    function login() {
        //检测是否管理员
        functions::api('loginc')->AI('login_agent',functions::urlc('agent', 'index', 'home'));
        functions::import('login');
    }

    //home界面
    function home(){
        $user = $this->AI();
        functions::import('home',array("user"=>$user));
    }
    
    //edit
    function edit() {
        $this->AI();
        $query = functions::open_mysql()->query('users', "id={$id}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import('user_edit', array('data' => $query[0]));
    }
    
    //order
    function order() {
        $user=$this->AI();
        $userid = intval(functions::request('userid'));
        $payc = intval(functions::request('payc'));
        $mark = trim(functions::request('mark'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if($start_time=="")
        {
            $start_time = strtotime(date('Y-m-d',time()));
        }
        else
        {
            $start_time = strtotime($start_time);
        }
        if($end_time=="")
        {
            $end_time = strtotime(date('Y-m-d',time()))+86399;
        }
        else
        {
            $end_time = strtotime($end_time);
        }
        $where = "type=0 and agentid=".$user->sid." and order_time>=".$start_time." and order_time<=".$end_time;
        if (!empty($userid))
        {
            $userid = $userid-10000;
            $where = $where . " and userid=" . $userid;
        }
        if (!empty($payc))
            $where = $where . ' and payc=' . $payc;
        $num = trim(functions::request('num'));
        if (!empty($num)) $where = $where." and num='" . $num."'";
        if(!empty($mark)) $where = $where." and remark='" . $mark."'";
        functions::import('order', array('data' => functions::drive('query')->column('orders', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    //take
    function takes() {
        $user = $this->AI();
        //关键词查询
        $userid = intval(functions::request('userid'));
        $payc = intval(functions::request('payc'));
        $mark = trim(functions::request('mark'));
        $state = intval(functions::request('state'));
        $info = trim(functions::request('info'));
        $num = trim(functions::request('num'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if($start_time=="")
        {
            $start_time = strtotime(date('Y-m-d',time()));
        }
        else
        {
            $start_time = strtotime($start_time);
        }
        if($end_time=="")
        {
            $end_time = strtotime(date('Y-m-d',time()))+86399;
        }
        else
        {
            $end_time = strtotime($end_time);
        }
        $where = "type=0 and agentid=".$user->sid." and create_time>=".$start_time." and create_time<=".$end_time;
        if (!empty($userid))
        {
            $userid = $userid-10000;
            $where = $where . " and userid=" . $userid;
        } 
        if (!empty($payc))
            $where = $where . ' and payc=' . $payc;
        if (!empty($num))
            $where = $where . " and num='" . $num . "'";
        if (!empty($mark))
            $where = $where . " and mark='" . $mark . "'";
        if (!empty($state))
            $where = $where . ' and state=' . $state;
        if (!empty($info))
            $where = $where . ' and info=' . $info;
        functions::import('takes', array('data' => functions::drive('query')->column('takes', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    //user
    function user() {
        $agent = $this->AI();
        //关键词查询
        $userid = intval(functions::request('userid'));
        $phone = functions::request('num');
        $status = intval(functions::request('status'));
        $where = "agentid={$agent->sid}";
        if (!empty($userid))
        {
            $userid = $userid-10000;
            $where = $where . " and id=" . $userid;
        }
        if (!empty($phone))
        {
            $where = $where . " and phone='" . $phone."'";
        }
        if (!empty($status))
            $where = $where . ' and status=' . $status;
        functions::import('user', array('data' => functions::drive('query')->column('users', "{$where}", array('num' => intval(functions::request('page')), 'all' => 15), null, 'id', 'desc')));
    }

    //user_edit
    function user_edit() {
        $this->AI();
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('users', "id={$id}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import('user_edit', array('data' => $query[0]));
    }
    
    //user_add
    function user_add() {
        $this->AI();
        functions::import('user_add');
    }
    
    function withdraw(){
        $agent = $this->AI();
        $where = "user_id=".$agent->sid;
//        $start_time = trim(functions::request('start_time'));
//        $end_time = trim(functions::request('end_time'));
//        if($start_time=="")
//        {
//            $start_time = strtotime(date('Y-m-d',time()));
//        }
//        else
//        {
//            $start_time = strtotime($start_time);
//        }
//        if($end_time=="")
//        {
//            $end_time = strtotime(date('Y-m-d',time()))+86399;
//        }
//        else
//        {
//            $end_time = strtotime($end_time);
//        }
        $types = intval(functions::request('types'));
        if(!empty($types))
            $where = $where . " and types=".$types;
        //$where = "user_id=".$agent->sid." and apply_time>=".$start_time." and apply_time<=".$end_time ."and status=".$status;
        functions::import('withdraw', array('data' => functions::drive('query')->column('withdraw', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc'),"agent"=>$agent));
    }
    
    function applyWithdraw(){
        $agent = $this->AI();
        if (!in_array($agent->bank->type, [1,2])) exit('<span style="color:red;">您当前没有填写收款方式,请在个人设置里面添加银行卡或支付宝!</span>');
        functions::import('apply_withdraw',array("agent"=>$agent));
    }
    //我的收款账号
    function news(){
        $agent = $this->AI();
        $query = functions::open_mysql()->query('news');
        functions::import('news',array('data'=>$query));
    }
    
    //我的收款账号
    function news_view(){
        $agent = $this->AI();
        $id = functions::request("id");
        $query = functions::open_mysql()->query('news',"id={$id}");
        functions::import('news_view',array('data'=>$query[0]));
    }
    //个人设置
    function my(){
        $agent = $this->AI();
        functions::import('my',array("agent"=>$agent));
    }

    //cookies验证
    //AI登录验证
    private function AI(){
        return functions::api('loginc')->AI('agent',functions::urlc('agent', 'index', 'login'));
    }

}
