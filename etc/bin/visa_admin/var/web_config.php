 <?php require ('header.php');?>
  <div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">


<link rel="stylesheet" href="<?php echo _pub;?>layui/css/layui.css"  media="all">

<form class="layui-form" style="margin-top: 20px;" id="from">
    <div class="layui-form-item">
    <label class="layui-form-label">网址</label>
    <div class="layui-input-block">
     <input type="text" name="site" value="<?php echo $data['site'];?>" class="layui-input" style="width: 98%;">
    </div>
  </div>
    
  <div class="layui-form-item">
    <label class="layui-form-label">网站标题</label>
    <div class="layui-input-block">
     <input type="text" name="title" value="<?php echo $data['title'];?>" class="layui-input" style="width: 98%;">
    </div>
  </div>

  <div class="layui-form-item">
    <label class="layui-form-label">关键词</label>
    <div class="layui-input-block">
      <input type="text" name="keywords" value="<?php echo $data['keywords'];?>" class="layui-input" style="width: 98%;">
    </div>
  </div>
  
   <div class="layui-form-item">
    <label class="layui-form-label">网站描述</label>
    <div class="layui-input-block">
      <input type="text" name="description" value="<?php echo $data['description'];?>" class="layui-input" style="width: 98%;">
    </div>
  </div>
    
    <div class="layui-form-item">
    <label class="layui-form-label">网站主题</label>
    <div class="layui-input-block">
      <input type="text" name="theme" value="<?php echo $data['theme'];?>" class="layui-input" style="width: 98%;">
    </div>
  </div>
    
    <div class="layui-form-item">
    <label class="layui-form-label">单笔最大提交金额</label>
    <div class="layui-input-block">
      <input type="text" name="max_money" value="<?php echo $data['max_money'];?>" class="layui-input" style="width: 98%;">
    </div>
  </div>
    
    <div class="layui-form-item">
    <label class="layui-form-label">单笔最小提交金额</label>
    <div class="layui-input-block">
      <input type="text" name="min_money" value="<?php echo $data['min_money'];?>" class="layui-input" style="width: 98%;">
    </div>
  </div>

  <div class="layui-form-item">
    <div class="layui-input-block">
      <button class="layui-btn" lay-submit type="button" lay-filter="edit">确定</button>
    </div>
  </div>
</form>
 
</div>
  </div>
  

<script src="<?php echo _pub;?>js/jquery.min.js" charset="utf-8"></script>
<script>
layui.use(['form', 'layedit'], function(){
  var form = layui.form
  ,layer = layui.layer
  ,layedit = layui.layedit;
//添加
  form.on('submit(edit)', function(){
	  	layer.load();
	  	$.ajax({
	          type: "POST",
	          dataType: "json",
	          url: "<?php echo functions::getdomain() . 'visa_admin.php?b=action&c=web_config';?>",
	          data: $('#from').serialize(),
	          success: function (data) {
	              if(data.code == '200'){
	              	layer.closeAll('loading');
	              	layer.msg(data.msg, {icon: 1});
	              	setTimeout(function(){location.href = '';},2000);
	              }else{
	              	layer.closeAll('loading');
	              	layer.msg(data.msg, {icon: 2});
	              }
	          },
	          error: function(data) {
	              alert("error:"+data.responseText);
	           }
	  });

});
});
</script>
  
 
  <?php require ('footer.php');?>
  

