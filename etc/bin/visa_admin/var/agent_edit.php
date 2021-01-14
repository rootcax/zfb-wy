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
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-block">
                    <div class="layui-form-mid layui-word-aux"><?php echo $data['phone'] ?></div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">修改密码</label>
                <div class="layui-input-block">
                    <input type="text" name="pwd" placeholder="如果不需要修改请留空.." class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">余额</label>
                <div class="layui-input-block">
                    <input type="text" name="money" value="<?php echo $data['balance']; ?>" class="layui-input" style="width: 98%;">
                </div>
            </div>
            <div class="layui-form-item">
                <label class="layui-form-label">支付宝费率</label>
                <div class="layui-input-block">
                    <input type="text" name="bank2alipay_withdraw" value="<?php echo $data['bank2alipay_withdraw']; ?>" class="layui-input" style="width: 98%;">
                    <div class="layui-form-mid layui-word-aux">此处代理费率默认为百分比，如输入1即为1%，分润费率请勿超过旗下商户手续费费率，否则代理分润将为负数！代理分润=订单金额*（用户手续费率-代理分润费率）</div>
                </div>
            </div>

            <?php $bank = json_decode($data['bank'], true) ?>
            <div class="layui-form-item" id="input-select">
                <label class="layui-form-label">银行卡</label>
                <div class="layui-input-block" style="width: 50%;">
                    <select name="bank_type" lay-filter="bank_type">
                        <option value="1" <?php echo $bank['type'] == '1' ? 'selected' : ''; ?> >支付宝</option>
                        <option value="2" <?php echo $bank['type'] == '2' ? 'selected' : ''; ?> >银行卡</option>
                        <option value="3" <?php echo empty($bank['type']) == '2' ? 'selected' : ''; ?> >暂不填写</option>
                    </select>
                </div>
            </div>
            <div class="layui-form-item" <?php if ($bank['type'] != 1) { ?>style="display: none;"<?php } ?> id="bank_a_1">
                <label class="layui-form-label">姓名</label>
                <div class="layui-input-block">
                    <input placeholder="真实姓名" name="alipay_name" type="text" class="layui-input" value="<?php
                    if ($bank['type'] == 1) {
                        echo $bank['name'];
                    }
                    ?>" style="width: 54%;">
                </div>
            </div>
            <div class="layui-form-item" <?php if ($bank['type'] != 1) { ?>style="display: none;"<?php } ?> id="bank_a_2">
                <label class="layui-form-label">账号</label>
                <div class="layui-input-block">
                    <input placeholder="支付宝账号" name="alipay_content" type="text" class="layui-input" value="<?php
                    if ($bank['type'] == 1) {
                        echo $bank['card'];
                    }
                    ?>" style="width: 54%;">
                </div>
            </div>

            <div class="layui-form-item" <?php if ($bank['type'] != 2) { ?>style="display: none;"<?php } ?> id="bank_b_1">
                <label class="layui-form-label">姓名</label>
                <div class="layui-input-block">
                    <input placeholder="真实姓名" name="bank_name" type="text" class="layui-input" value="<?php
                    if ($bank['type'] == 2) {
                        echo $bank['name'];
                    }
                    ?>" style="width: 54%;">
                </div>
            </div>
            <div class="layui-form-item" <?php if ($bank['type'] != 2) { ?>style="display: none;"<?php } ?> id="bank_b_2">
                <label class="layui-form-label">所属银行</label>
                <div class="layui-input-block">
                    <input placeholder="所属银行" name="bank" type="text" class="layui-input" value="<?php
                    if ($bank['type'] == 2) {
                        echo $bank['bank'];
                    }
                    ?>" style="width: 54%;">
                </div>
            </div>
            <div class="layui-form-item" <?php if ($bank['type'] != 2) { ?>style="display: none;"<?php } ?> id="bank_b_3">
                <label class="layui-form-label">银行卡号</label>
                <div class="layui-input-block">
                    <input placeholder="银行卡号" name="card" type="text" class="layui-input" value="<?php
                    if ($bank['type'] == 2) {
                        echo $bank['card'];
                    }
                    ?>" style="width: 54%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <select name="status" style="width: 98%;">
                        <option value="0" <?php echo $data['status'] == '0' ? 'selected' : ''; ?>>未审核</option>
                        <option value="1" <?php echo $data['status'] == '1' ? 'selected' : ''; ?>>正常</option>
                        <option value="2" <?php echo $data['status'] == '2' ? 'selected' : ''; ?>>已冻结</option>
                    </select>
                </div>
            </div>


            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit type="button" lay-filter="add">确认修改</button>
                </div>
            </div>
        </form>

        <script src="<?php echo _pub; ?>layui/layui.js" charset="utf-8"></script>
        <script src="<?php echo _pub; ?>js/jquery.min.js" charset="utf-8"></script>
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
                        url: "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=agent_edit&id=' . $data['id']; ?>",
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

                layui.use(['form', 'layedit'], function () {
                    var form = layui.form
                            , layer = layui.layer
                            , layedit = layui.layedit;
                    form.on('select(bank_type)', function (data) {
                        if (data.value == 1) {
                            $('#bank_a_1').show();
                            $('#bank_a_2').show();
                            $('#bank_b_1').hide();
                            $('#bank_b_2').hide();
                            $('#bank_b_3').hide();
                        }
                        if (data.value == 2) {
                            $('#bank_a_1').hide();
                            $('#bank_a_2').hide();
                            $('#bank_b_1').show();
                            $('#bank_b_2').show();
                            $('#bank_b_3').show();
                        }
                        if (data.value == 3) {
                            $('#bank_a_1').hide();
                            $('#bank_a_2').hide();
                            $('#bank_b_1').hide();
                            $('#bank_b_2').hide();
                            $('#bank_b_3').hide();
                        }
                        form.render('select');
                    });
                });
            });
        </script>
    </body>
</html>