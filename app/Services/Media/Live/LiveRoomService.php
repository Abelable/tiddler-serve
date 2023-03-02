<?php

namespace App\Services\Media\Live;

use App\Models\LiveRoom;
use App\Services\BaseService;
use App\Utils\Enums\LiveStatusEnums;
use App\Utils\Inputs\LiveRoomInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;

class LiveRoomService extends BaseService
{
    public function pageList(PageInput $input, $columns = ['*'], $statusList = [1, 2, 3], $anchorIds = null, $curRoomId = 0)
    {
        $query = LiveRoom::query();
        if (!is_null($anchorIds)) {
            $query = $query->whereIn('user_id', $anchorIds);
        }
        if ($curRoomId != 0) {
            $query = $query->orderByRaw("CASE WHEN id = " . $curRoomId . " THEN 0 ELSE 1 END");
        }
        return $query
            ->whereIn('status', $statusList)
            ->with('authorInfo')
            ->orderByRaw("CASE WHEN status = 1 THEN 0 WHEN status = 3 THEN 1 WHEN status = 2 THEN 2 ELSE 3 END")
            ->orderBy('viewers_number', 'desc')
            ->orderBy('praise_number', 'desc')
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function newLiveRoom($userId, LiveRoomInput $input)
    {
        $room = LiveRoom::new();
        $room->user_id = $userId;
        $room->title = $input->title;
        $room->cover = $input->cover;
        $room->share_cover = $input->shareCover;
        $room->direction = $input->direction;
        if (!empty($input->noticeTime)) {
            $room->status = LiveStatusEnums::STATUS_NOTICE;
            $room->notice_time = $input->noticeTime;
        }
        $room->save();
        return $room;
    }

    public function getRoom($userId, $id, $statusList, $columns = ['*'])
    {
        return LiveRoom::query()
            ->where('user_id', $userId)
            ->whereIn('status', $statusList)
            ->find($id, $columns);
    }

    public function getListByIds($ids, $columns = ['*'])
    {
        return LiveRoom::query()->whereIn('id', $ids)->get($columns);
    }

    public function cachePraiseNumber($roomId, $number)
    {
        return Cache::increment('live_room_praise_number' . $roomId, $number);
    }

    public function getPraiseNumber($roomId)
    {
        return Cache::get('live_room_praise_number' . $roomId) ?? 0;
    }

    public function clearPraiseNumber($roomId)
    {
        Cache::forget('live_room_praise_number' . $roomId);
    }

    public function cacheViewersNumber($roomId)
    {
        return Cache::increment('live_room_viewers_number' . $roomId);
    }

    public function getViewersNumber($roomId)
    {
        return Cache::get('live_room_viewers_number' . $roomId) ?? 0;
    }

    public function clearViewersNumber($roomId)
    {
        Cache::forget('live_room_viewers_number' . $roomId);
    }

    public function cacheChatMsg($roomId, $msg)
    {
        $msgList = Cache::get('live_room_chat_msg_list' . $roomId) ?? [];
        if (count($msgList) >= 80) {
            array_shift($msgList);
        }
        $msgList[] = $msg;
        Cache::put('live_room_chat_msg_list' . $roomId, $msgList);
        return $msgList;
    }

    public function getChatMsgList($roomId, PageInput $input)
    {
        $chatMsgList = Cache::get('live_room_chat_msg_list' . $roomId) ?? [];

        $chatMsgList = collect($chatMsgList)->map(function ($item) {
            return json_encode($item);
        });

        return $chatMsgList->sortBy($input->sort, SORT_REGULAR, $input->order === 'desc')->values()->forPage($input->page, $input->limit)->all();
    }

    public function clearChatMsgList($roomId)
    {
        Cache::forget('live_room_chat_msg_list' . $roomId);
    }
}
