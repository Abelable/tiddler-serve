<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelRoom;
use App\Models\HotelRoomType;
use App\Services\HotelProviderService;
use App\Services\HotelRoomTypeService;
use App\Services\HotelService;
use App\Services\HotelRoomService;
use App\Utils\CodeResponse;
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
            return $this->fail(CodeResponse::NOT_FOUND, '当前房间不存在');
        }

        $provider = HotelProviderService::getInstance()->getProviderById($room->provider_id);
        if (is_null($provider)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前服务商不存在');
        }

        $room['provider_info'] = $provider;

        return $this->success($room);
    }

    public function approve()
    {
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getRoomById($id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前房间不存在');
        }
        $room->status = 1;
        $room->save();

        return $this->success();
    }

    public function reject()
    {
        $id = $this->verifyRequiredId('id');
        $reason = $this->verifyRequiredString('failureReason');

        $room = HotelRoomService::getInstance()->getRoomById($id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前房间不存在');
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
            return $this->fail(CodeResponse::NOT_FOUND, '当前房间不存在');
        }
        $room->delete();

        return $this->success();
    }
}
