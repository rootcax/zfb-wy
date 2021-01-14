<?php require ('header.php'); ?>
<body>
    <div id="main">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>收款账号</th>
                    <th>整额</th>
                    <th>真实金额</th>
                    <th>二维码</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="list">
                <?php foreach ($data['query'] as $x) { ?>
                    <tr>
                        <td><?php echo $x['id']; ?></td>
                        <td><?php $row = functions::open_mysql()->query('land', 'id=' . $x['land_id']);
                echo $row[0]['username'] . '(' . M::payc($x['typec']) . ')'; ?></td>
                        <td><?php echo $x['money_res'] != 0 ? '<span style="color:green;">' . $x['money_res'] . '</span>' : '通用'; ?></td>
                        <td><?php echo $x['money'] != 0 ? '<span style="color:red;">' . $x['money'] . '</span>' : '通用'; ?></td>
                        <td><?php if (!empty($x['qrcode'])) {
                    echo '已上传';
                } else {
                    echo '未上传';
                }; ?></td>
                        <td><?php echo $x['state'] == 1 ? '<span style="color:green;">空闲中</span>' : '<span style="color:red;">正在扫码</span>'; ?></td>
                        <td><a href="#" class="editbt" onclick="edit(<?php echo $x['id']; ?>);">修改</a> / <a href="#" class="deletebt" onclick="del(<?php echo $x['id']; ?>);">删除</a></td>
                    </tr>
            <?php } ?>
            </tbody>


        </table>
        <ul class="pagination" style="margin-top: 0px;">
<?php functions::drive('page')->auto($data['info']['page'], $data['info']['current'], 10); ?>
            <li><select style="height: 31px;border:none;margin-left:10px;" onchange="javascript:location.href = this.value;">
                    <option value="<?php echo functions::urlc('user', 'index', 'qrcode'); ?>" <?php echo $_GET['payc'] == '' ? 'selected' : ''; ?> >全部</option>
                    <option value="<?php echo functions::urlc('user', 'index', 'qrcode', array('payc' => 1)); ?>" <?php echo $_GET['payc'] == '1' ? 'selected' : ''; ?> >支付宝</option>
                    <option value="<?php echo functions::urlc('user', 'index', 'qrcode', array('payc' => 2)); ?>" <?php echo $_GET['payc'] == '2' ? 'selected' : ''; ?> >微信</option>
                    <option value="<?php echo functions::urlc('user', 'index', 'qrcode', array('payc' => 2)); ?>" <?php echo $_GET['payc'] == '7' ? 'selected' : ''; ?> >星POS</option>

                </select></li>
        </ul>

    </div>
    <script type="text/javascript">
        function edit(id) {
            layer.open({
                type: 2,
                title: '修改',
                shadeClose: true,
                shade: 0.8,
                area: ['100%', '100%'],
                content: '<?php echo functions::urlc('user', 'index', 'qrcode_edit', array('id' => '')); ?>' + id //iframe的url
            });
        }
        function del(id) {
            layer.confirm('你是真的要删除该二维码么?删除无法恢复!', {
                btn: ['确认删除', '取消'] //按钮
            }, function () {
                location.href = "<?php echo functions::urlc('user', 'action', 'qrcode_del', array('id' => '')) ?>" + id;
            });
        }
    </script>

<?php require ('footer.php'); ?>