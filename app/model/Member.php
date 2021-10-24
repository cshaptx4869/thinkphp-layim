<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Member extends Model
{
    const STATUS_HIDE = 0;
    const STATUS_ONLINE = 1;

}
