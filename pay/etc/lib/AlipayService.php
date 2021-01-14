<?php

class AlipayService {

    protected $appId;
    protected $notifyUrl;
    protected $charset;
    protected $Method;
    //私钥值
    protected $rsaPrivateKey;
    protected $totalFee;
    protected $outTradeNo;
    protected $orderName;
    //红包收款用户ID
    protected $userId;
    //红包收款账号
    protected $account;
    protected $out_order_no;
    protected $out_request_no;
    protected $deduct_auth_no;

    public function __construct() {
        //$this->charset = 'utf8';
    }

    public function setAppid($appid) {
        $this->appId = $appid;
    }

    public function setCharset($charset) {
        $this->charset = $charset;
    }

    public function setNotifyUrl($notifyUrl) {
        $this->notifyUrl = $notifyUrl;
    }

    public function setRsaPrivateKey($rsaPrivateKey) {
        $this->rsaPrivateKey = $rsaPrivateKey;
    }

    public function setTotalFee($payAmount) {
        $this->totalFee = $payAmount;
    }

    public function setOutTradeNo($outTradeNo) {
        $this->outTradeNo = $outTradeNo;
    }

    public function setOrderName($orderName) {
        $this->orderName = $orderName;
    }

    public function setMethod($Method) {
        $this->Method = $Method;
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setAccount($account) {
        $this->account = $account;
    }

    public function setOutOrderNo($out_order_no) {
        $this->out_order_no = $out_order_no;
    }

    public function setOutRequestNo($out_request_no) {
        $this->out_request_no = $out_request_no;
    }

    public function setDeAuthNo($deduct_auth_no) {
        $this->deduct_auth_no = $deduct_auth_no;
    }

    /**
     * 发起订单
     * @return array
     */
    public function doPay() {
        
        //请求参数
        if ($this->Method == "alipay.fund.coupon.order.page.pay") {
            $requestConfigs = array(
                'out_order_no' => $this->outTradeNo,
                'out_request_no' => "19".$this->outTradeNo,
                'order_title' => $this->orderName,
                'amount' => $this->totalFee, //单位 元
                'pay_timeout' => '5m'       //该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
            );
        } else if ($this->Method == "alipay.fund.coupon.order.disburse") {
            $requestConfigs = array(
                'out_order_no' => $this->out_order_no,
               // 'deduct_auth_no' => "19".$this->deduct_auth_no,
                'deduct_auth_no' => $this->deduct_auth_no,
                //'deduct_out_order_no' => $this->out_order_no,
                //'out_request_no' => $this->out_request_no,
                'out_request_no' => "19".$this->out_order_no,
                'order_title' => $this->orderName,
                'amount' => $this->totalFee, //单位 元
                'pay_timeout' => '1h'       //该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
            );
            if($this->userId!="")
            {
                $requestConfigs['payee_user_id'] = $this->userId;
            }
            if($this->account!="")
            {
                $requestConfigs['payee_logon_id'] = $this->account;
            }
            if($this->userId==""&&$this->account=="")
            {
                $requestConfigs['payee_user_id'] = "2088102553349071";
            }
        } else {
            $requestConfigs = array(
                'out_trade_no' => $this->outTradeNo,
                'total_amount' => $this->totalFee, //单位 元
                'subject' => $this->orderName, //订单标题
                'timeout_express' => '2h'       //该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m。
            );
        }


        $commonConfigs = array(
            //公共参数
            'app_id' => $this->appId,
            'method' => $this->Method, //接口名称
            'format' => 'json',
            'charset' => $this->charset,
            'sign_type' => 'RSA2',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'notify_url' => $this->notifyUrl,
            'biz_content' => json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        if ($this->Method == "alipay.fund.coupon.order.page.pay") {
            $postData = http_build_query($commonConfigs);
            $result = $this->curl_get('https://openapi.alipay.com/gateway.do?' . $postData);
            return $result;
        } else {
            $result = $this->curlPost('https://openapi.alipay.com/gateway.do', $commonConfigs);
            return json_decode($result, true);
        }
    }

    public function generateSign($params, $signType = "RSA") {
        return $this->sign($this->getSignContent($params), $signType);
    }

    protected function sign($data, $signType = "RSA") {
        $priKey = $this->rsaPrivateKey;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($priKey, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, version_compare(PHP_VERSION, '5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256); //OPENSSL_ALGO_SHA256是php5.4.8以上版本才支持
        } else {
            openssl_sign($data, $sign, $res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     * */
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;
        return false;
    }

    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }
        unset($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }

    public function curlPost($url = '', $postData = '', $options = array()) {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        //$info  = curl_getinfo( $ch );
        //file_put_contents("info.txt", json_encode($info));
        curl_close($ch);
        return $data;
    }

    //curl
    public function curl_get($url, $data = null) {
        $header = array('Expect:');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $content = curl_exec($ch);
        //$info  = curl_getinfo( $ch );
        curl_close($ch);
        return $content;
    }

}
