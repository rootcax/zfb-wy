<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>
            在线支付 - 支付宝 - 网上支付 安全快速！
        </title>
        <script type="text/javascript" src="<?php echo _theme_var; ?>css/alipay/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo _theme_var; ?>css/alipay/qrcode.js"></script>
        <script type="text/javascript" src="<?php echo _pub; ?>js/layer/layer.js"></script>
        <link charset="utf-8" rel="stylesheet" href="<?php echo _theme_var; ?>css/alipay/front-old.css" media="all">
        <style>
            .switch-tip-icon-img {
                position: absolute;
                left: 70px;
                top: 70px;
                z-index: 11;
            }
            #codeico{
                position:fixed;
                z-index:9999999;
                width:43px; 
                height:43px;
                background:url('<?php echo _theme_var; ?>css/alipay/images/T1Z5XfXdxmXXXXXXXX.png') no-repeat;
            }
            body{
                font-family:微软雅黑;	
            }
        </style>



    </head>

    <body>
        <div class="topbar">
            <div class="topbar-wrap fn-clear">
                <a href="https://help.alipay.com/lab/help_detail.htm?help_id=258086" class="topbar-link-last" target="_blank" seed="goToHelp">常见问题</a>
                <span class="topbar-link-first">你好，欢迎使用支付宝付款！</span>

            </div>
        </div>
        <div id="header">
            <div class="header-container fn-clear">
                <div class="header-title">
                    <div class="alipay-logo">
                    </div>
                    <span class="logo-title">
                        我的收银台
                    </span>
                </div>
            </div>
        </div>


        <div id="container">
            <div id="content" class="fn-clear">
                <div id="J_order" class="order-area">
                    <div id="order" class="order order-bow">
                        <div class="orderDetail-base">
                            <div class="commodity-message-row">
                                <span class="first long-content">
                                    收款方：<?php echo $username; ?>
                                </span> 交易单号：<?php echo $order_num; ?>　 ( 温馨提示：支付后可能会出现延迟30秒后提示成功，如有问题联请系客服)
                                <input id="order_num" value="<?php echo $order_num; ?>" style="display:none" />
                                <span class="second short-content">
                                    &nbsp;
                                </span>
                            </div>
                            <span class="payAmount-area" id="J_basePriceArea">
                                <strong class=" amount-font-22 "><?php echo $money; ?></strong> 元

                            </span>

                        </div>
                    </div>
                </div>
                <!-- 操作区 -->
                <div class="cashier-center-container">
                    <div data-module="excashier/login/2015.08.02/loginPwdMemberT" id="J_loginPwdMemberTModule" class="cashiser-switch-wrapper fn-clear">
                        <!-- 扫码支付页面 -->
                        <div class="cashier-center-view view-qrcode fn-left" id="J_view_qr">

                            <!-- 扫码区域 -->
                            <div data-role="qrPayArea" class="qrcode-integration qrcode-area" id="J_qrPayArea">
                                <div class="qrcode-header">
                                    <div class="ft-center">
                                        扫一扫付款（元）                  </div>
                                    <div class="ft-center qrcode-header-money"><?php echo $money; ?></div>
                                    <input id="money" value="<?php echo $money; ?>" style="display:none" />
                                </div>
                                <div class="qrcode-img-wrapper" id="payok">

                                    <div align="center">

<!-- <img class="switch-tip-icon-img" id="imagesok" src="<?php echo _theme_var; ?>css/alipay/T1Z5XfXdxmXXXXXXXX.png" alt="手机支付宝图标" width="42" height="42"> -->

                                        <font id="qrcode"><canvas width="168" height="168" style="display: none;"></canvas><img alt="Scan me!" id="image" style="display: block;width:168px;height:168px;" src="<?php
                                        if ($real == 1) {
                                            echo _theme_var . "css/loading.gif";
                                        } else {
                                            echo $image;
                                        }
                                        ?>"></font>
                                        <font id="queren"></font>
                                    </div>
                                    <div class="qrcode-img-explain fn-clear">
                                        <img class="fn-left" src="<?php echo _theme_var; ?>css/alipay/T1bdtfXfdiXXXXXXXX.png" alt="扫一扫标识">
                                        <div class="fn-left">
                                            打开手机支付宝<br><strong id="minute_show"><s></s>04分</strong>
                                            <strong id="second_show"><s></s>30秒</strong>过期</div>
                                    </div>
                                </div>
                                <div id="qrPayScanSuccess" class="mi-notice mi-notice-success  qrcode-notice fn-hide" style="display: none;margin-top: 5px;">
                                    <div class="mi-notice-cnt">
                                        <div class="mi-notice-title qrcode-notice-title">
                                            <i class="iconfont qrcode-notice-iconfont" title="扫描成功"></i>
                                            <p class="mi-notice-explain-other qrcode-notice-explain ft-break">
                                                <span class="ft-orange fn-mr5" data-role="qrPayAccount"></span>已创建订单，请在手机支付宝上完成付款
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <br>
          　　　　 　　<a href="https://mobile.alipay.com/index.htm" class="qrcode-downloadApp">首次使用请下载手机支付宝</a><br><br>
                            </div>

                            <!-- 指引区域 -->
                            <div class="qrguide-area">
                                <img src="<?php echo _theme_var; ?>css/alipay/T13CpgXf8mXXXXXXXX.png" class="qrguide-area-img active">              </div>
                        </div>

                    </div>



                </div>

            </div>
        </div>

        <div id="partner"><br><p>本站为第三方辅助软件服务商，与支付宝官方和淘宝网无任何关系<br>支付系统 不提供资金托管和结算，转账后将立即到达指定的账户。</p>
            <br><img alt="合作机构" src="<?php echo _theme_var; ?>css/alipay/2R3cKfrKqS.png"></div>
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
                    $('#day_show').html(day + "天");
                    $('#hour_show').html('<s id="h"></s>' + hour + '时');
                    $('#minute_show').html('<s></s>' + minute + '分');
                    $('#second_show').html('<s></s>' + second + '秒');
                    intDiff--;
                }, 1000);
            }


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
                var order_num = $("#order_num").val();
                var money = $("#money").val();
                $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: "<?php echo functions::getdomain() . 'api.php?c=get' ?>",
                    data: {"num": order_num, "money": money},
                    cache: false,
                    success: function (data) {
                        if (data.code == '200') {
                            window.clearInterval(orderlst);
                            layer.confirm(data.msg, {
                                icon: 1,
                                title: '支付成功',
                                btn: ['我知道了'] //按钮
                            }, function () {
                                location.href = "<?php echo functions::getdomain() . 'index.php?a=servlet&b=index&c=Refer&num=' ?>" + order_num;
                            });
                        }
                        //订单被销毁
                        else if (data.code == '1001') {
                            window.clearInterval(orderlst);
                            layer.confirm(data.msg, {
                                icon: 2,
                                title: '订单错误',
                                btn: ['确认'] //按钮
                            }, function () {
                                location.href = "<?php echo functions::getdomain() . 'index.php?a=servlet&b=index&c=Refer&num=' ?>" + order_num;
                            });
                        }
                        //订单已经超时
                        else if (data.code == '1002') {
                            window.clearInterval(orderlst);
                            layer.confirm(data.msg, {
                                icon: 2,
                                title: '支付超时',
                                btn: ['确认'] //按钮
                            }, function () {
                                location.href = "<?php echo functions::getdomain() . 'index.php?a=servlet&b=index&c=Refer&num=' ?>" + order_num;
                            });
                        }
                    },
                    error: function (data) {
                        alert("error:" + data.responseText);
                    }
                });
            }
//周期监听
<?php if ($real != 1) { ?>
                var orderlst = setInterval("order()", 2000);
                $(function () {
                    timer(intDiff);
                });
<?php } ?>
<?php if ($real == 1) { ?>
                //二维码监控
                function qrcode() {
                    $.get("<?php echo functions::getdomain() . 'api.php?c=getQrcode&num=' . $order_num; ?>", function (result) {
                        //成功
                        if (result.code == '200') {
                            //回调页面
                            window.clearInterval(qrcodelst);
                            orderlst = setInterval("order()", 2000);
                            $(function () {
                                timer(intDiff);
                            });
                            $("#image").attr("src", "<?php echo functions::getdomain() . '?a=servlet&b=index&c=qrcode&text=' ?>" + result.data.qrcode);
                        }
                        //获取二维码超时
                        if (result.code == '1001') {
                            window.clearInterval(qrcodelst);
                            $('#show_qrcode').attr("src", "https://imgcdn2.xinlis.com/static/index/Images/qrcode_timeout.png");
                        }
                    });
                }
    //周期监
                var qrcodelst = setInterval("qrcode()", 2000);
                var orderlst;

<?php } ?>




        </script>

        <script language="Javascript">
            document.oncontextmenu = new Function("event.returnValue=false");
            document.onselectstart = new Function("event.returnValue=false");
        </script>
        <script type="text/javascript">
            document.oncontextmenu = function (e) {
                return false;
            }
        </script>
        <script type="text/javascript">
            document.onkeydown = function () {
                if (window.event && window.event.keyCode == 123) {
                    event.keyCode = 0;
                    event.returnValue = false;
                    return false;
                }
            };
            document.onkeydown = function (e) {
                e = window.event || e;
                var keycode = e.keyCode || e.which;
                if (keycode == 116) {
                    if (window.event) {// ie
                        try {
                            e.keyCode = 0;
                        } catch (e) {
                        }
                        e.returnValue = false;
                    } else {// firefox
                        e.preventDefault();
                    }
                }
            }
        </script>

    </body></html>