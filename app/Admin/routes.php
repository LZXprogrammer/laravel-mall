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
    $router->put('consumer/{id}', 'ConsumerController@update');
    $router->get('consumer/{id}', 'ConsumerController@show');

    //用户银行卡路由
    $router->get('bank', 'BankController@index');
    $router->get('bank/{id}/edit', 'BankController@edit');
    $router->put('bank/{id}', 'BankController@update');
    //用户地址路由
    $router->get('address', 'AddressController@index');
    $router->get('address/{id}/edit', 'AddressController@edit');
    $router->put('address/{id}', 'AddressController@update');
    //商品列表路由
    $router->get('goods', 'GoodsController@index');
    $router->get('goods/{id}/edit', 'GoodsController@edit');
    $router->put('goods/{id}', 'GoodsController@update');
    $router->get('goods/create', 'GoodsController@create');
    $router->post('goods', 'GoodsController@store');
    $router->get('goods/{id}', 'GoodsController@show');
    //商品系列路由
    $router->get('category', 'CategoryController@index');
    $router->get('category/{id}/edit', 'CategoryController@edit');
    $router->put('category/{id}', 'CategoryController@update');
    $router->get('category/create', 'CategoryController@create');
    $router->post('category', 'CategoryController@store');
    //短信列表路由
    $router->get('message', 'MessageController@index');
    //短信列表路由
    $router->get('message_template', 'MessageTemplateController@index');
    $router->get('message_template/{id}/edit', 'MessageTemplateController@edit');
    $router->put('message_template/{id}', 'MessageTemplateController@update');
    $router->get('message_template/create', 'MessageTemplateController@create');
    $router->post('message_template', 'MessageTemplateController@store');
    //信用卡列表路由
    $router->get('credit_card', 'CreditCardController@index');
    $router->get('credit_card/{id}/edit', 'CreditCardController@edit');
    $router->put('credit_card/{id}', 'CreditCardController@update');
    $router->get('credit_card/create', 'CreditCardController@create');
    $router->post('credit_card', 'CreditCardController@store');
    //信用卡类别列表路由
    $router->get('credit_type', 'CreditTypeController@index');
    $router->get('credit_type/{id}/edit', 'CreditTypeController@edit');
    $router->put('credit_type/{id}', 'CreditTypeController@update');
    $router->get('credit_type/create', 'CreditTypeController@create');
    $router->post('credit_type', 'CreditTypeController@store');
    //文章列表路由
    $router->get('article', 'ArticleController@index');
    $router->delete('article/{id}', 'ArticleController@destroy');
    $router->get('article/{id}/edit', 'ArticleController@edit');
    $router->put('article/{id}', 'ArticleController@update');
    $router->get('article/create', 'ArticleController@create');
    $router->post('article', 'ArticleController@store');
});
