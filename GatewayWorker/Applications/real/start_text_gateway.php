<?php

/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \Workerman\WebServer;
use \GatewayWorker\Gateway;
use \GatewayWorker\BusinessWorker;
use \Workerman\Autoloader;

// 自动加载类
require_once __DIR__ . '/../../vendor/autoload.php';

// #### 内部推送端口(假设当前服务器内网ip为本机) ####
$internal_gateway = new Gateway("Text://127.0.0.1:8806");
$internal_gateway->name = 'internalGateway';
$internal_gateway->startPort = 2800;
// 端口为start_register.php中监听的端口，websocket推送默认是1238
$internal_gateway->registerAddress = '127.0.0.1:1238';
// #### 内部推送端口设置完毕 ####


if (!defined('GLOBAL_START')) {
    Worker::runAll();
}

