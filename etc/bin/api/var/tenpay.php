<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="Content-Language" content="zh-cn">
<meta name="renderer" content="webkit">
<title>在线支付 - QQ钱包安全支付</title>
 <script type="text/javascript" src="<?php echo _theme_var;?>css/qqmoeny/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo _theme_var;?>css/qqmoeny/qrcode.js"></script>
 <script type="text/javascript" src="<?php echo _pub;?>js/layer/layer.js"></script>
<link href="<?php echo _theme_var;?>css/qqmoeny/wechat_pay.css" rel="stylesheet" media="screen">
<style>
.switch-tip-icon-img {
    position: absolute;
    left: 70px;
    top: 70px;
    z-index: 11;
}
.shadow{  
   -webkit-box-shadow: #666 0px 0px 10px;  
   -moz-box-shadow: #666 0px 0px 10px;  
   box-shadow: #666 0px 0px 10px;  
    padding-top: 15px;
    padding-right: 5px;
    padding-bottom: 1px;
    padding-left: 5px;
   background: #FFFFFF; 
   width:240px;
  height:240px;
} 
.time-item strong {
    background:#006BFC;
    color:#fff;
    line-height:30px;
    font-size:20px;
    font-family:Arial;
    padding:0 10px;
    margin-right:10px;
    border-radius:5px;
    box-shadow:1px 1px 3px rgba(0,0,0,0.2);
}
h2 {
	line-height:50px;
    font-family:"微软雅黑";
    font-size:16px;
    letter-spacing:2px;
}
</style>
</head>
<body>
<div class="body">
<h1 class="mod-title">
<span class="text"><img style="width:181px;height:33px;" alt="QQ钱包支付" src="<?php echo _theme_var;?>css/qqmoeny/mqq_logo.png"></span>

</h1>
<div class="mod-ct">
<div class="order">
</div>
<div class="amount">￥<?php echo $money;?></div>
<br>

<div align="center">
<div class="shadow">
<font class="qr-image" id="qrcode">
<canvas width="230" height="230" style="display: none;"></canvas><img alt="Scan me!" style="display: block;" src="<?php echo $image;?>"></font></div>
<h2>距离该订单过期还有</h2>
<div class="time-item">
<strong id="hour_show"><s id="h"></s>0时</strong>
<strong id="minute_show"><s></s>04分</strong>
    <strong id="second_show"><s></s>30秒</strong>
</div>

</div>

<div class="detail" id="orderDetail">
<dl class="detail-ct" style="display: none;">
<dt>商家</dt>
<dd id="storeName"><?php echo $username;?></dd>
<dt>商品类型</dt>
<dd id="productName">自动充值</dd>
<dt>商户订单号</dt>
<dd id="billId"><?php echo $order_num;?> </dd>
<dt>创建时间</dt>
<dd id="createTime"><?php echo date("Y-m-d H:i:s",$order_time);?></dd>
</dl>
<a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
</div>
<div class="tip">
<span class="dec dec-left"></span>
<span class="dec dec-right"></span>
<div class="ico-scan"></div>
<div class="tip-text">
<p>请使用QQ扫一扫</p>
<p>扫描二维码完成支付</p>
</div>
</div>
<div class="tip-text">
</div>
</div>
<div class="foot">
<div class="inner">

<p>本站为第三方辅助软件服务商，与微信钱包官方和腾讯网无任何关系</p>
<p>在付款前请确认收款人账户信息，转账后将立即到达对方账户</p>
</div>
</div>
</div>
	  <?php if ($msgInfo){?>
	  <script type="text/javascript">
	  layer.alert('<?php echo $msgInfo;?>', {
		  icon: 1,
		  title: '支付提醒'
		});
	  </script>
	  <?php }?>
  <script type="text/javascript">
var intDiff = parseInt(270);//倒计时总秒数量
function timer(intDiff){
    window.setInterval(function(){
    var day=0,
        hour=0,
        minute=0,
        second=0;//时间默认值       
    if(intDiff > 0){
        day = Math.floor(intDiff / (60 * 60 * 24));
        hour = Math.floor(intDiff / (60 * 60)) - (day * 24);
        minute = Math.floor(intDiff / 60) - (day * 24 * 60) - (hour * 60);
        second = Math.floor(intDiff) - (day * 24 * 60 * 60) - (hour * 60 * 60) - (minute * 60);
    }
	if (minute == 00 && second == 00) document.getElementById('qrcode').innerHTML='<br/><br/><br/><br/><br/><h2>二维码超时 请重新发起交易</h2><br/><br/><br/>';
    if (minute <= 9) minute = '0' + minute;
    if (second <= 9) second = '0' + second;
    $('#day_show').html(day+"天");
    $('#hour_show').html('<s id="h"></s>'+hour+'时');
    $('#minute_show').html('<s></s>'+minute+'分');
    $('#second_show').html('<s></s>'+second+'秒');
    intDiff--;
    }, 1000);
} 
$(function(){
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
    function order(){
    	$.get("<?php echo functions::get_Config('webCog')['site'] . 'api.php?c=get&num=' . $order_num;?>", function(result){
    		//成功
    		if(result.code == '200'){
				//回调页面
        		window.clearInterval(orderlst);
    			layer.confirm(result.msg, {
    			  icon: 1,
    			  title: '支付成功',
  				  btn: ['我知道了'] //按钮
  				}, function(){
  					location.href="<?php echo functions::get_Config('webCog')['site'] . 'index.php?a=servlet&b=index&c=Refer&num='.$order_num ?>";
  				});
    		}
    		//订单被销毁
    		if(result.code == '1001'){
    			window.clearInterval(orderlst);
    			layer.confirm(result.msg, {
    			  icon: 2,
    			  title: '订单错误',
  				  btn: ['确认'] //按钮
  				}, function(){
  					location.href="<?php echo functions::get_Config('webCog')['site'] . 'index.php?a=servlet&b=index&c=Refer&num='.$order_num ?>";
  				});
        	}
        	//订单已经超时
    		if(result.code == '1002'){
    			window.clearInterval(orderlst);
    			layer.confirm(result.msg, {
    			  icon: 2,
    			  title: '支付超时',
  				  btn: ['确认'] //按钮
  				}, function(){
  					location.href="<?php echo functions::get_Config('webCog')['site'] . 'index.php?a=servlet&b=index&c=Refer&num='.$order_num ?>";
  				});
        	}
    	  });
     }
    //周期监听
    var orderlst = setInterval("order()",2000);
  
 
</script>


</body></html>