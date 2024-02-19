<?php

namespace App\Services;

use App\Models\MallKeyword;
use Illuminate\Support\Facades\DB;

class MallKeywordService extends BaseService
{
    public function getHotList()
    {
        return MallKeyword::query()
            ->select('content', DB::raw('count(*) as count'))
            ->groupBy('content')
            ->orderByDesc('count')
            ->take(10)
            ->get();
    }
    public function getListByUserId($userId, $columns = ['*'])
    {
        return MallKeyword::query()->where('user_id', $userId)->orderBy('created_at', 'desc')->get($columns);
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
