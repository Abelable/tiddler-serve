<?php

namespace App\Services\Mall;

use App\Models\Mall\ShopOrderKeyword;
use App\Services\BaseService;

class ShopOrderKeywordService extends BaseService
{
    public function getListByShopId($shopId, $productType, $columns = ['*'])
    {
        return ShopOrderKeyword::query()
            ->where('shop_id', $shopId)
            ->where('product_type', $productType)
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }

    public function clearShopKeywords($shopId, $productType)
    {
        ShopOrderKeyword::query()
            ->where('shop_id', $shopId)
            ->where('product_type', $productType)
            ->delete();
    }

    public function addKeyword($shopId, $productType, $content)
    {
        $keyword = ShopOrderKeyword::query()
            ->where('shop_id', $shopId)
            ->where('product_type', $productType)
            ->where('content', $content)
            ->first();
        if (!is_null($keyword)) {
            $keyword->delete();
        }

        $keyword = ShopOrderKeyword::new();
        $keyword->shop_id = $shopId;
        $keyword->product_type = $productType;
        $keyword->content = $content;
        $keyword->save();
    }
}
