<?php

namespace App\Services;

use App\Models\GoodsKeyword;

class GoodsKeywordService extends BaseService
{
    public function getListByUserId($userId, $columns = ['*'])
    {
        return GoodsKeyword::query()->where('user_id', $userId)->get($columns);
    }

    public function clearUserKeywords($userId)
    {
        GoodsKeyword::query()->where('user_id', $userId)->delete();
    }

    public function addKeyword($userId, $content)
    {
        $keyword = GoodsKeyword::query()->where('user_id', $userId)->where('content', $content)->first();
        if (!is_null($keyword)) {
            $keyword->delete();
        }

        $keyword = GoodsKeyword::new();
        $keyword->user_id = $userId;
        $keyword->content = $content;
        $keyword->save();
    }
}
