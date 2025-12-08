<?php

namespace App\Services\Media;

use App\Models\Media\MediaProduct;
use App\Services\BaseService;
use App\Services\Mall\Catering\RestaurantService;
use App\Services\Mall\Goods\GoodsService;
use App\Services\Mall\Hotel\HotelService;
use App\Services\Mall\Scenic\ScenicService;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\ProductMediaPageInput;

class MediaProductService extends BaseService
{
    public function createMediaProduct($mediaType, $mediaId, $productType, $productId)
    {
        $mediaProduct = MediaProduct::new();
        $mediaProduct->media_type = $mediaType;
        $mediaProduct->media_id = $mediaId;
        $mediaProduct->product_type = $productType;
        $mediaProduct->product_id = $productId;
        $mediaProduct->save();
        return $mediaProduct;
    }

    public function getListByProductIds($productType, array $productIds, $columns = ['*'])
    {
        return MediaProduct::query()
            ->where('product_type', $productType)
            ->whereIn('product_id', $productIds)
            ->get($columns);
    }

    public function getListByMediaIds($mediaType, array $mediaIds, $columns = ['*'])
    {
        return MediaProduct::query()
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

        $scenicIds = $list->filter(function (MediaProduct $mediaProduct) {
            return $mediaProduct->product_type == ProductType::SCENIC;
        })->pluck('product_id')->toArray();
        $scenicList = ScenicService::getInstance()->getScenicListByIds($scenicIds, $scenicColumns)->keyBy('id');

        $hotelIds = $list->filter(function (MediaProduct $mediaProduct) {
            return $mediaProduct->product_type == ProductType::HOTEL;
        })->pluck('product_id')->toArray();
        $hotelList = HotelService::getInstance()->getHotelListByIds($hotelIds, $hotelColumns)->keyBy('id');

        $restaurantIds = $list->filter(function (MediaProduct $mediaProduct) {
            return $mediaProduct->product_type == ProductType::RESTAURANT;
        })->pluck('product_id')->toArray();
        $restaurantList = RestaurantService::getInstance()->getListByIds($restaurantIds, $restaurantColumns)->keyBy('id');

        $goodsIds = $list->filter(function (MediaProduct $mediaProduct) {
            return $mediaProduct->product_type == ProductType::GOODS;
        })->pluck('product_id')->toArray();
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
        return MediaProduct::query()
            ->where('media_type', $mediaType)
            ->where('media_id', $mediaId)
            ->delete();
    }

    public function deleteMediaProduct($mediaType, $mediaId)
    {
        MediaProduct::query()
            ->where('media_type', $mediaType)
            ->whereIn('media_id', $mediaId)
            ->delete();
    }

    public function getMediaPage(ProductMediaPageInput $input, $columns=['*'])
    {
        return MediaProduct::query()
            ->where('product_type', $input->productType)
            ->where('product_id', $input->productId)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getList()
    {
        return MediaProduct::query()->get();
    }
}
