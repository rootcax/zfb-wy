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
                <label class="layui-form-label">银行名称</label>
                <div class="layui-input-block">
                    <div class="layui-form-mid layui-word-aux"><?php echo $data['bank_name'] ?></div>
                    <input type="hidden" name="bank_id" value="<?php echo $data['bank_id'] ?>">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">单笔限额</label>
                <div class="layui-input-block">
                    <input type="text" name="singleQuota" placeholder="请输入单笔限额.." value="" class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">每日限额</label>
                <div class="layui-input-block">
                    <input type="text" name="dailyQuota" placeholder="请输入每日限额.." class="layui-input" style="width: 98%;">
                </div>
            </div>
            
            <div class="layui-form-item">
                <label class="layui-form-label">需要满足条件</label>
                <div class="layui-input-block">
                    <input type="text" name="memo" placeholder="请输入需要满足条件.." class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">排序</label>
                <div class="layui-input-block">
                    <input type="text" name="sortId" placeholder="数字越小越靠前.." class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit type="button" lay-filter="add">确认</button>
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
                        url: "/visa_admin.php?b=action&c=bank_memo_add",
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