<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\TourismNote;
use App\Models\User;
use App\Services\Media\MediaService;
use App\Services\Media\Note\TourismNoteGoodsService;
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

        $page = TourismNoteService::getInstance()->pageList($id, $input);
        $noteList = collect($page->items());

        $authorIds = $noteList->pluck('user_id')->toArray();
        $authorList = UserService::getInstance()->getListWithFanList($authorIds)->keyBy('id');

        $list = $noteList->map(function (TourismNote $note) use ($authorList) {
            /** @var User $author */
            $author = $authorList->get($note->user_id);
            $note['author_info'] = [
                'id' => $author->id,
                'avatar' => $author->avatar,
                'nickname' => $author->nickname,
            ];
            unset($note->user_id);

            $note['is_follow'] = 0;
            if ($this->isLogin()) {
                $fansIds = $author['fanList']->pluck('fan_id')->toArray();
                if (in_array($this->userId(), $fansIds)) {
                    $note['is_follow'] = 1;
                }
            }

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

            MediaService::getInstance()->newMedia($this->userId(), $note->id, MediaTypeEnums::NOTE);
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

            $media = MediaService::getInstance()->getMedia($note->id, MediaTypeEnums::NOTE);
            $media->delete();
        });

        return $this->success();
    }

    public function toggleCollectionStatus()
    {
        $id = $this->verifyRequiredId('id');

        $note = TourismNoteService::getInstance()->getUserNote($this->userId(), $id);
        if (is_null($note)) {
            return $this->fail(CodeResponse::NOT_FOUND, '旅游攻略不存在');
        }


    }
}
