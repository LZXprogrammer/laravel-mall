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

Route::post('/login', 'Api\LoginController@index')->middleware('mobile');

Route::group(['prefix' => 'credit', 'namespace' => 'Api'], function () {

    Route::get('/banner', 'CreditController@creditBanner');
    Route::get('/list', 'CreditController@creditList');
    Route::get('/detail/{id}', 'CreditController@creditDetail');

});

Route::group(['prefix' => 'home', 'namespace' => 'Api'], function () {

    Route::get('/banner', 'HomeController@homeBanner');
    Route::get('/goodlists', 'HomeController@homeGoodLists');
    
});