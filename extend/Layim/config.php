<?php

return [
    'gateway' => [
        'socket_name' => 'websocket://127.0.0.1:7272',
        'name' => 'ChatGateway', //gateway名称
        'count' => 4, //gateway进程数
        'lan_ip' => '127.0.0.1', //本机ip，分布式部署时使用内网ip
        'start_port' => 2300, //BusinessWorker内部通讯起始端口，假如 $gateway->count=4，起始端口为4000 则一般会使用4000 4001 4002 4003 4个端口作为内部通讯端口
        'register_address' => '127.0.0.1:1236', //服务注册地址
        'ping_interval' => 50, //心跳时间间隔
        'ping_not_response_limit' => 1,// pingNotResponseLimit * pingInterval 时间内，客户端未发送任何数据，断开客户端连接
        'ping_data' => '', //服务端向客户端发送的心跳数据 {"type":"ping"} 心跳模式有两种 1客户端定时发送心跳(推荐) 2服务端主动发送心跳(不推荐) 如果数据为空表示心跳方式1
    ],
    'worker' => [
        'name' => 'ChatBusinessWorker', //worker名称
        'count' => 4, //bussinessWorker进程数量 建议cpu核数的1-3倍
        'register_address' => '127.0.0.1:1236',// 服务注册地址
        'event_handler' => 'Layim\\Events', //设置处理业务的类
    ],
    'register' => [
        'socket_name' => 'text://127.0.0.1:1236',
    ]
];
