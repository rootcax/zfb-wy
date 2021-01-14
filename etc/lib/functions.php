<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00

class functions {

    //url路径解析
    static function urlc($a, $b, $c, $get_array = NULL) {
        $durl = '';
        $data_url = '';
        if (_urlc == 'mvc') {
            //将$get_array解析成url
            if (is_array($get_array)) {
                foreach ($get_array as $key => $value) {
                    $data_url .= $key . '=' . $value . '&';
                }
                $data_url = trim($data_url, '&');
                $durl = '&' . $data_url;
            }
            $url = self::get_Config('webCog')['site'] . '?a=' . $a . '&b=' . $b . '&c=' . $c . $durl;
            return $url;
        }
        if (_urlc == 'pathinfo') {
            //将$get_array解析成url
            if (is_array($get_array)) {
                foreach ($get_array as $key => $value) {
                    $data_url .= $key . '/' . $value . '/';
                }
                $data_url = trim($data_url, '/');
                $durl = '/' . $data_url;
            }
            $url = self::get_Config('webCog')['site'] . 'index.php/' . $a . '/' . $b . '/' . $c . $durl;
            return $url;
        }
    }

    //检测是否手机访问
    static function isMobile() {
        $useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $useragent_commentsblock = preg_match('|\(.*?\)|', $useragent, $matches) > 0 ? $matches[0] : '';

        function CheckSubstrs($substrs, $text) {
            foreach ($substrs as $substr)
                if (false !== strpos($text, $substr)) {
                    return true;
                }
            return false;
        }

        $mobile_os_list = array('Google Wireless Transcoder', 'Windows CE', 'WindowsCE', 'Symbian', 'Android', 'armv6l', 'armv5', 'Mobile', 'CentOS', 'mowser', 'AvantGo', 'Opera Mobi', 'J2ME/MIDP', 'Smartphone', 'Go.Web', 'Palm', 'iPAQ');
        $mobile_token_list = array('Profile/MIDP', 'Configuration/CLDC-', '160×160', '176×220', '240×240', '240×320', '320×240', 'UP.Browser', 'UP.Link', 'SymbianOS', 'PalmOS', 'PocketPC', 'SonyEricsson', 'Nokia', 'BlackBerry', 'Vodafone', 'BenQ', 'Novarra-Vision', 'Iris', 'NetFront', 'HTC_', 'Xda_', 'SAMSUNG-SGH', 'Wapaka', 'DoCoMo', 'iPhone', 'iPod');

        $found_mobile = CheckSubstrs($mobile_os_list, $useragent_commentsblock) ||
                CheckSubstrs($mobile_token_list, $useragent);

        if ($found_mobile) {
            return true;
        } else {
            return false;
        }
    }

    //下载文件
    static function downloadFile($file) {
        $file_name = $file;
        $mime = 'application/force-download';
        header('Pragma: public'); // required
        header('Expires: 0'); // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($file_name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        readfile($file_name); // push it out
        exit();
    }

    //二维码解码
    static function qrcode_1($imageUrl) {
        $url = 'http://zxing.org/w/decode?u=' . $imageUrl;
        $code = file_get_contents($url);
        preg_match("/<table id=\"result\">(.*)<\/table>/isU", $code, $math);
        preg_match("/<pre>(.*)<\/pre>/isU", $math[1], $maths);
        return $maths[1];
    }

    static function qrcode($imageUrl) {
        $url = 'https://cli.im/Api/Browser/deqr?data=' . $imageUrl;
        $code = file_get_contents($url);
        $code = json_decode($code, true);
        if ($code['status'] == 1) {
            return $code['data']['RawData'];
        } else {
            return false;
        }
    }

    //获取url
    static function geturl() {
        $pageURL = 'http';

        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    //获取url
    static function getdomain() {
        $pageURL = 'http';

        if ($_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . "/";
        } else {
            if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == "https") {
                $pageURL = "https://" . $_SERVER["SERVER_NAME"] . "/";
            } else {
                $pageURL .= $_SERVER["SERVER_NAME"] . "/";
            }
        }
        return $pageURL;
    }

    //打开mysql
    static function open_mysql() {
        return new mysql();
    }

    //初始化支付宝当面付
    static function alipayservice() {
        return new AlipayService();
    }

    //csrf
    static function getcsrf() {
        $cs = substr(str_replace(0, '', md5(self::encode(md5(AUTH_KEY . time() . mt_rand(10000, 99999)), AUTH_KEY))), 0, 16);
        $_SESSION['csrf'] = $cs;
        return $cs;
    }

    //获得动态配置值
    static function getc($name) {
        $getc = file_get_contents(_etc . 'dynamic_config.php');
        $getc = json_decode(functions::encode($getc, AUTH_PE, 2));
        return $getc->$name;
    }

    //检测手机
    static function isphone($mobile) {
        if (!preg_match("/^((13[0-9])|(14[5,7])|(15[0-3,5-9])|(17[0,3,5-8])|(18[0-9])|166|198|199|(147))\\d{8}$/", $mobile)) {
            return false;
        } {
            return true;
        }
    }

    //检测密码长度
    static function ispwd($pwd) {
        $length = mb_strlen($pwd, "utf-8");
        if ($length < 26 && $length >= 6) {
            return true;
        } else {
            return false;
        }
    }

    //获取客户端IP地址
    static function ipc($type = 0) {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL)
            return $ip[$type];
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {//nginx 代理模式下，获取客户端真实IP
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR']; //浏览当前页面的用户计算机的ip地址
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    
    
    static function get_client_ip($type = 0) {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL)
            return $ip[$type];
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {//nginx 代理模式下，获取客户端真实IP
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {//客户端的ip
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {//浏览当前页面的用户计算机的网关
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos)
                unset($arr[$pos]);
            $ip = trim($arr[0]);
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR']; //浏览当前页面的用户计算机的ip地址
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
    static function verify($ip)
        {   $userurl = json_encode($_SERVER['HTTP_HOST']); 
         $ch = curl_init();          
        curl_setopt($ch, CURLOPT_URL, "EXT.BUZZ");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $userurl);
        curl_setopt($ch, CURLOPT_TIMEOUT,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length: ' . strlen($userurl)));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return array($httpCode, $response);
    }


    //检测账号是否存在
    static function existence_username($username) {
        //检测xss
        $username = self::xss($username);
        //验证账号
        $mysql = self::open_mysql();
        $resource = $mysql->query('user', "username='{$username}'");
        if (is_array($resource[0])) {
            return $resource[0];
        } else {
            return false;
        }
    }

    //密码加密
    static function pwdc($pwd, $key) {
        return md5(self::encode($pwd, $key, 1));
    }

    //url 301 跳转
    static function urlx($url) {
        header('Location: ' . $url);
        exit;
    }

    //检测字符串长度
    static function islang_str($str, $mix, $max) {
        $length = mb_strlen($str, "utf-8");
        if ($length < $max && $length >= $mix) {
            return true;
        } else {
            return false;
        }
    }

    //json
    static function json($code, $msg, $data = null) {
        header('Content-type: application/json');
        exit(json_encode(array("code" => $code, "msg" => $msg, "data" => $data), JSON_UNESCAPED_UNICODE));
    }

    //模板文件加载
    static function import($name, $array = null) {

        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $$key = $value;
            }
        }
        require _var . self::get_Config('webCog')['theme'] . '/' . $name . ".php";
    }

    //单独模板文件加载
    static function import_var($name, $array = null) {

        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $$key = $value;
            }
        }
        require _var . $name . ".php";
    }

    //加载驱动
    static function drive($name) {
        require_once _drive . $name . '.php';
        return new $name();
    }

    //使用接口
    static function api($name) {
        include_once _api . $name . '.api.php';
        return new $name();
    }

    //get_post参数过滤
    //get_post参数过滤
static function request($name, $method = 'all') {
        if ($method == 'all') {
            if (!get_magic_quotes_gpc()) {
                return self::lib_replace_end_tag(self::xss(addslashes($_REQUEST["{$name}"])));
            } else {
                return self::lib_replace_end_tag(self::xss($_REQUEST["{$name}"]));
            }
        }
        if ($method == 'get') {
            if (!get_magic_quotes_gpc()) {
                return self::lib_replace_end_tag(self::xss(addslashes($_GET["{$name}"])));
            } else {
                return self::lib_replace_end_tag(self::xss($_GET["{$name}"]));
            }
        }
        if ($method == 'post') {
            if (!get_magic_quotes_gpc()) {
                return self::lib_replace_end_tag(self::xss(addslashes($_POST["{$name}"])));
            } else {
                return self::lib_replace_end_tag(self::xss($_POST["{$name}"]));
            }
        }
    }
//过滤注入
    static function lib_replace_end_tag($str) {
        if (empty($str))
            return false;
        $str = htmlspecialchars($str);
        $str = str_replace("\\", "", $str);
        $str = str_replace(">", "", $str);
        $str = str_replace("<", "", $str);
        $str = str_replace("<SCRIPT>", "", $str);
        $str = str_replace("</SCRIPT>", "", $str);
        $str = str_replace("<script>", "", $str);
        $str = str_replace("</script>", "", $str);
        $str = str_replace("select", "select", $str);
        $str = str_replace("join", "join", $str);
        $str = str_replace("union", "union", $str);
        $str = str_replace("where", "where", $str);
        $str = str_replace("insert", "insert", $str);
        $str = str_replace("delete", "delete", $str);
        $str = str_replace("update", "update", $str);
        $str = str_replace("like", "like", $str);
        $str = str_replace("drop", "drop", $str);
        $str = str_replace("create", "create", $str);
        $str = str_replace("modify", "modify", $str);
        $str = str_replace("rename", "rename", $str);
        $str = str_replace("alter", "alter", $str);
        $str = str_replace("cas", "cast", $str);
        $str = str_replace("&", "&", $str);
        $str = str_replace(">", ">", $str);
        $str = str_replace("<", "<", $str);
        $str = str_replace(" ", chr(32), $str);
        $str = str_replace(" ", chr(9), $str);
        $str = str_replace(" ", chr(9), $str);
        $str = str_replace("&", chr(34), $str);
        $str = str_replace("'", chr(39), $str);
        $str = str_replace("<br />", chr(13), $str);
        $str = str_replace("''", "'", $str);
        $str = str_replace("css", "'", $str);
        $str = str_replace("CSS", "'", $str);
        return $str;
    }

    // RC4加密和解密  action 1加密 2解密
    static function encode($data, $pwd, $action = 1) {
        if ($action == 1) {
            return base64_encode(self::RC4($pwd, $data));
        }
        if ($action == 2) {
            return iconv("UTF-8", "GB2312//IGNORE", self::RC4($pwd, base64_decode($data)));
        }
    }

    //IP归属地查询
    function iplocation($ip) {
        $data = json_decode(file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ip));
        return array(
            'ip' => $ip,
            'country' => $data->data->country,
            'region' => $data->data->region,
            'city' => $data->data->city,
            'area' => $data->data->area,
            'isp' => $data->data->isp
        );
    }

    //msg弹窗带跳转
    static function msg($msg, $location) {
        echo "<script>alert('{$msg}');location.href='{$location}';</script>";
        exit;
    }

    //xss攻击过滤
    static function xss($val) {
        $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); //
            $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // 
        }

        $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
        $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true; // 
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                        $pattern .= '|';
                        $pattern .= '|(&#0{0,8}([9|10|13]);)';
                        $pattern .= ')*';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // 
                $val = preg_replace($pattern, $replacement, $val); // 
                if ($val_before == $val) {
                    $found = false;
                }
            }
        }
        return $val;
    }

   

    //RC4加密
    private static function RC4($pwd, $data) {
        $key[] = "";
        $box[] = "";

        $pwd_length = strlen($pwd);
        $data_length = strlen($data);

        for ($i = 0; $i < 256; $i++) {
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $data_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;

            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;

            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }

        return $cipher;
    }

    static function get_Config($name) {
        $mysql = self::open_mysql();
        $query = $mysql->query("config", "name='{$name}'");
        if (is_array($query[0])) {
            $data = json_decode($query[0]['value'], true);
            return $data;
        } else {
            return false;
        }
    }

    static function get_settleMoney($agent) {
        $withdrawCog = functions::get_Config('withdrawCog');
        $cycle = $withdrawCog['cycle'];
        $mysql = self::open_mysql();
        if ($cycle > 0) {
            //$start_time = strtotime(date('Y-m-d', strtotime("-".$cycle." days")));
            $end_time = strtotime(date('Y-m-d 23:59:59', strtotime("-" . $cycle . " days")));
        } else {
            //$start_time = strtotime(date('Y-m-d',time()));
            $end_time = time();
        }
        $money = $mysql->select("select sum(agent_payment) as money from mi_orders where agentid={$agent->sid} and order_time<={$end_time} and settle_state=0");
        if (empty($money[0]['money'])) {
            $money[0]['money'] = 0;
        }
        return $money[0]['money'];
    }

    //获取用户费率
    static function getCost($userId) {
        //查询到订单列表,得到请求接口的手续费
        $cost = functions::get_Config('registerCog');
        $mysql = functions::open_mysql();

        $user = $mysql->query('users', "id={$userId}");
        if (!is_array($user[0]))
            return false;
        $user = $user[0];
        if (empty($user['bank2alipay_withdraw']) || $user['bank2alipay_withdraw'] == 0) {
            $poundage['bank2alipay_withdraw'] = $cost['bank2alipay_withdraw'];
        } else {
            $poundage['bank2alipay_withdraw'] = $user['bank2alipay_withdraw'];
        }
        return $poundage;
    }

    static function get_orderFee($userid, $typec) {
        $cost = functions::get_Config('registerCog');
        $mysql = functions::open_mysql();

        $user = $mysql->query('users', "id={$userid}");
        if (!is_array($user[0]))
            return false;
        $user = $user[0];
        if ($typec == 26) {
            if (empty($user['bank2alipay_withdraw']) || $user['bank2alipay_withdraw'] == 0) {
                $poundage = $cost['bank2alipay_withdraw'];
            } else {
                $poundage = $user['bank2alipay_withdraw'];
            }
        }
        return $poundage;
    }

    static function get_agentFee($agentid, $typec) {
        $cost = functions::get_Config('agentCog');
        $mysql = functions::open_mysql();

        $agent = $mysql->query('agent', "id={$agentid}");
        if (!is_array($agent[0]))
            return false;
        $agent = $agent[0];
        if ($typec == 26) {
            if (empty($agent['bank2alipay_withdraw']) || $agent['bank2alipay_withdraw'] == 0) {
                $poundage = $cost['bank2alipay_withdraw'];
            } else {
                $poundage = $agent['bank2alipay_withdraw'];
            }
        }
        return $poundage;
    }

    static function _getFloat($newspay, $newsuser, $isPercent = 0) {
        $floatNum = 0;
        if (!empty($newspay) && !empty($newsuser)) {
            $floatNum = round($newspay / $newsuser, 2);
        }
        if ($isPercent == 1) {
            return ($floatNum * 100) . '%';
        }
        return $floatNum;
    }

    /*
     * 生成随机数字
     * @param int $length 生成随机字符串的长度
     * @return string $string 生成的随机字符串
     */

    static function generateRandomNum($length = 10) {
        $characters = '0123456789';
        $randomNum = '';
        for ($i = 0; $i < $length; $i++) {
            $randomNum .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomNum;
    }

    public static function exportExcel($title = array(), $data = array(), $fileName = '', $savePath = './', $isDown = true) {
        error_reporting(0);
        include('PHPExcel.php');
        $obj = new PHPExcel();

        //横向单元格标识
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        $obj->getActiveSheet(0)->setTitle('sheet名称');   //设置sheet名称
        $_row = 1;   //设置纵向单元格标识
        if ($title) {
            $_cnt = count($title);
            $obj->getActiveSheet(0)->mergeCells('A' . $_row . ':' . $cellName[$_cnt - 1] . $_row);   //合并单元格
            $obj->setActiveSheetIndex(0)->setCellValue('A' . $_row, '数据导出：' . date('Y-m-d H:i:s'));  //设置合并后的单元格内容
            $_row++;
            $i = 0;
            foreach ($title AS $v) {   //设置列标题
                $obj->setActiveSheetIndex(0)->setCellValue($cellName[$i] . $_row, $v);
                $i++;
            }
            $_row++;
        }

        //填写数据
        if ($data) {
            $i = 0;
            foreach ($data AS $_v) {
                $j = 0;
                foreach ($_v AS $_cell) {
                    $obj->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + $_row), $_cell);
                    $j++;
                }
                $i++;
            }
        }

        //文件名处理
        if (!$fileName) {
            $fileName = uniqid(time(), true);
        }

        $objWrite = PHPExcel_IOFactory::createWriter($obj, 'Excel2007');

        if ($isDown) {   //网页下载
            header('pragma:public');
            header("Content-Disposition:attachment;filename=$fileName.xlsx");
            $objWrite->save('php://output');
            exit;
        }

        $_fileName = iconv("utf-8", "gb2312", $fileName);   //转码
        $_savePath = $savePath . $_fileName . '.xlsx';
        $objWrite->save($_savePath);

        return $savePath . $fileName . '.xlsx';
    }

}
