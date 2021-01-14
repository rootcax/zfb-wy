<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
        <meta name="referrer" content="never">
        <title><?php echo $data['bank_name'] ?>网银支付</title>
        <script src="<?php echo _pub?>js/admin.js">
        </script>
    </head>
    <script type='text/javascript'>
        var bankCode = "<?php echo $data['bank_code'] ?>";
        function sub() {
            if (bankCode == "CMB" && browser.versions.mobile) {
                window.location.href = '<?php echo $data['h5_link'] ?>';
            } else {
                document.forms['myForm'].submit();
            }
        }
    </script>
    <body onload='sub()'>
        <form name="myForm" method="POST" action="<?php echo $data['post_url'] ?>">
            <input type="hidden" name="epccGwMsg" value="<?php echo $data['qrcode'] ?>" />
        </form>
    </body>
</html>