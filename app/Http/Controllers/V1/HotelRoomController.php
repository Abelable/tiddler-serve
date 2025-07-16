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
use App\Utils\Inputs\HotelRoomInput;
use App\Utils\Inputs\StatusPageInput;

class HotelRoomController extends Controller
{
    protected $except = ['typeOptions', 'list'];

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
            ->getShopListByIds($shopIds, ['id', 'name', 'type'])->keyBy('id');

        $roomList = $roomList->map(function (HotelRoom $room) use ($shopList) {
            /** @var HotelShop $shop */
            $shop = $shopList->get($room->shop_id);
            $room['shopInfo'] = $shop;

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

    public function totals()
    {
        $shopId = $this->verifyRequiredId('shopId');

        return $this->success([
            HotelRoomService::getInstance()->getListTotal($shopId, 1),
            HotelRoomService::getInstance()->getListTotal($shopId, 3),
            HotelRoomService::getInstance()->getListTotal($shopId, 0),
            HotelRoomService::getInstance()->getListTotal($shopId, 2),
        ]);
    }

    public function shopList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $page = HotelRoomService::getInstance()->getRoomListByStatus($shopId, $input);
        $roomList = collect($page->items());

        $typeIds = $roomList->pluck('type_id')->toArray();
        $typeList = HotelRoomTypeService::getInstance()->getListByIds($typeIds, ['id', 'name'])->keyBy('id');

        $list = $roomList->map(function (HotelRoom $room) use ($typeList) {
            /** @var HotelRoomType $type */
            $type = $typeList->get($room->type_id);
            $room['typeName'] = $type->name;
            return $room;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function add()
    {
        /** @var HotelRoomInput $input */
        $input = HotelRoomInput::new();
        $shopId = $this->verifyRequiredId('shopId');

        $shopManagerIds = HotelShopManagerService::getInstance()
            ->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->hotelShop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前酒店商家或管理员，无权限添加酒店房间');
        }

        HotelRoomService::getInstance()
            ->createRoom($this->userId(), $this->user()->hotelMerchant->id, $shopId, $input);

        return $this->success();
    }

    public function edit()
    {
        /** @var HotelRoomInput $input */
        $input = HotelRoomInput::new();
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $shopManagerIds = HotelShopManagerService::getInstance()
            ->getManagerList($shopId)->pluck('user_id')->toArray();
        if ($shopId != $this->user()->hotelShop->id && !in_array($this->userId(), $shopManagerIds)) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是当前酒店商家或管理员，无权限添加酒店房间');
        }

        $room = HotelRoomService::getInstance()->getShopRoom($shopId, $id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店房间不存在');
        }

        HotelRoomService::getInstance()->updateRoom($room, $input);

        return $this->success();
    }

    public function up()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getShopRoom($shopId, $id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店房间不存在');
        }
        if ($room->status != 3) {
            return $this->fail(CodeResponse::FORBIDDEN, '非下架酒店房间，无法上架');
        }
        $room->status = 1;
        $room->save();

        return $this->success();
    }

    public function down()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getShopRoom($shopId, $id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店房间不存在');
        }
        if ($room->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中酒店房间，无法下架');
        }
        $room->status = 3;
        $room->save();

        return $this->success();
    }

    public function delete()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getShopRoom($shopId, $id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前酒店房间不存在');
        }
        $room->delete();

        return $this->success();
    }
}
