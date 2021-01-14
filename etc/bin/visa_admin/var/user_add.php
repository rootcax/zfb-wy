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
                    <input type="text" name="phone" placeholder="请输入手机号码.." value="" class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">修改密码</label>
                <div class="layui-input-block">
                    <input type="text" name="pwd" placeholder="请输入密码.." class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">所属代理</label>
                <div class="layui-input-block">
                    <input type="text" name="agent" placeholder="请输入代理用户手机号码.." class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">支付宝费率</label>
                <div class="layui-input-block">
                    <input type="text" name="bank2alipay_withdraw" placeholder="请输入支付宝费率.." class="layui-input" style="width: 98%;">
                    <div class="layui-form-mid layui-word-aux">此处默认为百分比，如输入1即为1%，用户手续费=订单金额*手续费率</div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <select name="status" style="width: 98%;">
                        <option value="0">未审核</option>
                        <option value="1" selected>正常</option>
                        <option value="2">已冻结</option>
                    </select>
                </div>
            </div>


            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit type="button" lay-filter="add">确认添加</button>
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
                        url: "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=user_add' ?>",
                        data: $('#from').serialize(),
                        success: function (data) {
                            if (data.code == '200') {
                                layer.closeAll('loading');
                                layer.msg(data.msg, {icon: 1});
                                setTimeout(function () {
                                    location.href = "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=user' ?>";
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
        </script>
    </body>
</html>