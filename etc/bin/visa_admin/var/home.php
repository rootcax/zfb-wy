<?php require ('header.php'); ?>
<div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
        <table class="layui-table" lay-even="" lay-skin="nob">
            <caption>
                <?php
                $payType = intval($_REQUEST['payc']);
                $num = $_REQUEST['num'];
                $mark = $_REQUEST['mark'];
                $userid = $_REQUEST['userid'];
                $agentid = $_REQUEST['agentid'];
                $sending = intval($_REQUEST['sending']);
                $landname = $_REQUEST['landname'];
                $start_time = $_REQUEST['start_time'];
                $end_time = $_REQUEST['end_time'];
                $where = ' and type=0';
                if ($payType > 0)
                    $where = $where . ' and payc=' . $payType;
                if (!empty($num)) {
                    $where = $where . " and num='" . $num . "'";
                }
                if (!empty($mark)) {
                    $where = $where . " and remark='" . $mark . "'";
                }
                if (!empty($userid)) {
                    $userid = $userid - 10000;
                    $where = $where . ' and userid=' . $userid;
                }
                if (!empty($agentid)) {
                    $agentid = $agentid - 10000;
                    $where = $where . ' and agentid=' . $agentid;
                }
                if ($landname != null && $landname != "") {
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
                <form id="form" action="" method="post" style="display:inline-block;">
                    订单金额：<span style="color: blue;font-weight:bold;"> 
                        <?php
                        $des = $my->select("select sum(money) as allmoney,count(*) as count,sum(payment) as allpayment,sum(IF(sending_times>0,money,0)) as sending_money,count(sending_times>0 or null) as count_sending,sum(agent_payment) as agent_payment,sum(profit) as profit from mi_orders where {$time} {$where}");
                        echo floatval($des[0]['allmoney']);
                        ?>
                        元</span> │ 订单笔数：<span style="color: green;font-weight:bold;"> 
                        <?php
                        echo intval($des[0]['count']);
                        ?> 
                    </span>笔  │  
                    商户所得：<span style="color: blue;font-weight:bold;">
                        <?php
                        echo floatval($des[0]['allpayment']);
                        ?>
                        元</span> │ 
                    代理分润：<span style="color: blue;font-weight:bold;">
                        <?php
                        echo floatval($des[0]['agent_payment']);
                        ?>
                        元</span> │ 
                    总利润：<span style="color: blue;font-weight:bold;">
                        <?php
                        echo floatval($des[0]['profit']);
                        ?>
                        元</span> │ 补单笔数：<span style="color: red;font-weight:bold;"> 
                        <?php
                        echo intval($des[0]['count_sending']);
                        ?> 
                    </span>笔  │  
                    补单金额：<span style="color: red;font-weight:bold;">
                        <?php
                        echo floatval($des[0]['sending_money']);
                        ?>
                        元</span> 

                    &nbsp;&nbsp;开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">

                    <select name="payc" style="height: 31px;border:none;width: 88px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['payc'] == '' ? 'selected' : ''; ?> >支付方式</option>
                        <option value="26" <?php echo $_REQUEST['payc'] == '26' ? 'selected' : ''; ?> >支付宝</option>
                    </select> <br><br>
                    代理ID：<input type="text" value="<?php echo $_REQUEST['agentid']; ?>" name="agentid"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    用户ID：<input type="text" value="<?php echo $_REQUEST['userid']; ?>" name="userid"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    系统单号：<input type="text" value="<?php echo $_REQUEST['num']; ?>" name="num" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    备注信息：<input type="text" value="<?php echo $_REQUEST['mark']; ?>" name="mark"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    收款账号：<input type="text" value="<?php echo $_REQUEST['landname']; ?>" name="landname" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <select name="sending" style="height: 31px;border:none;width: 88px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="0" <?php echo $_REQUEST['sending'] == '0' ? 'selected' : ''; ?> >全部订单</option>
                        <option value="1" <?php echo $_REQUEST['sending'] == '1' ? 'selected' : ''; ?> >正常</option>
                        <option value="2" <?php echo $_REQUEST['sending'] == '2' ? 'selected' : ''; ?> >补单</option>
                    </select>
                    <input type="hidden" name="is_export" id="is_export" value=""/>
                    <input type="hidden" name="export_all" id="export_all" value=""/>
                    <input type="hidden" name="page" id="page" value="<?php echo $_POST['page'] ?>"/>
                    <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <button type="button" onclick="onSubmit()"  style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" >导出当前页</button>
                    <button type="button" onclick="onSubmit(1)"  style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" >导出全部</button>
                </form>

            </caption>  
            <colgroup>
                <col width="100">
                <col width="80">
                <col width="80">
                <col width="200">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th>收款账号</th>
                    <th>用户ID</th>
                    <th>代理ID</th>
                    <th>系统单号</th>
                    <th>金额</th>
                    <th>备注信息</th>
                    <th>支付时间</th>
                    <th>API</th>
                    <th>HTTP</th>
                    <th>回调时间</th>
                    <th>商户所得</th>
                    <th>代理分润</th>
                    <th>操作</th>
                </tr> 
            </thead>
            <tbody>
                <?php foreach ($data['query'] as $minet) { ?>
                    <tr>
                        <td><?php
                            $call = functions::open_mysql()->query("land", "id={$minet['land_id']}");
                            echo $call[0]['username'] . '(' . M::payc($minet['payc']) . ')';
                            ?></td>
                        <td><?php echo $minet['userid'] + 10000; ?></td>
                        <td><?php echo $minet['agentid'] + 10000; ?></td>
                        <td><?php echo $minet['num']; ?></td>
                        <td style="color: green;font-weight:bold;"><?php echo $minet['money']; ?></td>
                        <td><?php echo $minet['remark']; ?></td>
                        <td><?php echo date('Y/m/d H:i:s', $minet['order_time']); ?></td>
                        <td><?php
                            if ($minet['api_state'] == 1) {
                                echo '<span style="color:red;">未请求</span>';
                            } if ($minet['api_state'] == 2) {
                                echo '<span style="color:green;">请求成功</span>';
                            } if ($minet['api_state'] == 3) {
                                echo '请求失败';
                            }
                            ?></td>
                        <td><?php echo htmlspecialchars($minet['http']); ?></td>
                        <td><?php echo $minet['request_time'] != 0 ? date('Y/m/d H:i:s', $minet['request_time']) : '暂无数据'; ?></td>
                        <td><?php echo htmlspecialchars($minet['payment']); ?></td>
                        <td><?php echo htmlspecialchars($minet['agent_payment']); ?></td>
                        <td><a href="#" style="color: red;" onclick="del(<?php echo $minet['id']; ?>);">删除</a>   <a href="#" class="editbt" onclick="api_request(<?php echo $minet['id']; ?>);">手动请求</a>&nbsp;&nbsp;<?php if ($minet['sending_times'] > 0) { ?><img src="<?php echo _pub; ?>image/bu.png" width="20px"><?php } ?></td>

                    </tr>
                </tbody>
            <?php } ?>
        </table> 
        <div class="layui-table-page">
            <div id="layui-table-page1">
                <div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">
                    <?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 20); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function del(id) {
        var r = confirm("你真的要删除吗?这将是无法恢复的!")
        if (r == true)
        {
            location.href = "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=order_del&id='; ?>" + id;
        }
    }

    function api_request(id) {
        layer.confirm('你确认要手动回掉该订单吗?手动回掉立即生效无延迟!', {
            btn: ['确认', '取消'] //按钮
        }, function () {
            $(".layer-anim").remove();
            $(".layui-layer-shade").remove();
            layer.load(4, {shade: [0.6, '#fff']});
            $.get("<?php echo functions::get_Config('webCog')['site']; ?>?a=visa_admin&b=api&c=order_api&id=" + id, function (result) {
                if (result.code == '200') {
                    layer.closeAll('loading');
                    layer.msg(result.msg, {icon: 1});
                    setTimeout(function () {
                        location.href = '';
                    }, 1000);
                } else {
                    //请求失败
                    layer.closeAll('loading');
                    layer.msg(result.msg, {icon: 2});
                }
            });
        });
    }

    function doSubmit() {
        $('#page').val(1);
        $('#form').submit();
    }

    function onSubmit(all) {
        // target="_blank"
        $('#form').attr('target', '_blank');
        $('#is_export').val('1');
        if (all == 1) {
            $('#export_all').val('1');
        }
        $('#form').submit();
        $('#is_export').val(null);
        $('#export_all').val(null);
        $('#form').attr('target', null);
    }
</script>
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
  

