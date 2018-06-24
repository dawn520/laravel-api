<?php
/**
 * Created by PhpStorm.
 * User: xixi
 * Date: 2018/4/12
 * Time: 14:09
 */

/**
 * 随机产生六位数
 *
 * @param int $len
 * @param string $format
 * @return string
 */
function getRandStr($len = 6, $format = 'ALL')
{
    switch ($format) {
        case 'ALL':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
            break;
        case 'CHAR':
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
            break;
        case 'NUMBER':
            $chars = '0123456789';
            break;
        default :
            $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
            break;
    }
    mt_srand(intval(floatval(microtime()) * 1000000 * getmypid()));
    $password = "";
    while (strlen($password) < $len)
        $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
    return $password;
}