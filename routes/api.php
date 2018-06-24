<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', ['namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
    $api->get('/wx/authorize', 'AuthenticateController@authorize1');
    $api->get('/wx/captcha', 'AuthenticateController@getCaptcha');
    $api->get('/wx/getcode', ['uses'=>'AuthenticateController@sendPhoneCode']);


});

$api->version('v1', ['middleware' => 'api.auth', 'namespace' => 'App\Http\Controllers\Api\V1'], function ($api) {
    $api->put('/wx/user/editAccount', 'UserController@editAccount');
    $api->post('/wx/user/saveProfile', 'AuthenticateController@saveProfile');

    $api->get('/wx/phone', 'UserController@phoneList');

});

