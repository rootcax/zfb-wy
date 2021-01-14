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
                <label class="layui-form-label">密码</label>
                <div class="layui-input-block">
                    <input type="password" name="password" class="layui-input" style="width: 98%;">
                    <input type="text" id="id" name="id" style="display: none"  value="<?php echo $data['id']; ?>">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit type="button" lay-filter="add">确认补发</button>
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

                form.on('submit(add)', function () {
                    layer.confirm('你确认要设置该订单为成功状态吗?', {
                        btn: ['确认', '取消'] //按钮
                    }, function () {
                        var id = $("#id").val();
                        var password = $("#password").val();
                        $(".layer-anim").remove();
                        $(".layui-layer-shade").remove();
                        layer.load(4, {shade: [0.6, '#fff']});
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: "<?php echo functions::urlc('user', 'api', 'take_api', array('csrf' => functions::getcsrf())) ?>",
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
            });


        </script>
    </body>
</html>