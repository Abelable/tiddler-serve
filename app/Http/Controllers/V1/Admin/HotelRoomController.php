<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Services\HotelMerchantService;
use App\Services\HotelRoomTypeService;
use App\Services\HotelService;
use App\Services\HotelRoomService;
use App\Services\HotelShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\CommissionInput;
use App\Utils\Inputs\Admin\HotelRoomListInput;

class HotelRoomController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        /** @var HotelRoomListInput $input */
        $input = HotelRoomListInput::new();
        $page = HotelRoomService::getInstance()->getList($input);
        $roomList = collect($page->items());

        $hotelIds = $roomList->pluck('hotel_id')->toArray();
        $hotelList = HotelService::getInstance()->getHotelListByIds($hotelIds, ['id', 'name'])->keyBy('id');

        $typeIds = $roomList->pluck('type_id')->toArray();
        $typeList = HotelRoomTypeService::getInstance()->getListByIds($typeIds, ['id', 'name'])->keyBy('id');

        $list = $roomList->map(function (HotelRoom $room) use ($typeList, $hotelList) {
            /** @var Hotel $hotel */
            $hotel = $hotelList->get($room->hotel_id);
            $room['hotelName'] = $hotel->name;

            /** @var HotelRoomType $type */
            $type = $typeList->get($room->type_id);
            $room['typeName'] = $type->name;

            return $room;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getRoomById($id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店房间不存在');
        }

        $hotel = HotelService::getInstance()->getHotelById($room->hotel_id);
        $room['hotelName'] = $hotel->name;

        $roomType = HotelRoomTypeService::getInstance()->getTypeById($room->type_id);
        $room['typeName'] = $roomType->name;

        $shop = HotelShopService::getInstance()->getShopById($room->shop_id);
        if (is_null($shop)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店商家店铺不存在');
        }
        $merchant = HotelMerchantService::getInstance()->getMerchantById($shop->merchant_id);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店商家不存在');
        }
        $room['shop_info'] = $shop;
        $room['merchant_info'] = $merchant;
        unset($shop->merchant_id);
        unset($room->shop_id);

        return $this->success($room);
    }

    public function editCommission()
    {
        /** @var CommissionInput $input */
        $input = CommissionInput::new();
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getRoomById($id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店房价不存在');
        }

        if ($input->promotionCommissionRate) {
            $room->promotion_commission_rate = $input->promotionCommissionRate;
        }
        if ($input->promotionCommissionUpperLimit) {
            $room->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit;
        }
        if ($input->superiorPromotionCommissionRate) {
            $room->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate;
        }
        if ($input->superiorPromotionCommissionUpperLimit) {
            $room->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit;
        }
        $room->save();

        return $this->success();
    }

    public function approve()
    {
        /** @var CommissionInput $input */
        $input = CommissionInput::new();
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getRoomById($id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店房间不存在');
        }
        $room->status = 1;
        $room->promotion_commission_rate = $input->promotionCommissionRate;
        $room->promotion_commission_upper_limit = $input->promotionCommissionUpperLimit;
        $room->superior_promotion_commission_rate = $input->superiorPromotionCommissionRate;
        $room->superior_promotion_commission_upper_limit = $input->superiorPromotionCommissionUpperLimit;
        $room->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $room = HotelRoomService::getInstance()->getRoomById($id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店房间不存在');
        }
        $room->status = 2;
        $room->failure_reason = $reason;
        $room->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getRoomById($id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店房间不存在');
        }
        $room->delete();

        return $this->success();
    }
}
