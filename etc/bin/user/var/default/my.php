<?php require ('function.php');?>
<?php $csrf = functions::getcsrf();?>
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
<form class="layui-form" style="margin-top: 20px;" id="from">
  
  
   <div class="layui-form-item">
    <label class="layui-form-label"></label>
    <div class="layui-input-block">
    <img alt="二维码" src="<?php  if (!empty($user->avatar)){ echo _pub . 'upload/' . $user->sid . '/images/' . $user->avatar; }else{ echo _theme . 'images/ns.png'; }?>" width="160">
    </div>
  </div>
  
  <!--<div class="layui-form-item">
    <label class="layui-form-label">头像</label>
    <div class="layui-input-block">
      <input type="file" name="avatar" id="avatar" class="layui-input" style="width: 98%;" onchange="uploadPic();">
    </div>
  </div>-->

  <div class="layui-form-item">
    <label class="layui-form-label">手机</label>
    <div class="layui-input-block">
    	<div class="layui-form-mid layui-word-aux"><?php echo $user->phone;?></div>  
    </div>
  </div>
  
   <!--<div class="layui-form-item">
    <label class="layui-form-label">验证码</label>
    <div class="layui-input-block">
      <input type="text" name="code" class="layui-input" style="width: 150px;float:left;">
      <button class="layui-btn layui-btn-normal" type="button" style="width:120px;float:left;margin-left:10px;" id="sms" onclick="sendemail();">发送验证码</button>
    </div>
  </div>-->
  
  <div class="layui-form-item">
    <label class="layui-form-label">新密码</label>
    <div class="layui-input-block">
      <input type="text" name="pwd" class="layui-input" style="width: 98%;">
    </div>
  </div>

  <div class="layui-form-item">
    <div class="layui-input-block">
      <button class="layui-btn" type="button" onclick="pwds();">立即修改</button>
    </div>
  </div>
</form>

<script src="<?php echo _pub;?>layui/layui.js" charset="utf-8"></script>
<script src="<?php echo _theme;?>js/jquery.min.js" charset="utf-8"></script>
<script>

function pwds(){
	layer.load();
	$.ajax({
        type: "POST",
        dataType: "json",
        url: "<?php echo functions::urlc('user', 'action', 'edit')?>",
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
}

layui.use(['form', 'layedit'], function(){
  var form = layui.form
  ,layer = layui.layer
  ,layedit = layui.layedit;
});

//上传头像
function uploadPic(){
    var pic = $('#avatar')[0].files[0];
    var fd = new FormData();
    fd.append('avatar', pic);
    $.ajax({
        url:"<?php echo functions::urlc('user', 'action', 'avatar')?>",
        type:"post",
        // Form数据
        data: fd,
        cache: false,
        contentType: false,
        processData: false,
        success:function(data){
            if(data.code == '200'){
            	layer.msg(data.msg, {icon: 1});
            	setTimeout(function(){location.href = '';},1000);
            }else{
            	layer.msg(data.msg, {icon: 2});
            }
        }
    });
                    
}




var countdown=90; 
function sendemail(){
	var obj = $("#sms");
	var csrf = $('#csrf').val();
	layer.load();
	 $.get("<?php echo functions::get_Config('webCog')['site'];?>?a=user&b=api&c=sms&typec=3&phone=<?php echo $user->phone;?>&csrf=<?php echo $csrf;?>", function(result){
		    if(result.code == '200'){
		    	layer.closeAll('loading');
            	layer.msg(result.msg, {icon: 1});
                settime(obj);
			}else{
				layer.closeAll('loading');
            	layer.msg(result.msg, {icon: 2});
			}
		});
    }
    
function settime(obj) { //发送验证码倒计时
    if (countdown == 0) { 
        obj.attr('disabled',false); 
        //obj.removeattr("disabled"); 
        obj.text("发送验证码");
        countdown = 60;
        return;
    } else { 
        obj.attr('disabled',true);
        obj.text("重新发送(" + countdown + ")");
        countdown--; 
        console.log(countdown);
    } 
setTimeout(function() { 
    settime(obj) }
    ,1000) 
}

</script>
</body>
</html>