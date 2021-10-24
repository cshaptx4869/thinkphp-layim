<?php


namespace Fairy;

class Toolkit
{
    public static function success($data = '', $msg = '信息调用成功！')
    {
        return self::returnData($data, $msg, 0);
    }

    public static function error($msg = '信息调用成功！', $data = '')
    {
        return self::returnData($data, $msg, 1);
    }

    public static function returnData($data = '', $msg = '信息调用成功！', $code = 0)
    {
        return ['code' => $code, 'data' => $data, 'msg' => $msg, 'time' => $_SERVER['REQUEST_TIME']];
    }
}
