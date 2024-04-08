<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaCommodity;
use App\Models\TourismNote;
use App\Models\TourismNoteComment;
use App\Services\FanService;
use App\Services\KeywordService;
use App\Services\Media\MediaCommodityService;
use App\Services\Media\Note\TourismNoteCollectionService;
use App\Services\Media\Note\TourismNoteCommentService;
use App\Services\Media\Note\TourismNoteLikeService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CommentInput;
use App\Utils\Inputs\CommentListInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\TourismNoteInput;
use App\Utils\Inputs\TourismNotePageInput;
use Illuminate\Support\Facades\DB;

class TourismNoteController extends Controller
{
    protected $except = ['list', 'search'];

    public function list()
    {
        /** @var TourismNotePageInput $input */
        $input = TourismNotePageInput::new();
        $page = TourismNoteService::getInstance()->pageList($input);
        $list = $this->handleList(collect($page->items()), $this->isLogin());
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();

        if ($this->isLogin()) {
            KeywordService::getInstance()->addKeyword($this->userId(), $input->keywords);
        }

        $page = TourismNoteService::getInstance()->search($input);
        $list = $this->handleList(collect($page->items()), $this->isLogin());
        return $this->success($this->paginate($page, $list));
    }

    public function userNoteList()
    {
        /** @var TourismNotePageInput $input */
        $input = TourismNotePageInput::new();
        $page = TourismNoteService::getInstance()->userPageList($this->userId(), $input);
        $list = $this->handleList(collect($page->items()), true, true);
        return $this->success($this->paginate($page, $list));
    }

    public function likeNoteList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyId('id', 0);

        $page = TourismNoteLikeService::getInstance()->pageList($this->userId(), $input, $id);
        $noteIds = collect($page->items())->pluck('note_id')->toArray();
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds);
        $list = $this->handleList($noteList, true, false, true);

        return $this->success($this->paginate($page, $list));
    }

    public function collectNoteList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyId('id', 0);

        $page = TourismNoteCollectionService::getInstance()->pageList($this->userId(), $input, $id);
        $noteIds = collect($page->items())->pluck('note_id')->toArray();
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds);
        $list = $this->handleList($noteList, true, false, false, true);

        return $this->success($this->paginate($page, $list));
    }

    private function handleList($noteList, $isLogin, $isUserList = false, $isLikeList = false, $isCollectList = false)
    {
        $goodsColumns = ['id', 'name', 'image', 'price', 'market_price', 'stock', 'sales_volume'];
        $noteIds = $noteList->pluck('id')->toArray();
        $authorIds = $noteList->pluck('user_id')->toArray();

        $likeUserIdsGroup = TourismNoteLikeService::getInstance()->likeUserIdsGroup($noteIds);
        $collectedUserIdsGroup = TourismNoteCollectionService::getInstance()->collectedUserIdsGroup($noteIds);
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $fanIdsGroup = FanService::getInstance()->fanIdsGroup($authorIds);
        [
            $mediaCommodityList,
            $scenicList,
            $hotelList,
            $restaurantList,
            $goodsList
        ] = MediaCommodityService::getInstance()->getListByMediaIds(2, $noteIds, ['*'], ['*'], ['*'], $goodsColumns);

        return $noteList->map(function (TourismNote $note) use (
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
            $authorList,
            $fanIdsGroup
        ) {
            if ($isUserList) {
                $isFollow = true;
            } else {
                $isFollow = false;
                if ($isLogin) {
                    $fansIds = $fanIdsGroup->get($note->user_id) ?? [];
                    if (in_array($this->userId(), $fansIds) || $note->user_id == $this->userId()) {
                        $isFollow = true;
                    }
                }
            }

            if ($isLikeList) {
                $isLike = true;
            } else {
                $isLike = false;
                if ($isLogin) {
                    $likeUserIds = $likeUserIdsGroup->get($note->id) ?? [];
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
                    $collectedUserIds = $collectedUserIdsGroup->get($note->id) ?? [];
                    if (in_array($this->userId(), $collectedUserIds)) {
                        $isCollected = true;
                    }
                }
            }

            /** @var MediaCommodity $commodity */
            $commodity = $mediaCommodityList->find($note->id);
            $scenicInfo = $commodity ? $scenicList->get($commodity->scenic_id) : null;
            $hotelInfo = $commodity ? $hotelList->get($commodity->hotel_id) : null;
            $restaurantInfo = $commodity ? $restaurantList->get($commodity->restaurant_id) : null;
            $goodsInfo = $commodity ? $goodsList->get($commodity->goods_id) : null;

            return [
                'id' => $note->id,
                'imageList' => json_decode($note->image_list),
                'title' => $note->title,
                'content' => $note->content,
                'likeNumber' => $note->like_number,
                'commentsNumber' => $note->comments_number,
                'collectionTimes' => $note->collection_times,
                'shareTimes' => $note->share_times,
                'address' => $note->address,
                'authorInfo' => $authorList->get($note->user_id),
                'scenicInfo' => $scenicInfo,
                'hotelInfo' => $hotelInfo,
                'restaurantInfo' => $restaurantInfo,
                'goodsInfo' => $goodsInfo,
                'isFollow' => $isFollow,
                'isLike' => $isLike,
                'isCollected' => $isCollected,
                'comments' => $note['commentList']->map(function ($comment) {
                    return [
                        'nickname' => $comment['userInfo']->nickname,
                        'content' => $comment['content']
                    ];
                }),
                'createdAt' => $note->created_at,
            ];
        });
    }

    public function createNote()
    {
        /** @var TourismNoteInput $input */
        $input = TourismNoteInput::new();

        DB::transaction(function () use ($input) {
            $note = TourismNoteService::getInstance()->newNote($this->userId(), $input);
            if (!empty($input->scenicId) || !empty($input->hotelId) || !empty($input->restaurantId) || !empty($input->goodsId)) {
                MediaCommodityService::getInstance()->createMediaCommodity(
                    2,
                    $note->id,
                    $input->scenicId,
                    $input->hotelId,
                    $input->restaurantId,
                    $input->goodsId,
                );
            }

        });

        return $this->success();
    }

    public function togglePrivate()
    {
        $id = $this->verifyRequiredId('id');

        $note = TourismNoteService::getInstance()->getUserNote($this->userId(), $id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '短视频不存在');
        }

        $note->is_private = $note->is_private ? 0 : 1;
        $note->save();

        return $this->success();
    }

    public function deleteNote()
    {
        $id = $this->verifyRequiredId('id');

        $note = TourismNoteService::getInstance()->getUserNote($this->userId(), $id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '游记不存在');
        }

        DB::transaction(function () use ($note) {
            $note->delete();
            TourismNoteCollectionService::getInstance()->deleteList($note->id);
            TourismNoteLikeService::getInstance()->deleteList($note->id);
        });

        return $this->success();
    }

    public function toggleLikeStatus()
    {
        $id = $this->verifyRequiredId('id');

        /** @var TourismNote $note */
        $note = TourismNoteService::getInstance()->getNote($id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '游记不存在');
        }

        $likeNumber = DB::transaction(function () use ($note, $id) {
            $like = TourismNoteLikeService::getInstance()->getLike($this->userId(), $id);
            if (!is_null($like)) {
                $like->delete();
                $likeNumber = max($note->like_number - 1, 0);
            } else {
                TourismNoteLikeService::getInstance()->newLike($this->userId(), $id);
                $likeNumber = $note->like_number + 1;
            }
            $note->like_number = $likeNumber;
            $note->save();

            return $likeNumber;
        });

        return $this->success($likeNumber);
    }

    public function toggleCollectionStatus()
    {
        $id = $this->verifyRequiredId('id');

        /** @var TourismNote $note */
        $note = TourismNoteService::getInstance()->getNote($id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '游记不存在');
        }

        $collectionTimes = DB::transaction(function () use ($id, $note) {
            $collection = TourismNoteCollectionService::getInstance()->getCollection($this->userId(), $id);
            if (!is_null($collection)) {
                $collection->delete();
                $collectionTimes = max($note->collection_times - 1, 0);
            } else {
                TourismNoteCollectionService::getInstance()->newCollection($this->userId(), $id);
                $collectionTimes = $note->collection_times + 1;
            }
            $note->collection_times = $collectionTimes;
            $note->save();

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

        $page = TourismNoteCommentService::getInstance()->pageList($input);
        $commentList = collect($page->items());

        $ids = $commentList->pluck('id')->toArray();
        $repliesCountList = TourismNoteCommentService::getInstance()->repliesCountList($ids);

        $list = $commentList->map(function (TourismNoteComment $comment) use ($repliesCountList) {
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
        $page = TourismNoteCommentService::getInstance()->pageList($input);
        $list = collect($page->items())->map(function (TourismNoteComment $comment) {
            return [
                'id' => $comment->id,
                'userInfo' => $comment->userInfo,
                'content' => $comment->content,
                'createdAt' => $comment->created_at
            ];
        });

        return $this->success($this->paginate($page, $list));
    }

    public function comment()
    {
        /** @var CommentInput $input */
        $input = CommentInput::new();

        /** @var TourismNote $note */
        $note = TourismNoteService::getInstance()->getNote($input->mediaId);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '游记不存在');
        }

        /** @var TourismNoteComment $comment */
        $comment = DB::transaction(function () use ($input) {
            $comment = TourismNoteCommentService::getInstance()->newComment($this->userId(), $input);

            $note = TourismNoteService::getInstance()->getNote($input->mediaId);
            $note->comments_number = $note->comments_number + 1;
            $note->save();

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

    public function deleteComment()
    {
        $id = $this->verifyRequiredId('id');

        $comment = TourismNoteCommentService::getInstance()->getComment($this->userId(), $id);
        if (is_null($comment)) {
            return $this->fail(CodeResponse::NOT_FOUND, '评论不存在');
        }

        $commentsNumber = DB::transaction(function () use ($comment) {
            $comment->delete();

            $count = TourismNoteCommentService::getInstance()->deleteReplies($this->userId(), $comment->id);

            $note = TourismNoteService::getInstance()->getNote($comment->note_id);
            $note->comments_number = max($note->comments_number - 1 - $count, 0);
            $note->save();

            return $note->comments_number;
        });

        return $this->success($commentsNumber);
    }
}
