<?php

namespace App\Services;

use App\Models\ProviderHotel;
use App\Utils\Inputs\Admin\ProviderHotelListInput;
use App\Utils\Inputs\StatusPageInput;

class ProviderHotelService extends BaseService
{
    public function getListTotal($userId, $status)
    {
        return ProviderHotel::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getUserHotelList($userId, StatusPageInput $input, $columns = ['*'])
    {
        return ProviderHotel::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserHotelById($userId, $id, $columns = ['*'])
    {
        return ProviderHotel::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getHotelByHotelId($userId, $hotelId, $columns = ['*'])
    {
        return ProviderHotel::query()->where('user_id', $userId)->where('hotel_id', $hotelId)->first($columns);
    }

    public function getHotelList(ProviderHotelListInput $input, $columns = ['*'])
    {
        $query = ProviderHotel::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getHotelById($id, $columns = ['*'])
    {
        return ProviderHotel::query()->find($id, $columns);
    }

    public function getUserHotelOptions($userId, $columns = ['*'])
    {
        return ProviderHotel::query()->where('user_id', $userId)->get($columns);
    }
}
