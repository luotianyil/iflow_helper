<?php

namespace iflow\Helper\Str;

use iflow\Helper\Str\Tools\SnowFlake;

class Str {

    /**
     * 雪花工具类
     * @var ?SnowFlake
     */
    protected static ?SnowFlake $snowFlake = null;

    /**
     * 生成UUID
     * @param bool $trim
     * @return string
     */
    public static function genUuid(bool $trim = true): string {
        // Windows
        if (function_exists('com_create_guid') === true) {
            return $trim === true ? trim(com_create_guid(), '{}') : com_create_guid();
        }

        // OSX/Linux
        if (function_exists('openssl_random_pseudo_bytes') === true) {
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40);    // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80);    // set bits 6-7 to 10
            return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
        }

        // Fallback (PHP 4.2+)
        mt_srand((int) microtime() * 10000);
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);                  // "-"
        $lbrace = $trim ? "" : chr(123);    // "{"
        $rbrace = $trim ? "" : chr(125);    // "}"
        return $lbrace.
            substr($charid,  0,  8).$hyphen.
            substr($charid,  8,  4).$hyphen.
            substr($charid, 12,  4).$hyphen.
            substr($charid, 16,  4).$hyphen.
            substr($charid, 20, 12).
            $rbrace;
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     */
    public static function RandomStr(int $length = 20): string {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = '';
        for($i = 0; $i < $length; $i++) $str .= substr($chars, mt_rand(0, 62), 1);
        return $str;
    }

    /**
     * 创建随机数
     * @return string
     */
    public static function RandomNumber() : string {
        $mtime = explode(' ',microtime());
        $random = $mtime[1] . $mtime[0] . rand(999, 9999);
        $random_sum = 0;
        for ($i = 0; $i < strlen($random); $i++) {
            $random_sum += (int)(substr($random, $i,1));
        }
        return str_replace('.', '', $random) . str_pad((100 - $random_sum % 100) % 100,2,'0',STR_PAD_LEFT);
    }

    /**
     * 生成随机数可指定长度
     * @param int $length
     * @return int
     */
    public static function RandomNumberByLength(int $length): int {
        $number = '';
        $numberArr = [ 0, 1, 2, 3, 4, 5, 6, 7, 8, 9 ];
        for($i = 0; $i < $length; $i++) {
            $number .= $numberArr[mt_rand(
                $i === 0 ? 1 : 0, 9
            )];
        }
        return intval($number);
    }

    /**
     * 生成雪花ID
     * @param string $prefix
     * @param string $nsCode 机器id
     * @param string $serviceId 服务id
     * @return string
     */
    public static function genSnowFlake(string $prefix = '', string $nsCode = '', string $serviceId = ''): string {
        static::$snowFlake ??= new SnowFlake();
        return self::$snowFlake -> genSnowFlake($prefix, $nsCode, $serviceId);
    }

    /**
     * 驼峰转小写
     * @param string $str
     * @param string $separator
     * @return string
     */
    public static function humpToLower(string $str, string $separator = '_'): string {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $str));
    }

    /**
     * 小写转驼峰
     * @param string $str
     * @param string $separator
     * @return string
     */
    public static function unHumpToLower(string $str, string $separator = '_'): string {
        $str = $separator . str_replace($separator, " ", strtolower($str));
        return ltrim(str_replace(" ", "", ucwords($str)), $separator);
    }
}