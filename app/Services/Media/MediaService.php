<?php

namespace App\Services\Media;

use App\Models\LiveRoom;
use App\Models\ShortVideo;
use App\Models\ShortVideoCollection;
use App\Models\ShortVideoLike;
use App\Models\TourismNote;
use App\Models\TourismNoteCollection;
use App\Models\TourismNoteLike;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class MediaService extends BaseService
{
    public function pageList(PageInput $input, $videoColumns = ['*'], $noteColumns = ['*'], $liveColumns = ['*'], $withLiveList = true, $authorIds = null)
    {
        $videoQuery = ShortVideo::query()->select($videoColumns)->where('is_private', 0)->selectRaw("2 as type");
        if (!is_null($authorIds)) {
            $videoQuery = $videoQuery->whereIn('user_id', $authorIds);
        }

        $noteQuery = TourismNote::query()->select($noteColumns)->where('is_private', 0)->selectRaw("3 as type");
        if (!is_null($authorIds)) {
            $noteQuery = $noteQuery->whereIn('user_id', $authorIds);
        }

        $mediaQuery = $videoQuery->union($noteQuery);

        if ($withLiveList) {
            $liveQuery = LiveRoom::query()->select($liveColumns)->whereIn('status', [1, 3])->selectRaw("1 as type");
            if (!is_null($authorIds)) {
                $liveQuery = $liveQuery->whereIn('user_id', $authorIds);
            }

            $mediaQuery = $mediaQuery->union($liveQuery);
        }

        return $mediaQuery
            ->orderByRaw("CASE WHEN status = 1 THEN 0 ELSE 1 END")
            ->orderBy('viewers_number', 'desc')
            ->orderBy('praise_number', 'desc')
            ->orderBy('like_number', 'desc')
            ->orderBy('comments_number', 'desc')
            ->orderBy('collection_times', 'desc')
            ->orderBy('share_times', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }

    public function collectPageList($userId, PageInput $input)
    {
        $videoQuery = ShortVideoCollection::query()->where('user_id', $userId);
        $noteQuery = TourismNoteCollection::query()->where('user_id', $userId);
        $mediaQuery = $videoQuery->union($noteQuery);

        return $mediaQuery
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }

    public function likePageList($userId, PageInput $input)
    {
        $videoQuery = ShortVideoLike::query()->where('user_id', $userId);
        $noteQuery = TourismNoteLike::query()->where('user_id', $userId);
        $mediaQuery = $videoQuery->union($noteQuery);

        return $mediaQuery
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }
}
