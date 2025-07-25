<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\HotScenicSpot;

class HotScenicService extends BaseService
{
    public function createHotScenic($scenicId, $scenicCover, $scenicName, $recommendReason, $interestedUserNumber)
    {
        $hotScenic = HotScenicSpot::new();
        $hotScenic->scenic_id = $scenicId;
        $hotScenic->scenic_cover = $scenicCover;
        $hotScenic->scenic_name = $scenicName;

        return $this->updateHotScenic($hotScenic, $recommendReason, $interestedUserNumber);
    }

    public function updateHotScenic(HotScenicSpot $hotScenic, $recommendReason, $interestedUserNumber)
    {
        $hotScenic->recommend_reason = $recommendReason;
        $hotScenic->interested_user_number = $interestedUserNumber;
        $hotScenic->save();
        return $hotScenic;
    }

    public function getHotScenic($id, $columns = ['*'])
    {
        return HotScenicSpot::query()->find($id, $columns);
    }

    public function getHotScenicList($columns = ['*'])
    {
        return Banner::query()
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }
}
