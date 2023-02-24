<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LiveGoods;
use App\Models\LiveRoom;
use App\Models\User;
use App\Services\LiveRoomService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\LiveRoomInput;
use App\Utils\Inputs\PageInput;
use App\Utils\TencentLiveServe;
use App\Utils\TimServe;
use Illuminate\Support\Facades\DB;

class LiveRoomController extends Controller
{
    protected $except = ['getRoomList', 'getRoomInfo'];

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
        $room = LiveRoomService::getInstance()->getRoom($this->userId(), $id, [0, 3], $columns);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        // 创建群聊，获取群组id
        $groupId = TimServe::new()->createChatGroup($id);
        $room->group_id = $groupId;

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

        $room = LiveRoomService::getInstance()->getRoom($this->userId(), $id, [0, 3]);
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

        $room = LiveRoomService::getInstance()->getRoom($this->userId(), $id, [3], ['name', 'cover', 'share_cover', 'notice_time']);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }
        return $this->success($room);
    }

    public function getRoomList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $columns = ['id', 'status', 'user_id', 'cover', 'name', 'play_url', 'notice_time', 'viewers_number', 'praise_number'];
        $page = LiveRoomService::getInstance()->list($input, $columns);
        $roomList = collect($page->items());

        $userIds = $roomList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getUserListByIds($userIds, ['avatar', 'nickname'])->keyBy('id');

        $list = $roomList->map(function (LiveRoom $room) use ($userList) {
            $anchorInfo = $userList->get($room->user_id);
            $room['anchor_info'] = $anchorInfo;
            unset($room->user_id);
            return $room;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function getRoomInfo()
    {
        $id = $this->verifyRequiredId('id');

        $columns = ['id', 'user_id', 'cover', 'share_cover', 'name', 'group_id', 'play_url', 'playback_url', 'notice_time', 'viewers_number', 'praise_number'];
        $room = LiveRoomService::getInstance()->getRoom($this->userId(), $id, [1, 2, 3], $columns);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $anchorInfo = UserService::getInstance()->getUserById($room->user_id, ['id', 'avatar', 'nickname', 'shop_id']);
        $room['anchor_info'] = $anchorInfo;
        unset($room->user_id);

        return $this->success($room);
    }
}
