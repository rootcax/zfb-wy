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
  <div class="layui-form-item">
    <label class="layui-form-label">标题</label>
    <div class="layui-input-block">
      <div class="layui-form-mid layui-word-aux"><?php echo $data['title']?></div>
    </div>
  </div>

  <div class="layui-form-item">
    <label class="layui-form-label">内容</label>
    <div class="layui-input-block">
      <div class="layui-form-mid layui-word-aux"><?php echo $data['contents']?></div>
    </div>
  </div>
     
</body>
</html>