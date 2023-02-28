<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\ShortVideo;
use App\Models\ShortVideoComment;
use App\Services\Media\MediaService;
use App\Services\Media\ShortVideo\ShortVideoCollectionService;
use App\Services\Media\ShortVideo\ShortVideoCommentService;
use App\Services\Media\ShortVideo\ShortVideoGoodsService;
use App\Services\Media\ShortVideo\ShortVideoPraiseService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MediaTypeEnums;
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

        $page = ShortVideoService::getInstance()->pageList($id, $input);
        $videoList = collect($page->items());

        $authorIds = $videoList->pluck('user_id')->toArray();
        $authorList = UserService::getInstance()->getUserListByIds($authorIds, ['id', 'name', 'avatar'])->keyBy('id');

        $list = $videoList->map(function (ShortVideo $video) use ($authorList) {
            $authorInfo = $authorList->get($video->user_id);
            $video['author_info'] = $authorInfo;
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

            MediaService::getInstance()->newMedia($this->userId(), $video->id, MediaTypeEnums::VIDEO);
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

            $media = MediaService::getInstance()->getMedia($video->id, MediaTypeEnums::VIDEO);
            $media->delete();
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

        $praise = ShortVideoPraiseService::getInstance()->getPraise($this->userId(), $id);
        $praiseNumber = DB::transaction(function () use ($video, $id, $praise) {
            if (!is_null($praise)) {
                $praise->delete();
                $praiseNumber = max($video->praise_number - 1, 0);
            } else {
                ShortVideoPraiseService::getInstance()->newPraise($this->userId(), $id);
                $praiseNumber = $video->praise_number + 1;
            }
            $video->praise_number = $praiseNumber;
            $video->save();

            $media = MediaService::getInstance()->getMedia($id, MediaTypeEnums::VIDEO);
            $media->praise_number = $praiseNumber;
            $media->save();

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

    public function increaseViewersNumber()
    {
        $id = $this->verifyRequiredId('id');

        /** @var ShortVideo $video */
        $video = ShortVideoService::getInstance()->getVideo($id);
        if (is_null($video)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        DB::transaction(function () use ($id, $video) {
            $viewersNumber = $video->viewers_number + 1;
            $video->viewers_number = $viewersNumber;
            $video->save();

            $media = MediaService::getInstance()->getMedia($id, MediaTypeEnums::VIDEO);
            $media->viewers_number = $viewersNumber;
            $media->save();
        });

        return $this->success();
    }

    public function share()
    {

    }

    public function getCommentList()
    {
        /** @var CommentListInput $input */
        $input = CommentListInput::new();

        $page = ShortVideoCommentService::getInstance()->pageList($input);
        $commentList = collect($page->items());

        $userIds = $commentList->pluck('user_id')->toArray();
        $userList = UserService::getInstance()->getUserListByIds($userIds, ['id', 'name', 'avatar'])->keyBy('id');

        $list = $commentList->map(function (ShortVideoComment $comment) use ($userList) {
            $userInfo = $userList->get($comment->user_id);
            $comment['user_info'] = $userInfo;
            unset($comment->user_id);
            return $comment;
        });

        return $this->success($this->paginate($page, $list));
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
