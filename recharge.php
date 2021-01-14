<?php
function sign($money, $record, $sdk) {
    $sign = md5(Number_format($money, 2, '.','') . trim($record) . $sdk);
    return $sign;
}
$money = $_REQUEST['money'];//金额
$sdk = $_REQUEST['sdk'];//支付方式以及对应的key
$record = $_REQUEST['record'];//订单号
//$refer = $_REQUEST['refer'];//同步通知地址
$refer = "http://www.yuebao321.com/demo1/hrefback.php";
$notify_url = "http://www.yuebao321.com/demo1/notify.php"; //异步通知
$sign = sign($money, $record, $sdk);


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
</head>
<script language='javascript'>
function autoup(){
	document.frmSubmit.submit();
}
</script>
<body onload="autoup()">
<form id='frmSubmit' method='post' name='frmSubmit' action='http://www.yuebao321.com/pay/'>
<input type='hidden' name='sdk' value='<?php echo $sdk ?>' />
<input type='hidden' name='record' value='<?php echo $record  ?>' />
<input type='hidden' name='money' value='<?php echo $money ?>' />
<input type='hidden' name='refer' value='<?php echo $refer  ?>' />
<input type='hidden' name='notify_url' value='<?php echo  $notify_url ?>' />
<input type='hidden' name='attach' value='' />
<input type='hidden' name='sign' value='<?php echo $sign  ?>' />
</form>

</body>
</html>