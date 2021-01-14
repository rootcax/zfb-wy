<?php require ('header.php'); ?>   
<div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
        <table class="layui-table" lay-even="" lay-skin="nob">
            <caption>
                <form action="" id="form" method="post" style="display:inline-block;">
                    用户昵称：<input type="text" value="<?php echo $_REQUEST['user_name']; ?>" name="user_name" placeholder="请输入用户昵称" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    用户手机号：<input type="text" value="<?php echo $_REQUEST['user_phone']; ?>" name="user_phone" placeholder="请输入用户手机号" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    商户昵称：<input type="text" value="<?php echo $_REQUEST['merchant_name']; ?>" name="merchant_name" placeholder="请输入商户昵称" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    商户手机号：<input type="text" value="<?php echo $_REQUEST['merchant_phone']; ?>" name="merchant_phone" placeholder="请输入商户手机号" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <input type="hidden" name="page" id="page" value="<?php echo $_POST['page'] ?>"/>
                    <input type="submit" name="btn" value="搜索用户" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                </form>
            </caption>
            <thead>
                <tr>
                    <th>用户ID</th>
                    <th>用户昵称</th>
                    <th>用户账号</th>
                    <th>商户ID</th>
                    <th>商户昵称</th>
                    <th>商户账号</th>
                    <th>操作</th>
                </tr> 
            </thead>
            <tbody>
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td><?php echo $x['user_id']; ?></td>
                        <td><?php echo $x['user_name']; ?></td>
                        <td><?php echo $x['user_phone']; ?></td>
                        <td><?php echo $x['merchant_id']; ?></td>
                        <td><?php echo $x['merchant_name']; ?></td>
                        <td><?php echo $x['merchant_phone']; ?></td>
                        <td>
                            <a href="#" class="layui-btn layui-bg-green layui-btn-xs qMerchnat" mid="<?php echo $x['merchant_id']; ?>" uid="<?php echo $x['user_id']; ?>">取消绑定商户</a>
                        </td>
                    </tr>
                </tbody>
            <?php } ?>
        </table> 
        <div class="layui-table-page">
            <div id="layui-table-page1">
                <div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">
                    <?php functions::Common()->page_admin($data['info']['page'], $data['info']['current'], 20); ?>
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
    $('.qMerchnat').stop().click(function () {
        $(this).remove;
        var mid = $(this).attr('mid'),
                uid = $(this).attr('uid');

        $.ajax({
            type: "POST",
            url: "/visa_admin.php?b=action&c=delDesignation",
            data: {mid: mid, uid: uid},
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
<?php require ('footer.php'); ?>