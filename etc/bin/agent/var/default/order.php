<?php require ('header.php'); ?>
<body>
    <div id="main">
        <table class="table">
            <caption>
                <?php
                $payType = intval($_REQUEST['payc']);
                $num = $_REQUEST['num'];
                $userid = $_REQUEST['userid'];
                $mark = $_REQUEST['mark'];
                $landname = $_REQUEST['landname'];
                $start_time = $_REQUEST['start_time'];
                $end_time = $_REQUEST['end_time'];
                $user = json_decode(functions::encode($_SESSION['agent'], AUTH_KEY, 2));
                $where = "agentid={$user->sid} and type=0";
                if ($payType > 0)
                    $where = $where . ' and payc=' . $payType;
                if (!empty($num)) {
                    $where = $where . " and num='" . $num . "'";
                }
                if (!empty($mark)) {
                    $where = $where . " and remark='" . $mark . "'";
                }
                if(!empty($userid))
                    {
                        $userid = $userid -10000;
                        $where = $where . ' and userid=' . $userid;
                    }
                if (!empty($landname)) {
                    $land = functions::open_mysql()->query("land", "username='{$landname}'");
                    if (is_array($land[0])) {
                        $where = $where . ' and land_id=' . $land[0]['id'];
                    }
                }
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
                $time = "order_time>=" . $start_time . " and order_time<=" . $end_time;
                $my = functions::open_mysql();
                $nowTime = strtotime(date("Y-m-d", time()) . ' 00:00:00');
                $yuechu = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y"))));
                $yuedi = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("t"), date("Y"))));
                $shangyuechu = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
                $shangyuedi = strtotime(date("Y-m-d 23:59:59", strtotime(-date('d') . 'day')));
                ?> 
                <form action="" method="post" style="display:inline-block;">
                订单金额:<span style="color: blue;font-weight:bold;"> 
                <?php
                $des = $my->select("select sum(money) as allmoney from mi_orders where {$time} and {$where}");
                echo floatval($des[0]['allmoney']);
                ?>
                    元</span> │ 订单笔数:<span style="color: green;font-weight:bold;"> 
                    <?php
                    $des = $my->select("select count(id) as count from mi_orders where {$time} and {$where}");
                    echo intval($des[0]['count']);
                    ?> 
                </span>笔  │ 
                代理分润：<span style="color: blue;font-weight:bold;">
                    <?php
                    $des = $my->select("select sum(agent_payment) as allmoney from mi_orders where {$time} and {$where}");
                    echo floatval($des[0]['allmoney']);
                    ?>
                    元</span> │ 开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">
                    <select name="payc" style="height: 31px;border:none;width: 78px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['payc'] == '' ? 'selected' : ''; ?> >支付方式</option>
                        <option value="26" <?php echo $_REQUEST['payc'] == '26' ? 'selected' : ''; ?> >支付宝</option>
                    </select>
                    <br>
                    <br>
                    商户ID：<input type="text" value="<?php echo $_REQUEST['userid']; ?>" name="userid" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    系统单号：<input type="text" value="<?php echo $_REQUEST['num']; ?>" name="num" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    备注信息：<input type="text" value="<?php echo $_REQUEST['mark']; ?>" name="mark" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    收款账号：<input type="text" value="<?php echo $_REQUEST['landname']; ?>" name="landname" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                </form>

            </caption>  

            <thead>
                <tr>
                    <th>商户ID</th>
                    <th>收款账号</th>
                    <th>系统单号</th>
                    <th>金额</th>
                    <th>备注信息</th>
                    <th>类型</th>
                    <th>支付时间</th>
                    <th>API</th>
                    <th>HTTP</th>
                    <th>回调时间</th>
                    <th>分润</th>
                </tr>
            </thead>
            <tbody id="list">
<?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td><?php echo $x['userid'] + 10000; ?></td>
                        <td><?php $call = functions::open_mysql()->query('land', "id={$x['land_id']}");
    echo $call[0]['username']; ?></td>
                        <td><?php echo $x['num']; ?></td>
                        <td><?php echo $x['money']; ?></td>
                        <td><?php echo $x['remark']; ?></td>
                        <td><?php echo M::payc($x['payc']); ?></td>
                        <td><?php echo date('Y/m/d H:i:s', $x['order_time']); ?></td>
                        <td><?php if ($x['api_state'] == 1) {
                    echo '<span style="color:red;">未请求</span>';
                } if ($x['api_state'] == 2) {
                    echo '<span style="color:green;">请求成功</span>';
                } if ($x['api_state'] == 3) {
                    echo '请求失败';
                } ?></td>
                        <td><?php echo htmlspecialchars($x['http']); ?></td>
                        <td><?php echo $x['request_time'] != 0 ? date('Y/m/d H:i:s', $x['request_time']) : '暂无数据'; ?></td>
                        <td><?php echo $x['agent_payment']; ?></td>
                    </tr>
<?php } ?>
            </tbody>


        </table>
        <ul class="pagination" style="margin-top: 0px;">
<?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 10); ?>
            <li><a class="waves-effect waves-button" href="javascript:;" onclick="del()" style="display:none;" id="delbtn">删除选中</a></li>
        </ul>

    </div>
    <script>
        $(function () {
            $("#start_time").datepicker({dateFormat: 'yy-mm-dd 00:00:00',
                dayNamesMin: ['日', '一', '二', '三', '四', '五', '六'],
                monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']

            });
        });
        $(function () {
            $("#end_time").datepicker({dateFormat: 'yy-mm-dd 23:59:59',
                dayNamesMin: ['日', '一', '二', '三', '四', '五', '六'],
                monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']
            });
        });
    </script>

<?php require ('footer.php'); ?>