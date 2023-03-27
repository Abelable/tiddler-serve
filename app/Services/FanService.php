<?php

namespace App\Services;

use App\Models\Fan;

class FanService extends BaseService
{
    public function newFan($authorId, $fanId)
    {
        $fan = Fan::new();
        $fan->author_id = $authorId;
        $fan->fan_id = $fanId;
        $fan->save();
        return $fan;
    }

    public function authorList($fanId, $columns = ['*'])
    {
        return Fan::query()->where('fan_id', $fanId)->get($columns);
    }

    public function authorIds($fanId)
    {
        $list = $this->authorList($fanId);
        return $list->pluck('author_id')->toArray();
    }

    public function fanList($authorId, $columns = ['*'])
    {
        return Fan::query()->where('author_id', $authorId)->get($columns);
    }

    public function fanIds($authorId)
    {
        $list = $this->fanList($authorId);
        return $list->pluck('fan_id')->toArray();
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

    public function followedAuthorNumber($userId)
    {
        return Fan::query()->where('fan_id', $userId)->count();
    }

    public function fansNumber($authorId)
    {
        return Fan::query()->where('author_id', $authorId)->count();
    }
}
