<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaCommodity;
use App\Models\ShortVideo;
use App\Models\TourismNote;
use App\Services\FanService;
use App\Services\GoodsService;
use App\Services\KeywordService;
use App\Services\Media\MediaService;
use App\Services\Media\Note\TourismNoteCollectionService;
use App\Services\Media\Note\TourismNoteLikeService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoCollectionService;
use App\Services\Media\ShortVideo\ShortVideoLikeService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\MediaCommodityService;
use App\Services\UserService;
use App\Utils\Enums\MediaType;
use App\Utils\Inputs\CommodityMediaPageInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller
{
    protected $except = ['list', 'search'];

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

        $authorIds = FanService::getInstance()->followAuthorIds($this->userId());
        return $this->getMediaList($input, $authorIds, false);
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');
        /** @var PageInput $input */
        $input = PageInput::new();

        if ($this->isLogin()) {
            KeywordService::getInstance()->addKeyword($this->userId(), $keywords);
        }

        $authorIds = UserService::getInstance()->searchUserIds($keywords);
        return $this->getMediaList($input, $authorIds, true, $keywords);
    }

    private function getMediaList(PageInput $input, $authorIds = null, $withLiveList = true, $keywords = '')
    {
        $videoColumns = [
            'id',
            'user_id',
            DB::raw('NULL as status'),
            DB::raw('NULL as direction'),
            'is_private',
            'title',
            DB::raw('NULL as content'),
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
            'content',
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
            DB::raw('NULL as content'),
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

        $page = MediaService::getInstance()->pageList($input, $videoColumns, $noteColumns, $liveColumns, $authorIds, $withLiveList, $keywords);
        $mediaList = collect($page->items());

        $videoList = $mediaList->filter(function ($media) {
            return $media->type == MediaType::VIDEO;
        });
        $videoIds = $videoList->pluck('id')->toArray();
        $videoLikeUserIdsGroup = ShortVideoLikeService::getInstance()->likeUserIdsGroup($videoIds);
        $videoCollectedUserIdsGroup = ShortVideoCollectionService::getInstance()->collectedUserIdsGroup($videoIds);

        $noteList = $mediaList->filter(function ($media) {
            return $media->type == MediaType::NOTE;
        });
        $noteIds = $noteList->pluck('id')->toArray();
        $noteLikeUserIdsGroup = TourismNoteLikeService::getInstance()->likeUserIdsGroup($noteIds);
        $noteCollectedUserIdsGroup = TourismNoteCollectionService::getInstance()->collectedUserIdsGroup($noteIds);

        $authorIds = $mediaList->pluck('user_id')->toArray();
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $goodsIds = $mediaList->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, ['id', 'name', 'cover', 'price', 'market_price', 'stock', 'sales_volume'])->keyBy('id');

        $list = $mediaList->map(function ($media) use (
            $authorList,
            $goodsList,
            $videoLikeUserIdsGroup,
            $videoCollectedUserIdsGroup,
            $noteLikeUserIdsGroup,
            $noteCollectedUserIdsGroup
        ) {
            $authorInfo = $authorList->get($media['user_id']);
            if ($media['type'] == MediaType::LIVE) {
                $media['anchorInfo'] = $authorInfo;
            } else {
                $media['authorInfo'] = $authorInfo;
            }

            $goodsInfo = $goodsList->get($media['goods_id']);
            if ($media['type'] != MediaType::LIVE) {
                $media['goodsInfo'] = $goodsInfo;
            }

            if ($media['type'] == MediaType::NOTE) {
                $media['image_list'] = json_decode($media['image_list']);
            }

            if ($this->isLogin()) {
                if ($media['type'] == MediaType::VIDEO) {
                    $videoLikeUserIds = $videoLikeUserIdsGroup->get($media->id) ?? [];
                    $media['isLike'] = in_array($this->userId(), $videoLikeUserIds);

                    $videoCollectedUserIds = $videoCollectedUserIdsGroup->get($media->id) ?? [];
                    $media['isCollected'] = in_array($this->userId(), $videoCollectedUserIds);
                }

                if ($media['type'] == MediaType::NOTE) {
                    $noteLikeUserIds = $noteLikeUserIdsGroup->get($media->id) ?? [];
                    $media['isLike'] = in_array($this->userId(), $noteLikeUserIds);

                    $noteCollectedUserIds = $noteCollectedUserIdsGroup->get($media->id) ?? [];
                    $media['isCollected'] = in_array($this->userId(), $noteCollectedUserIds);
                }
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
                    'type' => MediaType::VIDEO,
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
                    'type' => MediaType::NOTE,
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
                return [
                    'type' => MediaType::VIDEO,
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
                    'type' => MediaType::NOTE,
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

    public function commodityMediaList()
    {
        /** @var CommodityMediaPageInput $input */
        $input = CommodityMediaPageInput::new();

        $page = MediaCommodityService::getInstance()->getMediaPage($input);
        $mediaList = collect($page->items());

        $videoIds = $mediaList->filter(function (MediaCommodity $mediaCommodity) {
            return $mediaCommodity->media_type == MediaType::VIDEO;
        })->pluck('media_id')->toArray();
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds)->keyBy('id');

        $noteIds = $mediaList->filter(function (MediaCommodity $mediaCommodity) {
            return $mediaCommodity->media_type == MediaType::NOTE;
        })->pluck('media_id')->toArray();
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds)->keyBy('id');

        $list = $mediaList->map(function (MediaCommodity $mediaCommodity) use ($noteList, $videoList) {
            if ($mediaCommodity->media_type == MediaType::VIDEO) {
                /** @var ShortVideo $video */
                $video = $videoList->get($mediaCommodity->media_id);
                return [
                    'type' => MediaType::VIDEO,
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
                $note = $noteList->get($mediaCommodity->media_id);
                return [
                    'type' => MediaType::NOTE,
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
