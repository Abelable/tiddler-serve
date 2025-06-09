<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\ScenicSpot;
use App\Services\GiftGoodsService;
use App\Services\GoodsService;
use App\Services\HotelService;
use App\Services\MallService;
use App\Services\RestaurantService;
use App\Services\ScenicService;
use App\Services\ShopService;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class MallController extends Controller
{
    protected $only = [];

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        $scenicColumns = [
            'id',
            DB::raw('NULL as shop_id'),
            DB::raw('NULL as status'),
            'name',
            DB::raw('NULL as cover'),
            'image_list',
            'price',
            DB::raw('NULL as market_price'),
            'longitude',
            'latitude',
            'address',
            'feature_tag_list',
            DB::raw('NULL as facility_list'),
            'sales_volume',
            'views',
            'created_at',
        ];
        $hotelColumns = [
            'id',
            DB::raw('NULL as shop_id'),
            DB::raw('NULL as status'),
            'name',
            'cover',
            DB::raw('NULL as image_list'),
            'price',
            DB::raw('NULL as market_price'),
            'longitude',
            'latitude',
            'address',
            'feature_tag_list',
            DB::raw('NULL as facility_list'),
            'sales_volume',
            'views',
            'created_at',
        ];
        $restaurantColumns = [
            'id',
            DB::raw('NULL as shop_id'),
            DB::raw('NULL as status'),
            'name',
            'cover',
            DB::raw('NULL as image_list'),
            'price',
            DB::raw('NULL as market_price'),
            'longitude',
            'latitude',
            'address',
            DB::raw('NULL as feature_tag_list'),
            'facility_list',
            'sales_volume',
            'views',
            'created_at',
        ];
        $goodsColumns = [
            'id',
            'shop_id',
            'status',
            'name',
            'cover',
            'image_list',
            'price',
            'market_price',
            DB::raw('NULL as longitude'),
            DB::raw('NULL as latitude'),
            DB::raw('NULL as address'),
            DB::raw('NULL as feature_tag_list'),
            DB::raw('NULL as facility_list'),
            'sales_volume',
            'views',
            'created_at',
        ];

        $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList()->pluck('goods_id')->toArray();

        $page = MallService::getInstance()->pageList($input, $scenicColumns, $hotelColumns, $restaurantColumns, $goodsColumns);
        $list = collect($page->items())->map(function ($product) use ($giftGoodsIds) {
            if ($product['type'] == 1) {
                $product['cover'] = json_decode($product['image_list'])[0];
            }
            if ($product['type'] == 1 || $product['type'] == 2) {
                $product['feature_tag_list'] = json_decode($product['feature_tag_list']);
            }
            if ($product['type'] == 3) {
                $product['facility_list'] = json_decode($product['facility_list']);
            }
            if ($product['type'] == 4) {
                if ($product->shop_id != 0) {
                    $shopInfo = ShopService::getInstance()->getShopById($product->shop_id, ['id', 'type', 'logo', 'name']);
                    $product['shop_info'] = $shopInfo;
                }

                $product['is_gift'] = in_array($product->id, $giftGoodsIds) ? 1 : 0;
            }
            return $product;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function initViews()
    {
        $scenicList = ScenicService::getInstance()->getList();
        $scenicList->map(function (ScenicSpot $scenicSpot) {
            $scenicSpot->views = mt_rand(0, 1000);
            $scenicSpot->save();
        });

        $hotelList = HotelService::getInstance()->getList();
        $hotelList->map(function (Hotel $hotel) {
            $hotel->views = mt_rand(0, 1000);
            $hotel->save();
        });

        $restaurantList = RestaurantService::getInstance()->getList();
        $restaurantList->map(function (Restaurant $restaurant) {
            $restaurant->views = mt_rand(0, 1000);
            $restaurant->save();
        });

        $goodsList = GoodsService::getInstance()->getGoodsList();
        $goodsList->map(function (Goods $goods) {
            $goods->views = mt_rand(0, 1000);
            $goods->save();
        });

        return $this->success();
    }
}
