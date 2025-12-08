<?php

namespace App\Services\Theme;

use App\Models\Theme\LakeCycle;
use App\Services\BaseService;
use App\Utils\Inputs\Admin\LakeCycleInput;
use App\Utils\Inputs\PageInput;

class LakeCycleService extends BaseService
{
    public function createLakeCycle(LakeCycleInput $input)
    {
        $lakeCycle = LakeCycle::new();
        return $this->updateLakeCycle($lakeCycle, $input);
    }

    public function updateLakeCycle(LakeCycle $lakeCycle, LakeCycleInput $input)
    {
        $lakeCycle->route_id = $input->routeId;
        $lakeCycle->scenic_id = $input->scenicId;
        $lakeCycle->scenic_cover = $input->scenicCover;
        $lakeCycle->scenic_name = $input->scenicName;
        $lakeCycle->desc = $input->desc;
        $lakeCycle->distance = $input->distance;
        $lakeCycle->duration = $input->duration;
        $lakeCycle->time = $input->time;
        $lakeCycle->save();

        return $lakeCycle;
    }

    public function getLakeCyclePage(PageInput $input, $columns = ['*'])
    {
        return LakeCycle::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getLakeCycle($id, $columns = ['*'])
    {
        return LakeCycle::query()->find($id, $columns);
    }

    public function getLakeCycleList($routeId, $columns = ['*'])
    {
        return LakeCycle::query()
            ->where('route_id', $routeId)
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }
}
