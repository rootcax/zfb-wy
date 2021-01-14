<?php require ('function.php'); ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>订单管理系统</title>
        <link rel="stylesheet" href="<?php echo _pub . 'layui/css/layui.css' ?>">
        <link rel="stylesheet" href="<?php echo _pub . 'jquery-ui-1.12.1/jquery-ui.css' ?>">
        <script src="<?php echo _pub; ?>js/jquery.min.js"></script>
        <script src="<?php echo _pub; ?>js/layer/layer.js"></script>
        <script src="<?php echo _pub; ?>layui/layui.js" charset="utf-8"></script>
        <script type="text/javascript">
            function add_user() {
                layer.open({
                    type: 2,
                    title: '添加用户',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['680px', '440px'],
                    content: '<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=user_add'; ?>' //iframe的url
                });
            }

            function add_agent() {
                layer.open({
                    type: 2,
                    title: '添加代理',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['680px', '400px'],
                    content: '<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=agent_add'; ?>' //iframe的url
                });
            }

            function add_manager() {
                layer.open({
                    type: 2,
                    title: '添加管理员',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['680px', '400px'],
                    content: '<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=manager_add'; ?>' //iframe的url
                });
            }
            function manager_edit(id) {
                layer.open({
                    type: 2,
                    title: '修改',
                    shadeClose: true,
                    shade: 0.8,
                    area: ['680px', '350px'],
                    content: '<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=manager_edit&id=' ?>' + id//iframe的url
                });
            }
        </script>
        <style type="text/css">
            .editbt{
                color:green;
            }
            .deletebt{
                color:red;
            }
        </style>
    </head>
    <body>
        <div class="layui-layout layui-layout-admin">
            <div class="layui-header">
                <div class="layui-logo">日月支付 [正式版]</div>
                <!-- 头部区域（可配合layui已有的水平导航） -->

                <div class="layui-nav layui-layout-left">
                    <div class="index-nav-frame clearfix">
                        <?php
                        $admin = json_decode(functions::encode($_SESSION['user_admin'], AUTH_KEY, 2));
                        if ($admin->groupid == "1") {
                            ?>
                            <div class="layui-nav-item <?php if ($_REQUEST['c'] == "config") { ?>active<?php } ?>">
                                <a href="javascript:;">系统管理</a>
                                <dl class="layui-nav-child">
                                    <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=web_config' ?>">网站配置</a></dd>
                                    <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=sms_config' ?>">短信配置</a></dd>
                                    <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=reg_config' ?>">注册配置</a></dd>
                                    <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=agent_config' ?>">代理设置</a></dd>
                                    <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=withdraw_config' ?>">提现设置</a></dd>
                                        </dl>
                            </div>
                        <?php } ?>
                        <div class="layui-nav-item <?php if ($_REQUEST['c'] == "user") { ?>active<?php } ?>">
                            <a href="javascript:;">用户管理</a>
                            <dl class="layui-nav-child">
                                <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=user' ?>">用户管理</a></dd>
                                <dd><a href="#"  onclick="add_user()">添加用户</a></dd>
                            </dl>
                        </div>

                        <div class="layui-nav-item <?php if ($_REQUEST['c'] == "home" || $_REQUEST['c'] == "takes") { ?>active<?php } ?>">
                            <a href="javascript:;">订单管理</a>
                            <dl class="layui-nav-child">
                                <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=home' ?>">收款订单</a></dd>
                                <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=takes' ?>">支付订单</a></dd>
                                <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=recharge' ?>">充值订单</a></dd>
                            </dl>
                        </div>

                        <div class="layui-nav-item <?php if ($_REQUEST['c'] == "agent") { ?>active<?php } ?>">
                            <a href="javascript:;">代理管理</a>
                            <dl class="layui-nav-child">
                                <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=agent' ?>">代理管理</a></dd>
                                <?php if ($admin->groupid != "3") { ?><dd><a href="#" onclick="add_agent()">添加代理</a></dd><?php } ?>
                            </dl>
                        </div>
                        <?php if ($admin->groupid != "3") { ?>
                            <div class="layui-nav-item <?php if ($_REQUEST['c'] == "withdraw") { ?>active<?php } ?>">
                                <a href="javascript:;">提现管理</a>
                                <dl class="layui-nav-child">
                                    <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=withdraw' ?>">提现管理</a></dd>
                                </dl>
                            </div>
                        <?php } ?>
                        <div class="layui-nav-item <?php if ($_REQUEST['c'] == "manager") { ?>active<?php } ?>">
                            <a href="javascript:;">管理员管理</a>
                            <dl class="layui-nav-child">
                                <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=manager' ?>">管理员管理</a></dd>
                                <?php if ($admin->groupid == "1") { ?><dd><a href="#" onclick="add_manager()">添加管理员</a></dd><?php } ?>
                            </dl>
                        </div>

                        <div class="layui-nav-item <?php if ($_REQUEST['c'] == "bank") { ?>active<?php } ?>">
                            <a href="javascript:;">银行管理</a>
                            <dl class="layui-nav-child">
                                <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=bank' ?>">银行管理</a></dd>
                            </dl>
                        </div>

        

                        <div class="layui-nav-item <?php if ($_REQUEST['c'] == "news") { ?>active<?php } ?>">
                            <a href="javascript:;">文章管理</a>
                            <dl class="layui-nav-child">
                                <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=news' ?>">文章管理</a></dd>
                                <dd><a href="<?php echo functions::getdomain() . 'visa_admin.php?b=index&c=news_add' ?>">添加文章</a></dd>
                            </dl>
                        </div>

                    </div>
                </div>
                <div class="layui-nav layui-layout-right">
                    <div class="layui-nav-item">
                        <a href="javascript:;">
                            <img src="<?php if (!empty($admin->avatar)) {
                                    echo _pub . 'upload/' . $admin->sid . '/images/' . $admin->avatar;
                                } else {
                                    echo _pub . 'cache/images/ns.jpg';
                                } ?>" class="layui-nav-img">
<?php echo $admin->username; ?>
                        </a>
                        <dl class="layui-nav-child">
                            <dd><a href="#" onclick="manager_edit(<?php echo $admin->sid; ?>)">基本资料</a></dd>
                            <!--<dd><a href="">安全设置</a></dd>-->
                        </dl>
                    </div>
                    <div class="layui-nav-item"><a href="<?php echo functions::urlc('visa_admin', 'action', 'out') ?>">退出登录</a></div>
                </div>
            </div>


