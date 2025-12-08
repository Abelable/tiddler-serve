<?php

namespace App\Services\Theme;

use App\Models\Theme\LakeTrip;
use App\Services\BaseService;
use App\Utils\Inputs\Admin\LakeTripInput;
use App\Utils\Inputs\PageInput;

class LakeTripService extends BaseService
{
    public function createLakeTrip(LakeTripInput $input)
    {
        $lakeTrip = LakeTrip::new();
        return $this->updateLakeTrip($lakeTrip, $input);
    }

    public function updateLakeTrip(LakeTrip $lakeTrip, LakeTripInput $input)
    {
        $lakeTrip->lake_id = $input->lakeId;
        $lakeTrip->scenic_id = $input->scenicId;
        $lakeTrip->scenic_cover = $input->scenicCover;
        $lakeTrip->scenic_name = $input->scenicName;
        $lakeTrip->desc = $input->desc;
        $lakeTrip->distance = $input->distance;
        $lakeTrip->duration = $input->duration;
        $lakeTrip->time = $input->time;
        $lakeTrip->save();

        return $lakeTrip;
    }

    public function getLakeTripPage(PageInput $input, $columns = ['*'])
    {
        return LakeTrip::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getLakeTrip($id, $columns = ['*'])
    {
        return LakeTrip::query()->find($id, $columns);
    }

    public function getLakeTripList($lakeId, $columns = ['*'])
    {
        return LakeTrip::query()
            ->where('lake_id', $lakeId)
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }
}
