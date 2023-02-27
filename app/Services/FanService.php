<?php

namespace App\Services;

use App\Models\Fan;

class FanService extends BaseService
{
    public function authorList($fanId, $columns = ['*'])
    {
        return Fan::query()->where('fan_id', $fanId)->get($columns);
    }
}
