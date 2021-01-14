<?php //解析数据
$json = json_decode(functions::encode($data['reback'], AUTH_PE,2));
?>
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
    <label class="layui-form-label">账户</label>
    <div class="layui-input-block">
        <input type="text" name="username" placeholder="请输入你的收款账号.." class="layui-input" style="width: 98%;" value="<?php echo $data['username'];?>" disabled="disabled">
       <div class="layui-form-mid layui-word-aux">该账号不能为空,如果实在不想泄露账号可随意填写一个虚拟账号.</div>
    </div>
  </div>
 

  <div class="layui-form-item">
    <label class="layui-form-label">类型</label>
    <div class="layui-input-block">
      <input type="radio" name="typec" value="1" title="支付宝" <?php if ($data['typec']==1) echo 'checked';?> disabled>
      <input type="radio" name="typec" value="2" title="微信" <?php if ($data['typec']==2) echo 'checked';?> disabled>
    </div>
    <div class="layui-form-mid layui-word-aux">类型是指该收款账号的账号类型，无法修改</div>
  </div>
  <?php $sms_config = functions::get_Config('smsCog');  if($sms_config['landdel_sms']){?>
  <div class="layui-form-item">
                <label class="layui-form-label">验证码</label>
                <div class="layui-input-block">
                    <input type="text" name="code" class="layui-input" style="width: 150px;float:left;">
                    <button class="layui-btn layui-btn-normal" type="button" style="width:120px;float:left;margin-left:10px;" id="sms" onclick="sendemail();">发送验证码</button>
                </div>
            </div>
  <?php }?>
  <div class="layui-form-item">
    <div class="layui-input-block">
      <button class="layui-btn" lay-submit type="button" lay-filter="del">确认删除</button>
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
//添加

  form.on('submit(del)', function(){
	  	layer.load();
	  	$.ajax({
	          type: "POST",
	          dataType: "json",
	          url: "<?php echo functions::urlc('user', 'api', 'land_del',array('id'=>intval($_GET['id'])))?>",
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


var countdown = 90;
            function sendemail() {
                var obj = $("#sms");
                var csrf = $('#csrf').val();
                layer.load();
                $.get("<?php echo functions::get_Config('webCog')['site']; ?>?a=user&b=api&c=sms&typec=3&phone=<?php echo $user->phone; ?>&csrf=<?php echo $csrf; ?>", function (result) {
                            if (result.code == '200') {
                                layer.closeAll('loading');
                                layer.msg(result.msg, {icon: 1});
                                settime(obj);
                            } else {
                                layer.closeAll('loading');
                                layer.msg(result.msg, {icon: 2});
                            }
                        });
                    }

                    function settime(obj) { //发送验证码倒计时
                        if (countdown == 0) {
                            obj.attr('disabled', false);
                            //obj.removeattr("disabled"); 
                            obj.text("发送验证码");
                            countdown = 60;
                            return;
                        } else {
                            obj.attr('disabled', true);
                            obj.text("重新发送(" + countdown + ")");
                            countdown--;
                            console.log(countdown);
                        }
                        setTimeout(function () {
                            settime(obj)
                        }
                        , 1000)
                    }

function closed(){
	var index = parent.layer.getFrameIndex(window.name);
	parent.layer.close(index);
}
</script>
</body>
</html>