<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\KeywordService;

class GoodsKeywordController extends Controller
{
    public function list()
    {
        $list = KeywordService::getInstance()->getListByUserId($this->userId());
        $contentList = $list->pluck('content')->toArray();
        return $this->success($contentList);
    }

    public function clear()
    {
        KeywordService::getInstance()->clearUserKeywords($this->userId());
        return $this->success();
    }
}
