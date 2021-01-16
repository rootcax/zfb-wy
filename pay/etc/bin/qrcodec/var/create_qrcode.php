
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <title>创建订单</title>
        <link href="<?php echo _pub ?>css/pay_admin.css" rel="stylesheet" />
    </head>
    <body>
        <div class="c-container">
            <div class="c-head">
                <div class="c-return black">
                    <span>创建订单</span>
                </div>
            </div>
            <div class="c-step">
                <!-- <div class="item radius active">1</div>
                <div class="item spot active"><span></span><span></span><span></span></div>
                <div class="item radius active">2</div>
                <div class="item spot active"><span></span><span></span><span></span></div>
                <div class="item radius">3</div>
                <div class="item spot"><span></span><span></span><span></span></div>
                <div class="item radius">4</div>
                <div class="item spot"><span></span><span></span><span></span></div>
                <div class="item radius">5</div> -->
            </div>
            <div class="c-createOrder">
                <div id="progress" class="progress">
                    <input class="knob" readonly data-min="0" data-max="20">
                    <div class="timeDiv">
                        <p class="time">20s</p>
                        <p class="t1">loading...</p>
                    </div>
                </div>
                <p class="h1">订单创建中...</p>
                <p class="t1">
                    <span>20s</span>
                    后页面将自动跳转，若未跳转请刷新！
                </p>
            </div>
        </div>

        <script src="<?php echo _pub ?>js/jquery.min.js"></script>
        <script src="https://cdn.bootcss.com/jQuery-Knob/1.2.13/jquery.knob.min.js"></script>
        <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <script src="<?php echo _pub ?>js/admin.js"></script>
    </body>
    <script>
        var orderId = "<?php echo $data['order_sn'] ?>";
        var channelId = "superpay_bank2alipay";
        var bankCode = "CMB";
        var skinStyle = 0;
        var totalHeadStep = 3;
        var curHeadStep = 1;
        var nextHeadStep = curHeadStep + 1;
        //0：为支付宝样式 1：为微信样式
        tplSkinStyle(skinStyle);
        //总步骤数
        tplHeadStep(totalHeadStep);
        //当前步骤数
        activeHeadStep(curHeadStep);

        var time = 29;
        //开启定时器
        var timer = setInterval(countDown, 1000);
        //倒计时
        function countDown() {
            $("#progress .knob").val(10 - time).trigger('change');
            $("#progress .timeDiv .time").html(time + "s");
            if (skinStyle == 2) {
                window.location.href = "/api/toBank?orderId=" + orderId + "&nextHeadStep=" + nextHeadStep;
            } else {
                getPayOrder();
            }
            if (time <= 0) {
                // 关闭定时器
                clearInterval(timer);
                // 页面跳转
                if (curHeadStep == 1) {
                    //alert("创建二维码失败，即将自动刷新页面");
                } else {
                    alert("创建二维码失败，请重新获取验证码");
                }
                //window.location.href = "/api/pay?orderId=" + orderId;
            }
            time--;
        }

        // 获取订单信息
        function getPayOrder() {
            var timestamp = new Date().getTime();
            $.ajax({
                url: '/pay/qrcode.php?c=getPayOrder&time=' + timestamp,
                type: 'get',
                data: {
                    orderId: orderId
                },
                success: function (res) {
                    if (res.code == 200) {
                        var qrData = res.data.ma_qrcode;
                        if (qrData != null && qrData != "") {
                            window.location.href = "/pay/qrcode.php?order_no=" + orderId + "&step=" + nextHeadStep;
                        }
                    }
                    //获取二维码超时
                    if (res.code == '1001') {
                        alert(res.msg);
                    }
                }
            })
        }
    </script>
</html>
