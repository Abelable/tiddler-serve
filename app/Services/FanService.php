<?php

namespace App\Services;

use App\Models\Fan;

class FanService extends BaseService
{
    public function authorList($fanId, $columns = ['*'])
    {
        return Fan::query()->where('fan_id', $fanId)->get($columns);
    }

    public function authorIds($fanId)
    {
        $list = $this->authorList($fanId);
        return $list->pluck('author_id')->toArray();
    }

    public function fanIdsGroup($authorIds)
    {
        return Fan::query()
            ->whereIn('author_id', $authorIds)
            ->select(['author_id', 'fan_id'])
            ->get()
            ->groupBy('author_id')
            ->map(function ($fan) {
                return $fan->pluck('fan_id')->toArray();
            });
    }
}
