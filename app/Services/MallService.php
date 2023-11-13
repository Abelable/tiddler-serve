<?php

namespace App\Services;

use App\Models\Goods;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\ScenicSpot;
use App\Utils\Inputs\PageInput;

class MallService extends BaseService
{
    public function pageList(PageInput $input, $scenicColumns = ['*'], $hotelColumns = ['*'], $restaurantColumns = ['*'], $goodsColumns = ['*'])
    {
        $scenicQuery = ScenicSpot::query()->select($scenicColumns)->where('status', 1)->selectRaw("1 as type");
        $hotelQuery = Hotel::query()->select($hotelColumns)->where('status', 1)->selectRaw("2 as type");
        $restaurantQuery = Restaurant::query()->select($restaurantColumns)->selectRaw("3 as type");
        $goodsQuery = Goods::query()->select($goodsColumns)->where('status', 1)->selectRaw("4 as type");
        $mallQuery = $scenicQuery->union($hotelQuery)->union($restaurantQuery)->union($goodsQuery);
        return $mallQuery
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }
}
