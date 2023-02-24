<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LiveGoods;
use App\Services\LiveRoomService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\LiveRoomInput;
use App\Utils\TencentLiveServe;
use App\Utils\TimServe;
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

    public function getPushRoomInfo()
    {
        $id = $this->verifyRequiredId('id');

        $columns = ['name', 'cover', 'share_cover', 'viewers_number', 'praise_number', 'group_id', 'push_url', 'play_url'];
        $room = LiveRoomService::getInstance()->getPushRoom($this->userId(), $id, $columns);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        // 创建群聊，获取群组id
        $ret = TimServe::new()->group_create_group3('AVChatRoom', '' . $id, env('TIM_ADMIN'), $id);
        $room->group_id = $ret['GroupId'];

        // 获取推、拉流地址
        $pushUrl = TencentLiveServe::new()->getPushUrl($id);
        $playUrl = TencentLiveServe::new()->getPlayUrl($id);
        $room->push_url = $pushUrl;
        $room->play_url = $playUrl;

        $room->save();

        return $this->success($room);
    }

    public function startLive()
    {
        $id = $this->verifyRequiredId('id');

        $room = LiveRoomService::getInstance()->getPushRoom($this->userId(), $id);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $room->status = 1;
        $room->start_time = now()->toDateTimeString();
        $room->save();

        // todo 开播通知

        return $this->success();
    }

    public function getNoticeRoomInfo()
    {
        $id = $this->verifyRequiredId('id');

        $room = LiveRoomService::getInstance()->getPushRoom($this->userId(), $id, ['name', 'cover', 'share_cover', 'notice_time'], [3]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }
        return $this->success($room);
    }
}
