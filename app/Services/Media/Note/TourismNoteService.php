<?php

namespace App\Services\Media\Note;

use App\Models\TourismNote;
use App\Services\BaseService;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\Inputs\TourismNoteInput;
use App\Utils\Inputs\TourismNotePageInput;
use function PHPUnit\Framework\isEmpty;

class TourismNoteService extends BaseService
{
    public function pageList(TourismNotePageInput $input, $columns = ['*'])
    {
        $query = TourismNote::query()->where('is_private', 0);
        if ($input->authorId != 0) {
            $query = $query->where('user_id', $input->authorId);
        }
        if ($input->id != 0) {
            $query = $query->orderByRaw("CASE WHEN id = " . $input->id . " THEN 0 ELSE 1 END");
        }
        if ($input->withComments == 1) {
            $query = $query->with(['commentList' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(8)->with('userInfo');
            }]);
        }
        return $query
            ->orderBy('like_number', 'desc')
            ->orderBy('comments_number', 'desc')
            ->orderBy('collection_times', 'desc')
            ->orderBy('share_times', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function search(SearchPageInput $input)
    {
        return TourismNote::search($input->keywords)
            ->where('is_private', 0)
            ->orderBy('like_number', 'desc')
            ->orderBy('comments_number', 'desc')
            ->orderBy('collection_times', 'desc')
            ->orderBy('share_times', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit,'page', $input->page);
    }

    public function userPageList($userId, TourismNotePageInput $input, $columns = ['*'])
    {
        $query = TourismNote::query()->where('user_id', $userId);
        if (!isEmpty($input->id)) {
            $query = $query->orderByRaw("CASE WHEN id = " . $input->id . " THEN 0 ELSE 1 END");
        }
        if ($input->withComments) {
            $query = $query->with(['commentList' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(8)->with('userInfo');
            }]);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListByIds($ids, $columns = ['*'])
    {
        return TourismNote::query()
            ->whereIn('id', $ids)
            ->with(['commentList' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(8)->with('userInfo');
            }])
            ->with('authorInfo')
            ->get($columns);
    }

    public function newNote($userId, TourismNoteInput $input)
    {
        $note = TourismNote::new();
        $note->user_id = $userId;
        $note->image_list = json_encode($input->imageList);
        $note->title = $input->title;
        $note->content = $input->content;
        if (!empty($input->address)) {
            $note->longitude = $input->longitude;
            $note->latitude = $input->latitude;
            $note->address = $input->address;
        }
        if (!empty($input->isPrivate)) {
            $note->is_private = $input->isPrivate;
        }
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

    public function getNoteByTitle($title, $columns = ['*'])
    {
        return TourismNote::query()->where('title', $title)->first($columns);
    }

    public function getList()
    {
        return TourismNote::query()->get();
    }
}
