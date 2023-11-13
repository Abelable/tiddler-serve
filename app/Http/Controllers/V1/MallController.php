<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\MallService;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class MallController extends Controller
{
    protected $except = ['list'];

    private function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $scenicColumns = [
            'id',
            'status',
            'name',
            DB::raw('NULL as cover'),
            DB::raw('NULL as image'),
            'image_list',
            DB::raw('NULL as price'),
            'longitude',
            'latitude',
            'address',
            'created_at',
        ];
        $hotelColumns = [
            'id',
            'status',
            'name',
            'cover',
            DB::raw('NULL as image'),
            DB::raw('NULL as image_list'),
            'price',
            'longitude',
            'latitude',
            'address',
            'created_at',
        ];
        $restaurantColumns = [
            'id',
            DB::raw('NULL as status'),
            'name',
            'cover',
            DB::raw('NULL as image'),
            DB::raw('NULL as image_list'),
            'longitude',
            'latitude',
            'address',
            'created_at',
        ];
        $goodsColumns = [
            'id',
            'status',
            'name',
            DB::raw('NULL as cover'),
            'image',
            'image_list',
            DB::raw('NULL as longitude'),
            DB::raw('NULL as latitude'),
            DB::raw('NULL as address'),
            'created_at',
        ];

        $page = MallService::getInstance()->pageList($input, $scenicColumns, $hotelColumns, $restaurantColumns, $goodsColumns);
        return $this->successPaginate($page);
    }
}
