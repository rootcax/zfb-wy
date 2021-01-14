<?php
require ('function.php');

?>
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
        <form class="layui-form" style="margin-top: 20px;" method="post" enctype="multipart/form-data" action="<?php echo functions::urlc('user', 'action', 'qrcode_add'); ?>">
            <div class="layui-form-item">
                <label class="layui-form-label">二维码</label>
                <div class="layui-input-block">
                    <input type="file" name="image" placeholder="230px*230px" class="layui-input" style="width: 98%;">
                    <div class="layui-form-mid layui-word-aux">请将二维码图片制作成230px*230px的图片进行上传</div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">金额</label>
                <div class="layui-input-block">
                    <input type="text" name="money" value="0" placeholder="只能上传通用码" class="layui-input" style="width: 98%;" disabled="disabled">
                    <div class="layui-form-mid layui-word-aux">只能上传通用码</div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">整额</label>
                <div class="layui-input-block">
                    <input type="text" name="money_res" value="0" class="layui-input" style="width: 98%;" disabled="disabled">
                    <div class="layui-form-mid layui-word-aux">只能上传通用码</div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">收款账号</label>
                <div class="layui-input-block"  style="width: 300px;">
                    <select name="land_id" id="land_id">
                        <?php foreach ($land as $ld) { ?>
                            <option value="<?php echo $ld['id']; ?>"><?php echo $ld['username']; ?>(<?php echo M::payc($ld['typec']); ?>)</option>
                        <?php } ?>
                    </select>
                    <div class="layui-form-mid layui-word-aux">将二维码绑定到收款账号</div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" type="submit">确认上传</button>
                    <!--<button class="layui-btn" type="button" onclick="upload();">开启批量上传</button>-->
                </div>
            </div>
        </form>

        <script src="<?php echo _pub; ?>layui/layui.js" charset="utf-8"></script>
        <script src="<?php echo _theme; ?>js/jquery.min.js" charset="utf-8"></script>
        <script>
            layui.use(['form', 'layedit'], function () {
                var form = layui.form
                        , layer = layui.layer
                        , layedit = layui.layedit;
            });


        </script>
    </body>
</html>