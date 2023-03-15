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
Route::get('tim_login_info', 'UserController@getTimLoginInfo');
Route::get('oss_config', 'CommonController@ossConfig');

Route::prefix('wx')->group(function () {
    Route::post('pay_notify', 'CommonController@wxPayNotify');
    Route::get('qrcode', 'CommonController@wxQRCode');
});

Route::prefix('shop')->group(function () {
    Route::get('category_options', 'ShopController@categoryOptions');

    Route::prefix('merchant')->group(function () {
        Route::post('settle_in', 'ShopController@addMerchant');
        Route::get('status', 'ShopController@merchantStatusInfo');
        Route::post('pay_deposit', 'ShopController@payDeposit');
        Route::post('delete', 'ShopController@deleteMerchant');
    });

    Route::get('info', 'ShopController@shopInfo');
    Route::get('my_shop_info', 'ShopController@myShopInfo');
    Route::get('express_options', 'ShopController@expressOptions');

    Route::prefix('freight_template')->group(function () {
        Route::get('list', 'FreightTemplateController@list');
        Route::get('detail', 'FreightTemplateController@detail');
        Route::post('add', 'FreightTemplateController@add');
        Route::post('edit', 'FreightTemplateController@edit');
        Route::post('delete', 'FreightTemplateController@delete');
    });

    Route::prefix('goods_return_address')->group(function () {
        Route::get('list', 'GoodsReturnAddressController@list');
        Route::get('detail', 'GoodsReturnAddressController@detail');
        Route::post('add', 'GoodsReturnAddressController@add');
        Route::post('edit', 'GoodsReturnAddressController@edit');
        Route::post('delete', 'GoodsReturnAddressController@delete');
    });

    Route::get('goods_list', 'GoodsController@shopGoodsList');
    Route::prefix('goods')->group(function () {
        Route::get('totals', 'GoodsController@goodsListTotals');
        Route::get('list', 'GoodsController@merchantGoodsList');
        Route::get('info', 'GoodsController@goodsInfo');
        Route::post('add', 'GoodsController@add');
        Route::post('edit', 'GoodsController@edit');
        Route::post('up', 'GoodsController@up');
        Route::post('down', 'GoodsController@down');
        Route::post('delete', 'GoodsController@delete');
    });
});

Route::prefix('goods')->group(function () {
    Route::get('category_options', 'GoodsController@categoryOptions');
    Route::get('list', 'GoodsController@list');
    Route::get('detail', 'GoodsController@detail');
    Route::get('user_goods_list', 'GoodsController@userGoodsList');
});

Route::prefix('cart')->group(function () {
    Route::get('goods_number', 'CartController@goodsNumber');
    Route::get('list', 'CartController@list');
    Route::post('fast_add', 'CartController@fastAdd');
    Route::post('add', 'CartController@add');
    Route::post('edit', 'CartController@edit');
    Route::post('delete', 'CartController@delete');
});

Route::prefix('address')->group(function () {
    Route::get('list', 'AddressController@list');
    Route::get('detail', 'AddressController@detail');
    Route::post('add', 'AddressController@add');
    Route::post('edit', 'AddressController@edit');
    Route::post('delete', 'AddressController@delete');
});

Route::prefix('order')->group(function () {
    Route::post('pre_order_info', 'OrderController@preOrderInfo');
    Route::post('submit', 'OrderController@submit');
    Route::post('pay_params', 'OrderController@payParams');
    Route::get('list', 'OrderController@list');
    Route::get('detail', 'OrderController@detail');
    Route::post('confirm', 'OrderController@confirm');
    Route::post('refund', 'OrderController@refund');
    Route::post('cancel', 'OrderController@cancel');
    Route::post('delete', 'OrderController@delete');
});

Route::prefix('media')->group(function () {
    Route::get('list', 'MediaController@getList');
    Route::get('follow_list', 'MediaController@getFollowList');

    Route::prefix('live')->group(function () {
        Route::post('create', 'LivePushController@createRoom');
        Route::get('room_status', 'LivePushController@roomStatus');
        Route::get('notice_room', 'LivePushController@noticeRoomInfo');
        Route::post('delete_notice_room', 'LivePushController@deleteNoticeRoom');
        Route::get('push_room', 'LivePushController@pushRoomInfo');
        Route::post('start', 'LivePushController@startLive');
        Route::post('stop', 'LivePushController@stopLive');
        Route::get('list', 'LivePlayController@roomList');
        Route::get('push_room_goods_list', 'LivePushController@pushRoomGoodsList');
        Route::post('listing_goods', 'LivePushController@listingGoods');
        Route::post('de_listing_goods', 'LivePushController@delistingGoods');
        Route::post('set_hot_goods', 'LivePushController@setHotGoods');
        Route::post('cancel_hot_goods', 'LivePushController@cancelHotGoods');
        Route::get('goods_list', 'LivePlayController@roomGoodsList');
        Route::get('hot_goods', 'LivePlayController@roomHotGoods');
        Route::post('join', 'LivePlayController@joinRoom');
        Route::post('praise', 'LivePlayController@praise');
        Route::post('comment', 'LivePlayController@comment');
    });

    Route::prefix('short_video')->group(function () {
        Route::get('list', 'ShortVideoController@getRoomList');
        Route::post('create', 'ShortVideoController@createVideo');
        Route::post('delete', 'ShortVideoController@deleteVideo');
        Route::post('toggle_praise', 'ShortVideoController@togglePraiseStatus');
        Route::post('toggle_collection', 'ShortVideoController@toggleCollectionStatus');
        Route::post('share', 'ShortVideoController@share');
        Route::get('comment_list', 'ShortVideoController@getCommentList');
        Route::get('reply_comment_list', 'ShortVideoController@getReplyCommentList');
        Route::post('comment', 'ShortVideoController@comment');
        Route::post('delete_comment', 'ShortVideoController@deleteComment');
    });

    Route::prefix('tourism_note')->group(function () {
        Route::get('list', 'TourismNoteController@getRoomList');
        Route::post('create', 'TourismNoteController@createVideo');
        Route::post('delete', 'TourismNoteController@deleteVideo');
        Route::post('toggle_praise', 'TourismNoteController@togglePraiseStatus');
        Route::post('toggle_collection', 'TourismNoteController@toggleCollectionStatus');
        Route::post('share', 'TourismNoteController@share');
        Route::get('comment_list', 'TourismNoteController@getCommentList');
        Route::get('reply_comment_list', 'TourismNoteController@getReplyCommentList');
        Route::post('comment', 'TourismNoteController@comment');
        Route::post('delete_comment', 'TourismNoteController@deleteComment');
    });
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

    Route::prefix('goods')->group(function () {
        Route::prefix('category')->group(function () {
            Route::post('list', 'GoodsCategoryController@list');
            Route::get('detail', 'GoodsCategoryController@detail');
            Route::post('add', 'GoodsCategoryController@add');
            Route::post('edit', 'GoodsCategoryController@edit');
            Route::post('delete', 'GoodsCategoryController@delete');
            Route::get('options', 'GoodsCategoryController@options');
        });

        Route::post('list', 'GoodsController@list');
        Route::get('detail', 'GoodsController@detail');
        Route::get('owner_list', 'GoodsController@ownerList');
        Route::get('owner_detail', 'GoodsController@ownerDetail');
        Route::post('up', 'GoodsController@up');
        Route::post('down', 'GoodsController@down');
        Route::post('reject', 'GoodsController@reject');
        Route::post('delete', 'GoodsController@delete');
        Route::post('add', 'GoodsController@add');
        Route::post('edit', 'GoodsController@edit');
    });
});
