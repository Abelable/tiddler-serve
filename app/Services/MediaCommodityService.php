<?php

namespace App\Services;

use App\Models\MediaCommodity;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\CommodityMediaPageInput;

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

    public function getListByProductIds($productType, array $productIds, $columns = ['*'])
    {
        return MediaCommodity::query()
            ->where('commodity_type', $productType)
            ->whereIn('commodity_id', $productIds)
            ->get($columns);
    }

    public function getListByMediaIds($mediaType, array $mediaIds, $columns = ['*'])
    {
        return MediaCommodity::query()
            ->where('media_type', $mediaType)
            ->whereIn('media_id', $mediaIds)
            ->get($columns);
    }

    public function getFilterListByMediaIds(
        $mediaType,
        array $mediaIds,
        $scenicColumns = ['*'],
        $hotelColumns = ['*'],
        $restaurantColumns = ['*'],
        $goodsColumns = ['*'],
        $columns = ['*']
    ): array
    {
        $list = $this->getListByMediaIds($mediaType, $mediaIds, $columns);

        $scenicIds = $list->filter(function (MediaCommodity $mediaCommodity) {
            return $mediaCommodity->commodity_type == ProductType::SCENIC;
        })->pluck('commodity_id')->toArray();
        $scenicList = ScenicService::getInstance()->getScenicListByIds($scenicIds, $scenicColumns)->keyBy('id');

        $hotelIds = $list->filter(function (MediaCommodity $mediaCommodity) {
            return $mediaCommodity->commodity_type == ProductType::HOTEL;
        })->pluck('commodity_id')->toArray();
        $hotelList = HotelService::getInstance()->getHotelListByIds($hotelIds, $hotelColumns)->keyBy('id');

        $restaurantIds = $list->filter(function (MediaCommodity $mediaCommodity) {
            return $mediaCommodity->commodity_type == ProductType::RESTAURANT;
        })->pluck('commodity_id')->toArray();
        $restaurantList = RestaurantService::getInstance()->getRestaurantListByIds($restaurantIds, $restaurantColumns)->keyBy('id');

        $goodsIds = $list->filter(function (MediaCommodity $mediaCommodity) {
            return $mediaCommodity->commodity_type == ProductType::GOODS;
        })->pluck('commodity_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, $goodsColumns)->keyBy('id');

        return [
            'mediaList'     => $list,
            'scenicIds'     => $scenicIds,
            'scenicList'    => $scenicList,
            'hotelIds'      => $hotelIds,
            'hotelList'     => $hotelList,
            'restaurantIds' => $restaurantIds,
            'restaurantList'=> $restaurantList,
            'goodsIds'      => $goodsIds,
            'goodsList'     => $goodsList,
        ];
    }

    public function deleteList($mediaType, $mediaId)
    {
        return MediaCommodity::query()
            ->where('media_type', $mediaType)
            ->where('media_id', $mediaId)
            ->delete();
    }

    public function deleteMediaProduct($mediaType, $mediaId)
    {
        MediaCommodity::query()
            ->where('media_type', $mediaType)
            ->whereIn('media_id', $mediaId)
            ->delete();
    }

    public function getMediaPage(CommodityMediaPageInput $input, $columns=['*'])
    {
        return MediaCommodity::query()
            ->where('commodity_type', $input->commodityType)
            ->where('commodity_id', $input->commodityId)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }
}
