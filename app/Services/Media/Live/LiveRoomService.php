<?php

namespace App\Services\Media\Live;

use App\Models\LiveRoom;
use App\Services\BaseService;
use App\Utils\Enums\LiveStatusEnums;
use App\Utils\Inputs\LiveRoomInput;
use App\Utils\Inputs\PageInput;

class LiveRoomService extends BaseService
{
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

    public function list(PageInput $input, $columns = ['*'])
    {
        return LiveRoom::query()
            ->whereIn('status', [1, 2, 3])
            ->orderByRaw("CASE WHEN status = 1 THEN 0 WHEN status = 3 THEN 1 WHEN status = 2 THEN 2 ELSE 3 END")
            ->orderBy('viewers_number', 'desc')
            ->orderBy('praise_number', 'desc')
            ->paginate($input->limit, $columns, 'page', $input->page);
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
}
