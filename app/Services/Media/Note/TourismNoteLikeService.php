<?php

namespace App\Services\Media\Note;

use App\Models\TourismNoteLike;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class TourismNoteLikeService extends BaseService
{
    public function getPraise($userId, $noteId)
    {
        return TourismNoteLike::query()->where('user_id', $userId)->where('note_id', $noteId)->first();
    }

    public function newPraise($userId, $noteId)
    {
        $praise = TourismNoteLike::new();
        $praise->user_id = $userId;
        $praise->note_id = $noteId;
        $praise->save();
        return $praise;
    }

    public function pageList($userId, PageInput $input, $columns = ['*'])
    {
        return TourismNoteLike::query()
            ->where('user_id', $userId)
            ->orderBy($input->sort, $input->order)
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
}
