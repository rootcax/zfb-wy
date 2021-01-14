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


支付改变生活..
 
<script src="<?php echo _pub;?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo _theme;?>js/jquery.min.js" charset="utf-8"></script>
<script>
layui.use(['element', 'layer'], function(){
  var element = layui.element;
  var layer = layui.layer;
  
  //监听折叠
  element.on('collapse(test)', function(data){
    layer.msg('展开状态：'+ data.show);
  });
});
</script>
</body>
</html>