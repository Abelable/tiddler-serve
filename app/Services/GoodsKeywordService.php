<?php

namespace App\Services;

use App\Models\GoodsKeyword;
use Illuminate\Support\Facades\DB;

class GoodsKeywordService extends BaseService
{
    public function getHotList()
    {
        return GoodsKeyword::query()
            ->select('content', DB::raw('count(*) as count'))
            ->groupBy('content')
            ->orderByDesc('count')
            ->take(10)
            ->get();
    }
    public function getListByUserId($userId, $columns = ['*'])
    {
        return GoodsKeyword::query()->where('user_id', $userId)->orderBy('created_at', 'desc')->get($columns);
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
