<?php require ('header.php'); ?>
<div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
        <table class="layui-table" lay-even="" lay-skin="nob">
            <caption>
                提现记录 <span style="font-size: 15px;margin-left:20px;">[ 所有用户总提现金额: <?php
                    //查询全部提现 
                    $mysql = functions::open_mysql();
                    $userid = functions::request('userid');
                    $agentid = functions::request('agentid');
                    if (!empty($userid))
                        $where = " and user_id={$userid}";
                    if (!empty($agentid))
                        $where = " and agent_id={$agentid}";
                    $order = $mysql->select("select sum(amount) as money,count(id) as count from mi_withdraw where types=2 {$where}");
                    echo '<span style="font-weight:bold;font-size:20px;color:red;"> ' . floatval($order[0]['money']) . ' </span> / 总提现笔数: <span style="color:green;font-weight:bold;">' . intval($order[0]['count']) . '</span> ';
                    ?>] </span> <br>
                <form action="" method="post" style="display:inline-block;">
                    开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">
                    <select name="types" style="height: 31px;border:none;width: 88px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['types'] == '' ? 'selected' : ''; ?> >处理状态</option>
                        <option value="1" <?php echo $_REQUEST['types'] == '1' ? 'selected' : ''; ?> >等待管理员处理..</option>
                        <option value="2" <?php echo $_REQUEST['types'] == '2' ? 'selected' : ''; ?> >已经处理</option>
                        <option value="3" <?php echo $_REQUEST['types'] == '3' ? 'selected' : ''; ?> >已驳回该提现</option>
                    </select>
                    用户ID：<input type="text" value="<?php echo $_REQUEST['userid']; ?>" name="userid" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    代理ID：<input type="text" value="<?php echo $_REQUEST['agentid']; ?>" name="agentid" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    系统单号：<input type="text" value="<?php echo $_REQUEST['num']; ?>" name="num"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                </form>
            </caption>
            <colgroup>
                <col width="300">
                <col width="100">
                <col width="200">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th>系统单号</th>
                    <th>用户信息</th>
                    <th>金额</th>
                    <th>银行状态</th>
                    <th>提现时间</th>
                    <th>打款信息</th>
                    <td>操作 <div class="checkbox checkbox-warning" style="display:inline-block;margin:0 0 0 25px;padding:0;position:relative;top:6px;">
                            <input id="checkboxAll" type="checkbox">
                            <label for="checkboxAll">
                            </label>

                            <button type="button" id="deletes" onclick="deletes();" class="btn btn-option1 btn-xs" style="display:none;position:relative;top:-8px;"><i class="fa fa-trash-o"></i>删除</button>
                        </div>
                    </td>
                </tr> 
            </thead>
            <tbody>
                <?php if (!is_array($data['query'])) echo '<tr><td colspan="6" style="text-align: center;">暂时没有查询到订单!</td></tr>'; ?>
                <?php foreach ($data['query'] as $minet) { ?>
                    <tr>
                        <td>
                            <p>流水单号：<?php echo $minet['flow_no']; ?></p>
                            <p>余额变更：提现前余额 ( <?php echo $minet['old_amount']; ?> ) / 提现后余额 ( <?php echo $minet['new_amount']; ?> )  </p>
                        </td>
                        <td>
                            <p><?php if ($minet['user_id'] != 0) { ?>用户ID：<?php echo $minet['user_id'] + 10000; ?> <?php } else { ?>代理ID：<?php echo $minet['agent_id'] + 10000; ?><?php } ?> </p>
                            <p>用户手机号：<?php
                                $mysql = functions::open_mysql();
                                if ($minet['user_id'] != 0) {
                                    $user = $mysql->query('users', "id={$minet['user_id']}")[0];
                                    echo $user['phone'];
                                } else {
                                    $user = $mysql->query('agent', "id={$minet['agent_id']}")[0];
                                    echo $user['phone'];
                                }
                                ?>  </p>
                        </td>
                        <td>
                            <p>提现金额：<span style="color: green;"><?php echo $minet['amount']; ?> ( 实际打款 : <b style="color:red;"><?php echo $minet['amount'] - $minet['fees']; ?></b> )</span></p>
                            <p>手续费用：<b><?php echo $minet['fees']; ?></b></p>
                        </td>
                        <td>
                            <p>银行信息：<?php echo $minet['content']; ?></p>
                            <p>提现状态：<?php
                                if ($minet['types'] == 1)
                                    echo '<span style="color:#039be5;">等待管理员处理..</span>';
                                if ($minet['types'] == 2)
                                    echo '<span style="color:green;">已经处理</span>';
                                if ($minet['types'] == 3)
                                    echo '<span style="color:#bdbdbd;">已驳回该提现</span>'
                                    ?><?php if ($minet['status'] == 4) echo ' (' . date("Y/m/d H:i:s", $minet['pay_time']) . ')'; ?></p>
                        </td>
                        <td>
                            <p>提交时间：<?php echo date("Y/m/d H:i:s", $minet['apply_time']); ?></p>
                            <p>处理时间：<?php
                                if ($minet['deal_time'] != 0) {
                                    echo date("Y/m/d H:i:s", $minet['deal_time']);
                                } else {
                                    echo '等待处理中';
                                }
                                ?></p>
                        </td>
                        <td>
                            <?php if ($minet['types'] == 1) { ?><p>
                                    <?php
                                    //查询收款人信息
                                    $bank = json_decode($user['bank'], true);
                                    if ($bank['type'] == 1)
                                        echo '支付宝账号：<b style="color:red;font-size:15px;">' . $bank['card'] . '</b> / 姓名：<b style="color:green;font-size:15px;">' . $bank['name'] . '</b>'; //支付宝
                                    if ($bank['type'] == 2)
                                        echo '银行卡号：<b style="color:red;font-size:15px;">' . $bank['card'] . '</b> / 姓名：<b style="color:green;font-size:15px;">' . $bank['name'] . '</b> / 银行：<b>' . $bank['bank'] . '</b>'; //支付宝
                                    ?></p>
                                <p>请给该账户打款：<b style="font-size: 15px;color:red;"><?php echo $minet['amount'] - $minet['fees']; ?></b> 元</p>

                                <?php
                            }else {
                                echo '已经处理';
                            }
                            ?>
                        </td>
                        <td>
                            <p><?php if ($minet['types'] == 1) { ?><a href="#" onclick="ok('<?php echo $minet['id']; ?>')" class="btn btn-success btn-xs"><i class="fa fa-user-md"></i>确认</a>  <a href="#" onclick="turnDown('<?php echo $minet['id']; ?>')" class="btn btn-danger btn-xs"><i class="fa fa-reply-all"></i>驳回</a><?php
                                } else {
                                    echo '已经处理';
                                }
                                ?></p>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>

        </table> 
        <div class="layui-table-page"><div id="layui-table-page1"><div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">

                    <?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 10); ?>


                </div></div></div>
    </div>
</div>
<script type="text/javascript">

    function ok(id) {
        layer.confirm('你确认已经为该提现订单打过款了吗？', {
            btn: ['我已打款', '取消'] //按钮
        },
                function () {
                    $(".layer-anim").remove();
                    $(".layui-layer-shade").remove();
                    layer.load(4, {shade: [0.6, '#fff']});
                    $.get("/visa_admin.php?b=api&c=updateWithdraw&type=2&id=" + id, function (data) {
                        if (data.code == '200') {
                            layer.closeAll('loading');
                            layer.msg(data.msg, {icon: 1});
                            setTimeout(function () {
                                location.href = '';
                            }, 1000);
                        } else {
                            layer.closeAll('loading');
                            layer.msg(data.msg, {icon: 2});
                        }
                    });

                });
    }

    function turnDown(id) {
        layer.prompt({formType: 0, title: '请输入驳回信息反馈给用户'},
                function (value, index, elem) {
                    if (value === false)
                        return false;
                    if (value === "") {
                        alert("请输入驳回信息!");
                        return false;
                    }
                    $(".layer-anim").remove();
                    $(".layui-layer-shade").remove();
                    layer.load(4, {shade: [0.6, '#fff']});
                    $.get("/visa_admin.php?b=api&c=updateWithdraw&type=3&id=" + id + "&msg=" + value, function (data) {
                        if (data.code == '200') {
                            layer.closeAll('loading');
                            layer.msg(data.msg, {icon: 1});
                            setTimeout(function () {
                                location.href = '';
                            }, 1000);
                        } else {
                            layer.closeAll('loading');
                            layer.msg(data.msg, {icon: 2});
                        }
                    });

                });
    }



    function showBtn() {
        var Inc = 0;
        $("input[name='items']:checkbox").each(function () {
            if (this.checked) {
                $('#deletes').show();
                $('#callback').show();
                return true;
            }
            Inc++;
        });
        if ($("input[name='items']:checkbox").length == Inc) {
            $('#deletes').hide();
            $('#callback').hide();
        }
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
  

