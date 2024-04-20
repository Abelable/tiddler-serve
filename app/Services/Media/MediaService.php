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

class MediaService extends BaseService
{
    public function pageList(
        PageInput $input,
        $videoColumns = ['*'],
        $noteColumns = ['*'],
        $liveColumns = ['*'],
        $authorIds = null,
        $withLiveList = true,
        $keywords = ''
    )
    {
        $videoQuery = ShortVideo::query()->select($videoColumns)->where('is_private', 0)->selectRaw("2 as type");
        if (!is_null($authorIds)) {
            $videoQuery = $videoQuery->whereIn('user_id', $authorIds);
        }
        if (!$keywords) {
            $videoQuery = $videoQuery->where('title', 'like', "%$keywords%");
        }

        $noteQuery = TourismNote::query()->select($noteColumns)->where('is_private', 0)->selectRaw("3 as type");
        if (!is_null($authorIds)) {
            $noteQuery = $noteQuery->whereIn('user_id', $authorIds);
        }
        if (!$keywords) {
            $noteQuery = $noteQuery
                ->where('title', 'like', "%$keywords%")
                ->where('content', 'like', "%$keywords%");
        }

        $mediaQuery = $videoQuery->union($noteQuery);

        if ($withLiveList) {
            $liveQuery = LiveRoom::query()->select($liveColumns)->whereIn('status', [1, 3])->selectRaw("1 as type");
            if (!is_null($authorIds)) {
                $liveQuery = $liveQuery->whereIn('user_id', $authorIds);
            }
            if (!$keywords) {
                $liveQuery = $liveQuery->where('title', 'like', "%$keywords%");
            }

            $mediaQuery = $mediaQuery->union($liveQuery);
        }

        return $mediaQuery
            ->orderByRaw("CASE WHEN type = 1 THEN 0 ELSE 1 END")
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

    public function collectPageList($userId, PageInput $input, $videoColumns = ['*'], $noteColumns = ['*'])
    {
        $videoQuery = ShortVideoCollection::query()->select($videoColumns)->where('user_id', $userId);
        $noteQuery = TourismNoteCollection::query()->select($noteColumns)->where('user_id', $userId);
        $mediaQuery = $videoQuery->union($noteQuery);

        return $mediaQuery
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }

    public function likePageList($userId, PageInput $input, $videoColumns = ['*'], $noteColumns = ['*'])
    {
        $videoQuery = ShortVideoLike::query()->select($videoColumns)->where('user_id', $userId);
        $noteQuery = TourismNoteLike::query()->select($noteColumns)->where('user_id', $userId);
        $mediaQuery = $videoQuery->union($noteQuery);

        return $mediaQuery
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }

    public function beLikedTimes($authorId)
    {
        $videoIds = ShortVideo::query()->where('user_id', $authorId)->get()->pluck('id')->toArray();
        $videoTimes = ShortVideoLike::query()->whereIn('video_id', $videoIds)->count();

        $noteIds = TourismNote::query()->where('user_id', $authorId)->get()->pluck('id')->toArray();
        $noteTimes = TourismNoteLike::query()->whereIn('note_id', $noteIds)->count();

        return $videoTimes + $noteTimes;
    }

    public function beCollectedTimes($authorId)
    {
        $videoIds = ShortVideo::query()->where('user_id', $authorId)->get()->pluck('id')->toArray();
        $videoTimes = ShortVideoCollection::query()->whereIn('video_id', $videoIds)->count();

        $noteIds = TourismNote::query()->where('user_id', $authorId)->get()->pluck('id')->toArray();
        $noteTimes = TourismNoteCollection::query()->whereIn('note_id', $noteIds)->count();

        return $videoTimes + $noteTimes;
    }
}
