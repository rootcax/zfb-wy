<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<head>
	<meta charset="utf-8" />
	<title>日月支付</title>
	<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
  	<meta name="keywords" content="日月支付,支付宝免签约即时到账,财付通免签约,微信免签约支付,QQ钱包免签约,免签约支付">
	<meta name="description" content="日月支付是一个免签约支付产品，可以助你一站式解决网站签约各种支付接口的难题，现拥有支付宝、财付通、QQ钱包、微信支付等免签约支付功能，并有开发文档与SDK，可快速集成到你的网站。" />
		
	<!-- ================== BEGIN BASE CSS STYLE ================== -->
  	<link rel="shortcut icon" href="favicon.ico">
	<link href="<?php echo _theme;?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="<?php echo _theme;?>assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="<?php echo _theme;?>assets/css/animate.min.css" rel="stylesheet" />
	<link href="<?php echo _theme;?>assets/css/style.min.css" rel="stylesheet" />
	<link href="<?php echo _theme;?>assets/css/style-responsive.min.css" rel="stylesheet" />
	<link href="<?php echo _theme;?>assets/css/theme/blue.css" id="theme" rel="stylesheet" />
    <link href="<?php echo _theme;?>assets/css/common.css" rel="stylesheet" />
	<!-- ================== END BASE CSS STYLE ================== -->
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="<?php echo _theme;?>assets/plugins/pace/pace.min.js"></script>
	<!-- ================== END BASE JS ================== -->

</head>
<body data-spy="scroll" data-target="#header-navbar" data-offset="51">
  <!--代码部分begin-->
<div id="floatTools" class="rides-cs" style="height:246px;">
  <div class="floatL">
  	<a id="aFloatTools_Show" class="btnOpen" title="查看在线客服" style="top:20px;display:block" href="javascript:void(0);">展开</a>
  	<a id="aFloatTools_Hide" class="btnCtn" title="关闭在线客服" style="top:20px;display:none" href="javascript:void(0);">收缩</a>
  </div>
  <div id="divFloatToolsView" class="floatR" style="display: none;height:237px;width: 140px;">
    <div class="cn">
      <h3 class="titZx">24H在线客服</h3>
      <ul>
        <li><span>开户</span> <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=3333333333&site=qq&menu=yes"><img border="0" src="<?php echo _theme;?>assets/img/qq1.png" alt="点击这里给我发消息" title="点击这里给我发消息"/></a> </li>
        <li><span>加盟</span> <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=4444444444&site=qq&menu=yes"><img border="0" src="<?php echo _theme;?>assets/img/qq1.png" alt="点击这里给我发消息" title="点击这里给我发消息"/></a> </li>
        <li><span>技术</span> <a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=5555555555&site=qq&menu=yes"><img border="0" src="<?php echo _theme;?>assets/img/qq1.png" alt="点击这里给我发消息" title="点击这里给我发消息"/></a> </li>
        <li>
            <a> 日月支付</a>
            
            <div class="div_clear"></div>
        </li>
        <li style="border:none;"><span>免费开户，资金安全</span> </li>
      </ul>
    </div>
  </div>
</div>
<script src="<?php echo _theme;?>assets/js/jquery1.min.js"></script>
<script>
	$(function(){
		$("#aFloatTools_Show").click(function(){
			$('#divFloatToolsView').animate({width:'show',opacity:'show'},100,function(){$('#divFloatToolsView').show();});
			$('#aFloatTools_Show').hide();
			$('#aFloatTools_Hide').show();				
		});
		$("#aFloatTools_Hide").click(function(){
			$('#divFloatToolsView').animate({width:'hide', opacity:'hide'},100,function(){$('#divFloatToolsView').hide();});
			$('#aFloatTools_Show').show();
			$('#aFloatTools_Hide').hide();	
		});
	});
  </script>
<!--代码部分end-->
    <!-- begin #page-container -->
    <div id="page-container" class="fade">
        <!-- begin #header -->
        <div id="header" class="header navbar navbar-transparent navbar-fixed-top">
            <!-- begin container -->
            <div class="container">
                <!-- begin navbar-header -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-navbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a href="index.html" class="navbar-brand">
                        <span class="brand-logo"></span>
                        <span class="brand-text">
                            <span class="text-theme"></span> 日月支付
                        </span>
                    </a>
                </div>
                <!-- end navbar-header -->
                <!-- begin navbar-collapse -->
                <div class="collapse navbar-collapse" id="header-navbar">
                    <ul class="nav navbar-nav navbar-right">
                        <li class="active"><a href="#home" data-click="scroll-to-target">首页</a> </li>
                      	<li><a href="#service" data-click="scroll-to-target">优势</a></li>
                        <li><a href="/demo">在线测试</a></li>
                        <li><a href="/demo2">开发文档</a></li>
                      	<li><a href="agent.php">代理登录</a></li>
                        <li><a href="?a=user&b=index&c=login">商户登录</a></li>
                    </ul>
                </div>
                <!-- end navbar-collapse -->
            </div>
            <!-- end container -->
        </div>
        <!-- end #header -->
        
        <!-- begin #home -->
        <div id="home" class="content has-bg home">
            <!-- begin content-bg -->
            <div class="content-bg">
                <img src="<?php echo _theme;?>assets/img/home-bg.jpg" alt="Home" />
            </div>
            <!-- end content-bg -->
            <!-- begin container -->
            <div class="container home-content">
                <h1>欢迎来到 <a href="">日月支付</a></h1>
                <h4>
                    支付方式：支付宝扫码、支付宝H5、云闪付扫码、微信扫码，可根据开发文档快速接入自己网站！<br />
                    <a href="">稳定、安全、值得信赖</a>
                </h4>
                <a href="?a=user&b=index&c=register" class="btn btn-theme">免费开户</a> <a href="?a=user&b=index&c=login" class="btn btn-outline">商户登录</a><br />
            </div>
            <!-- end container -->
        </div>
        <!-- end #home -->
    	
      	<!-- beign #service -->
        <div id="service" class="content" data-scrollview="true">
            <!-- begin container -->
            <div class="container">
                <h2 class="content-title">为什么选择我们？</h2>
                <p class="content-desc">
                    日月支付免去个人无法签约支付接口以及企业申请签约支付接口麻烦的问题，免签约也能享受及时到账的乐趣，系统优势如下：
                </p>
                <!-- begin row -->
                <div class="row">
                    <!-- begin col-3 -->
                    <div class="col-md-4 col-sm-4">
                        <div class="service">
                            <div class="icon bg-theme" data-animation="true" data-animation-type="bounceIn"><i class="fa fa-cog"></i></div>
                            <div class="info">
                                <h4 class="title">方便接入</h4>
                                <p class="desc">根据我们提供的开发文档，可快速接入你的网站，让你的网站支持在线支付功能，享受免签约支付的乐趣。</p>
                            </div>
                        </div>
                    </div>
                    <!-- end col-3 -->
                    <!-- begin col-3 -->
                    <div class="col-md-4 col-sm-4">
                        <div class="service">
                            <div class="icon bg-theme" data-animation="true" data-animation-type="bounceIn"><i class="fa fa-paint-brush"></i></div>
                            <div class="info">
                                <h4 class="title">免手续费</h4>
                                <p class="desc">提现免手续费，D0自动结算。</p>
                            </div>
                        </div>
                    </div>
                    <!-- end col-3 -->
                    <!-- begin col-3 -->
                    <div class="col-md-4 col-sm-4">
                        <div class="service">
                            <div class="icon bg-theme" data-animation="true" data-animation-type="bounceIn"><i class="fa fa-file"></i></div>
                            <div class="info">
                                <h4 class="title">智能提醒</h4>
                                <p class="desc">日月支付提供商户APP、QQ机器人、邮箱等多种提醒方式可选，让您随时获知自己的收入动态。</p>
                            </div>
                        </div>
                    </div>
                    <!-- end col-3 -->
                </div>
                <!-- end row -->
                <!-- begin row -->
                <div class="row">
                    <!-- begin col-3 -->
                    <div class="col-md-4 col-sm-4">
                        <div class="service">
                            <div class="icon bg-theme" data-animation="true" data-animation-type="bounceIn"><i class="fa fa-code"></i></div>
                            <div class="info">
                                <h4 class="title">安全放心</h4>
                                <p class="desc">我们用的支付接口全为自己申请，不存在二次对接的情况，彻底避免对接方跑路导致无法结算的情况！</p>
                            </div>
                        </div>
                    </div>
                    <!-- end col-3 -->
                    <!-- begin col-3 -->
                    <div class="col-md-4 col-sm-4">
                        <div class="service">
                            <div class="icon bg-theme" data-animation="true" data-animation-type="bounceIn"><i class="fa fa-shopping-cart"></i></div>
                            <div class="info">
                                <h4 class="title">自动结算</h4>
                                <p class="desc">采取D0结算方式，交易金额自选，系统收取0.6%手续费。</p>
                            </div>
                        </div>
                    </div>
                    <!-- end col-3 -->
                    <!-- begin col-3 -->
                    <div class="col-md-4 col-sm-4">
                        <div class="service">
                            <div class="icon bg-theme" data-animation="true" data-animation-type="bounceIn"><i class="fa fa-heart"></i></div>
                            <div class="info">
                                <h4 class="title">插件拓展</h4>
                                <p class="desc">提供SDK测试包，方便快速开发和接入，后续会逐渐提供discuz、WordPress等平台的支付相关插件。</p>
                            </div>
                        </div>
                    </div>
                    <!-- end col-3 -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end #service -->
      
        <!-- begin #milestone -->
        <div id="milestone" class="content bg-black-darker has-bg" data-scrollview="true">
            <!-- begin content-bg -->
            <div class="content-bg">
                <img src="<?php echo _theme;?>assets/img/milestone-bg.jpg" alt="Milestone" />
            </div>
            <!-- end content-bg -->
            <!-- begin container -->
            <div class="container">
                <!-- begin row -->
                <div class="row">
                    <!-- begin col-3 -->
                    <div class="col-md-4 col-sm-4 milestone-col">
                        <div class="milestone">
                            <div class="number" data-animation="true" data-animation-type="number" data-final-number="3091">3091</div>
                            <div class="title">接入商户</div>
                        </div>
                    </div>
                    <!-- end col-3 -->
                    <!-- begin col-3 -->
                    <div class="col-md-4 col-sm-4 milestone-col">
                        <div class="milestone">
                            <div class="number" data-animation="true" data-animation-type="number" data-final-number="19039">19039</div>
                            <div class="title">接入网站</div>
                        </div>
                    </div>
                    <!-- end col-3 -->
                    <!-- begin col-3 -->
                    <div class="col-md-4 col-sm-4 milestone-col">
                        <div class="milestone">
                            <div class="number" data-animation="true" data-animation-type="number" data-final-number="372">372</div>
                            <div class="title">合作伙伴</div>
                        </div>
                    </div>
                    <!-- end col-3 -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end #milestone -->
        
        <!-- begin #footer -->
        <div id="footer" class="footer">
            <div class="container">
                <div class="footer-brand">
                    <div class="footer-brand-logo"></div>
                    日月支付
                </div>
              	<p>
                    开户咨询:3333333333 代理加盟:4444444444 技术专员:55555555 <br />
                </p>
                <p>
                    Copyright &copy; 2018  日月支付</a> Reserved. <br />
                </p>
            </div>
        </div>
        <!-- end #footer -->
      
        <!-- begin theme-panel -->
        <div class="theme-panel">
            
            
        </div>
        <!-- end theme-panel -->
    </div>
    <!-- end #page-container -->
	
	<!-- ================== BEGIN BASE JS ================== -->
	<script src="<?php echo _theme;?>assets/plugins/jquery/jquery-1.9.1.min.js"></script>
	<script src="<?php echo _theme;?>assets/plugins/jquery/jquery-migrate-1.1.0.min.js"></script>
	<script src="<?php echo _theme;?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
		<script src="<?php echo _theme;?>assets/crossbrowserjs/html5shiv.js"></script>
		<script src="<?php echo _theme;?>assets/crossbrowserjs/respond.min.js"></script>
		<script src="<?php echo _theme;?>assets/crossbrowserjs/excanvas.min.js"></script>
	<![endif]-->
	<script src="<?php echo _theme;?>assets/plugins/jquery-cookie/jquery.cookie.js"></script>
	<script src="<?php echo _theme;?>assets/plugins/scrollMonitor/scrollMonitor.js"></script>
	<script src="<?php echo _theme;?>assets/js/apps.min.js"></script>
	<!-- ================== END BASE JS ================== -->
	
	<script>    
	    $(document).ready(function() {
	        App.init();
	    });
	</script>

</body>
</html>
