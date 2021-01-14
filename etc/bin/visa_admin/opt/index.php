<?php

class index {

    //登录
    function login() {
        //检测是否管理员
        functions::api('loginc')->AI('login_admin', functions::urlc('visa_admin', 'index', 'home'));
        functions::import_var('login');
    }

    //home
    function home() {
        $this->AI();
        $userid = intval(functions::request('userid'));
        $agentid = intval(functions::request('agentid'));
        $payc = intval(functions::request('payc'));
        $mark = trim(functions::request('mark'));
        $landname = trim(functions::request('landname'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        $sending = intval(functions::request("sending"));
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where = "type=0 and order_time>=" . $start_time . " and order_time<=" . $end_time;
        if (!empty($userid)) {
            $userid = $userid - 10000;
            $where = $where . " and userid=" . $userid;
        }
        if (!empty($agentid)) {
            $agentid = $agentid - 10000;
            $where = $where . " and agentid=" . $agentid;
        }
        if (!empty($payc))
            $where = $where . ' and payc=' . $payc;
        $num = trim(functions::request('num'));
        if (!empty($num))
            $where = $where . " and num='" . $num . "'";
        if (!empty($mark))
            $where = $where . " and remark='" . $mark . "'";
        if (!empty($landname)) {
            $land = functions::open_mysql()->query("land", "username='{$landname}'");
            if (is_array($land[0])) {
                $where = $where . ' and land_id=' . $land[0]['id'];
            }
        }
        if ($sending == 1) {
            $where = $where . ' and sending_times=0';
        } else if ($sending == 2) {
            $where = $where . ' and sending_times>0';
        }
        $is_export = trim(functions::request('is_export'));
        $export_all = trim(functions::request('export_all'));
        if ($is_export) {
            if ($export_all) {
                $res['query'] = functions::open_mysql()->query('orders', "{$where}", null, 'id', 'desc');
            } else {
                $res = functions::drive('query')->column('orders', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc');
            }

            $title = ['ID', '收款账号', '用户ID', '订单号', '金额', '商户订单号', '类型', '支付时间', 'API', 'HTTP', '回调时间', '商户所得'];

            $api_state_map = ['1' => '未请求', '2' => '请求成功', '3' => '请求失败'];
            $payc_map = ['26' => '支付宝'];
            $data = [];
            foreach ($res['query'] as $x) {
                $call = functions::open_mysql()->query("land", "id={$x['land_id']}");

                $data[] = [
                    $x['id'],
                    $call[0]['username'] . '(' . $payc_map[$x['payc']] . ')',
                    $x['userid'],
                    $x['num'],
                    $x['money'],
                    $x['remark'],
                    $payc_map[$x['payc']],
                    date('Y/m/d H:i:s', $x['order_time']),
                    $api_state_map[$x['api_state']],
                    htmlspecialchars($x['http']),
                    $x['request_time'] != 0 ? date('Y/m/d H:i:s', $x['request_time']) : '暂无数据',
                    htmlspecialchars($x['payment'])
                ];
            }
            return functions::exportExcel($title, $data);
        }
        functions::import_var('home', array('data' => functions::drive('query')->column('orders', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    //个人设置
    function my() {
        $manager = $this->AI();
        functions::import_var('my', array("manager" => $manager));
    }

    //take
    function takes() {
        $this->AI();
        //关键词查询
        $userid = intval(functions::request('userid'));
        $agentid = intval(functions::request('agentid'));
        $payc = intval(functions::request('payc'));
        $mark = trim(functions::request('mark'));
        $state = intval(functions::request('state'));
        $info = trim(functions::request('info'));
        $num = trim(functions::request('num'));
        $orderNo = trim(functions::request('orderNo'));
        $landname = trim(functions::request('landname'));
        $sending = intval(functions::request("sending"));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where = "a.create_time between " . $start_time . " and " . $end_time;
        if (!empty($userid)) {
            $userid = $userid - 10000;
            $where = $where . " and a.userid=" . $userid;
        }
        if (!empty($agentid)) {
            $agentid = $agentid - 10000;
            $where = $where . " and a.agentid=" . $agentid;
        }
        if (!empty($payc))
            $where = $where . ' and a.payc=' . $payc;
        if (!empty($num))
            $where = $where . " and a.num='" . $num . "'";
        if (!empty($orderNo))
            $where = $where . " and a.reorderNo='" . $orderNo . "'";
        if (!empty($mark))
            $where = $where . " and a.mark='" . $mark . "'";
        if (!empty($state))
            $where = $where . ' and a.state=' . $state;
        if (!empty($info))
            $where = $where . ' and a.info="' . $info . '"';
        if (!empty($landname)) {
            $where = $where . ' and b.username="' . $landname . '"';
        }
        if ($sending == 1) {
            $where = $where . ' and a.sending_times=0';
        } else if ($sending == 2) {
            $where = $where . ' and a.sending_times>0';
        }
        $mysql = functions::open_mysql();
        $is_export = trim(functions::request('is_export'));
        $export_all = trim(functions::request('export_all'));
        $sql = "select a.*,b.username,c.bank_name from mi_takes as a left join mi_land as b on a.land_id = b.id left join mi_bank as c on a.bank_code=c.bank_code where {$where} order by id desc";
        $count = "select count(*) as count from mi_takes as a left join mi_land as b on a.land_id = b.id where {$where}";
        if ($is_export) {
            if ($export_all) {
                $res['query'] = $mysql->query($sql);
            } else {
                $res = functions::drive('query')->column_sql($sql, $count, array('num' => intval(functions::request('page')), 'all' => 20));
            }

            $title = ['ID', '收款账号', '用户ID', '订单号', '商户订单号', '金额', '支付时间', '创建时间', '状态'];
            $state_map = ['1' => '未支付', '2' => '已支付', '3' => '订单超时'];
            $payc_map = ['26' => '支付宝'];
            $data = [];
            foreach ($res['query'] as $x) {
                $call = $mysql->query("land", "id={$x['land_id']}");
                $data[] = [
                    $x['id'],
                    $call[0]['username'] . '(' . $payc_map[$x['payc']] . ')',
                    $x['userid'],
                    $x['num'],
                    $x['info'],
                    $x['money'],
                    date('Y/m/d H:i:s', $x['create_time']),
                    $x['pay_time'] != 0 ? date('Y/m/d H:i:s', $x['pay_time']) : '暂无数据',
                    $state_map[$x['state']],
                ];
            }

            return functions::exportExcel($title, $data);
        }
        functions::import_var('takes', array('data' => functions::drive('query')->column_sql($sql, $count, array('num' => intval(functions::request('page')), 'all' => 20))));
    }

    //take
    function df_orders() {
        $this->AI();
        $memberCode = trim(functions::request('memberCode'));
        $phone = trim(functions::request('phone'));
        $status = trim(functions::request('status'));
        $order_seq_id = trim(functions::request('order_seq_id'));
        $orderId = trim(functions::request('orderId'));
        $creditName = trim(functions::request('creditName'));
        $bankAcctId = trim(functions::request('bankAcctId'));
        $remark = trim(functions::request('remark'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where = "a.create_time between " . $start_time . " and " . $end_time;
        if (!empty($memberCode)) {
            $where = $where . " and a.memberCode='" . $memberCode . "'";
        }
        if (!empty($order_seq_id))
            $where = $where . " and a.order_seq_id='" . $order_seq_id . "'";
        if (!empty($orderId))
            $where = $where . " and a.orderId='" . $orderId . "'";

        if (!empty($creditName)) {
            $where = $where . " and a.creditName='" . $creditName . "'";
        }
        if (!empty($bankAcctId)) {
            $where = $where . " and a.bankAcctId='" . $bankAcctId . "'";
        }
        if (!empty($remark)) {
            $where = $where . " and a.remark='" . $remark . "'";
        }
        if (!empty($status)) {
            $where = $where . " and a.status='" . $status . "'";
        }
        if (!empty($phone)) {
            $where = $where . " and b.phone='" . $phone . "'";
        }
        $mysql = functions::open_mysql();
        $is_export = trim(functions::request('is_export'));
        $export_all = trim(functions::request('export_all'));
        $sql = "select a.*,b.phone from mi_dforders as a left join mi_users as b on a.memberCode = b.memberCode where {$where} order by id desc";
        $count = "select count(*) as count from mi_dforders as a left join mi_users as b on a.memberCode = b.memberCode where {$where}";
        if ($is_export) {
            if ($export_all) {
                $res['query'] = $mysql->query($sql);
            } else {
                $res = functions::drive('query')->column_sql($sql, $count, array('num' => intval(functions::request('page')), 'all' => 20));
            }

            $title = ['ID', '商户号', '手机号', '系统订单号', '商户订单号', '金额', '收款方户名', '收款方银行卡号', '收款方银行', '创建时间', '支付时间', '状态'];
            $state_map = ['110' => '待处理', '111' => '成功', '112' => '失败'];
            $data = [];
            foreach ($res['query'] as $x) {
                $data[] = [
                    $x['id'],
                    $x['memberCode'],
                    $x['phone'],
                    $x['order_seq_id'],
                    $x['orderId'],
                    $x['amount'],
                    $x['creditName'],
                    $x['bankAcctId'],
                    $x['bankName'],
                    date('Y/m/d H:i:s', $x['create_time']),
                    $x['update_time'] != 0 ? date('Y/m/d H:i:s', $x['update_time']) : '暂无数据',
                    $state_map[$x['state']],
                ];
            }
            return functions::exportExcel($title, $data);
        }
        functions::import_var('df_orders', array('data' => functions::drive('query')->column_sql($sql, $count, array('num' => intval(functions::request('page')), 'all' => 20))));
    }

    function change_Userbalance() {
        $manager = $this->AI();
        if ($manager->groupid != "1")
            exit('对不起，权限不足');
        $id = intval(functions::request("id"));
        if (empty($id)) {
            functions::json(-1, "用户不存在");
        }
        $query = "select id,phone,balance from mi_users where id={$id}";
        $data = functions::open_mysql()->select($query);
        functions::import_var('change_user_balance', array('data' => $data[0], 'manager' => $manager, "id" => $id));
    }

    //recharge用户余额充值
    function recharge() {
        $this->AI();
        //关键词查询
        $userid = intval(functions::request('userid'));
        $payc = intval(functions::request('payc'));
        $mark = trim(functions::request('mark'));
        $state = intval(functions::request('state'));
        $attach = trim(functions::request('attach'));
        $num = trim(functions::request('num'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where = "type=1 and create_time>=" . $start_time . " and create_time<=" . $end_time;
        if (!empty($userid)) {
            $userid = $userid - 10000;
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
        if (!empty($attach))
            $where = $where . ' and attach=' . $attach;
        functions::import_var('recharge', array('data' => functions::drive('query')->column('takes', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    //user
    function user() {
        $this->AI();
        //关键词查询
        $keyword = functions::request('where');
        $q = functions::request('q');
        if (!empty($keyword)) {
            if ($q == 'uid') {
                $where = "id={$keyword}";
            }
            if ($q == 'phone') {
                $where = "phone={$keyword}";
            }
        }
        functions::import_var('user', array('data' => functions::drive('query')->column('users', "{$where}", array('num' => intval(functions::request('page')), 'all' => 15), null, 'id', 'desc')));
    }

    //news
    function news() {
        $this->AI();
        //关键词查询
        $keyword = functions::request('where');
        $q = functions::request('q');
        if (!empty($keyword)) {
            if ($q == 'uid') {
                $where = "id={$keyword}";
            }
            if ($q == 'phone') {
                $where = "phone={$keyword}";
            }
        }
        functions::import_var('news', array('data' => functions::drive('query')->column('news', "{$where}", array('num' => intval(functions::request('page')), 'all' => 15), null, 'id', 'desc')));
    }

    //news_add
    function news_add() {
        $this->AI();
        functions::import_var('news_add');
    }

    function news_edit() {
        $this->AI();
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('news', "id={$id}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import_var('news_edit', array('data' => $query[0]));
    }

    //user_edit
    function user_edit() {
        $manager = $this->AI();
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('users', "id={$id}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import_var('user_edit', array('data' => $query[0], 'manager' => $manager));
    }

    //user_add
    function user_add() {
        $this->AI();
        functions::import_var('user_add');
    }

    //user_edit
    function user_customer_edit() {
        $manager = $this->AI();
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('users', "id={$id}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import_var('customer_edit', array('data' => $query[0], 'manager' => $manager));
    }

    //agent
    function agent() {
        $this->AI();
        //关键词查询
        $keyword = functions::request('where');
        $q = functions::request('q');
        if (!empty($keyword)) {
            if ($q == 'uid') {
                $where = "id={$keyword}";
            }
            if ($q == 'phone') {
                $where = "phone={$keyword}";
            }
        }
        functions::import_var('agent', array('data' => functions::drive('query')->column('agent', "{$where}", array('num' => intval(functions::request('page')), 'all' => 15), null, 'id', 'desc')));
    }

    //agent_add
    function agent_add() {
        $user = $this->AI();
        if ($user->groupid == "3")
            exit('对不起，权限不足');
        functions::import_var('agent_add');
    }

    //agent_edit
    function agent_edit() {
        $user = $this->AI();
        if ($user->groupid == "3")
            exit('对不起，权限不足');
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('agent', "id={$id}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        functions::import_var('agent_edit', array('data' => $query[0], 'user' => $user));
    }

    //manager
    function manager() {
        $this->AI();
        //关键词查询
        $keyword = functions::request('where');
        $q = functions::request('q');
        if (!empty($keyword)) {
            if ($q == 'uid') {
                $where = "id={$keyword}";
            }
            if ($q == 'username') {
                $where = "username={$keyword}";
            }
        }
        functions::import_var('manager', array('data' => functions::drive('query')->column('admin', "{$where}", array('num' => intval(functions::request('page')), 'all' => 15), null, 'id', 'desc')));
    }

    //manager_edit
    function manager_edit() {
        $user = $this->AI();
        $id = intval(functions::request('id'));
        $query = functions::open_mysql()->query('admin', "id={$id}");
        if (!is_array($query[0]))
            exit('系统错误,请联系管理员!');
        if ($user->sid != $id) {
            if ($user->groupid >= $query[0]['group_id'])
                exit('对不起，权限不足');
        }
        functions::import_var('manager_edit', array('data' => $query[0], 'user' => $user));
    }

    //manager_add
    function manager_add() {
        $user = $this->AI();
        if ($user->groupid != "1")
            exit('对不起，权限不足');
        functions::import_var('manager_add');
    }

    //银行配置
    function bank() {
        $this->AI();
        functions::import_var('banks', array('data' => functions::drive('query')->column('bank', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    function quota() {
        $this->AI();
        functions::import_var("quota");
    }

    //银行添加
    function bank_memo_add() {
        $id = intval(functions::request("id"));
        if (empty($id)) {
            functions::json(-1, "银行信息有误！");
        }
        $user = $this->AI();
        if ($user->groupid != "1")
            exit('对不起，权限不足');
        $mysql = functions::open_mysql();
        $bank = $mysql->query("bank", "id={$id}");
        functions::import_var('banks_memo', array('data' => $bank[0]));
    }

    //网站设置
    function web_config() {
        $this->AI();
        $data = functions::get_Config('webCog');
        functions::import_var('web_config', array('data' => $data));
    }

    function sms_config() {
        $this->AI();
        $data = functions::get_Config('smsCog');
        functions::import_var('sms_config', array('data' => $data));
    }

    function withdraw_config() {
        $this->AI();
        $data = functions::get_Config('withdrawCog');
        functions::import_var('withdraw_config', array('data' => $data));
    }

    function df_config() {
        $this->AI();
        $data = functions::get_Config('dfCog');
        functions::import_var('df_config', array('data' => $data));
    }

    function reg_config() {
        $this->AI();
        $data = functions::get_Config('registerCog');
        functions::import_var('reg_config', array('data' => $data));
    }

    function agent_config() {
        $this->AI();
        $data = functions::get_Config('agentCog');
        functions::import_var('agent_config', array('data' => $data));
    }

    //cookies验证
    //AI登录验证
    private function AI() {
        return functions::api('loginc')->AI('visa_admin', functions::urlc('visa_admin', 'index', 'login'));
    }

    //take
    function withdraw() {
        $this->AI();
        //关键词查询
        $userid = intval(functions::request('userid'));
        $agentid = intval(functions::request('agentid'));
        $types = intval(functions::request('types'));
        $num = trim(functions::request('num'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where = "apply_time>=" . $start_time . " and apply_time<=" . $end_time;
        if (!empty($userid)) {
            $userid = $userid - 10000;
            $where = $where . " and user_id=" . $userid;
        }
        if (!empty($agentid)) {
            $agentid = $agentid - 10000;
            $where = $where . " and agent_id=" . $agentid;
        }
        if (!empty($num))
            $where = $where . " and flow_no='" . $num . "'";
        if (!empty($types))
            $where = $where . ' and types=' . $types;
        functions::import_var('withdraw', array('data' => functions::drive('query')->column('withdraw', "{$where}", array('num' => intval(functions::request('page')), 'all' => 20), null, 'id', 'desc')));
    }

    //导出excel文件
    function export() {
        $this->AI();
        //关键词查询
        $userid = intval(functions::request('userid'));
        $agentid = intval(functions::request('agentid'));
        $payc = intval(functions::request('payc'));
        $mark = trim(functions::request('mark'));
        $state = intval(functions::request('state'));
        $info = trim(functions::request('info'));
        $num = trim(functions::request('num'));
        $start_time = trim(functions::request('start_time'));
        $end_time = trim(functions::request('end_time'));
        if ($start_time == "") {
            $start_time = strtotime(date('Y-m-d', time()));
        } else {
            $start_time = strtotime($start_time);
        }
        if ($end_time == "") {
            $end_time = strtotime(date('Y-m-d', time())) + 86399;
        } else {
            $end_time = strtotime($end_time);
        }
        $where = "type=0 and create_time>=" . $start_time . " and create_time<=" . $end_time;
        if (!empty($userid)) {
            $userid = $userid - 10000;
            $where = $where . " and userid=" . $userid;
        }
        if (!empty($agentid)) {
            $agentid = $agentid - 10000;
            $where = $where . " and agentid=" . $agentid;
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
            $where = $where . ' and info="' . $info . '"';
        $mysql = functions::open_mysql();
        $takes = $mysql->select("select * from mi_takes {$where}");
        $objPHPExcel = new PHPExcel(); //实例化phpexcel对象
        //创建人
        $objPHPExcel->getProperties()->setCreator("{$_SESSION['adminName']}");
        //最后修改人
        $objPHPExcel->getProperties()->setLastModifiedBy("{$_SESSION['adminName']}");
        //标题
        $objPHPExcel->getProperties()->setTitle("Office 2007 XLSX takes Document");
        //题目
        $objPHPExcel->getProperties()->setSubject("Office 2007 XLSX takes Document");
        //描述
        $objPHPExcel->getProperties()->setDescription("takes");
        //关键字
        $objPHPExcel->getProperties()->setKeywords("takes");
        //种类
        $objPHPExcel->getProperties()->setCategory("office document");

        //设置当前的sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //设置表头

        $objPHPExcel->getActiveSheet()->setCellValue('A1', "ID");
        $objPHPExcel->getActiveSheet()->setCellValue('B1', "收款账号");
        $objPHPExcel->getActiveSheet()->setCellValue('C1', "用户ID");
        $objPHPExcel->getActiveSheet()->setCellValue('D1', "代理ID");
        $objPHPExcel->getActiveSheet()->setCellValue('E1', "系统单号");
        $objPHPExcel->getActiveSheet()->setCellValue('F1', "商户订单号");
        $objPHPExcel->getActiveSheet()->setCellValue('G1', "金额");
        $objPHPExcel->getActiveSheet()->setCellValue('H1', "备注");
        $objPHPExcel->getActiveSheet()->setCellValue('I1', "创建时间");
        $objPHPExcel->getActiveSheet()->setCellValue('J1', "支付时间");
        $objPHPExcel->getActiveSheet()->setCellValue('K1', "状态");

        foreach ($takes as $key => $val) {
            //把结果集进行遍历一行一行写入excel
            $key += 2;

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $key, $val['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $key, $val['password']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $key, $val['mail']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $key, $val['phone']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $key, $val['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $key, $val['add_time']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $key, $val['ipaddress']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $key, $val['name']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $key, $val['add_time']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $key, $val['ipaddress']);
            $objPHPExcel->getActiveSheet()->setCellValue('K' . $key, $val['ipaddress']);
        }
        // 高置列的宽度 

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);

        ob_end_clean(); //清除缓存防止乱码
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="takes.xlsx"'); //设置excel 文件名 
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output'); //保存 
        exit;
    }

}
