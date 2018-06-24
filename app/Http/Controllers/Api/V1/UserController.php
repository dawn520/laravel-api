<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Sms;
use Illuminate\Http\Request;
use Validator;
use JWTAuth;

class UserController extends Controller
{
    public function editAccount(Request $request)
    {
        $input = $request->only(['phone', 'name', 'code']);
        $rules = [
            'phone' => 'regex:/^1[34578][0-9]{9}$/',
            'name'  => 'max:3|min:2',
            'code'  => 'required_with:phone|max:4|min:4'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->error('请求参数有误!', 400000, ['error' => $validator->errors()->all()]);
        }
        $user = auth()->user();
        $user = User::find($user->id);
        if (empty($user)) {
            return $this->error('用户不存在!', 400001);
        }
        if (!empty($input['code'])) {
            $sms = Sms::where('phone', $input['phone'])
                ->where('code', $input['code'])
                ->where('used', 0)
                ->where('type', 1)
                ->first();

            if ($sms) {
                if (time() - strtotime($sms->created_at) > 3600 * 24) {
                    return $this->error('注册失败：验证码已失效！', 400022);
                }
            } else {
                return $this->error('注册失败：验证码有误！', 400023);
            }
            $sms->used = 1;
            $sms->save();
        }

        if (!empty($input['phone'])) {
            $user->phone = $input['phone'];
        }
        if (!empty($input['name'])) {
            $user->name = $input['name'];
        }
        $user->save();

        return $this->success();
    }


    public function phoneList(Request $request)
    {
        $page = intval($request->get('page', 1));
        $limit = intval($request->get('limit', 10));
        $list = User
            ::with(['profile'])
            ->whereNotNull('phone')
            ->select(['id', 'name', 'phone', 'created_at'])
            ->paginate($limit, $columns = ['*'], $pageName = 'page', $page);

        foreach ($list->items() as $item) {
            if(!empty($item->profile->nickName)){
                if (mb_strlen($item->profile->nickName) > 5) {
                    $item->profile->nickName = mb_substr($item->profile->nickName, 0, 5, 'utf-8');
                    $item->profile->nickName .= '..';
                }
            }

        }

        $data = [
            'count' => $list->total(),
            'list'  => $list->items()
        ];
        return $this->success(Null, $data);
    }
}
