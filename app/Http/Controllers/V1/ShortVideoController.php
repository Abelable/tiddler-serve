<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShortVideo;
use App\Models\ShortVideoComment;
use App\Services\FanService;
use App\Services\Media\ShortVideo\ShortVideoCollectionService;
use App\Services\Media\ShortVideo\ShortVideoCommentService;
use App\Services\Media\ShortVideo\ShortVideoGoodsService;
use App\Services\Media\ShortVideo\ShortVideoLikeService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CommentInput;
use App\Utils\Inputs\CommentListInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShortVideoInput;
use Illuminate\Support\Facades\DB;

class ShortVideoController extends Controller
{
    protected $except = ['list'];

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyId('id', 0);
        $authorId = $this->verifyId('authorId', 0);

        $columns = ['id', 'user_id', 'cover', 'video_url', 'title', 'like_number', 'comments_number', 'collection_times', 'share_times', 'address', 'is_private'];
        $page = ShortVideoService::getInstance()->pageList($input, $columns, $authorId != 0 ? [$authorId] : null, $id);
        $videoList = collect($page->items());

        $authorIds = $videoList->pluck('user_id')->toArray();
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $fanIdsGroup = FanService::getInstance()->fanIdsGroup($authorIds);

        $videoIds = $videoList->pluck('id')->toArray();
        $likeUserIdsGroup = ShortVideoLikeService::getInstance()->likeUserIdsGroup($videoIds);
        $collectedUserIdsGroup = ShortVideoCollectionService::getInstance()->collectedUserIdsGroup($videoIds);

        $list = $videoList->map(function (ShortVideo $video) use ($collectedUserIdsGroup, $likeUserIdsGroup, $fanIdsGroup, $authorList) {
            $video['is_follow'] = false;
            if ($this->isLogin()) {
                $fansIds = $fanIdsGroup->get($video->user_id) ?? [];
                if (in_array($this->userId(), $fansIds) || $video->user_id == $this->userId()) {
                    $video['is_follow'] = true;
                }

                $likeUserIds = $likeUserIdsGroup->get($video->id) ?? [];
                if (in_array($this->userId(), $likeUserIds)) {
                    $video['is_like'] = true;
                }

                $collectedUserIds = $collectedUserIdsGroup->get($video->id) ?? [];
                if (in_array($this->userId(), $collectedUserIds)) {
                    $video['is_collected'] = true;
                }
            }

            $authorInfo = $authorList->get($video->user_id);
            $video['author_info'] = $authorInfo;
            unset($video->user_id);

            return $video;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function userVideoList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyId('id', 0);

        $columns = ['id', 'cover', 'video_url', 'title', 'like_number', 'comments_number', 'collection_times', 'share_times', 'address', 'is_private'];
        $page = ShortVideoService::getInstance()->pageList($input, $columns, [$this->userId()], $id);
        $list = collect($page->items())->map(function (ShortVideo $video) {
            $video['is_follow'] = true;
            $video['author_info'] = [
                'id' => $this->userId(),
                'avatar' => $this->user()->avatar,
                'nickname' => $this->user()->nickname
            ];
            return $video;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function createVideo()
    {
        /** @var ShortVideoInput $input */
        $input = ShortVideoInput::new();

        DB::transaction(function () use ($input) {
            $video = ShortVideoService::getInstance()->newVideo($this->userId(), $input);

            if (!empty($input->goodsId)) {
                ShortVideoGoodsService::getInstance()->newGoods($video->id, $input->goodsId);
            }
        });

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

            ShortVideoGoodsService::getInstance()->deleteList($video->id);
        });

        return $this->success();
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

    public function share()
    {

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
}
