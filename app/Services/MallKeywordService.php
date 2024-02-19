<?php

namespace App\Services;

use App\Models\MallKeyword;

class MallKeywordService extends BaseService
{
    public function getListByUserId($userId, $columns = ['*'])
    {
        return MallKeyword::query()->where('user_id', $userId)->get($columns);
    }

    public function clearUserKeywords($userId)
    {
        MallKeyword::query()->where('user_id', $userId)->delete();
    }

    public function addKeyword($userId, $content)
    {
        $keyword = MallKeyword::query()->where('user_id', $userId)->where('content', $content)->first();
        if (!is_null($keyword)) {
            $keyword->delete();
        }

        $keyword = MallKeyword::new();
        $keyword->user_id = $userId;
        $keyword->content = $content;
        $keyword->save();
    }
}
