<?php
//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
//短信验证码接口
//采用的阿里云短信平台
class sms{
    //发送验证码
    function send($phone,$code){
        //载入阿里云官方的sdk
        require ('signaturehelper.private.php');
        
        $params = array ();
        
        // *** 需用户填写部分 ***
        
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = functions::get_Config('smsCog')['accessKeyId'];
        $accessKeySecret = functions::get_Config('smsCog')['accessKeySecret'];
        
        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $phone;
        
        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = functions::get_Config('smsCog')['SignName'];
        
        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = functions::get_Config('smsCog')['TemplateCode'];
        
        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array ("code" => $code);

        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"]);
        }
        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new signaturehelper();
        
        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
            );
        return $content;
    }
    
    //发送异常通知
    function send_abnormal($phone,$username){
        //载入阿里云官方的sdk
        require ('signaturehelper.private.php');
        $params = array ();
        $accessKeyId = functions::get_Config('smsCog')['accessKeyId'];
        $accessKeySecret = functions::get_Config('smsCog')['accessKeySecret'];
        $params["PhoneNumbers"] = $phone;
        $params["SignName"] = functions::get_Config('smsCog')['SignName'];
        $params["TemplateCode"] = functions::get_Config('smsCog')['Abnormal'];
        $params['TemplateParam'] = array(
            "name"=>$username,
            "time"=>date("Y/m/d H:i:s",time())
        );
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"]);
        }
        $helper = new signaturehelper();
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
            );
        return $content;
    }
    
}