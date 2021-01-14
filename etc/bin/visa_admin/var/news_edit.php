<?php require ('function.php'); ?>
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
                <label class="layui-form-label">标题</label>
                <div class="layui-input-block">
                    <input type="text" name="title" placeholder="请输入标题.." value="<?php echo $data['title']; ?>" class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">内容</label>
                <div class="layui-input-block">
                    <textarea class="layui-textarea" id="contents" name="content" style="display: none">
                        <?php echo $data['contents']; ?>
                    </textarea>
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
                        var index = layedit.build('contents');
                //添加
                form.on('submit(add)', function () {
                    layer.load();
                    layedit.sync(index);
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=news_edit&id=' . $data['id']; ?>",
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