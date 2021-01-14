<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="<?php echo _pub; ?>layui/css/layui.css"  media="all">
    </head>
    <body>    
        <form class="layui-form" style="margin-top: 20px;" id="from">
            <div class="layui-form-item">
                <label class="layui-form-label">账号</label>
                <div class="layui-input-block">
                    <input type="text" name="username" placeholder="请输入你的收款账号.." class="layui-input" style="width: 98%;">
                    <div class="layui-form-mid layui-word-aux">该账号不能为空,如果实在不想泄露账号可随意填写一个虚拟账号.</div>
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">类型</label>
                <div class="layui-input-block">
                    <input type="radio" name="typec" value="26" title="支付宝" lay-filter="typec" checked>
                </div>
                <div class="layui-form-mid layui-word-aux">类型是指该收款账号的账号类型，请正确勾选.</div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">二维码类型</label>
                <div class="layui-input-block">
                    <input type="radio" name="qr_typec" value="1" title="固码" checked lay-filter="qr_typec">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">限制时间</label>
                <div class="layui-input-block">
                    <input type="text" name="limit_time" placeholder="请输入收款账户收款限制时间" class="layui-input" style="width: 98%;" value="0">
                    <div class="layui-form-mid layui-word-aux">限制收款账户相同金额收款时间,输入 0 表示不限制，以秒为单位</div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">单笔最小金额</label>
                <div class="layui-input-block">
                    <input type="text" name="min_amount" placeholder="请输入收款账户收款单笔最小金额" class="layui-input" style="width: 98%;" value="0">
                    <div class="layui-form-mid layui-word-aux">请输入收款账户收款单笔最小金额</div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">单笔最大金额</label>
                <div class="layui-input-block">
                    <input type="text" name="max_amount" placeholder="请输入收款账户收款单笔最大金额" class="layui-input" style="width: 98%;" value="0">
                    <div class="layui-form-mid layui-word-aux">请输入收款账户收款单笔最大金额</div>
                </div>
            </div>

            <div class="layui-form-item" id="otorder_limit">
                <label class="layui-form-label">风控设置</label>
                <div class="layui-input-block">
                    <input type="text" name="otorder_limit" placeholder="请输入超时订单笔数.." class="layui-input" style="width: 98%;" value="0">
                    <div class="layui-form-mid layui-word-aux">如果连续达到设定笔数，收款账户将停用，再次使用需手动开启(0：表示不限制)</div>
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">收款额度</label>
                <div class="layui-input-block">
                    <input type="text" name="requota" placeholder="请输入收款额度.." class="layui-input" style="width: 98%;" value="-1">
                    <div class="layui-form-mid layui-word-aux">收款额度每日更新，使用完以后自动关闭收款，输入 -1 表示不限额，输入 0 表示关闭该收款账号</div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">回调方式</label>
                <div class="layui-input-block"  style="width: 100px;">
                    <select name="send_type">
                        <option value="post">POST</option>
                        <option value="get">GET</option>
                    </select>
                </div>

            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">开启轮询</label>
                <div class="layui-input-block">
                    <input type="radio" name="polling" value="0" title="关闭" >
                    <input type="radio" name="polling" value="1" title="开启" checked>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">运行端口</label>
                <div class="layui-input-block">
                    <input type="radio" name="pattern" value="3" title="PC监控" checked>
                </div>
                <div class="layui-form-mid layui-word-aux">
                    支付宝费率为：<span style="color: red;font-weight: bold"><?php echo $cost['bank2alipay_withdraw']; ?>%</span><br>
                </div>
            </div>
            <?php
            $sms_config = functions::get_Config('smsCog');
            if ($sms_config['landadd_sms']) {
                ?>
                <div class="layui-form-item">
                    <label class="layui-form-label">验证码</label>
                    <div class="layui-input-block">
                        <input type="text" name="code" class="layui-input" style="width: 150px;float:left;">
                        <button class="layui-btn layui-btn-normal" type="button" style="width:120px;float:left;margin-left:10px;" id="sms" onclick="sendemail();">发送验证码</button>
                    </div>
                </div>
            <?php } ?>
            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit type="button" lay-filter="add">确认添加</button>
                </div>
            </div>
        </form>

        <script src="<?php echo _pub; ?>layui/layui.js" charset="utf-8"></script>
        <script src="<?php echo _theme; ?>js/jquery.min.js" charset="utf-8"></script>
        <script>
                            layui.use(['form', 'layedit'], function () {
                                var form = layui.form
                                        , layer = layui.layer
                                        , layedit = layui.layedit;
                                //添加
                                form.on('submit(add)', function () {
                                    layer.load();
                                    $.ajax({
                                        type: "POST",
                                        dataType: "json",
                                        url: "<?php echo functions::urlc('user', 'api', 'land_add', array('csrf' => functions::getcsrf())) ?>",
                                        data: $('#from').serialize(),
                                        success: function (data) {
                                            if (data.code == '200') {
                                                layer.closeAll('loading');
                                                layer.msg(data.msg, {icon: 1});
                                                setTimeout(function () {
                                                    location.href = '';
                                                }, 2000);
                                            } else {
                                                layer.closeAll('loading');
                                                layer.msg(data.msg, {icon: 2});
                                            }
                                        },
                                        error: function (data) {
                                            alert("error:" + data.responseText);
                                        }
                                    });

                                });
                            });


                            var countdown = 90;
                            function sendemail() {
                                var obj = $("#sms");
                                var csrf = $('#csrf').val();
                                layer.load();
                                $.get("<?php echo functions::get_Config('webCog')['site']; ?>?a=user&b=api&c=sms&typec=3&phone=<?php echo $user->phone; ?>&csrf=<?php echo $csrf; ?>", function (result) {
                                            if (result.code == '200') {
                                                layer.closeAll('loading');
                                                layer.msg(result.msg, {icon: 1});
                                                settime(obj);
                                            } else {
                                                layer.closeAll('loading');
                                                layer.msg(result.msg, {icon: 2});
                                            }
                                        });
                                    }

                                    function settime(obj) { //发送验证码倒计时
                                        if (countdown == 0) {
                                            obj.attr('disabled', false);
                                            //obj.removeattr("disabled"); 
                                            obj.text("发送验证码");
                                            countdown = 60;
                                            return;
                                        } else {
                                            obj.attr('disabled', true);
                                            obj.text("重新发送(" + countdown + ")");
                                            countdown--;
                                            console.log(countdown);
                                        }
                                        setTimeout(function () {
                                            settime(obj)
                                        }
                                        , 1000)
                                    }
        </script>
    </body>
</html>