<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GiftGoodsService;
use App\Services\MallBannerService;
use App\Services\MallService;
use App\Services\ShopService;
use App\Utils\Inputs\PageInput;
use Illuminate\Support\Facades\DB;

class MallController extends Controller
{
    protected $except = ['bannerList', 'list'];

    public function bannerList()
    {
        $list = MallBannerService::getInstance()->getBannerList();
        return $this->success($list);
    }

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
            'sales_volume',
            'longitude',
            'latitude',
            'address',
            'feature_tag_list',
            DB::raw('NULL as facility_list'),
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
            'sales_volume',
            'longitude',
            'latitude',
            'address',
            'feature_tag_list',
            DB::raw('NULL as facility_list'),
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
            'sales_volume',
            'longitude',
            'latitude',
            'address',
            DB::raw('NULL as feature_tag_list'),
            'facility_list',
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
            'sales_volume',
            DB::raw('NULL as longitude'),
            DB::raw('NULL as latitude'),
            DB::raw('NULL as address'),
            DB::raw('NULL as feature_tag_list'),
            DB::raw('NULL as facility_list'),
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
}
