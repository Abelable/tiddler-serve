<?php

namespace App\Services\Media\ShortVideo;

use App\Models\ShortVideo;
use App\Services\BaseService;
use App\Utils\Inputs\ShortVideoInput;

class ShortVideoService extends BaseService
{
    public function pageList($input, $columns = ['*'], $authorIds = null, $curVideoId = 0)
    {
        $query = ShortVideo::query();
        if (!is_null($authorIds)) {
            $query = $query->whereIn('user_id', $authorIds);
        }
        if ($curVideoId != 0) {
            $query = $query->orderByRaw("CASE WHEN id = " . $curVideoId . " THEN 0 ELSE 1 END");
        }
        return $query
            ->with('authorInfo')
            ->orderBy('praise_number', 'desc')
            ->orderBy('comments_number', 'desc')
            ->orderBy('collection_times', 'desc')
            ->orderBy('share_times', 'desc')
            ->orderBy($input['sort'], $input['order'])
            ->paginate($input['limit'], $columns, 'page', $input['page']);
    }

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

    public function getUserVideo($userId, $id, $columns = ['*'])
    {
        return ShortVideo::query()->where('user_id', $userId)->find($id, $columns);
    }
}
