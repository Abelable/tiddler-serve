<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\MediaHistory;
use App\Models\ProductHistory;
use App\Services\GiftGoodsService;
use App\Services\GoodsService;
use App\Services\HotelService;
use App\Services\Mall\Catering\RestaurantService;
use App\Services\Media\Note\TourismNoteService;
use App\Services\Media\ShortVideo\ShortVideoService;
use App\Services\MediaHistoryService;
use App\Services\ProductHistoryService;
use App\Services\ScenicService;
use App\Services\ShopService;
use App\Services\UserService;
use App\Utils\Enums\MediaType;
use App\Utils\Enums\ProductType;
use App\Utils\Inputs\PageInput;

class HistoryController extends Controller
{
    public function mediaHistory()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = MediaHistoryService::getInstance()->getHistoryPage($this->userId(), $input);
        $historyList = collect($page->items());

        $videoIds = $historyList
            ->where('media_type', MediaType::VIDEO)
            ->pluck('media_id')
            ->values();
        $videoList = ShortVideoService::getInstance()->getListByIds($videoIds)->keyBy('id');

        $noteIds = $historyList
            ->where('media_type', MediaType::NOTE)
            ->pluck('media_id')
            ->values();
        $noteList = TourismNoteService::getInstance()->getListByIds($noteIds)->keyBy('id');

        $videoAuthorIds = $videoList->pluck('user_id');
        $noteAuthorIds = $noteList->pluck('user_id');
        $authorIds = $videoAuthorIds->merge($noteAuthorIds)->unique()->values()->toArray();
        $authorList = UserService::getInstance()->getListByIds($authorIds, ['id', 'avatar', 'nickname'])->keyBy('id');

        $list = $historyList->map(function (MediaHistory $item) use ($authorList, $noteList, $videoList) {
            $media = $item->media_type == MediaType::VIDEO
                ? $videoList->get($item->media_id)
                : $noteList->get($item->media_id);

            $media['type'] = $item->media_type;

            $media['authorInfo'] = $authorList->get($media->user_id);
            unset($media['user_id']);

            if ($item->media_type == MediaType::NOTE) {
                $media['image_list'] = json_decode($media['image_list']);
            }

            return $media;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function productHistory()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $type = $this->verifyInteger('type');

        $page = ProductHistoryService::getInstance()->getHistoryPage($this->userId(), $type, $input);
        $historyList = collect($page->items());

        $productIds = $historyList->pluck('product_id')->toArray();
        $productList = collect();
        switch ($type) {
            case ProductType::SCENIC:
                $productList = ScenicService::getInstance()->getListByIds($productIds)->keyBy('id');
                break;

            case ProductType::HOTEL:
                $productList = HotelService::getInstance()->getListByIds($productIds)->keyBy('id');
                break;

            case ProductType::RESTAURANT:
                $productList = RestaurantService::getInstance()->getListByIds($productIds)->keyBy('id');
                break;

            case ProductType::GOODS:
                $productList = GoodsService::getInstance()->getListByIds($productIds)->keyBy('id');
                break;
        }

        $giftGoodsIds = [];
        if ($type == ProductType::GOODS) {
            $giftGoodsIds = GiftGoodsService::getInstance()->getList()->pluck('goods_id')->toArray();
        }

        $list = $historyList->map(function (ProductHistory $item) use ($giftGoodsIds, $productList) {
            $product = $productList->get($item->product_id);

            if ($item->product_type == ProductType::SCENIC) {
                $product['cover'] = json_decode($product['image_list'])[0];
            }
            if ($item->product_type == ProductType::SCENIC || $item->product_type == ProductType::HOTEL) {
                $product['feature_tag_list'] = json_decode($product['feature_tag_list']);
            }
            if ($item->product_type == ProductType::RESTAURANT) {
                $product['facility_list'] = json_decode($product['facility_list']);
            }
            if ($item->product_type == ProductType::GOODS) {
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
