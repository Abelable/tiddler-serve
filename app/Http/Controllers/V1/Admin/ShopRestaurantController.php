<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catering\CateringMerchant;
use App\Models\Catering\CateringShop;
use App\Models\Restaurant;
use App\Models\ShopRestaurant;
use App\Services\Mall\Catering\CateringMerchantService;
use App\Services\Mall\Catering\CateringShopService;
use App\Services\RestaurantService;
use App\Services\ShopRestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ShopRestaurantController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var StatusPageInput  $input */
        $input = StatusPageInput::new();
        $page = ShopRestaurantService::getInstance()->getAdminRestaurantPage($input);
        $shopRestaurantList = collect($page->items());

        $shopIds = $shopRestaurantList->pluck('shop_id')->toArray();
        $shopList = CateringShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $merchantIds = $shopList->pluck('merchant_id')->toArray();
        $merchantList = CateringMerchantService::getInstance()->getMerchantListByIds($merchantIds)->keyBy('id');

        $restaurantIds = $shopRestaurantList->pluck('restaurant_id')->toArray();
        $restaurantList = RestaurantService::getInstance()
            ->getListByIds($restaurantIds, ['id', 'name', 'cover'])->keyBy('id');

        $list = $shopRestaurantList->map(function (ShopRestaurant $shopRestaurant) use (
            $restaurantList,
            $shopList,
            $merchantList
        ) {
            /** @var CateringShop $shop */
            $shop = $shopList->get($shopRestaurant->shop_id);
            $shopRestaurant['shop_logo'] = $shop->logo;
            $shopRestaurant['shop_name'] = $shop->name;

            /** @var CateringMerchant $merchant */
            $merchant = $merchantList->get($shop->merchant_id);
            $shopRestaurant['merchant_name'] = $merchant->company_name ?: $merchant->name;
            $shopRestaurant['business_license'] = $merchant->business_license_photo;
            $shopRestaurant['hygienic_license'] = $merchant->hygienic_license_photo;

            /** @var Restaurant $restaurant */
            $restaurant = $restaurantList->get($shopRestaurant->restaurant_id);
            $shopRestaurant['restaurant_name'] = $restaurant->name;
            $shopRestaurant['restaurant_cover'] = $restaurant->cover;

            return $shopRestaurant;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function approve()
    {
        $id = $this->verifyRequiredId('id');

        $restaurant = ShopRestaurantService::getInstance()->getRestaurantById($id);
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '餐饮门店不存在');
        }
        $restaurant->status = 1;
        $restaurant->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $restaurant = ShopRestaurantService::getInstance()->getRestaurantById($id);
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '餐饮门店不存在');
        }
        $restaurant->status = 2;
        $restaurant->failure_reason = $reason;
        $restaurant->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $restaurant = ShopRestaurantService::getInstance()->getRestaurantById($id);
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '餐饮门店不存在');
        }
        $restaurant->delete();

        return $this->success();
    }
}
