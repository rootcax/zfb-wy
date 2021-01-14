<?php require ('header.php'); ?>
<body>
    <div id="main">
        <table class="table">
            <caption>
                <form action="" method="post" style="display:inline-block;">
                    开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">
                    <select name="payc" style="height: 31px;border:none;width: 78px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['payc'] == '' ? 'selected' : ''; ?> >支付方式</option>
                        <option value="1" <?php echo $_REQUEST['payc'] == '1' ? 'selected' : ''; ?> >支付宝</option>
                        <option value="2" <?php echo $_REQUEST['payc'] == '2' ? 'selected' : ''; ?> >微信</option>
                        <option value="3" <?php echo $_REQUEST['payc'] == '3' ? 'selected' : ''; ?> >云闪付</option>
                        <option value="4" <?php echo $_REQUEST['payc'] == '4' ? 'selected' : ''; ?> >支付宝</option>
                        <option value="5" <?php echo $_REQUEST['payc'] == '5' ? 'selected' : ''; ?> >瑞银微信</option>
                        <option value="6" <?php echo $_REQUEST['payc'] == '6' ? 'selected' : ''; ?> >瑞银银联</option>
                        <option value="7" <?php echo $_REQUEST['payc'] == '7' ? 'selected' : ''; ?> >星POS</option>
                    </select>
                    <select name="state" style="height: 31px;border:none;width: 78px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['state'] == '' ? 'selected' : ''; ?> >订单状态</option>
                        <option value="1" <?php echo $_REQUEST['state'] == '0' ? 'selected' : ''; ?> >未处理</option>
                        <option value="2" <?php echo $_REQUEST['state'] == '1' ? 'selected' : ''; ?> >已处理</option>
                    </select>
                    <br>
                    <br>
                    系统单号：<input type="text" value="<?php echo $_REQUEST['num']; ?>" name="num" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    备注信息：<input type="text" value="<?php echo $_REQUEST['remark']; ?>" name="remark" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />

                    收款账号：<input type="text" value="<?php echo $_REQUEST['landname']; ?>" name="landname" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                </form>
            </caption>
            <thead>
                <tr>
                    <th>系统单号</th>
                    <th>备注信息</th>
                    <th>订单时间</th>
                    <th>金额</th>
                    <th>类型</th>
                    <th>收款账号</th>
                    <th>状态</th>
                    <th>异常原因</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="list">
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td><?php echo $x['num']; ?></td>
                        <td><?php echo $x['remark']; ?></td>
                        <td><?php echo date('Y/m/d H:i:s', $x['order_time']); ?></td>
                        <td><?php echo $x['money']; ?></td>
                        <td><?php echo M::payc($x['payc']); ?></td>
                        <td><?php
                            $call = functions::open_mysql()->query('land', "id={$x['land_id']}");
                            echo $call[0]['username'];
                            ?></td>
                        <td><?php echo $x['state'] == "1" ? "<span style='color:green;'>已处理</span>" : "<span style='color:red;'>未处理</span>"; ?></td>
                        <td><?php if ($x['error_type'] == "1") {
                            echo "超时订单";
                        } ?></td>
                        <td><a href="#" class="editbt" onclick="api_request(<?php echo $x['id']; ?>);">设为成功订单</a></td>
                    </tr>
<?php } ?>
            </tbody>


        </table>
        <ul class="pagination" style="margin-top: 0px;">
<?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 10); ?>
        </ul>

    </div>
    <script type="text/javascript">

        function api_request(id) {
            layer.confirm('你确认要设置该订单为成功状态吗?', {
                btn: ['确认', '取消'] //按钮
            }, function () {
                $(".layer-anim").remove();
                $(".layui-layer-shade").remove();
                layer.load(4, {shade: [0.6, '#fff']});
                $.get("<?php echo functions::get_Config('webCog')['site']; ?>?a=user&b=api&c=ab_orders_api&csrf=<?php echo $csrf; ?>&id=" + id, function (result) {
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