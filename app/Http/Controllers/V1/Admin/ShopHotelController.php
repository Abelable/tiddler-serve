<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mall\Hotel\Hotel;
use App\Models\Mall\Hotel\HotelMerchant;
use App\Models\Mall\Hotel\HotelShop;
use App\Models\Mall\Hotel\ShopHotel;
use App\Services\Mall\Hotel\HotelMerchantService;
use App\Services\Mall\Hotel\HotelService;
use App\Services\Mall\Hotel\HotelShopService;
use App\Services\Mall\Hotel\ShopHotelService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ShopHotelController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var StatusPageInput  $input */
        $input = StatusPageInput::new();
        $page = ShopHotelService::getInstance()->getAdminHotelPage($input);
        $shopHotelList = collect($page->items());

        $shopIds = $shopHotelList->pluck('shop_id')->toArray();
        $shopList = HotelShopService::getInstance()->getShopListByIds($shopIds)->keyBy('id');

        $merchantIds = $shopList->pluck('merchant_id')->toArray();
        $merchantList = HotelMerchantService::getInstance()->getMerchantListByIds($merchantIds)->keyBy('id');

        $hotelIds = $shopHotelList->pluck('hotel_id')->toArray();
        $hotelList = HotelService::getInstance()
            ->getHotelListByIds($hotelIds, ['id', 'name', 'cover'])->keyBy('id');

        $list = $shopHotelList->map(function (ShopHotel $shopHotel) use ($hotelList, $shopList, $merchantList) {
            /** @var HotelShop $shop */
            $shop = $shopList->get($shopHotel->shop_id);
            $shopHotel['shop_logo'] = $shop->logo;
            $shopHotel['shop_name'] = $shop->name;

            /** @var HotelMerchant $merchant */
            $merchant = $merchantList->get($shop->merchant_id);
            $shopHotel['merchant_name'] = $merchant->company_name;
            $shopHotel['business_license'] = $merchant->business_license_photo;

            /** @var Hotel $hotel */
            $hotel = $hotelList->get($shopHotel->hotel_id);
            $shopHotel['hotel_name'] = $hotel->name;
            $shopHotel['hotel_cover'] = $hotel->cover;

            return $shopHotel;
        });
        return $this->success($this->paginate($page, $list));
    }

    public function approve()
    {
        $id = $this->verifyRequiredId('id');

        $hotel = ShopHotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '酒店不存在');
        }
        $hotel->status = 1;
        $hotel->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $hotel = ShopHotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '酒店不存在');
        }
        $hotel->status = 2;
        $hotel->failure_reason = $reason;
        $hotel->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $hotel = ShopHotelService::getInstance()->getHotelById($id);
        if (is_null($hotel)) {
            return $this->fail(CodeResponse::NOT_FOUND, '酒店不存在');
        }
        $hotel->delete();

        return $this->success();
    }
}
