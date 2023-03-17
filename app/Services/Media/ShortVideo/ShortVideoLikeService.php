<?php

namespace App\Services\Media\ShortVideo;

use App\Models\ShortVideoLike;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class ShortVideoLikeService extends BaseService
{
    public function getPraise($userId, $videoId)
    {
        return ShortVideoLike::query()->where('user_id', $userId)->where('video_id', $videoId)->first();
    }

    public function newPraise($userId, $videoId)
    {
        $praise = ShortVideoLike::new();
        $praise->user_id = $userId;
        $praise->video_id = $videoId;
        $praise->save();
        return $praise;
    }

    public function pageList($userId, PageInput $input, $columns = ['*'])
    {
        return ShortVideoLike::query()
            ->where('user_id', $userId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }
}
