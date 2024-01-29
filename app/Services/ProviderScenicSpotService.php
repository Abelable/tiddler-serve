<?php

namespace App\Services;

use App\Models\ProviderScenicSpot;
use App\Utils\CodeResponse;
use App\Utils\Inputs\StatusPageInput;

class ProviderScenicSpotService extends BaseService
{
    public function getListTotal($userId, $status)
    {
        return ProviderScenicSpot::query()->where('user_id', $userId)->where('status', $status)->count();
    }

    public function getUserSpotList($userId, StatusPageInput $input, $columns = ['*'])
    {
        return ProviderScenicSpot::query()
            ->where('user_id', $userId)
            ->where('status', $input->status)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getUserSpotById($userId, $id, $columns = ['*'])
    {
        return ProviderScenicSpot::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getSpotByScenicId($userId, $scenicId, $columns = ['*'])
    {
        return ProviderScenicSpot::query()->where('user_id', $userId)->where('scenic_id', $scenicId)->first($columns);
    }

    public function getScenicList(StatusPageInput $input, $columns = ['*'])
    {
        $query = ProviderScenicSpot::query();
        if (!is_null($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getScenicById($id, $columns = ['*'])
    {
        return ProviderScenicSpot::query()->find($id, $columns);
    }

    public function getUserScenicOptions($userId, $columns = ['*'])
    {
        return ProviderScenicSpot::query()->where('user_id', $userId)->get($columns);
    }

    public function createScenicList($userId, $providerId, array $scenicIds)
    {
        foreach ($scenicIds as $scenicId) {
            $scenic = $this->getUserSpotById($userId, $scenicId);
            if (!is_null($scenic)) {
                $this->throwBusinessException(CodeResponse::INVALID_OPERATION, '包含已添加景点，请重试');
            }

            $scenic = ProviderScenicSpot::new();
            $scenic->user_id = $userId;
            $scenic->provider_id = $providerId;
            $scenic->scenic_id = $scenicId;
            $scenic->save();
        }
    }
}
