<?php

namespace App\Services;

use App\Models\Keyword;
use Illuminate\Support\Facades\DB;

class KeywordService extends BaseService
{
    public function getHotList()
    {
        return Keyword::query()
            ->select('content', DB::raw('count(*) as count'))
            ->groupBy('content')
            ->orderByDesc('count')
            ->take(10)
            ->get();
    }

    public function getListByUserId($userId, $columns = ['*'])
    {
        return Keyword::query()->where('user_id', $userId)->orderBy('created_at', 'desc')->get($columns);
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
