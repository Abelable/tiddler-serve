<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProviderRestaurant;
use App\Models\Restaurant;
use App\Services\ProviderRestaurantService;
use App\Services\RestaurantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ProviderRestaurantController extends Controller
{
    public function listTotals()
    {
        return $this->success([
            ProviderRestaurantService::getInstance()->getListTotal($this->userId(), 1),
            ProviderRestaurantService::getInstance()->getListTotal($this->userId(), 0),
            ProviderRestaurantService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = ProviderRestaurantService::getInstance()->getUserList($this->userId(), $input);
        $providerRestaurantList = collect($page->items());
        $restaurantIds = $providerRestaurantList->pluck('restaurant_id')->toArray();
        $restaurantList = RestaurantService::getInstance()->getListByIds($restaurantIds, ['id', 'name', 'cover', 'address'])->keyBy('id');
        $list = $providerRestaurantList->map(function (ProviderRestaurant $providerRestaurant) use ($restaurantList) {
            /** @var Restaurant $restaurant */
            $restaurant = $restaurantList->get($providerRestaurant->restaurant_id);
            $providerRestaurant['restaurant_image'] = $restaurant->cover;
            $providerRestaurant['restaurant_name'] = $restaurant->name;
            $providerRestaurant['restaurant_address'] = $restaurant->address;
            return $providerRestaurant;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function apply()
    {
        $restaurantIds = $this->verifyArrayNotEmpty('restaurantIds');

        $cateringProvider = $this->user()->cateringProvider;
        if (is_null($cateringProvider)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加门店');
        }

        ProviderRestaurantService::getInstance()->createRestaurants($this->userId(), $cateringProvider->id, $restaurantIds);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $restaurant = ProviderRestaurantService::getInstance()->getUserRestaurant($this->userId(), $id);
        if (is_null($restaurant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '供应商门店不存在');
        }
        $restaurant->delete();
        return $this->success();
    }

    public function options()
    {
        $restaurantIds = ProviderRestaurantService::getInstance()->getOptions($this->userId())->pluck('restaurant_id')->toArray();
        $options = RestaurantService::getInstance()->getListByIds($restaurantIds, ['id', 'name']);
        return $this->success($options);
    }
}
