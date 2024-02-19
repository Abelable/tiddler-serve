<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\MallKeywordService;

class MallKeywordController extends Controller
{
    protected $except = ['hotList'];

    public function hotList()
    {
        $list = MallKeywordService::getInstance()->getHotList();
        $contentList = $list->pluck('content')->toArray();
        return $this->success($contentList);
    }

    public function list()
    {
        $list = MallKeywordService::getInstance()->getListByUserId($this->userId());
        $contentList = $list->pluck('content')->toArray();
        return $this->success($contentList);
    }

    public function clear()
    {
        MallKeywordService::getInstance()->clearUserKeywords($this->userId());
        return $this->success();
    }
}
