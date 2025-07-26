<?php

namespace App\Services;

use App\Models\HotScenicSpot;
use App\Utils\Inputs\Admin\HotScenicInput;
use App\Utils\Inputs\PageInput;

class HotScenicService extends BaseService
{
    public function createHotScenic(HotScenicInput $input)
    {
        $hotScenic = HotScenicSpot::new();
        $hotScenic->scenic_id = $input->scenicId;
        $hotScenic->scenic_cover = $input->scenicCover;
        $hotScenic->scenic_name = $input->scenicName;

        return $this->updateHotScenic($hotScenic, $input->recommendReason, $input->interestedUserNumber);
    }

    public function updateHotScenic(HotScenicSpot $hotScenic, $recommendReason, $interestedUserNumber)
    {
        $hotScenic->recommend_reason = $recommendReason;
        $hotScenic->interested_user_number = $interestedUserNumber;
        $hotScenic->save();
        return $hotScenic;
    }

    public function getHotScenicPage(PageInput $input, $columns = ['*'])
    {
        return HotScenicSpot::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getHotScenic($id, $columns = ['*'])
    {
        return HotScenicSpot::query()->find($id, $columns);
    }

    public function getHotScenicList($columns = ['*'])
    {
        return HotScenicSpot::query()
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }
}
