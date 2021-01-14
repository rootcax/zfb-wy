<?php
include './config.php';
$payUrl = $_POST['payUrl']; //提交地址
$amount = number_format(floatval($_POST['amount']), 2, '.', ''); //订单金额（元，两位小数）
$currentTime = $_POST['currentTime']; //当前时间
$merchant = $_POST['merchant']; //商户号
$notifyUrl = $_POST['notifyUrl']; //异步回调地址
$orderNo = $_POST['orderNo']; //订单号
$payType = $_POST['payType']; //支付类型
$remark = $_POST['remark']; //备注信息
$returnUrl = $_POST['returnUrl']; //同步回调地址
$bank = $_POST['bank'];

//步骤1、必填参数按照ascii码表顺序拼接
$sign = "amount=" . $amount . "&bank=" . $bank . "&currentTime=" . $currentTime . "&merchant=" . $merchant . "&notifyUrl=" . $notifyUrl . "&orderNo=" . $orderNo . "&payType=" . $payType;

//步骤2、选填参数判断拼接
if ($remark != "") {
    $sign = $sign . "&remark=" . $remark;
}
if ($returnUrl != "") {
    $sign = $sign . "&returnUrl=" . $returnUrl;
}
//步骤3、把字段字符串通过“#"号与商户密钥拼接，得到最终的加密字符串
$sign = $sign . "#" . $key;

//步骤4、把最终的加密字符串进行md5加密
$sign = md5($sign);
?>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
        <meta name="format-detection" content="telephone=no" />
        <title>支付接口</title>
        <style type="text/css">
            body{padding:0;margin:0;color:#333;font-size:14px;}
        </style>
    </head>
    <body>
        <form id="form1" name="form1" method="post" action="<?php echo $payUrl; ?>">
            <input type="hidden" id="amount"		name="amount"		value="<?php echo $amount; ?>"/>
            <input type="hidden" id="bank"              name="bank"	value="<?php echo $bank; ?>"/>
            <input type="hidden" id="currentTime"	name="currentTime"	value="<?php echo $currentTime; ?>"/>
            <input type="hidden" id="merchant"		name="merchant"		value="<?php echo $merchant; ?>"/>
            <input type="hidden" id="notifyUrl"		name="notifyUrl"	value="<?php echo $notifyUrl; ?>"/>
            <input type="hidden" id="orderNo"		name="orderNo"		value="<?php echo $orderNo; ?>"/>
            <input type="hidden" id="payType"		name="payType"		value="<?php echo $payType; ?>"/>
            <input type="hidden" id="remark"		name="remark"		value="<?php echo $remark; ?>"/>
            <input type="hidden" id="returnUrl"		name="returnUrl" 	value="<?php echo $returnUrl; ?>"/>
            <input type="hidden" id="sign"			name="sign" 		value="<?php echo $sign; ?>"/>
            <?php if ($sdk != "" && $sdk != null) { ?>
                <input type="hidden" id="sdk"			name="sdk" 		value="<?php echo $sdk; ?>"/>
            <?php } ?>
            <input type="hidden" id="version" name="version" value="1">
        </form>
        <div style="width:100%;min-width:320px;">
            <div style="background-color:#18b4ed;color:#fff; height:45px; line-height:45px; text-align:center;border-radius:3px; width:90%;margin:20px auto;cursor:pointer;" name="pay_submit">提交中 ... ( 超过5秒未自动跳转，请点击！ )</div>
        </div>
        <script type="text/javascript" src="./statics/js/zepto.min.js"></script>
        <script type="text/javascript">
            $(function () {
                $("form[name='form1']").submit();
            })
            $("div[name='pay_submit']").on("click", function () {
                $("form[name='form1']").submit();
            });
        </script>
    </body>
</html>
