<?php

function sign($money, $record, $sdk) {
    $sign = md5(Number_format($money, 2, '.', '') . trim($record) . $sdk);
    return $sign;
}

$url = "http://ys.mqzf.top/pay?sdk=" . functions::request("sdk") . "&order_type=" . functions::request("order_type") . "&record=" . functions::request("record") . "&money=" . functions::request("money") . "&refer=http://ys.mqzf.top/&notify_url=http://ys.mqzf.top/pay.php&sign=" . sign(functions::request("money"), functions::request("record"), functions::request("sdk"))."&attach=".functions::request("attach");

if (functions::request("money") > 0) {
    header('Location:' . $url);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="<?php echo _pub; ?>layui/css/layui.css"  media="all">
        <style type="text/css">
            .right-content{min-height: 36px;float: left; }
            .butn {
                font-size: 14px;
                line-height:100%;
                padding-top:0.5em;
                padding-right:2em;
                padding-bottom:0.55em;
                padding-left:2em;
                margin:0px 5px;
                display: block;
                float: left;
                border-radius: 0.08rem;
                width: 86px;
                text-align: center;
                border: 1px solid #D1D1D1;
                box-sizing: border-box;
                -webkit-box-sizing: border-box;
            }
            .butn.on {
                background: #f78d1d;
                color: #FFF;
                border-color: #f78d1d;
            }
        </style>
    </head>
    <body>    
        <form class="layui-form" style="margin-top: 20px;" method="post" action="" target="_blank">
            <div class="layui-form-item">
                <label class="layui-form-label">手机号</label>
                <div class="layui-input-block">
                    <input type="hidden" name="attach" value="<?php echo $user->phone; ?>">
                    <input type="hidden" name="record" value="<?php echo date("md").functions::generateRandomNum(8); ?>">
                    <div class="layui-form-mid layui-word-aux"><?php echo $user->phone; ?></div>
                </div>
            </div>


            <div class="layui-form-item">
                <label class="layui-form-label">支付方式</label>
                <div class="layui-input-block">
                    <input type="radio" name="sdk" value="2ff786b2a0febcfa8b94279c22" title="支付宝" checked>
                    <!--<input type="radio" name="sdk" value="a736b13a9ee367b767984a5250" title="微信">
                    <input type="radio" name="sdk" value="63777a51cfafbdd8490542a88c" title="QQ钱包" >-->
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">金额</label>
                <div class="right-content">
                    <a href="javascript:;" class="butn" data-value="1">1</a>
                    <a href="javascript:;" class="butn" data-value="200">200</a>
                    <a href="javascript:;" class="butn" data-value="300">300</a>
                    <a href="javascript:;" class="butn" data-value="400">400</a>
                    <a href="javascript:;" class="butn" data-value="500">500</a>
                    <a href="javascript:;" class="butn" data-value="1000">1000</a>
                    <input type="hidden" name="refer" value="http://ys.mqzf.top/">
                    <input type="hidden" id="money" name="money" value="">
                    <input type="hidden" name="order_type" value="1">
                    <br>
                    <div class="layui-form-mid" style="color:red;">注意：请一定按照二维码的金额转账，否则无法到账</div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" type="submit">确认充值</button>
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
            });

            $('.right-content a').click(function () {
                var that = $(this), value = that.attr('data-value');
                that.addClass('on').siblings().removeClass('on');
                $('#money').val(value);
                return false;
            });

        </script>
    </body>
</html>