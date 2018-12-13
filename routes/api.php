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

// 信用卡
Route::group(['prefix' => 'credit', 'namespace' => 'Api'], function () {
    Route::get('/banner', 'CreditController@creditBanner');
    Route::get('/list', 'CreditController@creditList');
    Route::get('/detail', 'CreditController@creditDetail');
    Route::get('/comment', 'CreditController@creditComment');
});

// 首页
Route::group(['prefix' => 'home', 'namespace' => 'Api'], function () {
    Route::get('/banner', 'HomeController@homeBanner');
    Route::get('/goodlists', 'HomeController@homeGoodLists');
});

//发送短信路由
Route::post('/sendSms', 'Api\SmsController@index');

// 商品
Route::group(['prefix' => 'goods', 'namespace' => 'Api'], function () {
    Route::get('/detail', 'GoodsDetailController@goodsDetail');
    Route::get('/comment/{id}', 'GoodsDetailController@goodsComment');
});

// 验证当前用户保持登陆状态
Route::group(['middleware' => 'user'], function () {

    // 个人中心 -- 我的设置
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
        Route::post('/listsTrade', 'UsersListController@trade');
    });
    
    // 购物车
    Route::group(['prefix' => 'cart', 'namespace' => 'Api'], function () {
        Route::post('/add', 'CartController@cartAdd');
        Route::post('/list', 'CartController@cartList');
    });

    // 购物车结算下单、立即购买下单，提交订单
    Route::group(['prefix' => 'order', 'namespace' => 'Api'], function () {

        Route::post('/address', 'OrderController@orderAddress');
        Route::post('/goods/buynow', 'OrderController@orderGoodsBuyNow');
        Route::post('/goods/buycart', 'OrderController@orderGoodsBuyCart')->middleware('orderGoods');
        Route::post('/submit', 'OrderController@orderSubmit')->middleware('orderSubmit');
        Route::post('/add/member', 'OrderController@addMember');
    });

    // 个人中心 -- 我的订单
    Route::group(['prefix' => 'orders', 'namespace' => 'Api'], function () {
        Route::get('/index', 'UserOrderController@index');
        Route::post('/ordersCancel', 'UserOrderController@cancelOrder')->middleware('id');
        Route::post('/ordersDel', 'UserOrderController@delOrder')->middleware('id');
        Route::post('/ordersDetail', 'UserOrderController@detailOrder')->middleware('id');
        Route::post('/orderConfirm', 'UserOrderController@confirmOrder')->middleware('id');
    });

    Route::group(['prefix' => 'pay', 'namespace' => 'Api'], function () {
        Route::get('/aliPay', 'PayController@aliPay')->middleware('id');
        Route::get('/alipayReturn', 'PayController@alipayReturn')->name('pay.alipay.return');
        Route::post('/alipayNotify', 'PayController@alipayNotify')->name('pay.alipay.notify');
    });
});


