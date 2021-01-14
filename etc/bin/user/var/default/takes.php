<?php require ('header.php'); ?>
<body>
    <div id="main">
        <table class="table">
            <caption>
                <form action="" id="form" method="post" style="display:inline-block;">
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
                    系统单号：<input type="text" value="<?php echo $_REQUEST['num']; ?>" name="num" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    商户单号：<input type="text" value="<?php echo $_REQUEST['info']; ?>" name="info" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    三方单号：<input type="text" value="<?php echo $_REQUEST['orderNo']; ?>" name="orderNo" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    备注信息：<input type="text" value="<?php echo $_REQUEST['mark']; ?>" name="mark" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    收款账号：<input type="text" value="<?php echo $_REQUEST['landname']; ?>" name="landname" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
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
                    <th></th>
                    <th>系统单号</th>
                    <th>商户单号</th>
                    <th>三方单号</th>
                    <th>备注信息</th>
                    <th>创建时间</th>
                    <th>支付时间</th>
                    <th>金额</th>
                    <th>类型</th>
                    <th>银行编码</th>
                    <th>状态</th>
                    <th>收款账号</th>
                    <?php if ($user->parentid == 0) { ?><th>操作</th><?php } ?>
                </tr>
            </thead>
            <tbody id="list">
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <!--<td><input type="checkbox" name="ckbox" value="<?php echo $x['id']; ?>"></td>-->
                        <td><?php if ($x['sending_times'] > 0) { ?><img src="<?php echo _pub; ?>image/bu.png" width="20px"><?php } ?></td>
                        <td><?php echo $x['num']; ?></td>
                        <td><?php echo $x['info']; ?></td>
                        <td>
                            <?php
                            if ($x['state'] != 2) {
                                echo $x['reorderNo'];
                            } else {
                                echo $x['orderNo'];
                            }
                            ?>
                        </td>
                        <td><?php echo $x['mark']; ?></td>
                        <td><?php echo date('Y/m/d H:i:s', $x['create_time']); ?></td>
                        <td><?php echo $x['pay_time'] != 0 ? date('Y/m/d H:i:s', $x['pay_time']) : '暂无数据'; ?></td>
                        <td><?php echo $x['money']; ?></td>
                        <td><?php echo M::payc($x['payc']); ?></td>
                        <td><?php echo $x['bank_name']; ?></td>
                        <td><?php echo M::takes_state($x['state']); ?></td>
                        <td><?php echo $x['username']; ?></td>
                        <?php if ($user->parentid == 0) { ?><td><a href="#" class="editbt" onclick="api_request(<?php echo $x['id']; ?>);">补单</a></td><?php } ?>
                    </tr>
                <?php } ?>
            </tbody>


        </table>
        <ul class="pagination" style="margin-top: 0px;">
            <?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 10); ?>
        </ul>

    </div>
    <script type="text/javascript">

        $("input[name='ckbox']").click(function () {
            var chk_value = [];
            $('input[name="ckbox"]:checked').each(function () {
                chk_value.push($(this).val());
            });

            if (chk_value.length != 0) {
                $("#delbtn").show();
            } else {
                $("#delbtn").hide();
            }
        });
<?php if ($user->parentid == 0) { ?>
            function api_request(id) {
                layer.confirm('你确认要设置该订单为成功状态吗?', {
                    btn: ['确认', '取消'] //按钮
                }, function () {
                    $(".layer-anim").remove();
                    $(".layui-layer-shade").remove();
                    layer.load(4, {shade: [0.6, '#fff']});
                    $.get("/?a=user&b=api&c=take_api&csrf=<?php echo $csrf; ?>&id=" + id, function (result) {
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
<?php } ?>

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