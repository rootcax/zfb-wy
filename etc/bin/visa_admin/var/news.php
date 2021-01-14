<?php require ('header.php'); ?>
<div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
        <table class="layui-table" lay-even="" lay-skin="nob">
            <caption>
                <form action="" method="post" style="display:inline-block;">
                    标题：<input type="text" value="<?php echo $_REQUEST['title']; ?>" name="title" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" /> 开始时间：<input type="text" id="start_time" name="start_time" value="<?php echo $_REQUEST['start_time'] != '' ? $_REQUEST['start_time'] : date('Y-m-d 00:00:00'); ?>"> 结束时间： <input type="text" id="end_time" name="end_time" value="<?php echo $_REQUEST['end_time'] != '' ? $_REQUEST['end_time'] : date("Y-m-d 23:59:59"); ?>">
                    <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                </form>
            </caption>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>标题</th>
                    <th>发布时间</th>
                    <th>操作</th>
                </tr> 
            </thead>
            <tbody>
                <?php foreach ($data['query'] as $minet) { ?>
                    <tr>
                        <td><?php echo $minet['id']; ?></td>
                        <td><?php echo $minet['title']; ?></td>
                        <td><?php echo date('Y/m/d H:i:s', $minet['timec']); ?></td>
                        <td><a href="#" style="color: green;" onclick="edit(<?php echo $minet['id']; ?>);">修改</a> / <a href="#" style="color: red;" onclick="del(<?php echo $minet['id']; ?>);">删除</a></td>

                    </tr>
                </tbody>
            <?php } ?>
        </table> 
        <div class="layui-table-page"><div id="layui-table-page1"><div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">

                    <?php functions::drive('page')->auto($data['info']['page'], $data['info']['current'], 10); ?>
                </div></div></div>
    </div>
</div>
<script type="text/javascript">
    function del(id) {
        var r = confirm("你真的要删除该用户,包括他的一切数据?")
        if (r == true)
        {
            location.href = "<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=action&c=news_del&id='; ?>" + id;
        }
    }

    function edit(id) {
        layer.open({
            type: 2,
            title: '修改',
            shadeClose: true,
            shade: 0.8,
            area: ['100%', '100%'],
            content: '<?php echo functions::get_Config('webCog')['site'] . 'visa_admin.php?b=index&c=news_edit&id='; ?>' + id //iframe的url
        });
    }
</script>
<script>
    $(function () {
        $("#start_time").datepicker({dateFormat: 'yy-mm-dd 00:00:00',
            dayNamesMin: ['日', '一', '二', '三', '四', '五', '六'],
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']

        });
    });
    $(function () {
        $("#end_time").datepicker({dateFormat: 'yy-mm-dd 23:59:59',
            dayNamesMin: ['日', '一', '二', '三', '四', '五', '六'],
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月']
        });
    });
</script>
<?php require ('footer.php'); ?>
  

