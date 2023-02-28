<?php

namespace App\Services\Media\ShortVideo;

use App\Models\ShortVideo;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShortVideoInput;

class ShortVideoService extends BaseService
{
    public function pageList($currentVideoId, PageInput $input, $columns = ['*'])
    {
        return ShortVideo::query()
            ->orderByRaw("CASE WHEN id = " . $currentVideoId ." THEN 0 ELSE 1 END")
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
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
}
