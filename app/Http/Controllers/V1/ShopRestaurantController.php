<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Catering\Restaurant;
use App\Models\ShopRestaurant;
use App\Services\RestaurantService;
use App\Services\ShopRestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ShopRestaurantController extends Controller
{
    public function totals()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            ShopRestaurantService::getInstance()->getListTotal($shopId, 1),
            ShopRestaurantService::getInstance()->getListTotal($shopId, 0),
            ShopRestaurantService::getInstance()->getListTotal($shopId, 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'restaurant_id', 'status', 'failure_reason', 'created_at', 'updated_at'];

        $page = ShopRestaurantService::getInstance()->getRestaurantPage($shopId, $input, $columns);
        $shopRestaurantList = collect($page->items());

        $restaurantIds = $shopRestaurantList->pluck('restaurant_id')->toArray();
        $restaurantList = RestaurantService::getInstance()->getListByIds($restaurantIds)->keyBy('id');

        $list = $shopRestaurantList->map(function (ShopRestaurant $shopRestaurant) use ($restaurantList) {
            /** @var Restaurant $restaurant */
            $restaurant = $restaurantList->get($shopRestaurant->restaurant_id);
            $shopRestaurant['restaurant_cover'] = $restaurant->cover;
            $shopRestaurant['restaurant_name'] = $restaurant->name;
            $shopRestaurant['restaurant_address'] = $restaurant->address;
            return $shopRestaurant;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function apply()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $restaurantIds = $this->verifyArrayNotEmpty('restaurantIds');

        $userShopId = $this->user()->cateringShop->id;
        $userShopManagerList = $this->user()->cateringShopManagerList;
        if ($userShopId != $shopId && $userShopManagerList->isEmpty()) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加餐饮门店');
        }

        ShopRestaurantService::getInstance()->createRestaurantList($shopId, $restaurantIds);
        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $restaurant = ShopRestaurantService::getInstance()->getShopRestaurantById($shopId, $id);
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '餐饮门店不存在');
        }

        $restaurant->delete();

        return $this->success();
    }

    public function options()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $restaurantIds = ShopRestaurantService::getInstance()
            ->getShopRestaurantOptions($shopId)
            ->pluck('restaurant_id')
            ->toArray();
        $restaurantOptions = RestaurantService::getInstance()->getListByIds($restaurantIds, ['id', 'name']);

        return $this->success($restaurantOptions);
    }
}
