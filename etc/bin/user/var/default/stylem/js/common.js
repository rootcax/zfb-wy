(function(){function w(){var r=document.documentElement;var a=r.getBoundingClientRect().width;if(a>750){a=750;}
rem=a/7.5;r.style.fontSize=rem+"px"}
var t;w();window.addEventListener("resize",function(){clearTimeout(t);t=setTimeout(w,300)},false);})();$(function(){(function(){var str_header='<div style="margin: 0 auto;width: 0px;height: 0px;overflow: hidden;"> <img src="/stylem/images/s.png" width="512" height="512" alt="分享图片"/></div><header> '+
'<a href="index.html" class="logo">神马支付</a> '+
'<a href="javascript: ;" class="bmenu"><img src="/stylem/images/header_menu.png" width="44" height="88" alt=""></a>'+
'</header>';var str_nav='<div id="nav"> '+
'<ul> '+
'<li><a href="/">首页</a></li> '+
'<li><a href="download.html">开发文档</a></li> '+
'<li><a href="download.html">DEMO下载</a></li> '+
'</ul>'+
'</div>';var str_footer='<div class="footer clearfix">'+
'<div class="container-fluid">'+
'<div class="row contact-nav">'+
'<div class="col-xs-3"><a href="contact.html">联系我们</a></div>'+
'<div class="col-xs-3"><a href="about.html">关于我们</a></div>'+
'<div class="col-xs-3"><a href="privacy.html">隐私政策</a></div>'+
'<div class="col-xs-3"><a href="service.html">服务条款</a></div>'+
'</div>'+
'<div class="row">'+
'<p class="tele2">服务热线：<strong> 020-29051927</strong></p>'+
'<p class="f_email">电子邮箱：<a href="mailto: service@smfaka.com">service@smfaka.com</a></p>'+
'<p class="f_address">地址：广州市天河区东站路1号东站综合楼三楼A区R131房</p>'+
'<p class="f_company">广州追梦网络科技有限公司 粤ICP备15115032号-9</p>'+
'<div class="wechat"> <img src="/style/picture/weChat_code.jpg" width="200" height="200" alt="广州追梦网络科技有限公司官方公众号"></div>'+
'</div>'+
'</div>'+
'</div>';var loadJSON={"header":str_header,"nav":str_nav,"footer":str_footer}
$("body").prepend(loadJSON.nav).prepend(loadJSON.header).append(loadJSON.footer);})();$(".bmenu").on("click",function(){$("#nav").toggleClass("show")
$("header").toggleClass("show");if($("#mask2").length==0){var mask2=$("<div id='mask2'></div>");$("body").append(mask2);mask2.on("click",function(){$("#nav").removeClass("show");$(this).hide();});}
$("#mask2").toggle();});window.addEventListener('load',function(){FastClick.attach(document.body);},false);(function(){$("body").append('<div id="backTop"> <a href="javascript:;" title="回到顶部"></a></div>')
var winh=$(window).height()/2;$(window).scroll(function(){if($(window).scrollTop()>winh){$("#backTop").show();$("#backTop").click(function(){scroll(0,500);});}else{$("#backTop").hide();}});})();var _hmt=_hmt||[];(function(){var hm=document.createElement("script");hm.src="https://hm.baidu.com/hm.js?91fd749a2424b8f7c646f7fde0293f75";var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(hm,s);})();});function scroll(scrollTop,speed){var speed=speed>0?speed:500;$("html,body").stop().animate({scrollTop:scrollTop},speed);}