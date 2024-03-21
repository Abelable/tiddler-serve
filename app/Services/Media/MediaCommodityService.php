<?php

namespace App\Services\Media;

use App\Models\MediaCommodity;
use App\Services\BaseService;
use App\Services\GoodsService;
use App\Services\HotelService;
use App\Services\RestaurantService;
use App\Services\ScenicService;

class MediaCommodityService extends BaseService
{
    public function createMediaCommodity($mediaType, $mediaId, $scenicId, $hotelId, $restaurantId, $goodsId)
    {
        $mediaCommodity = MediaCommodity::new();
        $mediaCommodity->media_type = $mediaType;
        $mediaCommodity->media_id = $mediaId;
        if (!empty($scenicId)) {
            $mediaCommodity->scenic_id = $scenicId;
        }
        if (!empty($hotelId)) {
            $mediaCommodity->hotel_id = $hotelId;
        }
        if (!empty($restaurantId)) {
            $mediaCommodity->restaurant_id = $restaurantId;
        }
        if (!empty($goodsId)) {
            $mediaCommodity->goods_id = $goodsId;
        }
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
        $list = MediaCommodity::query()->where('media_type', $mediaType)->whereIn('id' ,$mediaIds)->get($columns);

        $scenicIds = $list->pluck('scenic_id')->filter(function ($scenicId) {
            return $scenicId != 0;
        })->toArray();
        $hotelIds = $list->pluck('hotel_id')->filter(function ($hotelId) {
            return $hotelId != 0;
        })->toArray();
        $restaurantIds = $list->pluck('restaurant_id')->filter(function ($restaurantId) {
            return $restaurantId != 0;
        })->toArray();
        $goodsIds = $list->pluck('goods_id')->filter(function ($goodsId) {
            return $goodsId != 0;
        })->toArray();

        $mediaCommodityList = $list->keyBy('media_id');
        $scenicList = ScenicService::getInstance()->getScenicListByIds($scenicIds, $scenicColumns)->keyBy('id');
        $hotelList = HotelService::getInstance()->getHotelListByIds($hotelIds, $hotelColumns)->keyBy('id');
        $restaurantList = RestaurantService::getInstance()->getRestaurantListByIds($restaurantIds, $restaurantColumns)->keyBy('id');
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, $goodsColumns)->keyBy('id');

        return [$mediaCommodityList, $scenicList, $hotelList, $restaurantList, $goodsList];
    }
}
