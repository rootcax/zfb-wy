
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>忘记密码 - <?php echo functions::get_Config('webCog')['title'];?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="<?php echo functions::get_Config('webCog')['description'];?>" />
	<meta name="keywords" content="<?php echo functions::get_Config('webCog')['keywords'];?>" />

	<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
	<link rel="shortcut icon" href="<?php echo _theme;?>favicon.ico">

	<link rel="stylesheet" href="<?php echo _theme;?>css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo _theme;?>css/animate.css">
	<link rel="stylesheet" href="<?php echo _theme;?>css/style.css">

	<!-- Modernizr JS -->
	<script src="<?php echo _theme;?>js/modernizr-2.6.2.min.js"></script>
	<!-- FOR IE9 below -->
	<!--[if lt IE 9]>
	<script src="<?php echo _theme;?>js/respond.min.js"></script>
	<![endif]-->

	</head>
	<body class="style-2">

		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<ul class="menu">
						<!--标题 -->
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					

					<!-- Start Sign In Form -->
					<form action="#" class="fh5co-form animate-box" data-animate-effect="fadeInLeft" id="from">
						<h2>找回密码</h2>
						<!-- <div class="form-group">
							<div class="alert alert-success" role="alert">如果您的密码忘记,可以在这里找回</div>
						</div> -->
						<div class="form-group">
							<label for="name" class="sr-only">手机号</label>
							<input type="hidden" id="csrf" name="csrf" value="<?php echo functions::getcsrf();?>">
							<input type="text" class="form-control" id="phone" name="phone" placeholder="手机号" autocomplete="off">
						</div>
						<div class="form-group">
							<div class="row">
							<div class="col-md-8">
							<label for="text" class="sr-only">验证码</label>
							<input type="text" class="form-control" name="code" placeholder="验证码" autocomplete="off">
							
							</div>
						
							<div class="col-md-3">
								<button type="button" id="sms" class="btn btn-default" style="position: relative;top: 15px;left: -15px;" onclick="sendemail();">发送验证码</button>
							</div>
							</div>
						</div>
						<div class="form-group">
							<label for="password" class="sr-only">设置新密码</label>
							<input type="password" class="form-control" name="pwd" placeholder="设置新密码" autocomplete="off">
						</div>
						<div class="form-group">
							<label for="re-password" class="sr-only">再次输入密码</label>
							<input type="password" class="form-control" name="repwd" placeholder="再次输入密码" autocomplete="off">
						</div>
						
						<div class="form-group">
							<p>已经有账号了? <a href="<?php echo functions::urlc('user','index','login');?>">立即登录</a></p>
						</div>
						<div class="form-group">
							<input type="button" value="立即修改" class="btn btn-primary" style="width: 100%;" onclick="forgetx();">
						</div>
					</form>
					<!-- END Sign In Form -->


				</div>
			</div>
			<div class="row" style="padding-top: 60px; clear: both;">
				<div class="col-md-12 text-center"><!-- 这里是版权 --></div>
			</div>
		</div>
	
	<!-- jQuery -->
	<script src="<?php echo _theme;?>js/jquery.min.js"></script>
	<script src="<?php echo _pub;?>js/layer/layer.js"></script>
	<!-- Bootstrap -->
	<script src="<?php echo _theme;?>js/bootstrap.min.js"></script>
	<!-- Placeholder -->
	<script src="<?php echo _theme;?>js/jquery.placeholder.min.js"></script>
	<!-- Waypoints -->
	<script src="<?php echo _theme;?>js/jquery.waypoints.min.js"></script>
	<!-- Main JS -->
	<script src="<?php echo _theme;?>js/main.js"></script>
<script type="text/javascript">
		var countdown=90; 
function sendemail(){
	var phone = $("#phone").val();
	var obj = $("#sms");
	var csrf = $('#csrf').val();
	layer.load();
	 $.get("<?php echo functions::get_Config('webCog')['site'];?>?a=user&b=api&c=sms&typec=2&phone=" + phone + "&csrf=" + csrf, function(result){
		    if(result.code == '200'){
		    	layer.closeAll('loading');
            	layer.msg(result.msg, {icon: 1});
                settime(obj);
			}else{
				layer.closeAll('loading');
            	layer.msg(result.msg, {icon: 2});
			}
		});
    }
function settime(obj) { //发送验证码倒计时
    if (countdown == 0) { 
        obj.attr('disabled',false); 
        //obj.removeattr("disabled"); 
        obj.text("发送验证码");
        countdown = 60;
        return;
    } else { 
        obj.attr('disabled',true);
        obj.text("重新发送(" + countdown + ")");
        countdown--; 
        console.log(countdown);
    } 
setTimeout(function() { 
    settime(obj) }
    ,1000) 
}

function forgetx(){
	layer.load();
	var csrf = $('#csrf').val();
	$.ajax({
        type: "POST",
        dataType: "json",
        url: "<?php echo functions::get_Config('webCog')['site'];?>?a=user&b=api&c=forget&csrf=" + csrf,
        data: $('#from').serialize(),
        success: function (data) {
            if(data.code == '200'){
            	layer.closeAll('loading');
            	layer.msg(data.msg, {icon: 1});
            	setTimeout(function(){location.href = '<?php echo functions::urlc('user','index','login');?>';},2000);
            }else{
            	layer.closeAll('loading');
            	layer.msg(data.msg, {icon: 2});
            }
        },
        error: function(data) {
            alert("error:"+data.responseText);
         }
});
}
	</script>
	</body>
</html>

