<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\KeywordService;

class KeywordController extends Controller
{
    protected $except = ['hotList'];

    public function hotList()
    {
        $list = KeywordService::getInstance()->getHotList();
        $contentList = $list->pluck('content')->toArray();
        return $this->success($contentList);
    }

    public function list()
    {
        $list = KeywordService::getInstance()->getListByUserId($this->userId());
        $contentList = $list->pluck('content')->toArray();
        return $this->success($contentList);
    }

    public function add()
    {
        $keywords = $this->verifyRequiredString('keywords');
        KeywordService::getInstance()->addKeyword($this->userId(), $keywords);
        return $this->success();
    }

    public function clear()
    {
        KeywordService::getInstance()->clearUserKeywords($this->userId());
        return $this->success();
    }
}
