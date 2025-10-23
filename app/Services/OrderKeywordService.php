<?php

namespace App\Services;

use App\Models\OrderKeyword;
use Illuminate\Support\Facades\DB;

class OrderKeywordService extends BaseService
{
    public function getListByUserId($userId, $productType, $columns = ['*'])
    {
        return OrderKeyword::query()
            ->where('user_id', $userId)
            ->where('product_type', $productType)
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }

    public function clearUserKeywords($userId, $productType)
    {
        OrderKeyword::query()
            ->where('user_id', $userId)
            ->where('product_type', $productType)
            ->delete();
    }

    public function addKeyword($userId, $productType, $content)
    {
        $keyword = OrderKeyword::query()
            ->where('user_id', $userId)
            ->where('product_type', $productType)
            ->where('content', $content)
            ->first();
        if (!is_null($keyword)) {
            $keyword->delete();
        }

        $keyword = OrderKeyword::new();
        $keyword->user_id = $userId;
        $keyword->product_type = $productType;
        $keyword->content = $content;
        $keyword->save();
    }
}
