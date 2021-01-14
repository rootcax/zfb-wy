<?php require ('header.php'); ?>
<div id="main">
    <!-- 内容主体区域 -->
    <table class="table">
        <caption>
            <form action="" method="post" style="display:inline-block;">
                <!--开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">
                <select name="state" style="height: 31px;border:none;width: 78px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                    <option value="" <?php echo $_REQUEST['types'] == '' ? 'selected' : ''; ?> >处理状态</option>
                    <option value="1" <?php echo $_REQUEST['types'] == '1' ? 'selected' : ''; ?> >等待管理员处理..</option>
                    <option value="2" <?php echo $_REQUEST['types'] == '2' ? 'selected' : ''; ?> >已经处理</option>
                    <option value="3" <?php echo $_REQUEST['types'] == '3' ? 'selected' : ''; ?> >已驳回该提现</option>
                </select>
                <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />-->
            </form>
            <span style="font-size: 15px;margin-left:20px;">[ 利润总金额: <b style="font-size: 20px;color:green;"><?php
                    $mysql = functions::open_mysql();
                    $amount = $mysql->select("select sum(agent_payment) as money from mi_orders where agentid={$agent->sid}");
                    echo $amount[0]['money'];
                    ?></b> / 已提现金额: <?php
                //查询全部提现 
                $mysql = functions::open_mysql();
                $order = $mysql->select("select sum(amount) as money,count(id) as count from mi_withdraw where user_id={$agent->sid} and types=2");
                echo '<span style="font-weight:bold;"> ' . floatval($order[0]['money']) . ' </span> / 已提现笔数: <span style="color:green;font-weight:bold;">' . intval($order[0]['count']) . '</span> ';
                ?> / 可用余额：<span style="color:green;font-weight:bold;"><?php $L_amount = $amount[0]['money'] - floatval($order[0]['money']);
                echo $L_amount;
                ?>]</span>
        </caption>
        <thead>
            <tr>
                <th>流水单号</th>
                <th>提现金额</th>
                <th>手续费用</th>
                <th>提现时间</th>
                <th>处理时间</th>
                <th>提现状态</th>
            </tr> 
        </thead>
        <tbody>
<?php foreach ($data['query'] as $minet) { ?>
                <tr>
                    <td><?php echo $minet['flow_no']; ?><br>余额变更：提现前余额 ( <?php echo $minet['old_amount']; ?> ) / 提现后余额 ( <?php echo $minet['new_amount']; ?> ) </td>
                    <td><span style="color: green;"><b><?php echo $minet['amount']; ?></b> ( 实际到款 : <?php echo $minet['amount'] - $minet['fees']; ?> )</span></td>
                    <td style="color: red;font-weight:bold;"><?php echo $minet['fees']; ?></td>
                    <td><?php echo date('Y/m/d H:i:s', $minet['apply_time']); ?></td>
                    <td><?php
                        if ($minet['deal_time'] != 0) {
                            echo date("Y/m/d H:i:s", $minet['deal_time']);
                        } else {
                            echo '等待处理中';
                        }
                        ?></td>
                    <td><?php
                        if ($minet['types'] == 1) {
                            echo '等待管理员处理..';
                        } else if ($minet['types'] == 2) {
                            echo '已经处理';
                        } else if ($minet['types'] == 3) {
                            echo '已驳回该提现';
                        }
                        ?></td>
                </tr>
            </tbody>
<?php } ?>
    </table> 
    <ul class="pagination" style="margin-top: 0px;">
<?php functions::drive('page')->auto($data['info']['page'], $data['info']['current'], 10); ?>
    </ul></div>
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

    function withdraw() {
        layer.open({
            type: 2,
            title: '申请提现',
            shadeClose: true,
            shade: 0.8,
            area: ['500px', '400px'],
            content: '<?php echo functions::get_Config('webCog')['site'] . 'agent.php?b=index&c=applyWithdraw'; ?>' //iframe的url
        });
    }
</script>


<?php require ('footer.php'); ?>
  

