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
                            <th>买家ID</th>
                            <th>买家手机</th>
                            <th>卖家ID</th>
                            <th>卖家手机</th>
                            <th>操作</th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php if (!empty($data)) { ?>
                            <?php foreach ($data as $x) { ?>
                                <tr>
                                    <td><?php echo $x['buyer_id']; ?></td>
                                    <td><?php echo $x['buyer_phone']; ?></td>
                                    <td><?php echo $x['seller_id']; ?></td>
                                    <td><?php echo $x['seller_phone']; ?></td>
                                    <td>
                                        <?php if (!empty($x['buyer_id']) && $x['buyer_id'] == $id) { ?>
                                            <a href="#" class="layui-btn layui-btn-disabled layui-btn-xs">已绑定</a>
                                        <?php } else { ?>
                                            <a href="#" class="layui-btn layui-bg-green layui-btn-xs getSeller" mid="<?php echo $x['id']; ?>">点击绑定商户</a>
                                        <?php } ?>
                                    </td>
                                </tr>

                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="5" align="center">
                                    请搜索卖家以后绑定
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table> 
                <div class="layui-table-page">
                    <div id="layui-table-page1">
                        <div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">
                            <?php functions::drive('page')->auto($data['info']['page'],$data['info']['current'],10);?>
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
        </script>
    </body>
</html>