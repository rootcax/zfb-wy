<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link rel="stylesheet" href="<?php echo _pub; ?>layui/css/layui.css"  media="all">
    </head>
    <body>    
        <div>
            <!-- 内容主体区域 -->
            <div style="padding: 15px;">
                <table class="layui-table" lay-even="" lay-skin="nob">
                    <caption>
                        <form action="" id="form" method="post" style="display:inline-block;">
                            卖家ID：<input type="text" value="<?php echo $_REQUEST['userid']; ?>" name="userid" placeholder="请输入卖家ID" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                            卖家手机号：<input type="text" value="<?php echo $_REQUEST['phone']; ?>" name="phone" placeholder="请输入手机号" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                            <input type="hidden" name="page" id="page" value="<?php echo $_POST['page'] ?>"/>
                            <input type="submit" name="btn" value="搜索卖家" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                        </form>
                    </caption>
                    <thead>
                        <tr>
                            <th>卖家ID</th>
                            <th>卖家手机</th>
                            <th>操作</th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php if (!empty($data)) { ?>
                            <?php foreach ($data as $x) { ?>
                                <tr>
                                    <td><?php echo $x['id']; ?></td>
                                    <td><?php echo $x['phone']; ?></td>
                                    <td>
                                        <a href="#" class="layui-btn layui-bg-green layui-btn-xs getSeller" sid="<?php echo $x['id']; ?>">点击绑定商户</a>
                                    </td>
                                </tr>

                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3" align="center">
                                    请搜索卖家以后绑定
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table> 
                <div class="layui-table-page">
                    <div id="layui-table-page1">
                        <div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">
                            <?php functions::drive('page')->auto($data['info']['page'], $data['info']['current'], 10); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" id="user-id" value="<?php echo $id; ?>">
        <script src="<?php echo _pub; ?>layui/layui.js" charset="utf-8"></script>
        <script src="<?php echo _pub; ?>js/jquery.min.js" charset="utf-8"></script>
        <script>
            layui.use(['form', 'layedit'], function () {
                var form = layui.form
                        , layer = layui.layer
                        , layedit = layui.layedit;
            });
            $('.getSeller').stop().click(function () {
                $(this).remove;
                var sid = $(this).attr('sid'),
                        bid = <?php echo $id?>;

                $.ajax({
                    type: "POST",
                    url: "/visa_admin.php?b=action&c=addDesignation",
                    data: {sid: sid, bid: bid},
                    dataType: "json",
                    success: function (res) {
                        if (res.code == 200)
                        {
                            layer.msg(res.msg, {icon: 1});
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        } else {
                            layer.msg(res.msg, {icon: 2});
                            console.log(res.msg);
                        }
                    },
                    error: function (data) {
                        layer.msg(data.msg, {icon: 2});
                        console.log('系统异常');
                    }
                })

            });
        </script>
    </body>
</html>