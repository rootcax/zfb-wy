<?php require ('header.php'); ?>
<div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">


        <link rel="stylesheet" href="<?php echo _pub; ?>layui/css/layui.css"  media="all">

        <form class="layui-form" style="margin-top: 20px;" id="from">
            <div class="layui-form-item">
                <label class="layui-form-label">次数</label>
                <div class="layui-input-block">
                    <input type="text" name="drawcount" value="<?php if (empty($data['drawcount'])) {
    echo 1;
} else {
    echo $data['drawcount'];
} ?>" class="layui-input" style="width: 98%;">
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">最低金额</label>
                <div class="layui-input-block">
                    <input type="text" name="min_payment" value="<?php echo $data['min_payment']; ?>" class="layui-input" style="width: 98%;">
                </div>
            </div>

            <!--<div class="layui-form-item">
             <label class="layui-form-label">最高金额</label>
             <div class="layui-input-block">
               <input type="text" name="max_payment" value="<?php echo $data['max_payment']; ?>" class="layui-input" style="width: 98%;">
             </div>
           </div>-->

            <div class="layui-form-item">
                <label class="layui-form-label">手续费</label>
                <div class="layui-input-block">
                    <input type="text" name="drwaFee" value="<?php echo $data['drwaFee']; ?>" class="layui-input" style="width: 98%;">
                </div>
            </div>

            <!--<div class="layui-form-item">
              <label class="layui-form-label">周期</label>
              <div class="layui-input-block">
               <input type="text" name="cycle" value="<?php echo $data['cycle']; ?>" class="layui-input" style="width: 98%;">
              </div>
            </div>-->

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit type="button" lay-filter="edit">确定</button>
                </div>
            </div>
        </form>

    </div>
</div>

<script src="<?php echo _pub; ?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo _pub; ?>js/jquery.min.js" charset="utf-8"></script>
<script>
    layui.use(['form', 'layedit'], function () {
        var form = layui.form
                , layer = layui.layer
                , layedit = layui.layedit;
//添加
        form.on('submit(edit)', function () {
            layer.load();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=withdraw_config'; ?>",
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


<?php require ('footer.php'); ?>
  

