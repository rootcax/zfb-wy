<?php require ('header.php'); ?>
<div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
        <table class="layui-table" lay-even="" lay-skin="nob">
            <caption>
                <form action="" id="form" method="post" style="display:inline-block;">
                    开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">
                    <select name="payc" style="height: 31px;border:none;width: 88px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['payc'] == '' ? 'selected' : ''; ?> >支付方式</option>
                        <option value="26" <?php echo $_REQUEST['payc'] == '26' ? 'selected' : ''; ?> >支付宝</option>
                    </select>
                    <select name="state" style="height: 31px;border:none;width: 88px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['state'] == '' ? 'selected' : ''; ?> >订单状态</option>
                        <option value="1" <?php echo $_REQUEST['state'] == '1' ? 'selected' : ''; ?> >未支付</option>
                        <option value="2" <?php echo $_REQUEST['state'] == '2' ? 'selected' : ''; ?> >已支付</option>
                        <option value="3" <?php echo $_REQUEST['state'] == '3' ? 'selected' : ''; ?> >订单超时</option>
                    </select><br><br>
                    用户ID：<input type="text" value="<?php echo $_REQUEST['userid']; ?>" name="userid" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    代理ID：<input type="text" value="<?php echo $_REQUEST['agentid']; ?>" name="agentid" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    系统单号：<input type="text" value="<?php echo $_REQUEST['num']; ?>" name="num"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    备注信息：<input type="text" value="<?php echo $_REQUEST['mark']; ?>" name="mark"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    商户单号：<input type="text" value="<?php echo $_REQUEST['info']; ?>" name="info" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    三方单号：<input type="text" value="<?php echo $_REQUEST['orderNo']; ?>" name="orderNo" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
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
                <col width="150">
                <col width="70">
                <col width="70">
                <col width="200">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th>收款账号</th>
                    <th>用户ID</th>
                    <th>代理ID</th>
                    <th>系统单号</th>
                    <th>商户订单号</th>
                    <th>三方单号</th>
                    <th>金额</th>
                    <th>备注</th>
                    <th>创建时间</th>
                    <th>支付时间</th>
                    <th>银行编码</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr> 
            </thead>
            <tbody>
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td>
                            <?php
                            echo $x['username'] . '(' . M::payc($x['payc']) . ')';
                            ?>
                        </td>
                        <td><?php echo $x['userid'] + 10000; ?></td>
                        <td>
                            <?php
                            if (empty($x['agentid'])) {
                                echo "-";
                            } else {
                                echo $x['agentid'] + 10000;
                            }
                            ?>
                        </td>
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
                        <td style="color: green;font-weight:bold;"><?php echo $x['money']; ?></td>
                        <td><?php echo $x['mark']; ?></td>
                        <td><?php echo date('Y/m/d H:i:s', $x['create_time']); ?></td>
                        <td><?php echo $x['pay_time'] != 0 ? date('Y/m/d H:i:s', $x['pay_time']) : '暂无数据'; ?></td>
                        <td><?php echo $x['bank_name']; ?></td>
                        <td><?php echo M::takes_state($x['state']); ?></td>
                        <td><a href="#" style="color: red;" onclick="del(<?php echo $x['id']; ?>);">删除</a>  <a href="#" class="editbt" onclick="api_request(<?php echo $x['id']; ?>);">设为成功</a>&nbsp;&nbsp;<?php if ($x['sending_times'] > 0) { ?><img src="<?php echo _pub; ?>image/bu.png" width="20px"><?php } ?></td>

                    </tr>
                </tbody>
            <?php } ?>
        </table> 
        <div class="layui-table-page"><div id="layui-table-page1"><div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">

                    <?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 10); ?>


                </div></div></div>
    </div>
</div>
<script type="text/javascript">
    function del(id) {
        var r = confirm("你真的要删除吗?这将是无法恢复的!")
        if (r == true)
        {
            location.href = "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=takes_del&id='; ?>" + id;
        }
    }

    function api_request(id) {
        layer.confirm('你确认要设置该订单为成功状态吗?', {
            btn: ['确认', '取消'] //按钮
        }, function () {
            $(".layer-anim").remove();
            $(".layui-layer-shade").remove();
            layer.load(4, {shade: [0.6, '#fff']});
            $.get("<?php echo functions::get_Config('webCog')['site']; ?>?a=visa_admin&b=api&c=take_api&id=" + id, function (result) {
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
  

