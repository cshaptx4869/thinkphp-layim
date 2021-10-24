<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class GroupMember extends Model
{
    const STATUS_NORMAL = 0;
    const STATUS_BLACKLIST = 1;
}
