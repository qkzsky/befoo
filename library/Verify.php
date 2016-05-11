<?php

/*
 * Mi Framework
 *
 * Copyright (C) 2015 by kuangzhiqiang. All rights reserved
 *
 * To contact the author write to {@link mailto:kuangzhiqiang@xiaomi.com}
 *
 * @author kuangzhiqiang
 * @encoding UTF-8
 * @version $Id: Verify.php, v 1.0 2015-9-4 20:04:25
 */

/**
 * Description of Verify
 *
 * @author kuangzhiqiang
 */
class Verify
{

    /**
     * 是否为空值
     * @param string|int $value
     * @return boolean
     */
    public static function isEmpty($value)
    {
        return empty(trim($value));
    }

    /**
     * 是否为int型
     * @param string $value
     * @return boolean
     */
    public static function isInt($value)
    {
        return (string) (int) $value === (string) $value;
    }

    /**
     * 是否为float
     * @param string $value
     * @return boolean
     */
    public static function isFloat($value)
    {
        return (string) (float) $value === (string) $value;
    }

    /**
     * 邮箱验证
     * @param string $value
     * @return boolean
     */
    public static function isEmail($value)
    {
        return preg_match("/([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i", $value);
    }

    /**
     * 手机号码验证
     * @param string $value
     * @return boolean
     */
    public static function isMobile($value)
    {
        $exp = "/^13\d{9}$|15[012356789]{1}\d{8}|18[012356789]{1}\d{8}|14[57]{1}\d{8}$/";
        return preg_match($exp, $value);
    }

    /**
     * URL验证
     * @param string $value
     * @return boolean
     */
    public static function isUrl($value)
    {
        return preg_match('#(http|https|ftp|ftps)://([\w-]+.)+[\w-]+(/[\w-./?%&=]*)?#i', $value);
    }

    /**
     * 验证中文
     * @param string $value
     * @param string $charset 编码（默认utf-8,支持gb2312）
     * @return boolean
     */
    public static function isChinese($value, $charset = 'utf-8')
    {
        $match = (strtolower($charset) == 'gb2312') ? "/^[" . chr(0xa1) . "-" . chr(0xff) . "]+$/" : "/^[x{4e00}-x{9fa5}]+$/u";
        return preg_match($match, $value) ? true : false;
    }

    /**
     * UTF-8验证
     * @param string $value
     * @return boolean
     */
    public static function isUtf8($value)
    {
        return (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $value) === true ||
                preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}$/", $value) === true ||
                preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $value) === true);
    }

    /**
     * 是否为Y, N
     * @param string $value
     * @return boolean
     */
    public static function isSwitch($value)
    {
        return in_array($value, array(YES, NO));
    }

    /**
     * 验证长度
     * @param string $value
     * @param int $min 最小值
     * @param int $max 最大值
     * @param string $charset 字符集
     * @return boolean
     */
    public static function checkLength($value, $min = 0, $max = 0, $charset = 'utf-8')
    {
        $len = mb_strlen($value, $charset);
        if ($min > 0 && $max > 0)
        {
            return $len >= $min && $len <= $max;
        }
        elseif ($min > 0)
        {
            return $len >= $min;
        }
        elseif ($max > 0)
        {
            return $len <= $max;
        }
        return false;
    }

    /**
     * 验证用户名格式
     * @param string $value
     * @param int $length
     * @return boolean
     */
    public static function isNames($value, $minLen = 2, $maxLen = 16, $charset = 'ALL')
    {
        switch ($charset)
        {
            case 'EN':
                $match = '/^[_\w\d]{' . (int) $minLen . ',' . (int) $maxLen . '}$/iu';
                break;
            case 'CN':
                $match = '/^[_\x{4e00}-\x{9fa5}\d]{' . (int) $minLen . ',' . (int) $maxLen . '}$/iu';
                break;
            default:
                $match = '/^[_\w\d\x{4e00}-\x{9fa5}]{' . (int) $minLen . ',' . (int) $maxLen . '}$/iu';
                break;
        }
        return preg_match($match, $value);
    }

    /**
     * 验证日期格式
     * @param string $value
     * @return boolean
     */
    public static function checkDate($value)
    {
        $dateArr = explode("-", $value);
        if (count($dateArr) === 3 && self::isInt($dateArr[0]) && self::isInt($dateArr[1]) && self::isInt($dateArr[2]))
        {
            if (($dateArr[0] >= 1000 && $dateArr[0] <= 10000) && ($dateArr[1] >= 0 && $dateArr[1] <= 12) && ($dateArr[2] >= 0 && $dateArr[2] <= 31))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        return false;
    }

    /**
     * 验证时间格式
     * @param string $value
     * @return boolean
     */
    public static function checkTime($value)
    {
        $timeArr = explode(":", $value);
        if (count($timeArr) === 2 && self::isInt($timeArr[0]) && self::isInt($timeArr[1]) && self::isInt($timeArr[2]))
        {
            if (($timeArr[0] >= 0 && $timeArr[0] <= 23) && ($timeArr[1] >= 0 && $timeArr[1] <= 59) && ($timeArr[2] >= 0 && $timeArr[2] <= 59))
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        return false;
    }

    /**
     * 验证日期时间格式
     * @param string $value
     * @return boolean
     */
    public static function checkDateTime($value)
    {
        $arr = explode(" ", $value);
        return count($arr) === 2 && self::checkDate($arr[0]) && self::checkTime($arr[1]);
    }

}
