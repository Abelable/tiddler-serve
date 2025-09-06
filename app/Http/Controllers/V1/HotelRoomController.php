<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Models\HotelShop;
use App\Services\HotelRoomTypeService;
use App\Services\HotelRoomService;
use App\Services\HotelShopManagerService;
use App\Services\HotelShopService;
use App\Utils\CodeResponse;

class HotelRoomController extends Controller
{
    protected $only = [];

    public function typeOptions()
    {
        $hotelId = $this->verifyRequiredId('hotelId');
        $options = HotelRoomTypeService::getInstance()->getTypeOptions($hotelId);
        $options = $options->map(function (HotelRoomType $type) {
            $type->image_list = json_decode($type->image_list);
            $type->facility_list = json_decode($type->facility_list);

            unset($type->hotel_id);
            unset($type->created_at);
            unset($type->updated_at);

            return $type;
        });
        return $this->success($options);
    }

    public function list()
    {
        $hotelId = $this->verifyRequiredId('hotelId');

        $roomList = HotelRoomService::getInstance()->getListByHotelId($hotelId);
        $shopIds = $roomList->pluck('shop_id')->toArray();
        $shopList = HotelShopService::getInstance()
            ->getShopListByIds($shopIds, ['id', 'user_id', 'name', 'type', 'owner_avatar', 'owner_name'])
            ->keyBy('id');
        $shopManagerListGroup = HotelShopManagerService::getInstance()
            ->getListByShopIds($shopIds, ['id', 'user_id', 'avatar', 'nickname', 'role_id'])
            ->groupBy('shop_id');

        $roomList = $roomList->map(function (HotelRoom $room) use ($shopList, $shopManagerListGroup) {
            /** @var HotelShop $shop */
            $shop = $shopList->get($room->shop_id);
            $room['shopInfo'] = $shop;

            $managerList = $shopManagerListGroup->get($room->shop_id);
            $room['managerList'] = $managerList;

            $room->price_list = json_decode($room->price_list);

            unset($room->hotel_id);
            unset($room->shop_id);
            unset($room->status);
            unset($room->failure_reason);
            unset($room->superior_promotion_commission_rate);
            unset($room->superior_promotion_commission_upper_limit);
            unset($room->created_at);
            unset($room->updated_at);

            return $room;
        });

        return $this->success($roomList);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getRoomById($id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店房间不存在');
        }
        $room->price_list = json_decode($room->price_list);

        return $this->success($room);
    }
}
