<?php

namespace App\Services\Media;

use App\Models\MediaCommodity;
use App\Services\BaseService;
use App\Services\GoodsService;
use App\Services\HotelService;
use App\Services\RestaurantService;
use App\Services\ScenicService;
use App\Utils\Enums\CommodityType;

class MediaCommodityService extends BaseService
{
    public function createMediaCommodity($mediaType, $mediaId, $commodityType, $commodityId)
    {
        $mediaCommodity = MediaCommodity::new();
        $mediaCommodity->media_type = $mediaType;
        $mediaCommodity->media_id = $mediaId;
        $mediaCommodity->commodity_type = $commodityType;
        $mediaCommodity->commodity_id = $commodityId;
        $mediaCommodity->save();
        return $mediaCommodity;
    }

    public function getListByMediaIds(
        $mediaType,
        array $mediaIds,
        $scenicColumns = ['*'],
        $hotelColumns = ['*'],
        $restaurantColumns = ['*'],
        $goodsColumns = ['*'],
        $columns = ['*']
    )
    {
        $list = MediaCommodity::query()->where('media_type', $mediaType)->whereIn('media_id', $mediaIds)->get($columns);

        $scenicIds = $list->pluck('commodity_id')->filter(function ($commodityType) {
            return $commodityType == CommodityType::SCENIC;
        })->toArray();
        $scenicList = ScenicService::getInstance()->getScenicListByIds($scenicIds, $scenicColumns)->keyBy('id');

        $hotelIds = $list->pluck('commodity_id')->filter(function ($commodityType) {
            return $commodityType == CommodityType::HOTEL;
        })->toArray();
        $hotelList = HotelService::getInstance()->getHotelListByIds($hotelIds, $hotelColumns)->keyBy('id');

        $restaurantIds = $list->pluck('commodity_id')->filter(function ($commodityType) {
            return $commodityType == CommodityType::RESTAURANT;
        })->toArray();
        $restaurantList = RestaurantService::getInstance()->getRestaurantListByIds($restaurantIds, $restaurantColumns)->keyBy('id');

        $goodsIds = $list->pluck('commodity_id')->filter(function ($commodityType) {
            return $commodityType == CommodityType::GOODS;
        })->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, $goodsColumns)->keyBy('id');

        return [$list, $scenicList, $hotelList, $restaurantList, $goodsList];
    }
}
