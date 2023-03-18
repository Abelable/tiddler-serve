<?php

namespace App\Services\Media;

use App\Models\LiveRoom;
use App\Services\BaseService;
use App\Services\Media\Live\LiveRoomService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Utils\Inputs\PageInput;

class MediaService extends BaseService
{
    public function mediaPageList(PageInput $input, $videoColumns = ['*'], $noteColumns = ['*'], $liveColumns = ['*'], $withLiveList = true, $authorIds = null)
    {
        $videoQuery = ShortVideoService::getInstance()->videoQuery($videoColumns, $authorIds)->selectRaw("2 as type");
        $noteQuery = TourismNoteService::getInstance()->noteQuery($noteColumns, $authorIds)->selectRaw("3 as type");
        $mediaQuery = $videoQuery->union($noteQuery);

        if ($withLiveList) {
            $liveQuery = LiveRoomService::getInstance()->liveQuery($liveColumns, [1, 3], $authorIds)->selectRaw("1 as type");
            $mediaQuery = $mediaQuery->union($liveQuery);
        }

        return $mediaQuery
            ->orderBy('type')
            ->orderByRaw("CASE WHEN status = 1 THEN 0 WHEN status = 3 THEN 1 WHEN status = 2 THEN 2 ELSE 3 END")
            ->orderBy('viewers_number', 'desc')
            ->orderBy('praise_number', 'desc')
            ->orderBy('like_number', 'desc')
            ->orderBy('comments_number', 'desc')
            ->orderBy('collection_times', 'desc')
            ->orderBy('share_times', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }
}
