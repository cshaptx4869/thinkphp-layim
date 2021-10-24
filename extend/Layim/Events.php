<?php

namespace Layim;

use GatewayWorker\Lib\Gateway;

class Events
{
    // 当有客户端连接时，将client_id返回，让mvc框架判断当前uid并执行绑定
    public static function onConnect($clientId)
    {
        Gateway::sendToClient($clientId, json_encode([
            'emit' => 'bind',
            'data' => [
                'client_id' => $clientId
            ]
        ]));
    }

    // 集合 TP 等框架时，GatewayWorker建议不做任何业务逻辑，onMessage留空即可
    public static function onMessage($clientId, $message)
    {
    }

    public static function onClose($clientId)
    {
    }
}
