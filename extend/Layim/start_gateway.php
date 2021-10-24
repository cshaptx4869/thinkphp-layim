<?php

use GatewayWorker\Gateway;
use Workerman\Worker;

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
$config = require_once 'config.php';

//Gateway进程是暴露给客户端的让其连接的进程。
//所有客户端的请求都是由Gateway接收然后分发给BusinessWorker处理，同样BusinessWorker也会将要发给客户端的响应通过Gateway转发出去。
$gateway = new Gateway($config['gateway']['socket_name']);
$gateway->name = $config['gateway']['name'];
$gateway->count = $config['gateway']['count'];
$gateway->lanIp = $config['gateway']['lan_ip'];
$gateway->startPort = $config['gateway']['start_port'];
$gateway->registerAddress = $config['gateway']['register_address'];
$gateway->pingInterval = $config['gateway']['ping_interval'];
$gateway->pingNotResponseLimit = $config['gateway']['ping_not_response_limit'];
$gateway->pingData = $config['gateway']['ping_data'];

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
