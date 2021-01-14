<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>同步回调</title>
	<style type="text/css">
	body{padding:0;margin:0;color:#333;font-size:14px;font-family:微软雅黑;}
	ul,li{padding:0;margin:0;list-style:none;}
	.top{width:100%; height:40px;position: fixed; top:0;left:0;z-index:100;}
	.top-head{background-color:#18b4ed; text-align:center;color:#fff; height:45px; line-height:45px;}
	.top-nav{ height:45px; line-height:45px; background-color:#f0f0f0;}
	.top-nav li{width:50%; float:left;color:#777;border-bottom:2px solid #777; text-align:center;}
	.top-nav li.thisclass{border-bottom:2px solid #e70000;color: #e70000;background-color:#fff;}
	.content{width:98%;padding:1%;position:absolute; top:140px;left:0;z-index:99;}
	.ui-form-item{ height:45px; line-height:40px; width:100%;padding:5px 0;}
	.ui-form-item label{min-width:100px;width:32%; display:block; float:left; line-height:20px;}
	input,select{ height:40px;line-height:40px; float:left; padding:0 1%;color:#18b4ed;width:65%;font-size:13px;}
	.ui-btn-lg{ background-color:#f75549;color:#fff; height:45px; line-height:45px; text-align:center;border-radius:3px; margin-top:10px;cursor:pointer;}
	.top-nav li{cursor:pointer;}
	</style>
</head>

<body>
	<div style="width:100%; min-width:320px;">
		<div class="top">
			<div class="top-head">同步回调</div>
		</div>
		<?php
			include 'config.php';
			//=====================同步返回网址、用于展示充值结果（只展示不操作）=======================
			$accFlag = $_REQUEST['accFlag'];//账号所属（1平台、2商户）
			$accName = $_REQUEST['accName'];//收款账号（微信账号、支付宝账号等）
			$amount=number_format(floatval($_REQUEST['amount']),2, '.', '');//订单金额（元，两位小数）
			$createTime = $_REQUEST['createTime'];//创建时间 ( 格式为：yyyyMMddHHmmss )
			$currentTime=$_REQUEST['currentTime'];//当前时间 ( 格式为：yyyyMMddHHmmss )
			$merchant=$_REQUEST['merchant'];//商户号
			$orderNo=$_REQUEST['orderNo'];//订单号
			$payFlag = $_REQUEST['payFlag'];//支付状态 ( 1未支付，2已支付，3已关闭 ) 
			$payTime = $_REQUEST['payTime'];//支付时间 ( 格式为：yyyyMMddHHmmss )
			$payType=$_REQUEST['payType'];//支付类型
			$remark=$_REQUEST['remark'];//备注信息
			$systemNo=$_REQUEST['systemNo'];//同步回调地址
			$sign=$_REQUEST['sign'];//md5密钥（KEY）
			
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
					echo "<h1 style='margin-top:80px;color:green;text-align:center; height:50px;'>充值成功</h1>";
					?>
					<div class="content">
						<div class="content-check">
								<div class="ui-form-item">
									<label>
										<div>商户号</div>
										<div style="font-size:12px;"> ( merchant )</div>
									</label>
									<input type="text" value="<?php echo $merchant; ?>"/>
								</div>
								<div class="ui-form-item">
									<label>
										<div>商户订单号</div>
										<div style="font-size:12px;"> ( orderNo )</div>
									</label>
									<input type="text" value="<?php echo $orderNo; ?>"/>
								</div>
								<div class="ui-form-item">
									<label>
										<div>系统订单号</div>
										<div style="font-size:12px;"> ( systemNo )</div>
									</label>
									<input type="text" value="<?php echo $systemNo; ?>"/>
								</div>
								<div class="ui-form-item">
									<label>
										<div>创建时间</div>
										<div style="font-size:12px;"> ( createTime )</div>
									</label>
									<input type="text" value="<?php echo $createTime; ?>"/>
								</div>
								<div class="ui-form-item">
									<label>
										<div>充值金额</div>
										<div style="font-size:12px;"> ( amount )</div>
									</label>
									<input type="text" value="<?php echo $amount; ?>"/>
								</div>
								<div class="ui-form-item">
									<label>
										<div>订单状态</div>
										<div style="font-size:12px;"> ( payFlag )</div>
									</label>
									<input type="text" value="<?php echo $payFlag ?>"/>
								</div>
						</div>
					</div>
					<?php
				}else{
					echo "<h1 style='margin-top:80px;color:blue;text-align:center; height:50px;'>订单未支付</h1>";
				}
			}else{
				echo "<h1 style='margin-top:80px;color:red;text-align:center; height:50px;'>订单签名错误</h1><div style='width:400px;margin:50px auto;text-align:center;'>请按要求重新充值</div>";
			}
		?>
		
	</div>
</body>
</html>
