<?php require ('header.php'); ?>
<body>
    <div id="main">
        <table class="table">
            <tbody id="list">
                <?php foreach ($data as $x) { ?>
                    <tr>
                        <td><a onclick="view(<?php echo $x['id'];?>)"><?php echo $x['title']; ?></a></td>
                        <td><?php echo date("Y/m/d H:i:s", $x['timec']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>


        </table>




    </div>


    <script>

        function view(id) {
            layer.open({
                type: 2,
                title: '查看',
                shadeClose: true,
                shade: 0.8,
                area: ['100%', '100%'],
                content: '<?php echo functions::urlc('agent', 'index', 'news_view', array('id' => '')); ?>' + id //iframe的url
            });
        }
    </script>

    <?php require ('footer.php'); ?>