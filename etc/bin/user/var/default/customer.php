<?php require ('header.php'); ?>
<body>
    <div id="main">
        <table class="table">
            <caption>
                <form action="" method="post" style="display:inline-block;">
                    用户ID：<input type="text" value="<?php echo $_REQUEST['id']; ?>" name="id" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    手机号：<input type="text" value="<?php echo $_REQUEST['phone']; ?>" name="phone" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    ip：<input type="text" value="<?php echo $_REQUEST['ip']; ?>" name="ip" style="border: none;outline:none;width: 135px;height: 20px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                    <select name="state" style="height: 31px;border:none;width: 78px;height: 22px;line-height: 21px;padding-left: 0.5px;border: 1px #9c9c9c solid;color: #3b3b3b;">
                        <option value="" <?php echo $_REQUEST['state'] == '' ? 'selected' : ''; ?> >客服状态</option>
                        <option value="0" <?php echo $_REQUEST['state'] == '0' ? 'selected' : ''; ?> >未审核</option>
                        <option value="1" <?php echo $_REQUEST['state'] == '1' ? 'selected' : ''; ?> >正常</option>
                        <option value="2" <?php echo $_REQUEST['state'] == '2' ? 'selected' : ''; ?> >已冻结</option>
                    </select>
                    <input type="submit" name="btn" value="查询" style="border: none;outline:none;width: 88px;height: 22px;line-height: 20px;border: 1px #9c9c9c solid;padding-left: 5px;color: #3b3b3b;" />
                </form>
            </caption>
            <thead>
                <tr>
                    <th><input type="checkbox" id="all"></th>
                    <th>ID</th>
                    <th>用户名</th>
                    <th>ip</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="list">
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td><input type="checkbox" name="ckbox" value="<?php echo $x['id']; ?>"></td>
                        <td><?php echo $x['id']; ?></td>
                        <td><?php echo $x['phone']; ?></td>
                        <td><?php echo $x['ip']; ?></td>
                        <td <?php if ($x['status'] == 0) { ?>style='color: #985f0d;font-weight:bold;'<?php } else if ($x['status'] == 1) { ?>style='color: green;font-weight:bold;'<?php } else { ?>style='color: red;font-weight:bold;'<?php } ?>><?php
                            if ($x['status'] == 0) {
                                echo '未审核';
                            } else if ($x['status'] == 1) {
                                echo '正常';
                            } else {
                                echo '已冻结';
                            }
                            ?></td>
                        <td><a href="#" style="color: green;" onclick="edit(<?php echo $x['id']; ?>);">修改</a> / <a href="#" style="color: red;" onclick="del(<?php echo $x['id']; ?>);">删除</a></td>
                    </tr>
                <?php } ?>
            </tbody>


        </table>
        <ul class="pagination" style="margin-top: 0px;">
            <?php functions::drive('page')->new_auto($data['info']['page'], $data['info']['current'], 10); ?>
            <li><a class="waves-effect waves-button" href="javascript:;" onclick="del()" style="display:none;" id="delbtn">删除选中</a></li>
        </ul>

    </div>
    <script type="text/javascript">
        function del(id) {
            var r = confirm("你真的要删除该用户,包括他的一切数据?")
            if (r == true)
            {
                location.href = "<?php echo functions::getdomain() . 'index.php?b=action&c=customer_del&id='; ?>" + id;
            }
        }

        function edit(id) {
            layer.open({
                type: 2,
                title: '修改',
                shadeClose: true,
                shade: 0.8,
                area: ['680px', '490px'],
                content: '<?php echo functions::getdomain() . 'index.php?b=index&c=customer_edit&id='; ?>' + id //iframe的url
            });
        }
    </script>

    <?php require ('footer.php'); ?>