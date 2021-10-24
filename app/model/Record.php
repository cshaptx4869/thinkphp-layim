<?php
declare (strict_types = 1);

namespace app\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class Record extends Model
{
    const IS_NOT_READ = 0;
    const IS_READ = 1;
}
