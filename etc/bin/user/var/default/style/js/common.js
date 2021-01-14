//获取Url参数
function getParameter() {
    var obj = {};
    var url = document.URL;
    var para = "";
    if (url.lastIndexOf("?") > 0) {
        para = url.substring(url.lastIndexOf("?") + 1, url.length);
        var arr = para.split("&");
        para = "";
        for (var i = 0; i < arr.length; i++) {
            var key = arr[i].split("=")[0];
            var name = arr[i].split("=")[1];
            obj[key] = name;
        }
    }
    return obj;
}
//滑动预览
function scroll(scrollTop, speed) {
    var speed = speed > 0 ? speed : 500;
    $("html,body").stop().animate({scrollTop: scrollTop}, speed);
}

$(function () {
    (function () {
        //头部
        var str_header = '<div class="header" id="header">' +
            '<div class="wrap clearfix">' +
            '<div class="logo">' +
            '<a href="/" title="演示站-首页">演示站</a>' +
            '</div>' +
            '<div class="nav">' +
            '<ul> ' +
                //'<li class="soluLinkBtn"> ' +
                //    // '<a href="solution.html">支付解决方案</a>' +
                //'<a href="javascript:;">支付解决方案</a>' +
                //'<div class="smenu clearfix">' +
                //'<div class="mspace"></div>' +
                //'<div class="mtm" data-target="phone"><a href="solution.html?m=phone">移动支付</a></div>' +
                //'<div class="mtm" data-target="pc"><a href="solution.html?m=pc">PC支付</a></div>' +
                //'<div class="mtm" data-target="code"><a href="solution.html?m=code">扫码支付</a></div>' +
                //'</div>' +
                //'</li>' +
            '<li><a href="/">首页</a></li> ' +
            '<li><a href="demo/demo.rar">开发文档</a></li> ' +
            '<li><a href="/demo/">DEMO测试</a></li> ' +
            '</ul>' +
            '</div>' +
            '<div class="info">' +
            '<p class="user">' +
            '<a href="/agent.php" target="_blank" class="lgBtn">代理登录</a>' +
            '<a href="?a=user&b=index&c=login" target="_blank" class="lgBtn">商户登录</a>' +
            '<a href="?a=user&b=index&c=register" target="_blank" class="lgBtn">免费注册</a>' +
            '</p>' +
            '</div> ' +
            '</div>' +
            '</div>';

        //尾部
        var str_footer = '<div class="footer clearfix">' +
            '<div class="w1200 clearfix">' +
            '<div class="frlink">' +
            '<h1>友情链接</h1>' +
            '<div class="linkbox">' +
            '<a target="_blank" href="https://www.alipay.com">支付宝</a><span>|</span>' +
            '<a target="_blank" href="https://www.baifubao.com">百度钱包</a><span>|</span>' +
            '<a target="_blank" href="http://jr.jd.com">京东金融</a><span>|</span>' +
            '<a target="_blank" href="https://pay.weixin.qq.com">微信支付</a><span>|</span>' +
            '</div>' +
            '</div>' +

           
   
            '<p class="f_company">演示站 京ICP备8888888号-8</p>' +
            '<div class="wechat"> <img src="/etc/bin/user/var/default/style/picture/weChat_code.jpg" width="200" height="200" alt="XXXX网络科技有限公司官方公众号"></div>' +
            '</div>' +
            '<div class="backTop" id="backTop">' +
            '<div class="backTopBtn">' +
            '<img src="/etc/bin/user/var/default/style/images/backtop.png" width="17" height="10" alt="回到顶部" title="回到顶部" />' +
            '<span>Top</span>' +
            '</div>' +
            '</div>' +
            '</div>';

        var loadJSON = {
            "header": str_header,
            "footer": str_footer
        }
        //加载头和尾
        $("body").prepend(loadJSON.header).append(loadJSON.footer);
        //体验
        /*$("#tyBtn ").hover(function () {
         $(".imgWait").addClass('show');
         },function(){
         $(".imgWait").removeClass('show');
         });*/
    })();

    //支付解决方案
    (function () {
        var timer;
        //二级菜单
        $(".soluLinkBtn").hover(function () {
            $(".smenu").show();
            clearTimeout(timer);
            timer = setTimeout(function () {
                $(".smenu").addClass("inview");
            }, 300);
        }, function () {
            clearTimeout(timer);
            $(".smenu").removeClass("inview");
            timer = setTimeout(function () {
                $(".smenu").hide();
            }, 500);
        });

        //链接过来后定位对应的支付方式。
        var getParas = getParameter();
        // console.log(getParas);
        var top;
        var target = "." + getParas.m; //?m=pc
        if (!getParas.m) {
            return;
        }
        if (target == 'phone') {
            top = $(target).offset().top - 100;
        } else {
            top = $(target).offset().top - 40;
        }
        scroll(top);
    })();

    //向下预览
    $("#arrow").on("click", function () {
        var char_top = $("#character").offset().top - $("#header").outerHeight();
        scroll(char_top);
    });

    //回到顶部.
    $("#backTop").on("click", function () {
        scroll(0);
    });

    //咨询台
    (function () {
        var timer;
        $(".advisory .contact li ").hover(function () {
            var index = $(this).index();
            var oDiv = $('.advisory').find(".adct");
            oDiv.hide().eq(index).fadeIn();

        }, function () {
            var index = $(this).index();
            var oDiv = $('.advisory').find(".adct");
            oDiv.eq(index).fadeOut();
        });

        //回到顶部
        $(".btopBtn").on("click", function () {
            scroll(0);
        });
    })();

    //头部固定定位
    $(window).scroll(function () {
        var oStp = $(document).scrollTop();
        if (oStp >= 100) {
            if ($('.header').hasClass('fixed') == false) {
                $('.header').addClass('fixed');
            }
        } else {
            if ($('.header').hasClass('fixed')) {
                $('.header').removeClass('fixed');
            }
        }
    });

    //标记菜单栏
    (function () {
        var url = location.href; //地址链接
        var aLink = $("#header .nav ul > li");
        aLink.removeClass("active");
        if (getPara("solution.html")) {
            aLink.eq(0).addClass("current");
        }
        if (getPara("p2p.html")) {
            aLink.eq(1).addClass("current");
        }
        if (getPara("rsb.html") || getPara("sao.html")) {
            // aLink.eq(2).addClass("current");
            aLink.eq(0).addClass("current");
        }
        if (getPara("cost.html")) {
            aLink.eq(2).addClass("current");
        }
        if (getPara("doc.html")) {
            aLink.eq(3).addClass("current");
        }
        if (getPara("download.html")) {
            aLink.eq(4).addClass("current");
        }
        if (getPara("help.html")) {
            aLink.eq(5).addClass("current");
        }
        //判断是否是当前页面。
        function getPara(arg) {
            return url.indexOf(arg) > 0;
        }
    })();
    })();



