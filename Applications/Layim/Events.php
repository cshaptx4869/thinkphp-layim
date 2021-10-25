<?php

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start_for_linux.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    /**
     * 当客户端连接时触发
     * 集合 TP 等框架时，将clientId返回，让mvc框架判断当前uid并执行绑定
     * 如果业务不需此回调可以删除onConnect
     * @param int $clientId 连接id
     */
    public static function onConnect($clientId)
    {
        Gateway::sendToClient($clientId, json_encode([
            'emit' => 'bind',
            'data' => [
                'client_id' => $clientId
            ]
        ]));
    }

    //
    /**
     * 当客户端发来消息时触发
     * 集合 TP 等框架时，GatewayWorker建议不做任何业务逻辑，onMessage留空即可
     * @param int $clientId 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage($clientId, $message)
    {
    }

    /**
     * 当用户断开连接时触发
     * @param int $clientId 连接id
     */
    public static function onClose($clientId)
    {
    }
}
