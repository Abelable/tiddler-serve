<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\TourismNote;
use App\Models\User;
use App\Services\FanService;
use App\Services\Media\MediaService;
use App\Services\Media\Note\TourismNoteCollectionService;
use App\Services\Media\Note\TourismNoteGoodsService;
use App\Services\Media\Note\TourismNotePraiseService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\UserService;
use App\Utils\CodeResponse;
use App\Utils\Enums\MediaTypeEnums;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\TourismNoteInput;
use Illuminate\Support\Facades\DB;

class TourismNoteController extends Controller
{
    protected $except = ['list'];

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyRequiredId('id');

        $columns = ['id', 'user_id', 'image_list', 'title', 'content', 'praise_number', 'comments_number', 'collection_times', 'share_times', 'created_at'];
        $page = TourismNoteService::getInstance()->pageList($input, $columns, null, $id, true);
        $noteList = collect($page->items());

        $authorIds = $noteList->pluck('user_id')->toArray();
        $fansGroup = FanService::getInstance()->fansGroup($authorIds);

        $list = $noteList->map(function (TourismNote $note) use ($fansGroup) {
            $note['is_follow'] = 0;
            if ($this->isLogin()) {
                $fansIds = collect($fansGroup->get($note->user_id))->pluck('fan_id')->toArray();
                if (in_array($this->userId(), $fansIds)) {
                    $note['is_follow'] = 1;
                }
            }
            unset($note->user_id);

            $note['commentList'] = $note['commentList']->map(function ($comment) {
                return [
                    'nickname' => $comment['userInfo']->nickname,
                    'content' => $comment['content']
                ];
            });

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

            if (!empty($input->goodsId)) {
                TourismNoteGoodsService::getInstance()->newGoods($note->id, $input->goodsId);
            }
        });

        return $this->success();
    }

    public function deleteNote()
    {
        $id = $this->verifyRequiredId('id');

        $note = TourismNoteService::getInstance()->getUserNote($this->userId(), $id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '旅游攻略不存在');
        }

        DB::transaction(function () use ($note) {
            $note->delete();

            TourismNoteGoodsService::getInstance()->deleteList($note->id);
        });

        return $this->success();
    }

    public function togglePraiseStatus()
    {
        $id = $this->verifyRequiredId('id');

        /** @var TourismNote $note */
        $note = TourismNoteService::getInstance()->getNote($id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '旅游攻略不存在');
        }

        $praiseNumber = DB::transaction(function () use ($note, $id) {
            $praise = TourismNotePraiseService::getInstance()->getPraise($this->userId(), $id);
            if (!is_null($praise)) {
                $praise->delete();
                $praiseNumber = max($note->praise_number - 1, 0);
            } else {
                TourismNotePraiseService::getInstance()->newPraise($this->userId(), $id);
                $praiseNumber = $note->praise_number + 1;
            }
            $note->praise_number = $praiseNumber;
            $note->save();

            return $praiseNumber;
        });

        return $this->success($praiseNumber);
    }

    public function toggleCollectionStatus()
    {
        $id = $this->verifyRequiredId('id');

        /** @var TourismNote $note */
        $note = TourismNoteService::getInstance()->getNote($id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '旅游攻略不存在');
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
}
