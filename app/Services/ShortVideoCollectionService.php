<?php

namespace App\Services;

use App\Models\ShortVideoCollection;

class ShortVideoCollectionService extends BaseService
{
     public function getCollection($userId, $videoId)
     {
         return ShortVideoCollection::query()->where('video_id', $videoId)->where('user_id', $userId)->first();
     }

     public function newCollection($userId, $videoId)
     {
        $collection = ShortVideoCollection::new();
        $collection->user_id = $userId;
        $collection->video_id = $videoId;
        $collection->save();
        return $collection;
     }
}
