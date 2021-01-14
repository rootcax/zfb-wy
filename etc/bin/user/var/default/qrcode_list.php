<?php require ('header.php'); ?>
<body>
    <div id="main">
        <table class="table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="all"></th>
                    <th>价格</th>
                    <th>收款码数量/已使用数量</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody id="list">
                <?php foreach ($qrcode as $x) { ?>
                    <tr>
                        <td><input type="checkbox" name="ckbox" value="<?php echo floatval($x['money_res']); ?>"></td>
                        <td><?php echo $x['money_res']; ?></td>
                        <td><?php echo $x['count']; ?> / <?php echo empty($x['used']) ? '0' : $x['used']; ?></td>
                        <td><?php if ($x['used'] >= $x['count']) {
                    echo '已全部使用';
                } else {
                    echo '正常';
                }; ?></td>
                        <td><a href="#" class="deletebt" onclick="del(<?php echo $x['money_res']; ?>,<?php echo functions::request('id') ?>);">删除</a></td>
                    </tr>
<?php } ?>
            </tbody>


        </table>
        <ul class="pagination" style="margin-top: 0px;">
<?php functions::drive('page')->auto($data['page'], $data['current'], 10); ?>
            <li><a class="waves-effect waves-button" href="javascript:;" onclick="del_all(<?php echo functions::request('id') ?>)" style="display:none;" id="delbtn">删除选中</a></li>
        </ul>
    </div>
    <script type="text/javascript">

        function del(id, landid) {
            layer.confirm('你是真的要删除该价格所有收款码么?删除无法恢复!', {
                btn: ['确认删除', '取消'] //按钮
            }, function () {
                location.href = "<?php echo functions::get_Config('webCog')['site']; ?>?a=user&b=action&c=qrcode_link_del&landid=" + landid + "&id=" + id;

            });
        }

        function del_all(landid) {
            var chk_value = [];
            $('input[name="ckbox"]:checked').each(function () {
                chk_value.push($(this).val());
            });

            layer.confirm('你是真的要删除这些订单信息?', {
                btn: ['确认删除', '取消'] //按钮
            }, function () {
                location.href = "<?php echo functions::get_Config('webCog')['site']; ?>?a=user&b=action&c=qrcode_link_ch_del&landid=" + landid + "&id=" + chk_value;
            });
        }

    //选择框操作
        $("#all").click(function () {
            if (this.checked) {
                $("#list :checkbox").prop("checked", true);
                $("#delbtn").show();
            } else {
                $("#list :checkbox").prop("checked", false);
                $("#delbtn").hide();
            }
        });

        $("input[name='ckbox']").click(function () {
            var chk_value = [];
            $('input[name="ckbox"]:checked').each(function () {
                chk_value.push($(this).val());
            });

            if (chk_value.length != 0) {
                $("#delbtn").show();
            } else {
                $("#delbtn").hide();
            }
        });
    </script>

<?php require ('footer.php'); ?>