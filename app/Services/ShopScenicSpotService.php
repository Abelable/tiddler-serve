<?php

namespace App\Services;

use App\Models\ShopScenicSpot;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ShopScenicSpotService extends BaseService
{
    public function getListTotal($shopId, $status)
    {
        return ShopScenicSpot::query()
            ->where('ship_id', $shopId)
            ->where('status', $status)
            ->count();
    }

    public function getScenicList($shopId, StatusPageInput $input, $columns = ['*'])
    {
        return ShopScenicSpot::query()
            ->where('shop_id', $shopId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getShopScenicById($shopId, $id, $columns = ['*'])
    {
        return ShopScenicSpot::query()->where('shop_id', $shopId)->find($id, $columns);
    }

    public function getSpotByScenicId($userId, $scenicId, $columns = ['*'])
    {
        return ShopScenicSpot::query()->where('user_id', $userId)->where('scenic_id', $scenicId)->first($columns);
    }

    public function getAdminScenicPage(StatusPageInput $input, $columns = ['*'])
    {
        $query = ShopScenicSpot::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getScenicById($id, $columns = ['*'])
    {
        return ShopScenicSpot::query()->find($id, $columns);
    }

    public function getUserScenicOptions($userId, $columns = ['*'])
    {
        return ShopScenicSpot::query()->where('user_id', $userId)->get($columns);
    }

    public function createScenicList($userId, $shopId, array $scenicIds)
    {
        foreach ($scenicIds as $scenicId) {
            $scenic = $this->getShopScenicById($userId, $scenicId);
            if (!is_null($scenic)) {
                $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '包含已添加景点，请重试');
            }

            $scenic = ShopScenicSpot::new();
            $scenic->user_id = $userId;
            $scenic->shop_id = $shopId;
            $scenic->scenic_id = $scenicId;
            $scenic->save();
        }
    }
}
