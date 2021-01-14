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
        <form class="layui-form" style="margin-top: 20px;" id="from">
            <div class="layui-form-item">
                <label class="layui-form-label">用户名</label>
                <div class="layui-input-block">
                    <?php if ($user->groupid < 2) { ?><input type="text" name="username" placeholder="请输入用户名" class="layui-input" value="<?php echo $data['username'] ?>" style="width: 98%;"><?php } else { ?><div class="layui-form-mid layui-word-aux"><?php echo $data['username'] ?></div><?php } ?>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">手机号码</label>
                <div class="layui-input-block">
                    <input type="text" name="phone" placeholder="请输入手机号码" class="layui-input" value="<?php echo $data['phone'] ?>" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">修改密码</label>
                <div class="layui-input-block">
                    <input type="text" name="pwd" placeholder="如果不需要修改请留空.." class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">用户组</label>
                <div class="layui-input-block">
                    <select name="group_id" style="width:98%" <?php if ($manager->groupid != 1){if($manager->sid == $data['id']) { ?>disabled="disabled"<?php } } ?>>
                        <option value="1" <?php echo $data['group_id'] == '1' ? 'selected' : ''; ?>>超级管理员</option>
                        <option value="2" <?php echo $data['group_id'] == '2' ? 'selected' : ''; ?>>管理员</option>
                        <option value="3" <?php echo $data['group_id'] == '3' ? 'selected' : ''; ?>>客服</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">状态</label>
                <div class="layui-input-block">
                    <select name="status" style="width:98%" <?php if ($user->sid == $data['id']) { ?>disabled="disabled"<?php } ?>>
                        <option value="0" <?php echo $data['status'] == '0' ? 'selected' : ''; ?>>未审核</option>
                        <option value="1" <?php echo $data['status'] == '1' ? 'selected' : ''; ?>>正常</option>
                        <option value="2" <?php echo $data['status'] == '2' ? 'selected' : ''; ?>>已冻结</option>
                    </select>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit type="button" lay-filter="add">确认修改</button>
                </div>
            </div>
        </form>

        <script src="<?php echo _pub; ?>layui/layui.js" charset="utf-8"></script>
        <script src="<?php echo _pub; ?>js/jquery.min.js" charset="utf-8"></script>
        <script>
            layui.use(['form', 'layedit'], function () {
                var form = layui.form
                        , layer = layui.layer
                        , layedit = layui.layedit;
                //添加
                form.on('submit(add)', function () {
                    layer.load();
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=manager_edit&id=' . $data['id']; ?>",
                        data: $('#from').serialize(),
                        success: function (data) {
                            if (data.code == '200') {
                                layer.closeAll('loading');
                                layer.msg(data.msg, {icon: 1});
                                setTimeout(function () {
                                    location.href = '';
                                }, 2000);
                            } else {
                                layer.closeAll('loading');
                                layer.msg(data.msg, {icon: 2});
                            }
                        },
                        error: function (data) {
                            alert("error:" + data.responseText);
                        }
                    });

                });
            });
        </script>
    </body>
</html>