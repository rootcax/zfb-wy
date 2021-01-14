<?php require ('function.php');?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="<?php echo _pub;?>layui/css/layui.css"  media="all">
</head>
<body>    
<form class="layui-form" style="margin-top: 20px;" method="post" enctype="multipart/form-data" action="<?php echo functions::urlc('user', 'action', 'qrcode_edit');?>">
  
  
   <div class="layui-form-item">
    <label class="layui-form-label"></label>
    <div class="layui-input-block">
     <input type="hidden" name="id"  value="<?php echo $data['id'];?>">
    <img alt="二维码" src="<?php echo _pub . 'cache/images/' . $data['qrcode'];?>" width="160">
    </div>
  </div>
  
  <div class="layui-form-item">
    <label class="layui-form-label">更改</label>
    <div class="layui-input-block">
      <input type="file" name="image" placeholder="230px*230px" class="layui-input" style="width: 98%;">
      <div class="layui-form-mid layui-word-aux">不修改二维码图片，请不要上传</div>
    </div>
  </div>

  <div class="layui-form-item">
    <label class="layui-form-label">金额</label>
    <div class="layui-input-block">
      <input type="text" name="money" placeholder="只能上传通用码" class="layui-input" style="width: 98%;" value="<?php echo $data['money'];?>" disabled="disabled">
    <div class="layui-form-mid layui-word-aux">只能上传通用码</div>
    </div>
  </div>
  
  <div class="layui-form-item">
    <label class="layui-form-label">整额</label>
    <div class="layui-input-block">
      <input type="text" name="money_res" class="layui-input" style="width: 98%;" value="<?php echo $data['money_res'];?>" disabled="disabled">
      <div class="layui-form-mid layui-word-aux">只能上传通用码</div>
    </div>
  </div>
  
    <div class="layui-form-item">
    <label class="layui-form-label">收款账号</label>
    <div class="layui-input-block">
      <div class="layui-form-mid layui-word-aux"><?php $call = functions::open_mysql()->query("land","id={$data['land_id']}"); echo $call[0]['username']. '(' . M::payc($call[0]['typec']) . ')';?></div>
    </div>
  </div>

  <div class="layui-form-item">
    <div class="layui-input-block">
      <button class="layui-btn" type="submit">确认修改</button>
      <button class="layui-btn layui-btn-normal" type="button" onclick="closed();">取消</button>
    </div>
  </div>
</form>

<script src="<?php echo _pub;?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo _theme;?>js/jquery.min.js" charset="utf-8"></script>
<script>
layui.use(['form', 'layedit'], function(){
  var form = layui.form
  ,layer = layui.layer
  ,layedit = layui.layedit;
});

function closed(){
	var index = parent.layer.getFrameIndex(window.name);
	parent.layer.close(index);
}

</script>
</body>
</html>