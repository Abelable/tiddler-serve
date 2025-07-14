<?php

namespace App\Services;

use App\Models\ShopHotel;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ShopHotelService extends BaseService
{
    public function getListTotal($shopId, $status)
    {
        return ShopHotel::query()
            ->where('shop_id', $shopId)
            ->where('status', $status)
            ->count();
    }

    public function getHotelPage($shopId, StatusPageInput $input, $columns = ['*'])
    {
        return ShopHotel::query()
            ->where('shop_id', $shopId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getHotelList($shopId, array $statusList, $columns = ['*'])
    {
        return ShopHotel::query()
            ->where('shop_id', $shopId)
            ->whereIn('status', $statusList)
            ->get($columns);
    }

    public function getShopHotelById($shopId, $id, $columns = ['*'])
    {
        return ShopHotel::query()->where('shop_id', $shopId)->find($id, $columns);
    }

    public function getByHotelId($shopId, $hotelId, $columns = ['*'])
    {
        return ShopHotel::query()
            ->where('shop_id', $shopId)
            ->where('hotel_id', $hotelId)
            ->first($columns);
    }

    public function getAdminHotelPage(StatusPageInput $input, $columns = ['*'])
    {
        $query = ShopHotel::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query
            ->orderByRaw("FIELD(status, 0) DESC")
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getHotelById($id, $columns = ['*'])
    {
        return ShopHotel::query()->find($id, $columns);
    }

    public function getShopHotelOptions($shopId, $columns = ['*'])
    {
        return ShopHotel::query()->where('shop_id', $shopId)->get($columns);
    }

    public function createHotelList($shopId, array $hotelIds)
    {
        foreach ($hotelIds as $hotelId) {
            $hotel = $this->getByHotelId($shopId, $hotelId);
            if (!is_null($hotel)) {
                $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '包含已添加酒店，请重试');
            }

            $hotel = ShopHotel::new();
            $hotel->shop_id = $shopId;
            $hotel->hotel_id = $hotelId;
            $hotel->save();
        }
    }
}
