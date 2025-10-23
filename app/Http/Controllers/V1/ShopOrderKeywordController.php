<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ShopOrderKeywordService;

class ShopOrderKeywordController extends Controller
{
    protected $except = [];

    public function list()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $productType = $this->verifyRequiredInteger('productType');

        $list = ShopOrderKeywordService::getInstance()->getListByShopId($shopId, $productType);
        $contentList = $list->pluck('content')->toArray();
        return $this->success($contentList);
    }

    public function add()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $productType = $this->verifyRequiredInteger('productType');
        $keywords = $this->verifyRequiredString('keywords');

        ShopOrderKeywordService::getInstance()->addKeyword($shopId, $productType, $keywords);

        return $this->success();
    }

    public function clear()
    {
        $shopId = $this->verifyRequiredId('shopId');
        $productType = $this->verifyRequiredInteger('productType');

        ShopOrderKeywordService::getInstance()->clearShopKeywords($shopId, $productType);

        return $this->success();
    }
}
