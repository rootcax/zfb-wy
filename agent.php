<?php
require_once './etc/lib/minet.php';
// 设置根目录
define('__ROOT__', dirname(__FILE__));
// 入口参数
$Funtion = array(
    'error' => true, // 错误屏蔽
    'charset' => 'utf-8', // 字符集编码
    'format' => 'text/html', // 网页内容类型
    'session' => true, // session 开启
    'timezone' => 'PRC', // 设置时区
    'buffer' => true, // 是否打开缓冲区
    'url' => 'mvc', // url模式 mvc=传统模式 pathinfo=pathinfo模式
    'modular' => 'agent', // 绑定模块
    'modular_many' => false, // 多模块化开启
    'controller' => 'index', // 绑定控制器
    'controller_many' => true, // 多控制器开启
    'action' => 'login', // 绑定默认执行方法
    'bin' => 'bin' // 绑定环境
);
// 实例化入口
$MI = new MAIN($Funtion);
// 调用入口
$MI->RUN();