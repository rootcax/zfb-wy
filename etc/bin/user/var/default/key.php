<?php require ('function.php'); ?>
<?php $csrf = functions::getcsrf(); ?>
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
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block">
                    <input type="hidden" name="id" value="<?php echo $user->sid; ?>">
                    <div class="layui-form-mid layui-word-aux"><?php echo $user->phone; ?></div>  
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">用户ID</label>
                <div class="layui-input-block">
                    <div class="layui-form-mid layui-word-aux"><?php echo $user->memberCode; ?></div>  
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">公钥</label>
                <div class="layui-input-block">
                    <?php if (empty($data['publicKey'])) { ?>
                        <input type="text" name="publicKey" placeholder="请输入公钥" class="layui-input" style="width: 98%;">
                        <div class="layui-form-mid layui-word-aux">请输入公钥</div>
                    <?php } else { ?>
                        <div class="layui-form-mid layui-word-aux">公钥已配置</div>
                    <?php } ?>
                </div>
            </div>

            <?php if (empty($data['publicKey'])) { ?>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button class="layui-btn" lay-submit type="button" lay-filter="add">提交</button>
                    </div>
                </div>
            <?php } ?>


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
                        url: "<?php echo functions::urlc('user', 'api', 'key_edit', array('csrf' => functions::getcsrf())) ?>",
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
                $.get("/?a=merchant&b=api&c=sms&typec=3&phone=<?php echo $user->phone; ?>&csrf=<?php echo $csrf; ?>", function (result) {
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