<?php

namespace App\Services;

use App\Models\ShopTodo;

class ShopTodoService extends BaseService
{
    public function getTodoList($shopId, $columns=['*'])
    {
        return ShopTodo::query()
            ->where('shop_id', $shopId)
            ->where('status', 0)
            ->orderBy('created_at', 'desc')
            ->get($columns);
    }

    public function createTodo($shopId, $type, array $referenceIds)
    {
        foreach ($referenceIds as $referenceId) {
            $todo = ShopTodo::new();
            $todo->shop_id = $shopId;
            $todo->type = $type;
            $todo->reference_id = $referenceId;
            $todo->save();
        }
    }

    public function finishTodo($shopId, $type, $referenceId)
    {
        $todo = ShopTodo::query()
            ->where('shop_id', $shopId)
            ->where('type', $type)
            ->where('reference_id', $referenceId)
            ->first();

        $todo->status = 1;
        $todo->save();

        return $todo;
    }
}
