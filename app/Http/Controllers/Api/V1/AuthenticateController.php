<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Models\Sms;
use App\Services\sms as ServiceSms;
use EasyWeChat\Factory;
use EasyWeChat\Kernel\Exceptions\InvalidConfigException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Validator;
use JWTAuth;
use App\Traits\Whchat;
use Captcha;
use Carbon\Carbon;

class AuthenticateController extends Controller
{
    use Whchat;

    public function authorize1(Request $request)
    {
        $input['code'] = $request->input('code');
        $rules = ['code' => 'required'];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->error('请求参数有误!', 400000, ['error' => $validator->errors()->all()]);
        }
        $authData = $this->getAuthData($input['code']);
        $session_key = $authData['session_key'];
        $openid = $authData['openid'];
        $user = User::firstOrCreate(['wx_openid' => $openid], ['session_key' => $session_key]);

        $token = JWTAuth::fromUser($user);
        $isRegister = 0;
        if (!empty($user->phone)) {
            $isRegister = 1;
        }
        $data = [
            'token'       => $token,
            'is_register' => $isRegister
        ];
        return $this->success('成功', $data);
    }

    public function saveProfile(Request $request)
    {
//        $dataGet = $request->only(['encryptedData', 'errMsg', 'iv', 'signature', 'userInfo']);
//
//        $input = $dataGet['userInfo'];
        $input = $request->input();
        $user = auth()->user();
//        $decryptedData = $this->getDecryptData($user->session_key, $dataGet['iv'], $dataGet['encryptedData']);
        $data = $input;
        $data['uid'] = $user->id;
        $profile = Profile::where('uid', $user->id)->first();
        if (!empty($profile)) {
            $profile->nickName = $input['nickName'];
            $profile->country = $input['country'];
            $profile->city = $input['city'];
            $profile->language = $input['language'];
            $profile->avatarUrl = $input['avatarUrl'];
            $profile->gender = $input['gender'];
            $profile->save();
        } else {
            Profile::create($data);
        }

        return $this->success('成功', [$profile]);
    }

    /**
     *获取验证码
     * @param Request $request
     * @return string $data
     */
    public function getCaptcha(Request $request)
    {
        $image = Captcha::width(100)->height(36)->make();

        if ($request->input('image') == 1) {
            $data = $image->response();
        } else {
            $data = $image->getDataUrl();
        }
        return $data;
    }

    public function sendPhoneCode(Request $request, ServiceSms $sms)
    {
        $input = $request->only(['phone', 'captcha']);
        $rules = [
            'phone'   => 'required|regex:/^1[34578][0-9]{9}$/',
            'captcha' => 'required|captcha'
        ];
        $messages = [
            'captcha.required' => '请输入图形验证码！',
            'captcha.captcha'  => '图形验证码输入有误，请重试！'
        ];
        $validator = Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return $this->error('请求参数有误!', 400000, ['error' => $validator->errors()->all()]);
        }

        $clientIp = intval(ip2long(app('request')->getClientIp()));
        //验证规则
        //24小时内的次数
        $requestTimes = Sms::where('phone', $input['phone'])
            ->where('created_at', '>', Carbon::now()->subDay())
            ->count();
        if ($requestTimes >= 3) {
            return $this->error('您请求验证码太过频繁，请稍后再试！', 400014);
        }
        //上一条是否距离3分钟
        $lastRequest = Sms::where('phone', $input['phone'])
            ->where('created_at', '>', Carbon::now()->subMinute(3))
            ->orderBy('id', 'desc')
            ->first();
        if ($lastRequest) {
            return $this->error('您请求验证码太过频繁，请稍后再试！', 400014);
        }

        //相同IP每天的次数
        $requestIPTimes = Sms::where('ip', $clientIp)
            ->where('created_at', '>', Carbon::now()->subDay())
            ->count();
        if ($requestIPTimes >= 3) {
            return $this->error('您请求验证码太过频繁，请稍后再试！', 400014);
        }

        //同一ip两次间隔10秒
        $lastIPRequest = Sms::where('ip', $clientIp)
            ->where('created_at', '>', Carbon::now()->subSeconds(10))
            ->orderBy('id', 'desc')
            ->first();
        if ($lastIPRequest) {
            return $this->error('发送失败：您请求验证码太过频繁，请稍后再试！', 400014);
        }
        $user = auth()->user();
        if (empty($user)) {
            $uid = 0;
        } else {
            $uid = $user->id;
        }
        $code = getRandStr(4, 'NUMBER');
        Sms::create([
            'uid'   => $uid,
            'phone' => $input['phone'],
            'code'  => $code,
            'used'  => 0,
            'type'  => 1,
            'ip'    => ip2long(app('request')->getClientIp())
        ]);
        $res = $sms->send($input['phone'], $code);
        if (!$res) {
            return $this->error('');
        }
        return $this->success();
    }
}
