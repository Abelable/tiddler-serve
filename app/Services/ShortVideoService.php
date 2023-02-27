<?php

namespace App\Services;

use App\Models\ShortVideo;

class ShortVideoService extends BaseService
{
    public function getListByIds($ids, $columns = ['*'])
    {
        return ShortVideo::query()->whereIn('id', $ids)->get($columns);
    }
}
