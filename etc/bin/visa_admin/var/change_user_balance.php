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
        <?php if ($data) { ?>
            <form class="layui-form" style="margin-top: 20px;" id="from">
                <div class="layui-form-item">
                    <label class="layui-form-label">用户手机号</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid"><?php echo $data['phone'] ?></div>
                        <input type="hidden" name="user_id" value="<?php echo $data['id'] ?>" class="layui-input" style="width: 98%;">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">可提现余额</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid"><?php echo number_format(floatval($data['balance']), 2); ?> 元</div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">操作方式</label>
                    <div class="layui-input-block">
                        <input type="radio" name="mode" value="1" title="增加" checked>
                        <input type="radio" name="mode" value="2" title="减少">
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">金额</label>
                    <div class="layui-input-block">
                        <input type="text" name="amount"  placeholder="￥" lay-verify="required" autocomplete="off" class="layui-input" style="width: 98%;">
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
                        url = "/visa_admin.php?b=action&c=user_balance_edit&id=" + <?php echo $id; ?>;
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: url,
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
                                layer.closeAll('loading');
                            }
                        });

                    });
                });
            </script>
        <?php } else { ?>
            <blockquote class="layui-elem-quote layui-text">
                当前用户不存在
            </blockquote>
        <?php } ?>
    </body>
</html>