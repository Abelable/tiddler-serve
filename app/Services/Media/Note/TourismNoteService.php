<?php

namespace App\Services\Media\Note;

use App\Models\TourismNote;
use App\Services\BaseService;
use App\Utils\Inputs\TourismNoteInput;

class TourismNoteService extends BaseService
{
    public function getListByIds($ids, $columns = ['*'])
    {
        return TourismNote::query()->whereIn('id', $ids)->get($columns);
    }

    public function pageList($curNoteId, $input, $columns = ['*'])
    {
        return TourismNote::query()
            ->orderByRaw("CASE WHEN id = " . $curNoteId . " THEN 0 ELSE 1 END")
            ->with(['commentList' => function ($query) {
                $query->orderBy('create_at', 'desc')->take(2)->with('userInfo');
            }])
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function newNote($userId, TourismNoteInput $input)
    {
        $note = TourismNote::new();
        $note->user_id = $userId;
        $note->image_list = $input->imageList;
        $note->title = $input->title;
        $note->content = $input->content;
        $note->save();
        return $note;
    }

    public function getNote($id, $columns = ['*'])
    {
        return TourismNote::query()->find($id, $columns);
    }

    public function getUserNote($userId, $id, $columns = ['*'])
    {
        return TourismNote::query()->where('user_id', $userId)->find($id, $columns);
    }
}
