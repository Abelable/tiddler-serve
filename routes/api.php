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

Route::get('user_info', 'UserController@userInfo');
Route::get('tim_login_info', 'UserController@timLoginInfo');
Route::get('author_info', 'UserController@authorInfo');
Route::get('oss_config', 'CommonController@ossConfig');

Route::prefix('wx')->group(function () {
    Route::post('pay_notify', 'CommonController@wxPayNotify');
    Route::get('qrcode', 'CommonController@wxQRCode');
});

Route::prefix('fan')->group(function () {
    Route::post('follow', 'FanController@follow');
    Route::post('cancel_follow', 'FanController@cancelFollow');
    Route::get('follow_status', 'FanController@followStatus');
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
    Route::get('shop_list', 'OrderController@shopList');
    Route::get('detail', 'OrderController@detail');
    Route::post('confirm', 'OrderController@confirm');
    Route::post('refund', 'OrderController@refund');
    Route::post('cancel', 'OrderController@cancel');
    Route::post('delete', 'OrderController@delete');
});

Route::prefix('scenic')->group(function () {
    Route::get('category_options', 'ScenicController@categoryOptions');
    Route::get('list', 'ScenicController@list');
    Route::get('detail', 'ScenicController@detail');
    Route::get('options', 'ScenicController@options');

    Route::prefix('provider')->group(function () {
        Route::post('settle_in', 'ScenicProviderController@settleIn');
        Route::get('status', 'ScenicProviderController@statusInfo');
        Route::post('pay_deposit', 'ScenicProviderController@payDeposit');
        Route::post('delete', 'ScenicProviderController@deleteProvider');
        Route::get('scenic_list_totals', 'ScenicProviderController@scenicListTotals');
        Route::get('scenic_list', 'ScenicProviderController@providerScenicSpotList');
        Route::post('apply_scenic', 'ScenicProviderController@applyScenicSpot');
        Route::post('delete_scenic', 'ScenicProviderController@deleteProviderScenicSpot');
        Route::get('scenic_options', 'ScenicProviderController@providerScenicOptions');

        Route::prefix('ticket')->group(function () {
            Route::get('totals', 'ScenicTicketController@ticketListTotals');
            Route::get('list', 'ScenicTicketController@userTicketList');
            Route::get('detail', 'ScenicTicketController@detail');
            Route::post('add', 'ScenicTicketController@add');
            Route::post('edit', 'ScenicTicketController@edit');
            Route::post('up', 'ScenicTicketController@up');
            Route::post('down', 'ScenicTicketController@down');
            Route::post('delete', 'ScenicTicketController@delete');
        });
    });

    Route::prefix('shop')->group(function () {
        Route::get('my_shop_info', 'ScenicProviderController@myShopInfo');
    });

    Route::prefix('ticket')->group(function () {
        Route::get('category_options', 'ScenicTicketController@categoryOptions');
        Route::get('list', 'ScenicTicketController@list');
        Route::get('detail', 'ScenicTicketController@detail');
        Route::get('list_of_scenic', 'ScenicTicketController@listByScenicId');
    });

    Route::prefix('order')->group(function () {
        Route::get('calc_payment_amount', 'ScenicOrderController@paymentAmount');
        Route::post('submit', 'ScenicOrderController@submit');
        Route::post('pay_params', 'ScenicOrderController@payParams');
        Route::get('list', 'ScenicOrderController@list');
        Route::get('shop_list', 'ScenicOrderController@shopList');
        Route::get('detail', 'ScenicOrderController@detail');
        Route::post('confirm', 'ScenicOrderController@confirm');
        Route::post('refund', 'ScenicOrderController@refund');
        Route::post('cancel', 'ScenicOrderController@cancel');
        Route::post('delete', 'ScenicOrderController@delete');
    });
});

Route::prefix('hotel')->group(function () {
    Route::get('category_options', 'HotelController@categoryOptions');
    Route::get('list', 'HotelController@list');
    Route::get('detail', 'HotelController@detail');
    Route::get('options', 'HotelController@options');

    Route::prefix('provider')->group(function () {
        Route::post('settle_in', 'HotelProviderController@settleIn');
        Route::get('status', 'HotelProviderController@statusInfo');
        Route::post('pay_deposit', 'HotelProviderController@payDeposit');
        Route::post('delete', 'HotelProviderController@deleteProvider');
        Route::get('hotel_list_totals', 'HotelProviderController@hotelListTotals');
        Route::get('hotel_list', 'HotelProviderController@providerHotelList');
        Route::post('apply_hotel', 'HotelProviderController@applyHotel');
        Route::post('delete_hotel', 'HotelProviderController@deleteProviderHotel');
        Route::get('hotel_options', 'HotelProviderController@providerHotelOptions');

        Route::prefix('room')->group(function () {
            Route::get('totals', 'HotelRoomController@roomListTotals');
            Route::get('list', 'HotelRoomController@userRoomList');
            Route::get('detail', 'HotelRoomController@detail');
            Route::post('add', 'HotelRoomController@add');
            Route::post('edit', 'HotelRoomController@edit');
            Route::post('up', 'HotelRoomController@up');
            Route::post('down', 'HotelRoomController@down');
            Route::post('delete', 'HotelRoomController@delete');
        });
    });

    Route::prefix('shop')->group(function () {
        Route::get('my_shop_info', 'HotelProviderController@myShopInfo');
    });

    Route::prefix('room')->group(function () {
        Route::get('type_options', 'HotelRoomController@typeOptions');
        Route::get('list', 'HotelRoomController@list');
        Route::get('detail', 'HotelRoomController@detail');
        Route::get('list_of_hotel', 'HotelRoomController@listByHotelId');
    });

    Route::prefix('order')->group(function () {
        Route::get('calc_payment_amount', 'HotelOrderController@paymentAmount');
        Route::post('submit', 'HotelOrderController@submit');
        Route::post('pay_params', 'HotelOrderController@payParams');
        Route::get('list', 'HotelOrderController@list');
        Route::get('shop_list', 'HotelOrderController@shopList');
        Route::get('detail', 'HotelOrderController@detail');
        Route::post('confirm', 'HotelOrderController@confirm');
        Route::post('refund', 'HotelOrderController@refund');
        Route::post('cancel', 'HotelOrderController@cancel');
        Route::post('delete', 'HotelOrderController@delete');
    });
});

Route::prefix('catering')->group(function () {
    Route::prefix('restaurant')->group(function () {
        Route::get('category_options', 'RestaurantController@categoryOptions');
        Route::get('list', 'RestaurantController@list');
        Route::get('detail', 'RestaurantController@detail');
        Route::get('options', 'RestaurantController@options');
        Route::post('add', 'RestaurantController@add');
        Route::post('edit', 'RestaurantController@edit');
        Route::post('delete', 'RestaurantController@delete');
        Route::get('user_options', 'RestaurantController@userOptions');
    });

    Route::prefix('provider')->group(function () {
        Route::post('settle_in', 'CateringProviderController@settleIn');
        Route::get('status', 'CateringProviderController@statusInfo');
        Route::post('pay_deposit', 'CateringProviderController@payDeposit');
        Route::post('delete', 'CateringProviderController@deleteProvider');

        Route::prefix('restaurant')->group(function () {
            Route::get('totals', 'ProviderRestaurantController@listTotals');
            Route::get('list', 'ProviderRestaurantController@list');
            Route::post('apply', 'ProviderRestaurantController@apply');
            Route::post('delete', 'ProviderRestaurantController@delete');
        });

        Route::prefix('set_meal')->group(function () {
            Route::get('totals', 'SetMealController@roomListTotals');
            Route::get('list', 'SetMealController@userRoomList');
            Route::get('detail', 'SetMealController@detail');
            Route::post('add', 'SetMealController@add');
            Route::post('edit', 'SetMealController@edit');
            Route::post('up', 'SetMealController@up');
            Route::post('down', 'SetMealController@down');
            Route::post('delete', 'SetMealController@delete');
        });

        Route::prefix('meal_ticket')->group(function () {
            Route::get('totals', 'MealTicketController@roomListTotals');
            Route::get('list', 'MealTicketController@userRoomList');
            Route::get('detail', 'MealTicketController@detail');
            Route::post('add', 'MealTicketController@add');
            Route::post('edit', 'MealTicketController@edit');
            Route::post('up', 'MealTicketController@up');
            Route::post('down', 'MealTicketController@down');
            Route::post('delete', 'MealTicketController@delete');
        });
    });

    Route::prefix('set_meal')->group(function () {
        Route::get('list', 'SetMealController@list');
        Route::get('detail', 'SetMealController@detail');
        Route::prefix('order')->group(function () {
            Route::get('calc_payment_amount', 'SetMealOrderController@paymentAmount');
            Route::post('submit', 'SetMealOrderController@submit');
            Route::post('pay_params', 'SetMealOrderController@payParams');
            Route::get('list', 'SetMealOrderController@list');
            Route::get('shop_list', 'SetMealOrderController@shopList');
            Route::get('detail', 'SetMealOrderController@detail');
            Route::post('confirm', 'SetMealOrderController@confirm');
            Route::post('refund', 'SetMealOrderController@refund');
            Route::post('cancel', 'SetMealOrderController@cancel');
            Route::post('delete', 'SetMealOrderController@delete');
        });
    });

    Route::prefix('meal_ticket')->group(function () {
        Route::get('list', 'MealTicketController@list');
        Route::get('detail', 'MealTicketController@detail');
        Route::prefix('order')->group(function () {
            Route::get('calc_payment_amount', 'MealTicketOrderController@paymentAmount');
            Route::post('submit', 'MealTicketOrderController@submit');
            Route::post('pay_params', 'MealTicketOrderController@payParams');
            Route::get('list', 'MealTicketOrderController@list');
            Route::get('shop_list', 'MealTicketOrderController@shopList');
            Route::get('detail', 'MealTicketOrderController@detail');
            Route::post('confirm', 'MealTicketOrderController@confirm');
            Route::post('refund', 'MealTicketOrderController@refund');
            Route::post('cancel', 'MealTicketOrderController@cancel');
            Route::post('delete', 'MealTicketOrderController@delete');
        });
    });
});

Route::prefix('media')->group(function () {
    Route::get('list', 'MediaController@list');
    Route::get('follow_list', 'MediaController@followList');
    Route::get('collect_list', 'MediaController@collectList');
    Route::get('like_list', 'MediaController@likeList');

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
        Route::post('join_room', 'LivePlayController@joinRoom');
        Route::post('praise', 'LivePlayController@praise');
        Route::post('comment', 'LivePlayController@comment');
        Route::post('subscribe', 'LivePlayController@subscribe');
    });

    Route::prefix('short_video')->group(function () {
        Route::get('list', 'ShortVideoController@list');
        Route::get('user_list', 'ShortVideoController@userVideoList');
        Route::get('collect_list', 'ShortVideoController@collectVideoList');
        Route::get('like_list', 'ShortVideoController@likeVideoList');
        Route::post('create', 'ShortVideoController@createVideo');
        Route::post('toggle_private', 'ShortVideoController@togglePrivate');
        Route::post('delete', 'ShortVideoController@deleteVideo');
        Route::post('toggle_like', 'ShortVideoController@toggleLikeStatus');
        Route::post('toggle_collect', 'ShortVideoController@toggleCollectStatus');
        Route::post('comment', 'ShortVideoController@comment');
        Route::get('comment_list', 'ShortVideoController@getCommentList');
        Route::get('reply_comment_list', 'ShortVideoController@getReplyCommentList');
        Route::post('share', 'ShortVideoController@share');
        Route::post('delete_comment', 'ShortVideoController@deleteComment');
    });

    Route::prefix('tourism_note')->group(function () {
        Route::get('list', 'TourismNoteController@list');
        Route::get('user_list', 'TourismNoteController@userNoteList');
        Route::get('collect_list', 'TourismNoteController@collectNoteList');
        Route::get('like_list', 'TourismNoteController@likeNoteList');
        Route::post('create', 'TourismNoteController@createNote');
        Route::post('toggle_private', 'TourismNoteController@togglePrivate');
        Route::post('delete', 'TourismNoteController@deleteNote');
        Route::post('toggle_like', 'TourismNoteController@toggleLikeStatus');
        Route::post('toggle_collect', 'TourismNoteController@toggleCollectionStatus');
        Route::post('comment', 'TourismNoteController@comment');
        Route::get('comment_list', 'TourismNoteController@getCommentList');
        Route::get('reply_comment_list', 'TourismNoteController@getReplyCommentList');
        Route::post('share', 'TourismNoteController@share');
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

    Route::prefix('scenic')->group(function () {
        Route::prefix('category')->group(function () {
            Route::post('list', 'ScenicCategoryController@list');
            Route::get('detail', 'ScenicCategoryController@detail');
            Route::post('add', 'ScenicCategoryController@add');
            Route::post('edit', 'ScenicCategoryController@edit');
            Route::post('delete', 'ScenicCategoryController@delete');
            Route::get('options', 'ScenicCategoryController@options');
        });

        Route::post('list', 'ScenicController@list');
        Route::get('detail', 'ScenicController@detail');
        Route::post('add', 'ScenicController@add');
        Route::post('edit', 'ScenicController@edit');
        Route::post('delete', 'ScenicController@delete');
        Route::get('options', 'ScenicController@options');

        Route::prefix('provider')->group(function () {
            Route::post('list', 'ScenicProviderController@list');
            Route::get('detail', 'ScenicProviderController@detail');
            Route::post('approved', 'ScenicProviderController@approved');
            Route::post('reject', 'ScenicProviderController@reject');
            Route::post('order_list', 'ScenicProviderController@orderList');

            Route::prefix('scenic')->group(function () {
                Route::post('list', 'ScenicProviderController@providerScenicList');
                Route::post('approved', 'ScenicProviderController@approvedScenicApply');
                Route::post('reject', 'ScenicProviderController@rejectScenicApply');
                Route::post('delete', 'ScenicProviderController@deleteScenicApply');
            });
        });

        Route::prefix('shop')->group(function () {
            Route::post('list', 'ScenicShopController@list');
            Route::get('detail', 'ScenicShopController@detail');
        });

        Route::prefix('ticket')->group(function () {
            Route::prefix('category')->group(function () {
                Route::post('list', 'ScenicTicketCategoryController@list');
                Route::get('detail', 'ScenicTicketCategoryController@detail');
                Route::post('add', 'ScenicTicketCategoryController@add');
                Route::post('edit', 'ScenicTicketCategoryController@edit');
                Route::post('delete', 'ScenicTicketCategoryController@delete');
            });

            Route::post('list', 'ScenicTicketController@list');
            Route::get('detail', 'ScenicTicketController@detail');
            Route::post('approve', 'ScenicTicketController@approve');
            Route::post('reject', 'ScenicTicketController@reject');
            Route::post('delete', 'ScenicTicketController@delete');
        });
    });

    Route::prefix('hotel')->group(function () {
        Route::prefix('category')->group(function () {
            Route::post('list', 'HotelCategoryController@list');
            Route::get('detail', 'HotelCategoryController@detail');
            Route::post('add', 'HotelCategoryController@add');
            Route::post('edit', 'HotelCategoryController@edit');
            Route::post('delete', 'HotelCategoryController@delete');
            Route::get('options', 'HotelCategoryController@options');
        });

        Route::post('list', 'HotelController@list');
        Route::get('detail', 'HotelController@detail');
        Route::post('add', 'HotelController@add');
        Route::post('edit', 'HotelController@edit');
        Route::post('delete', 'HotelController@delete');
        Route::get('options', 'HotelController@options');

        Route::prefix('room_type')->group(function () {
            Route::post('list', 'HotelRoomTypeController@list');
            Route::get('detail', 'HotelRoomTypeController@detail');
            Route::post('add', 'HotelRoomTypeController@add');
            Route::post('edit', 'HotelRoomTypeController@edit');
            Route::post('delete', 'HotelRoomTypeController@delete');
            Route::get('options', 'HotelRoomTypeController@options');
        });

        Route::prefix('provider')->group(function () {
            Route::post('list', 'HotelProviderController@list');
            Route::get('detail', 'HotelProviderController@detail');
            Route::post('approved', 'HotelProviderController@approved');
            Route::post('reject', 'HotelProviderController@reject');
            Route::post('order_list', 'HotelProviderController@orderList');

            Route::prefix('hotel')->group(function () {
                Route::post('list', 'HotelProviderController@providerHotelList');
                Route::post('approved', 'HotelProviderController@approvedHotelApply');
                Route::post('reject', 'HotelProviderController@rejectHotelApply');
                Route::post('delete', 'HotelProviderController@deleteHotelApply');
            });
        });

        Route::prefix('shop')->group(function () {
            Route::post('list', 'HotelShopController@list');
            Route::get('detail', 'HotelShopController@detail');
        });

        Route::prefix('room')->group(function () {
            Route::prefix('type')->group(function () {
                Route::post('list', 'HotelRoomTypeController@list');
                Route::get('detail', 'HotelRoomTypeController@detail');
                Route::post('add', 'HotelRoomTypeController@add');
                Route::post('edit', 'HotelRoomTypeController@edit');
                Route::post('delete', 'HotelRoomTypeController@delete');
            });

            Route::post('list', 'HotelRoomController@list');
            Route::get('detail', 'HotelRoomController@detail');
            Route::post('approve', 'HotelRoomController@approve');
            Route::post('reject', 'HotelRoomController@reject');
            Route::post('delete', 'HotelRoomController@delete');
        });
    });

    Route::prefix('catering')->group(function () {
        Route::prefix('provider')->group(function () {
            Route::post('list', 'CateringProviderController@list');
            Route::get('detail', 'CateringProviderController@detail');
            Route::post('approved', 'CateringProviderController@approved');
            Route::post('reject', 'CateringProviderController@reject');
            Route::post('order_list', 'CateringProviderController@orderList');

            Route::prefix('restaurant')->group(function () {
                Route::post('list', 'ProviderRestaurantController@list');
                Route::post('approved', 'ProviderRestaurantController@approvedApply');
                Route::post('reject', 'ProviderRestaurantController@rejectApply');
                Route::post('delete', 'ProviderRestaurantController@deleteApply');
            });
        });

        Route::prefix('restaurant')->group(function () {
            Route::prefix('category')->group(function () {
                Route::post('list', 'RestaurantCategoryController@list');
                Route::get('detail', 'RestaurantCategoryController@detail');
                Route::post('add', 'RestaurantCategoryController@add');
                Route::post('edit', 'RestaurantCategoryController@edit');
                Route::post('delete', 'RestaurantCategoryController@delete');
                Route::get('options', 'RestaurantCategoryController@options');
            });

            Route::post('list', 'RestaurantController@list');
            Route::get('detail', 'RestaurantController@detail');
            Route::post('add', 'RestaurantController@add');
            Route::post('edit', 'RestaurantController@edit');
            Route::post('delete', 'RestaurantController@delete');
            Route::get('options', 'RestaurantController@options');
        });

        Route::prefix('set_meal')->group(function () {
            Route::post('list', 'SetMealController@list');
            Route::get('detail', 'SetMealController@detail');
            Route::post('approve', 'SetMealController@approve');
            Route::post('reject', 'SetMealController@reject');
            Route::post('delete', 'SetMealController@delete');
        });

        Route::prefix('meal_ticket')->group(function () {
            Route::post('list', 'MealTicketController@list');
            Route::get('detail', 'MealTicketController@detail');
            Route::post('approve', 'MealTicketController@approve');
            Route::post('reject', 'MealTicketController@reject');
            Route::post('delete', 'MealTicketController@delete');
        });
    });
});
