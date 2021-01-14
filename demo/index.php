<?php
include './config.php';
date_default_timezone_set("Asia/Shanghai");
$currentTime = date("YmdHis");
$orderNo = "SH" . $currentTime; //流水号
?>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
        <meta name="format-detection" content="telephone=no" />
        <title>支付对接实例（demo）</title>
        <style type="text/css">
            body{padding:0;margin:0;color:#333;font-size:14px;font-family:微软雅黑;}
            ul,li{padding:0;margin:0;list-style:none;}
            .top{width:100%; height:60px;position: fixed; top:0;left:0;z-index:100;}
            .top-head{background-color:#18b4ed; text-align:center;color:#fff; height:45px; line-height:45px;}
            .top-nav{ height:45px; line-height:45px; background-color:#f0f0f0;}
            .top-nav li{width:50%; float:left;color:#777;border-bottom:2px solid #777; text-align:center;}
            .top-nav li.thisclass{border-bottom:2px solid #e70000;color: #e70000;background-color:#fff;}
            .content{width:98%;padding:1%;position:absolute; top:50px;left:0;z-index:99;}
            .ui-form-item{ height:45px; line-height:40px; width:100%;padding:5px 0; border-bottom:1px solid #e0e0e0;}
            .ui-form-item label{width:100px; display:block; float:left; line-height:20px;position:absolute;}
            input,select{ height:40px;line-height:40px; float:left;color:#18b4ed;width:100%;font-size:13px; border:0; padding-left:100px;}
            .ui-btn-lg{ background-color:#18b4ed;color:#fff; height:45px; line-height:45px; text-align:center;border-radius:3px; margin-top:10px;cursor:pointer;}
            .top-nav li{cursor:pointer;}
        </style>
    </head>
    <body>
        <div style="width:100%; min-width:320px;">
            <div class="top">
                <div class="top-nav">
                    <ul>
                        <li class="thisclass">支付接口</li>
                        <!--<li>查询接口</li>-->
                    </ul>
                </div>
            </div>
            <div class="content">
                <div class="content-pay">
                    <form name="payForm" id="payForm" action="./pay.php" autocomplete="off" method="post" target="_blank">
                        <div class="ui-form-item">
                            <label>
                                <div>支付地址</div>
                                <div style="font-size:12px;"> ( payUrl )</div>
                            </label>
                            <input name="payUrl" type="text" value="<?php echo $payUrl; ?>" readonly="readonly"/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>充值金额</div>
                                <div style="font-size:12px;"> ( amount )</div>
                            </label>
                            <input name="amount" type="text"  placeholder="充值金额 ( 单位元，两位小数 ) " value="500.00"/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>当前时间</div>
                                <div style="font-size:12px;"> ( currentTime )</div>
                            </label>
                            <input name="currentTime" type="text" onkeyup="this.value = this.value.replace(/\D/g, '')" onafterpaste="this.value=this.value.replace(/\D/g,'')" placeholder="当前时间 ( 格式为：yyyyMMddHHmmss，例如：20180101235959 ) " value="<?php echo $currentTime; ?>"/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>商 户 号</div>
                                <div style="font-size:12px;"> ( merchant )</div>
                            </label>
                            <input name="merchant" type="text" onkeyup="this.value = this.value.replace(/\D/g, '')" onafterpaste="this.value=this.value.replace(/\D/g,'')" placeholder="请输入商户号" value="<?php echo $merchant; ?>"/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>异步回调</div>
                                <div style="font-size:12px;"> ( notifyUrl )</div>
                            </label>
                            <input name="notifyUrl" type="text" placeholder="异步回调地址 ( 返回支付结果 ) " value="<?php echo $notifyUrl; ?>" autocomplete="off"/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>订单号</div>
                                <div style="font-size:12px;"> ( orderNo )</div>
                            </label>
                            <input name="orderNo" type="text" placeholder="商户订单号" value="<?php echo $orderNo; ?>" autocomplete="off"/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>支付类型</div>
                                <div style="font-size:12px;"> ( payType )</div>
                            </label>
                            <div style="padding-left:100px;">
                                <select name="payType" style=" padding-left:0;">
                                    <option value="bank2alipay" >网银</option>
                                </select>
                            </div>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>银行类型</div>
                                <div style="font-size:12px;"> ( bank )</div>
                            </label>
                            <div style="padding-left:100px;">
                                <select name="bank" style=" padding-left:0;">
                                    <option value="" >银行类型</option>
                                    <option value="ICBC" >中国工商银行</option>
                                    <option value="CCB" >中国建设银行</option>
                                    <option value="ABC" >中国农业银行</option>
                                    <option value="PSBC" >中国邮政储蓄银行</option>
                                    <option value="COMM" >交通银行</option>
                                    <option value="CMB" >招商银行</option>
                                    <option value="BOC" >中国银行</option>
                                    <option value="CEB" >中国光大银行</option>
                                    <option value="CITIC" >中信银行</option>
                                    <option value="SPDB" >浦发银行</option>
                                    <option value="CIB" >兴业银行</option>
                                    <option value="SPABANK" >平安银行</option>
                                    <option value="GDB" >广发银行</option>
                                    <option value="SHRCB" >上海农商银行</option>
                                    <option value="SHBANK" >上海银行</option>
                                    <option value="NBBANK" >宁波银行</option>
                                    <option value="HZCB" >杭州银行</option>
                                    <option value="BJBANK" >北京银行</option>
                                    <option value="BJRCB" >北京农商行</option>
                                    <option value="FDB" >富滇银行</option>
                                    <option value="WZCB" >温州银行</option>
                                    <option value="CDCB" >成都银行</option>
                                    <option value="CSRCB" >常熟农商银行</option>
                                    <option value="HXBANK" >华夏银行</option>
                                    <option value="NJCB" >南京银行</option>
                                    <option value="WJRCB" >苏州农村商业银行</option>
                                </select>
                            </div>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>备注信息</div>
                                <div style="font-size:12px;"> ( remark )</div>
                            </label>
                            <input name="remark" type="text" placeholder="备注信息 ( 该备注信息会通过异步回调接口回调 ) " value="12345" autocomplete="off"/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>同步回调</div>
                                <div style="font-size:12px;"> ( returnUrl )</div>
                            </label>
                            <input name="returnUrl" type="text" placeholder=" 	同步回调地址 ( 支付成功或订单超时自动跳转的地址 ) " value="<?php echo $returnUrl; ?>" autocomplete="off"/>
                        </div>
                        <div class="ui-form-item">
                            <div class="ui-btn-lg" name="pay_submit">提交支付</div>
                        </div>
                    </form>
                </div>
                <div class="content-check" style="display:none;">
                    <form name="checkForm" id="checkForm" action="./query.php" autocomplete="off" method="post">
                        <div class="ui-form-item">
                            <label>
                                <div>查询地址</div>
                                <div style="font-size:12px;"> ( queryUrl )</div>
                            </label>
                            <input name="queryUrl" type="text" value="<?php echo $queryUrl; ?>" readonly="readonly"/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>创建时间</div>
                                <div style="font-size:12px;"> ( createTime )</div>
                            </label>
                            <input name="createTime" class="time" type="text" value=""  placeholder="创建时间 ( 格式为：yyyyMMddHHmmss，例如：20180101235959 ) "/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>当前时间</div>
                                <div style="font-size:12px;"> ( currentTime )</div>
                            </label>
                            <input name="currentTime" value="<?php echo $currentTime; ?>" type="text"  placeholder="当前时间 ( 格式为：yyyyMMddHHmmss，例如：20180101235959 )）"/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>商户号</div>
                                <div style="font-size:12px;"> ( merchant )</div>
                            </label>
                            <input name="merchant" type="text" onkeyup="this.value = this.value.replace(/\D/g, '')" onafterpaste="this.value=this.value.replace(/\D/g,'')" placeholder="请输入商户号" value="<?php echo $merchant; ?>"/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>商户订单号</div>
                                <div style="font-size:12px;"> ( orderNo )</div>
                            </label>
                            <input name="orderNo" type="text"  placeholder="商户订单号" value=""/>
                        </div>
                        <div class="ui-form-item">
                            <label>
                                <div>密&nbsp;钥</div>
                                <div style="font-size:12px;"> ( key )</div>
                            </label>
                            <input name="key" type="text" placeholder="请输入商户密钥" value="<?php echo $key; ?>" autocomplete="off"/>
                        </div>
                        <div class="ui-form-item ui-btn-wrap">
                            <div class="ui-btn-lg ui-btn-danger" name="check_submit">提交查询</div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="./statics/js/zepto.min.js"></script>
        <script type="text/javascript">
                                $(function () {
                                    $(".top-nav li").on("click", function () {
                                        $(this).parent().find("li").removeClass("thisclass");
                                        $(this).addClass("thisclass");
                                        var index = $(this).index();
                                        $(".content > div").hide();
                                        $(".content > div").eq(index).show();
                                    });
                                })
                                $("div[name='pay_submit']").on("click", function () {
                                    $("form[name='payForm']").submit();
                                });

                                $("div[name='check_submit']").on("click", function () {
                                    $("form[name='checkForm']").submit();
                                });

                                //去掉input输入内容的首尾空格
                                $("input.time").on("input", function () {
                                    var str = $(this).val();
                                    str = str.replace(/\:|\-|\s/g, "");
                                    if (!!str) {
                                        $(this).val(str.trim());
                                    }
                                });

        </script>
    </body>
</html>