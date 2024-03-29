<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaCommodity;
use App\Models\TourismNote;
use App\Models\TourismNoteCollection;
use App\Models\TourismNoteComment;
use App\Models\TourismNoteLike;
use App\Services\FanService;
use App\Services\GoodsService;
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
    protected $except = ['list'];

    public function list()
    {
        /** @var TourismNotePageInput $input */
        $input = TourismNotePageInput::new();
        $page = TourismNoteService::getInstance()->pageList($input);
        $list = $this->handleList(collect($page->items()), $input->withComments);
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();

        KeywordService::getInstance()->addKeyword($this->userId(), $input->keywords);

        $page = TourismNoteService::getInstance()->search($input);
        $list = $this->handleList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    private function handleList($noteList, $withComments = 0)
    {
        $authorIds = $noteList->pluck('user_id')->toArray();
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $fanIdsGroup = FanService::getInstance()->fanIdsGroup($authorIds);

        $noteIds = $noteList->pluck('id')->toArray();

        $goodsColumns = ['id', 'name', 'image', 'price', 'market_price', 'stock', 'sales_volume'];
        [
            $mediaCommodityList,
            $scenicList,
            $hotelList,
            $restaurantList,
            $goodsList
        ] = MediaCommodityService::getInstance()->getListByMediaIds(2, $noteIds, ['*'], ['*'], ['*'], $goodsColumns);

        $likeUserIdsGroup = TourismNoteLikeService::getInstance()->likeUserIdsGroup($noteIds);
        $collectedUserIdsGroup = TourismNoteCollectionService::getInstance()->collectedUserIdsGroup($noteIds);

        return $noteList->map(function (TourismNote $note) use (
            $withComments,
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
            $isFollow = false;
            $isLike = false;
            $isCollected = false;
            if ($this->isLogin()) {
                $fansIds = $fanIdsGroup->get($note->user_id) ?? [];
                if (in_array($this->userId(), $fansIds) || $note->user_id == $this->userId()) {
                    $isFollow = true;
                }

                $likeUserIds = $likeUserIdsGroup->get($note->id) ?? [];
                if (in_array($this->userId(), $likeUserIds)) {
                    $isLike = true;
                }

                $collectedUserIds = $collectedUserIdsGroup->get($note->id) ?? [];
                if (in_array($this->userId(), $collectedUserIds)) {
                    $isCollected = true;
                }
            }

            if ($withComments == 1) {
                $comments = $note['commentList']->map(function ($comment) {
                    return [
                        'nickname' => $comment['userInfo']->nickname,
                        'content' => $comment['content']
                    ];
                });
            }

            /** @var MediaCommodity $commodity */
            $commodity = $mediaCommodityList->find($note->id);
            $scenicInfo = $scenicList->get($commodity->scenic_id) ?: null;
            $hotelInfo = $hotelList->get($commodity->hotel_id) ?: null;
            $restaurantInfo = $restaurantList->get($commodity->restaurant_id) ?: null;
            $goodsInfo = $goodsList->get($commodity->goods_id) ?: null;

            $note = [
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
                'createdAt' => $note->created_at,
            ];

            return $withComments == 1 ? array_merge(['comments' => $comments], $note) : $note;
        });
    }

    public function userNoteList()
    {
        /** @var TourismNotePageInput $input */
        $input = TourismNotePageInput::new();

        $columns = ['id', 'image_list', 'title', 'content', 'like_number', 'comments_number', 'collection_times', 'share_times', 'address', 'is_private', 'created_at'];
        $page = TourismNoteService::getInstance()->userPageList($this->userId(), $input, $columns);
        $noteList = collect($page->items());

        $noteIds = $noteList->pluck('id')->toArray();

        $goodsColumns = ['id', 'name', 'image', 'price', 'market_price', 'stock', 'sales_volume'];
        [
            $mediaCommodityList,
            $scenicList,
            $hotelList,
            $restaurantList,
            $goodsList
        ] = MediaCommodityService::getInstance()->getListByMediaIds(2, $noteIds, ['*'], ['*'], ['*'], $goodsColumns);

        $likeUserIdsGroup = TourismNoteLikeService::getInstance()->likeUserIdsGroup($noteIds);
        $collectedUserIdsGroup = TourismNoteCollectionService::getInstance()->collectedUserIdsGroup($noteIds);

        $list = $noteList->map(function (TourismNote $note) use (
            $input,
            $mediaCommodityList,
            $scenicList,
            $hotelList,
            $restaurantList,
            $goodsList,
            $collectedUserIdsGroup,
            $likeUserIdsGroup
        ) {
            $note->image_list = json_decode($note->image_list);

            $note['is_follow'] = true;

            $likeUserIds = $likeUserIdsGroup->get($note->id) ?? [];
            if (in_array($this->userId(), $likeUserIds)) {
                $note['is_like'] = true;
            }

            $collectedUserIds = $collectedUserIdsGroup->get($note->id) ?? [];
            if (in_array($this->userId(), $collectedUserIds)) {
                $note['is_collected'] = true;
            }

            $note['author_info'] = [
                'id' => $this->userId(),
                'avatar' => $this->user()->avatar,
                'nickname' => $this->user()->nickname
            ];

            if ($input->withComments) {
                $note['comments'] = $note['commentList']->map(function ($comment) {
                    return [
                        'nickname' => $comment['userInfo']->nickname,
                        'content' => $comment['content']
                    ];
                });
                unset($note['commentList']);
            }

            /** @var MediaCommodity $commodity */
            $commodity = $mediaCommodityList->find($note->id);
            $scenicInfo = $scenicList->get($commodity->scenic_id) ?: null;
            $hotelInfo = $hotelList->get($commodity->hotel_id) ?: null;
            $restaurantInfo = $restaurantList->get($commodity->restaurant_id) ?: null;
            $goodsInfo = $goodsList->get($commodity->goods_id) ?: null;

            $note['scenic_info'] = $scenicInfo;
            $note['hotel_info'] = $hotelInfo;
            $note['restaurant_info'] = $restaurantInfo;
            $note['goods_info'] = $goodsInfo;

            return $note;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function collectNoteList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyId('id', 0);

        $page = TourismNoteCollectionService::getInstance()->pageList($this->userId(), $input, $id);
        $collectNoteList = collect($page->items());

        $noteIds = $collectNoteList->pluck('note_id')->toArray();
        $columns = ['id', 'user_id', 'image_list', 'title', 'content', 'like_number', 'comments_number', 'collection_times', 'share_times', 'address', 'is_private', 'created_at'];
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds, $columns)->keyBy('id');

        $authorIds = $noteList->pluck('user_id')->toArray();
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $fanIdsGroup = FanService::getInstance()->fanIdsGroup($authorIds);

        $likeUserIdsGroup = TourismNoteLikeService::getInstance()->likeUserIdsGroup($noteIds);

        $goodsColumns = ['id', 'name', 'image', 'price', 'market_price', 'stock', 'sales_volume'];
        [
            $mediaCommodityList,
            $scenicList,
            $hotelList,
            $restaurantList,
            $goodsList
        ] = MediaCommodityService::getInstance()->getListByMediaIds(2, $noteIds, ['*'], ['*'], ['*'], $goodsColumns);

        $list = $collectNoteList->map(function (TourismNoteCollection $collect) use (
            $mediaCommodityList,
            $scenicList,
            $hotelList,
            $restaurantList,
            $goodsList,
            $authorList,
            $likeUserIdsGroup,
            $fanIdsGroup,
            $noteList
        ) {
            /** @var TourismNote $note */
            $note = $noteList->get($collect->note_id);

            $note->image_list = json_decode($note->image_list);

            $fansIds = $fanIdsGroup->get($note->user_id) ?? [];
            if (in_array($this->userId(), $fansIds) || $note->user_id == $this->userId()) {
                $note['is_follow'] = true;
            }

            $likeUserIds = $likeUserIdsGroup->get($note->id) ?? [];
            if (in_array($this->userId(), $likeUserIds)) {
                $note['is_like'] = true;
            }

            $note['is_collected'] = true;

            $authorInfo = $authorList->get($note->user_id);
            $note['author_info'] = $authorInfo;
            unset($note->user_id);

            $note['comments'] = $note['commentList']->map(function ($comment) {
                return [
                    'nickname' => $comment['userInfo']->nickname,
                    'content' => $comment['content']
                ];
            });
            unset($note['commentList']);

            /** @var MediaCommodity $commodity */
            $commodity = $mediaCommodityList->find($note->id);
            $scenicInfo = $scenicList->get($commodity->scenic_id) ?: null;
            $hotelInfo = $hotelList->get($commodity->hotel_id) ?: null;
            $restaurantInfo = $restaurantList->get($commodity->restaurant_id) ?: null;
            $goodsInfo = $goodsList->get($commodity->goods_id) ?: null;

            $note['scenic_info'] = $scenicInfo;
            $note['hotel_info'] = $hotelInfo;
            $note['restaurant_info'] = $restaurantInfo;
            $note['goods_info'] = $goodsInfo;

            return $note;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function likeNoteList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyId('id', 0);

        $page = TourismNoteLikeService::getInstance()->pageList($this->userId(), $input, $id);
        $likeNoteList = collect($page->items());

        $noteIds = $likeNoteList->pluck('note_id')->toArray();
        $columns = ['id', 'user_id', 'goods_id', 'image_list', 'title', 'content', 'like_number', 'comments_number', 'collection_times', 'share_times', 'address', 'is_private', 'created_at'];
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds, $columns)->keyBy('id');

        $authorIds = $noteList->pluck('user_id')->toArray();
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $fanIdsGroup = FanService::getInstance()->fanIdsGroup($authorIds);

        $collectedUserIdsGroup = TourismNoteCollectionService::getInstance()->collectedUserIdsGroup($noteIds);

        $goodsColumns = ['id', 'name', 'image', 'price', 'market_price', 'stock', 'sales_volume'];
        [
            $mediaCommodityList,
            $scenicList,
            $hotelList,
            $restaurantList,
            $goodsList
        ] = MediaCommodityService::getInstance()->getListByMediaIds(2, $noteIds, ['*'], ['*'], ['*'], $goodsColumns);

        $list = $likeNoteList->map(function (TourismNoteLike $like) use (
            $mediaCommodityList,
            $scenicList,
            $hotelList,
            $restaurantList,
            $goodsList,
            $authorList,
            $collectedUserIdsGroup,
            $fanIdsGroup,
            $noteList
        ) {
            /** @var TourismNote $note */
            $note = $noteList->get($like->note_id);

            $note->image_list = json_decode($note->image_list);

            $fansIds = $fanIdsGroup->get($note->user_id) ?? [];
            if (in_array($this->userId(), $fansIds) || $note->user_id == $this->userId()) {
                $note['is_follow'] = true;
            }

            $note['is_like'] = true;

            $collectedUserIds = $collectedUserIdsGroup->get($note->id) ?? [];
            if (in_array($this->userId(), $collectedUserIds)) {
                $note['is_collected'] = true;
            }

            $authorInfo = $authorList->get($note->user_id);
            $note['author_info'] = $authorInfo;
            unset($note->user_id);

            $note['comments'] = $note['commentList']->map(function ($comment) {
                return [
                    'nickname' => $comment['userInfo']->nickname,
                    'content' => $comment['content']
                ];
            });
            unset($note['commentList']);

            /** @var MediaCommodity $commodity */
            $commodity = $mediaCommodityList->find($note->id);
            $scenicInfo = $scenicList->get($commodity->scenic_id) ?: null;
            $hotelInfo = $hotelList->get($commodity->hotel_id) ?: null;
            $restaurantInfo = $restaurantList->get($commodity->restaurant_id) ?: null;
            $goodsInfo = $goodsList->get($commodity->goods_id) ?: null;

            $note['scenic_info'] = $scenicInfo;
            $note['hotel_info'] = $hotelInfo;
            $note['restaurant_info'] = $restaurantInfo;
            $note['goods_info'] = $goodsInfo;

            return $note;
        });

        return $this->success($this->paginate($page, $list));
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
