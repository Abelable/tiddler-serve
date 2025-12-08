<?php

namespace App\Services\Theme;

use App\Models\Theme\StarTrip;
use App\Services\BaseService;
use App\Utils\Inputs\Admin\StarTripInput;
use App\Utils\Inputs\PageInput;

class StarTripService extends BaseService
{
    public function createStarTrip(StarTripInput $input)
    {
        $starTrip = StarTrip::new();
        return $this->updateStarTrip($starTrip, $input);
    }

    public function updateStarTrip(StarTrip $starTrip, StarTripInput $input)
    {
        $starTrip->product_type = $input->productType;
        $starTrip->product_id = $input->productId;
        $starTrip->cover = $input->cover;
        $starTrip->name = $input->name;
        $starTrip->desc = $input->desc;
        $starTrip->save();
        return $starTrip;
    }

    public function getStarTripPage(PageInput $input, $columns = ['*'])
    {
        return StarTrip::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getStarTrip($id, $columns = ['*'])
    {
        return StarTrip::query()->find($id, $columns);
    }

    public function getStarTripList($columns = ['*'])
    {
        return StarTrip::query()
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }
}
