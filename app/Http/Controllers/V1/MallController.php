<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Mall\Catering\Restaurant;
use App\Models\Mall\Goods\Goods;
use App\Models\Mall\Hotel\Hotel;
use App\Models\Mall\Scenic\ScenicSpot;
use App\Services\Mall\Catering\RestaurantService;
use App\Services\Mall\Goods\GiftGoodsService;
use App\Services\Mall\Goods\GoodsService;
use App\Services\Mall\Goods\ShopService;
use App\Services\Mall\Hotel\HotelService;
use App\Services\Mall\MallService;
use App\Services\Mall\Scenic\ScenicService;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\NearbyPageInput;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MallController extends Controller
{
    protected $only = [];

    public function list()
    {
        /** @var PageInput $input */
        $input = PageInput::new();

        if ($input->page == 1) {
            $page = Cache::remember('product_list_cache', 1440, function () use ($input) {
                return $this->page($input);
            });
        } else {
            $page = $this->page($input);
        }

        return $this->success($page);
    }

    public function nearbyList()
    {
        /** @var NearbyPageInput $input */
        $input = NearbyPageInput::new();
        $page = $this->page($input, 2, $input->longitude, $input->latitude);
        return $this->success($page);
    }

    private function page(PageInput $input, $scene = 1, $longitude = null, $latitude = null)
    {
        $scenicColumns = [
            'id',
            DB::raw('NULL as status'),
            DB::raw('NULL as shop_id'),
            DB::raw('NULL as category_id'),
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
            DB::raw('NULL as status'),
            DB::raw('NULL as shop_id'),
            'category_id',
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
            DB::raw('NULL as status'),
            DB::raw('NULL as shop_id'),
            DB::raw('NULL as category_id'),
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
            'status',
            'shop_id',
            DB::raw('NULL as category_id'),
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

        $giftGoodsIds = GiftGoodsService::getInstance()->getList()->pluck('goods_id')->toArray();

        if ($scene == 1) {
            $page = MallService::getInstance()
                ->pageList($input, $scenicColumns, $hotelColumns, $restaurantColumns, $goodsColumns);
        } else {
            $page = MallService::getInstance()
                ->nearbyPageList($input, $longitude, $latitude, $scenicColumns, $hotelColumns, $restaurantColumns);
        }

        $list = collect($page->items())->map(function ($product) use ($giftGoodsIds) {
            if ($product['type'] == ProductType::SCENIC) {
                $product['cover'] = json_decode($product['image_list'])[0];
            }
            if ($product['type'] == ProductType::SCENIC || $product['type'] == ProductType::HOTEL) {
                $product['feature_tag_list'] = json_decode($product['feature_tag_list']);
            }
            if ($product['type'] == ProductType::RESTAURANT) {
                $product['facility_list'] = json_decode($product['facility_list']);
            }
            if ($product['type'] == ProductType::GOODS) {
                if ($product->shop_id != 0) {
                    $shopInfo = ShopService::getInstance()->getShopById($product->shop_id, ['id', 'type', 'logo', 'name']);
                    $product['shop_info'] = $shopInfo;
                }

                $product['is_gift'] = in_array($product->id, $giftGoodsIds) ? 1 : 0;
            }
            return $product;
        });

        return $this->paginate($page, $list);
    }

    public function initViews()
    {
        $scenicList = ScenicService::getInstance()->getList();
        $scenicList->map(function (ScenicSpot $scenicSpot) {
            if ($scenicSpot->views == 0) {
                $scenicSpot->views = mt_rand(0, 1000);
                $scenicSpot->save();
            }
        });

        $hotelList = HotelService::getInstance()->getList();
        $hotelList->map(function (Hotel $hotel) {
            if ($hotel->views == 0) {
                $hotel->views = mt_rand(0, 1000);
                $hotel->save();
            }
        });

        $restaurantList = RestaurantService::getInstance()->getList();
        $restaurantList->map(function (Restaurant $restaurant) {
            if ($restaurant->views == 0) {
                $restaurant->views = mt_rand(0, 1000);
                $restaurant->save();
            }
        });

        $goodsList = GoodsService::getInstance()->getGoodsList();
        $goodsList->map(function (Goods $goods) {
            if ($goods->views == 0) {
                $goods->views = mt_rand(0, 1000);
                $goods->save();
            }
        });

        return $this->success();
    }
}
