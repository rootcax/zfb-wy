<?php require ('header.php'); ?>
<div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
        <table class="layui-table" lay-even="" lay-skin="nob">
            <caption>
                <span style="font-size: 15px;margin-left:20px;">[ 已代付金额: <?php
                	$mysql = functions::open_mysql();
                    //查询全部代付 
                    $order = $mysql->select("select sum(amount) as money,count(id) as count from mi_dforders where status='111'");
                    echo '<span style="font-weight:bold;"> ' . floatval($order[0]['money']) . ' </span> / 已代付笔数: <span style="color:green;font-weight:bold;">' . intval($order[0]['count']) . '</span> ';
                    ?> / 待处理代付金额: <?php
                    $pending_order = $mysql->select("select sum(amount) as pending_money,count(id) as pending_count from mi_dforders where status='110'");
                    echo '<span style="font-weight:bold;"> ' . floatval($pending_order[0]['pending_money']) . ' </span> / 待处理代付笔数: <span style="color:green;font-weight:bold;">' . intval($pending_order[0]['pending_count']) . '</span> ';
                    ?>]</span>
                </span> <br>
                <form action="" id="form" method="post" style="display:inline-block;">
                    开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">
                    <select name="status" style="height: 31px;border:none;width: 88px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['status'] == '' ? 'selected' : ''; ?> >订单状态</option>
                        <option value="110" <?php echo $_REQUEST['status'] == '110' ? 'selected' : ''; ?> >待处理</option>
                        <option value="111" <?php echo $_REQUEST['status'] == '111' ? 'selected' : ''; ?> >成功</option>
                        <option value="112" <?php echo $_REQUEST['status'] == '112' ? 'selected' : ''; ?> >失败</option>
                    </select><br><br>
                    用户手机号：<input type="text" value="<?php echo $_REQUEST['phone']; ?>" name="phone" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    用户代付号：<input type="text" value="<?php echo $_REQUEST['memberCode']; ?>" name="memberCode" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    系统单号：<input type="text" value="<?php echo $_REQUEST['order_seq_id']; ?>" name="order_seq_id"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    商户单号：<input type="text" value="<?php echo $_REQUEST['orderId']; ?>" name="orderId" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    收款人姓名：<input type="text" value="<?php echo $_REQUEST['creditName']; ?>" name="creditName" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    收款人卡号：<input type="text" value="<?php echo $_REQUEST['bankAcctId']; ?>" name="bankAcctId" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    备注信息：<input type="text" value="<?php echo $_REQUEST['remark']; ?>" name="remark"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <input type="hidden" name="is_export" id="is_export" value=""/>
                    <input type="hidden" name="export_all" id="export_all" value=""/>
                    <input type="hidden" name="page" id="page" value="<?php echo $_POST['page'] ?>"/>
                    <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <button type="button" onclick="onSubmit()"  style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" >导出当前页</button>
                    <button type="button" onclick="onSubmit(1)"  style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" >导出全部</button>
                </form>
            </caption>
            <thead>
                <tr>
                    <th>单号</th>
                    <th>用户信息</th>
                    <th>金额</th>
                    <th>收款人信息</th>
                    <th>开户行信息</th>
                    <th>备注</th>
                    <th>时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr> 
            </thead>
            <tbody>
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td>
                            <p>系统单号：<?php echo $x['order_seq_id']; ?></p>
                            <p>商户单号：<?php echo $x['orderId']; ?></p>
                            <p>余额变更：代付前余额 ( <?php echo $x['old_amount']; ?> ) / 代付后余额 ( <?php echo $x['new_amount']; ?> )  </p>
                        </td>
                        <td>
                            <p>用户手机号：<?php echo $x['phone']; ?></p>
                            <p>用户号：<?php echo $x['memberCode']; ?></p>
                        </td>
                        <td>
                            <p>代付金额：<span style="color: green;"><?php echo $x['amount']; ?> ( 实际打款 : <b style="color:red;"><?php echo $x['amount'] - $x['fee']; ?></b> )</span></p>
                            <p>手续费用：<b><?php echo $minet['fee']; ?></b></p>
                        </td>
                        <td>
                            <p>收款人卡号：<?php echo $x['bankAcctId']; ?></p>
                            <p>收款人户名：<?php echo $x['creditName']; ?></p>
                            <p>收款人银行名称：<?php echo $x['bankName']; ?></p>
                        </td>

                        <td>
                            <p>收款人银行开户行：<?php echo $x['branchName']; ?></p>
                            <p>收款人银行卡开户省份：<?php echo $x['province']; ?></p>
                            <p>收款人银行卡开户城市：<?php echo $x['city']; ?></p>
                        </td>

                        <td><?php echo $x['remark']; ?></td>
                        <td>
                            <p>创建时间：<?php echo date('Y/m/d H:i:s', $x['create_time']); ?></p>
                            <p>处理时间：<?php echo $x['update_time'] != 0 ? date('Y/m/d H:i:s', $x['pay_time']) : '暂无数据'; ?></p>
                        </td>
                        <td><?php echo M::dforder_status($x['status']); ?></td>
                        <td>
                            <p>
                                <?php if ($x['status'] == 110) { ?>
                                    <a href="#" onclick="ok('<?php echo $x['id']; ?>')" class="btn btn-success btn-xs"><i class="fa fa-user-md"></i>确认</a>  <a href="#" onclick="turnDown('<?php echo $x['id']; ?>')" class="btn btn-danger btn-xs"><i class="fa fa-reply-all"></i>驳回</a>
                                    <?php
                                } else {
                                    echo '已经处理';
                                }
                                ?>
                            </p>
                        </td>
                    </tr>

                <?php } ?>
            </tbody>
        </table> 
        <div class="layui-table-page">
            <div id="layui-table-page1">
                <div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">
                    <?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 10); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function ok(id) {
        layer.confirm('你确认已经为该代付订单打过款了吗？', {
            btn: ['我已打款', '取消'] //按钮
        },
                function () {
                    $(".layer-anim").remove();
                    $(".layui-layer-shade").remove();
                    layer.load(4, {shade: [0.6, '#fff']});
                    $.get("/visa_admin.php?b=api&c=updateOrder&type=2&id=" + id, function (data) {
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
                    $.get("/visa_admin.php?b=api&c=updateOrder&type=3&id=" + id + "&msg=" + value, function (data) {
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
  

