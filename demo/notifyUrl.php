<?php
	include 'config.php';
	$accFlag = $_POST['accFlag'];//账号所属（1平台、2商户）
	$accName = $_POST['accName'];//收款账号（微信账号、支付宝账号等）
	$amount=number_format(floatval($_POST['amount']),2, '.', '');//订单金额（元，两位小数）
	$createTime = $_POST['createTime'];//创建时间 ( 格式为：yyyyMMddHHmmss )
	$currentTime=$_POST['currentTime'];//当前时间 ( 格式为：yyyyMMddHHmmss )
	$merchant=$_POST['merchant'];//商户号
	$orderNo=$_POST['orderNo'];//订单号
	$payFlag = $_POST['payFlag'];//支付状态 ( 1未支付，2已支付，3已关闭 ) 
	$payTime = $_POST['payTime'];//支付时间 ( 格式为：yyyyMMddHHmmss )
	$payType=$_POST['payType'];//支付类型
	$remark=$_POST['remark'];//备注信息
	$systemNo=$_POST['systemNo'];//同步回调地址
	$sign=$_POST['sign'];//md5密钥（KEY）
	
	//步骤1、必填参数按照ascii码表顺序拼接
	$mySign = "accFlag=".$accFlag."&accName=".$accName."&amount=".$amount."&createTime=".$createTime."&currentTime=".$currentTime."&merchant=".$merchant."&orderNo=".$orderNo."&payFlag=".$payFlag."&payTime=".$payTime."&payType=".$payType;
	
	//步骤2、选填参数判断拼接
	if($remark != ""){
		$mySign = $mySign."&remark=".$remark;
	}
	
	$mySign = $mySign."&systemNo=".$systemNo;
	
	//步骤3、把字段字符串通过“#"号与商户密钥拼接，得到最终的加密字符串
	$mySign = $mySign."#".$key;
	//echo $mySign;
	
	//步骤4、把最终的加密字符串进行md5加密
	$mySign = md5($mySign);

	if($sign == $mySign){
		if($payFlag==2){
			//此处填写加款（加分）逻辑
			echo "success";//必须把成功状态告知系统，success为固定值，不能用其他单词替换。
		}else{
			echo "no payment";
		}
	}else{
		echo "sign error";
	}
?>