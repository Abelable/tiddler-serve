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
        $videoQuery = ShortVideoService::getInstance()->videoQuery($videoColumns, $authorIds);
        $noteQuery = TourismNoteService::getInstance()->noteQuery($noteColumns, $authorIds);
        $mediaQuery = $videoQuery->union($noteQuery);

        if ($withLiveList) {
            $liveQuery = LiveRoomService::getInstance()->liveQuery($liveColumns, [1, 3], $authorIds);
            $mediaQuery = $mediaQuery->union($liveQuery);
        }

        return $mediaQuery
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }
}
