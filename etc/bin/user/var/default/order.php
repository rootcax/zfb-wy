<?php require ('header.php'); ?>
<body>
    <div id="main">
        <table class="table">
            <caption> 
                <?php
                $payType = intval($_REQUEST['payc']);
                $num = $_REQUEST['num'];
                $mark = $_REQUEST['mark'];
                $landname = $_REQUEST['landname'];
                $start_time = $_REQUEST['start_time'];
                $end_time = $_REQUEST['end_time'];
                $where = '';
                if ($payType > 0)
                    $where = ' and payc=' . $payType;
                if (!empty($num)) {
                    $where = $where . " and num='" . $num . "'";
                }
                if (!empty($mark)) {
                    $where = $where . " and remark='" . $mark . "'";
                }
                if ($user->parentid != 0) {
                    $userid = $user->parentid;
                } else {
                    $userid = $user->sid;
                }
                if (!empty($landname)) {
                    $land = functions::open_mysql()->query("land", "username='{$landname}' and userid={$userid}");
                    if (is_array($land[0])) {
                        $where = $where . ' and land_id=' . $land[0]['id'];
                    }
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
                <form action="" method="post" style="display:inline-block;">
                    订单金额:<span style="color: blue;font-weight:bold;"> 
                        <?php
                        $des = $my->select("select sum(money) as allmoney,count(*) as count,sum(payment) as allpayment,sum(IF(sending_times>0,money,0)) as sending_money,count(sending_times>0 or null) as count_sending from mi_orders where {$time} and userid={$userid} {$where}");
                        echo floatval($des[0]['allmoney']);
                        ?>
                        元</span> │ 订单笔数:<span style="color: green;font-weight:bold;"> 
                        <?php
                        echo intval($des[0]['count']);
                        ?> 
                    </span>笔  │  
                    利润：<span style="color: blue;font-weight:bold;">
                        <?php
                        echo floatval($des[0]['allpayment']);
                        ?>
                        元</span> │ 补单金额:<span style="color: red;font-weight:bold;"> 
                        <?php
                        echo intval($des[0]['sending_money']);
                        ?> 
                    元 </span> │  
                    补单笔数：<span style="color: red;font-weight:bold;">
                        <?php
                        echo floatval($des[0]['count_sending']);
                        ?>
                        笔 </span> 开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">
                    <select name="payc" style="height: 31px;border:none;width: 78px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['payc'] == '' ? 'selected' : ''; ?> >支付方式</option>
                        <option value="26" <?php echo $_REQUEST['payc'] == '26' ? 'selected' : ''; ?> >支付宝</option>
                    </select>
                    <br>
                    <br>
                    系统单号：<input type="text" value="<?php echo $_REQUEST['num']; ?>" name="num" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    备注信息：<input type="text" value="<?php echo $_REQUEST['mark']; ?>" name="mark" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    收款账号：<input type="text" value="<?php echo $_REQUEST['landname']; ?>" name="landname" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                </form>

            </caption>  

            <thead>
                <tr>

                    <th></th>
                    <th>收款账号</th>
                    <th>系统单号</th>
                    <th>金额</th>
                    <th>备注信息</th>
                    <th>类型</th>
                    <th>支付时间</th>
                    <th>API</th>
                    <th>HTTP</th>
                    <th>回调时间</th>
                    <th>利润</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="list">
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td><?php if ($x['sending_times'] > 0) { ?><img src="<?php echo _pub; ?>image/bu.png" width="20px"><?php } ?></td>
                        <td><?php echo $x['username']; ?></td>
                        <td><?php echo $x['num']; ?></td>
                        <td><?php echo $x['money']; ?></td>
                        <td><?php echo $x['remark']; ?></td>
                        <td><?php echo M::payc($x['payc']); ?></td>
                        <td><?php echo date('Y/m/d H:i:s', $x['order_time']); ?></td>
                        <td><?php
                            if ($x['api_state'] == 1) {
                                echo '<span style="color:red;">未请求</span>';
                            } if ($x['api_state'] == 2) {
                                echo '<span style="color:green;">请求成功</span>';
                            } if ($x['api_state'] == 3) {
                                echo '请求失败';
                            }
                            ?></td>
                        <td><?php echo htmlspecialchars($x['http']); ?></td>
                        <td><?php echo $x['request_time'] != 0 ? date('Y/m/d H:i:s', $x['request_time']) : '暂无数据'; ?></td>
                        <td><?php echo $x['payment']; ?></td>
                        <td><a href="#" class="editbt" onclick="api_request(<?php echo $x['id']; ?>);">手动请求</a></td>

                    </tr>
                <?php } ?>
            </tbody>


        </table>
        <ul class="pagination" style="margin-top: 0px;">
            <?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 10); ?>
            <!--<li><a class="waves-effect waves-button" href="javascript:;" onclick="del()" style="display:none;" id="delbtn">删除选中</a></li>-->
        </ul>

    </div>
    <script type="text/javascript">

        //选择框操作
        $("#all").click(function () {
            if (this.checked) {
                $("#list :checkbox").prop("checked", true);
                $("#delbtn").show();
            } else {
                $("#list :checkbox").prop("checked", false);
                $("#delbtn").hide();
            }
        });

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


        function del() {
            var chk_value = [];
            $('input[name="ckbox"]:checked').each(function () {
                chk_value.push($(this).val());
            });

            layer.confirm('你是真的要删除这些订单信息?', {
                btn: ['确认删除', '取消'] //按钮
            }, function () {
                location.href = "<?php echo functions::urlc('user', 'action', 'order_del', array('id' => '')) ?>" + chk_value;
            });
        }

        function api_request(id) {
            layer.confirm('你确认要手动回掉该订单吗?手动回掉立即生效无延迟!', {
                btn: ['确认', '取消'] //按钮
            }, function () {
                $(".layer-anim").remove();
                $(".layui-layer-shade").remove();
                layer.load(4, {shade: [0.6, '#fff']});
                $.get("<?php echo functions::get_Config('webCog')['site']; ?>?a=user&b=api&c=order_api&csrf=<?php echo $csrf; ?>&id=" + id, function (result) {
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