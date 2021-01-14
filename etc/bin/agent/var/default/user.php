 <?php require ('header.php');?>
  <div id="main">
    <!-- 内容主体区域 -->
<table class="table">
    <caption>
        <form action="" method="post" style="display:inline-block;">
            用户ID：<input type="text" value="<?php echo $_REQUEST['userid']; ?>" name="userid" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
            手机号码：<input type="text" value="<?php echo $_REQUEST['num']; ?>" name="num" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
            <select name="state" style="height: 31px;border:none;width: 78px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                <option value="" <?php echo $_REQUEST['status'] == '' ? 'selected' : ''; ?> >用户状态</option>
                <option value="1" <?php echo $_REQUEST['status'] == '1' ? 'selected' : ''; ?> >未审核</option>
                <option value="2" <?php echo $_REQUEST['status'] == '2' ? 'selected' : ''; ?> >正常</option>
                <option value="3" <?php echo $_REQUEST['status'] == '3' ? 'selected' : ''; ?> >冻结</option>
            </select>
            <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
        </form>
    </caption>
  <thead>
    <tr>
      <th>用户ID</th>
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
      <td><?php echo $minet['id'] + 10000;?></td>
      <td><?php echo $minet['phone'];?></td>
      <td><?php echo $minet['ip'];?></td>
      <td><?php echo date('Y/m/d H:i:s',$minet['regc']);?></td>
      <td><?php echo date('Y/m/d H:i:s',$minet['loginc']);?></td>
      <td style="color: green;font-weight:bold;"><?php echo $minet['balance'];?></td>
      <td <?php if($minet['status']==0){?>style='color: #985f0d;font-weight:bold;'<?php }else if($minet['status']==1){?>style='color: green;font-weight:bold;'<?php }else{?>style='color: red;font-weight:bold;'<?php }?>><?php if($minet['status']==0){echo '未审核';}else if($minet['status']==1){echo '正常';}else{echo '已冻结';}?></td>
      <td><a href="#" style="color: green;" onclick="edit(<?php echo $minet['id'];?>);">修改</a></td>
      
    </tr>
  </tbody>
  <?php }?>
</table> 
<ul class="pagination" style="margin-top: 0px;">
 <?php functions::drive('page')->auto($data['info']['page'],$data['info']['current'],10);?>
</ul></div>
<script type="text/javascript">

function edit(id){
	layer.open({
		  type: 2,
		  title: '修改',
		  shadeClose: true,
		  shade: 0.8,
		  area: ['680px', '400px'],
		  content: '<?php echo functions::get_Config('webCog')['site'] .  'agent.php?b=index&c=user_edit&id=';?>' + id //iframe的url
		}); 
}
</script>
  <?php require ('footer.php');?>
  

