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

    Route::prefix('info')->group(function () {
        Route::get('detail', 'AuthInfoController@detail');
        Route::post('add', 'AuthInfoController@add');
        Route::post('edit', 'AuthInfoController@edit');
        Route::post('delete', 'AuthInfoController@delete');
    });
});

Route::prefix('user')->group(function () {
    Route::get('me', 'UserController@myInfo');
    Route::get('info', 'UserController@userInfo');
    Route::post('update', 'UserController@updateUserInfo');
    Route::get('tim_login_info', 'UserController@timLoginInfo');
    Route::get('author_info', 'UserController@authorInfo');
    Route::get('search', 'UserController@search');
    Route::get('options', 'UserController@options');
    Route::get('order_total', 'UserController@orderTotal');
    Route::get('add_temp_user', 'UserController@addTempUser');
    Route::get('supply_user_mobile', 'UserController@supplyUserMobile');
});

Route::get('oss_config', 'CommonController@ossConfig');
Route::get('qr_code', 'CommonController@qrCode');

Route::prefix('ai')->group(function () {
    Route::post('stream', 'AiController@stream');
});

Route::prefix('feedback')->group(function () {
    Route::post('submit', 'FeedbackController@submit');
});

Route::prefix('wx')->group(function () {
    Route::post('pay_notify', 'CommonController@wxPayNotify');
    Route::get('qr_code', 'CommonController@wxQrCode');
});

Route::prefix('fan')->group(function () {
    Route::post('follow', 'FanController@follow');
    Route::post('cancel_follow', 'FanController@cancelFollow');
    Route::get('follow_status', 'FanController@followStatus');
    Route::get('follow_list', 'FanController@followList');
    Route::get('fan_list', 'FanController@fanList');
});

Route::prefix('keyword')->group(function () {
    Route::get('list', 'KeywordController@list');
    Route::post('add', 'KeywordController@add');
    Route::post('clear', 'KeywordController@clear');
    Route::get('hot_list', 'KeywordController@hotList');
});

Route::prefix('merchant')->group(function () {
    Route::post('settle_in', 'MerchantController@settleIn');
    Route::get('status', 'MerchantController@status');
    Route::get('info', 'MerchantController@info');
    Route::post('delete', 'MerchantController@delete');
});

Route::prefix('shop')->group(function () {
    Route::get('express_options', 'ShopController@expressOptions');
    Route::get('category_options', 'ShopController@categoryOptions');
    Route::get('info', 'ShopController@shopInfo');
    Route::post('update_info', 'ShopController@updateShopInfo');

    Route::prefix('deposit')->group(function () {
        Route::post('pay_params', 'ShopDepositController@payParams');
        Route::get('info', 'ShopDepositController@depositInfo');
        Route::get('log_list', 'ShopDepositController@changeLogList');
    });

    Route::prefix('manager')->group(function () {
        Route::get('list', 'ShopManagerController@list');
        Route::get('detail', 'ShopManagerController@detail');
        Route::post('add', 'ShopManagerController@add');
        Route::post('edit', 'ShopManagerController@edit');
        Route::post('delete', 'ShopManagerController@delete');
    });

    Route::prefix('freight_template')->group(function () {
        Route::get('list', 'FreightTemplateController@list');
        Route::get('detail', 'FreightTemplateController@detail');
        Route::post('add', 'FreightTemplateController@add');
        Route::post('edit', 'FreightTemplateController@edit');
        Route::post('delete', 'FreightTemplateController@delete');
    });

    Route::prefix('refund_address')->group(function () {
        Route::get('list', 'ShopRefundAddressController@list');
        Route::get('detail', 'ShopRefundAddressController@detail');
        Route::post('add', 'ShopRefundAddressController@add');
        Route::post('edit', 'ShopRefundAddressController@edit');
        Route::post('delete', 'ShopRefundAddressController@delete');
    });

    Route::prefix('pickup_address')->group(function () {
        Route::get('list', 'ShopPickupAddressController@list');
        Route::get('detail', 'ShopPickupAddressController@detail');
        Route::post('add', 'ShopPickupAddressController@add');
        Route::post('edit', 'ShopPickupAddressController@edit');
        Route::post('delete', 'ShopPickupAddressController@delete');
    });

    Route::prefix('goods')->group(function () {
        Route::get('category_options', 'ShopGoodsController@categoryOptions');
        Route::get('totals', 'ShopGoodsController@totals');
        Route::get('list', 'ShopGoodsController@list');
        Route::get('detail', 'ShopGoodsController@detail');
        Route::post('add', 'ShopGoodsController@add');
        Route::post('edit', 'ShopGoodsController@edit');
        Route::post('up', 'ShopGoodsController@up');
        Route::post('down', 'ShopGoodsController@down');
        Route::post('delete', 'ShopGoodsController@delete');
    });

    Route::prefix('order')->group(function () {
        Route::get('total', 'ShopOrderController@total');
        Route::get('list', 'ShopOrderController@list');
        Route::get('detail', 'ShopOrderController@detail');
        Route::post('verify', 'ShopOrderController@verify');
        Route::get('unshipped_goods_list', 'ShopOrderController@unshippedGoodsList');
        Route::post('ship', 'ShopOrderController@ship');

        Route::prefix('keyword')->group(function () {
            Route::get('list', 'ShopOrderKeywordController@list');
            Route::post('add', 'ShopOrderKeywordController@add');
            Route::post('clear', 'ShopOrderKeywordController@clear');
        });
    });

    Route::prefix('income')->group(function () {
        Route::get('data_overview', 'ShopIncomeController@dataOverview');
        Route::get('sum', 'ShopIncomeController@sum');
        Route::get('time_data', 'ShopIncomeController@timeData');
        Route::post('order_list', 'ShopIncomeController@incomeOrderList');

        Route::prefix('withdraw')->group(function () {
            Route::post('submit', 'ShopWithdrawalController@submit');
            Route::get('record_list', 'ShopWithdrawalController@recordList');
        });
    });
});

Route::prefix('goods')->group(function () {
    Route::get('category_options', 'GoodsController@categoryOptions');
    Route::get('list', 'GoodsController@list');
    Route::get('search', 'GoodsController@search');
    Route::get('detail', 'GoodsController@detail');
    Route::get('shop_list', 'GoodsController@shopList');
    Route::get('purchased_list', 'GoodsController@purchasedList');
    Route::post('recommend_list', 'GoodsController@recommendList');
    Route::post('media_relative_list', 'GoodsController@mediaRelativeList');

    Route::prefix('evaluation')->group(function () {
        Route::get('summary', 'GoodsEvaluationController@summary');
        Route::get('list', 'GoodsEvaluationController@list');
        Route::get('detail', 'GoodsEvaluationController@detail');
        Route::post('add', 'GoodsEvaluationController@add');
        Route::post('delete', 'GoodsEvaluationController@delete');
    });
});

Route::prefix('gift')->group(function () {
    Route::get('type_options', 'GiftGoodsController@typeOptions');
    Route::get('list', 'GiftGoodsController@list');
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
    Route::get('pickup_address_list', 'OrderController@pickupAddressList');
    Route::post('pre_order_info', 'OrderController@preOrderInfo');
    Route::post('submit', 'OrderController@submit');
    Route::post('pay_params', 'OrderController@payParams');
    Route::get('total', 'OrderController@total');
    Route::get('list', 'OrderController@list');
    Route::get('search', 'OrderController@search');
    Route::get('detail', 'OrderController@detail');
    Route::get('verify_code', 'OrderController@verifyCode');
    Route::post('confirm', 'OrderController@confirm');
    Route::post('refund', 'OrderController@refund');
    Route::post('cancel', 'OrderController@cancel');
    Route::post('delete', 'OrderController@delete');

    Route::prefix('keyword')->group(function () {
        Route::get('list', 'OrderKeywordController@list');
        Route::post('add', 'OrderKeywordController@add');
        Route::post('clear', 'OrderKeywordController@clear');
    });
});

Route::prefix('scenic')->group(function () {
    Route::get('category_options', 'ScenicController@categoryOptions');
    Route::get('list', 'ScenicController@list');
    Route::get('search', 'ScenicController@search');
    Route::get('nearby_list', 'ScenicController@nearbyList');
    Route::post('media_relative_list', 'ScenicController@mediaRelativeList');
    Route::get('detail', 'ScenicController@detail');
    Route::get('options', 'ScenicController@options');
    Route::post('add', 'ScenicController@add');
    Route::post('edit', 'ScenicController@edit');
    Route::post('delete', 'ScenicController@delete');
    Route::get('shop_options', 'ScenicController@shopOptions');

    Route::prefix('question')->group(function () {
        Route::get('list', 'ScenicQAController@questionList');
        Route::get('detail', 'ScenicQAController@questionDetail');
        Route::post('add', 'ScenicQAController@addQuestion');
        Route::post('delete', 'ScenicQAController@deleteQuestion');
    });

    Route::prefix('answer')->group(function () {
        Route::get('list', 'ScenicQAController@answerList');
        Route::post('add', 'ScenicQAController@addAnswer');
        Route::post('delete', 'ScenicQAController@deleteAnswer');
    });

    Route::prefix('evaluation')->group(function () {
        Route::get('list', 'ScenicEvaluationController@list');
        Route::post('add', 'ScenicEvaluationController@add');
        Route::post('delete', 'ScenicEvaluationController@delete');
    });

    Route::prefix('merchant')->group(function () {
        Route::post('settle_in', 'ScenicMerchantController@settleIn');
        Route::get('status', 'ScenicMerchantController@status');
        Route::get('info', 'ScenicMerchantController@info');
        Route::post('delete', 'ScenicMerchantController@delete');
    });

    Route::prefix('shop')->group(function () {
        Route::get('info', 'ScenicShopController@shopInfo');
        Route::post('update_info', 'ScenicShopController@updateShopInfo');

        Route::prefix('deposit')->group(function () {
            Route::post('pay_params', 'ScenicShopDepositController@payParams');
            Route::get('info', 'ScenicShopDepositController@depositInfo');
            Route::get('log_list', 'ScenicShopDepositController@changeLogList');
        });

        Route::prefix('manager')->group(function () {
            Route::get('list', 'ScenicShopManagerController@list');
            Route::get('detail', 'ScenicShopManagerController@detail');
            Route::post('add', 'ScenicShopManagerController@add');
            Route::post('edit', 'ScenicShopManagerController@edit');
            Route::post('delete', 'ScenicShopManagerController@delete');
        });

        Route::prefix('scenic')->group(function () {
            Route::get('totals', 'ShopScenicController@totals');
            Route::get('list', 'ShopScenicController@list');
            Route::post('apply', 'ShopScenicController@apply');
            Route::post('delete', 'ShopScenicController@delete');
            Route::get('options', 'ShopScenicController@options');
        });

        Route::prefix('ticket')->group(function () {
            Route::get('totals', 'ShopScenicTicketController@totals');
            Route::get('list', 'ShopScenicTicketController@list');
            Route::post('add', 'ShopScenicTicketController@add');
            Route::post('edit', 'ShopScenicTicketController@edit');
            Route::post('up', 'ShopScenicTicketController@up');
            Route::post('down', 'ShopScenicTicketController@down');
            Route::post('delete', 'ShopScenicTicketController@delete');
        });

        Route::prefix('order')->group(function () {
            Route::get('total', 'ScenicShopOrderController@total');
            Route::get('list', 'ScenicShopOrderController@list');
            Route::get('search', 'ScenicShopOrderController@search');
            Route::get('detail', 'ScenicShopOrderController@detail');
            Route::post('approve', 'ScenicShopOrderController@approve');
            Route::post('refund', 'ScenicShopOrderController@refund');
            Route::post('verify', 'ScenicShopOrderController@verify');
        });

        Route::prefix('income')->group(function () {
            Route::get('data_overview', 'ScenicShopIncomeController@dataOverview');
            Route::get('sum', 'ScenicShopIncomeController@sum');
            Route::get('time_data', 'ScenicShopIncomeController@timeData');
            Route::post('order_list', 'ScenicShopIncomeController@incomeOrderList');
        });
    });

    Route::prefix('ticket')->group(function () {
        Route::get('category_options', 'ScenicTicketController@categoryOptions');
        Route::get('list', 'ScenicTicketController@list');
        Route::get('detail', 'ScenicTicketController@detail');
    });

    Route::prefix('order')->group(function () {
        Route::get('payment_amount', 'ScenicOrderController@paymentAmount');
        Route::post('submit', 'ScenicOrderController@submit');
        Route::post('pay_params', 'ScenicOrderController@payParams');
        Route::get('total', 'ScenicOrderController@total');
        Route::get('list', 'ScenicOrderController@list');
        Route::get('search', 'ScenicOrderController@search');
        Route::get('detail', 'ScenicOrderController@detail');
        Route::get('verify_code', 'ScenicOrderController@verifyCode');
        Route::post('refund', 'ScenicOrderController@refund');
        Route::post('cancel', 'ScenicOrderController@cancel');
        Route::post('delete', 'ScenicOrderController@delete');
    });
});

Route::prefix('hotel')->group(function () {
    Route::get('category_options', 'HotelController@categoryOptions');
    Route::get('list', 'HotelController@list');
    Route::get('search', 'HotelController@search');
    Route::get('nearby_list', 'HotelController@nearbyList');
    Route::post('media_relative_list', 'HotelController@mediaRelativeList');
    Route::get('detail', 'HotelController@detail');
    Route::get('options', 'HotelController@options');
    Route::post('add', 'HotelController@add');
    Route::post('edit', 'HotelController@edit');
    Route::post('delete', 'HotelController@delete');
    Route::post('homestay_list', 'HotelController@homestayList');
    Route::get('shop_options', 'HotelController@shopOptions');

    Route::prefix('question')->group(function () {
        Route::get('list', 'HotelQAController@questionList');
        Route::get('detail', 'HotelQAController@questionDetail');
        Route::post('add', 'HotelQAController@addQuestion');
        Route::post('delete', 'HotelQAController@deleteQuestion');
    });

    Route::prefix('answer')->group(function () {
        Route::get('list', 'HotelQAController@answerList');
        Route::post('add', 'HotelQAController@addAnswer');
        Route::post('delete', 'HotelQAController@deleteAnswer');
    });

    Route::prefix('evaluation')->group(function () {
        Route::get('list', 'HotelEvaluationController@list');
        Route::post('add', 'HotelEvaluationController@add');
        Route::post('delete', 'HotelEvaluationController@delete');
    });

    Route::prefix('merchant')->group(function () {
        Route::post('settle_in', 'HotelMerchantController@settleIn');
        Route::get('status', 'HotelMerchantController@status');
        Route::get('info', 'HotelMerchantController@info');
        Route::post('delete', 'HotelMerchantController@delete');
    });

    Route::prefix('shop')->group(function () {
        Route::get('info', 'HotelShopController@shopInfo');
        Route::post('update_info', 'HotelShopController@updateShopInfo');

        Route::prefix('deposit')->group(function () {
            Route::post('pay_params', 'HotelShopDepositController@payParams');
            Route::get('info', 'HotelShopDepositController@depositInfo');
            Route::get('log_list', 'HotelShopDepositController@changeLogList');
        });

        Route::prefix('manager')->group(function () {
            Route::get('list', 'HotelShopManagerController@list');
            Route::get('detail', 'HotelShopManagerController@detail');
            Route::post('add', 'HotelShopManagerController@add');
            Route::post('edit', 'HotelShopManagerController@edit');
            Route::post('delete', 'HotelShopManagerController@delete');
        });

        Route::prefix('hotel')->group(function () {
            Route::get('totals', 'ShopHotelController@totals');
            Route::get('list', 'ShopHotelController@list');
            Route::post('apply', 'ShopHotelController@apply');
            Route::post('delete', 'ShopHotelController@delete');
            Route::get('options', 'ShopHotelController@options');
        });

        Route::prefix('room')->group(function () {
            Route::get('totals', 'ShopHotelRoomController@totals');
            Route::get('list', 'ShopHotelRoomController@list');
            Route::post('add', 'ShopHotelRoomController@add');
            Route::post('edit', 'ShopHotelRoomController@edit');
            Route::post('up', 'ShopHotelRoomController@up');
            Route::post('down', 'ShopHotelRoomController@down');
            Route::post('delete', 'ShopHotelRoomController@delete');
        });

        Route::prefix('order')->group(function () {
            Route::get('total', 'HotelShopOrderController@total');
            Route::get('list', 'HotelShopOrderController@list');
            Route::get('detail', 'HotelShopOrderController@detail');
            Route::post('approve', 'HotelShopOrderController@approve');
            Route::post('refund', 'HotelShopOrderController@refund');
            Route::post('verify', 'HotelShopOrderController@verify');
        });

        Route::prefix('income')->group(function () {
            Route::get('data_overview', 'HotelShopIncomeController@dataOverview');
            Route::get('sum', 'HotelShopIncomeController@sum');
            Route::get('time_data', 'HotelShopIncomeController@timeData');
            Route::post('order_list', 'HotelShopIncomeController@incomeOrderList');
        });
    });

    Route::prefix('room')->group(function () {
        Route::get('type_options', 'HotelRoomController@typeOptions');
        Route::get('list', 'HotelRoomController@list');
        Route::get('detail', 'HotelRoomController@detail');
    });

    Route::prefix('order')->group(function () {
        Route::get('payment_amount', 'HotelOrderController@paymentAmount');
        Route::post('submit', 'HotelOrderController@submit');
        Route::post('pay_params', 'HotelOrderController@payParams');
        Route::get('total', 'HotelOrderController@total');
        Route::get('list', 'HotelOrderController@list');
        Route::get('search', 'HotelOrderController@search');
        Route::get('detail', 'HotelOrderController@detail');
        Route::get('verify_code', 'HotelOrderController@verifyCode');
        Route::post('refund', 'HotelOrderController@refund');
        Route::post('cancel', 'HotelOrderController@cancel');
        Route::post('delete', 'HotelOrderController@delete');
    });
});

Route::prefix('catering')->group(function () {
    Route::prefix('restaurant')->group(function () {
        Route::get('category_options', 'RestaurantController@categoryOptions');
        Route::get('list', 'RestaurantController@list');
        Route::get('search', 'RestaurantController@search');
        Route::post('media_relative_list', 'RestaurantController@mediaRelativeList');
        Route::get('detail', 'RestaurantController@detail');
        Route::get('options', 'RestaurantController@options');
        Route::post('add', 'RestaurantController@add');
        Route::post('edit', 'RestaurantController@edit');
        Route::post('delete', 'RestaurantController@delete');
        Route::get('shop_options', 'RestaurantController@shopOptions');
    });

    Route::prefix('question')->group(function () {
        Route::get('summary', 'CateringQAController@questionSummary');
        Route::get('list', 'CateringQAController@questionList');
        Route::get('list', 'CateringQAController@questionList');
        Route::get('detail', 'CateringQAController@questionDetail');
        Route::post('add', 'CateringQAController@addQuestion');
        Route::post('delete', 'CateringQAController@deleteQuestion');
    });

    Route::prefix('answer')->group(function () {
        Route::get('list', 'CateringQAController@answerList');
        Route::post('add', 'CateringQAController@addAnswer');
        Route::post('delete', 'CateringQAController@deleteAnswer');
    });

    Route::prefix('evaluation')->group(function () {
        Route::get('list', 'CateringEvaluationController@list');
        Route::post('add', 'CateringEvaluationController@add');
        Route::post('delete', 'CateringEvaluationController@delete');
    });

    Route::prefix('merchant')->group(function () {
        Route::post('settle_in', 'CateringMerchantController@settleIn');
        Route::get('status', 'CateringMerchantController@status');
        Route::get('info', 'CateringMerchantController@info');
        Route::post('delete', 'CateringMerchantController@delete');
    });

    Route::prefix('shop')->group(function () {
        Route::get('info', 'CateringShopController@shopInfo');
        Route::post('update_info', 'CateringShopController@updateShopInfo');

        Route::prefix('deposit')->group(function () {
            Route::post('pay_params', 'CateringShopDepositController@payParams');
            Route::get('info', 'CateringShopDepositController@depositInfo');
            Route::get('log_list', 'CateringShopDepositController@changeLogList');
        });

        Route::prefix('manager')->group(function () {
            Route::get('list', 'CateringShopManagerController@list');
            Route::get('detail', 'CateringShopManagerController@detail');
            Route::post('add', 'CateringShopManagerController@add');
            Route::post('edit', 'CateringShopManagerController@edit');
            Route::post('delete', 'CateringShopManagerController@delete');
        });

        Route::prefix('restaurant')->group(function () {
            Route::get('totals', 'ShopRestaurantController@totals');
            Route::get('list', 'ShopRestaurantController@list');
            Route::post('apply', 'ShopRestaurantController@apply');
            Route::post('delete', 'ShopRestaurantController@delete');
            Route::get('options', 'ShopRestaurantController@options');
        });

        Route::prefix('meal_ticket')->group(function () {
            Route::get('totals', 'ShopMealTicketController@totals');
            Route::get('list', 'ShopMealTicketController@list');
            Route::post('add', 'ShopMealTicketController@add');
            Route::post('edit', 'ShopMealTicketController@edit');
            Route::post('up', 'ShopMealTicketController@up');
            Route::post('down', 'ShopMealTicketController@down');
            Route::post('delete', 'ShopMealTicketController@delete');

            Route::prefix('order')->group(function () {
                Route::get('total', 'ShopMealTicketOrderController@total');
                Route::get('list', 'ShopMealTicketOrderController@list');
                Route::get('detail', 'ShopMealTicketOrderController@detail');
                Route::post('approve', 'ShopMealTicketOrderController@approve');
                Route::post('refund', 'ShopMealTicketOrderController@refund');
                Route::post('verify', 'ShopMealTicketOrderController@verify');
            });
        });

        Route::prefix('set_meal')->group(function () {
            Route::get('totals', 'ShopSetMealController@totals');
            Route::get('list', 'ShopSetMealController@list');
            Route::post('add', 'ShopSetMealController@add');
            Route::post('edit', 'ShopSetMealController@edit');
            Route::post('up', 'ShopSetMealController@up');
            Route::post('down', 'ShopSetMealController@down');
            Route::post('delete', 'ShopSetMealController@delete');

            Route::prefix('order')->group(function () {
                Route::get('total', 'ShopSetMealOrderController@total');
                Route::get('list', 'ShopSetMealOrderController@list');
                Route::get('detail', 'ShopSetMealOrderController@detail');
                Route::post('approve', 'ShopSetMealOrderController@approve');
                Route::post('refund', 'ShopSetMealOrderController@refund');
                Route::post('verify', 'ShopSetMealOrderController@verify');
            });
        });

        Route::prefix('income')->group(function () {
            Route::get('data_overview', 'CateringShopIncomeController@dataOverview');
            Route::get('sum', 'CateringShopIncomeController@sum');
            Route::get('time_data', 'CateringShopIncomeController@timeData');
            Route::post('order_list', 'CateringShopIncomeController@incomeOrderList');
        });
    });

    Route::prefix('meal_ticket')->group(function () {
        Route::get('list', 'MealTicketController@list');
        Route::get('detail', 'MealTicketController@detail');

        Route::prefix('order')->group(function () {
            Route::get('payment_amount', 'MealTicketOrderController@paymentAmount');
            Route::post('submit', 'MealTicketOrderController@submit');
            Route::post('pay_params', 'MealTicketOrderController@payParams');
            Route::get('total', 'MealTicketOrderController@total');
            Route::get('list', 'MealTicketOrderController@list');
            Route::get('search', 'MealTicketOrderController@search');
            Route::get('detail', 'MealTicketOrderController@detail');
            Route::get('verify_code', 'MealTicketOrderController@verifyCode');
            Route::post('refund', 'MealTicketOrderController@refund');
            Route::post('cancel', 'MealTicketOrderController@cancel');
            Route::post('delete', 'MealTicketOrderController@delete');
        });
    });

    Route::prefix('set_meal')->group(function () {
        Route::get('list', 'SetMealController@list');
        Route::get('detail', 'SetMealController@detail');

        Route::prefix('order')->group(function () {
            Route::get('payment_amount', 'SetMealOrderController@paymentAmount');
            Route::post('submit', 'SetMealOrderController@submit');
            Route::post('pay_params', 'SetMealOrderController@payParams');
            Route::get('total', 'SetMealOrderController@total');
            Route::get('list', 'SetMealOrderController@list');
            Route::get('search', 'SetMealOrderController@search');
            Route::get('detail', 'SetMealOrderController@detail');
            Route::get('verify_code', 'SetMealOrderController@verifyCode');
            Route::post('refund', 'SetMealOrderController@refund');
            Route::post('cancel', 'SetMealOrderController@cancel');
            Route::post('delete', 'SetMealOrderController@delete');
        });
    });
});

Route::prefix('coupon')->group(function () {
    Route::post('receive', 'CouponController@receiveCoupon');
    Route::get('user_list', 'CouponController@userCouponList');
});

Route::prefix('banner')->group(function () {
    Route::get('pop', 'BannerController@pop');
    Route::get('list', 'BannerController@list');
});

Route::prefix('task')->group(function () {
    Route::get('list', 'TaskController@list');
    Route::post('pick', 'TaskController@pickTask');
    Route::post('cancel', 'TaskController@cancelTask');
    Route::get('user_data', 'TaskController@userTaskData');
    Route::get('user_list', 'TaskController@userTasklist');
    Route::get('detail', 'TaskController@detail');
    Route::get('status', 'TaskController@status');
});

Route::prefix('trip_type')->group(function () {
    Route::get('hot_scenic_list', 'TripTypeController@hotScenicList');
    Route::get('lake_trip_list', 'TripTypeController@lakeTripList');
    Route::get('lake_cycle_list', 'TripTypeController@lakeCycleList');
    Route::get('lake_cycle_media_list', 'TripTypeController@lakeCycleMediaList');
    Route::get('night_trip_list', 'TripTypeController@nightTripList');
    Route::get('star_trip_list', 'TripTypeController@starTripList');
    Route::get('lake_homestay_list', 'TripTypeController@lakeHomestayList');
});

Route::prefix('mall')->group(function () {
    Route::get('product_list', 'MallController@list');
    Route::post('init_product_views', 'MallController@initViews');
});

Route::prefix('media')->group(function () {
    Route::get('top_list', 'MediaController@topList');
    Route::get('list', 'MediaController@list');
    Route::get('random_list', 'MediaController@randomList');
    Route::get('nearby_list', 'MediaController@nearbyList');
    Route::get('follow_list', 'MediaController@followList');
    Route::get('search', 'MediaController@search');
    Route::get('collect_list', 'MediaController@collectList');
    Route::get('like_list', 'MediaController@likeList');
    Route::get('product_relative_list', 'MediaController@productMediaList');

    Route::prefix('live')->group(function () {
        Route::post('create', 'LivePushController@createRoom');
        Route::get('room_status', 'LivePushController@roomStatus');
        Route::get('notice_room', 'LivePushController@noticeRoomInfo');
        Route::post('delete_notice_room', 'LivePushController@deleteNoticeRoom');
        Route::get('push_room', 'LivePushController@pushRoomInfo');
        Route::post('start', 'LivePushController@startLive');
        Route::post('stop', 'LivePushController@stopLive');
        Route::get('list', 'LivePlayController@roomList');
        Route::get('search', 'LivePlayController@search');
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
        Route::get('search', 'ShortVideoController@search');
        Route::get('user_list', 'ShortVideoController@userVideoList');
        Route::get('collect_list', 'ShortVideoController@collectVideoList');
        Route::get('like_list', 'ShortVideoController@likeVideoList');
        Route::post('create', 'ShortVideoController@createVideo');
        Route::post('toggle_private', 'ShortVideoController@togglePrivate');
        Route::post('delete', 'ShortVideoController@deleteVideo');
        Route::post('share', 'ShortVideoController@share');
        Route::post('toggle_like', 'ShortVideoController@toggleLikeStatus');
        Route::post('toggle_collect', 'ShortVideoController@toggleCollectStatus');
        Route::post('comment', 'ShortVideoController@comment');
        Route::get('comment_list', 'ShortVideoController@getCommentList');
        Route::get('reply_comment_list', 'ShortVideoController@getReplyCommentList');
        Route::post('delete_comment', 'ShortVideoController@deleteComment');
        Route::post('create_history', 'ShortVideoController@createHistory');
        Route::post('create_temp_video', 'ShortVideoController@createTempVideo');
    });

    Route::prefix('tourism_note')->group(function () {
        Route::get('list', 'TourismNoteController@list');
        Route::get('search', 'TourismNoteController@search');
        Route::get('user_list', 'TourismNoteController@userNoteList');
        Route::get('collect_list', 'TourismNoteController@collectNoteList');
        Route::get('like_list', 'TourismNoteController@likeNoteList');
        Route::get('detail', 'TourismNoteController@detail');
        Route::post('create', 'TourismNoteController@createNote');
        Route::post('toggle_private', 'TourismNoteController@togglePrivate');
        Route::post('delete', 'TourismNoteController@deleteNote');
        Route::post('share', 'TourismNoteController@share');
        Route::post('toggle_like', 'TourismNoteController@toggleLikeStatus');
        Route::post('toggle_collect', 'TourismNoteController@toggleCollectionStatus');
        Route::post('comment', 'TourismNoteController@comment');
        Route::get('comment_list', 'TourismNoteController@getCommentList');
        Route::get('reply_comment_list', 'TourismNoteController@getReplyCommentList');
        Route::post('delete_comment', 'TourismNoteController@deleteComment');
        Route::post('create_temp_note', 'TourismNoteController@createTempNote');
    });
});

Route::prefix('history')->group(function () {
    Route::get('media', 'HistoryController@mediaHistory');
    Route::get('product', 'HistoryController@productHistory');
});

Route::prefix('promoter')->group(function () {
    Route::get('achievement', 'PromoterController@achievement');
    Route::get('customer_data', 'PromoterController@customerData');
    Route::get('today_new_customer_list', 'PromoterController@todayNewCustomerList');
    Route::get('today_ordering_customer_list', 'PromoterController@todayOrderingCustomerList');
    Route::get('customer_list', 'PromoterController@customerList');
});

Route::prefix('shop_deposit')->group(function () {
    Route::get('info', 'ShopDepositController@accountInfo');
    Route::get('change_log_list', 'ShopDepositController@changeLogList');
});

Route::prefix('commission')->group(function () {
    Route::get('achievement', 'CommissionController@achievement');
    Route::post('order_list', 'CommissionController@commissionOrderList');
    Route::get('sum', 'CommissionController@sum');
    Route::get('time_data', 'CommissionController@timeData');
    Route::get('cash', 'CommissionController@cash');
});

Route::prefix('withdraw')->group(function () {
    Route::prefix('commission')->group(function () {
        Route::post('submit', 'CommissionWithdrawalController@submit');
        Route::get('record_list', 'CommissionWithdrawalController@recordList');
    });

    Route::prefix('income')->group(function () {
        Route::post('submit', 'IncomeWithdrawalController@submit');
        Route::get('record_list', 'IncomeWithdrawalController@recordList');
    });

    Route::prefix('reward')->group(function () {
        Route::post('submit', 'RewardWithdrawalController@submit');
        Route::get('record_list', 'RewardWithdrawalController@recordList');
    });
});

Route::prefix('account')->group(function () {
    Route::get('info', 'AccountController@accountInfo');
    Route::get('change_log_list', 'AccountController@changeLogList');
});

Route::prefix('bank_card')->group(function () {
    Route::get('detail', 'BankCardController@detail');
    Route::post('add', 'BankCardController@add');
    Route::post('edit', 'BankCardController@edit');
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
        Route::get('token_refresh', 'AuthController@refreshToken');
        Route::post('reset_password', 'AuthController@resetPassword');
        Route::get('base_info', 'AuthController@baseInfo');
        Route::post('update_base_info', 'AuthController@updateBaseInfo');
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
        Route::post('edit', 'UserController@edit');
        Route::post('delete', 'UserController@delete');
        Route::get('options', 'UserController@options');
        Route::get('normal_options', 'UserController@normalOptions');
        Route::post('bind_superior', 'UserController@bindSuperior');
        Route::post('delete_superior', 'UserController@deleteSuperior');
    });

    Route::prefix('auth_info')->group(function () {
        Route::post('list', 'AuthInfoController@list');
        Route::get('detail', 'AuthInfoController@detail');
        Route::post('approve', 'AuthInfoController@approve');
        Route::post('reject', 'AuthInfoController@reject');
        Route::post('delete', 'AuthInfoController@delete');
    });

    Route::prefix('banner')->group(function () {
        Route::post('list', 'BannerController@list');
        Route::get('detail', 'BannerController@detail');
        Route::post('add', 'BannerController@add');
        Route::post('edit', 'BannerController@edit');
        Route::post('edit_sort', 'BannerController@editSort');
        Route::post('up', 'BannerController@up');
        Route::post('down', 'BannerController@down');
        Route::post('delete', 'BannerController@delete');
    });

    Route::prefix('task')->group(function () {
        Route::post('list', 'TaskController@list');
        Route::get('detail', 'TaskController@detail');
        Route::post('add', 'TaskController@add');
        Route::post('edit', 'TaskController@edit');
        Route::post('up', 'TaskController@up');
        Route::post('down', 'TaskController@down');
        Route::post('delete', 'TaskController@delete');
    });

    Route::prefix('trip_type')->group(function () {
        Route::prefix('hot_scenic')->group(function () {
            Route::post('list', 'HotScenicController@list');
            Route::get('detail', 'HotScenicController@detail');
            Route::post('add', 'HotScenicController@add');
            Route::post('edit', 'HotScenicController@edit');
            Route::post('edit_interested_number', 'HotScenicController@editInterestedNumber');
            Route::post('edit_sort', 'HotScenicController@editSort');
            Route::post('delete', 'HotScenicController@delete');
        });

        Route::prefix('lake_trip')->group(function () {
            Route::post('list', 'LakeTripController@list');
            Route::get('detail', 'LakeTripController@detail');
            Route::post('add', 'LakeTripController@add');
            Route::post('edit', 'LakeTripController@edit');
            Route::post('edit_sort', 'LakeTripController@editSort');
            Route::post('delete', 'LakeTripController@delete');
        });

        Route::prefix('lake_cycle')->group(function () {
            Route::post('list', 'LakeCycleController@list');
            Route::get('detail', 'LakeCycleController@detail');
            Route::post('add', 'LakeCycleController@add');
            Route::post('edit', 'LakeCycleController@edit');
            Route::post('edit_sort', 'LakeCycleController@editSort');
            Route::post('delete', 'LakeCycleController@delete');

            Route::prefix('media')->group(function () {
                Route::post('list', 'LakeCycleMediaController@list');
                Route::post('add', 'LakeCycleMediaController@add');
                Route::post('edit_sort', 'LakeCycleMediaController@editSort');
                Route::post('delete', 'LakeCycleMediaController@delete');
            });
        });

        Route::prefix('night_trip')->group(function () {
            Route::post('list', 'NightTripController@list');
            Route::get('detail', 'NightTripController@detail');
            Route::post('add', 'NightTripController@add');
            Route::post('edit', 'NightTripController@edit');
            Route::post('edit_sort', 'NightTripController@editSort');
            Route::post('delete', 'NightTripController@delete');
        });

        Route::prefix('star_trip')->group(function () {
            Route::post('list', 'StarTripController@list');
            Route::get('detail', 'StarTripController@detail');
            Route::post('add', 'StarTripController@add');
            Route::post('edit', 'StarTripController@edit');
            Route::post('edit_sort', 'StarTripController@editSort');
            Route::post('delete', 'StarTripController@delete');
        });

        Route::prefix('lake_homestay')->group(function () {
            Route::post('list', 'LakeHomestayController@list');
            Route::get('detail', 'LakeHomestayController@detail');
            Route::post('add', 'LakeHomestayController@add');
            Route::post('edit', 'LakeHomestayController@edit');
            Route::post('edit_sort', 'LakeHomestayController@editSort');
            Route::post('delete', 'LakeHomestayController@delete');
        });
    });

    Route::prefix('merchant')->group(function () {
        Route::post('list', 'MerchantController@list');
        Route::get('detail', 'MerchantController@detail');
        Route::post('approve', 'MerchantController@approve');
        Route::post('reject', 'MerchantController@reject');
        Route::get('options', 'MerchantController@options');
    });

    Route::prefix('shop')->group(function () {
        Route::prefix('category')->group(function () {
            Route::post('list', 'ShopCategoryController@list');
            Route::get('detail', 'ShopCategoryController@detail');
            Route::post('add', 'ShopCategoryController@add');
            Route::post('edit', 'ShopCategoryController@edit');
            Route::post('delete', 'ShopCategoryController@delete');
            Route::get('options', 'ShopCategoryController@options');
            Route::post('edit_sort', 'ShopCategoryController@editSort');
            Route::post('edit_visible', 'ShopCategoryController@editVisible');
        });

        Route::post('list', 'ShopController@list');
        Route::get('detail', 'ShopController@detail');
        Route::post('deposit_payment_logs', 'ShopController@depositPaymentLogs');
        Route::get('options', 'ShopController@options');
    });

    Route::prefix('express')->group(function () {
        Route::post('list', 'ExpressController@list');
        Route::get('detail', 'ExpressController@detail');
        Route::post('add', 'ExpressController@add');
        Route::post('edit', 'ExpressController@edit');
        Route::post('delete', 'ExpressController@delete');
        Route::get('options', 'ExpressController@options');
    });

    Route::prefix('freight_template')->group(function () {
        Route::post('list', 'FreightTemplateController@list');
        Route::get('detail', 'FreightTemplateController@detail');
        Route::post('add', 'FreightTemplateController@add');
        Route::post('edit', 'FreightTemplateController@edit');
        Route::post('delete', 'FreightTemplateController@delete');
        Route::get('options', 'FreightTemplateController@options');
    });

    Route::prefix('refund_address')->group(function () {
        Route::post('list', 'RefundAddressController@list');
        Route::get('detail', 'RefundAddressController@detail');
        Route::post('add', 'RefundAddressController@add');
        Route::post('edit', 'RefundAddressController@edit');
        Route::post('delete', 'RefundAddressController@delete');
        Route::get('options', 'RefundAddressController@options');
    });

    Route::prefix('pickup_address')->group(function () {
        Route::post('list', 'PickupAddressController@list');
        Route::get('detail', 'PickupAddressController@detail');
        Route::post('add', 'PickupAddressController@add');
        Route::post('edit', 'PickupAddressController@edit');
        Route::post('delete', 'PickupAddressController@delete');
        Route::get('options', 'PickupAddressController@options');
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
        Route::post('add', 'GoodsController@add');
        Route::post('edit', 'GoodsController@edit');
        Route::post('edit_commission', 'GoodsController@editCommission');
        Route::post('edit_views', 'GoodsController@editViews');
        Route::post('approve', 'GoodsController@approve');
        Route::post('reject', 'GoodsController@reject');
        Route::post('down', 'GoodsController@down');
        Route::post('delete', 'GoodsController@delete');
        Route::get('options', 'GoodsController@options');
        Route::get('self_options', 'GoodsController@selfSupportGoodsOptions');
        Route::get('normal_options', 'GoodsController@normalGoodsOptions');
    });

    Route::prefix('gift')->group(function () {
        Route::prefix('type')->group(function () {
            Route::post('list', 'GiftTypeController@list');
            Route::get('detail', 'GiftTypeController@detail');
            Route::post('add', 'GiftTypeController@add');
            Route::post('edit', 'GiftTypeController@edit');
            Route::post('edit_sort', 'GiftTypeController@editSort');
            Route::post('edit_status', 'GiftTypeController@editStatus');
            Route::post('delete', 'GiftTypeController@delete');
            Route::get('options', 'GiftTypeController@options');
        });

        Route::post('list', 'GiftGoodsController@list');
        Route::post('add', 'GiftGoodsController@add');
        Route::post('edit_duration', 'GiftGoodsController@editDuration');
        Route::post('delete', 'GiftGoodsController@delete');
    });

    Route::prefix('promoter')->group(function () {
        Route::post('list', 'PromoterController@list');
        Route::get('detail', 'PromoterController@detail');
        Route::post('add', 'PromoterController@add');
        Route::post('edit', 'PromoterController@edit');
        Route::post('delete', 'PromoterController@delete');
        Route::get('options', 'PromoterController@options');
        Route::post('top_list', 'PromoterController@topPromoterList');
        Route::post('update_list', 'PromoterController@updateList');
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
        Route::post('edit_views', 'ScenicController@editViews');
        Route::post('delete', 'ScenicController@delete');
        Route::get('options', 'ScenicController@options');

        Route::prefix('merchant')->group(function () {
            Route::post('list', 'ScenicMerchantController@list');
            Route::get('detail', 'ScenicMerchantController@detail');
            Route::post('approve', 'ScenicMerchantController@approve');
            Route::post('reject', 'ScenicMerchantController@reject');
        });

        Route::prefix('shop')->group(function () {
            Route::post('list', 'ScenicShopController@list');
            Route::get('detail', 'ScenicShopController@detail');

            Route::prefix('scenic')->group(function () {
                Route::post('list', 'ShopScenicController@list');
                Route::post('approve', 'ShopScenicController@approve');
                Route::post('reject', 'ShopScenicController@reject');
                Route::post('delete', 'ShopScenicController@delete');
            });
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
            Route::post('edit_commission', 'ScenicTicketController@editCommission');
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
        Route::post('edit_views', 'HotelController@editViews');
        Route::post('delete', 'HotelController@delete');
        Route::get('options', 'HotelController@options');
        Route::get('homestay_options', 'HotelController@homestayOptions');

        Route::prefix('room_type')->group(function () {
            Route::post('list', 'HotelRoomTypeController@list');
            Route::get('detail', 'HotelRoomTypeController@detail');
            Route::post('add', 'HotelRoomTypeController@add');
            Route::post('edit', 'HotelRoomTypeController@edit');
            Route::post('delete', 'HotelRoomTypeController@delete');
            Route::get('options', 'HotelRoomTypeController@options');
        });

        Route::prefix('merchant')->group(function () {
            Route::post('list', 'HotelMerchantController@list');
            Route::get('detail', 'HotelMerchantController@detail');
            Route::post('approve', 'HotelMerchantController@approve');
            Route::post('reject', 'HotelMerchantController@reject');
        });

        Route::prefix('shop')->group(function () {
            Route::post('list', 'HotelShopController@list');
            Route::get('detail', 'HotelShopController@detail');

            Route::prefix('hotel')->group(function () {
                Route::post('list', 'ShopHotelController@list');
                Route::post('approve', 'ShopHotelController@approve');
                Route::post('reject', 'ShopHotelController@reject');
                Route::post('delete', 'ShopHotelController@delete');
            });
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
            Route::post('edit_commission', 'HotelRoomController@editCommission');
            Route::post('approve', 'HotelRoomController@approve');
            Route::post('reject', 'HotelRoomController@reject');
            Route::post('delete', 'HotelRoomController@delete');
        });
    });

    Route::prefix('catering')->group(function () {
        Route::prefix('merchant')->group(function () {
            Route::post('list', 'CateringMerchantController@list');
            Route::get('detail', 'CateringMerchantController@detail');
            Route::post('approve', 'CateringMerchantController@approve');
            Route::post('reject', 'CateringMerchantController@reject');
        });

        Route::prefix('shop')->group(function () {
            Route::post('list', 'CateringShopController@list');
            Route::get('detail', 'CateringShopController@detail');

            Route::prefix('restaurant')->group(function () {
                Route::post('list', 'ShopRestaurantController@list');
                Route::post('approve', 'ShopRestaurantController@approve');
                Route::post('reject', 'ShopRestaurantController@reject');
                Route::post('delete', 'ShopRestaurantController@delete');
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
            Route::post('edit_views', 'RestaurantController@editViews');
            Route::post('delete', 'RestaurantController@delete');
            Route::get('options', 'RestaurantController@options');
        });

        Route::prefix('meal_ticket')->group(function () {
            Route::post('list', 'MealTicketController@list');
            Route::get('detail', 'MealTicketController@detail');
            Route::post('edit_commission', 'MealTicketController@editCommission');
            Route::post('approve', 'MealTicketController@approve');
            Route::post('reject', 'MealTicketController@reject');
            Route::post('delete', 'MealTicketController@delete');
        });

        Route::prefix('set_meal')->group(function () {
            Route::post('list', 'SetMealController@list');
            Route::get('detail', 'SetMealController@detail');
            Route::post('edit_commission', 'SetMealController@editCommission');
            Route::post('approve', 'SetMealController@approve');
            Route::post('reject', 'SetMealController@reject');
            Route::post('delete', 'SetMealController@delete');
        });
    });

    Route::prefix('media')->group(function () {
        Route::prefix('short_video')->group(function () {
            Route::post('list', 'ShortVideoController@list');
            Route::get('detail', 'ShortVideoController@detail');
            Route::post('add', 'ShortVideoController@add');
            Route::post('edit', 'ShortVideoController@edit');
            Route::post('edit_views', 'ShortVideoController@editViews');
            Route::get('options', 'ShortVideoController@options');
            Route::post('delete', 'ShortVideoController@delete');
        });

        Route::prefix('tourism_note')->group(function () {
            Route::post('list', 'TourismNoteController@list');
            Route::get('detail', 'TourismNoteController@detail');
            Route::post('add', 'TourismNoteController@add');
            Route::post('edit', 'TourismNoteController@edit');
            Route::post('edit_views', 'TourismNoteController@editViews');
            Route::get('options', 'TourismNoteController@options');
            Route::post('delete', 'TourismNoteController@delete');
        });

        Route::prefix('top')->group(function () {
            Route::post('list', 'TopMediaController@list');
            Route::get('detail', 'TopMediaController@detail');
            Route::post('add', 'TopMediaController@add');
            Route::post('edit', 'TopMediaController@edit');
            Route::post('delete', 'TopMediaController@delete');
        });
    });
});
