<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShortVideo;
use App\Models\TourismNote;
use App\Services\FanService;
use App\Services\Media\MediaService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\UserService;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller
{
    protected $except = ['getList'];

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        return $this->getMediaList($input);
    }

    public function followList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $authorIds = FanService::getInstance()->authorIds($this->userId());
        return $this->getMediaList($input, $authorIds);
    }

    private function getMediaList(PageInput $input, $authorIds = null)
    {
        $videoColumns = [
            'id',
            'user_id',
            DB::raw('NULL as status'),
            DB::raw('NULL as direction'),
            'is_private',
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
            DB::raw('NULL as direction'),
            'is_private',
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
            'direction',
            DB::raw('NULL as is_private'),
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

        $page = MediaService::getInstance()->pageList($input, $videoColumns, $noteColumns, $liveColumns, true, $authorIds);
        $mediaList = collect($page->items());
        $authorIds = $mediaList->pluck('user_id')->toArray();
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $list = collect($page->items())->map(function ($media) use ($authorList) {
            $authorInfo = $authorList->get($media['user_id']);
            if ($media['type'] == 1) {
                $media['anchorInfo'] = $authorInfo;
            } else {
                $media['authorInfo'] = $authorInfo;
            }

            if ($media['type'] == 3) {
                $media['image_list'] = json_decode($media['image_list']);
            }

            unset($media['user_id']);

            return $media;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function collectList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $videoColumns = ['video_id', DB::raw('NULL as note_id'), 'user_id', 'created_at'];
        $noteColumns = [DB::raw('NULL as video_id'), 'note_id', 'user_id', 'created_at'];

        $page = MediaService::getInstance()->collectPageList($this->userId(), $input, $videoColumns, $noteColumns);
        $mediaList = collect($page->items());

        $videoIds = $mediaList->pluck('video_id')->toArray();
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds)->keyBy('id');

        $noteIds = $mediaList->pluck('note_id')->toArray();
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds)->keyBy('id');

        $list = $mediaList->map(function ($media) use ($noteList, $videoList) {
            if ($media['video_id']) {
                /** @var ShortVideo $video */
                $video = $videoList->get($media['video_id']);
                return [
                    'type' => 2,
                    'id' => $video->id,
                    'cover' => $video->cover,
                    'videoUrl' => $video->video_url,
                    'title' => $video->title,
                    'likeNumber' => $video->like_number,
                    'address' => $video->address,
                    'authorInfo' => $video->authorInfo
                ];
            } else {
                /** @var TourismNote $note */
                $note = $noteList->get($media['note_id']);
                return [
                    'type' => 3,
                    'id' => $note->id,
                    'imageList' => json_decode($note->image_list),
                    'title' => $note->title,
                    'likeNumber' => $note->like_number,
                    'address' => $note->address,
                    'authorInfo' => $note->authorInfo
                ];
            }
        });

        return $this->success($this->paginate($page, $list));
    }

    public function likeList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $videoColumns = ['video_id', DB::raw('NULL as note_id'), 'user_id', 'created_at'];
        $noteColumns = [DB::raw('NULL as video_id'), 'note_id', 'user_id', 'created_at'];

        $page = MediaService::getInstance()->likePageList($this->userId(), $input, $videoColumns, $noteColumns);
        $mediaList = collect($page->items());

        $videoIds = $mediaList->pluck('video_id')->toArray();
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds)->keyBy('id');

        $noteIds = $mediaList->pluck('note_id')->toArray();
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds)->keyBy('id');

        $list = $mediaList->map(function ($media) use ($noteList, $videoList) {
            if ($media['video_id']) {
                /** @var ShortVideo $video */
                $video = $videoList->get($media['video_id']);
                $video['type'] = 2;
                return [
                    'type' => 2,
                    'id' => $video->id,
                    'cover' => $video->cover,
                    'videoUrl' => $video->video_url,
                    'title' => $video->title,
                    'likeNumber' => $video->like_number,
                    'address' => $video->address,
                    'authorInfo' => $video->authorInfo
                ];
            } else {
                /** @var TourismNote $note */
                $note = $noteList->get($media['note_id']);
                return [
                    'type' => 3,
                    'id' => $note->id,
                    'imageList' => json_decode($note->image_list),
                    'title' => $note->title,
                    'likeNumber' => $note->like_number,
                    'address' => $note->address,
                    'authorInfo' => $note->authorInfo
                ];
            }
        });

        return $this->success($this->paginate($page, $list));
    }
}
