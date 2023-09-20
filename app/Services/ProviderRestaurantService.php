<?php

namespace App\Services;

use App\Models\ProviderRestaurant;
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

    public function getRestaurant($id, $columns = ['*'])
    {
        return ProviderRestaurant::query()->find($id, $columns);
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

    public function getOptions($userId, $columns = ['*'])
    {
        return ProviderRestaurant::query()->where('user_id', $userId)->get($columns);
    }
}
