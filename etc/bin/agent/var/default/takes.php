<?php require ('header.php'); ?>
<body>
    <div id="main">
        <table class="table">
            <caption>
                <form action="" method="post" style="display:inline-block;">
                    开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">
                    <select name="payc" style="height: 31px;border:none;width: 78px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['payc'] == '' ? 'selected' : ''; ?> >支付方式</option>
                        <option value="26" <?php echo $_REQUEST['payc'] == '26' ? 'selected' : ''; ?> >支付宝</option>
                        
                    </select>
                    <select name="state" style="height: 31px;border:none;width: 78px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['state'] == '' ? 'selected' : ''; ?> >订单状态</option>
                        <option value="1" <?php echo $_REQUEST['state'] == '1' ? 'selected' : ''; ?> >未支付</option>
                        <option value="2" <?php echo $_REQUEST['state'] == '2' ? 'selected' : ''; ?> >已支付</option>
                        <option value="3" <?php echo $_REQUEST['state'] == '3' ? 'selected' : ''; ?> >订单超时</option>
                    </select>
                    <br>
                    <br>
                    商户ID：<input type="text" value="<?php echo $_REQUEST['userid']; ?>" name="userid" style="border: none;outline:none;width: 105px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    系统单号：<input type="text" value="<?php echo $_REQUEST['num']; ?>" name="num" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    备注信息：<input type="text" value="<?php echo $_REQUEST['mark']; ?>" name="mark" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    商户单号：<input type="text" value="<?php echo $_REQUEST['info']; ?>" name="info" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    收款账号：<input type="text" value="<?php echo $_REQUEST['landname']; ?>" name="landname" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                </form>
            </caption>
            <thead>
                <tr>
                    <th>商户ID</th>
                    <th>系统单号</th>
                    <th>商户单号</th>
                    <th>备注信息</th>
                    <th>创建时间</th>
                    <th>支付时间</th>
                    <th>金额</th>
                    <th>类型</th>
                    <th>状态</th>
                    <th>收款账号</th>
                </tr>
            </thead>
            <tbody id="list">
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td><?php echo $x['userid'] + 10000; ?></td>
                        <td><?php echo $x['num']; ?></td>
                        <td><?php echo $x['info']; ?></td>
                        <td><?php echo $x['mark']; ?></td>
                        <td><?php echo date('Y/m/d H:i:s', $x['create_time']); ?></td>
                        <td><?php echo $x['pay_time'] != 0 ? date('Y/m/d H:i:s', $x['pay_time']) : '暂无数据'; ?></td>
                        <td><?php echo $x['money']; ?></td>
                        <td><?php echo M::payc($x['payc']); ?></td>
                        <td><?php echo M::takes_state($x['state']); ?></td>
                        <td><?php $call = functions::open_mysql()->query('land', "id={$x['land_id']}");
                echo $call[0]['username'];
                    ?></td>
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