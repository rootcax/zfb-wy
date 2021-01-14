 <?php require ('header.php');?>
  <div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">


<link rel="stylesheet" href="<?php echo _pub;?>layui/css/layui.css"  media="all">

<form class="layui-form" style="margin-top: 20px;" id="from">  
  
  <div class="layui-form-item">
    <label class="layui-form-label">KeyId</label>
    <div class="layui-input-block">
     <input type="text" name="accessKeyId" value="<?php echo $data['accessKeyId'];?>" class="layui-input" style="width: 98%;">
     <div class="layui-form-mid layui-word-aux">阿里大鱼短信平台申请的accessKeyId</div>
    </div>
  </div>
  
  <div class="layui-form-item">
    <label class="layui-form-label">KeySecret</label>
    <div class="layui-input-block">
     <input type="text" name="accessKeySecret" value="<?php echo $data['accessKeySecret'];?>" class="layui-input" style="width: 98%;">
     <div class="layui-form-mid layui-word-aux">阿里大鱼短信平台申请的accessKeySecret</div>
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">SignName</label>
    <div class="layui-input-block">
     <input type="text" name="SignName" value="<?php echo $data['SignName'];?>" class="layui-input" style="width: 98%;">
     <div class="layui-form-mid layui-word-aux">阿里大鱼短信平台申请的短信模板名称</div>
    </div>
  </div>
  <div class="layui-form-item">
    <label class="layui-form-label">Template</label>
    <div class="layui-input-block">
     <input type="text" name="TemplateCode" value="<?php echo $data['TemplateCode'];?>" class="layui-input" style="width: 98%;">
     <div class="layui-form-mid layui-word-aux">阿里大鱼短信平台申请的短信模板(短信注册)</div>
    </div>
  </div>
    
    <!--<div class="layui-form-item">
    <label class="layui-form-label">Abnormal</label>
    <div class="layui-input-block">
     <input type="text" name="Abnormal" value="<?php echo $data['Abnormal'];?>" class="layui-input" style="width: 98%;">
     <div class="layui-form-mid layui-word-aux">阿里大鱼短信平台申请的短信模板(异常通知)</div>
    </div>
  </div>-->
    
    <!--注册页面是否开启短信验证-->
    <div class="layui-form-item">
    <label class="layui-form-label">注册页面短信开关</label>
    <div class="layui-input-block">
     <input type="radio" name="register_sms" value="0" title="关闭" <?php if ($data['register_sms']==0) echo 'checked';?>>
     <input type="radio" name="register_sms" value="1" title="开启" <?php if ($data['register_sms']==1) echo 'checked';?>>
    </div>
  </div>
    
    
    <!--添加收款账户页面是否开启短信验证-->
    <div class="layui-form-item">
    <label class="layui-form-label">添加收款账户页面短信开关</label>
    <div class="layui-input-block">
     <input type="radio" name="landadd_sms" value="0" title="关闭" <?php if ($data['landadd_sms']==0) echo 'checked';?>>
     <input type="radio" name="landadd_sms" value="1" title="开启" <?php if ($data['landadd_sms']==1) echo 'checked';?>>
    </div>
  </div>
    
    <!--修改收款账户页面是否开启短信验证-->
    <div class="layui-form-item">
    <label class="layui-form-label">修改收款账户页面短信开关</label>
    <div class="layui-input-block">
     <input type="radio" name="landedit_sms" value="0" title="关闭" <?php if ($data['landedit_sms']==0) echo 'checked';?>>
     <input type="radio" name="landedit_sms" value="1" title="开启" <?php if ($data['landedit_sms']==1) echo 'checked';?>>
    </div>
  </div>
    
    <!--删除收款账户页面是否开启短信验证-->
    <div class="layui-form-item">
    <label class="layui-form-label">删除收款账户页面短信开关</label>
    <div class="layui-input-block">
     <input type="radio" name="landdel_sms" value="0" title="关闭" <?php if ($data['landdel_sms']==0) echo 'checked';?>>
     <input type="radio" name="landdel_sms" value="1" title="开启" <?php if ($data['landdel_sms']==1) echo 'checked';?>>
    </div>
  </div>
    
    <!--代理提现页面是否开启短信验证-->
    <div class="layui-form-item">
    <label class="layui-form-label">代理提现账户页面短信开关</label>
    <div class="layui-input-block">
     <input type="radio" name="withdraw_sms" value="0" title="关闭" <?php if ($data['withdraw_sms']==0) echo 'checked';?>>
     <input type="radio" name="withdraw_sms" value="1" title="开启" <?php if ($data['withdraw_sms']==1) echo 'checked';?>>
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
  
<script src="<?php echo _pub;?>layui/layui.js" charset="utf-8"></script>
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
	          url: "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=sms_config';?>",
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
  

