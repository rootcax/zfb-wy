<?php require ('function.php');
$csrf = functions::getcsrf(); ?>
<!DOCTYPE HTML>
<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="<?php echo _theme; ?>user/plugins/bootstrap-3.3.0/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="<?php echo _theme; ?>user/plugins/material-design-iconic-font-2.2.0/css/material-design-iconic-font.min.css" rel="stylesheet"/>
        <link href="<?php echo _theme; ?>user/plugins/bootstrap-table-1.11.0/bootstrap-table.min.css" rel="stylesheet"/>
        <link href="<?php echo _theme; ?>user/plugins/waves-0.7.5/waves.min.css" rel="stylesheet"/>
        <link href="<?php echo _theme; ?>user/plugins/jquery-confirm/jquery-confirm.min.css" rel="stylesheet"/>
        <link href="<?php echo _pub . 'jquery-ui-1.12.1/jquery-ui.css' ?>" rel="stylesheet">
        <script src="<?php echo _theme; ?>user/js/jquery-latest.js"></script>
        <script src="<?php echo _theme; ?>user/js/layer/3.0/layer.js"></script>
        <script src="<?php echo _theme; ?>user/plugins/jquery.1.12.4.min.js"></script>
        <style type="text/css">
            body, html {height: 100%; position: relative; font-family: 'Microsoft yahei'; font-size: 13px; font-weight: 400;}
            img {vertical-align: middle;}
            a, a:hover, a:active, a:focus {text-decoration: none; -webkit-user-drag: none; outline: none; color: #000;}
            a i{font-size: 13px;}

            #main{padding: 10px 20px;}

            /* 数据表格 */
            body{font-size: 12px;}
            .table i{font-size: 12px; color: #000;}
            .bootstrap-table .table>thead>tr>th{border-bottom: none;}
            .bootstrap-table .table:not(.table-condensed), .bootstrap-table .table:not(.table-condensed)>tbody>tr>td, .bootstrap-table .table:not(.table-condensed)>tbody>tr>th, .bootstrap-table .table:not(.table-condensed)>tfoot>tr>td, .bootstrap-table .table:not(.table-condensed)>tfoot>tr>th, .bootstrap-table .table:not(.table-condensed)>thead>tr>td{padding: 12px 8px;}
            /* 分页 */
            
            /* bootstrap */
            .jconfirm .jconfirm-box .jconfirm-buttons button{-webkit-border-radius: 0; border-radius: 0;}
            .btn:active{-webkit-box-shadow: none; box-shadow: none;}
            .editbt{
                color:green;
            }
            .singlebt{
                color:#8A2BE2;
            }
            .deletebt{
                color:red;
            }
        </style>
    </head>