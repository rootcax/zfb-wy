 <?php require ('header.php');?>
  <div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
<table class="layui-table" lay-even="" lay-skin="nob">
  <colgroup>
    <col width="150">
    <col width="150">
    <col width="200">
    <col>
  </colgroup>
  <thead>
    <tr>
      <th>代理ID</th>
      <th>手机号</th>
      <th>IP地址</th>
      <th>注册时间</th>
      <th>登录时间</th>
      <th>余额</th>
      <th>状态</th>
      <th>操作</th>
    </tr> 
  </thead>
  <tbody>
  <?php foreach ($data['query'] as $minet){?>
    <tr>
      <td><?php echo $minet['id'];?></td>
      <td><?php echo $minet['phone'];?></td>
      <td><?php echo $minet['ip'];?></td>
      <td><?php echo date('Y/m/d H:i:s',$minet['regc']);?></td>
      <td><?php echo date('Y/m/d H:i:s',$minet['loginc']);?></td>
      <td style="color: green;font-weight:bold;"><?php echo $minet['balance'];?></td>
      <td <?php if($minet['status']==0){?>style='color: #985f0d;font-weight:bold;'<?php }else if($minet['status']==1){?>style='color: green;font-weight:bold;'<?php }else{?>style='color: red;font-weight:bold;'<?php }?>><?php if($minet['status']==0){echo '未审核';}else if($minet['status']==1){echo '正常';}else{echo '已冻结';}?></td>
      <td><a href="#" style="color: green;" onclick="edit(<?php echo $minet['id'];?>);">修改</a> / <a href="#" style="color: red;" onclick="del(<?php echo $minet['id'];?>);">删除</a></td>
      
    </tr>
  </tbody>
  <?php }?>
</table> 
<div class="layui-table-page"><div id="layui-table-page1"><div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">

 <?php functions::drive('page')->auto($data['info']['page'],$data['info']['current'],10);?>
<form action="" method="post" style="display: inline-block;">
<span class="layui-laypage-skip">查询
<input type="text" name="where" value="" style="width: 200px;" class="layui-input" placeholder="这里只能是数字级别的..">
<select name="q">
<option value="uid" selected>用户ID</option>
<option value="phone">手机号</option>
</select>
<button type="submit" class="layui-laypage-btn">开始查询</button></span>
</form>


</div></div></div>
</div>
  </div>
<script type="text/javascript">
function del(id){
	 var r=confirm("你真的要删除该用户,包括他的一切数据?如果存在下级用户，则将转为平台直接用户，该操作不可恢复")
	  if (r==true)
	   {
	    location.href="<?php echo functions::get_Config('webCog')['site'] .  'visa_admin.php?b=action&c=agent_del&id=';?>" + id;
	   }
}

function edit(id){
	layer.open({
		  type: 2,
		  title: '修改',
		  shadeClose: true,
		  shade: 0.8,
		  area: ['680px', '440px'],
		  content: '<?php echo functions::get_Config('webCog')['site'] .  'visa_admin.php?b=index&c=agent_edit&id=';?>' + id //iframe的url
		}); 
}
</script>
  <?php require ('footer.php');?>
  

