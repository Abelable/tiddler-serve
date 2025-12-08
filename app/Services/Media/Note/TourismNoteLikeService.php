<?php

namespace App\Services\Media\Note;

use App\Models\Media\Note\TourismNoteLike;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class TourismNoteLikeService extends BaseService
{
    public function getLike($userId, $noteId)
    {
        return TourismNoteLike::query()->where('user_id', $userId)->where('note_id', $noteId)->first();
    }

    public function newLike($userId, $noteId)
    {
        $praise = TourismNoteLike::new();
        $praise->user_id = $userId;
        $praise->note_id = $noteId;
        $praise->save();
        return $praise;
    }

    public function pageList($userId, PageInput $input, $curNoteId = 0, $columns = ['*'])
    {
        $query = TourismNoteLike::query()->where('user_id', $userId);
        if ($curNoteId != 0) {
            $query = $query->orderByRaw("CASE WHEN note_id = " . $curNoteId . " THEN 0 ELSE 1 END");
        }
        return $query->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function likeUserIdsGroup($noteIds)
    {
        return TourismNoteLike::query()
            ->whereIn('note_id', $noteIds)
            ->get()
            ->groupBy('note_id')
            ->map(function ($fan) {
                return $fan->pluck('user_id')->toArray();
            });
    }

    public function deleteList($noteId)
    {
        return TourismNoteLike::query()->where('note_id', $noteId)->delete();
    }
}
