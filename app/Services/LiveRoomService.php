<?php

namespace App\Services;

use App\Models\LiveRoom;
use App\Utils\Inputs\LiveRoomInput;
use App\Utils\TimServe;

class LiveRoomService extends BaseService
{
    public function newLiveRoom(LiveRoomInput $input)
    {
        $room = LiveRoom::new();
        $room->user_id = $this->userId();
        $room->name = $input->name;
        $room->cover = $input->cover;
        $room->share_cover = $input->shareCover;
        $room->direction = $input->direction;
        if (!empty($input->noticeTime)) {
            $room->status = 3;
            $room->notice_time = $input->noticeTime;
        }
        $room->save();
        return $room->id;
    }

    public function getPushRoom($userId, $id, $columns = ['*'], $statusList = [0, 3])
    {
        return LiveRoom::query()
            ->where('user_id', $userId)
            ->whereIn('status', $statusList)
            ->find($id, $columns);
    }
}
