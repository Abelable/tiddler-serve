<?php

namespace App\Services;

use App\Models\ShopRestaurant;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ShopRestaurantService extends BaseService
{
    public function getListTotal($shopId, $status)
    {
        return ShopRestaurant::query()
            ->where('shop_id', $shopId)
            ->where('status', $status)
            ->count();
    }

    public function getRestaurantPage($shopId, StatusPageInput $input, $columns = ['*'])
    {
        return ShopRestaurant::query()
            ->where('shop_id', $shopId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRestaurantList($shopId, array $statusList, $columns = ['*'])
    {
        return ShopRestaurant::query()
            ->where('shop_id', $shopId)
            ->whereIn('status', $statusList)
            ->get($columns);
    }

    public function getShopRestaurantById($shopId, $id, $columns = ['*'])
    {
        return ShopRestaurant::query()->where('shop_id', $shopId)->where('id', $id)->first($columns);
    }

    public function getByRestaurantId($shopId, $restaurantId, $columns = ['*'])
    {
        return ShopRestaurant::query()
            ->where('shop_id', $shopId)
            ->where('restaurant_id', $restaurantId)
            ->first($columns);
    }

    public function getAdminRestaurantPage(StatusPageInput $input, $columns = ['*'])
    {
        $query = ShopRestaurant::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query
            ->orderByRaw("FIELD(status, 0) DESC")
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRestaurantById($id, $columns = ['*'])
    {
        return ShopRestaurant::query()->find($id, $columns);
    }

    public function getShopRestaurantOptions($shopId, $columns = ['*'])
    {
        return ShopRestaurant::query()->where('shop_id', $shopId)->get($columns);
    }

    public function createRestaurantList($shopId, array $restaurantIds)
    {
        foreach ($restaurantIds as $restaurantId) {
            $restaurant = $this->getByRestaurantId($shopId, $restaurantId);
            if (!is_null($restaurant)) {
                $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '包含已添加餐饮门店，请重试');
            }

            $restaurant = ShopRestaurant::new();
            $restaurant->shop_id = $shopId;
            $restaurant->restaurant_id = $restaurantId;
            $restaurant->save();
        }
    }
}
