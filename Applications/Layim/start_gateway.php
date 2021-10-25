<?php

use GatewayWorker\Gateway;
use Workerman\Worker;

// 自动加载类
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/config.php';

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
// 当客户端连接上来时，设置连接的onWebSocketConnect，即在websocket握手时的回调
//$gateway->onConnect = function ($connection) {
//    $connection->onWebSocketConnect = function ($connection, $httpHeader) {
//        // 可以在这里判断连接来源是否合法，不合法就关掉连接
//        // $_SERVER['HTTP_ORIGIN']标识来自哪个站点的页面发起的websocket链接
//        if ($_SERVER['HTTP_ORIGIN'] != 'http://layim.test') {
//            $connection->close();
//        }
//        // onWebSocketConnect 里面$_GET $_SERVER是可用的
//        // var_dump($_GET, $_SERVER);
//    };
//};

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
