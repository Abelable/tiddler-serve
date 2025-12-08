<?php

namespace App\Services\Media\Note;

use App\Models\Media\Note\TourismNoteCollection;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class TourismNoteCollectionService extends BaseService
{
    public function getCollection($userId, $noteId)
    {
        return TourismNoteCollection::query()->where('note_id', $noteId)->where('user_id', $userId)->first();
    }

    public function newCollection($userId, $noteId)
    {
        $collection = TourismNoteCollection::new();
        $collection->user_id = $userId;
        $collection->note_id = $noteId;
        $collection->save();
        return $collection;
    }

    public function pageList($userId, PageInput $input, $curNoteId = 0, $columns = ['*'])
    {
        $query = TourismNoteCollection::query()->where('user_id', $userId);
        if ($curNoteId != 0) {
            $query = $query->orderByRaw("CASE WHEN note_id = " . $curNoteId . " THEN 0 ELSE 1 END");
        }
        return $query->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function collectedUserIdsGroup($noteIds)
    {
        return TourismNoteCollection::query()
            ->whereIn('note_id', $noteIds)
            ->get()
            ->groupBy('note_id')
            ->map(function ($fan) {
                return $fan->pluck('user_id')->toArray();
            });
    }

    public function deleteList($noteId)
    {
        return TourismNoteCollection::query()->where('note_id', $noteId)->delete();
    }
}
