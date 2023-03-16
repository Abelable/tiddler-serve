<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\FanService;
use App\Services\Media\Live\LiveRoomService;
use App\Utils\CodeResponse;
use App\Utils\Enums\LiveGroupMsgType;
use App\Utils\Inputs\PageInput;
use App\Utils\TimServe;

class LivePlayController extends Controller
{
    protected $except = ['roomList'];

    public function roomList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyRequiredId('id');

        $columns = ['id', 'status', 'title', 'cover', 'share_cover', 'direction', 'group_id', 'play_url', 'notice_time'];
        $list = LiveRoomService::getInstance()->pageList($input, $columns, [1, 3], null, $id);

        return $this->successPaginate($list);
    }

    public function joinRoom()
    {
        $id = $this->verifyRequiredId('id');
        $room = LiveRoomService::getInstance()->getRoom($id, [1], ['*'], true);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        // 增加直播间人数
        $room->viewers_number = $room->viewers_number + 1;

        // 将缓存中的实时点赞数，保存到数据库中
        $praiseNumber = LiveRoomService::getInstance()->getPraiseNumber($id);
        $room->praise_number = $praiseNumber;

        $room->save();

        // 发送即时通讯消息（用户进入直播间）
        $data = [
            'type' => LiveGroupMsgType::JOIN_ROOM,
            'data' => [
                'nickname' => $this->user()->nickname
            ]
        ];
        TimServe::new()->sendGroupSystemNotification($room->group_id, $data);

        // 获取历史聊天消息列表
        $historyChatMsgList = LiveRoomService::getInstance()->getChatMsgList($id);

        // 获取当前用户关注状态
        $fanIds = FanService::getInstance()->fanIds($room->user_id);

        // 返回
        // 实时数据：点赞数、观看人数、历史聊天消息列表、商品列表
        // 用户状态：是否关注直播间、用户粉丝等级（待开发）
        return $this->success([
            'viewersNumber' => $room->viewers_number,
            'praiseNumber' => $room->praise_number,
            'goodsCount' => $room->goods_list_count,
            'historyChatMsgList' => $historyChatMsgList,
            'isFollow' => in_array($this->userId(), $fanIds)
        ]);
    }

    public function followStatus()
    {
        $id = $this->verifyRequiredId('id');
        $room = LiveRoomService::getInstance()->getRoom($id, [1, 3]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $fanIds = FanService::getInstance()->fanIds($room->user_id);

        return $this->success([
            'isFollow' => in_array($this->userId(), $fanIds)
        ]);
    }

    public function praise()
    {
        $id = $this->verifyRequiredId('id');
        $count = $this->verifyRequiredInteger('count');

        $room = LiveRoomService::getInstance()->getRoom($id, [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $praiseNumber = LiveRoomService::getInstance()->cachePraiseNumber($id, $count);

        // 发送及时通讯消息（点赞数更新）
        $data = [
            'type' => LiveGroupMsgType::PRAISE,
            'data' => [
                'praiseNumber' => $praiseNumber
            ]
        ];
        TimServe::new()->sendGroupSystemNotification($room->group_id, $data);

        return $this->success();
    }

    public function comment()
    {
        $id = $this->verifyRequiredId('id');
        $content = $this->verifyRequiredString('content');
        $identity = $this->verifyRequiredInteger('identity');

        $room = LiveRoomService::getInstance()->getRoom($id, [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $chatMsg = [
            'identity' => $identity,
            'userId' => $this->userId(),
            'avatar' => $this->user()->avatar,
            'nickname' => $this->user()->nickname,
            'content' => $content
        ];
        LiveRoomService::getInstance()->cacheChatMsg($id, $chatMsg);

        return $this->success();
    }

    public function roomGoodsList()
    {
        $id = $this->verifyRequiredId('id');

        $room = LiveRoomService::getInstance()->getRoom($id, [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        return $this->success([
            'goodsList' => $room->goodsList
        ]);
    }

    public function roomHotGoods()
    {
        $id = $this->verifyRequiredId('id');

        $room = LiveRoomService::getInstance()->getRoom($id, [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        return $this->success($room->hotGoods());
    }

    public function subscribe()
    {
        $anchorId = $this->verifyRequiredId('anchorId');
        // todo 订阅逻辑
        return $this->success();
    }
}
