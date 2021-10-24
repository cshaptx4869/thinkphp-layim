<?php

use GatewayWorker\BusinessWorker;
use Workerman\Worker;

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
$config = require_once 'config.php';

//BusinessWorker是运行业务逻辑的进程。
//BusinessWorker收到Gateway转发来的事件及请求时会默认调用Events.php中的onConnect onMessage onClose方法处理事件及数据
$worker = new BusinessWorker();
$worker->name = $config['worker']['name'];
$worker->count = $config['worker']['count'];
$worker->registerAddress = $config['worker']['register_address'];
$worker->eventHandler = $config['worker']['event_handler'];

// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}
