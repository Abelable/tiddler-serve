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

    }
}
