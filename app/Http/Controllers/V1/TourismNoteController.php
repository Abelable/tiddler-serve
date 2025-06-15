<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaProduct;
use App\Models\ScenicSpot;
use App\Models\TourismNote;
use App\Models\TourismNoteComment;
use App\Services\FanService;
use App\Services\Media\Note\TourismNoteCollectionService;
use App\Services\Media\Note\TourismNoteCommentService;
use App\Services\Media\Note\TourismNoteLikeService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\MediaHistoryService;
use App\Services\MediaProductService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\ProductType;
use App\Utils\Enums\MediaType;
use App\Utils\Inputs\CommentInput;
use App\Utils\Inputs\CommentListInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\TempTourismNoteInput;
use App\Utils\Inputs\TourismNoteInput;
use App\Utils\Inputs\TourismNotePageInput;
use App\Utils\WxMpServe;
use Illuminate\Support\Facades\DB;

class TourismNoteController extends Controller
{
    protected $except = ['list', 'detail', 'search', 'createTempNote'];

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
        $scenicColumns = ['id', 'name', 'image_list', 'level', 'score', 'price', 'sales_volume'];
        $hotelColumns = ['id', 'category_id', 'name', 'english_name', 'cover', 'grade', 'price', 'sales_volume'];
        $restaurantColumns = ['id', 'category_id', 'name', 'cover', 'price', 'score', 'sales_volume'];
        $goodsColumns = ['id', 'name', 'cover', 'price', 'market_price', 'stock', 'sales_volume', 'sales_volume'];
        $noteIds = $noteList->pluck('id')->toArray();
        $authorIds = $noteList->pluck('user_id')->toArray();

        $likeUserIdsGroup = TourismNoteLikeService::getInstance()->likeUserIdsGroup($noteIds);
        $collectedUserIdsGroup = TourismNoteCollectionService::getInstance()->collectedUserIdsGroup($noteIds);
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');
        $fanIdsGroup = FanService::getInstance()->fanIdsGroup($authorIds);

        $relatedProductInfo = MediaProductService::getInstance()
            ->getFilterListByMediaIds(MediaType::NOTE, $noteIds, $scenicColumns, $hotelColumns, $restaurantColumns, $goodsColumns);
        $mediaProductList = $relatedProductInfo['mediaList'];
        $scenicList = $relatedProductInfo['scenicList'];
        $hotelList = $relatedProductInfo['hotelList'];
        $restaurantList = $relatedProductInfo['restaurantList'];
        $goodsList = $relatedProductInfo['goodsList'];

        return $noteList->map(function (TourismNote $note) use (
            $isUserList,
            $isCollectList,
            $isLikeList,
            $isLogin,
            $mediaProductList,
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

            $productList = $mediaProductList->filter(function (MediaProduct $product) use ($note) {
                return $product->media_id == $note->id;
            })->map(function (MediaProduct $product) use ($goodsList, $restaurantList, $hotelList, $scenicList) {
                $info = null;
                switch ($product->product_type) {
                    case ProductType::SCENIC:
                        /** @var ScenicSpot $info */
                        $info = $scenicList->get($product->product_id);
                        if ($info->image_list) {
                            $info['cover'] = json_decode($info->image_list)[0];
                            unset($info->image_list);
                        }
                        break;
                    case ProductType::HOTEL:
                        $info = $hotelList->get($product->product_id);
                        break;
                    case ProductType::RESTAURANT:
                        $info = $restaurantList->get($product->product_id);
                        break;
                    case ProductType::GOODS:
                        $info = $goodsList->get($product->product_id);
                        break;
                }
                $info['type'] = $product->product_type;
                return $info;
            })->values()->toArray();

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
                'productList' => $productList,
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

    public function detail()
    {
        $scenicColumns = ['id', 'name', 'image_list', 'level', 'score', 'price', 'sales_volume'];
        $hotelColumns = ['id', 'category_id', 'name', 'english_name', 'cover', 'grade', 'price', 'sales_volume'];
        $restaurantColumns = ['id', 'category_id', 'name', 'cover', 'price', 'score', 'sales_volume'];
        $goodsColumns = ['id', 'name', 'cover', 'price', 'market_price', 'stock', 'sales_volume', 'sales_volume'];
        $id = $this->verifyRequiredId('id');

        $note = TourismNoteService::getInstance()->getNote($id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '游记不存在');
        }

        if ($this->isLogin()) {
            DB::transaction(function () use ($note) {
                TourismNoteService::getInstance()->updateViews($note);
                MediaHistoryService::getInstance()->createHistory($this->userId(), MediaType::NOTE, $note->id);
            });
        }

        $relatedProductInfo = MediaProductService::getInstance()
            ->getFilterListByMediaIds(MediaType::NOTE, [$id], $scenicColumns, $hotelColumns, $restaurantColumns, $goodsColumns);
        $mediaProductList = $relatedProductInfo['mediaList'];
        $scenicList = $relatedProductInfo['scenicList'];
        $hotelList = $relatedProductInfo['hotelList'];
        $restaurantList = $relatedProductInfo['restaurantList'];
        $goodsList = $relatedProductInfo['goodsList'];
        $productList = $mediaProductList->map(function (MediaProduct $product) use ($goodsList, $restaurantList, $hotelList, $scenicList) {
            $info = null;
            switch ($product->product_type) {
                case ProductType::SCENIC:
                    /** @var ScenicSpot $info */
                    $info = $scenicList->get($product->product_id);
                    if ($info->image_list) {
                        $info['cover'] = json_decode($info->image_list)[0];
                        unset($info->image_list);
                    }
                    break;
                case ProductType::HOTEL:
                    $info = $hotelList->get($product->product_id);
                    break;
                case ProductType::RESTAURANT:
                    $info = $restaurantList->get($product->product_id);
                    break;
                case ProductType::GOODS:
                    $info = $goodsList->get($product->product_id);
                    break;
            }
            $info['type'] = $product->product_type;
            return $info;
        })->values()->toArray();

        $note['productList'] = $productList;
        $note->image_list = json_decode($note->image_list);
        $note['authorInfo'] = $note->authorInfo;

        $isFollow = false;
        $isLike = false;
        $isCollected = false;
        if ($this->isLogin()) {
            $fan = FanService::getInstance()->fan($note->user_id, $this->userId());
            if (!is_null($fan) || $note->user_id == $this->userId()) {
                $isFollow = true;
            }

            $like = TourismNoteLikeService::getInstance()->getLike($this->userId(), $id);
            if (!is_null($like)) {
                $isLike = true;
            }

            $collect = TourismNoteCollectionService::getInstance()->getCollection($this->userId(), $id);
            if (!is_null($collect)) {
                $isCollected = true;
            }
        }
        $note['isFollow'] = $isFollow;
        $note['isLike'] = $isLike;
        $note['isCollected'] = $isCollected;

        return $this->success($note);
    }

    public function createNote()
    {
        /** @var TourismNoteInput $input */
        $input = TourismNoteInput::new();

        DB::transaction(function () use ($input) {
            $note = TourismNoteService::getInstance()->createNote($this->userId(), $input);
            foreach ($input->scenicIds as $scenicId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::SCENIC,
                    $scenicId,
                );
            }

            foreach ($input->hotelIds as $hotelId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::HOTEL,
                    $hotelId,
                );
            }

            foreach ($input->restaurantIds as $restaurantId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::RESTAURANT,
                    $restaurantId,
                );
            }

            foreach ($input->goodsIds as $goodsId) {
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    ProductType::GOODS,
                    $goodsId,
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
            return $this->fail(CodeResponse::NOT_FOUND, '游记不存在');
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
            MediaProductService::getInstance()->deleteList(MediaType::NOTE, $note->id);
        });

        return $this->success();
    }

    public function share()
    {
        $id = $this->verifyRequiredId('id');
        $scene = $this->verifyRequiredString('scene');
        $page = $this->verifyRequiredString('page');

        /** @var TourismNote $note */
        $note = TourismNoteService::getInstance()->getNote($id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '游记不存在');
        }

        $shareTimes = $note->share_times + 1;
        $note->share_times = $shareTimes;
        $note->save();

        $imageData = WxMpServe::new()->getQRCode($scene, $page);
        $qrcode = 'data:image/png;base64,' . base64_encode($imageData);

        return $this->success(['qrcode' => $qrcode, 'shareTimes' => $shareTimes]);
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

    public function createTempNote()
    {
        /** @var TempTourismNoteInput $input */
        $input = TempTourismNoteInput::new();

        $note = TourismNoteService::getInstance()->getNoteByTitle($input->title);
        if (is_null($note)) {
            DB::transaction(function () use ($input) {
                $note = TourismNoteService::getInstance()->createTempNote($input->userId, $input);
                MediaProductService::getInstance()->createMediaProduct(
                    MediaType::NOTE,
                    $note->id,
                    $input->productType,
                    $input->productId,
                );
            });
        }

        return $this->success();
    }
}
