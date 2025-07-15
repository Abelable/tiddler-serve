<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShopHotel;
use App\Models\Hotel;
use App\Services\HotelService;
use App\Services\ShopHotelService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ShopHotelController extends Controller
{
    public function totals()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            ShopHotelService::getInstance()->getListTotal($shopId, 1),
            ShopHotelService::getInstance()->getListTotal($shopId, 0),
            ShopHotelService::getInstance()->getListTotal($shopId, 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $columns = ['id', 'hotel_id', 'status', 'failure_reason', 'created_at', 'updated_at'];

        $page = ShopHotelService::getInstance()->getHotelPage($shopId, $input, $columns);
        $shopHotelList = collect($page->items());

        $hotelIds = $shopHotelList->pluck('hotel_id')->toArray();
        $hotelList = HotelService::getInstance()
            ->getHotelListByIds($hotelIds, ['id', 'name', 'cover', 'address'])
            ->keyBy('id');

        $list = $shopHotelList->map(function (ShopHotel $shopHotel) use ($hotelList) {
            /** @var Hotel $hotel */
            $hotel = $hotelList->get($shopHotel->hotel_id);
            $shopHotel['hotel_cover'] = $hotel->cover;
            $shopHotel['hotel_name'] = $hotel->name;
            $shopHotel['hotel_address'] = $hotel->address;
            return $shopHotel;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function apply()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $hotelIds = $this->verifyArrayNotEmpty('hotelIds');

        $userShopId = $this->user()->hotelShop->id;
        $userShopManagerList = $this->user()->hotelShopManagerList;
        if ($userShopId != $shopId && $userShopManagerList->isEmpty()) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加酒店');
        }

        ShopHotelService::getInstance()->createHotelList($shopId, $hotelIds);
        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $hotel = ShopHotelService::getInstance()->getShopHotelById($shopId, $id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '酒店不存在');
        }

        $hotel->delete();

        return $this->success();
    }

    public function options()
    {
        $shopId = $this->verifyRequiredId('shopId');

        $hotelIds = ShopHotelService::getInstance()
            ->getShopHotelOptions($shopId)
            ->pluck('hotel_id')
            ->toArray();
        $hotelOptions = HotelService::getInstance()->getHotelListByIds($hotelIds, ['id', 'name']);

        return $this->success($hotelOptions);
    }
}
