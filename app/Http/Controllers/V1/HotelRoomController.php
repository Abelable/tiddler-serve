<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ScenicShop;
use App\Models\ScenicTicket;
use App\Services\HotelRoomTypeService;
use App\Services\ScenicShopService;
use App\Services\HotelRoomService;
use App\Services\TicketScenicService;
use App\Services\TicketSpecService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\ScenicTicketInput;
use App\Utils\Inputs\StatusPageInput;
use Illuminate\Support\Facades\DB;

class HotelRoomController extends Controller
{
    protected $except = ['categoryOptions', 'listByScenicId'];

    public function typeOptions()
    {
        $options = HotelRoomTypeService::getInstance()->getTypeOptions(['id', 'name']);
        return $this->success($options);
    }

    public function listByScenicId()
    {
        $scenicId = $this->verifyRequiredId('scenicId');

        $roomIds = TicketScenicService::getInstance()->getListByScenicId($scenicId)->pluck('room_id')->toArray();
        $roomList = HotelRoomService::getInstance()->getListByIds($roomIds);

        $shopIds = $roomList->pluck('shop_id')->toArray();
        $shopList = ScenicShopService::getInstance()->getShopListByIds($shopIds, ['id', 'name', 'type'])->keyBy('id');

        $roomList = $roomList->map(function (ScenicTicket $room) use ($shopList) {
            /** @var ScenicShop $shop */
            $shop = $shopList->get($room->shop_id);
            $room['shopInfo'] = $shop;

            unset($room->user_id);
            unset($room->shop_id);
            unset($room->provider_id);
            unset($room->status);
            unset($room->failure_reason);
            unset($room->promotion_commission_rate);
            unset($room->sales_commission_rate);
            unset($room->created_at);
            unset($room->updated_at);

            return $room;
        });

        return $this->success($roomList);
    }

    public function roomListTotals()
    {
        return $this->success([
            HotelRoomService::getInstance()->getListTotal($this->userId(), 1),
            HotelRoomService::getInstance()->getListTotal($this->userId(), 3),
            HotelRoomService::getInstance()->getListTotal($this->userId(), 0),
            HotelRoomService::getInstance()->getListTotal($this->userId(), 2),
        ]);
    }

    public function userTicketList()
    {
        /** @var StatusPageInput $input */
        $input = StatusPageInput::new();

        $page = HotelRoomService::getInstance()->getTicketListByStatus($this->userId(), $input);
        $roomList = collect($page->items());
        $list = $roomList->map(function (ScenicTicket $room) {
            $room['scenicIds'] = $room->scenicIds();
            return $room;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getTicketById($id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }

        $scenicIds = TicketScenicService::getInstance()->getListByTicketId($room->id)->pluck('scenic_id')->toArray();
        $specList = TicketSpecService::getInstance()->getSpecListByTicketId($room->id, ['category_id', 'price_list']);
        $room['scenicIds'] = $scenicIds;
        $room['specList'] = $specList;

        return $this->success($room);
    }

    public function add()
    {
        /** @var ScenicTicketInput $input */
        $input = ScenicTicketInput::new();

        $shopId = $this->user()->scenicShop->id;
        if ($shopId == 0) {
            return $this->fail(CodeResponse::FORBIDDEN, '您不是服务商，无法上传景点门票');
        }

        DB::transaction(function () use ($shopId, $input) {
            $room = HotelRoomService::getInstance()->createTicket($this->userId(), $this->user()->scenicProvider->id, $shopId, $input);
            TicketScenicService::getInstance()->createTicketScenicSpots($room->id, $input->scenicIds);
            TicketSpecService::getInstance()->createTicketSpecList($room->id, $input->specList);
        });

        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var ScenicTicketInput $input */
        $input = ScenicTicketInput::new();

        $room = HotelRoomService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }

        DB::transaction(function () use ($input, $room) {
            HotelRoomService::getInstance()->updateTicket($room, $input);
            TicketScenicService::getInstance()->updateTicketScenicSpots($room->id, $input->scenicIds);
            TicketSpecService::getInstance()->updateTicketSpecList($room->id, $input->specList);
        });

        return $this->success();
    }

    public function up()
    {
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        if ($room->status != 3) {
            return $this->fail(CodeResponse::FORBIDDEN, '非下架景点门票，无法上架');
        }
        $room->status = 1;
        $room->save();

        return $this->success();
    }

    public function down()
    {
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        if ($room->status != 1) {
            return $this->fail(CodeResponse::FORBIDDEN, '非售卖中景点门票，无法下架');
        }
        $room->status = 3;
        $room->save();

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');

        $room = HotelRoomService::getInstance()->getUserTicket($this->userId(), $id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前景点门票不存在');
        }
        $room->delete();

        return $this->success();
    }
}
