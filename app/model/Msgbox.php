<?php
declare (strict_types=1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Msgbox extends Model
{
    const TYPE_MAKE_FRIEND_USER = 0;
    const TYPE_MAKE_FRIEND_SYSTEM = 1;
    const TYPE_JOIN_GROUP_USER = 2;
    const TYPE_JOIN_GROUP_SYSTEM = 3;

    const STATUS_PENDING = 0;
    const STATUS_AGREED = 1;
    const STATUS_REFUSED = 2;
    const STATUS_IGNORED = 3;

    /**
     * 未读的消息数
     * @param $memberId
     * @return int
     */
    public static function getUnreadCountByMemberId($memberId)
    {
        return self::where('to', $memberId)
            ->whereNull('read_time')
            ->count();
    }
}
