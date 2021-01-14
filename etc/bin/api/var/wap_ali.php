<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
        <title>在线支付 - 支付宝 - 网上支付 安全快速！</title>
        <link rel="stylesheet" type="text/css" href="<?php echo _theme_var; ?>mobile/QRCode.css">
        <script type="text/javascript" src="<?php echo _theme_var; ?>css/alipay/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo _theme_var; ?>css/alipay/qrcode.js"></script>
        <script type="text/javascript" src="<?php echo _pub; ?>js/layer/layer.js"></script>
    </head>
    <body>
        <div style="width: 100%; text-align: center;font-family:微软雅黑;">
            <div id="panelWrap" class="panel-wrap">
                <!-- CUSTOM LOGO -->
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <img src="<?php echo _theme_var; ?>mobile/T1HHFgXXVeXXXXXXXX.png" alt="Logo-QQPay" class="img-responsive center-block">
                        </div>

                    </div>
                </div>
                <!-- PANEL TlogoEMPLATE START -->
                <div class="panel panel-easypay">
                    <!-- PANEL HEADER -->
                    <div class="panel-heading">
                        <h3>

                            <small>收款方：<?php echo $username; ?></small>
                            <h3>
                                <small>订单号：<?php echo $order_num; ?></small>

                            </h3>
                            <div class="money">
                                <span class="price"><?php echo $money; ?></span>
                                <span class="currency">元</span>
                            </div>
                    </div>
                    <div class="panel-footer">
                        <!-- SYSTEM MESSAGE -->
                       <span id="Span1" class="warning" style="color:red;"><small>请长按二维码保存到相册 或 屏幕截图<br/>打开支付宝左上角扫一扫，点击右上角相册<br/>选择保存的二维码进行充值</small></span>                    </div>
                    <div class="qrcode-warp">
                        <div id="qrcode" title="">
                            <img id="image" src="<?php
                                echo $image;
                            ?>" style="display: block;"></div>
                    </div>
                    

                </div>
            </div>
        </div>
        <?php if ($msgInfo) { ?>
            <script type="text/javascript">
                layer.alert('<?php echo $msgInfo; ?>', {
                    icon: 1,
                    title: '支付提醒'
                });
            </script>
        <?php } ?>
        <script type="text/javascript">
            var intDiff = parseInt(270);//倒计时总秒数量
            function timer(intDiff) {
                window.setInterval(function () {
                    var day = 0,
                            hour = 0,
                            minute = 0,
                            second = 0;//时间默认值       
                    if (intDiff > 0) {
                        day = Math.floor(intDiff / (60 * 60 * 24));
                        hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
                        minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
                        second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
                    }
                    if (minute == 00 && second == 00)
                        document.getElementById('qrcode').innerHTML = '<br/><br/><br/><br/><br/><br/><br/><h2>二维码超时 请重新发起交易</h2><br/>';
                    if (minute <= 9)
                        minute = '0' + minute;
                    if (second <= 9)
                        second = '0' + second;
                    //$('#day_show').html(day+"天");
                    //$('#hour_show').html('<s id="h"></s>'+hour+'时');
                    //$('#minute_show').html('<s></s>'+minute+'分');
                    //$('#btnDL').valu('<s></s>'+second+'秒');
                    $('#btnDL').val('立即支付(' + minute + '分' + second + '秒' + ')');
                    intDiff--;
                }, 1000);
            }
            $(function () {
                timer(intDiff);
            });

            // 订单详情
            $('#orderDetail .arrow').click(function (event) {
                if ($('#orderDetail').hasClass('detail-open')) {
                    $('#orderDetail .detail-ct').slideUp(500, function () {
                        $('#orderDetail').removeClass('detail-open');
                    });
                } else {
                    $('#orderDetail .detail-ct').slideDown(500, function () {
                        $('#orderDetail').addClass('detail-open');
                    });
                }
            });

            //订单监控  {订单监控}
            function order() {
                $.get("<?php echo functions::getdomain() . 'api.php?c=get&num=' . $order_num; ?>", function (result) {
                    //成功
                    if (result.code == '200') {
                        //回调页面
                        window.clearInterval(orderlst);
                        layer.confirm(result.msg, {
                            icon: 1,
                            title: '支付成功',
                            btn: ['我知道了'] //按钮
                        }, function () {
                            location.href = "<?php echo functions::getdomain() . 'index.php?a=servlet&b=index&c=Refer&num=' . $order_num ?>";
                        });
                    }
                    //订单被销毁
                    if (result.code == '1001') {
                        window.clearInterval(orderlst);
                        layer.confirm(result.msg, {
                            icon: 2,
                            title: '订单错误',
                            btn: ['确认'] //按钮
                        }, function () {
                            location.href = "<?php echo functions::getdomain() . 'index.php?a=servlet&b=index&c=Refer&num=' . $order_num ?>";
                        });
                    }
                    //订单已经超时
                    if (result.code == '1002') {
                        window.clearInterval(orderlst);
                        layer.confirm(result.msg, {
                            icon: 2,
                            title: '支付超时',
                            btn: ['确认'] //按钮
                        }, function () {
                            location.href = "<?php echo functions::getdomain() . 'index.php?a=servlet&b=index&c=Refer&num=' . $order_num ?>";
                        });
                    }
                });
            }
            //周期监听
            var orderlst = setInterval("order()", 2000);
            //二维码监控
            function qrcode() {
                $.get("<?php echo functions::getdomain() . 'api.php?c=getQrcode&num=' . $order_num; ?>", function (result) {
                    //成功
                    if (result.code == '200') {
                        //回调页面
                        window.clearInterval(qrcodelst);
                        $("#image").attr("src", "<?php echo functions::getdomain() . '?a=servlet&b=index&c=qrcode&text=' ?>" + result.data.qrcode);
                        $('#link').removeAttr("disabled");
                        $("#link").attr("href", result.data.qrcode);
                    }
                    //获取二维码超时
                    if (result.code == '1001') {
                        window.clearInterval(qrcodelst);
                        $('#show_qrcode').attr("src", "https://imgcdn2.xinlis.com/static/index/Images/qrcode_timeout.png");
                    }
                });
            }

            function pay(url) {
<?php if ($real == 2) { ?>
                    window.location.href = url;
<?php } else if ($real == 3) { ?>
                    //window.location.href = "alipays://platformapi/startapp?appId=20000067&url=" + url;
                    window.location.href = url;
<?php } else { ?>
                    window.location.href = url;
<?php } ?>
            }

<?php if ($real == 1) { ?>
                //周期监
                var qrcodelst = setInterval("qrcode()", 2000);
<?php } ?>

        </script>
    </body></html>