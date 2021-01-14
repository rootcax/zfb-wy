<?php require ('header.php'); ?>
<div id="main">
    <!-- 内容主体区域 -->
    <table class="table">
        <caption>
            <span style="font-size: 15px;margin-left:20px;">[ 已代付金额: <?php
                //查询全部代付 
                $order = $mysql->select("select sum(amount) as money,count(id) as count from mi_dforders where memberCode={$user->memberCode} and status='111'");
                echo '<span style="font-weight:bold;"> ' . floatval($order[0]['money']) . ' </span> / 已代付笔数: <span style="color:green;font-weight:bold;">' . intval($order[0]['count']) . '</span> ';
                ?> / 待处理代付金额: <?php
                $pending_order = $mysql->select("select sum(amount) as pending_money,count(id) as pending_count from mi_dforders where memberCode={$user->memberCode} and status='110'");
                echo '<span style="font-weight:bold;"> ' . floatval($pending_order[0]['pending_money']) . ' </span> / 待处理代付笔数: <span style="color:green;font-weight:bold;">' . intval($pending_order[0]['pending_count']) . '</span> ';
                ?>]</span>
            </span> <br>
            <form action="" id="form" method="post" style="display:inline-block;">
                开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">
                <select name="types" style="height: 31px;border:none;width: 78px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                    <option value="" <?php echo $_REQUEST['status'] == '' ? 'selected' : ''; ?> >订单状态</option>
                    <option value="1" <?php echo $_REQUEST['status'] == '110' ? 'selected' : ''; ?> >待处理</option>
                    <option value="2" <?php echo $_REQUEST['status'] == '111' ? 'selected' : ''; ?> >成功</option>
                    <option value="3" <?php echo $_REQUEST['status'] == '112' ? 'selected' : ''; ?> >失败</option>
                </select>
                系统单号：<input type="text" value="<?php echo $_REQUEST['order_seq_id']; ?>" name="order_seq_id"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                商户单号：<input type="text" value="<?php echo $_REQUEST['orderId']; ?>" name="orderId" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                收款人姓名：<input type="text" value="<?php echo $_REQUEST['creditName']; ?>" name="creditName" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                收款人卡号：<input type="text" value="<?php echo $_REQUEST['bankAcctId']; ?>" name="bankAcctId" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                备注信息：<input type="text" value="<?php echo $_REQUEST['remark']; ?>" name="remark"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                <input type="hidden" name="is_export" id="is_export" value=""/>
                <input type="hidden" name="export_all" id="export_all" value=""/>
                <input type="hidden" name="page" id="page" value="<?php echo $_POST['page'] ?>"/>
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
            <?php foreach ($data['query'] as $minet) { ?>
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
                </tr>
            </tbody>
        <?php } ?>
    </table> 
    <ul class="pagination" style="margin-top: 0px;">
        <?php functions::drive('page')->auto($data['info']['page'], $data['info']['current'], 10); ?>
    </ul></div>
<script>
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
  

