<?php

function sign($money, $record, $sdk) {
    $sign = md5(Number_format($money, 2, '.', '') . trim($record) . $sdk);
    return $sign;
}

$record = "test" . date("md") . functions::generateRandomNum(8);
$notify = functions::request("notify");
$sdk = functions::request("sdk");
$money = floatval(functions::request("money"));

$url = functions::getdomain() . "pay?sdk=" . $sdk . "&record=" . $record . "&money=" . $money . "&refer=" . $notify . "&notify_url=" . $notify . "&sign=" . sign($money, $record, $sdk) . "&attach=" . functions::request("attach")."&test=1";
if ($money > 0) {
    header('Location:' . $url);
}
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
        <form class="layui-form" style="margin-top: 20px;" id="from" method="post" enctype="multipart/form-data" action="" target="_blank">

            <div class="layui-form-item">
                <label class="layui-form-label">测试金额</label>
                <div class="layui-input-block">
                    <input type="text" name="money" placeholder="请输入测试金额" class="layui-input" style="width: 98%;" value="1">
                    <input type="text" name="sdk" style="display: none"  value="<?php echo $data['sdk']; ?>">
                    <input type="text" name="payType" style="display: none"  value="<?php echo $data['typec']; ?>">
                    <input type="text" name="merchant" style="display: none"  value="<?php echo $data['userid']; ?>">
                    <div class="layui-form-mid layui-word-aux">请输入测试金额</div>
                </div>
            </div>

            <div class="layui-form-item">
                <label class="layui-form-label">回调地址</label>
                <div class="layui-input-block">
                    <input type="text" name="notify" placeholder="请输入回调地址" class="layui-input" style="width: 98%;" value="http://www.baidu.com/">
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button class="layui-btn" lay-submit type="submit" lay-filter="add">确认提交</button>
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
            });

        </script>
    </body>
</html>