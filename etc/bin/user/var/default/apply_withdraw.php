<?php require ('function.php'); ?>
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
            <?php if ($user->bank->type == 1) { ?>
                <div class="layui-form-item">
                    <label class="layui-form-label">姓名</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux"><?php echo $user->bank->name; ?></div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">支付宝账号</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux"><?php echo $user->bank->card; ?></div>
                    </div>
                </div>
            <?php } ?>

            <?php if ($user->bank->type == 2) { ?>
                <div class="layui-form-item">
                    <label class="layui-form-label">姓名</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux"><?php echo $user->bank->name; ?></div>
                    </div>
                </div>

                <div class="layui-form-item">
                    <label class="layui-form-label">银行名称</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux"><?php echo $user->bank->bank; ?></div>
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">银行卡号</label>
                    <div class="layui-input-block">
                        <div class="layui-form-mid layui-word-aux"><?php echo $user->bank->card; ?></div>
                    </div>
                </div>

            <?php } ?>
            <div class="layui-form-item">
                <label class="layui-form-label">金额</label>
                <div class="layui-input-block">
                    <input type="text" name="money" placeholder="需要提现的金额" value="<?php echo $balance ?>" class="layui-input" style="width: 98%;" <?php if(empty($balance)){ ?> disabled="disabled"<?php }?>>
                    <div class="layui-form-mid layui-word-aux">可提现金额：<b style="color: red;"><?php echo $balance ?> 元</b></div>
                </div>
            </div>
            <?php $sms_config = functions::get_Config('smsCog');  if($sms_config['withdraw_sms']){?>
            <div class="layui-form-item">
                <label class="layui-form-label">验证码</label>
                <div class="layui-input-block">
                    <input type="text" name="code" class="layui-input" style="width: 150px;float:left;">
                    <button class="layui-btn layui-btn-normal" type="button" style="width:120px;float:left;margin-left:10px;" id="sms" <?php if(!empty($balance)){ ?> onclick="getCode();"<?php } ?> >发送验证码</button>
                </div>
            </div>
            <?php }?>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" type="button" onclick="apply();" <?php if(empty($balance)){ ?> disabled="disabled"<?php } ?>>确认提现</button>
                </div>
            </div>
        </form>

        <script src="<?php echo _pub; ?>js/jquery.min.js" charset="utf-8"></script>

        <script type="text/javascript">
    function apply() {
        layer.load();
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "<?php echo functions::urlc('user', 'action', 'applyWithdraw') ?>",
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
    }

    //获取验证码
    var countdown = 90;
    function getCode() {
        var obj = $("#sms");
        var csrf = $('#csrf').val();
        layer.load();
        $.get("/?a=user&b=api&c=sms&typec=3&phone=<?php echo $user->phone; ?>&csrf=<?php echo $csrf; ?>", function (result) {
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
        <script src="<?php echo _theme;?>user/js/layer/3.0/layer.js"></script>
    </body>
</html>