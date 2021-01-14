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
                    <thead>
                        <tr>
                            <th>买家ID</th>
                            <th>买家手机</th>
                            <th>卖家ID</th>
                            <th>卖家手机</th>
                            <th>操作</th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php foreach ($data['query'] as $x) { ?>
                            <tr>
                                <td><?php echo $x['buyer_id']; ?></td>
                                <td><?php echo $x['buyer_phone']; ?></td>
                                <td><?php echo $x['seller_id']; ?></td>
                                <td><?php echo $x['seller_phone']; ?></td>
                                <td>
                                    <a href="#" class="layui-btn layui-bg-green layui-btn-xs qSeller" sid="<?php echo $x['seller_id']; ?>" bid="<?php echo $x['buyer_id']; ?>">取消绑定</a>
                                </td>
                            </tr>
                        </tbody>
                    <?php } ?>
                </table> 
                <div class="layui-table-page">
                    <div id="layui-table-page1">
                        <div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">
                            <?php functions::drive('page')->auto($data['info']['page'],$data['info']['current'],20);?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo _pub; ?>layui/layui.js" charset="utf-8"></script>
        <script src="<?php echo _pub; ?>js/jquery.min.js" charset="utf-8"></script>
        <script>
            layui.use(['form', 'layedit'], function () {
                var form = layui.form
                        , layer = layui.layer
                        , layedit = layui.layedit;
            });
            $('.qSeller').stop().click(function () {
                $(this).remove;
                var sid = $(this).attr('sid'),
                        bid = $(this).attr('bid');

                $.ajax({
                    type: "POST",
                    url: "/visa_admin.php?b=action&c=delDesignation",
                    data: {sid: sid, bid: bid},
                    dataType: "json",
                    success: function (res) {
                        if (res.code == 200)
                        {
                            layer.msg(res.msg, {icon: 1});
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
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