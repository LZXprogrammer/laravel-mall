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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

//登陆注册模块路由
Route::group(['prefix' => 'login', 'namespace' => 'Api'], function () {
    Route::post('/doLogin', 'LoginController@index')->middleware(['mobile', 'password']);
    Route::post('/doRegister', 'LoginController@register')->middleware(['mobile', 'password', 'code']);
    Route::post('/upPassWord', 'LoginController@updatePassword')->middleware(['mobile', 'upPwd', 'code']);
    Route::post('/fgPassWord', 'LoginController@forgotPassword')->middleware(['mobile', 'fgPwd', 'code']);
});

Route::group(['prefix' => 'credit', 'namespace' => 'Api'], function () {
    Route::get('/banner', 'CreditController@creditBanner');
    Route::get('/list', 'CreditController@creditList');
    Route::get('/detail/{id}', 'CreditController@creditDetail');
    Route::get('/comment/{id}', 'CreditController@creditComment');
});

Route::group(['prefix' => 'home', 'namespace' => 'Api'], function () {
    Route::get('/banner', 'HomeController@homeBanner');
    Route::get('/goodlists', 'HomeController@homeGoodLists');
});

//发送短信路由
Route::post('/sendSms', 'Api\SmsController@index');

Route::group(['prefix' => 'goods', 'namespace' => 'Api'], function () {
    Route::get('/detail', 'GoodsDetailController@goodsDetail');
    Route::get('/comment/{id}', 'GoodsDetailController@goodsComment');
});


Route::group(['middleware' => 'user'], function () {
    Route::group(['prefix' => 'users', 'namespace' => 'Api'], function () {
        Route::post('/home', 'UserController@index');
        Route::post('/address', 'UserController@addressList');
        Route::post('/addressEdit', 'UserController@editAddress')->middleware(['addressEdit']);
        Route::post('/addressDel', 'UserController@delAddress')->middleware(['address']);
        Route::post('/realName', 'UserController@realNameAuth')->middleware(['realNameAuth']);
        Route::post('/listsBank', 'UserController@bankList');
        Route::post('/bankEdit', 'UserController@editBank')->middleware('mobile');
        Route::post('/bankDel', 'UserController@delBank')->middleware('id');
        Route::get('/listsUser', 'UsersListController@index')->middleware('level');
    });
    
    Route::group(['prefix' => 'cart', 'namespace' => 'Api'], function () {
        Route::post('/add', 'CartController@cartAdd');
        Route::post('/list', 'CartController@cartList');
    });

    Route::group(['prefix' => 'order', 'namespace' => 'Api'], function () {

        Route::post('/address', 'OrderController@orderAddress');
        Route::post('/goods', 'OrderController@orderGoods');
        Route::post('/submit', 'OrderController@orderSubmit')->middleware('orderSubmit');

    });

    Route::group(['prefix' => 'orders', 'namespace' => 'Api'], function () {
        Route::post('/index', 'UserOrderController@index');
    });
});


