<?php
/**
 * Created by PhpStorm.
 * User: xixi
 * Date: 2018/4/10
 * Time: 15:28
 */

return  [
    'app_id' => env('WX_APP_ID',NULL),
    'secret' => env('WX_APP_SECRET',NULL),

    // 下面为可选项
    // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
    'response_type' => 'array',

    'log' => [
        'level' => 'debug',
        'file' => storage_path().'/logs/wechat.log',
    ],
];