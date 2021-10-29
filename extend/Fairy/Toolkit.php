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

    public static function returnData($data = '', $msg = '信息调用成功！', $code)
    {
        return ['code' => $code, 'data' => $data, 'msg' => $msg, 'time' => $_SERVER['REQUEST_TIME']];
    }

    /**
     * 格式化时间
     * @param $time
     * @return string
     */
    public static function formatDate($time)
    {
        $t = time() - $time;
        $f = [
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        ];

        foreach ($f as $k => $v) {
            if (0 != $c = floor($t / (int)$k)) {
                return $c . $v . '前';
            }
        }
    }

    /**
     * @param array $arr
     * @param $key
     * @return array
     */
    public static function setArrayIndex(array $arr, $key)
    {
        $newarr = [];
        foreach ($arr as $k => $v) {
            $newarr[$v[$key]] = $v;
        }

        return $newarr;
    }

    /**
     * @param array $arr
     * @param $key
     * @return array
     */
    public static function setArray2Index(array $arr, $key)
    {
        $newArr = [];
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $newArr[$v[$key]][] = $v;
            }
        }

        return $newArr;
    }

    /**
     * @param array $arr
     * @param $key
     * @param $value
     * @return array
     */
    public static function setArray2Column(array $arr, $key, $value)
    {
        $newArr = [];
        foreach ($arr as $k => $v) {
            $newArr[$v[$key]][] = $v[$value];
        }

        return $newArr;
    }

    /**
     * 随机字符串
     * @param int $length
     * @return false|string
     */
    public static function randStr($length = 6)
    {
        return substr(str_shuffle(join('', array_merge(range('a', 'z'), range('A', 'Z'), range(0, 9)))), 0, $length);
    }
}
