<?php

namespace App\Services;

use App\Models\NightTrip;
use App\Utils\Inputs\Admin\NightTripInput;
use App\Utils\Inputs\PageInput;

class NightTripService extends BaseService
{
    public function createNightTrip(NightTripInput $input)
    {
        $hotScenic = NightTrip::new();
        return $this->updateNightTrip($hotScenic, $input);
    }

    public function updateNightTrip(NightTrip $hotScenic, NightTripInput $input)
    {
        $hotScenic->scenic_id = $input->scenicId;
        $hotScenic->scenic_cover = $input->scenicCover;
        $hotScenic->scenic_name = $input->scenicName;
        $hotScenic->feature_tips = $input->featureTips ?: '';
        $hotScenic->recommend_tips = $input->recommendTips ?: '';
        $hotScenic->guide_tips = $input->guideTips ?: '';
        $hotScenic->save();
        return $hotScenic;
    }

    public function getNightTripPage(PageInput $input, $columns = ['*'])
    {
        return NightTrip::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getNightTrip($id, $columns = ['*'])
    {
        return NightTrip::query()->find($id, $columns);
    }

    public function getNightTripList($columns = ['*'])
    {
        return NightTrip::query()
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }
}
