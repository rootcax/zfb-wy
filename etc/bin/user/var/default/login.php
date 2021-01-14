<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>登录 - <?php echo functions::get_Config('webCog')['title'];?></title>
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
						<!-- 这里写top -->
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					

					<!-- Start Sign In Form -->
					<form action="#" class="fh5co-form animate-box" data-animate-effect="fadeInLeft" id="from">
						<h2>日月支付</h2>
						<div class="form-group">
							<label for="phone" class="sr-only">手机号</label>
							<?php $csrf = functions::getcsrf();?>
							<input type="hidden" id="csrf" name="csrf" value="<?php echo $csrf;?>">
							<input type="text" class="form-control" name="phone" placeholder="手机号" autocomplete="off">
						</div>
						<div class="form-group">
							<label for="password" class="sr-only">密码</label>
							<input type="password" class="form-control" name="pwd" placeholder="密码" autocomplete="off">
						</div>
						<!--<div class="form-group">
							<div class="row">
							<div class="col-md-7">
							<label for="text" class="sr-only">验证码</label>
							<input type="text" class="form-control" name="code" placeholder="验证码" autocomplete="off">
							
							</div>
						
							<div class="col-md-5">
								<img style="position: relative;top: 18px;left: -5px;width:100%;"  onclick="this.src='<?php echo functions::urlc('user', 'api', 'imagec', array('time'=>time(),'csrf'=>$csrf,'typec'=>'login'));?>'" src="<?php echo functions::urlc('user', 'api', 'imagec', array('time'=>time(),'csrf'=>$csrf,'typec'=>'login'));?>"/>
							</div>
							</div>
						</div> -->
						<div class="form-group">
							<input type="button" value="登录" class="btn btn-primary" style="width: 100%;" onclick="login();">
						</div>
						<div class="form-group">
							<p style="text-align: right;font-size: 12px;"><a href="<?php echo functions::urlc('user', 'index', 'register');?>">免费注册</a><!-- | <a href="<?php echo functions::urlc('user', 'index', 'forget');?>">忘记密码?</a>--></p>
						</div>
					</form>
					<!-- END Sign In Form -->

				</div>
			</div>
			<div class="row" style="padding-top: 60px; clear: both;">
				<!-- <div class="col-md-12 text-center"><p><small>&copy; NS辅助开放平台</small></p></div> -->
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
function login(){
	layer.load();
	var csrf = $('#csrf').val();
	$.ajax({
        type: "POST",
        dataType: "json",
        url: "<?php echo functions::get_Config('webCog')['site'];?>?a=user&b=api&c=login&csrf=" + csrf,
        data: $('#from').serialize(),
        success: function (data) {
            if(data.code == '200'){
            	layer.closeAll('loading');
            	layer.msg(data.msg, {icon: 1});
            	setTimeout(function(){location.href = '<?php echo functions::urlc('user','index','home');?>';},2000);
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

