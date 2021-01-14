<?php require ('header.php'); ?>
<body>
    <link rel="stylesheet" href="<?php echo _pub; ?>layui/css/layui.css"  media="all">
    <div id="main">
        <form class="layui-form" action="">
            <div class="" style="float: left; width:350px">
                <label class="layui-form-label" style="width: 150px;">支付宝轮询模式</label>
                <div class="layui-input-block" style="width: 100px;margin-left: 160px">
                    <select name="mode" lay-filter="mode">
                        <option value="1_26" <?php if (M::get_pollingmode($user->sid, "26") == 1) echo 'selected=""'; ?>>随机模式</option>
                        <option value="2_26" <?php if (M::get_pollingmode($user->sid, "26") == 2) echo 'selected=""'; ?>>顺序模式</option>
                    </select>
                </div>
            </div>
        </form>
        <hr style="height:1px;border:none;border-top:1px solid #555555;" />
        <table class="table">
            <caption>异步通知通讯KEY：<span style="color: red;font-weight:bold;" id="keyId">已隐藏</span> <input type="button" onclick="displayKey();" value="显示" id="displayKey" style="border: none;outline:none;" /> <input type="button" onclick="GeneratingKey();" value="重新生成" style="border: none;outline:none;" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 商户ID：<span style="color:#8A2BE2;font-weight: bold;"><?php echo $user->sid; ?></span>
                <hr style="height:1px;border:none;border-top:1px solid #555555;" />
            </caption> 

            <thead>
                <tr>
                    <th>#</th>
                    <th>收款账号</th>
                    <th>类型</th>
                    <th>额度</th>
                    <th>已用额度</th>
                    <th>今日收款</th>
                    <th>昨日收款</th>
                    <th>在线</th>
                    <th>监控</th>
                    <th>SDK</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="list">
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td><?php if ($x['isClosed']) { ?><img src="<?php echo _pub; ?>image/dong.png" width="20px"><?php } ?><?php echo $x['id']; ?></td> 
                        <td><?php echo $x['username']; ?></td>
                        <td><?php echo M::payc($x['typec']); ?></td>
                        <td><?php echo M::quotac($x['requota']); ?></td>
                        <td><?php echo M::used_quotac($x['requota'], $x['quota']); ?></td>
                        <td><?php echo M::succ_rate($x['today_all_money'], $x['today_total_success_order'], $x['today_total_order']); ?></td>
                        <td><?php echo M::succ_rate($x['yesterday_all_money'], $x['yesterday_total_success_order'], $x['yesterday_total_order']); ?></td>
                        <td><?php
                            $app_status = $x['app_status'] == 1 ? '<span style="color:green;">在线</span>' : '<span style="color:red;">未在线</span>';
                            echo $app_status;
                            ?></td>
                        <td><?php
                            $app_back = $x['app_status'] == 1 ? '<span style="color:green;">监控中</span>' : '<span style="color:red;">未监控</span>';
                            echo $app_back;
                            ?></td>



                        <td><?php echo $x['sdk']; ?></td>
                        <td>
                            <input id="checkbox_S_<?php echo $x['id']; ?>" class="switch switch-anim" onchange="updateStatus(<?php echo $x['id']; ?>)" type="checkbox" <?php if ($x['status'] == 1) echo 'checked'; ?> <?php if ($x['status'] == 2) echo "disabled='disabled' data=" . $x['status']; ?> >
                        </td>
                        <td><a href="#" class="editbt" onclick="edit(<?php echo $x['id']; ?>);">修改</a> / <a href="#" class="singlebt" onclick="single(<?php echo $x['id']; ?>)">单通道测试</a> / <a href="#" class="deletebt" onclick="del(<?php echo $x['id']; ?>);">删除</a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <ul class="pagination" style="margin-top: 0px;">
            <?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 10); ?>
            <li><a class="waves-effect waves-button" href="javascript:;" onclick="del()" style="display:none;" id="delbtn">删除选中</a></li>
        </ul>
    </div>


    <script src="<?php echo _pub; ?>layui/layui.js" charset="utf-8"></script>
    <script>
                layui.use(['form', 'layedit'], function () {
                    var form = layui.form
                            , layer = layui.layer
                            , layedit = layui.layedit;

                    form.on('select(Transfer)', function (data) {
                        console.log(data.value); //得到被选中的值
                        var value = data.value;
                        arr = value.split("_");
                        var id = arr[1];
                        var transfer = arr[0];
                        console.log(id);
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "<?php echo functions::getdomain() ?>?a=user&b=api&c=updateTransfer",
                            data: {"id": id, "transfer": transfer},
                            success: function (data) {
                                if (data.code == '200') {
                                    layer.msg(data.msg, {icon: 1});
                                } else {
                                    layer.msg(data.msg, {icon: 2});
                                }
                            },
                            error: function (data) {
                                alert("error:" + data.responseText);
                            }
                        });
                    });

                    form.on('select(mode)', function (data) {
                        console.log(data.value); //得到被选中的值
                        var value = data.value;
                        arr = value.split("_");
                        var typec = arr[1];
                        var mode = arr[0];
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "<?php echo functions::getdomain() ?>?a=user&b=api&c=updatePollMode",
                            data: {"typec": typec, "mode": mode},
                            success: function (data) {
                                if (data.code == '200') {
                                    layer.msg(data.msg, {icon: 1});
                                } else {
                                    layer.msg(data.msg, {icon: 2});
                                }
                            },
                            error: function (data) {
                                alert("error:" + data.responseText);
                            }
                        });
                    });
                });
                function displayKey() {
                    $('#keyId').html("<?php echo $user->keyid; ?>");
                    $('#displayKey').remove();
                }

                function GeneratingKey() {
                    layer.confirm('你真的要重新生成通讯key吗？这样可能会造成已对接过的网站异步通知失败！', {
                        btn: ['确认', '取消'] //按钮
                    }, function () {
                        layer.load(4, {shade: [0.6, '#fff']});
                        $.get("<?php echo functions::urlc('user', 'api', 'GeneratingKey'); ?>", function (result) {
                            if (result.code == '200') {
                                layer.closeAll('loading');
                                layer.msg(result.msg, {icon: 1});
                                $('#keyId').html(result.data.key);
                                $('#displayKey').remove();
                            } else {
                                //请求失败
                                layer.closeAll('loading');
                                layer.msg(result.msg, {icon: 2});
                            }
                        });
                    });
                }

                //拉取二维码通讯
                var listen_login = 0;
                //登录成功或超时
                var listen_sin = 0;

                //监控通讯
                function start_listen(id, username) {
                    listen_login = setInterval(function () {
                        $.get("<?php echo functions::getdomain() ?>?a=user&b=api&c=land_login&csrf=<?php echo $csrf; ?>&id=" + id, function (result) {
                            if (result.data.login == '2' && result.data.image != '0') {
                                layer.closeAll('loading');
                                //载入登录二维码
                                logins(id, username);
                                clearInterval(listen_login);
                            }
                        });
                    }, 1500);
                }

                function start_login_sin(id) {
                    listen_sin = setInterval(function () {
                        $.get("<?php echo functions::getdomain() ?>?a=user&b=api&c=land_login&csrf=<?php echo $csrf; ?>&id=" + id, function (result) {
                            if (result.data.login == '3') {
                                $(".layer-anim").remove();
                                $(".layui-layer-shade").remove();
                                layer.msg('登录成功', {icon: 1});
                                setTimeout(function () {
                                    location.href = '';
                                }, 1000);
                            }
                        });
                    }, 1500);
                }


                //登录服务
                function login(id, username) {
                    layer.load(4, {shade: [0.6, '#fff']});
                    //先查询是否已经登录
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "<?php echo functions::getdomain() ?>?a=user&b=api&c=land_login&csrf=<?php echo $csrf; ?>",
                                    data: "id=" + id,
                                    success: function (data) {
                                        if (data.code == '200') {
                                            layer.closeAll('loading');
                                            //layer.msg(data.msg, {icon: 1});
                                            //这里判断是否已经登录或者未登录
                                            if (data.data.login == '0') {
                                                //开始登录
                                                $.get("<?php echo functions::getdomain() ?>?a=user&b=api&c=land_login_typec&csrf=<?php echo $csrf; ?>&id=" + id, function (result) {
                                                    //检测是否请求登录成功
                                                    if (result.code == '200') {
                                                        //开始登录
                                                        //$(".layer-anim").css("display","none");
                                                        $(".layer-anim").remove();
                                                        $(".layui-layer-shade").remove();
                                                        layer.load(4, {shade: [0.6, '#fff']});
                                                        start_listen(id, username);
                                                    } else {
                                                        //请求失败
                                                        layer.closeAll('loading');
                                                        layer.msg(result.msg, {icon: 2});
                                                    }
                                                });
                                            } else {
                                                //重新登录
                                                layer.confirm('系统检测到该账号有可能已经登录或正在登录中,您是否要重新登录呢?', {
                                                    btn: ['重新登录', '取消'] //按钮
                                                }, function () {
                                                    //登录
                                                    //开始登录
                                                    $.get("<?php echo functions::getdomain() ?>?a=user&b=api&c=land_login_typec&csrf=<?php echo $csrf; ?>&id=" + id, function (result) {
                                                        //检测是否请求登录成功
                                                        if (result.code == '200') {
                                                            //开始登录
                                                            //$(".layer-anim").css("display","none");
                                                            $(".layer-anim").remove();
                                                            $(".layui-layer-shade").remove();
                                                            layer.load(4, {shade: [0.6, '#fff']});
                                                            start_listen(id, username);
                                                        } else {
                                                            //请求失败
                                                            layer.closeAll('loading');
                                                            layer.msg(result.msg, {icon: 2});
                                                        }
                                                    });
                                                });

                                            }
                                        } else {
                                            layer.closeAll('loading');
                                            layer.msg(data.msg, {icon: 2});
                                        }
                                    },
                                    error: function (data) {
                                        alert("error:" + data.responseText);
                                    }
                                });

                            }

                            function single(id)
                            {
                                layer.open({
                                    type: 2,
                                    title: '单通道测试',
                                    shadeClose: true,
                                    shade: 0.8,
                                    area: ['560px', '320px'],
                                    content: '<?php echo functions::urlc('user', 'index', 'singleTest', array('id' => '')); ?>' + id
                                });
                            }

                            function updateStatus(id) {
                                if ($('#checkbox_S_' + id).prop('checked')) {
                                    console.log("开启");
                                    status = 1;
                                } else {
                                    console.log("关闭");
                                    status = 0;
                                }
                                if ($('#checkbox_' + id).attr('data') == 2)
                                {
                                    console.log("禁止");
                                }
                                $.ajax({
                                    type: "POST",
                                    dataType: "json",
                                    url: "<?php echo functions::getdomain() ?>?a=user&b=api&c=updateStatus",
                                    data: {"id": id, "status": status},
                                    success: function (data) {
                                        if (data.code == '200') {
                                            layer.msg(data.msg, {icon: 1});
                                        } else {
                                            layer.msg(data.msg, {icon: 2});
                                        }
                                    },
                                    error: function (data) {
                                        alert("error:" + data.responseText);
                                    }
                                });
                            }

//                                                     function updateTransfer(id) {
//                                                         if ($('#checkbox_T_' + id).prop('checked')) {
//                                                             console.log("开启");
//                                                             transferA = 1;
//                                                         } else {
//                                                             console.log("关闭");
//                                                             transferA = 0;
//                                                         }
//                                                         $.ajax({
//                                                             type: "POST",
//                                                             dataType: "json",
//                                                             url: "<?php echo functions::getdomain() ?>?a=user&b=api&c=updateTransfer",
//                                                             data: {"id": id, "transferA": transferA},
//                                                             success: function (data) {
//                                                                 if (data.code == '200') {
//                                                                     layer.msg(data.msg, {icon: 1});
//                                                                 } else {
//                                                                     layer.msg(data.msg, {icon: 2});
//                                                                 }
//                                                             },
//                                                             error: function (data) {
//                                                                 alert("error:" + data.responseText);
//                                                             }
//                                                         });
//                                                     }
//
//                                                     function updateGTransfer(id) {
//                                                         if ($('#checkbox_GT_' + id).prop('checked')) {
//                                                             console.log("开启");
//                                                             transferB = 1;
//                                                         } else {
//                                                             console.log("关闭");
//                                                             transferB = 0;
//                                                         }
//                                                         $.ajax({
//                                                             type: "POST",
//                                                             dataType: "json",
//                                                             url: "<?php echo functions::getdomain() ?>?a=user&b=api&c=updateGTransfer",
//                                                             data: {"id": id, "transferB": transferB},
//                                                             success: function (data) {
//                                                                 if (data.code == '200') {
//                                                                     layer.msg(data.msg, {icon: 1});
//                                                                 } else {
//                                                                     layer.msg(data.msg, {icon: 2});
//                                                                 }
//                                                             },
//                                                             error: function (data) {
//                                                                 alert("error:" + data.responseText);
//                                                             }
//                                                         });
//                                                     }

                            function edit(id) {
                                layer.open({
                                    type: 2,
                                    title: '修改',
                                    shadeClose: true,
                                    shade: 0.8,
                                    area: ['100%', '100%'],
                                    content: '<?php echo functions::urlc('user', 'index', 'land_edit', array('id' => '')); ?>' + id //iframe的url
                                });
                            }

                            function windowsId(id) {
                                layer.open({
                                    type: 2,
                                    title: '下载电脑版',
                                    shadeClose: true,
                                    shade: 0.8,
                                    area: ['640px', '500px'],
                                    content: '<?php echo functions::urlc('user', 'index', 'windows', array('id' => '')); ?>' + id //iframe的url
                                });
                            }
<?php
$sms_config = functions::get_Config('smsCog');
if ($sms_config['landdel_sms']) {
    ?>
                                function del(id) {
                                    layer.open({
                                        type: 2,
                                        title: '删除',
                                        shadeClose: true,
                                        shade: 0.8,
                                        area: ['100%', '100%'],
                                        content: '<?php echo functions::urlc('user', 'index', 'land_del', array('id' => '')); ?>' + id //iframe的url
                                    });
                                }
<?php } else { ?>
                                function del(id) {
                                    layer.confirm('你是真的要删除该收款账号?并且删除关于该收款账号的一切数据!', {
                                        btn: ['确认删除', '取消'] //按钮
                                    }, function () {
                                        location.href = "<?php echo functions::urlc('user', 'api', 'land_del', array('id' => '')) ?>" + id;
                                    });
                                }
<?php } ?>
                            function logins(id, username) {
                                layer.open({
                                    type: 1,
                                    title: '请进行扫码登录..',
                                    skin: 'layui-layer-rim', //加上边框
                                    area: ['230px', '250px'], //宽高
                                    content: '<img src="<?php echo _pub . upload . '/' . $user->sid; ?>/images/' + username + '.jpg?rand=' + Math.random() + '" style="width:100%;height:100%;">'
                                });
                                start_login_sin(id);
                            }

                            function start_listen_api(id) {
                                layer.load(4, {shade: [0.6, '#fff']});
                                $.get("<?php echo functions::getdomain() ?>?a=user&b=api&c=start_listen&csrf=<?php echo $csrf; ?>&id=" + id, function (result) {
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
                            }

                            function stop_listen_api(id) {
                                layer.load(4, {shade: [0.6, '#fff']});
                                $.get("<?php echo functions::getdomain() ?>?a=user&b=api&c=stop_listen&csrf=<?php echo $csrf; ?>&id=" + id, function (result) {
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
                            }
    </script>

    <?php require ('footer.php'); ?>