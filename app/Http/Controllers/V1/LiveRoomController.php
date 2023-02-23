<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LiveGoods;
use App\Models\LiveRoom;
use App\Services\LiveRoomService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\LiveRoomInput;
use Illuminate\Support\Facades\DB;

class LiveRoomController extends Controller
{
    public function createLiveRoom()
    {
        /** @var LiveRoomInput $input */
        $input = LiveRoomInput::new();

        $roomId = DB::transaction(function () use ($input) {
            $roomId = LiveRoomService::getInstance()->newLiveRoom($input);
            if (count($input->goodsIds) != 0) {
                foreach ($input->goodsIds as $goodsId) {
                    $liveGoods = LiveGoods::new();
                    $liveGoods->room_id = $roomId;
                    $liveGoods->goods_id = $goodsId;
                    $liveGoods->save();
                }
            }
            return $roomId;
        });

        return $this->success($roomId);
    }

    public function startLive()
    {
        $id = $this->verifyRequiredId('id');

        $room = LiveRoomService::getInstance()->getPushRoom($this->userId(), $id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }
        if ($room->status != 0 || $room->status != 3) {
            return $this->fail(CodeResponse::INVALID_OPERATION, '非正常状态直播间，无法开播');
        }

        $room->status = 1;
        $room->start_time = now()->toDateTimeString();

        // 创建群聊，获取群组id

        // 获取推、拉流地址
    }

    public function getNoticeRoomInfo()
    {
        $id = $this->verifyRequiredId('id');

        $room = LiveRoomService::getInstance()->getPushRoom($this->userId(), $id, ['name', 'cover', 'notice_time']);
        if (is_null($room) || $room->status != 3) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }
        return $this->success($room);
    }
}
