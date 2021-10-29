<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Member extends Model
{
    const STATUS_ONLINE = 0;
    const STATUS_OFFLINE = 1;
    const STATUS_HIDE = 2;

    /**
     * @return string
     */
    public static function getStatusText($value)
    {
        $arr = [
            self::STATUS_ONLINE => 'online',
            self::STATUS_OFFLINE => 'offline',
            self::STATUS_HIDE => 'hide',
        ];

        return $arr[$value];
    }

    public static function getStatusValue($text)
    {
        $arr = [
            'online' => self::STATUS_ONLINE,
            'offline' => self::STATUS_OFFLINE,
            'hide' => self::STATUS_HIDE
        ];

        return $arr[$text];
    }
}
