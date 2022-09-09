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

Route::prefix('auth')->group(function() {
    Route::prefix('wx_mp')->group(function() {
        Route::post('mobile', 'AuthController@getWxMpUserMobile');
        Route::post('register', 'AuthController@wxMpRegister');
        Route::post('login', 'AuthController@wxMpLogin');
    });
});

Route::get('user_info', 'UserController@getUserInfo');

// 管理后台
Route::namespace('Admin')->prefix('admin')->group(function() {
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');

    Route::post('list', 'AdminController@list');
    Route::post('add', 'AdminController@add');
    Route::post('edit', 'AdminController@edit');
    Route::post('delete', 'AdminController@delete');

    Route::prefix('role')->group(function() {
        Route::post('list', 'RoleController@list');
        Route::post('add', 'RoleController@add');
        Route::post('edit', 'RoleController@edit');
        Route::post('delete', 'RoleController@delete');
    });

    Route::prefix('user')->group(function() {
        Route::post('list', 'UserController@list');
        Route::post('edit', 'UserController@edit');
        Route::post('delete', 'UserController@delete');
    });
});


