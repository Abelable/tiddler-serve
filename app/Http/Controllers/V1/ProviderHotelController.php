<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ProviderHotel;
use App\Models\Hotel;
use App\Services\ProviderHotelService;
use App\Services\HotelService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ProviderHotelController extends Controller
{
    public function listTotals()
    {
        return $this->success([
            ProviderHotelService::getInstance()->getListTotal($this->userId(), 1),
            ProviderHotelService::getInstance()->getListTotal($this->userId(), 0),
            ProviderHotelService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function list()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = ProviderHotelService::getInstance()->getUserHotelList($this->userId(), $input, ['id', 'hotel_id', 'status', 'failure_reason', 'created_at', 'updated_at']);
        $providerHotelList = collect($page->items());
        $hotelIds = $providerHotelList->pluck('hotel_id')->toArray();
        $hotelList = HotelService::getInstance()->getHotelListByIds($hotelIds, ['id', 'name', 'cover', 'grade', 'address'])->keyBy('id');
        $list = $providerHotelList->map(function (ProviderHotel $providerHotel) use ($hotelList) {
            /** @var Hotel $hotel */
            $hotel = $hotelList->get($providerHotel->hotel_id);
            $providerHotel['hotel_cover'] = $hotel->cover;
            $providerHotel['hotel_name'] = $hotel->name;
            $providerHotel['hotel_grade'] = $hotel->grade;
            $providerHotel['hotel_address'] = $hotel->address;
            return $providerHotel;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function apply()
    {
        $hotelIds = $this->verifyArrayNotEmpty('hotelIds');
        $hotelProvider = $this->user()->hotelProvider;
        if (is_null($hotelProvider)) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '暂无权限申请添加酒店');
        }
        ProviderHotelService::getInstance()->createHotels($this->userId(), $hotelProvider->id, $hotelIds);
        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $hotel = ProviderHotelService::getInstance()->getUserHotelById($this->userId(), $id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '供应商酒店不存在');
        }
        $hotel->delete();
        return $this->success();
    }

    public function options()
    {
        $hotelIds = ProviderHotelService::getInstance()->getUserHotelOptions($this->userId())->pluck('hotel_id')->toArray();
        $hotelOptions = HotelService::getInstance()->getHotelListByIds($hotelIds, ['id', 'name']);
        return $this->success($hotelOptions);
    }
}
