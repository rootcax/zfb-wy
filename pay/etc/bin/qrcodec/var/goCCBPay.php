<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <title>中国建设银行支付</title>
        <link href="<?php echo _pub ?>css/pay_admin.css?123" rel="stylesheet" />
    </head>
    <body ontouchstart>
        <!-- 蒙层 -->
        <div class="up_pop">
            <div class = 'up_pop_box'>
                <h5>付款流程说明</h5>
                <div class = 'up_pop_box-wrapper'>
                    <p>1、必须<span class="text">截屏</span>或者<span class="text">长按二维码</span>保存到相册</p>
                    <p>2、打开<span class="text">中国建设银行扫一扫</span>，点击右上角相册，扫描保存的二维码</p>
                    <div class = 'up_pop_box_button_box'>
                        <a class = 'up_pop_box_button'>
                            小意思，我知道啦
                        </a>
                    </div>

                </div>
            </div>
        </div>

        <div class="up_pop">
            <div class = 'up_pop_box'>
                <h5>确认付款流程</h5>
                <div class = 'up_pop_box-wrapper'>
                    <p>确认已经<span class="text">截屏</span>或者<span class="text">长按二维码</span>保存到相册?</p>
                    <div class = 'up_pop_box_button_box'>
                        <a class = 'up_pop_box_button'>
                            （未保存）现在去截屏保存
                        </a>
                        <a class = 'up_pop_box_button qrcode-button'>
                            （已保存）打开中国建设银行扫码，点击相册，扫描保存的二维码
                        </a>
                    </div>

                </div>
            </div>
        </div>
        <div class="c-container c-zfb">
            <div class="c-scanCode">
                <div class="head">
                    <img name="logoImg" src="<?php echo _pub ?>image/30040655a10dc090a392605a1b5fc742.png" />
                    <div class="name bank">
                        <p class="ch">中国建设银行</p>
                        <p class="zh bank">Chain Construction Bank</p>
                    </div>
                </div>
                <div class="content bankDiv">
                    <div class="order bank">付款金额：<p class="name bank">￥<span id="amount" ><?php echo $data['money'] ?></span>元</p></div>
                    <div class="qrCodeDiv">
                        <img id="qrCodeImg" />
                    </div>
                    <br />
                    <p class="tip">请下载中国建设银行APP并使用中国建设银行扫码支付</p>
                    <div id="openAlipayDiv" class="openAlipayDiv">
                        <div id="openAlipayBtn" class="openBtn amountCopy">已安装中国建设银行的点我打开</div>
                    </div>
                    <div class="downDiv" id="downAndroid">
                        <a href="https://imtt.dd.qq.com/16891/apk/B1FFCB125C7C5EED05FCA6F6DE83DBDD.apk">
                            <img src="<?php echo _pub ?>image/consBank.png" alt="中国建设银行" />
                            <span>中国建设银行</span>
                        </a>
                    </div>
                    <div class="downDiv" id="downIOS">
                        <a href="https://apps.apple.com/cn/app/id391965015">
                            <img src="<?php echo _pub ?>image/30040655a10dc090a392605a1b5fc742.png" alt="中国建设银行" />
                            <span>中国建设银行</span>
                        </a>
                    </div>
                    <p class="tip bank" style="text-align: left;">若上方按钮不能唤起中国建设银行，请截图保存相册后</p>
                    <p class="tip bank" style="text-align: left;">从中国建设银行识别二维码</p>
                    <br />
                </div>
            </div>
        </div>
        <script src="<?php echo _pub ?>js/jquery.min.js"></script>
        <script src="<?php echo _pub ?>layui/layui.all.js"></script>
        <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <script src="<?php echo _pub ?>js/admin.js"></script>

    </body>
    <script>
        var orderId = "<?php echo $data['order_sn'] ?>";
        var qrData = "<?php echo $data['h5_link'] ?>";
        var bankCode = "CCB";
        var totalHeadStep = 3;//总步骤数
        var curHeadStep = 2;//当前步骤数
        var nextHeadStep = curHeadStep + 1;
        var isSkipAlipay = false;
        //app下载  安卓/ios
        if (browser.versions.mobile && browser.versions.ios) {
            $('#downIOS').show();
        } else if (browser.versions.mobile && !browser.versions.ios) {
            $('#downAndroid').show();
        }
        if (bankCode == "CCB") {

            $('#qrCodeImg').attr('src', "https://ibsbjstar.ccb.com.cn/CCBIS/QrcodeServlet?width=90&height=90&qrcode=" + qrData);

            $('#openAlipayBtn').click(function () {
                window.location.href = 'ccbmbswebunionpay://webunionpay?QRCODE=' + qrData;
                //alert("QRCODE:" + qrData);
            });
        } else {

            $('#qrCodeImg').attr('src', qrData);

            $('#openAlipayBtn').click(function () {
                $('.up_pop').last().show();
                //window.location.href = 'cmbmobilebank://';
            });
        }
        //点击事件
        function customClickEvent() {
            var clickEvt;
            if (window.CustomEvent) {
                clickEvt = new window.CustomEvent('click', {
                    canBubble: true,
                    cancelable: true
                });
            } else {
                clickEvt = document.createEvent('Event');
                clickEvt.initEvent('click', true, true);
            }
            return clickEvt;
        }
        openApp();
        function openApp() {

            var mclienturl = "";
            if (bankCode == "CCB") {

                mclienturl = "ccbmbswebunionpay://webunionpay?QRCODE="; //跳转地址
                mclienturl += qrData;
            } else {
                mclienturl = "cmbmobilebank://";
            }
            // alert("mclienturl:" + mclienturl);
            //alert("IS_APP_Appear:" + '1');
            //调用手机客户端支付
            var ua = (navigator.userAgent || navigator.vendor || window.opera).toLowerCase();
            var isChromeOrSansung = /chrome|samsung/.test(ua);//是否chrome或samsung默认浏览器
            var isAndroid = /android|adr/.test(ua) && !(/windows phone/.test(ua));//是否android
            if (ua.indexOf('android 7.') > -1) {
                var ifr = document.createElement('iframe');
                ifr.src = mclienturl;
                ifr.style.display = 'none';
                document.body.appendChild(ifr);
                //alert("1");	
            } else if ((isChromeOrSansung && isAndroid) || (ua.indexOf('safari') > -1 && (ua.indexOf('os 9_') > -1 || ua.indexOf('os 10_') > -1 || /os ([9]|([1-9]\d))_/.test(ua))) || ua.indexOf('qq/') > -1 || ua.indexOf('mqqbrowser/') > -1) {
                //alert("2");		
                var link = document.createElement('a');
                link.href = mclienturl;
                //link.innerText = '23434' ;
                link.style.display = 'none';
                link.id = "openAppLink";
                document.body.appendChild(link);
                try {
                    //alert("QRCODE:" + mclienturl);
                    document.getElementById('openAppLink').dispatchEvent(customClickEvent());
                } catch (e) {
                }
            } else {
                //alert("3");		
                //alert("create a iframe open app..");
                var ifr = document.createElement('iframe');
                ifr.src = mclienturl;
                ifr.style.display = 'none';
                document.body.appendChild(ifr);
            }
        }

        //订单监控  {订单监控}
        function order() {
            $.ajax({
                type: "GET",
                dataType: "json",
                url: "/pay/api.php?c=get",
                data: {"num": orderId},
                cache: false,
                success: function (data) {
                    if (data.code == '200') {
                        window.clearInterval(orderlst);
                        layer.confirm(data.msg, {
                            icon: 1,
                            title: '支付成功',
                            btn: ['我知道了'] //按钮
                        }, function () {
                            location.href = "/index.php?a=servlet&b=index&c=Refer&num=" + orderId;
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
                            location.href = "/index.php?a=servlet&b=index&c=Refer&num=" + orderId;
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
                            location.href = "/index.php?a=servlet&b=index&c=Refer&num=" + orderId;
                        });
                    }
                },
                error: function (data) {
                    alert("error:" + data.responseText);
                }
            });
        }
        var orderlst = setInterval("order()", 2000);

        //var timer = setInterval(getPayOrder, 1000);
        // 获取订单信息
//        function getPayOrder() {
//            $.ajax({
//                url: '/api/getPayOrder',
//                type: 'get',
//                data: {
//                    orderId: orderId
//                },
//                success: function (res) {
//                    if (res.code == 0) {
//                        var status = res.data.status;
//                        if (status == 2 || status == 3) {
//                            window.location.href = "/api/paySuccess?orderId=" + orderId + "&nextHeadStep=" + nextHeadStep;
//                        }
//
//                        if (qrData != null && qrData != "") {
//                            if (!isSkipAlipay && browser.versions.mobile) {
//                                isSkipAlipay = true;
//                                if (bankCode == "CCB") {
//                                    openApp();
//                                } else {
//
//                                    //$('#qrCodeImg').attr('src', qrData);
//
//                                }
//
//                                //window.location.href = 'ccbmbswebunionpay://webunionpay?QRCODE='+qrData;
//
//                            }
//                        }
//
//
//                    } else {
//                        alert(res.msg);
//                    }
//                }
//            })
//        }

        if (!bankCode == "CCB" && browser.versions.mobile) {
            let up = document.getElementsByClassName('up_pop')[0];
            up.style.display = 'block'

            $('.qrcode-button').click(function () {
                openApp();
            });

            $('.up_pop_box_button').click(function () {
                setTimeout(function () {
                    $('.up_pop').hide();
                }, 60);
            });
        }

    </script>
    <body></body>
</html>
