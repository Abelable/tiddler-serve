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
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function createHistory($userId, $productType, $productId)
    {
        $history = $this->getHistory($userId, $productType, $productId);
        if (!is_null($history)) {
            $history->delete();
        }

        $history = ProductHistory::new();
        $history->user_id = $userId;
        $history->product_type = $productType;
        $history->product_id = $productId;
        $history->save();

        return $history;
    }

    public function getHistory($userId, $productType, $productId, $columns = ['*'])
    {
        return ProductHistory::query()
            ->where('user_id', $userId)
            ->where('product_type', $productType)
            ->where('product_id', $productId)
            ->first($columns);
    }
}
