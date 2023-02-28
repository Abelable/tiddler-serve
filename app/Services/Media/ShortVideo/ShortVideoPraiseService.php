<?php

namespace App\Services\Media\ShortVideo;

use App\Models\ShortVideoPraise;
use App\Services\BaseService;

class ShortVideoPraiseService extends BaseService
{
    public function getPraise($userId, $videoId)
    {
        return ShortVideoPraise::query()->where('user_id', $userId)->where('video_id', $videoId)->first();
    }

    public function newPraise($userId, $videoId)
    {
        $praise = ShortVideoPraise::new();
        $praise->user_id = $userId;
        $praise->video_id = $videoId;
        $praise->save();
        return $praise;
    }
}
