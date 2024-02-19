<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GoodsKeywordService;

class GoodsKeywordController extends Controller
{
    protected $except = ['hotList'];

    public function hotList()
    {
        $list = GoodsKeywordService::getInstance()->getHotList();
        $contentList = $list->pluck('content')->toArray();
        return $this->success($contentList);
    }

    public function list()
    {
        $list = GoodsKeywordService::getInstance()->getListByUserId($this->userId());
        $contentList = $list->pluck('content')->toArray();
        return $this->success($contentList);
    }

    public function clear()
    {
        GoodsKeywordService::getInstance()->clearUserKeywords($this->userId());
        return $this->success();
    }
}
