<?php

namespace App\Services\Media\Note;

use App\Models\TourismNote;
use App\Services\BaseService;
use App\Utils\Inputs\TourismNoteInput;

class TourismNoteService extends BaseService
{
    public function pageList($input, $columns = ['*'], $authorIds = null, $curNoteId = 0, $withComments = false)
    {
        $query = TourismNote::query();
        if (!is_null($authorIds)) {
            $query = $query->whereIn('user_id', $authorIds);
        }
        if ($curNoteId != 0) {
            $query = $query->orderByRaw("CASE WHEN id = " . $curNoteId . " THEN 0 ELSE 1 END");
        }
        if ($withComments) {
            $query = $query->with(['commentList' => function ($query) {
                $query->orderBy('create_at', 'desc')->take(2)->with('userInfo');
            }]);
        }
        return $query
            ->with('authorInfo')
            ->orderBy('praise_number', 'desc')
            ->orderBy('comments_number', 'desc')
            ->orderBy('collection_times', 'desc')
            ->orderBy('share_times', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListByIds($ids, $columns = ['*'])
    {
        return TourismNote::query()->whereIn('id', $ids)->get($columns);
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
