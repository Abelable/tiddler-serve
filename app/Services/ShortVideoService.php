<?php

namespace App\Services;

use App\Models\ShortVideo;
use App\Utils\Inputs\ShortVideoInput;

class ShortVideoService extends BaseService
{
    public function getListByIds($ids, $columns = ['*'])
    {
        return ShortVideo::query()->whereIn('id', $ids)->get($columns);
    }

    public function newVideo($userId, ShortVideoInput $input)
    {
        $video = ShortVideo::new();
        $video->user_id = $userId;
        $video->title = $input->title;
        if (!empty($input->cover)) {
            $video->cover = $input->cover;
        }
        $video->video_url = $input->videoUrl;
        $video->save();
        return $video;
    }

    public function getVideo($id, $columns = ['*'])
    {
        return ShortVideo::query()->find($id, $columns);
    }
}
