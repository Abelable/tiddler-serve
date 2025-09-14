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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class MediaService extends BaseService
{
    public function mediaPage(
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
        if ($keywords) {
            $videoQuery = $videoQuery->where('title', 'like', "%$keywords%");
        }

        $noteQuery = TourismNote::query()->select($noteColumns)->where('is_private', 0)->selectRaw("3 as type");
        if (!is_null($authorIds)) {
            $noteQuery = $noteQuery->whereIn('user_id', $authorIds);
        }
        if ($keywords) {
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
            if ($keywords) {
                $liveQuery = $liveQuery->where('title', 'like', "%$keywords%");
            }

            $mediaQuery = $mediaQuery->union($liveQuery);
        }

        return $mediaQuery
            ->orderByRaw("CASE WHEN type = 1 THEN 0 ELSE 1 END")
            ->orderByRaw("CASE WHEN status = 1 THEN 0 ELSE 1 END")
            ->orderBy('views', 'desc')
            ->orderBy('share_times', 'desc')
            ->orderBy('collection_times', 'desc')
            ->orderBy('like_number', 'desc')
            ->orderBy('praise_number', 'desc')
            ->orderBy('comments_number', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }

    public function randomMediaPage(
        PageInput $input,
        $videoColumns = ['*'],
        $noteColumns = ['*'],
        $liveColumns = ['*'],
        $cacheKey = 'random_media_ids'
    ) {
        $refresh = ($input->page == 1);

        if ($refresh || !Cache::has($cacheKey)) {
            $videoIds = ShortVideo::query()
                ->where('is_private', 0)
                ->pluck('id')
                ->map(fn($id) => "v:$id")
                ->toArray();

            $noteIds = TourismNote::query()
                ->where('is_private', 0)
                ->pluck('id')
                ->map(fn($id) => "n:$id")
                ->toArray();

            $liveIds = LiveRoom::query()
                ->whereIn('status', [1, 3])
                ->pluck('id')
                ->map(fn($id) => "l:$id")
                ->toArray();

            shuffle($liveIds);
            $otherIds = array_merge($videoIds, $noteIds);
            shuffle($otherIds);
            $allIds = array_merge($liveIds, $otherIds);

            Cache::put($cacheKey, $allIds, 1800);
        } else {
            $allIds = Cache::get($cacheKey);
        }

        $offset = ($input->page - 1) * $input->limit;
        $pageIds = array_slice($allIds, $offset, $input->limit);

        $videoIds = [];
        $noteIds  = [];
        $liveIds  = [];
        foreach ($pageIds as $id) {
            if (substr($id, 0, 2) == 'v:') {
                $videoIds[] = substr($id, 2);
            } elseif (substr($id, 0, 2) == 'n:') {
                $noteIds[] = substr($id, 2);
            } elseif (substr($id, 0, 2) == 'l:') {
                $liveIds[] = substr($id, 2);
            }
        }

        $videoList = ShortVideo::query()
            ->select($videoColumns)
            ->whereIn('id', $videoIds)
            ->get()
            ->map(fn($item) => $item->setAttribute('type', 2));

        $noteList = TourismNote::query()
            ->select($noteColumns)
            ->whereIn('id', $noteIds)
            ->get()
            ->map(fn($item) => $item->setAttribute('type', 3));

        $liveList = LiveRoom::query()
            ->select($liveColumns)
            ->whereIn('id', $liveIds)
            ->get()
            ->map(fn($item) => $item->setAttribute('type', 1));

        $result = collect($videoList)->merge($noteList)->merge($liveList)->sortBy(function ($item) use ($pageIds) {
            if ($item->type == 1) {
                $prefix = 'l:';
            } elseif ($item->type == 2) {
                $prefix = 'v:';
            } elseif ($item->type == 3) {
                $prefix = 'n:';
            }  else {
                $prefix = '';
            }
            return array_search($prefix . $item->id, $pageIds);
        })->values();

        return new LengthAwarePaginator(
            $result,
            count($allIds),
            $input->limit,
            $input->page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
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
