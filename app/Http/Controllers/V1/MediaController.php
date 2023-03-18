<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LiveRoom;
use App\Models\ShortVideo;
use App\Models\TourismNote;
use App\Services\FanService;
use App\Services\Media\Live\LiveRoomService;
use App\Services\Media\MediaService;
use App\Services\Media\Note\TourismNoteCollectionService;
use App\Services\Media\Note\TourismNoteLikeService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoCollectionService;
use App\Services\Media\ShortVideo\ShortVideoLikeService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\UserService;
use App\Utils\Enums\MediaTypeEnums;
use App\Utils\Inputs\PageInput;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller
{
    protected $except = ['getList'];

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $videoColumns = [
            'id',
            'user_id',
            DB::raw('NULL as status'),
            'title',
            'cover',
            'video_url',
            DB::raw('NULL as image_list'),
            DB::raw('NULL as play_url'),
            'address',
            'like_number',
            'comments_number',
            'collection_times',
            'share_times',
            DB::raw('NULL as viewers_number'),
            DB::raw('NULL as praise_number'),
            DB::raw('NULL as notice_time'),
            'created_at',
        ];
        $noteColumns = [
            'id',
            'user_id',
            DB::raw('NULL as status'),
            'title',
            DB::raw('NULL as cover'),
            DB::raw('NULL as video_url'),
            'image_list',
            DB::raw('NULL as play_url'),
            'address',
            'like_number',
            'comments_number',
            'collection_times',
            'share_times',
            DB::raw('NULL as viewers_number'),
            DB::raw('NULL as praise_number'),
            DB::raw('NULL as notice_time'),
            'created_at',
        ];
        $liveColumns = [
            'id',
            'user_id',
            'status',
            'title',
            'cover',
            DB::raw('NULL as video_url'),
            DB::raw('NULL as image_list'),
            'play_url',
            DB::raw('NULL as address'),
            DB::raw('NULL as like_number'),
            DB::raw('NULL as comments_number'),
            DB::raw('NULL as collection_times'),
            DB::raw('NULL as share_times'),
            'viewers_number',
            'praise_number',
            'notice_time',
            'created_at',
        ];

        $page = MediaService::getInstance()->mediaPageList($input, $videoColumns, $noteColumns, $liveColumns);
        $mediaList = collect($page->items());
        $authorIds = $mediaList->pluck('user_id')->toArray();
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $list = collect($page->items())->map(function ($media) use ($authorList) {
            $authorInfo = $authorList->get($media['user_id']);
            if ($media['type'] == 1) {
                $media['anchor_info'] = $authorInfo;
            } else {
                $media['author_info'] = $authorInfo;
            }
            unset($media['user_id']);
            return $media;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function followList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $authorIds = FanService::getInstance()->authorIds($this->userId());
        $list = $this->getMediaList($input, $authorIds);

        return $this->success($list);
    }

    public function collectList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $videoInput = (clone $input)->fill([
            'limit' => (string)intval($input->limit / 2)
        ]);
        $videoPage = ShortVideoCollectionService::getInstance()->pageList($this->userId(), $videoInput);
        $videoIds = collect($videoPage->items())->pluck('video_id')->toArray();
        $videoColumns = ['id', 'cover', 'video_url', 'title', 'like_number'];
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds, $videoColumns);

        $noteInput = (clone $input)->fill([
            'limit' => (string)$input->limit - $videoList->count()
        ]);
        $notePage = TourismNoteCollectionService::getInstance()->pageList($this->userId(), $noteInput);
        $noteIds = collect($notePage->items())->pluck('note_id')->toArray();
        $noteColumns = ['id', 'image_list', 'title', 'praise_number'];
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds, $noteColumns);

        $list = $videoList->merge($noteList)->sortByDesc('created_at');

        return $this->success($list);
    }

    public function likeList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $videoInput = (clone $input)->fill([
            'limit' => (string)intval($input->limit / 2)
        ]);
        $videoPage = ShortVideoLikeService::getInstance()->pageList($this->userId(), $videoInput);
        $videoIds = collect($videoPage->items())->pluck('video_id')->toArray();
        $videoColumns = ['id', 'cover', 'video_url', 'title', 'praise_number'];
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds, $videoColumns);

        $noteInput = (clone $input)->fill([
            'limit' => (string)$input->limit - $videoList->count()
        ]);
        $notePage = TourismNoteLikeService::getInstance()->pageList($this->userId(), $noteInput);
        $noteIds = collect($notePage->items())->pluck('note_id')->toArray();
        $noteColumns = ['id', 'image_list', 'title', 'praise_number'];
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds, $noteColumns);

        $list = $videoList->merge($noteList)->sortByDesc('created_at');

        return $this->success($list);
    }

    private function getMediaList(PageInput $input, $authorIds = null)
    {
        $liveColumns = ['id', 'user_id', 'status', 'title', 'cover', 'play_url', 'notice_time', 'viewers_number', 'praise_number'];
        $livePage = LiveRoomService::getInstance()->pageList($input, $liveColumns, [1, 3], $authorIds);
        $liveListCollect = collect($livePage->items());
        $liveAnchorIds = $liveListCollect->pluck('user_id')->toArray();
        $anchorList = UserService::getInstance()->getListByIds($liveAnchorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $liveList = $liveListCollect->map(function (LiveRoom $live) use ($anchorList) {
            $live['type'] = MediaTypeEnums::LIVE;

            $anchorInfo = $anchorList->get($live->user_id);
            $live['anchor_info'] = $anchorInfo;
            unset($live->user_id);

            return $live;
        });

        $videoColumns = ['id', 'user_id', 'cover', 'video_url', 'title', 'like_number', 'address'];
        $videoPage = ShortVideoService::getInstance()->pageList($input, $videoColumns, $authorIds);
        $videoListCollect = collect($videoPage->items());
        $videoAuthorIds = $videoListCollect->pluck('user_id')->toArray();
        $videoAuthorList = UserService::getInstance()->getListByIds($videoAuthorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $videoList = $videoListCollect->map(function (ShortVideo $video) use ($videoAuthorList) {
            $video['type'] = MediaTypeEnums::VIDEO;

            $authorInfo = $videoAuthorList->get($video->user_id);
            $video['author_info'] = $authorInfo;
            unset($video->user_id);

            return $video;
        });

        $noteColumns = ['id', 'user_id', 'image_list', 'title', 'praise_number'];
        $notePage = TourismNoteService::getInstance()->pageList($input, $noteColumns, $authorIds);
        $noteListCollect = collect($notePage->items());
        $noteAuthorIds = $noteListCollect->pluck('user_id')->toArray();
        $noteAuthorList = UserService::getInstance()->getListByIds($noteAuthorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $noteList = $noteListCollect->map(function (TourismNote $note) use ($noteAuthorList) {
            $note['type'] = MediaTypeEnums::NOTE;
            $note->image_list = json_decode($note->image_list);

            $authorInfo = $noteAuthorList->get($note->user_id);
            $note['author_info'] = $authorInfo;
            unset($note->user_id);

            return $note;
        });

        $totalList = $liveList->concat($videoList)->concat($noteList);
        $sortedList = $totalList->sortByDesc('created_at');
        $paginatedList = $sortedList->forPage($input->page, $input->limit);

        return new LengthAwarePaginator($paginatedList, $sortedList->count(), $input->limit, $input->page);
    }
}
