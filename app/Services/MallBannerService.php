<?php

namespace App\Services;

use App\Models\MallBanner;
use App\Utils\Inputs\BannerInput;
use App\Utils\Inputs\BannerPageInput;

class MallBannerService extends BaseService
{
    public function updateBanner(MallBanner $banner, BannerInput $input)
    {
        $banner->cover = $input->cover;
        if (!is_null($input->desc)) {
            $banner->desc = $input->desc;
        }
        $banner->scene = $input->scene;
        $banner->value = $input->value;
        $banner->save();
        return $banner;
    }

    public function getBannerPage(BannerPageInput $input, $columns = ['*'])
    {
        $query = MallBanner::query();
        if (!is_null($input->scene)) {
            $query->where('scene', $input->scene);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }
}
