<?php

namespace App\Services;

use App\Models\ProviderRestaurant;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ProviderRestaurantService extends BaseService
{
    public function getListTotal($userId, $status)
    {
        return ProviderRestaurant::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getList(StatusPageInput $input, $columns = ['*'])
    {
        $query = ProviderRestaurant::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function createRestaurants($userId, $providerId, array $restaurantIds)
    {
        foreach ($restaurantIds as $restaurantId) {
            $restaurant = $this->getUserRestaurant($userId, $restaurantId);
            if (!is_null($restaurant)) {
                $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '包含已添加门店，请重试');
            }

            $restaurant = ProviderRestaurant::new();
            $restaurant->user_id = $userId;
            $restaurant->provider_id = $providerId;
            $restaurant->restaurant_id = $restaurantId;
            $restaurant->save();
        }
    }

    public function getRestaurant($id, $columns = ['*'])
    {
        $restaurant = ProviderRestaurant::query()->find($id, $columns);
        if (is_null($restaurant)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '当前服务商门店不存在');
        }
        return $restaurant;
    }

    public function getUserList($userId, StatusPageInput $input, $columns = ['*'])
    {
        return ProviderRestaurant::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserRestaurant($userId, $id, $columns = ['*'])
    {
        return ProviderRestaurant::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getUserRestaurantByRestaurantId($userId, $restaurantId, $columns = ['*'])
    {
        return ProviderRestaurant::query()->where('user_id', $userId)->where('restaurant_id', $restaurantId)->first($columns);
    }

    public function getOptions($userId, $columns = ['*'])
    {
        return ProviderRestaurant::query()->where('user_id', $userId)->get($columns);
    }
}
