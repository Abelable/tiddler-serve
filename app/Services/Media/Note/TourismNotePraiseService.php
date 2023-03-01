<?php

namespace App\Services\Media\Note;

use App\Models\TourismNotePraise;
use App\Services\BaseService;

class TourismNotePraiseService extends BaseService
{
    public function getPraise($userId, $noteId)
    {
        return TourismNotePraise::query()->where('user_id', $userId)->where('note_id', $noteId)->first();
    }

    public function newPraise($userId, $noteId)
    {
        $praise = TourismNotePraise::new();
        $praise->user_id = $userId;
        $praise->note_id = $noteId;
        $praise->save();
        return $praise;
    }
}
