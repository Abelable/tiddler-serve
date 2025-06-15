<?php

namespace App\Services;

use App\Models\ProductHistory;
use App\Utils\Inputs\PageInput;

class ProductHistoryService extends BaseService
{

    public function getHistoryPage($userId, $type, PageInput $input, $columns = ['*'])
    {
        return ProductHistory::query()
            ->where('user_id', $userId)
            ->where('product_type', $type)
            ->orderBy('updated_at', $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function createHistory($userId, $mediaType, $mediaId)
    {
        return ProductHistory::query()->updateOrCreate(
            [
                'user_id' => $userId,
                'media_type' => $mediaType,
                'media_id' => $mediaId,
            ],
            [
                'updated_at' => now()
            ]
        );
    }

    public function getHistory($userId, $mediaType, $mediaId, $columns = ['*'])
    {
        return ProductHistory::query()
            ->where('user_id', $userId)
            ->where('media_type', $mediaType)
            ->where('media_id', $mediaId)
            ->first($columns);
    }
}
