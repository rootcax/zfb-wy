<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>代理后台管理</title>
    <link rel="icon" href="favicon.ico"/>
	<link href="<?php echo _theme;?>user/plugins/fullPage/jquery.fullPage.css" rel="stylesheet"/>
	<link href="<?php echo _theme;?>user/plugins/bootstrap-3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
	<link href="<?php echo _theme;?>user/plugins/material-design-iconic-font-2.2.0/css/material-design-iconic-font.min.css" rel="stylesheet"/>
	<link href="<?php echo _theme;?>user/plugins/waves-0.7.5/waves.min.css" rel="stylesheet"/>
	<link href="<?php echo _theme;?>user/plugins/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css" rel="stylesheet"/>
	<link href="<?php echo _theme;?>user/css/admin.css" rel="stylesheet"/>
	<style>
	/** skins **/
	#zheng-upms-server #header {background: rgba(7, 10, 41, 0.72);}
	#zheng-upms-server .content_tab{background: #818396;}
	#zheng-upms-server .s-profile>a{background: url(<?php echo _theme;?>user/images/zheng-upms.png) left top no-repeat;}
	
	#zheng-cms-admin #header {background: #455EC5;}
	#zheng-cms-admin .content_tab{background: #455EC5;}
	#zheng-cms-admin .s-profile>a{background: url(<?php echo _theme;?>user/images/zheng-cms.png) left top no-repeat;}
	
	#zheng-pay-admin #header {background: #F06292;}
	#zheng-pay-admin .content_tab{background: #F06292;}
	#zheng-pay-admin .s-profile>a{background: url(<?php echo _theme;?>user/images/zheng-pay.png) left top no-repeat;}
	
	#zheng-ucenter-home #header {background: #6539B4;}
	#zheng-ucenter-home .content_tab{background: #6539B4;}
	#zheng-ucenter-home .s-profile>a{background: url(<?php echo _theme;?>user/images/zheng-ucenter.png) left top no-repeat;}
	
	#zheng-oss-web #header {background: #0B8DE5;}
	#zheng-oss-web .content_tab{background: #0B8DE5;}
	#zheng-oss-web .s-profile>a{background: url(<?php echo _theme;?>user/images/zheng-oss.png) left top no-repeat;}
	
	#test #header {background: test;}
	#test .content_tab{background: test;}
	#test .s-profile>a{background: url(test) left top no-repeat;}
	</style>
</head>
<body>
<header id="header">
	<ul id="menu">
		<li id="guide" class="line-trigger">
			<div class="line-wrap">
				<div class="line top"></div>
				<div class="line center"></div>
				<div class="line bottom"></div>
			</div>
		</li>
		<li id="logo" class="hidden-xs">
			<a href="<?php echo functions::get_Config('webCog')['site'];?>">
			  <img src="<?php echo _theme;?>user/images/logo.png" style="height:40px;">
			</a>
	
		</li>
		<li class="pull-right">
			<ul class="hi-menu">
				<li class="dropdown">
					<a class="waves-effect waves-light" data-toggle="dropdown" href="javascript:;">
						<i class="him-icon zmdi zmdi-more-vert"></i>
					</a>
					<ul class="dropdown-menu dm-icon pull-right">
						<li class="hidden-xs">
							<a class="waves-effect" data-ma-action="fullscreen" href="javascript:fullPage();"><i class="zmdi zmdi-fullscreen"></i> 全屏模式</a>
						</li>
                                                <li>
							<a class="waves-effect" href="<?php echo functions::urlc('agent', 'action', 'out')?>"><i class="zmdi zmdi-run"></i> 退出登录</a>
						</li>
					</ul>
				</li>
			</ul>
		</li>
	</ul>
</header>
<section id="main">
	<!-- 左侧导航区 -->
	<aside id="sidebar">
		<!-- 个人资料区 -->
		<div class="s-profile">
			<a class="waves-effect waves-light" href="javascript:;">
				<div class="sp-pic">
					<img src="<?php  if (!empty($user->avatar)){ echo _pub . 'upload/' . $user->sid . '/images/' . $user->avatar; }else{ echo _theme . 'images/ns.png'; }?>"/>
				</div>
				<div class="sp-info">
					<?php echo $user->phone;?>，余额：<b><?php echo $user->balance;?></b>
					<i class="zmdi zmdi-caret-down"></i>
				</div>
			</a>
			<ul class="main-menu" style="display: block;">
                            <li>
					<a class="waves-effect" href="javascript:Tab.addTab('修改资料', '<?php echo functions::urlc('agent', 'index', 'my')?>');"><i class="zmdi zmdi-account"></i> 修改资料</a>
				</li>
				<li>
					<a class="waves-effect" href="<?php echo functions::urlc('agent', 'action', 'out')?>"><i class="zmdi zmdi-run"></i> 退出登录</a>
				</li>
			</ul>
		</div>
		<!-- /个人资料区 -->
		<!-- 菜单区 -->
		<ul class="main-menu">

			<li class="sub-menu system_menus system_1 0 toggled">
			<a class="waves-effect" href="javascript:;"><i class="zmdi zmdi-accounts"></i> 商户管理</a>
			<ul style="display: block;">
				<li><a class="waves-effect" href="javascript:Tab.addTab('添加商户', '<?php echo functions::urlc('agent', 'index', 'user_add')?>');">添加商户</a></li>
				<li><a class="waves-effect" href="javascript:Tab.addTab('商户管理', '<?php echo functions::urlc('agent', 'index', 'user')?>');">商户管理</a></li>
			</ul>
			</li>
			
			<li class="sub-menu system_menus system_1 3 toggled">
			<a class="waves-effect" href="javascript:;"><i class="zmdi zmdi-paypal-alt"></i> 订单管理</a>
			<ul style="display: block;">
				<li><a class="waves-effect" href="javascript:Tab.addTab('支付订单', '<?php echo functions::urlc('agent', 'index', 'takes')?>');">支付订单</a></li>
				<li><a class="waves-effect" href="javascript:Tab.addTab('收款订单', '<?php echo functions::urlc('agent', 'index', 'order')?>');">收款订单</a></li>
			</ul>
			</li>
                        
                        <li class="sub-menu system_menus system_1 0 toggled">
			<a class="waves-effect" href="javascript:;"><i class="zmdi zmdi-accounts-list"></i> 提现管理</a>
			<ul style="display: block;">
				<li><a class="waves-effect" href="javascript:Tab.addTab('申请提现', '<?php echo functions::urlc('agent', 'index', 'applyWithdraw')?>');">申请提现</a></li>
				<li><a class="waves-effect" href="javascript:Tab.addTab('提现记录', '<?php echo functions::urlc('agent', 'index', 'withdraw')?>');">提现记录</a></li>
			</ul>
			</li>
		</ul>
		<!-- /菜单区 -->
	</aside>
	<!-- /左侧导航区 -->
	<section id="content">
		<div class="content_tab">
			<div class="tab_left">
				<a class="waves-effect waves-light" href="javascript:;"><i class="zmdi zmdi-chevron-left"></i></a>
			</div>
			<div class="tab_right">
				<a class="waves-effect waves-light" href="javascript:;"><i class="zmdi zmdi-chevron-right"></i></a>
			</div>
			<ul id="tabs" class="tabs">
				<li id="tab_home" data-index="home" data-closeable="false" class="cur">
					<a class="waves-effect waves-light">系统公告</a>
				</li>
			</ul>
		</div>
		
		<div class="content_main">
			<div id="iframe_home" class="iframe cur">
					<iframe src="?a=agent&b=index&c=news" width="100%" frameborder="0" scrolling="auto"  height="100%"></iframe>
			</div>
		</div>
	</section>
</section>
<script type="text/javascript">
</script>
<footer id="footer"></footer>
<script src="<?php echo _theme;?>user/plugins/jquery.1.12.4.min.js"></script>
<script src="<?php echo _theme;?>user/plugins/bootstrap-3.3.0/js/bootstrap.min.js"></script>
<script src="<?php echo _theme;?>user/plugins/waves-0.7.5/waves.min.js"></script>
<script src="<?php echo _theme;?>user/plugins/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo _theme;?>user/plugins/BootstrapMenu.min.js"></script>
<script src="<?php echo _theme;?>user/plugins/device.min.js"></script>
<script src="<?php echo _theme;?>user/plugins/fullPage/jquery.fullPage.min.js"></script>
<script src="<?php echo _theme;?>user/plugins/fullPage/jquery.jdirk.min.js"></script>
<script src="<?php echo _theme;?>user/plugins/jquery.cookie.js"></script>
<script src="<?php echo _theme;?>user/js/admin.js"></script>
<script src="<?php echo _theme;?>user/js/layer/3.0/layer.js"></script>
</body>
</html>