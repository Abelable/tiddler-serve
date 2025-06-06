<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaCommodity;
use App\Models\ScenicSpot;
use App\Models\ShortVideo;
use App\Models\ShortVideoComment;
use App\Services\FanService;
use App\Services\KeywordService;
use App\Services\Media\ShortVideo\ShortVideoCollectionService;
use App\Services\Media\ShortVideo\ShortVideoCommentService;
use App\Services\Media\ShortVideo\ShortVideoLikeService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\MediaCommodityService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ProductType;
use App\Utils\Enums\MediaType;
use App\Utils\Inputs\CommentInput;
use App\Utils\Inputs\CommentListInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\ShortVideoInput;
use App\Utils\Inputs\TempShortVideoInput;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\DB;

class ShortVideoController extends Controller
{
    protected $except = ['list', 'search', 'createTempVideo', 'addLikes'];

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyId('id', 0);
        $authorId = $this->verifyId('authorId', 0);
        $page = ShortVideoService::getInstance()->pageList($input, $authorId != 0 ? [$authorId] : null, $id);
        $list = $this->handleList(collect($page->items()), $this->isLogin());
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();
        $page = ShortVideoService::getInstance()->search($input);
        $list = $this->handleList(collect($page->items()), $this->isLogin());
        return $this->success($this->paginate($page, $list));
    }

    public function userVideoList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyId('id', 0);

        $page = ShortVideoService::getInstance()->userPageList($input, $this->userId(), $id);
        $list = $this->handleList(collect($page->items()), true, true);

        return $this->success($this->paginate($page, $list));
    }

    public function likeVideoList() {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyId('id', 0);

        $page = ShortVideoLikeService::getInstance()->pageList($this->userId(), $input, $id);
        $videoIds = collect($page->items())->pluck('video_id')->toArray();
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds);
        $list = $this->handleList($videoList, true, false, true);

        return $this->success($this->paginate($page, $list));
    }

    public function collectVideoList() {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyId('id', 0);

        $page = ShortVideoCollectionService::getInstance()->pageList($this->userId(), $input, $id);
        $videoIds = collect($page->items())->pluck('video_id')->toArray();
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds);
        $list = $this->handleList($videoList, true, false, false, true);

        return $this->success($this->paginate($page, $list));
    }

    private function handleList($videoList, $isLogin, $isUserList = false, $isLikeList = false, $isCollectList = false)
    {
        $scenicColumns = ['id', 'name', 'image_list', 'level', 'score', 'price', 'sales_volume'];
        $hotelColumns = ['id', 'category_id', 'name', 'english_name', 'cover', 'grade', 'price', 'sales_volume'];
        $restaurantColumns = ['id', 'category_id', 'name', 'cover', 'price', 'score', 'sales_volume'];
        $goodsColumns = ['id', 'name', 'cover', 'price', 'market_price', 'stock', 'sales_volume', 'sales_volume'];
        $videoIds = $videoList->pluck('id')->toArray();
        $authorIds = $videoList->pluck('user_id')->toArray();

        $likeUserIdsGroup = ShortVideoLikeService::getInstance()->likeUserIdsGroup($videoIds);
        $collectedUserIdsGroup = ShortVideoCollectionService::getInstance()->collectedUserIdsGroup($videoIds);
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $fanIdsGroup = FanService::getInstance()->fanIdsGroup($authorIds);

        $relatedProductInfo = MediaCommodityService::getInstance()
            ->getFilterListByMediaIds(MediaType::VIDEO, $videoIds, $scenicColumns, $hotelColumns, $restaurantColumns, $goodsColumns);
        $mediaCommodityList = $relatedProductInfo['mediaList'];
        $scenicList = $relatedProductInfo['scenicList'];
        $hotelList = $relatedProductInfo['hotelList'];
        $restaurantList = $relatedProductInfo['restaurantList'];
        $goodsList = $relatedProductInfo['goodsList'];

        return $videoList->map(function (ShortVideo $video) use (
            $isUserList,
            $isCollectList,
            $isLikeList,
            $isLogin,
            $mediaCommodityList,
            $scenicList,
            $hotelList,
            $restaurantList,
            $goodsList,
            $collectedUserIdsGroup,
            $likeUserIdsGroup,
            $fanIdsGroup,
            $authorList
        ) {
            if ($isUserList) {
                $isFollow = true;
            } else {
                $isFollow = false;
                if ($isLogin) {
                    $fansIds = $fanIdsGroup->get($video->user_id) ?? [];
                    if (in_array($this->userId(), $fansIds) || $video->user_id == $this->userId()) {
                        $isFollow = true;
                    }
                }
            }

            if ($isLikeList) {
                $isLike = true;
            } else {
                $isLike = false;
                if ($isLogin) {
                    $likeUserIds = $likeUserIdsGroup->get($video->id) ?? [];
                    if (in_array($this->userId(), $likeUserIds)) {
                        $isLike = true;
                    }
                }
            }

            if ($isCollectList) {
                $isCollected = true;
            } else {
                $isCollected = false;
                if ($isLogin) {
                    $collectedUserIds = $collectedUserIdsGroup->get($video->id) ?? [];
                    if (in_array($this->userId(), $collectedUserIds)) {
                        $isCollected = true;
                    }
                }
            }

            $commodityList = $mediaCommodityList->filter(function (MediaCommodity $commodity) use ($video) {
                return $commodity->media_id == $video->id;
            })->map(function (MediaCommodity $commodity) use ($goodsList, $restaurantList, $hotelList, $scenicList) {
                $info = null;
                switch ($commodity->commodity_type) {
                    case ProductType::SCENIC:
                        /** @var ScenicSpot $info */
                        $info = $scenicList->get($commodity->commodity_id);
                        if ($info->image_list) {
                            $info['cover'] = json_decode($info->image_list)[0];
                            unset($info->image_list);
                        }
                        break;
                    case ProductType::HOTEL:
                        $info = $hotelList->get($commodity->commodity_id);
                        break;
                    case ProductType::RESTAURANT:
                        $info = $restaurantList->get($commodity->commodity_id);
                        break;
                    case ProductType::GOODS:
                        $info = $goodsList->get($commodity->commodity_id);
                        break;
                }
                $info['type'] = $commodity->commodity_type;
                return $info;
            })->values()->toArray();

            return [
                'id' => $video->id,
                'cover' => $video->cover,
                'videoUrl' => $video->video_url,
                'title' => $video->title,
                'likeNumber' => $video->like_number,
                'commentsNumber' => $video->comments_number,
                'collectionTimes' => $video->collection_times,
                'shareTimes' => $video->share_times,
                'address' => $video->address,
                'authorInfo' => $authorList->get($video->user_id),
                'commodityList' => $commodityList,
                'isFollow' => $isFollow,
                'isLike' => $isLike,
                'isCollected' => $isCollected,
            ];
        });
    }

    public function createVideo()
    {
        /** @var ShortVideoInput $input */
        $input = ShortVideoInput::new();

        DB::transaction(function () use ($input) {
            $video = ShortVideoService::getInstance()->newVideo($this->userId(), $input);
            foreach ($input->commodityList as $commodity) {
                MediaCommodityService::getInstance()->createMediaCommodity(
                    MediaType::VIDEO,
                    $video->id,
                    $commodity['type'],
                    $commodity['id'],
                );
            }
        });

        return $this->success();
    }

    public function createTempVideo()
    {
        /** @var TempShortVideoInput $input */
        $input = TempShortVideoInput::new();

        $video = ShortVideoService::getInstance()->getVideoByTitle($input->title);
        if (is_null($video)) {
            DB::transaction(function () use ($input) {
                $video = ShortVideoService::getInstance()->newVideo($input->userId, $input);
                MediaCommodityService::getInstance()->createMediaCommodity(
                    MediaType::VIDEO,
                    $video->id,
                    $input->commodityType,
                    $input->commodityId,
                );
            });
        }

        return $this->success();
    }

    public function togglePrivate()
    {
        $id = $this->verifyRequiredId('id');

        $video = ShortVideoService::getInstance()->getUserVideo($this->userId(), $id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        $video->is_private = $video->is_private ? 0 : 1;
        $video->save();

        return $this->success();
    }

    public function deleteVideo()
    {
        $id = $this->verifyRequiredId('id');

        $video = ShortVideoService::getInstance()->getUserVideo($this->userId(), $id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        DB::transaction(function () use ($video) {
            $video->delete();
            ShortVideoCollectionService::getInstance()->deleteList($video->id);
            ShortVideoLikeService::getInstance()->deleteList($video->id);
            MediaCommodityService::getInstance()->deleteList(MediaType::VIDEO, $video->id);
        });

        return $this->success();
    }

    public function share()
    {
        $id = $this->verifyRequiredId('id');
        $scene = $this->verifyRequiredString('scene');
        $page = $this->verifyRequiredString('page');

        /** @var ShortVideo $video */
        $video = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        $shareTimes = $video->share_times + 1;
        $video->share_times = $shareTimes;
        $video->save();

        $imageData = WxMpServe::new()->getQRCode($scene, $page);
        $qrcode = 'data:image/png;base64,' . base64_encode($imageData);

        return $this->success(['qrcode' => $qrcode, 'shareTimes' => $shareTimes]);
    }

    public function toggleLikeStatus()
    {
        $id = $this->verifyRequiredId('id');

        /** @var ShortVideo $video */
        $video = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        $likeNumber = DB::transaction(function () use ($video, $id) {
            $like = ShortVideoLikeService::getInstance()->getLike($this->userId(), $id);
            if (!is_null($like)) {
                $like->delete();
                $likeNumber = max($video->like_number - 1, 0);
            } else {
                ShortVideoLikeService::getInstance()->newLike($this->userId(), $id);
                $likeNumber = $video->like_number + 1;
            }
            $video->like_number = $likeNumber;
            $video->save();

            return $likeNumber;
        });

        return $this->success($likeNumber);
    }

    public function toggleCollectStatus()
    {
        $id = $this->verifyRequiredId('id');

        /** @var ShortVideo $video */
        $video = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        $collection = ShortVideoCollectionService::getInstance()->getCollection($this->userId(), $id);
        $collectionTimes = DB::transaction(function () use ($id, $video, $collection) {
            if (!is_null($collection)) {
                $collection->delete();
                $collectionTimes = max($video->collection_times - 1, 0);
            } else {
                ShortVideoCollectionService::getInstance()->newCollection($this->userId(), $id);
                $collectionTimes = $video->collection_times + 1;
            }
            $video->collection_times = $collectionTimes;
            $video->save();

            return $collectionTimes;
        });

        return $this->success($collectionTimes);
    }

    public function comment()
    {
        /** @var CommentInput $input */
        $input = CommentInput::new();

        /** @var ShortVideo $video */
        $video = ShortVideoService::getInstance()->getVideo($input->mediaId);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        /** @var ShortVideoComment $comment */
        $comment = DB::transaction(function () use ($video, $input) {
            $comment = ShortVideoCommentService::getInstance()->newComment($this->userId(), $input);

            $video->comments_number = $video->comments_number + 1;
            $video->save();

            return $comment;
        });

        // todo: 通知用户评论被回复

        return $this->success([
            'id' => $comment->id,
            'userInfo' => $comment->userInfo,
            'content' => $comment->content,
            'createdAt' => $comment->created_at
        ]);
    }

    public function getCommentList()
    {
        /** @var CommentListInput $input */
        $input = CommentListInput::new();

        $page = ShortVideoCommentService::getInstance()->pageList($input);
        $commentList = collect($page->items());

        $ids = $commentList->pluck('id')->toArray();
        $repliesCountList = ShortVideoCommentService::getInstance()->repliesCountList($ids);

        $list = $commentList->map(function (ShortVideoComment $comment) use ($repliesCountList) {
            return [
                'id' => $comment->id,
                'userInfo' => $comment->userInfo,
                'content' => $comment->content,
                'repliesCount' => $repliesCountList[$comment->id] ?? 0,
                'createdAt' => $comment->created_at
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function getReplyCommentList()
    {
        /** @var CommentListInput $input */
        $input = CommentListInput::new();
        $page = ShortVideoCommentService::getInstance()->pageList($input);
        $list = collect($page->items())->map(function (ShortVideoComment $comment) {
            return [
                'id' => $comment->id,
                'userInfo' => $comment->userInfo,
                'content' => $comment->content,
                'createdAt' => $comment->created_at
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function deleteComment()
    {
        $id = $this->verifyRequiredId('id');

        $comment = ShortVideoCommentService::getInstance()->getComment($this->userId(), $id);
        if (is_null($comment)) {
            return $this->fail(CodeResponse::NOT_FOUND, '评论不存在');
        }

        $commentsNumber = DB::transaction(function () use ($comment) {
            $comment->delete();

            $count = ShortVideoCommentService::getInstance()->deleteReplies($this->userId(), $comment->id);

            $video = ShortVideoService::getInstance()->getVideo($comment->video_id);
            $video->comments_number = max($video->comments_number - 1 - $count, 0);
            $video->save();

            return $video->comments_number;
        });

        return $this->success($commentsNumber);
    }

    public function addLikes()
    {
        $list = ShortVideoService::getInstance()->getList();
        /** @var ShortVideo $video */
        foreach ($list as $video) {
            if ($video->like_number == 0) {
                $video->like_number = mt_rand(100, 180);
                $video->save();
            }
        }
        return $this->success();
    }
}
