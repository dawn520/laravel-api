<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 错误返回
     *
     * @param $message
     * @param int $status_code
     * @param array $data
     * @return array
     */
    public function error($message = 'error', $status_code = -1, $data = [])
    {
        $returnJson = $this->createResponseData($status_code, $message, $data);
        return response()
            ->json($returnJson);

    }

    /**
     * 成功返回
     *
     * @param $message
     * @param array $data
     * @param int $status_code
     * @return array
     */
    public function success($message = 'success！', $data = [], $status_code = 200)
    {
        $returnJson = $this->createResponseData($status_code, $message, $data);
        return response()
            ->json($returnJson);
    }

    /**
     * 创建返回数据
     *
     * @param  int $code
     * @param  string $msg
     * @param  array $data
     * @return array $responseData
     */
    private function createResponseData($code, $msg, $data = [])
    {
        $responseData = [
            'code'    => $code,
            'message' => $msg,
            'data'    => $data
        ];
        if (empty($data)) {
            unset($responseData['data']);
        }
        if (!config('app.with_msg')) {
            unset($responseData['msg']);
        }
        return $responseData;
    }
}
