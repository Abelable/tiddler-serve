<?php

namespace App\Services;

use App\Models\LakeHomestay;
use App\Utils\Inputs\Admin\LakeHomestayInput;
use App\Utils\Inputs\PageInput;

class LakeHomestayService extends BaseService
{
    public function createLakeHomestay(LakeHomestayInput $input)
    {
        $lakeHomestay = LakeHomestay::new();
        return $this->updateLakeHomestay($lakeHomestay, $input);
    }

    public function updateLakeHomestay(LakeHomestay $lakeHomestay, LakeHomestayInput $input)
    {
        $lakeHomestay->hotel_id = $input->hotelId;
        $lakeHomestay->cover = $input->cover;
        $lakeHomestay->name = $input->name;
        $lakeHomestay->save();

        return $lakeHomestay;
    }

    public function getLakeHomestayPage(PageInput $input, $columns = ['*'])
    {
        return LakeHomestay::query()
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getLakeHomestay($id, $columns = ['*'])
    {
        return LakeHomestay::query()->find($id, $columns);
    }

    public function getLakeHomestayList($columns = ['*'])
    {
        return LakeHomestay::query()
            ->orderBy('sort', 'desc')
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }
}
