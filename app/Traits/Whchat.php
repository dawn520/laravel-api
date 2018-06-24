<?php

namespace App\Traits;

/**
 * Created by PhpStorm.
 * User: xixi
 * Date: 2018/4/10
 * Time: 19:02
 */
use EasyWeChat\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

trait Whchat
{
    /**
     * @param $code
     * @return bool
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function getAuthData($code)
    {
        $wechat = Factory::miniProgram(Config::get('wechat'));
        try {
            $re = $wechat->auth->session($code);
        } catch (InvalidConfigException $e) {
            Log::info($e);
            return false;
        }
        if (empty($re['openid'])) {
            Log::info('获取openid失败', $re);
            return false;
        }
        return $re;
    }

    /**
     * @param $session_key
     * @param $iv
     * @param $encryptedData
     * @return mixed
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function getDecryptData($session_key, $iv, $encryptedData)
    {
        $wechat = Factory::miniProgram(Config::get('wechat'));
        $decryptedData = $wechat->encryptor->decryptData($session_key, $iv, $encryptedData);
        return $decryptedData;

    }
}