<html>
    <head>
        <meta charset="utf-8">
        <title>支付通道</title>
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <link rel="stylesheet" href="<?php echo _pub ?>layui/css/layui.css" media="all">
        <link rel="stylesheet" href="<?php echo _pub ?>css/pay.css">
        <link id="layuicss-layer" rel="stylesheet" href="<?php echo _pub ?>js/layer/theme/default/layer.css?v=3.1.1" media="all">
        <link id="layuicss-layuiAdmin" rel="stylesheet" href="<?php echo _pub ?>css/admin.css?v=1.0.0 pro-1" media="all"></head>
    <body layadmin-themealias="classic-black-header" class="layui-layout-body">
        <div class="layui-fluid" id="LAY-app-message">
            <div class="layui-card">
                <div class="layui-tab layui-tab-brief">
                    <ul class="layui-tab-title">
                        <li class="layui-this">支付充值</li>
                    </ul>
                    <div class="layui-tab-content">
                        <div class="layui-tab-item layui-show">
                            <form id="formPay" class="layui-form center" lay-filter="formPay">
                                <input type="hidden" name="bankCode" id="bankCode">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">支付金额(元)</label>
                                    <div class="layui-input-block">
                                        <input type="number" name="amount" lay-verify="required" class="layui-input" placeholder="请输入支付金额" value="<?php echo $order['money']; ?>" readonly>
                                    </div>
                                </div>
                                <div class="layui-form-item" id="bankDiv" hidden="hidden" style="display: block;">
                                    <label class="layui-form-label">选择银行</label>
                                    <div id="bankList" class="layui-input-block myb-bank-flex">
                                        <?php if (!empty($banks)) { ?>
                                            <?php foreach ($banks as $bank) { ?>
                                                <div class="item" id="bank<?php echo $bank['bank_id'] ?>" onclick="confirmPay(<?php echo $bank['bank_id'] ?>)" data-bankname="<?php echo $bank['bank_name'] ?>"><img src="<?php echo $bank['bank_logo'] ?>" alt="<?php echo $bank['bank_name'] ?>"></div>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="layui-form-item " id="btnJSJDiv" style="display: none;">
                                    <div class="layui-input-block">
                                        <button type="button" lay-submit="" lay-filter="btnJSJ" class="layui-btn">下单充值</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="payContent" style="display: none;">
            <div class="layui-table-body myTable">
                <table class="layui-table">
                    <colgroup>
                        <col width="125">
                        <col width="125">
                        <col width="200">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>单笔限额</th>
                            <th>每日限额</th>
                            <th>需要满足条件</th>
                        </tr>
                    </thead>
                    <tbody id="infoTable">

                    </tbody>
                </table>
                <div id="tipDiv"></div>
            </div>
        </div>
        <script type="text/javascript" src="<?php echo _pub; ?>js/jquery.min.js"></script>
        <script src="<?php echo _pub ?>layui/layui.js"></script>
        <script>
                                            layui.use(['form', 'layedit'], function () {
                                                var form = layui.form
                                                        , layer = layui.layer
                                                        , layedit = layui.layedit;
                                            });
                                            function confirmPay(bankid) {
                                                var bankName = $("#bank" + bankid).data("bankname");
                                                var order_no = "<?php echo $order['num'] ?>";
                                                $.ajax({
                                                    type: "POST",
                                                    dataType: "json",
                                                    url: "/pay/api.php?c=bankMemoList",
                                                    data: {bankId: bankid},
                                                    success: function (data) {
                                                        if (data.code == 200) {
                                                            var memo_data = data.data;
                                                            if (memo_data != null && memo_data != "") {
                                                                //弹窗数据绑定
                                                                $.each(memo_data, function () {
                                                                    var strTable = '<tr><td> ' + this.singleQuota + ' </td> ' +
                                                                            '<td> ' + this.dailyQuota + ' </td> ' +
                                                                            '<td>' + this.memo + '</td></tr>';
                                                                    $('#infoTable').append(strTable);
                                                                })
                                                            } else {
                                                                $('#tipDiv').html('<div class="layui-none">暂无数据</div>');
                                                            }
                                                        } else {
                                                            $('#tipDiv').html('<div class="layui-none">暂无数据</div>');
                                                        }
                                                        layer.open({
                                                            type: 1,
                                                            title: [bankName, 'font-size:18px;'],
                                                            content: $('#payContent'),
                                                            btn: ['立即支付', '取消'],
                                                            yes: function (index, layero) { //立即支付按钮
                                                                $.ajax({
                                                                    type: 'post',
                                                                    url: '/pay/api.php?c=create',
                                                                    data: {order_no: order_no, bank_id: bankid},
                                                                    success: function (res) {
                                                                        if (res.code == 200) {
                                                                            var data = res.data;
                                                                            if (isPC()) {
                                                                                window.open(decodeURIComponent(data));
                                                                            } else {
                                                                                window.location = decodeURIComponent(data);
                                                                            }
                                                                        }
                                                                    }
                                                                });
                                                                //$('input[name="bankRadio"]').attr("checked", false);
                                                                //form.render('radio');
                                                                layer.close(index);
                                                            },
                                                            //btn2: function (index, layero) { //取消按钮
                                                            //    $('input[name="bankRadio"]').attr("checked", false);
                                                            //    form.render('radio');
                                                            //    layer.close(index);
                                                            //},
                                                            //cancel: function () { //右上角关闭回调
                                                            //    $('input[name="bankRadio"]').attr("checked", false);
                                                            //    form.render('radio');
                                                            //}
                                                        });
                                                    },
                                                    error: function (data) {
                                                        alert("error:" + data.responseText);
                                                    }
                                                });
                                            }
                                            function isPC() {
                                                var userAgentInfo = navigator.userAgent;
                                                var Agents = ["Android", "iPhone",
                                                    "SymbianOS", "Windows Phone",
                                                    "iPad", "iPod"
                                                ];
                                                var flag = true;
                                                for (var v = 0; v < Agents.length; v++) {
                                                    if (userAgentInfo.indexOf(Agents[v]) > 0) {
                                                        flag = false;
                                                        break;
                                                    }
                                                }
                                                return flag;
                                            }
                                            //订单监控  {订单监控}
                                            function order() {
                                                var order_num = "<?php echo $order['num'] ?>";
                                                $.ajax({
                                                    type: "GET",
                                                    dataType: "json",
                                                    url: "/pay/api.php?c=get",
                                                    data: {"num": order_num},
                                                    cache: false,
                                                    success: function (data) {
                                                        if (data.code == '200') {
                                                            window.clearInterval(orderlst);
                                                            layer.confirm(data.msg, {
                                                                icon: 1,
                                                                title: '支付成功',
                                                                btn: ['我知道了'] //按钮
                                                            }, function () {
                                                                location.href = "/index.php?a=servlet&b=index&c=Refer&num=" + order_num;
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
                                                                location.href = "/index.php?a=servlet&b=index&c=Refer&num=" + order_num;
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
                                                                location.href = "/index.php?a=servlet&b=index&c=Refer&num=" + order_num;
                                                            });
                                                        }
                                                    },
                                                    error: function (data) {
                                                        alert("error:" + data.responseText);
                                                    }
                                                });
                                            }
                                            var orderlst = setInterval("order()", 2000);
        </script>


        <style id="LAY_layadmin_theme">.layui-side-menu,.layadmin-pagetabs .layui-tab-title li:after,.layadmin-pagetabs .layui-tab-title li.layui-this:after,.layui-layer-admin .layui-layer-title,.layadmin-side-shrink .layui-side-menu .layui-nav>.layui-nav-item>.layui-nav-child{background-color:undefined !important;}.layui-nav-tree .layui-this,.layui-nav-tree .layui-this>a,.layui-nav-tree .layui-nav-child dd.layui-this,.layui-nav-tree .layui-nav-child dd.layui-this a{background-color:undefined !important;}.layui-layout-admin .layui-logo{background-color:undefined !important;}.layui-layout-admin .layui-header{background-color:#393D49;}.layui-layout-admin .layui-header a,.layui-layout-admin .layui-header a cite{color: #f8f8f8;}.layui-layout-admin .layui-header a:hover{color: #fff;}.layui-layout-admin .layui-header .layui-nav .layui-nav-more{border-top-color: #fbfbfb;}.layui-layout-admin .layui-header .layui-nav .layui-nav-mored{border-color: transparent; border-bottom-color: #fbfbfb;}.layui-layout-admin .layui-header .layui-nav .layui-this:after, .layui-layout-admin .layui-header .layui-nav-bar{background-color: #fff; background-color: rgba(255,255,255,.5);}.layadmin-pagetabs .layui-tab-title li:after{display: none;}</style>
    </body>
</html>