<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\OrderKeywordService;

class OrderKeywordController extends Controller
{
    protected $except = [];

    public function list()
    {
        $productType = $this->verifyRequiredInteger('productType');
        $list = OrderKeywordService::getInstance()->getListByUserId($this->userId(), $productType);
        $contentList = $list->pluck('content')->toArray();
        return $this->success($contentList);
    }

    public function add()
    {
        $productType = $this->verifyRequiredInteger('productType');
        $keywords = $this->verifyRequiredString('keywords');
        OrderKeywordService::getInstance()->addKeyword($this->userId(), $productType, $keywords);
        return $this->success();
    }

    public function clear()
    {
        OrderKeywordService::getInstance()->clearUserKeywords($this->userId());
        return $this->success();
    }
}
