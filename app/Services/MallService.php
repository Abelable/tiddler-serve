<?php

namespace App\Services;

use App\Models\Catering\Restaurant;
use App\Models\Goods;
use App\Models\Hotel;
use App\Models\ScenicSpot;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class MallService extends BaseService
{
    public function pageList(PageInput $input, $scenicColumns = ['*'], $hotelColumns = ['*'], $restaurantColumns = ['*'], $goodsColumns = ['*'])
    {
        $scenicQuery = ScenicSpot::query()->select($scenicColumns)->selectRaw("1 as type");
        $hotelQuery = Hotel::query()->select($hotelColumns)->selectRaw("2 as type");
        $restaurantQuery = Restaurant::query()->select($restaurantColumns)->selectRaw("3 as type");
        $goodsQuery = Goods::query()->select($goodsColumns)->where('status', 1)->selectRaw("4 as type");
        $mallQuery = $scenicQuery->union($hotelQuery)->union($restaurantQuery)->union($goodsQuery);
        return $mallQuery
            ->orderBy('views', 'desc')
            ->orderBy('sales_volume', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, ['*'], 'page', $input->page);
    }

    public function nearbyPageList(
        PageInput $input,
                  $longitude,
                  $latitude,
                  $scenicColumns = ['*'],
                  $hotelColumns = ['*'],
                  $restaurantColumns = ['*']
    ) {
        // 各类产品查询
        $scenicQuery = ScenicSpot::query()
            ->select(array_merge($scenicColumns, [DB::raw('1 as type')]));

        $hotelQuery = Hotel::query()
            ->select(array_merge($hotelColumns, [DB::raw('2 as type')]));

        $restaurantQuery = Restaurant::query()
            ->select(array_merge($restaurantColumns, [DB::raw('3 as type')]));

        $unionQuery = $scenicQuery
            ->unionAll($hotelQuery)
            ->unionAll($restaurantQuery);

        // Haversine 公式计算距离（单位：公里）
        $haversine = "(6371 * acos(
        cos(radians(?)) * cos(radians(latitude))
        * cos(radians(longitude) - radians(?))
        + sin(radians(?)) * sin(radians(latitude))
    ))";

        $query = DB::query()
            ->fromSub($unionQuery, 'products')
            ->select('products.*', DB::raw("$haversine as distance"))
            ->addBinding([$latitude, $longitude, $latitude], 'select')
            ->orderBy('distance', 'asc')
            ->orderBy('views', 'desc')
            ->orderBy('sales_volume', 'desc')
            ->orderBy($input->sort ?: 'created_at', $input->order ?: 'desc');

        $paginator = $query->paginate(
            $input->limit,
            ['*'],
            'page',
            $input->page
        );

        $mapped = collect($paginator->items())->map(function ($item) {
            $arr = (array)$item;
            $type = intval($arr['type'] ?? 0);

            switch ($type) {
                case 1:
                    return (new ScenicSpot)->newFromBuilder($arr);
                case 2:
                    return (new Hotel)->newFromBuilder($arr);
                case 3:
                    return (new Restaurant)->newFromBuilder($arr);
                default:
                    return (object)$arr;
            }
        });


        $paginator->setCollection($mapped);

        return $paginator;
    }
}
