<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LiveRoom;
use App\Services\FanService;
use App\Services\Media\Live\LiveGoodsService;
use App\Services\Media\Live\LiveRoomService;
use App\Utils\CodeResponse;
use App\Utils\Enums\LiveStatusEnums;
use App\Utils\Inputs\LiveRoomInput;
use App\Utils\Inputs\PageInput;
use App\Utils\TencentLiveServe;
use App\Utils\TimServe;
use Illuminate\Support\Facades\DB;

class LiveRoomController extends Controller
{
    protected $except = ['getRoomList'];

    public function getRoomList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyRequiredId('id');

        $columns = ['id', 'user_id', 'status', 'cover', 'share_cover', 'name', 'group_id', 'play_url', 'notice_time', 'viewers_number', 'praise_number'];
        $page = LiveRoomService::getInstance()->pageList($input, $columns, [1, 3], null, $id);
        $roomList = collect($page->items());

        $anchorIds = $roomList->pluck('user_id')->toArray();
        $fanIdsGroup = FanService::getInstance()->fanIdsGroup($anchorIds);

        $list = $roomList->map(function (LiveRoom $room) use ($fanIdsGroup) {
            $room['is_follow'] = 0;
            if ($this->isLogin()) {
                $fansIds = $fanIdsGroup->get($room->user_id);
                if (in_array($this->userId(), $fansIds)) {
                    $room['is_follow'] = 1;
                }
            }
            unset($room->user_id);
            return $room;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function createLiveRoom()
    {
        /** @var LiveRoomInput $input */
        $input = LiveRoomInput::new();

        /** @var LiveRoom $room */
        $room = DB::transaction(function () use ($input) {
            $room = LiveRoomService::getInstance()->newLiveRoom($this->userId(), $input);
            if (count($input->goodsIds) != 0) {
                foreach ($input->goodsIds as $goodsId) {
                    LiveGoodsService::getInstance()->newGoods($room->id, $goodsId);
                }
            }
            return $room;
        });

        return $this->success($room->id);
    }

    public function getRoomInfo()
    {
        $id = $this->verifyRequiredId('id');

        $columns = ['name', 'cover', 'share_cover', 'notice_time'];
        $room = LiveRoomService::getInstance()->getRoom($this->userId(), $id, [0, 3], $columns);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }
        return $this->success($room);
    }

    public function startLive()
    {
        $id = $this->verifyRequiredId('id');

        /** @var LiveRoom $room */
        $room = LiveRoomService::getInstance()->getRoom($this->userId(), $id, [0, 3]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        DB::transaction(function () use ($id, $room) {
            // 创建群聊，获取群组id
            $groupId = TimServe::new()->createChatGroup($id);
            $room->group_id = $groupId;

            // 获取推、拉流地址
            $pushUrl = TencentLiveServe::new()->getPushUrl($id);
            $playUrl = TencentLiveServe::new()->getPlayUrl($id);
            $room->push_url = $pushUrl;
            $room->play_url = $playUrl;

            $room->status = LiveStatusEnums::STATUS_LIVE;
            $room->start_time = now()->toDateTimeString();
            $room->save();
        });

        // todo 开播通知

        return $this->success([
            'groupId' => $room->group_id,
            'pushUrl' => $room->push_url
        ]);
    }

    public function stopLive()
    {
        $id = $this->verifyRequiredId('id');
        $room = LiveRoomService::getInstance()->getRoom($this->userId(), $id, [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $room->status = LiveStatusEnums::STATUS_STOP;
        $room->end_time = now()->toDateTimeString();
        $room->save();

        // todo: 生成回放地址
        // todo: 发送关闭直播间的即时通讯消息
        // todo: 解散聊天群组

        return $this->success();
    }
}
