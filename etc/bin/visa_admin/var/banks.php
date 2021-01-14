<?php require ('header.php'); ?>
<div>
    <!-- 内容主体区域 -->
    <div style="padding: 15px;">
        <table class="layui-table" lay-even="" lay-skin="nob">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>银行名称</th>
                    <th>银行ID</th>
                    <th>银行代码</th>
                    <th>银行token</th>
                    <th>银行logo</th>
                    <th>银行类型</th>
                    <th>操作</th>
                </tr> 
            </thead>
            <tbody>
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td><?php echo $x['id']; ?></td>
                        <td><?php echo $x['bank_name']; ?></td>
                        <td><?php echo $x['bank_id']; ?></td>
                        <td><?php echo $x['bank_code']; ?></td>
                        <td><?php echo $x['bank_token']; ?></td>
                        <td><img src="<?php echo $x['bank_logo']; ?>"</td>
                        <td><?php echo M::bank_type($x['bank_type']); ?></td>
                        <td><a href="#" style="color: green;" onclick="add(<?php echo $x['id']; ?>);">添加额度</a> / <a href="#" style="color: red;" onclick="del(<?php echo $x['id']; ?>);">删除</a></td>
                    </tr>
                </tbody>
            <?php } ?>
        </table> 
        <div class="layui-table-page">
            <div id="layui-table-page1">
                <div class="layui-box layui-laypage layui-laypage-default" id="layui-laypage-3">
                    <?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 20); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function del(id) {
        var r = confirm("你真的要删除吗?这将是无法恢复的!")
        if (r == true)
        {
            location.href = "/visa_admin.php?b=action&c=takes_del&id=" + id;
        }
    }
    function add(id) {
        layer.open({
            type: 2,
            title: '添加额度信息',
            shadeClose: true,
            shade: 0.8,
            area: ['680px', '440px'],
            content: '/visa_admin.php?b=index&c=bank_memo_add&id=' + id //iframe的url
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
  

