<?php

namespace App\Services\Media\ShortVideo;

use App\Models\ShortVideo;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\ShortVideoInput;

class ShortVideoService extends BaseService
{
    public function pageList(PageInput $input, $columns = ['*'], $authorIds = null, $curVideoId = 0)
    {
        $query = $this->videoQuery($columns, $authorIds, $curVideoId);
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }

    public function videoQuery($columns = ['*'], $authorIds = null, $curVideoId = 0)
    {
        $query = ShortVideo::query()->select($columns);
        if (!is_null($authorIds)) {
            $query = $query->whereIn('user_id', $authorIds);
        }
        if ($curVideoId != 0) {
            $query = $query->orderByRaw("CASE WHEN id = " . $curVideoId . " THEN 0 ELSE 1 END");
        }
        return $query
            ->with('authorInfo')
            ->orderBy('like_number', 'desc')
            ->orderBy('comments_number', 'desc')
            ->orderBy('collection_times', 'desc')
            ->orderBy('share_times', 'desc');
    }

    public function getListByIds($ids, $columns = ['*'])
    {
        return ShortVideo::query()->whereIn('id', $ids)->with('authorInfo')->get($columns);
    }

    public function newVideo($userId, ShortVideoInput $input)
    {
        $video = ShortVideo::new();
        $video->user_id = $userId;
        $video->title = $input->title;
        $video->cover = $input->cover;
        $video->video_url = $input->videoUrl;
        if (!empty($input->goodsId)) {
            $video->goods_id = $input->goodsId;
        }
        if (!empty($input->address)) {
            $video->longitude = $input->longitude;
            $video->latitude = $input->latitude;
            $video->address = $input->address;
        }
        if (!empty($input->isPrivate)) {
            $video->is_private = $input->isPrivate;
        }
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
