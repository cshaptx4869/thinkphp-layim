<?php

use Workerman\Worker;
use GatewayWorker\Register;

// 自动加载类
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/config.php';

// register 必须是text协议
// 客户端不要连接 Register 服务的端口，Register 服务是 GatewayWorker内部通讯用的。且不支持Gateway接口(包括GatewayClient接口)，不要在register进程写任何业务
// register 服务只能开一个进程，不能开启多个进程
$register = new Register($config['register']['socket_name']);
// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
