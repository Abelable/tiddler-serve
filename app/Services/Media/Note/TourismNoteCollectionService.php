<?php

namespace App\Services\Media\Note;

use App\Models\TourismNoteCollection;
use App\Services\BaseService;

class TourismNoteCollectionService extends BaseService
{
     public function getCollection($userId, $noteId)
     {
         return TourismNoteCollection::query()->where('video_id', $noteId)->where('user_id', $userId)->first();
     }

     public function newCollection($userId, $noteId)
     {
        $collection = TourismNoteCollection::new();
        $collection->user_id = $userId;
        $collection->note_id = $noteId;
        $collection->save();
        return $collection;
     }
}
