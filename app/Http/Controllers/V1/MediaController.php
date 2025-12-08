<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Media\MediaProduct;
use App\Models\Media\Note\TourismNote;
use App\Models\Media\ShortVideo\ShortVideo;
use App\Models\Media\TopMedia;
use App\Services\FanService;
use App\Services\Mall\Goods\GoodsService;
use App\Services\Media\MediaProductService;
use App\Services\Media\MediaService;
use App\Services\Media\Note\TourismNoteCollectionService;
use App\Services\Media\Note\TourismNoteLikeService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoCollectionService;
use App\Services\Media\ShortVideo\ShortVideoLikeService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\Media\TopMediaService;
use App\Services\UserService;
use App\Utils\Enums\MediaType;
use App\Utils\Inputs\NearbyPageInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ProductMediaPageInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MediaController extends Controller
{
    protected $except = ['topList', 'list', 'randomList', 'nearbyList', 'search', 'productMediaList'];

    public function topList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        if ($input->page == 1) {
            $page = Cache::remember('top_media_cache', 1440, function () use ($input) {
                return $this->topPage($input);
            });
        } else {
            $page = $this->topPage($input);
        }

        return $this->success($page);
    }

    private function topPage(PageInput $input)
    {
        $page = TopMediaService::getInstance()->getTopMediaPage($input);

        $topMediaList = collect($page->items());

        $videoIds = $topMediaList->where('media_type', MediaType::VIDEO)->pluck('media_id')->toArray();
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds)->keyBy('id');

        $noteIds = $topMediaList->where('media_type', MediaType::NOTE)->pluck('media_id')->toArray();
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds)->keyBy('id');

        $videoAuthorIds = $videoList->pluck('user_id');
        $noteAuthorIds = $noteList->pluck('user_id');
        $authorIds = $videoAuthorIds->merge($noteAuthorIds)->unique()->values()->toArray();
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $topMediaList->map(function (TopMedia $topMedia) use ($authorList, $noteList, $videoList) {
            $media = $topMedia->media_type == MediaType::VIDEO
                ? $videoList->get($topMedia->media_id)
                : $noteList->get($topMedia->media_id);

            $media['type'] = $topMedia->media_type == MediaType::VIDEO ? MediaType::VIDEO : MediaType::NOTE;

            $authorInfo = $authorList->get($media['user_id']);
            $media['authorInfo'] = $authorInfo;
            unset($media['user_id']);

            $media['cover'] = $topMedia->cover;

            return $media;
        });

        return $this->paginate($page, $list);
    }

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        return $this->getMediaList($input);
    }

    public function randomList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        return $this->getMediaList($input, null, true, '', 2);
    }

    public function nearbyList()
    {
        /** @var NearbyPageInput $input */
        $input = NearbyPageInput::new();
        return $this->getMediaList($input, null, false, '', 3, $input->longitude, $input->latitude);
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
        $authorIds = UserService::getInstance()->searchUserIds($keywords);
        return $this->getMediaList($input, count($authorIds) == 0 ? null :  $authorIds, true, $keywords);
    }

    private function getMediaList(
        PageInput $input,
        $authorIds = null,
        $withLiveList = true,
        $keywords = '',
        $scene = 1,
        $longitude = null,
        $latitude = null
    )
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
            'longitude',
            'latitude',
            'views',
            'like_number',
            'comments_number',
            'collection_times',
            'share_times',
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
            'longitude',
            'latitude',
            'views',
            'like_number',
            'comments_number',
            'collection_times',
            'share_times',
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
            DB::raw('NULL as longitude'),
            DB::raw('NULL as latitude'),
            'views',
            DB::raw('NULL as like_number'),
            DB::raw('NULL as comments_number'),
            DB::raw('NULL as collection_times'),
            DB::raw('NULL as share_times'),
            'praise_number',
            'notice_time',
            'created_at',
        ];


        if ($scene == 1) {
            $page = MediaService::getInstance()
                ->mediaPage($input, $videoColumns, $noteColumns, $liveColumns, $authorIds, $withLiveList, $keywords);
        } else if ($scene == 2) {
            // todo 随机数据 - 在推荐算法之前，临时使用
            $page = MediaService::getInstance()->randomMediaPage($input, $videoColumns, $noteColumns, $liveColumns);
        } else {
            $page = MediaService::getInstance()->nearbyMediaPage($input, $longitude, $latitude, $videoColumns, $noteColumns);
        }
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

    public function productMediaList()
    {
        /** @var ProductMediaPageInput $input */
        $input = ProductMediaPageInput::new();

        if ($input->page == 1) {
            $cacheKey = sprintf(
                'product_media_list_%s_%s',
                $input->productType ?? 0,
                $input->productId ?? 0,
            );
            $page = Cache::remember($cacheKey, 1440, function () use ($input) {
                return MediaProductService::getInstance()->getMediaPage($input);
            });
        } else {
            $page = MediaProductService::getInstance()->getMediaPage($input);
        }

        $mediaList = collect($page->items());

        $videoIds = $mediaList->filter(function (MediaProduct $mediaProduct) {
            return $mediaProduct->media_type == MediaType::VIDEO;
        })->pluck('media_id')->toArray();
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds)->keyBy('id');

        $noteIds = $mediaList->filter(function (MediaProduct $mediaProduct) {
            return $mediaProduct->media_type == MediaType::NOTE;
        })->pluck('media_id')->toArray();
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds)->keyBy('id');

        $list = $mediaList->map(function (MediaProduct $mediaProduct) use ($noteList, $videoList) {
            if ($mediaProduct->media_type == MediaType::VIDEO) {
                /** @var ShortVideo $video */
                $video = $videoList->get($mediaProduct->media_id);
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
                $note = $noteList->get($mediaProduct->media_id);
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
