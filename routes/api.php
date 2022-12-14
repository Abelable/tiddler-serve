<?php

use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function () {
    Route::prefix('wx_mp')->group(function () {
        Route::post('mobile', 'AuthController@getWxMpUserMobile');
        Route::post('register', 'AuthController@wxMpRegister');
        Route::post('login', 'AuthController@wxMpLogin');
    });

    Route::get('token_refresh', 'AuthController@refreshToken');
});

Route::get('user_info', 'UserController@getUserInfo');

Route::get('oss_config', 'CommonController@ossConfig');

Route::prefix('shop')->group(function () {
    Route::get('category_options', 'ShopController@categoryOptions');
    Route::prefix('merchant')->group(function () {
        Route::post('settle_in', 'ShopController@addMerchant');
        Route::get('status', 'ShopController@merchantStatusInfo');
        Route::post('pay_deposit', 'ShopController@payDeposit');
        Route::post('delete', 'ShopController@deleteMerchant');
    });
    Route::get('shop_info', 'ShopController@shopInfo');
    Route::get('my_shop_info', 'ShopController@myShopInfo');
    Route::get('express_options', 'ShopController@expressOptions');

});

Route::prefix('wx')->group(function () {
    Route::post('pay_notify', 'CommonController@wxPayNotify');
});


/*
|--------------------------------------------------------------------------
| 管理后台接口
|--------------------------------------------------------------------------
*/
Route::namespace('Admin')->prefix('admin')->group(function () {
    Route::get('oss_config', 'CommonController@ossConfig');

    Route::prefix('auth')->group(function () {
        Route::post('login', 'AuthController@login');
        Route::get('logout', 'AuthController@logout');
        Route::get('me', 'AuthController@info');
        Route::get('token_refresh', 'AuthController@refreshToken');
    });

    Route::post('list', 'AdminController@list');
    Route::get('detail', 'AdminController@detail');
    Route::post('add', 'AdminController@add');
    Route::post('edit', 'AdminController@edit');
    Route::post('delete', 'AdminController@delete');

    Route::prefix('role')->group(function () {
        Route::post('list', 'RoleController@list');
        Route::get('detail', 'RoleController@detail');
        Route::post('add', 'RoleController@add');
        Route::post('edit', 'RoleController@edit');
        Route::post('delete', 'RoleController@delete');
        Route::get('options', 'RoleController@options');
    });

    Route::prefix('user')->group(function () {
        Route::post('list', 'UserController@list');
        Route::get('detail', 'UserController@detail');
        Route::post('delete', 'UserController@delete');
    });

    Route::prefix('merchant')->group(function () {
        Route::post('list', 'MerchantController@list');
        Route::get('detail', 'MerchantController@detail');
        Route::post('approved', 'MerchantController@approved');
        Route::post('reject', 'MerchantController@reject');
        Route::post('order_list', 'MerchantController@orderList');
    });

    Route::prefix('shop')->group(function () {
        Route::prefix('category')->group(function () {
            Route::post('list', 'ShopCategoryController@list');
            Route::get('detail', 'ShopCategoryController@detail');
            Route::post('add', 'ShopCategoryController@add');
            Route::post('edit', 'ShopCategoryController@edit');
            Route::post('delete', 'ShopCategoryController@delete');
            Route::get('options', 'ShopCategoryController@options');
        });
        Route::post('list', 'ShopController@list');
        Route::get('detail', 'ShopController@detail');
    });

    Route::prefix('express')->group(function () {
        Route::post('list', 'ExpressController@list');
        Route::get('detail', 'ExpressController@detail');
        Route::post('add', 'ExpressController@add');
        Route::post('edit', 'ExpressController@edit');
        Route::post('delete', 'ExpressController@delete');
        Route::get('options', 'ExpressController@options');
    });
});
