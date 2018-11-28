<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    //用户路由
    $router->get('consumer', 'ConsumerController@index');
    $router->get('consumer/{id}/edit', 'ConsumerController@edit');
    $router->get('consumer/{id}', 'ConsumerController@show');
    $router->put('consumer/{id}', 'ConsumerController@update');
    //用户银行卡路由
    $router->get('bank', 'BankController@index');
    $router->get('bank/{id}/edit', 'BankController@edit');
    $router->put('bank/{id}', 'BankController@update');
    //用户地址路由
    $router->get('address', 'AddressController@index');
    $router->get('address/{id}/edit', 'AddressController@edit');
    $router->put('address/{id}', 'AddressController@update');
});
