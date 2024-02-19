<?php

namespace App\Services;

use App\Models\Keyword;

class KeywordService extends BaseService
{
    public function getListByUserId($userId, $columns = ['*'])
    {
        return Keyword::query()->where('user_id', $userId)->get($columns);
    }

    public function clearUserKeywords($userId)
    {
        Keyword::query()->where('user_id', $userId)->delete();
    }

    public function addKeyword($userId, $content)
    {
        $keyword = Keyword::query()->where('user_id', $userId)->where('content', $content)->first();
        if (!is_null($keyword)) {
            $keyword->delete();
        }

        $keyword = Keyword::new();
        $keyword->user_id = $userId;
        $keyword->content = $content;
        $keyword->save();
    }
}
