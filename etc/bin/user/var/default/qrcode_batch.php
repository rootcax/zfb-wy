<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script type="text/javascript" src="<?php echo _theme; ?>upload/jquery-1.7.2.js"></script>
        <link rel="stylesheet" href="<?php echo _theme; ?>upload/skins/zyupload-1.0.0.min.css " type="text/css">
        <script type="text/javascript" src="<?php echo _theme; ?>upload/zyupload.tailor-1.0.0.min.js"></script>
    </head>
    <body>
        <div id="zyupload" class="zyupload"></div> 
        <script type="text/javascript">
            $(function () {
                // 初始化插件
                $("#zyupload").zyUpload({
                    width: "100%", // 宽度
                    height: "400px", // 宽度
                    itemWidth: "140px", // 文件项的宽度
                    itemHeight: "115px", // 文件项的高度
                    url: "<?php echo functions::urlc("user", "action", "qrcode_batch", array("land_id" => intval($_GET['land_id']))); ?>", // 上传文件的路径
                    fileType: ["jpg", "png", "jpeg"], // 上传文件的类型
                    fileSize: 51200000, // 上传文件的大小
                    multiple: true, // 是否可以多个文件上传
                    dragDrop: true, // 是否可以拖动上传文件
                    tailor: true, // 是否可以裁剪图片
                    del: true, // 是否可以删除文件
                    finishDel: false, // 是否在上传文件完成后删除预览
                    /* 外部获得的回调接口 */
                    onSelect: function (selectFiles, allFiles) {    // 选择文件的回调方法  selectFile:当前选中的文件  allFiles:还没上传的全部文件
                        console.info("当前选择了以下文件：");
                        console.info(selectFiles);
                    },
                    onDelete: function (file, files) {              // 删除一个文件的回调方法 file:当前删除的文件  files:删除之后的文件
                        console.info("当前删除了此文件：");
                        console.info(file.name);
                    },
                    onSuccess: function (file, response) {          // 文件上传成功的回调方法
                        console.info("此文件上传成功：");
                        console.info(file.name);
                        console.info(response);
                        $("#uploadInf").append("<p>" + response + "</p>");
                    },
                    onFailure: function (file, response) {          // 文件上传失败的回调方法
                        console.info("此文件上传失败：");
                        console.info(file.name);
                    },
                    onComplete: function (response) {           	  // 上传完成的回调方法
                        console.info("文件上传完成");
                        console.info(response);
                    }
                });

            });

        </script> 
    </body>
</html>
