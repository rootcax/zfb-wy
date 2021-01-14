<?php require ('header.php'); ?>
<div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
        <table class="layui-table" lay-even="" lay-skin="nob">
            <caption>今日总额:<span style="color: red;font-weight:bold;"> 
                    <?php
                    $payType = intval($_REQUEST['payc']);
                    $num = $_REQUEST['num'];
                    $mark = $_REQUEST['mark'];
                    $userid = $_REQUEST['userid'];
                    $start_time = $_REQUEST['start_time'];
                    $end_time = $_REQUEST['end_time'];
                    $where = ' and type=1 and state=2';
                    if ($payType > 0)
                        $where = $where . ' and payc=' . $payType;
                    if (!empty($num)) {
                        $where = $where . " and num='" . $num . "'";
                    }
                    if (!empty($mark)) {
                        $where = $where . " and mark='" . $mark . "'";
                    }
                    if (!empty($userid)) {
                        $userid = $userid - 10000;
                        $where = $where . ' and userid=' . $userid;
                    }
                    $my = functions::open_mysql();
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
                    $time = "create_time>=" . $start_time . " and create_time<=" . $end_time;
                    $nowTime = strtotime(date("Y-m-d", time()) . ' 00:00:00');
                    $yuechu = strtotime(date("Y-m-d H:i:s", mktime(0, 0, 0, date("m"), 1, date("Y"))));
                    $yuedi = strtotime(date("Y-m-d H:i:s", mktime(23, 59, 59, date("m"), date("t"), date("Y"))));
                    $shangyuechu = strtotime(date('Y-m-01 00:00:00', strtotime('-1 month')));
                    $shangyuedi = strtotime(date("Y-m-d 23:59:59", strtotime(-date('d') . 'day')));
                    $call = $my->select("select sum(money) as allmoney from mi_takes where create_time >= {$nowTime} {$where}");
                    echo floatval($call[0]['allmoney']);
                    ?> 
                </span>元  │ 今日订单:<span style="color: green;font-weight:bold;"> 
                    <?php
                    $des = $my->select("select count(id) as count from mi_takes where create_time > {$nowTime} {$where}");
                    echo intval($des[0]['count']);
                    ?> 
                </span>笔  │ 所选日期总额:<span style="color: blue;font-weight:bold;"> 
                    <?php
                    $des = $my->select("select sum(money) as allmoney from mi_takes where {$time} {$where}");
                    echo floatval($des[0]['allmoney']);
                    ?>
                    元</span> │ 所选日期总订单:<span style="color: green;font-weight:bold;"> 
                    <?php
                    $des = $my->select("select count(id) as count from mi_takes where {$time} {$where}");
                    echo intval($des[0]['count']);
                    ?> 
                </span>笔  │ 本月总额:<span style="color: blue;font-weight:bold;"> 
                    <?php
                    $des = $my->select("select  sum(money) as allmoney  from mi_takes where create_time >= {$yuechu} and create_time<={$yuedi} {$where}");
                    echo floatval($des[0]['allmoney']);
                    ?>
                    元</span> │ 本月订单:<span style="color: green;font-weight:bold;"> 
                    <?php
                    $des = $my->select("select count(id) as count from mi_takes where create_time >= {$yuechu} and create_time<={$yuedi} {$where}");
                    echo intval($des[0]['count']);
                    ?> 
                </span>笔   │ 上月总额:<span style="color: blue;font-weight:bold;"> 
                    <?php
                    $dxTime = $nowTime - (86400 * 30); //30天的时间
                    $des = $my->select("select sum(money) as allmoney from mi_takes where create_time >= {$shangyuechu} and create_time<={$shangyuedi} {$where}");
                    echo floatval($des[0]['allmoney']);
                    ?>
                    元</span> │ 上月订单:<span style="color: green;font-weight:bold;"> 
                    <?php
                    $des = $my->select("select count(id) as count from mi_takes where create_time >= {$shangyuechu} and create_time<={$shangyuedi} {$where}");
                    echo intval($des[0]['count']);
                    ?> 
                </span>笔  <br>
                <form action="" method="post" style="display:inline-block;">
                    开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time']!=''?$_REQUEST['start_time']:date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time']!=''?$_REQUEST['end_time']:date("Y-m-d 23:59:59"); ?>">

                    <select name="payc" style="height: 31px;border:none;width: 88px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['payc']=='' ? 'selected' : '' ;?> >支付方式</option>
		<option value="1" <?php echo $_REQUEST['payc']=='1' ? 'selected' : '' ;?> >支付宝</option>
		<option value="2" <?php echo $_REQUEST['payc']=='2' ? 'selected' : '' ;?> >微信</option>
                <option value="3" <?php echo $_REQUEST['payc'] == '3' ? 'selected' : ''; ?> >云闪付</option>
                    </select>
			 <select name="state" style="height: 31px;border:none;width: 88px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['state'] == '' ? 'selected' : ''; ?> >订单状态</option>
                        <option value="1" <?php echo $_REQUEST['state'] == '1' ? 'selected' : ''; ?> >未支付</option>
                        <option value="2" <?php echo $_REQUEST['state'] == '2' ? 'selected' : ''; ?> >已支付</option>
                        <option value="3" <?php echo $_REQUEST['state'] == '3' ? 'selected' : ''; ?> >订单超时</option>
                    </select><br><br>
	            充值用户：<input type="text" value="<?php echo $_REQUEST['attach']; ?>" name="attach"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                系统单号：<input type="text" value="<?php echo $_REQUEST['num']; ?>" name="num" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                 备注信息：<input type="text" value="<?php echo $_REQUEST['mark']; ?>" name="mark"  style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />


                    <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                </form>
            </caption>
            <colgroup>
                <col width="150">
                <col width="70">
                <col width="200">
                <col>
            </colgroup>
            <thead>
                <tr>
                    <th width="150">收款账号</th>
                    <th>用户ID</th>
                    <th>系统单号</th>
                    <th>充值用户</th>
                    <th>金额</th>
                    <th>备注信息</th>
                    <th>创建时间</th>
                    <th>支付时间</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr> 
            </thead>
            <tbody>
<?php foreach ($data['query'] as $minet) { ?>
                    <tr>
                        <td><?php $call = functions::open_mysql()->query("land", "id={$minet['land_id']}");
    echo $call[0]['username'] . '(' . M::payc($minet['payc']) . ')'; ?></td>
                        <td><span style="color: green;"><?php $my = functions::open_mysql();
    $user = $my->query('users', "phone={$minet['attach']}");
    echo $user[0]['id'] + 10000 ?></span></td>
                        <td><?php echo $minet['num']; ?></td>
                        <td><?php echo $minet['attach']; ?></td>
                        <td style="color: green;font-weight:bold;"><?php echo $minet['money']; ?></td>
                        <td><?php echo $minet['mark']; ?></td>
                        <td><?php echo date('Y/m/d H:i:s', $minet['create_time']); ?></td>
                        <td><?php echo $minet['pay_time'] != 0 ? date('Y/m/d H:i:s', $minet['pay_time']) : '暂无数据'; ?></td>
                        <td><?php echo M::takes_state($minet['state']); ?></td>
                        <td><a href="#" style="color: red;" onclick="del(<?php echo $minet['id']; ?>);">删除</a>  <a href="#" class="editbt" onclick="api_request(<?php echo $minet['id']; ?>);">设为成功</a></td>

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
</script>
<script>
    $(function(){
        $("#start_time").datepicker({dateFormat: 'yy-mm-dd 00:00:00',
            dayNamesMin: ['日','一','二','三','四','五','六'],
            monthNames: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月']

        });
    });
    $(function(){
        $("#end_time").datepicker({dateFormat: 'yy-mm-dd 23:59:59',
            dayNamesMin: ['日','一','二','三','四','五','六'],
            monthNames: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月']
        });
    });
</script>
<?php require ('footer.php'); ?>
  

