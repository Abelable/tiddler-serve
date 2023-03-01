<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShortVideo;
use App\Models\ShortVideoComment;
use App\Services\FanService;
use App\Services\Media\ShortVideo\ShortVideoCollectionService;
use App\Services\Media\ShortVideo\ShortVideoCommentService;
use App\Services\Media\ShortVideo\ShortVideoGoodsService;
use App\Services\Media\ShortVideo\ShortVideoPraiseService;
use App\Services\Media\ShortVideo\ShortVideoService;
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
        $id = $this->verifyRequiredId('id');

        $columns = ['id', 'user_id', 'video_url', 'title', 'praise_number', 'comments_number', 'collection_times', 'share_times', 'created_at'];
        $page = ShortVideoService::getInstance()->pageList($input, $columns, null, $id);
        $videoList = collect($page->items());

        $authorIds = $videoList->pluck('user_id')->toArray();
        $fansGroup = FanService::getInstance()->fansGroup($authorIds);

        $list = $videoList->map(function (ShortVideo $video) use ($fansGroup) {
            $video['is_follow'] = 0;
            if ($this->isLogin()) {
                $fansIds = collect($fansGroup->get($video->user_id))->pluck('fan_id')->toArray();
                if (in_array($this->userId(), $fansIds)) {
                    $video['is_follow'] = 1;
                }
            }
            unset($video->user_id);
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

    public function togglePraiseStatus()
    {
        $id = $this->verifyRequiredId('id');

        /** @var ShortVideo $video */
        $video = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        $praiseNumber = DB::transaction(function () use ($video, $id) {
            $praise = ShortVideoPraiseService::getInstance()->getPraise($this->userId(), $id);
            if (!is_null($praise)) {
                $praise->delete();
                $praiseNumber = max($video->praise_number - 1, 0);
            } else {
                ShortVideoPraiseService::getInstance()->newPraise($this->userId(), $id);
                $praiseNumber = $video->praise_number + 1;
            }
            $video->praise_number = $praiseNumber;
            $video->save();

            return $praiseNumber;
        });

        return $this->success($praiseNumber);
    }

    public function toggleCollectionStatus()
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

    public function share()
    {

    }

    public function getCommentList()
    {
        /** @var CommentListInput $input */
        $input = CommentListInput::new();

        $page = ShortVideoCommentService::getInstance()->pageList($input, ['id', 'content']);
        $commentList = collect($page->items());

        $ids = $commentList->pluck('id')->toArray();
        $repliesCountList = ShortVideoCommentService::getInstance()->repliesCountList($ids);

        $list = $commentList->map(function (ShortVideoComment $comment) use ($repliesCountList) {
            $comment['replies_count'] = $repliesCountList[$comment->id] ?? 0;
            return $comment;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function getReplyCommentList()
    {
        /** @var CommentListInput $input */
        $input = CommentListInput::new();
        $list = ShortVideoCommentService::getInstance()->pageList($input, ['id', 'content']);
        return $this->successPaginate($list);
    }

    public function comment()
    {
        /** @var CommentInput $input */
        $input = CommentInput::new();

        DB::transaction(function () use ($input) {
            ShortVideoCommentService::getInstance()->newComment($this->userId(), $input);

            $video = ShortVideoService::getInstance()->getVideo($input->mediaId);
            $video->comments_number = $video->comments_number + 1;
            $video->save();
        });

        // todo: 通知用户评论被回复

        return $this->success();
    }

    public function deleteComment()
    {
        $id = $this->verifyRequiredId('id');

        $comment = ShortVideoCommentService::getInstance()->getComment($this->userId(), $id);
        if (is_null($comment)) {
            return $this->fail(CodeResponse::NOT_FOUND, '评论不存在');
        }

        DB::transaction(function () use ($comment) {
            $comment->delete();

            $video = ShortVideoService::getInstance()->getVideo($comment->video_id);
            $video->comments_number = max($video->comments_number - 1, 0);
            $video->save();
        });

        return $this->success();
    }
}
